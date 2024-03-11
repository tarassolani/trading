document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.getElementById('friends-table');

    searchInput.addEventListener('input', function () {
        var searchText = searchInput.value.trim();
        if (searchText.length > 0) {
            fetch(`../pages/get-users.php?searchText=${searchText}`)
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data);
                })
                .catch(error => console.error('Error:', error));
        } else {
            clearSearchResults();
        }
    });
    

    function displaySearchResults(results) {
        clearSearchResults();
        results.forEach(user => {
            const tr = searchResultsContainer.insertRow();
            tr.className = 'search-result-item';

            const td = tr.insertCell();
            td.textContent = user.username;

            const td2 = tr.insertCell();

            const span = document.createElement("span");
            span.textContent = "group_add";
            span.classList.add("material-symbols-outlined");

            td2.appendChild(span);

            span.addEventListener('click', function() {
                fetch(`../pages/add-friend.php?username=${user.username}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addFriend(user.username);
                        } else {
                            alert(data.error); // Mostra un alert con il messaggio di errore
                        }
                    })
                    .catch(error => console.error('Error:', error));});
                        
                searchResultsContainer.style.visibility = "visible";
            });
    }

    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }
});

function addFriend(friend){
    var table = document.getElementById("already-friend");

    var noFriendsElement = document.getElementById("no-friends");

    if (noFriendsElement) {
        noFriendsElement.remove();
    }

    const tr = table.insertRow();

    const td = tr.insertCell();
    td.textContent = friend;

    const td2 = tr.insertCell();

    const span1 = document.createElement("span");
    span1.textContent = "payments";
    span1.classList.add("material-symbols-outlined");

    td2.appendChild(span1);

    span1.addEventListener('click', function() {
        sendCrypto(friend);
    });

    const td3 = tr.insertCell();

    const span = document.createElement("span");
    span.textContent = "group_remove";
    span.classList.add("material-symbols-outlined");

    td3.appendChild(span);

    span.addEventListener('click', function() {
        removeFriendDB(friend);
    });
}

function removeFriendDB(friend){
    fetch(`../pages/remove-friend.php?username=${friend}`)
            .then(response => response.json())
            .then(data => {
                removeFriend(friend);
            })
            .catch(error => console.error('Error:', error));
}

function removeFriend(friend) {
    var table = document.getElementById("already-friend");
    var rows = table.getElementsByTagName("tr");

    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var cells = row.getElementsByTagName("td");
        
        if (cells.length > 0 && cells[0].textContent === friend) {
            row.remove();
        }
    }

    if (table.rows.length === 0) {
        const p = document.createElement("p");
        p.setAttribute("id", "no-friends");
        p.style.width = "300px";
        p.textContent = "You have no friends right now";

        table.appendChild(p);
    }
}

function sendCrypto(friend) {
    const amountToSend = prompt("Inserisci l'importo da inviare:");

    if (amountToSend !== null && amountToSend !== "") {
        fetch(`../pages/send-crypto.php?username=${friend}&amount=${amountToSend}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Errore nella richiesta.');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert("Errore: " + data.error);
                } else {
                    alert("Pagamento inviato con successo!");
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        alert("Inserisci un importo valido!");
    }
}