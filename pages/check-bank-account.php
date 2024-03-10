<?php
include 'connect-to-db.php';

// Receive data sent via POST
$type = $_GET['type']; // Transaction type (deposit or withdrawal)
$amount = $_GET['amount']; // Transaction amount

$username = $_SESSION['login-info'];

// Formattare l'importo con un massimo di 2 cifre decimali
$amount = number_format($amount, 2);

if ($type === 'deposit') {
    $stmt_check = $conn->prepare("SELECT balance FROM BankAccount WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($current_balance);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($current_balance < $amount) {
        header("Location: account.php?error=Insufficient funds.");
        exit();
    }
    // Prepare a statement
    $stmt = $conn->prepare("UPDATE BankAccount SET balance = balance - ? WHERE username = ?");

    // Bind parameters
    $stmt->bind_param("ds", $amount, $username);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: account.php?message=Deposit completed successfully.");
        exit();
    } else {
        header("Location: account.php?error=Error during deposit: " . $conn->error);
        exit();
    }
} elseif ($type === 'withdraw') {
    // Prepare a statement
    $stmt = $conn->prepare("UPDATE BankAccount SET balance = balance + ? WHERE username = ?");

    // Bind parameters
    $stmt->bind_param("ds", $amount, $username);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: account.php?message=Withdrawal completed successfully.");
        exit();
    } else {
        header("Location: account.php?error=Error during withdrawal: " . $conn->error);
        exit();
    }
} else {
    header("Location: account.php?error=Invalid transaction type.");
    exit();
}

// Close the prepared statements and connection
$stmt->close();
$conn->close();
?>