<?php
include 'connect-to-db.php';

//Questo file viene usato per ottenere le informazioni sulle 3 crypto che vengono sempre mostrate in home

header('Content-Type: application/json');

session_start();

$sql = "SELECT coinCode, Icon, price, variation FROM crypto WHERE name='Solana' OR name='Bitcoin' OR name='Ethereum';";//Seleziona le informazioni necessarie per ogni crypto

$result = $conn->query($sql);

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']); //Encode dell'immagine per poterla usare come src in html
    
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