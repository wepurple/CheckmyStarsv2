// Attend que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    
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
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <span class="flex-grow-1">
                        ${critere.Critere_description}
                        </span>

                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Actions">
                            <div class="pr-1">
                                <button type="button" class="btn btn-outline-primary">Modifier</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger">Supprimer</button>
                            </div>
                        </div>
                    </div>
                </td>
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
            filterData();
        });
        
        filterType.addEventListener('change', function() {
            filterData();
        });
        
    }
    
    // Fonction de filtrage
    function filterData() {
        const searchBar = document.getElementById('searchBar');
        const filterType = document.getElementById('filterType');
        const searchTerm = searchBar.value.toLowerCase().trim();
        
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
                    // Convertir les points en string pour la comparaison
                    const points = critere.Critere_points !== null && critere.Critere_points !== undefined 
                        ? critere.Critere_points.toString() 
                        : '';
                    return points.includes(searchTerm);
                case 'all':
                default:
                    // Gérer les points comme string aussi ici
                    const pointsAll = critere.Critere_points !== null && critere.Critere_points !== undefined 
                        ? critere.Critere_points.toString() 
                        : '';
                    return critere.Critere_ID.toString().includes(searchTerm) ||
                        critere.Critere_description.toLowerCase().includes(searchTerm) ||
                        critere.Critere_statut.toLowerCase().includes(searchTerm) ||
                        pointsAll.includes(searchTerm);
            }
        });
        
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
            countElement.style.color = '#ff0000';
            const container = document.querySelector('.search-filter-container');
            if (container) {
                container.appendChild(countElement);
            }
        }
        countElement.textContent = `${count} résultat(s)`;
    }
});
