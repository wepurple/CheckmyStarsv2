// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

let allData = []; // Stocke toutes les données

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => response.json())
    .then(data => {
        allData = data;
        displayData(data);
        setupFilters();
    })
    .catch(error => console.error('Erreur:', error));

// Fonction pour afficher les données
function displayData(data) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = ''; // Vide le tableau
    
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.Critere_ID}</td>
            <td>${critere.Critere_Description}</td>
            <td>${critere.Critere_statut}</td>
            <td>${critere.Critere_Points}</td>
        `;
    });
    
    // Affiche le nombre de résultats
    updateResultCount(data.length);
}

// Configure les filtres
function setupFilters() {
    const searchBar = document.getElementById('searchBar');
    const filterType = document.getElementById('filterType');
    
    // Événement sur la barre de recherche
    searchBar.addEventListener('input', filterData);
    
    // Événement sur la liste déroulante
    filterType.addEventListener('change', filterData);
}

// Fonction de filtrage
function filterData() {
    const searchTerm = document.getElementById('searchBar').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    
    if (!searchTerm) {
        displayData(allData);
        return;
    }
    
    const filteredData = allData.filter(critere => {
        switch(filterType) {
            case 'id':
                return critere.Critere_ID.toString().includes(searchTerm);
            case 'description':
                return critere.Critere_Description.toLowerCase().includes(searchTerm);
            case 'status':
                return critere.Critere_statut.toLowerCase().includes(searchTerm);
            case 'points':
                return critere.Critere_Points.toString().includes(searchTerm);
            case 'all':
            default:
                return critere.Critere_ID.toString().includes(searchTerm) ||
                       critere.Critere_Description.toLowerCase().includes(searchTerm) ||
                       critere.Critere_statut.toLowerCase().includes(searchTerm) ||
                       critere.Critere_Points.toString().includes(searchTerm);
        }
    });
    
    displayData(filteredData);
}

// Optionnel : Affiche le nombre de résultats
function updateResultCount(count) {
    let countElement = document.getElementById('result-count');
    if (!countElement) {
        countElement = document.createElement('div');
        countElement.id = 'result-count';
        countElement.style.marginTop = '10px';
        document.querySelector('.search-filter-container').appendChild(countElement);
    }
    countElement.textContent = `${count} résultat(s) trouvé(s)`;
}
