<?php
// Retrieve data sent from Wemos
$TDS = isset($_POST['TDS']) ? $_POST['TDS'] : null;
$pH = isset($_POST['pH']) ? $_POST['pH'] : null;
$Turbidity = isset($_POST['Turbidity']) ? $_POST['Turbidity'] : null;
$TDSStatus = isset($_POST['TDSStatus']) ? $_POST['TDSStatus'] : null;
$pHStatus = isset($_POST['pHStatus']) ? $_POST['pHStatus'] : null;
$TurbidityStatus = isset($_POST['TurbidityStatus']) ? $_POST['TurbidityStatus'] : null;

// Check if required fields are present and none of the sensor values are 0
if ($TDS !== null && $pH !== null && $Turbidity !== null && $TDSStatus !== null && $pHStatus !== null && $TurbidityStatus !== null &&
    $TDS != 0 && $pH != 0 && $Turbidity != 0) {
    // Debug: Output TurbidityStatus value before modification
    echo "Original TurbidityStatus: $TurbidityStatus<br>";

    // Determine TurbidityStatus based on the received value
    $TurbidityStatus = ($TurbidityStatus == "NORMAL" ? "NORMAL" : "HIGH");

    // Debug: Output TurbidityStatus value after modification
    echo "Modified TurbidityStatus: $TurbidityStatus<br>";

    // Database connection settings
    $servername = "waterguard.database.windows.net";
    $username = "waterguardmonitoringsystem";
    $password = "K7BVJwater0";
    $dbname = "waterguardmonitoringsystem";

    // Create connection  
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement
    $sql = "INSERT INTO WaterQualityMeasurements (TDS, pH, Turbidity, TDSStatus, pHStatus, TurbidityStatus) 
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddssss", $TDS, $pH, $Turbidity, $TDSStatus, $pHStatus, $TurbidityStatus);

    // Execute the query
    if ($stmt->execute() === TRUE) {
        echo "Data inserted successfully<br>";

        // Retrieve inserted data
        $insertedId = $stmt->insert_id;
        $selectSql = "SELECT * FROM WaterQualityMeasurements WHERE id = $insertedId";
        $result = $conn->query($selectSql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "Inserted Data: TDS={$row['TDS']}, pH={$row['pH']}, Turbidity={$row['Turbidity']}, TDSStatus={$row['TDSStatus']}, pHStatus={$row['pHStatus']}, TurbidityStatus={$row['TurbidityStatus']}<br>";
        } else {
            echo "Error retrieving inserted data<br>";
        }
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Skipping data insertion into the database due to zero sensor value";
}
?>
