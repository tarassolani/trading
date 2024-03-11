<?php
session_start();

//Ottengo i valori di tutti i parametri GET
$coinCode = isset($_GET["coinCode"]) ? strtoupper($_GET["coinCode"]) : "";
$imgSrc = isset($_GET["imgSrc"]) ? $_GET["imgSrc"] : "";
$cryptoName = isset($_GET["cryptoName"]) ? $_GET["cryptoName"] : "";
$amount = isset($_GET["amount"]) ? $_GET["amount"] : 0;

//Inizio API CoinMarketCap
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
//Fine API CoinMarketCap

//Valori dei dati dell'API assegnati alle variabili
if (isset($cmcData['data'][$coinCode]['quote']['USDT'])) {
    $quote = $cmcData['data'][$coinCode]['quote']['USDT'];

    $price = isset($cmcData['data'][$coinCode]['quote']['USDT']['price']) ? $cmcData['data'][$coinCode]['quote']['USDT']['price'] : null;
    $percent_change_24h = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_24h']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_24h'] : null;
    $volume24 = isset($cmcData['data'][$coinCode]['quote']['USDT']['volume_24h']) ? $cmcData['data'][$coinCode]['quote']['USDT']['volume_24h'] : null;
    $totalSupply = isset($cmcData['data'][$coinCode]['total_supply']) ? $cmcData['data'][$coinCode]['total_supply'] : null;
    $maxSupply = isset($cmcData['data'][$coinCode]['max_supply']) ? $cmcData['data'][$coinCode]['max_supply'] : null;
    $circulatingSupply = isset($cmcData['data'][$coinCode]['circulating_supply']) ? $cmcData['data'][$coinCode]['circulating_supply'] : null;
    $marketCap = isset($cmcData['data'][$coinCode]['quote']['USDT']['market_cap']) ? $cmcData['data'][$coinCode]['quote']['USDT']['market_cap'] : null;

    $percent_change_1h = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_1h']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_1h'] : null;
    $percent_change_7d = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_7d']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_7d'] : null;
    $percent_change_30d = isset($cmcData['data'][$coinCode]['quote']['USDT']['percent_change_30d']) ? $cmcData['data'][$coinCode]['quote']['USDT']['percent_change_30d'] : null;

    $valueInUSDT = $amount * $price; //Aggiorno anche il valore della crypto in USDT
}
?>

<!-- Inizio parte HTML, che aggiorna i dati della parte HTML già presente in crypto-info.php -->
<img src="<?php echo $imgSrc ?>" alt="crypto" class="crypto-img" style="float: left;">
<span id="crypto-name" style="float: center; margin-left: 10px;"><strong>
        <?php echo $coinCode ?>/USDT
    </strong>
</span>
<br>
<span id="crypto-price" style="float: center; margin-left: 10px;"
    class="<?php echo ($percent_change_24h > 0) ? 'crypto-price-green' : 'crypto-price-red'; ?>">
    $
    <?php echo number_format($price, 2) ?>
</span>
<span id="crypto-percent" style="float: center; margin-left: 10px;"
    class="<?php echo ($percent_change_24h > 0) ? 'highlight-green' : 'highlight-red'; ?>">
    <?php echo ($percent_change_24h > 0) ? '+' . number_format($percent_change_24h, 2) : number_format($percent_change_24h, 2); ?>%
</span>

<!-- DETTAGLI sulla crypto -->
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

<!-- PERFORMANCE della crypto -->
<p class="par-titles"><strong>Performance:</strong></p>
<div class="performance-container">
    <div class="performance-box <?php echo ($percent_change_1h > 0) ? 'positive' : 'negative'; ?>">
        <p class="performance-value">
            <?php echo ($percent_change_1h !== null) ? (($percent_change_1h > 0) ? '+' : '') . number_format($percent_change_1h, 2) . '%' : 'N/A'; ?>
        </p>
        <p class="performance-time">1H</p>
    </div>
    <div class="performance-box <?php echo ($percent_change_24h > 0) ? 'positive' : 'negative'; ?>">
        <p class="performance-value">
            <?php echo ($percent_change_24h !== null) ? (($percent_change_24h > 0) ? '+' : '') . number_format($percent_change_24h, 2) . '%' : 'N/A'; ?>
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

<?php
//Questa volta, non viene più eseguita nessuna query al database per ottenere amount
//dato che il suo valore cambia solo se l'utente fa uso dai tasti "buy" e "sell".
//Ci si limita a controllare se l'utente ha eseguito il login
if (isset($_SESSION['login-info']) || isset($_COOKIE['login-info'])) {
    ?>
    <div id="your-crypto">
        <p class="par-titles"><strong>Your
                <?php echo $cryptoName ?>:
            </strong></p>
        <ul>
            <li class="crypto-info-item">
                <p><strong>Amount:</strong></p>
                <p class="crypto-info-value">
                    <?php echo ($amount !== 0) ? number_format($amount) : 'N/A'; ?>
                </p>
            </li>
            <li class="crypto-info-item">
                <p><strong>Value in USDT:</strong></p>
                <p class="crypto-info-value">
                    <?php echo ($valueInUSDT !== 0) ? number_format($valueInUSDT, 2) : 'N/A'; ?>
                </p>
            </li>
        </ul>
    </div>
    <?php
}
?>