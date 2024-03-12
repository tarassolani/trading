<?php
include 'connect-to-db.php';
session_start();
header('Content-Type: application/json');

$username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

//Query per ottenere la hash del wallet dell'utente corrente
$stmt_get_wallet_hash = $conn->prepare("SELECT hash FROM users WHERE username = ?");
$stmt_get_wallet_hash->bind_param("s", $username);
$stmt_get_wallet_hash->execute();
$stmt_get_wallet_hash->bind_result($wallet_hash);
$stmt_get_wallet_hash->fetch();
$stmt_get_wallet_hash->close();

$coinCode = $_GET['coinName'];
$amount = $_GET['amount'];
$amount = floatval($amount);

$stmt_check = $conn->prepare("SELECT amount FROM position WHERE wallet = ? AND crypto = 'USDT'");
$stmt_check->bind_param("s", $wallet_hash);
$stmt_check->execute();
$stmt_check->bind_result($current_balance);
$stmt_check->fetch();
$stmt_check->close();

$stmt_check = $conn->prepare("SELECT price FROM crypto WHERE coinCode = ?");
$stmt_check->bind_param("s", $coinCode);
$stmt_check->execute();
$stmt_check->bind_result($crypto_price);
$stmt_check->fetch();
$stmt_check->close();

if ($current_balance < $amount) {
    $response = array("error" => "Insufficient funds."); //Messaggio di errore AJAX che notifica il non avvenuto deposito
} else {
    //Inizia la transazione
    $conn->begin_transaction();

    try {
        if($current_balance == $amount){
            $stmt_delete_position = $conn->prepare("DELETE FROM position
                                                    WHERE wallet = ? AND crypto = 'USDT'");
            $stmt_delete_position->bind_param("s", $wallet_hash);

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
        }

        $stmt_check_position = $conn->prepare("SELECT positionId, amount FROM position WHERE wallet = '1C4D3A5F4B2C9E1A8D4E0F6B9A2C7D5E8B1A0D7E2F8C6A3D9E' AND crypto = ?");
        $stmt_check_position->bind_param("s", $coinCode);
        $stmt_check_position->execute();
        $result_check_position = $stmt_check_position->get_result();

        $crypto_amount = $amount/$crypto_price;

        if ($result_check_position->num_rows > 0){
            $stmt_update_balance_receiver = $conn->prepare("UPDATE position 
                                                            SET amount = amount - ?
                                                            WHERE wallet = '1C4D3A5F4B2C9E1A8D4E0F6B9A2C7D5E8B1A0D7E2F8C6A3D9E' AND crypto = ?");
            $stmt_update_balance_receiver->bind_param("ds", $crypto_amount, $coinCode);

            if (!$stmt_update_balance_receiver->execute()) {
                throw new Exception("Error during transaction: " . $stmt_update_balance_receiver->error);
            }

            $stmt_check_position_sender = $conn->prepare("SELECT positionId, amount FROM position WHERE wallet = ? AND crypto = ?");
            $stmt_check_position_sender->bind_param("ss", $wallet_hash, $coinCode);
            $stmt_check_position_sender->execute();
            $result_check_position_sender = $stmt_check_position_sender->get_result();

            if($result_check_position_sender->num_rows > 0){
                $stmt_update_balance_sender = $conn->prepare("UPDATE position 
                                                                SET amount = amount + ?
                                                                WHERE wallet = ? AND crypto = ?");
                $stmt_update_balance_sender->bind_param("dss", $crypto_amount, $wallet_hash, $coinCode);

                if (!$stmt_update_balance_sender->execute()) {
                    throw new Exception("Error during transaction: " . $stmt_update_balance_receiver->error);
                }
            }
            else{
                $stmt_create_position = $conn->prepare("INSERT INTO position (crypto, wallet, amount) VALUES (?, ?, ?)");

                $stmt_create_position->bind_param("ssd", $coinCode, $wallet_hash, $crypto_amount);

                if (!$stmt_create_position->execute()) {
                    throw new Exception("Error creating position: " . $stmt_create_position->error);
                }
            }
        }
        else{
            throw new Exception("Error creating position: no one is selling the crypto");
        }

        $fee = floatval($crypto_amount)/100;
        // Inserimento della transazione nella tabella transactions
        $stmt_insert_transaction = $conn->prepare("INSERT INTO transaction(amount, crypto, sellerWallet, buyerWallet, fee) VALUES (?, ?, '1C4D3A5F4B2C9E1A8D4E0F6B9A2C7D5E8B1A0D7E2F8C6A3D9E', ?, ?)");
        $stmt_insert_transaction->bind_param("dssd", $crypto_amount, $coinCode, $wallet_hash, $fee);

        if (!$stmt_insert_transaction->execute()) {
            throw new Exception("Error inserting transaction: " . $stmt_insert_transaction->error);
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

    if (isset($stmt_update_balance_sender)) {
        $stmt_update_balance_receiver->close();
    }

    if(isset($stmt_check_position_sender)){
        $stmt_check_position_sender->close();
    }

    if(isset($stmt_create_position)){
        $stmt_create_position->close();
    }

    if(isset($stmt_insert_transaction)){
        $stmt_insert_transaction->close();
    }
}

// Chiudi la connessione al database
$conn->close();

// Restituisci la risposta come JSON
echo json_encode($response);
?>