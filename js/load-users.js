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

    //Funzione che mostra tutti i risultati di ricerca
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
                        addFriend(user.username);
                    })
                    .catch(error => console.error('Error:', error));
            });
            
            searchResultsContainer.style.visibility = "visible";
        });
    }

    function addFriend(friend){
        var table = document.getElementById("already-friend");
        table.textContent = "";

        const td = tr.insertCell();
        td.textContent = friend;

        const td2 = tr.insertCell();

        const span = document.createElement("span");
        span.textContent = "group_add";
        span.classList.add("material-symbols-outlined");
    }

    //Funzione che pulisce i risultati di ricerca, quando l'input è vuoto
    function clearSearchResults() {
        searchResultsContainer.innerHTML = '';
        searchResultsContainer.style.visibility = "hidden";
    }
});