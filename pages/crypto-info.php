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

$sql = "SELECT Icon, price, variation, name FROM crypto WHERE coinCode = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $coinCode);
$stmt->execute();
$stmt->bind_result($icon, $price, $variation, $cryptoName);
$stmt->fetch();
$stmt->close();

$conn->close();

if ($icon) {
    $base64Icon = base64_encode($icon);
    $imgSrc = "data:image/jpeg;base64," . $base64Icon;
}

$apiKey = 'a3975305-35e9-47e3-baae-a04ab54de810';

$cmcData = fetchDataFromApi($coinCode, $apiKey);

function fetchDataFromApi($coinCode, $apiKey)
{
    $cmcUrl = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?symbol=$coinCode&convert=USDT";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cmcUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'X-CMC_PRO_API_KEY: ' . $apiKey
        )
    );

    $cmcResponse = curl_exec($ch);
    curl_close($ch);

    return json_decode($cmcResponse, true);
}

// Aggiorna le variabili con dati dall'API
if (isset($cmcData['data'][$coinCode]['quote']['USDT'])) {
    $quote = $cmcData['data'][$coinCode]['quote']['USDT'];

    $volume24 = isset($cmcData['data'][$coinCode]['quote']['USDT']['volume_24h']) ? $cmcData['data'][$coinCode]['quote']['USDT']['volume_24h'] : null;
    $totalSupply = isset($cmcData['data'][$coinCode]['total_supply']) ? $cmcData['data'][$coinCode]['total_supply'] : null;
    $maxSupply = isset($cmcData['data'][$coinCode]['max_supply']) ? $cmcData['data'][$coinCode]['max_supply'] : null;
    $circulatingSupply = isset($cmcData['data'][$coinCode]['circulating_supply']) ? $cmcData['data'][$coinCode]['circulating_supply'] : null;
    $marketCap = isset($cmcData['data'][$coinCode]['quote']['USDT']['market_cap']) ? $cmcData['data'][$coinCode]['quote']['USDT']['market_cap'] : null;

    $percent_change_1h = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_1h']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_1h'] : null;
    $percent_change_7d = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_7d']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_7d'] : null;
    $percent_change_30d = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_30d']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_30d'] : null;
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
            <span id="crypto-price" style="float: center; margin-left: 10px;"
                class="<?php echo ($variation > 0) ? 'crypto-price-green' : 'crypto-price-red'; ?>">
                $
                <?php echo number_format($price, 2) ?>
            </span>
            <span id="crypto-percent" style="float: center; margin-left: 10px;"
                class="<?php echo ($variation > 0) ? 'highlight-green' : 'highlight-red'; ?>">
                <?php echo ($variation > 0) ? '+' . number_format($variation, 2) : number_format($variation, 2); ?>%
            </span>

            <p class="par-titles"><strong>Details about
                    <?php echo $cryptoName ?>:
                </strong></p>
            <ul>
                <li class="crypto-info-item">
                    <p><strong>Volume 24h:</strong></p>
                    <p class="crypto-info-value">
                        <?php echo ($volume24 !== null) ? number_format($volume24) : 'N/A'; ?>
                    </p>
                </li>
                <li class="crypto-info-item">
                    <p><strong>Total Supply:</strong></p>
                    <p class="crypto-info-value">
                        <?php echo ($totalSupply !== null) ? number_format($totalSupply) : 'N/A'; ?>
                    </p>
                </li>
                <li class="crypto-info-item">
                    <p><strong>Max Supply:</strong></p>
                    <p class="crypto-info-value">
                        <?php echo ($maxSupply !== null) ? number_format($maxSupply) : 'N/A'; ?>
                    </p>
                </li>
                <li class="crypto-info-item">
                    <p><strong>Circulating Supply:</strong></p>
                    <p class="crypto-info-value">
                        <?php echo ($circulatingSupply !== null) ? number_format($circulatingSupply) : 'N/A'; ?>
                    </p>
                </li>
                <li class="crypto-info-item">
                    <p><strong>Market Cap:</strong></p>
                    <p class="crypto-info-value">
                        <?php echo ($marketCap !== null) ? "$ " . number_format($marketCap) : 'N/A'; ?>
                    </p>
                </li>
            </ul>

            <p class="par-titles"><strong>Performance:</strong></p>
            <div class="performance-container">
                <div class="performance-box <?php echo ($percent_change_1h > 0) ? 'positive' : 'negative'; ?>">
                    <p class="performance-value">
                        <?php echo ($percent_change_1h !== null) ? (($percent_change_1h > 0) ? '+' : '') . number_format($percent_change_1h, 2) . '%' : 'N/A'; ?>
                    </p>
                    <p class="performance-time">1H</p>
                </div>
                <div class="performance-box <?php echo ($variation > 0) ? 'positive' : 'negative'; ?>">
                    <p class="performance-value">
                        <?php echo ($variation !== null) ? (($variation > 0) ? '+' : '') . number_format($variation, 2) . '%' : 'N/A'; ?>
                    </p>
                    <p class="performance-time">1D</p>
                </div>
                <div class="performance-box <?php echo ($percent_change_7d > 0) ? 'positive' : 'negative'; ?>">
                    <p class="performance-value">
                        <?php echo ($percent_change_7d !== null) ? (($percent_change_7d > 0) ? '+' : '') . number_format($percent_change_7d, 2) . '%' : 'N/A'; ?>
                    </p>
                    <p class="performance-time">1W</p>
                </div>
                <div class="performance-box <?php echo ($percent_change_30d > 0) ? 'positive' : 'negative'; ?>">
                    <p class="performance-value">
                        <?php echo ($percent_change_30d !== null) ? (($percent_change_30d > 0) ? '+' : '') . number_format($percent_change_30d, 2) . '%' : 'N/A'; ?>
                    </p>
                    <p class="performance-time">1M</p>
                </div>
            </div>
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