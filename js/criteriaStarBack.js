// Récupère le numéro d'étoile depuis l'URL
const urlParams = new URLSearchParams(window.location.search);
const star = urlParams.get('star') || 1;

let allCriteria = []; // Stocke toutes les données

// Fetch les données
fetch(`models/crud/getCriteriaByEtoile.php?star=${star}`)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        allCriteria = data;
        renderTable(data);
        setupSearch();
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('table-body').innerHTML = 
            '<tr><td colspan="4" class="text-danger">Erreur de chargement des données</td></tr>';
    });

// Fonction pour afficher le tableau
function renderTable(data) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '';
    
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-muted">Aucun critère trouvé</td></tr>';
        return;
    }
    
    data.forEach(critere => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${critere.Critere_ID}</td>
            <td>${critere.Critere_description}</td>
            <td>${critere.Critere_statut}</td>
            <td>${critere.Critere_points || '-'}</td>
        `;
    });
}

// Configuration de la recherche dynamique
function setupSearch() {
    const searchInput = document.getElementById('recherche');
    const searchType = document.getElementById('type');
    
    if (!searchInput || !searchType) {
        console.error('Éléments de recherche introuvables');
        return;
    }
    
    function filterTable() {
        const query = searchInput.value.trim().toLowerCase();
        const type = searchType.value;
        
        // Si vide, affiche tout
        if (!query) {
            renderTable(allCriteria);
            return;
        }
        
        // Filtre selon le type sélectionné
        const filtered = allCriteria.filter(critere => {
            switch(type) {
                case "1": // ID
                    return String(critere.Critere_ID).toLowerCase().includes(query);
                case "2": // Description
                    return String(critere.Critere_description).toLowerCase().includes(query);
                case "3": // Status
                    return String(critere.Critere_statut).toLowerCase().includes(query);
                case "4": // Points
                    return String(critere.Critere_points || '').toLowerCase().includes(query);
                default:
                    return false;
            }
        });
        
        renderTable(filtered);
    }
    
    // Écoute les événements
    searchInput.addEventListener('input', filterTable);
    searchType.addEventListener('change', filterTable);
}
