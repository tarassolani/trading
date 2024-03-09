<?php
header('Content-Type: application/json');

session_start();

$username = $_GET['username'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO Friends(friend1, friend2) VALUES (?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $_SESSION['login-info'], $username);
$stmt->execute();

if ($stmt->execute()) {
    $response = ["success" => true];
} else {
    $response = ["success" => false, "error" => $conn->error];
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>