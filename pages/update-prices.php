<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

$sql = "SELECT coinCode FROM crypto"; //Seleziona tutte le crypto dal database
$result = $conn->query($sql);

$coinCodes = [];
while ($row = $result->fetch_assoc()) {
    $coinCodes[] = $row['coinCode'];
}

$conn->close();

//Inizio API CoinMarketCap
$apiKey = 'a3975305-35e9-47e3-baae-a04ab54de810';
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

$cmcData = json_decode($cmcResponse, true);
//Fine API CoinMarketCap

foreach ($cmcData['data'] as $symbol => $data) {
    //Prende i valori del prezzo e di variazione di prezzo nell'ultimo giorno
    $price = isset($data['quote']['USDT']['price']) ? $data['quote']['USDT']['price'] : null;
    $percent_change_24h = isset($data['quote']['USDT']['percent_change_24h']) ? $data['quote']['USDT']['percent_change_24h'] : null;

    //Aggiorna i valori nel database
    $updateSql = "UPDATE crypto SET price = ?, variation = ? WHERE coinCode = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param('dss', $price, $percent_change_24h, $symbol);
    $updateStmt->execute();
    $updateStmt->close();
}

$conn->close();

echo json_encode(['success' => true]);
?>