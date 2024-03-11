<?php
include 'connect-to-db.php';
session_start();

$receivedHash = $_POST['hash'];

$receivedHash = substr($receivedHash, 0, 50);

$userID = $_SESSION['login-info'] ?? $_COOKIE['login-info'] ?? null;

if ($userID) {
    // Inserimento nella tabella Wallet
    $insertWalletSql = "INSERT INTO Wallet (hash, username) VALUES (?, ?)";
    $stmtWallet = $conn->prepare($insertWalletSql);
    $stmtWallet->bind_param("ss", $receivedHash, $userID);

    if (!$stmtWallet->execute()) {
        trigger_error("Can't insert into Wallet table", E_USER_ERROR);
    }
    $stmtWallet->close();
    
    // Aggiornamento della tabella Users
    $updateUserSql = "UPDATE Users SET hash = ? WHERE username = ?";
    $stmtUser = $conn->prepare($updateUserSql);
    $stmtUser->bind_param("ss", $receivedHash, $userID);

    if (!$stmtUser->execute()) {
        trigger_error("Can't update database", E_USER_ERROR);
    }
    $stmtUser->close();
}

$conn->close();
?>
