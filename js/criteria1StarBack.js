// Attend que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé !'); // Tu DOIS voir ce message
    
    // Récupère le numéro d'étoile depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const star = urlParams.get('star') || 1;
    
    let allData = []; // Stocke toutes les données
    
    // Fetch les données
    fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
        .then(response => {
            console.log('Réponse fetch:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            allData = data;
            displayData(data);
            setupFilters();
        })
        .catch(error => console.error('Erreur fetch:', error));
    
    // Fonction pour afficher les données
    function displayData(data) {
        const tbody = document.getElementById('table-body');
        
        if (!tbody) {
            console.error('ERREUR: table-body introuvable !');
            return;
        }
        
        tbody.innerHTML = '';
        
        data.forEach(critere => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${critere.Critere_ID}</td>
                <td>${critere.Critere_description}</td>
                <td>${critere.Critere_statut}</td>
                <td>${critere.Critere_points}</td>
            `;
        });
        
        updateResultCount(data.length);
    }
    
    // Configure les filtres
    function setupFilters() {
        const searchBar = document.getElementById('searchBar');
        const filterType = document.getElementById('filterType');
        
        console.log('searchBar:', searchBar);
        console.log('filterType:', filterType);
        
        if (!searchBar) {
            console.error('ERREUR: searchBar introuvable !');
            return;
        }
        
        if (!filterType) {
            console.error('ERREUR: filterType introuvable !');
            return;
        }
        
        // Événements
        searchBar.addEventListener('input', function() {
            console.log('Recherche en cours...');
            filterData();
        });
        
        filterType.addEventListener('change', function() {
            console.log('Filtre changé');
            filterData();
        });
        
        console.log('✓ Filtres configurés avec succès');
    }
    
    // Fonction de filtrage
    function filterData() {
        const searchBar = document.getElementById('searchBar');
        const filterType = document.getElementById('filterType');
        const searchTerm = searchBar.value.toLowerCase().trim();
        
        console.log('Recherche:', searchTerm, 'Type:', filterType.value);
        
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
        
        console.log('Résultats trouvés:', filteredData.length);
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
            countElement.style.color = '#666';
            const container = document.querySelector('.search-filter-container');
            if (container) {
                container.appendChild(countElement);
            }
        }
        countElement.textContent = `${count} résultat(s)`;
    }
});
