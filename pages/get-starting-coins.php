<?php
header('Content-Type: application/json');

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbregolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT coinCode, Icon, price, variation FROM crypto WHERE name='Solana' OR name='Bitcoin' OR name='Ethereum';";
$result = $conn->query($sql);

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);
    
    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'Icon' => $base64Icon,
        'price' => number_format($row['price'],2),
        'percent_change' =>  number_format($row['variation'],2)
    ];
}

$conn->close();

echo json_encode($crypto_data);

?>