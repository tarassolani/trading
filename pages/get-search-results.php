<?php
header('Content-Type: application/json');

// Check if prices are already stored in session
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

$sql = "SELECT coinCode, name, Icon, price, variation FROM crypto WHERE name LIKE ? OR coinCode LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchText, $searchText);
$stmt->execute();
$result = $stmt->get_result();

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);

    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'name' => $row['name'],
        'Icon' => $base64Icon,
        'price' => number_format($row['price'],2),
        'percent_change' => number_format($row['variation'],2)
    ];
}

$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>