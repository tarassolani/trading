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
    <script src="../js/load-users.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
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

            <script>
            <?php if (!empty($walletContent)): ?>
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
        <div class="chart-container" style="height: 300px;">
            <span id="total-balance">1000 USDT</span>
            <canvas id="myChart"></canvas> 
        </div>

        <script>
            var data = {
                labels: ["1", "2", "3", "4", "5", "6", "7"], 
                datasets: [{
                    label: "",
                    data: [10, 20, 30, 40, 50, 60, 70],
                    backgroundColor: ["#3366ff", "#3399ff", "#33ccff", "#33ffff", "#66ffff", "#99ffff", "#ccffff"],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.4,
                }]
            };

            var ctx = document.getElementById('myChart').getContext('2d'); 
            var myChart = new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        </script>
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

    <div class="friends">
        <h3>Friends</h3>
        <div class="search-bar-right-holder">
            <div class="search-bar-wide">
                <input type="text" name="search" placeholder="Search for user..." class="search-input">
                <span class="material-symbols-outlined size-medio" onclick="searchform.submit()">&#xe8b6;</span>
            </div>
        </div>
        <table id = "already-friend">You have no friends right now</table>
        <table id="friends-table">
        </table>
    </div>
</body>
</html>