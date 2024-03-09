<?php
session_start();
if (isset($_COOKIE['login-info']) || isset($_SESSION['login-info'])) {
    $username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbRegolare";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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

    $stmt->close();
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
    <title>Account dashboard</title>
</head>

<body>
    <nav>
        <div id="menu-icon" class="material-symbols-outlined">&#xe5d2;</div>

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
        </div>
        <div id="right-div">
            <?php echo $otherInfo ?>
        </div>

    </div>

    <div class="account-balance">
        <h3>Account Balance</h3>
        <div id="chart-container"></div>
    </div>

    <div class="positions">
        <h3>Positions</h3>
        <table id="search-results-wider">
            <tr id="top-row">
                <th colspan="3" id="th-crypto">Crypto</th>
                <th id="th-crypto-price">Price</th>
                <th id="th-crypto-variation">Variation</th>
                <th id="th-crypto-supply">Supply</th>
                <th id="th-crypto-trading">Trading</th>
            </tr>
        </table>
    </div>
</body>
</html>