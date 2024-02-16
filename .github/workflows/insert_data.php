<?php
// Get the POST data
$data = file_get_contents("php://input");
$data = json_decode($data, true);

// Database connection parameters
$servername = "localhost";
$username = "root"; // Change this to your MySQL username
$password = ""; // Change this to your MySQL password
$dbname = "waterqualitymonitoring";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$data = $_POST['data'];
list($pH, $pHStatus, $TDS, $TDSStatus, $Turbidity, $TurbidityStatus) = explode(",", $data);

$sql = "INSERT INTO waterqualitymeasurements (pH, TDS, Turbidity, pHStatus, TDSStatus, TurbidityStatus) VALUES ('$pH', '$TDS', '$Turbidity', '$pHStatus', '$TDSStatus', '$TurbidityStatus')";

if ($conn->query($sql) === TRUE) {
  echo "Data inserted successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
