<?php
include 'connect-to-db.php';
session_start();

// Receive data sent via GET
$type = $_GET['type']; // Transaction type (deposit or withdrawal)
$amount = $_GET['amount']; // Transaction amount

$username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

$amount = floatval($amount);
$amount = round($amount, 2);

if ($type === 'deposit') {
    $stmt_check = $conn->prepare("SELECT balance FROM BankAccount WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($current_balance);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($current_balance < $amount) {
        echo "error:Insufficient funds."; // Invia un messaggio di errore AJAX
        exit();
    }
    // Prepare a statement
    $stmt = $conn->prepare("UPDATE BankAccount SET balance = balance - ? WHERE username = ?");

    // Bind parameters
    $stmt->bind_param("ds", $amount, $username);

    // Execute the statement
    if ($stmt->execute()) {
        echo "success:Deposit completed successfully."; // Invia un messaggio di successo AJAX
        exit();
    } else {
        echo "error:Error during deposit: " . $conn->error; // Invia un messaggio di errore AJAX
        exit();
    }
} elseif ($type === 'withdraw') {
    // Prepare a statement
    $stmt = $conn->prepare("UPDATE BankAccount SET balance = balance + ? WHERE username = ?");

    // Bind parameters
    $stmt->bind_param("ds", $amount, $username);

    // Execute the statement
    if ($stmt->execute()) {
        echo "success:Withdrawal completed successfully."; // Invia un messaggio di successo AJAX
        exit();
    } else {
        echo "error:Error during withdrawal: " . $conn->error; // Invia un messaggio di errore AJAX
        exit();
    }
} else {
    echo "error:Invalid transaction type."; // Invia un messaggio di errore AJAX
    exit();
}
?>