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

$sql = "SELECT coinCode, Icon FROM crypto WHERE name='Solana' OR name='Bitcoin' OR name='Ethereum';";
$result = $conn->query($sql);

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);

    while (!isset($_SESSION['crypto_prices'][$row['coinCode']]['price']) || !isset($_SESSION['crypto_prices'][$row['coinCode']]['percent_change'])) {
        sleep(1); // Attesa di 1 secondo
    }

    // Le due variabili di sessione sono state settate, procedi con il tuo script
    $price = $_SESSION['crypto_prices'][$row['coinCode']]['price'];
    $percent_change = $_SESSION['crypto_prices'][$row['coinCode']]['percent_change'];
    
    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'Icon' => $base64Icon,
        'price' => $price,
        'percent_change' => $percent_change // Aggiungi percentuale di variazione qui
    ];
}

$conn->close();

echo json_encode($crypto_data);

?>