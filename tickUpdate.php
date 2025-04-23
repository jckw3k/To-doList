<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "webprogramming";

// Create connection
$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
$taskID = $data['taskID'];
$isCompleted = $data['isCompleted'];

// Update the task in the database
$sql = "UPDATE tasks SET isCompleted = $isCompleted WHERE taskID = $taskID";
$result = $connection->query($sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $connection->error]);
}

$connection->close();
?>
