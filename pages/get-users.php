<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

session_start();

$searchText = '%' . $_GET['searchText'] . '%';

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