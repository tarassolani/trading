<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Regolare.com</title>
    <script src="js/script-starting-coins.js"></script>
    <script src="js/script-search-results.js"></script>
    <script src="homepage-real-time-prices.js"></script>
</head>

<body>
    <div class="top-right-links">
        <a href="pages/signin.php"><strong>Sign in</strong></a> or <a href="pages/signup.html"><strong>Sign up</strong></a>
    </div>

    <div class="container">
        <h1>Regolare.com</h1>
        <p class="description">La tua piattaforma di trading preferita.</p>
        <form name="searchform" action="search/search.php" method="post" class="search-form">
            <div class="search-bar">
                <input type="text" name="search" placeholder="Search crypto..." class="search-input">
                <span class="material-symbols-outlined size-medio" onclick="searchform.submit()">&#xe8b6;</span>
            </div>
        </form>
        <div class="crypto-slots">
        </div>
        <table id="search-results">
        </table>
    </div>
</body>

</html>