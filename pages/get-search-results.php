<?php
header('Content-Type: application/json');

// Check if prices are already stored in session
session_start();
if (!isset($_SESSION['crypto_prices'])) {
    // Fetch symbols from your database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT coinCode FROM crypto";
    $result = $conn->query($sql);

    $coinCodes = [];
    while ($row = $result->fetch_assoc()) {
        $coinCodes[] = $row['coinCode'];
    }

    $conn->close();

    // Fetch prices from CoinMarketCap API
    $apiKey = 'a3975305-35e9-47e3-baae-a04ab54de810'; // Replace with your CoinMarketCap API Key
    $coinCodeString = implode(',', $coinCodes);
    $cmcUrl = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=$coinCodeString&convert=USDT";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cmcUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-CMC_PRO_API_KEY: ' . $apiKey
    ));
    $cmcResponse = curl_exec($ch);
    curl_close($ch);

    // Parse response and store prices in session
    $cmcData = json_decode($cmcResponse, true);
    $_SESSION['crypto_prices'] = [];
    foreach ($cmcData['data'] as $symbol => $data) {
        $price = isset($data['quote']['USDT']['price']) ? number_format($data['quote']['USDT']['price'], 2) : 'Price not available';
        $_SESSION['crypto_prices'][$symbol] = $price;
    }
}

$searchText = '%' . $_GET['searchText'] . '%';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT coinCode, name, Icon FROM crypto WHERE name LIKE ? OR coinCode LIKE ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchText, $searchText);
$stmt->execute();
$result = $stmt->get_result();

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']);
    $price = isset($_SESSION['crypto_prices'][$row['coinCode']]) ? $_SESSION['crypto_prices'][$row['coinCode']] : 'Price not available';

    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'name' => $row['name'],
        'Icon' => $base64Icon,
        'price' => $price
    ];
}

$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>