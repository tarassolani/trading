<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT coinCode, Icon FROM crypto WHERE name='Solana' OR name='Bitcoin' OR name='Ethereum';";
$result = $conn->query($sql);

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);
    
    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'Icon' => $base64Icon
    ];
}

$conn->close();

echo json_encode($crypto_data);

?>