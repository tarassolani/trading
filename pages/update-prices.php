<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

// Check if prices are already stored in session
session_start();

$sql = "SELECT coinCode FROM crypto";
$result = $conn->query($sql);

$coinCodes = [];
while ($row = $result->fetch_assoc()) {
    $coinCodes[] = $row['coinCode'];
}

$conn->close();

// Fetch prices from CoinMarketCap API
$apiKey = 'a3975305-35e9-47e3-baae-a04ab54de810';
$coinCodeString = implode(',', $coinCodes);

// Includere il campo percent_change_24h nella richiesta
$cmcUrl = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=$coinCodeString&convert=USDT";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $cmcUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-CMC_PRO_API_KEY: ' . $apiKey
));
$cmcResponse = curl_exec($ch);
curl_close($ch);

// Parse response and store prices and percentage changes in session
$cmcData = json_decode($cmcResponse, true);

$_SESSION['crypto_prices'] = []; 
foreach ($cmcData['data'] as $symbol => $data) {
    $price = isset($data['quote']['USDT']['price']) ? number_format($data['quote']['USDT']['price'], 2) : 'Price not available';

    // Ottieni percentuale di variazione
    $percent_change_24h = isset($data['quote']['USDT']['percent_change_24h']) ? number_format($data['quote']['USDT']['percent_change_24h'], 2) : 'Change not available';

    // Salva entrambi nella sessione
    $_SESSION['crypto_prices'][$symbol] = [
        'price' => $price, 
        'percent_change' => $percent_change_24h
    ];
}
?>