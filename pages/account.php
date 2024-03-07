<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/account.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
            <h2 id="username">@unclepear</h2>
            <p id="name-surname">Benjamin 'Uncle' Pearson</p>

            <div class="wallet-info">
                <h3>Wallet Address</h3>
                <span class="material-symbols-outlined copy-btn" onclick="copyContent()">content_copy</span>
                <div class="hash-container">
                    <span id="wallet-hash">
                        8016AC908B188B88E3DA81624AE6E9661C9096D832A0FA57681C6556FEDDE6DA79825BF838AB3C14F9C13174AEF4330C3A5A32BD9B3A7B0C96124F94D70E011898E4A2BFB0F3F556F30BDA0FFC0C5675
                    </span>
                </div>
            </div>
        </div>
        <div id="right-div">
        </div>

    </div>

    <script>
        function copyContent() {
            var walletHash = document.getElementById("wallet-hash");

            var tempInput = document.createElement("textarea");
            tempInput.value = walletHash.textContent;
            document.body.appendChild(tempInput);

            tempInput.select();
            document.execCommand("copy");

            document.body.removeChild(tempInput);

            alert("Wallet hash copied!");
        }
    </script>

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