<?php
include 'connect-to-db.php';
header('Content-Type: application/json');

session_start();

if (isset($_COOKIE['login-info']) || isset($_SESSION['login-info'])) { 
    $username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

    //Prima, ottengo la hash del wallet
    $stmt_get_wallet_hash = $conn->prepare("SELECT hash FROM users WHERE username = ?");
    $stmt_get_wallet_hash->bind_param("s", $username);
    $stmt_get_wallet_hash->execute();
    $stmt_get_wallet_hash->bind_result($wallet_hash);
    $stmt_get_wallet_hash->fetch();
    $stmt_get_wallet_hash->close();

    //Poi, ottengo le informazioni sulla crypto e l'amount di ogni posizione
    $sql = "SELECT p.crypto, c.name, c.Icon, c.price, c.variation, p.amount 
            FROM position p
            INNER JOIN Crypto c ON p.crypto = c.coinCode
            WHERE p.wallet = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $wallet_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    $crypto_data = [];

    while ($row = $result->fetch_assoc()) {
        $base64Icon = base64_encode($row['Icon']); //Encode dell'immagine per poterla usare come src in HTML

        $crypto_data[] = [
            'coinCode' => $row['crypto'],
            'name' => $row['name'],
            'Icon' => $base64Icon,
            'price' => number_format($row['price'], 2),
            'percent_change' => number_format($row['variation'], 2),
            'amount' => number_format($row['amount'], 2)
        ];
    }
    $stmt->close();
    $conn->close();

    if (empty($crypto_data)) {
        echo json_encode(['message' => 'No positions found for the user']); //Messaggio se non trovo alcuna posizione
        //Il messaggio è necessario perché, nel caso non ci siano posizioni, il json sarebbe vuoto e genererebbe errore in account.js
    } else {
        echo json_encode($crypto_data);
    }
} else {
    echo json_encode(['error' => 'User not authenticated']);
}
?>