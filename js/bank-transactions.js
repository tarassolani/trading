function linkBankAccount() {
    window.location.href = "add-bank-account.html";
}

function transaction(type) {
    // Nascondi i pulsanti di deposito e prelievo
    document.getElementById("buttons").style.display = "none";

    const response_div = document.getElementById("response");
    if (response_div) {
        response_div.textContent = "";
    }

    // Rimuove eventuali input e pulsanti precedentemente aggiunti
    removeInput();

    // Crea l'input per inserire la quantità
    var inputId = type === "deposit" ? "deposit-amount-input" : "withdraw-amount-input";
    var input = document.createElement("input");
    input.type = "number";
    input.id = inputId;
    input.className = "search-input";
    input.placeholder = "Enter amount";

    // Crea il pulsante di conferma
    var confirmButton = document.createElement("button");
    confirmButton.classList.add("deposit-button");
    confirmButton.classList.add("ok-button");
    confirmButton.id = "confirm-button";
    confirmButton.textContent = "OK";
    confirmButton.onclick = function () {
        var amount = document.getElementById(inputId).value;
        // Verifica che l'importo non sia vuoto o negativo
        if (amount.trim() !== "" && parseFloat(amount) > 0) {
            // Creazione dell'oggetto XMLHttpRequest
            var xhr = new XMLHttpRequest();

            // Definizione della funzione di gestione della risposta
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    const response_div = document.getElementById('response');

                    if (xhr.status === 200) {
                        // Se la richiesta è stata eseguita con successo
                        var response = xhr.responseText;

                        if (response.startsWith("success")) {
                            response_div.style.color = "#089981";
                            response_div.textContent = "Transaction completed successfully.";

                            var totalBalanceSpan = document.getElementById("total-balance");
                            if (totalBalanceSpan) {
                                //Aggiorna il account balance
                                var currentBalance = parseFloat(totalBalanceSpan.textContent);
                                var updatedBalance = type === "deposit" ? currentBalance + parseFloat(amount) : currentBalance - parseFloat(amount);
                                totalBalanceSpan.textContent = updatedBalance.toFixed(2) + " USDT";
                            }

                            removeInput();
                            document.getElementById("buttons").style.display = "inline-block";
                            window.location.reload(true);

                        } else {
                            response_div.style.color = "#F23645"
                                ; response_div.textContent = response;
                        }
                    } else {
                        // Gestione degli errori della richiesta AJAX
                        response_div.style.color = "#F23645"
                            ; response_div.textContent = "Error: Unable to complete the request.";
                    }
                }
            };
            // Ripristina i pulsanti di deposito e prelievo
            document.getElementById("buttons").style.display = "inline-block";
            // Rimuove l'input e il pulsante di conferma
            removeInput();

            // Apertura della richiesta
            xhr.open("GET", "check-bank-account.php?type=" + type + "&amount=" + amount, true);

            // Invio della richiesta
            xhr.send();
        } else {
            alert("Please enter a valid amount.");
        }
    };

    // Crea il pulsante di annulla
    var cancelButton = document.createElement("button");
    cancelButton.classList.add("withdraw-button");
    cancelButton.textContent = "Cancel";
    cancelButton.id = "cancel-button";
    cancelButton.onclick = function () {
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