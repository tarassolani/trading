<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

session_start();

$username = $_GET['username'];

$sql = "DELETE FROM Friends WHERE (friend1 = ? AND friend2 = ?) OR (friend1 = ? AND friend2 = ?);";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $_SESSION['login-info'], $username, $username, $_SESSION['login-info']);

$response = [];

if ($stmt->execute()) {
    $response = ["success" => true];
} else {
    $response = ["success" => false, "error" => $conn->error];
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>