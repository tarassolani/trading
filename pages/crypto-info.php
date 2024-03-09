<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/crypto-info.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com - Crypto Info</title>
</head>

<body>
    <nav>
        <div id="menu-icon" class="material-symbols-outlined">&#xe5d2;</div>

        <a id="logo" href="../index.php">Regolare.com</a>

        <div class="top-right-links">
            <a href="../pages/signin.php"><strong>Sign in</strong></a> or <a href="../pages/signup.html"><strong>Sign up</strong></a>
        </div>
    </nav>

    <section class="cripto-info">
        <div class="cripto-left">
            <img src="crypto-image.jpg" alt="crypto" style="float: left;">
            <span id="crypto-name" style="float: center; margin-left: 10px;">BTC/USDT</span>
            <br>
            <span id="crypto-price" style="float: center; margin-left: 10px;">$69,000.00</span>
            <span id="crypto-percent" style="float: center; margin-left: 10px;">11.22%</span>
        </div>
        <div class="cripto-right">
            <!-- TradingView Widget BEGIN -->
            <div class="tradingview-widget-container" style="height:100%;width:100%">
            <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
            <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
            {
            "autosize": true,
            "symbol": "BTCUSDT",
            "interval": "D",
            "timezone": "Etc/UTC",
            "theme": "dark",
            "style": "1",
            "locale": "en",
            "enable_publishing": false,
            "hide_top_toolbar": true,
            "calendar": false,
            "support_host": "https://www.tradingview.com"
            }
            </script>
            </div>
            <!-- TradingView Widget END -->
            
            <div class="buy-sell-buttons">
                <a href="#" class="pulsante compra">Compra</a>
                <a href="#" class="pulsante vendi">Vendi</a>
            </div>
        </div>
    </section>

    </body>
</html>
