function copyContent() {
    var walletHash = document.getElementById("wallet-hash");

    var tempInput = document.createElement("textarea");
    tempInput.value = walletHash.textContent;
    document.body.appendChild(tempInput);

    tempInput.select();
    /*document.execCommand("copy");

    document.body.removeChild(tempInput);

    alert("Wallet hash copied!");*/
}

function createWallet() {
    var walletButton = document.getElementById("wallet-button");

    walletButton.disabled = true;
    walletButton.classList.add("loading");
    var loader = document.getElementById("loader");
    loader.style.display = "inline-block";

    setTimeout(function () {
        var uniqueHash = generateUniqueHash();

        var hashContainer = document.createElement("span");
        hashContainer.id = "wallet-hash";
        hashContainer.textContent = uniqueHash;

        var walletInfo = document.querySelector(".wallet-info");
        walletButton.style.display = "none";
        walletInfo.appendChild(hashContainer);

        updateDatabaseWithHash(uniqueHash);
        
    }, 2000);

}

function generateUniqueHash() {
    var characters = 'ABCDEF0123456789';
    var hash = '';

    for (var i = 0; i < 50; i++) {
        hash += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    return hash;
}

function updateDatabaseWithHash(hash) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '../pages/update-database.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Database updated successfully.');
        }
    };
    xhr.send('hash=' + hash);
}