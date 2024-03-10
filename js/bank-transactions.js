function linkBankAccount(){
    window.location.href = "add-bank-account.html";
}

function transaction(type) {
    // Nascondi i pulsanti di deposito e prelievo
    document.getElementById("buttons").style.display = "none";

    // Rimuove eventuali input e pulsanti precedentemente aggiunti
    removeInput();

    // Crea l'input per inserire la quantità
    var inputId = type === "deposit" ? "deposit-amount-input" : "withdraw-amount-input";
    var input = document.createElement("input");
    input.type = "number";
    input.id = inputId;
    input.className = "search-input";
    input.placeholder = "Enter amount";
    input.style.marginLeft = "30px";

    // Crea il pulsante di conferma
    var confirmButton = document.createElement("button");
    confirmButton.id = "confirm-button";
    confirmButton.textContent = "OK";
    confirmButton.onclick = function() {
        var amount = document.getElementById(inputId).value;
        // Verifica che l'importo non sia vuoto o negativo
        if (amount.trim() !== "" && parseFloat(amount) > 0) {
            // Chiamare il file PHP per il deposito/prelievo passando l'importo e il tipo
            window.location.href = "check-bank-account.php?type=" + type + "&amount=" + amount;
            // Rimuove l'input e il pulsante di conferma dopo la conferma della transazione
            removeInput();
            // Ripristina i pulsanti di deposito e prelievo
            document.getElementById("buttons").style.display = "inline-block";
        } else {
            alert("Please enter a valid amount.");
        }
    };

    // Crea il pulsante di annulla
    var cancelButton = document.createElement("button");
    cancelButton.textContent = "Cancel";
    cancelButton.id = "cancel-button";
    cancelButton.onclick = function() {
        // Ripristina i pulsanti di deposito e prelievo
        document.getElementById("buttons").style.display = "inline-block";
        // Rimuove l'input e il pulsante di conferma
        removeInput();
    };

    // Aggiunge l'input e i pulsanti di conferma e annulla accanto ai pulsanti di deposito/prelievo
    var container = document.querySelector(".account-balance > div");
    container.appendChild(input);
    container.appendChild(confirmButton);
    container.appendChild(cancelButton);
}

function removeInput() {
    // Rimuovi l'input per il deposito
    var depositInput = document.getElementById("deposit-amount-input");
    if (depositInput) {
        depositInput.remove();
    }

    // Rimuovi l'input per il prelievo
    var withdrawInput = document.getElementById("withdraw-amount-input");
    if (withdrawInput) {
        withdrawInput.remove();
    }

    // Rimuovi il pulsante di conferma
    var confirmButton = document.getElementById("confirm-button");
    if (confirmButton) {
        confirmButton.remove();
    }

    // Rimuovi il pulsante di annulla
    var cancelButton = document.getElementById("cancel-button");
    if (cancelButton) {
        cancelButton.remove();
    }
}