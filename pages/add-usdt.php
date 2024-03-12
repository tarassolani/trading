<?php
include 'connect-to-db.php';
session_start();

$cryptoName = "USDT";

// Query per ottenere i dati della crypto USDT utilizzando prepared statement
$query = "SELECT Icon, price FROM Crypto WHERE name = ?";
$stmt = $conn->prepare($query);

// Verifica se lo statement è stato preparato correttamente
if (!$stmt) {
    die("Prepared statement failed: " . $conn->error);
}

$stmt->bind_param("s", $cryptoName);

if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

// Ottiene il risultato
$result = $stmt->get_result();

// Creare un array per memorizzare i dati
$data = array();

// Controlla se sono presenti risultati
if ($result->num_rows > 0) {
    // Ottieni la riga risultato
    $row = $result->fetch_assoc();
    
    $data['icon'] = $row['icon'];
    $data['price'] = $row['price'];
}

$stmt->close();
$conn->close();

echo json_encode($data);
?>