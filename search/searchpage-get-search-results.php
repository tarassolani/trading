<?php
include '../pages/connect-to-db.php';
header('Content-Type: application/json');

$searchText = '%' . $_GET['searchText'] . '%';

$sql = "SELECT coinCode, name, supply, Icon, price, variation FROM Crypto WHERE name LIKE ? OR coinCode LIKE ?"; //Seleziona le informazioni necessarie per ogni crypto
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $searchText, $searchText);
$stmt->execute();
$result = $stmt->get_result();

$crypto_data = [];

while ($row = $result->fetch_assoc()) {
    $base64Icon = base64_encode($row['Icon']); //Encode dell'immagine per poterla usare come src in html

    $crypto_data[] = [
        'coinCode' => $row['coinCode'],
        'name' => $row['name'],
        'supply' => number_format($row['supply'], 0, '.', ','),
        'Icon' => $base64Icon,
        'price' => number_format($row['price'],2),
        'percent_change' => number_format($row['variation'],2)
    ];
}
$stmt->close();
$conn->close();

echo json_encode($crypto_data);
?>
