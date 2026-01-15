// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

let allData = []; // Stocke toutes les données

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => response.json())
    .then(data => {
        console.log('Données reçues:', data); // Debug
        allData = data;
        displayData(data);
        setupFilters();
    })
    .catch(error => console.error('Erreur:', error));

// Fonction pour afficher les données
function displayData(data) {
    console.log("Ok")
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = ''; // Vide le tableau
    
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.Critere_ID}</td>
            <td>${critere.Critere_description}</td>
            <td>${critere.Critere_statut}</td>
            <td>${critere.Critere_points}</td>
        `;
    });
    
    // Affiche le nombre de résultats
    updateResultCount(data.length);
}

// Configure les filtres
function setupFilters() {
    const searchBar = document.getElementById('searchBar');
    const filterType = document.getElementById('filterType');
    
    if (!searchBar || !filterType) {
        console.error('Éléments de recherche non trouvés !');
        return;
    }
    
    // Événement sur la barre de recherche
    searchBar.addEventListener('input', filterData);
    
    // Événement sur la liste déroulante
    filterType.addEventListener('change', filterData);
    
    console.log('Filtres configurés avec succès');
}

// Fonction de filtrage
function filterData() {
    const searchBar = document.getElementById('searchBar');
    const filterType = document.getElementById('filterType');
    
    const searchTerm = searchBar.value.toLowerCase().trim();
    
    console.log('Recherche:', searchTerm, 'Type:', filterType.value); // Debug
    
    if (!searchTerm) {
        displayData(allData);
        return;
    }
    
    const filteredData = allData.filter(critere => {
        switch(filterType.value) {
            case 'id':
                return critere.Critere_ID.toString().includes(searchTerm);
            case 'description':
                return critere.Critere_description.toLowerCase().includes(searchTerm);
            case 'status':
                return critere.Critere_statut.toLowerCase().includes(searchTerm);
            case 'points':
                return critere.Critere_points.toString().includes(searchTerm);
            case 'all':
            default:
                return critere.Critere_ID.toString().includes(searchTerm) ||
                       critere.Critere_description.toLowerCase().includes(searchTerm) ||
                       critere.Critere_statut.toLowerCase().includes(searchTerm) ||
                       critere.Critere_points.toString().includes(searchTerm);
        }
    });
    
    console.log('Résultats filtrés:', filteredData.length); // Debug
    displayData(filteredData);
}

// Affiche le nombre de résultats
function updateResultCount(count) {
    let countElement = document.getElementById('result-count');
    if (!countElement) {
        countElement = document.createElement('div');
        countElement.id = 'result-count';
        countElement.style.marginTop = '10px';
        countElement.style.fontWeight = 'bold';
        const container = document.querySelector('.search-filter-container');
        if (container) {
            container.appendChild(countElement);
        }
    }
    countElement.textContent = `${count} résultat(s) trouvé(s)`;
}
