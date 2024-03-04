<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com</title>
    <script src="scriptstartingcoins.js"></script>
    <script src="scriptsearchresults.js"></script>
</head>

<body>
    <div class="top-right-links">
        <a href="php/login.php"><strong>Sign in</strong></a> or <a href="php/signup.php"><strong>Sign up</strong></a>
    </div>

    <div class="container">
        <h1>Regolare.com</h1>
        <p class="description">La tua piattaforma di trading preferita.</p>
        <form name="searchform" action="search.php" method="post" class="search-form">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search crypto..." class="search-input">
                <span class="material-symbols-outlined size-medio" onclick="searchform.submit()">&#xe8b6;</span>
            </div>
        </form>
        <div class="crypto-slots">
            <!--<div class="crypto-slot"><img src="btc-icon.png" alt="BTC"><span class="crypto-title">BTC</span><br><span class="crypto-descr">+12,1%</span></div>
            <div class="crypto-slot"><img src="sol-icon.png" alt="SOL"><span class="crypto-title">SOL</span><br><span class="crypto-descr">+5,43%</span></div>
            <div class="crypto-slot"><img src="eth-icon.png" alt="ETH"><span class="crypto-title">ETH</span><br><span class="crypto-descr">+6,12%</span></div>-->
        </div>
        <div id="search-results">
        </div>
    </div>
</body>

</html>