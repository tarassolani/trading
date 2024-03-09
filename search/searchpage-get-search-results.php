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

$searchText = '%' . $_GET['searchText'] . '%';

$sql = "SELECT coinCode, name, supply, Icon FROM crypto WHERE name LIKE ? OR coinCode LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchText, $searchText);
$stmt->execute();
$result = $stmt->get_result();

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);

    // Accesso corretto al prezzo
    $price = isset($_SESSION['crypto_prices'][$row['coinCode']]['price']) ? $_SESSION['crypto_prices'][$row['coinCode']]['price'] : 'Price not available'; 

    // Accesso corretto alla percentuale di variazione
    $percent_change = isset($_SESSION['crypto_prices'][$row['coinCode']]['percent_change']) ? $_SESSION['crypto_prices'][$row['coinCode']]['percent_change'] : 'Percent not available';

    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'name' => $row['name'],
        'supply' => number_format($row['supply'], 0, '.', ','),
        'Icon' => $base64Icon,
        'price' => $price,
        'percent_change' => $percent_change // Aggiungi percentuale di variazione qui
    ];
}
$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>