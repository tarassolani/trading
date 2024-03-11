<?php
include 'connect-to-db.php';
session_start();

$iban = $_POST['iban'];
$user = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

$sql = "INSERT INTO BankAccount(iban, username) VALUES(?, ?)";
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("ss", $iban, $user);

// Execute the statement
if ($stmt->execute()) {
    header("Location: account.php?message=Bank account connected successfully");
    exit();
} else {
    header("Location: account.php?error=Error during the bank account connection: " . $conn->error);
    exit();
}

// Close the prepared statements and connection
$stmt->close();
$conn->close();
?>