<?php
include 'connect-to-db.php';
session_start();
if (isset($_COOKIE['login-info']) || isset($_SESSION['login-info'])) { 
    $username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];
  
    $sql = "SELECT name, surname, birthDate, country, city, street, streetNumber, phoneNumber, email, hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $userData = null;
    }

    // Query per ottenere l'IBAN dell'account bancario associato all'utente
    $sql_iban = "SELECT iban FROM BankAccount WHERE username = ?";
    $stmt_iban = $conn->prepare($sql_iban);
    $stmt_iban->bind_param("s", $username);
    $stmt_iban->execute();
    $result_iban = $stmt_iban->get_result();

    // Estrai l'IBAN se esiste
    if ($result_iban->num_rows > 0) {
        $iban_row = $result_iban->fetch_assoc();
        $iban = $iban_row['iban'];
    } else {
        $iban = null; // Imposta IBAN a null se l'utente non ha un account bancario associato
    }

    $stmt->close();

    $sql_assets = "SELECT SUM(amount * price) AS balance FROM position 
                    INNER JOIN users ON hash = wallet
                    INNER JOIN crypto ON crypto = coinCode
                    WHERE username = ?";
    $stmt_assets = $conn->prepare($sql_assets);
    $stmt_assets->bind_param("s", $username);
    $stmt_assets->execute();
    $result_assets = $stmt_assets->get_result();
    
    // Estrai il valore totale degli asset
    if ($result_assets->num_rows > 0) {
        $assets_row = $result_assets->fetch_assoc();
        $balance = $assets_row['balance'];

        if($balance == null){
            $balance = 0;
        }
    } else {
        $balance = 0; // Imposta il valore totale su zero se l'utente non ha asset criptovalutari
    }
    
    $stmt_assets->close();
    $conn->close();

    $walletContent = ($userData['hash'] === null || $userData['hash'] === "") ? '<button id="wallet-button" onclick="createWallet()">
    <span class="material-symbols-outlined" id="loader" class="loader"></span>
    Create Wallet
    </button>' : '<span id="wallet-hash">' . $userData['hash'] . '</span>';

    $otherInfo = "<p>{$userData['birthDate']}</p>
                  <p>{$userData['country']}, {$userData['city']}</p>    
                  <p>{$userData['street']} {$userData['streetNumber']}</p>
                  <p>{$userData['phoneNumber']}</p>
                  <p>{$userData['email']}</p>";
    $nameLastname = $userData['name'] . " " . $userData['surname'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="../js/account.js"></script>
    <script src="../js/load-users.js"></script>
    <script src="../js/bank-transactions.js"></script>
    <title>Account dashboard</title>
</head>

<body>
    <nav>
        <div id="menu-icon" class="material-symbols-outlined" onclick="window.open('../search/search.php', '_self')">&#xe8b6;</div>

        <a id="logo" href="../index.php">Regolare.com</a>

    </nav>

    <h2 id="account-dashboard">Account dashboard</h2>

    <div class="account-info">
        <div id="left-div">
            <h2 id="username">@<?php echo $username ?></h2>
            <p id="name-surname">
                <?php echo $nameLastname ?>
            </p>

            <div class="wallet-info">
                <br>
                <h3>Wallet Address</h3>
                <span class="material-symbols-outlined copy-btn" id="btn-inviz" onclick="copyContent()">content_copy</span>
                <div class="hash-container">
                    <?php echo $walletContent ?>
                </div>
            </div>

            <script>
            <?php if (!empty($userData['hash'])): ?>
                var btnCopy = document.getElementById('btn-inviz');
                btnCopy.style.visibility = "visible";
            <?php endif; ?>
            </script>

        </div>
        <div id="right-div">
            <?php echo $otherInfo ?>
        </div>

    </div>

    <div class="account-balance">
        <h3>Account Balance</h3>
        <span id="total-balance"><?php echo $balance . " USDT"?></span>
        <div style="margin-top: 15px">
            <?php if ($iban === null): ?>
                <!-- Mostra il pulsante "Link bank account" solo se l'utente non ha un conto bancario collegato -->
                <button id="link-bank-account-button" onclick="linkBankAccount()">Link bank account</button>
            <?php else: ?>
                <!-- Mostra i pulsanti di deposito e prelievo se l'utente ha un conto bancario collegato -->
                <div id="buttons">
                    <button class="transaction-button" id="deposit-button" onclick="transaction('deposit')">Deposit</button>
                    <button class="transaction-button" id="withdraw-button" onclick="transaction('withdraw')">Withdraw</button>
                </div>
                <div id="response" style="margin-top: 10px"></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="positions">
        <h3>Positions</h3>
        <table id="search-results-wider">
            <tr id="top-row">
                <th colspan="3" id="th-crypto">Crypto</th>
                <th id="th-crypto-price">Price</th>
                <th id="th-crypto-variation">Variation</th>
                <th id="th-crypto-amount">Amount</th>
            </tr>
        </table>
    </div>

    <div class="friends">
        <h3>Friends</h3>
        <div class="search-bar-right-holder">
            <div class="search-bar-wide">
                <input type="text" name="search" placeholder="Search for user..." class="search-input">
                <span class="material-symbols-outlined size-medio" onclick="searchform.submit()">&#xe8b6;</span>
            </div>
        </div>
        <table id="already-friend">
            <?php
            $username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

            $servername = "localhost";
            $db_username = "root";
            $password = "";
            $dbname = "dbRegolare";
        
            $conn = new mysqli($servername, $db_username, $password, $dbname);
        
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Query per ottenere gli amici dell'utente
            $sql_friends = "SELECT friend1, friend2 FROM Friends WHERE friend1 = ? OR friend2 = ?";
            $stmt_friends = $conn->prepare($sql_friends);
            $stmt_friends->bind_param("ss", $username, $username);
            $stmt_friends->execute();
            $result_friends = $stmt_friends->get_result();

            // Array per memorizzare gli amici
            $friends_array = array();

            // Popola l'array degli amici
            while ($row_friends = $result_friends->fetch_assoc()) {
                if ($row_friends['friend1'] == $username) {
                    $friends_array[] = $row_friends['friend2'];
                } else {
                    $friends_array[] = $row_friends['friend1'];
                }
            }

            // Chiudi la query degli amici
            $stmt_friends->close();

            // Popola la tabella degli amici già presenti
            if (!empty($friends_array)) {
                foreach ($friends_array as $friend_username) {
                    echo "<tr><td>{$friend_username}</td><td><span class='material-symbols-outlined' onclick=\"sendCrypto('{$friend_username}')\">payments</span></td><td><span class='material-symbols-outlined' onclick=\"removeFriendDB('{$friend_username}')\">group_remove</span></td></tr>";
                }
            } else {
                echo '<p id="no-friends">You have no friends right now</p>';
            }
            $conn->close();
            ?>
        </table>

        <table id="friends-table">
        </table>
    </div>
</body>
</html>