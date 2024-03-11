<?php
session_start();
$username = isset($_SESSION['login-info']) ? $_SESSION['login-info'] : $_COOKIE['login-info'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com</title>
    <script src="../js/real-time-prices.js"></script>
    <script src="searchpage-search-script.js"></script>
</head>

<body>
    <nav>
        <div id="menu-icon" class="material-symbols-outlined">&#xe8b6;</div>

        <a id="logo" href="../index.php">Regolare.com</a>

        <div class="top-right-links">
        <?php if ($username): ?>
            <strong>Hello <a href="../pages/account.php"><?php echo $username; ?></a></strong>
        <?php else: ?>
            <a href="../pages/signin.php"><strong>Sign in</strong></a> or <a href="../pages/signup.html"><strong>Sign up</strong></a>
        <?php endif; ?>
        </div>
    </nav>

    <h2>Crypto screener</h2>
    <div class="search-bar-right-holder">
        <div class="search-bar-wide">
            <input type="text" name="search" placeholder="Search for crypto..." class="search-input">
            <span class="material-symbols-outlined size-medio" onclick="searchform.submit()">&#xe8b6;</span>
        </div>
    </div>

    <table id="search-results-wider">
        <tr id="top-row">
            <th colspan="3" id="th-crypto">
                Crypto coin
            </th>
            <th id="th-crypto-price">
                Price
            </th>
            <th id="th-crypto-variation">
                24h variation
            </th>
            <th id="th-crypto-supply">
                Supply
            </th>
        </tr>
    </table>
</body>

</html>