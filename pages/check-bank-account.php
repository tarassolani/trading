<?php
//Questo file si divide in 2 parti, in caso di deposito o prelievo
//Si basa sul controllo degli statement che riguardano sia la tabella dei conti bancari,
//che quella delle posizioni crypto: se soltanto una delle due tabelle contengono record
//che rendono impossibile la richiesta di deposito/prelievo, si procede con il rollback;
//altrimenti, si fa il commit
//Ciò funziona perché la valuta base di Regolare.com non sono dollari (USD), ma USDT,
//crypto stablecoin dal valore pari a quello del dollaro.
//Dunque, depositare su Regolare.com significa aprire una posizione in USDT
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

$type = $_GET['type']; //Tipo di transazione (deposito o prelievo)
$amount = $_GET['amount']; //Quantità di USDT interessata dalla transazione

$amount = floatval($amount);
$amount = round($amount, 2);

if ($type === 'deposit') { //Nel caso debba depositare

    $stmt_check = $conn->prepare("SELECT balance FROM BankAccount WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->bind_result($current_balance);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($current_balance < $amount) {
        echo "Error: Insufficient funds."; //Messaggio di errore AJAX che notifica il non avvenuto deposito
        exit();
    }

    //Inizia la transazione
    $conn->begin_transaction();

    try {
        //Query per aggiornare dati sul conto bancario
        $stmt_update_balance = $conn->prepare("UPDATE BankAccount SET balance = balance - ? WHERE username = ?");
        $stmt_update_balance->bind_param("ds", $amount, $username);

        if (!$stmt_update_balance->execute()) {
            throw new Exception("Error during deposit: " . $stmt_update_balance->error);
        }

        //Verifica se esiste già una posizione in USDT per l'utente
        $stmt_check_position = $conn->prepare("SELECT positionId, amount FROM position WHERE wallet = ? AND crypto = 'USDT'");
        $stmt_check_position->bind_param("s", $wallet_hash);
        $stmt_check_position->execute();
        $result_check_position = $stmt_check_position->get_result();

        if ($result_check_position->num_rows > 0) { //Se esiste già una posizione in USDT
            //Aggiorna l'amount della posizione esistente
            $position_row = $result_check_position->fetch_assoc();
            $positionId = $position_row['positionId'];
            $currentAmount = $position_row['amount'];

            $newAmount = $currentAmount + $amount;

            $stmt_update_position = $conn->prepare("UPDATE position SET amount = ? WHERE positionId = ?");
            $stmt_update_position->bind_param("di", $newAmount, $positionId);

            if (!$stmt_update_position->execute()) {
                throw new Exception("Error updating position: " . $stmt_update_position->error);
            }
        } else { //Se non esiste ancora una posizione
            //Crea una nuova posizione in USDT
            $stmt_create_position = $conn->prepare("INSERT INTO position (crypto, wallet, amount) VALUES ('USDT', ?, ?)");

            $stmt_create_position->bind_param("sd", $wallet_hash, $amount);

            if (!$stmt_create_position->execute()) {
                throw new Exception("Error creating position: " . $stmt_create_position->error);
            }
        }

        //Commit della transazione (si esegue solo se non sono presenti errori)
        $conn->commit();

        echo "success:Deposit completed successfully."; //Messaggio di successo AJAX

    } catch (Exception $e) {
        //Rollback della transazione in caso di errore
        $conn->rollback();
        echo "Error: " . $e->getMessage(); //Messaggio di errore AJAX
    }

    if (isset($stmt_update_balance)) {
        $stmt_update_balance->close();
    }
    
    if (isset($stmt_check_position)) {
        $stmt_check_position->close();
    }
    
    if (isset($stmt_update_position)) {
        $stmt_update_position->close();
    }

} elseif ($type === 'withdraw') { //Se devo prelevare, invece

    //Query per aggiornare dati sul conto bancario
    $stmt = $conn->prepare("UPDATE BankAccount SET balance = balance + ? WHERE username = ?");
    $stmt->bind_param("ds", $amount, $username);

    //Inizia la transazione
    $conn->begin_transaction();

    try {

        //Esegue l'operazione di prelievo
        if (!$stmt->execute()) {
            throw new Exception("Error during withdrawal: " . $stmt->error);
        }

        //Verifica se esiste una posizione in USDT per questo utente
        $stmt_check_position = $conn->prepare("SELECT positionId, amount FROM position WHERE wallet = ? AND crypto = 'USDT'");
        $stmt_check_position->bind_param("s", $wallet_hash);
        $stmt_check_position->execute();
        $result_check_position = $stmt_check_position->get_result();

        if ($result_check_position->num_rows > 0) {//Se esiste già una posizione in USDT (e deve per forza esistere)
            //Aggiorna l'amount della posizione esistente
            $position_row = $result_check_position->fetch_assoc();
            $positionId = $position_row['positionId'];
            $currentAmount = $position_row['amount'];

            //Dà errore, nel caso l'importo da prelevare sia superiore a quello della posizione
            if ($amount > $currentAmount) {
                throw new Exception("Insufficient funds in position.");
            }

            if (number_format($amount, 2) === number_format($currentAmount, 2)) {
                //Elimina la posizione se l'importo da prelevare è uguale all'importo della posizione
                $stmt_delete_position = $conn->prepare("DELETE FROM position WHERE positionId = ?");
                $stmt_delete_position->bind_param("s", $positionId);

                if (!$stmt_delete_position->execute()) {
                    throw new Exception("Error deleting position: " . $stmt_delete_position->error);
                }
            } else {
                //Altrimenti, aggiorna l'amount della posizione
                $newAmount = $currentAmount - $amount;

                $stmt_update_position = $conn->prepare("UPDATE position SET amount = ? WHERE positionId = ?");
                $stmt_update_position->bind_param("di", $newAmount, $positionId);

                if (!$stmt_update_position->execute()) {
                    throw new Exception("Error updating position: " . $stmt_update_position->error);
                }
            }
        }

        //Commit della transazione
        $conn->commit();

        echo "success:Withdrawal completed successfully."; //Messaggio di successo AJAX

    } catch (Exception $e) {
        //Rollback della transazione in caso di errore
        $conn->rollback();
        echo "Error: " . $e->getMessage(); //Messaggio di errore AJAX
    }

    $stmt->close();
    if (isset($stmt_update_balance)) {
        $stmt_update_balance->close();
    }
    
    if (isset($stmt_check_position)) {
        $stmt_check_position->close();
    }
    
    if (isset($stmt_update_position)) {
        $stmt_update_position->close();
    }

} else {
    echo "Error: Invalid transaction type."; //Messaggio di errore AJAX
}

$conn->close();
?>