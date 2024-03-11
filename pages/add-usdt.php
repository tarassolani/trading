<?php
include 'connect-to-db.php';
session_start();

// Assicurati che il nome della crypto sia impostato e sicuro
$cryptoName = "USDT"; // Imposta il nome della crypto

// Query per ottenere i dati della crypto USDT utilizzando prepared statement
$query = "SELECT icon, price FROM Crypto WHERE name = ?";
$stmt = $conn->prepare($query);

// Verifica se lo statement è stato preparato correttamente
if (!$stmt) {
    die("Prepared statement failed: " . $conn->error);
}

// Bind del parametro
$stmt->bind_param("s", $cryptoName);

// Esegui la query
if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

// Ottieni il risultato
$result = $stmt->get_result();

// Creare un array per memorizzare i dati
$data = array();

// Controlla se sono presenti risultati
if ($result->num_rows > 0) {
    // Ottieni la riga risultato
    $row = $result->fetch_assoc();
    
    // Inserisci i dati nell'array
    $data['icon'] = $row['icon'];
    $data['price'] = $row['price'];
}

// Chiudi lo statement e la connessione al database
$stmt->close();
$conn->close();

// Restituisci i dati in formato JSON
echo json_encode($data);
?>