<?php
include 'connect-to-db.php';
session_start();

$username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

//Query per ottenere la hash del wallet dall'utente corrente
$stmt_get_wallet_hash = $conn->prepare("SELECT hash FROM users WHERE username = ?");
$stmt_get_wallet_hash->bind_param("s", $username);
$stmt_get_wallet_hash->execute();
$stmt_get_wallet_hash->bind_result($wallet_hash);
$stmt_get_wallet_hash->fetch();
$stmt_get_wallet_hash->close();

$friend = $_GET['username'];
$amount = $_GET['amount']; //Quantità di USDT interessata dalla transazione
$amount = floatval($amount);

$stmt_check = $conn->prepare("SELECT amount FROM position WHERE wallet = ? AND crypto = 'USDT'");
$stmt_check->bind_param("s", $wallet_hash);
$stmt_check->execute();
$stmt_check->bind_result($current_balance);
$stmt_check->fetch();
$stmt_check->close();

if ($current_balance < $amount) {
    $response = array("error" => "Insufficient funds."); //Messaggio di errore AJAX che notifica il non avvenuto deposito
} else {
    //Inizia la transazione
    $conn->begin_transaction();

    try {
        if($current_balance == $amount){
            $stmt_delete_position = $conn->prepare("DELETE position FROM position
                                                    INNER JOIN users ON hash = wallet
                                                    WHERE username = ? AND crypto = 'USDT'");
            $stmt_delete_position->bind_param("s", $username);

            if (!$stmt_delete_position->execute()) {
                throw new Exception("Error deleting position: " . $stmt_delete_position->error);
            }
        }
        else{
            $stmt_update_balance = $conn->prepare("UPDATE position SET amount = amount - ? WHERE wallet = ? AND crypto = 'USDT'");
            $stmt_update_balance->bind_param("ds", $amount, $wallet_hash);

            if (!$stmt_update_balance->execute()) {
                throw new Exception("Error during transaction: " . $stmt_update_balance->error);
            }

            $stmt_update_balance_receiver = $conn->prepare("UPDATE position 
                                                            INNER JOIN users ON hash = wallet
                                                            SET amount = amount + ?
                                                            WHERE username = ? AND crypto = 'USDT'");
            $stmt_update_balance_receiver->bind_param("ds", $amount, $friend);

            if (!$stmt_update_balance_receiver->execute()) {
                throw new Exception("Error during transaction: " . $stmt_update_balance->error);
            }
        }

        //Commit della transazione (si esegue solo se non sono presenti errori)
        $conn->commit();

        $response = array("success" => "Transaction completed successfully."); //Messaggio di successo AJAX

    } catch (Exception $e) {
        //Rollback della transazione in caso di errore
        $conn->rollback();
        $response = array("error" => $e->getMessage()); //Messaggio di errore AJAX
    }

    if (isset($stmt_update_balance)) {
        $stmt_update_balance->close();
    }

    if (isset($stmt_update_balance_receiver)) {
        $stmt_update_balance_receiver->close();
    }
}

// Chiudi la connessione al database
$conn->close();

// Restituisci la risposta come JSON
echo json_encode($response);
?>