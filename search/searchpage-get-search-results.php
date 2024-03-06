<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "databaseregolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchText = '%' . $_GET['searchText'] . '%';

$sql = "SELECT coinCode, name, supply, Icon FROM crypto WHERE name LIKE ? OR coinCode LIKE ?";
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
        'supply' => $row['supply'],
        'Icon' => $base64Icon
    ];
}

$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>