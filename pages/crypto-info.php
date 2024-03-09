<?php
$coinCode = isset($_GET["coinCode"]) ? strtoupper($_GET["coinCode"]) : "";
$chartName = $coinCode . "USDT";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbRegolare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Icon FROM crypto WHERE coinCode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $coinCode);
$stmt->execute();
$stmt->bind_result($icon);
$stmt->fetch();
$stmt->close();

$conn->close();

if ($icon) {
    $base64Icon = base64_encode($icon);
    $imgSrc = "data:image/jpeg;base64," . $base64Icon;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/crypto-info.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com - Crypto Info</title>
</head>

<body>
    <nav>
        <div id="menu-icon" class="material-symbols-outlined">&#xe5d2;</div>

        <a id="logo" href="../index.php">Regolare.com</a>

        <div class="top-right-links">
            <a href="../pages/signin.php"><strong>Sign in</strong></a> or <a href="../pages/signup.html"><strong>Sign
                    up</strong></a>
        </div>
    </nav>

    <section class="crypto-info">
        <div class="crypto-left">
            <img src="<?php echo $imgSrc ?>" alt="crypto" class="crypto-img" style="float: left;">
            <span id="crypto-name" style="float: center; margin-left: 10px;"><strong>
                    <?php echo $coinCode ?>/USDT
                </strong>
            </span>
            <br>
            <span id="crypto-price" style="float: center; margin-left: 10px;">$69,000.00</span>
            <span id="crypto-percent" style="float: center; margin-left: 10px;">11.22%</span>
        </div>

        <div class="crypto-right">

            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container" id="chart-container" style="height:100%;width:100%">
                <div class="tradingview-widget-container__widget" id="chart-div"
                    style="height:calc(100% - 32px);width:100%;"></div>
                <script type="text/javascript"
                    src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                        {
                            "autosize": true,
                                "symbol": "<?php echo $chartName ?>",
                                    "interval": "D",
                                        "timezone": "Europe/Rome",
                                            "theme": "dark",
                                                "style": "1",
                                                    "locale": "en",
                                                        "enable_publishing": false,
                                                            "hide_top_toolbar": true,
                                                                "calendar": false,
                                                                    "backgroundColor": "rgba(7, 7, 7, 1)",
                                                                        "gridColor": "rgba(78, 172, 255, 0.06)",
                                                                            "support_host": "https://www.tradingview.com"
                        }
                    </script>
            </div>
            <!-- TradingView Widget END -->

            <div class="buy-sell-container">
                <div class="buy-sell-buttons">
                    <a href="#" class="pulsante compra">Buy</a>
                    <label for="buy-amount" class="dollar-label">$</label>
                    <div class="custom-number-input">
                        <button class="decrement-button" onclick="decrement('buy-amount')">-</button>
                        <input type="number" id="buy-amount" value="10" min="1" step="10" placeholder="Amount">
                        <button class="increment-button" onclick="increment('buy-amount')">+</button>
                    </div>
                    <a href="#" class="pulsante vendi">Sell</a>
                    <label for="sell-amount" class="dollar-label">$</label>
                    <div class="custom-number-input">
                        <button class="decrement-button" onclick="decrement('sell-amount')">-</button>
                        <input type="number" id="sell-amount" value="10" min="1" step="10" placeholder="Amount">
                        <button class="increment-button" onclick="increment('sell-amount')">+</button>
                    </div>
                </div>
            </div>

            <script>
                function increment(id) {
                    var input = document.getElementById(id);
                    input.stepUp();
                }

                function decrement(id) {
                    var input = document.getElementById(id);
                    input.stepDown();
                }
            </script>
        </div>
    </section>

</body>

</html>