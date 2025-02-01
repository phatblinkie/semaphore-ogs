<?php
// Database credentials
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost";
$username = "semaphore_user";
$password = "DFyuqwhjty34JK@#23@#";
$dbname = "semaphore_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve project_id from query parameters
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;


$sql = "SELECT
  hostname,
  ansible_ping,
  disk_capacity,
  proc_usage,
  app_check,
  last_updated,
  last_responded,
  id,
  uptime
FROM
  system_status
WHERE
  project_id = $project_id
ORDER BY
  id DESC;";

$result = $conn->query($sql);
$data = [];

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo json_encode([]);
}

$conn->close();

// Output JSON data
echo json_encode($data);
?>
