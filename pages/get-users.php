<?php
header('Content-Type: application/json');

session_start();

$searchText = '%' . $_GET['searchText'] . '%';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT username FROM Users WHERE username LIKE ? AND username <> ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchText, $_SESSION['login-info']);
$stmt->execute();
$result = $stmt->get_result();

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $crypto_data[] = [
        'username' => $row['username']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>