// Attend que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // Récupère le numéro d'étoile depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const star = urlParams.get('star') || 1;
    
    let allData = []; // Stocke toutes les données
    let editModal; // Instance Bootstrap Modal
    
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
            initModal();
        })
        .catch(error => console.error('Erreur fetch:', error));
    
    // Initialise la modal Bootstrap
    function initModal() {
        const modalElement = document.getElementById('editModal');
        if (modalElement) {
            editModal = new bootstrap.Modal(modalElement);
        }
    }
    
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
                            <div class="pe-3">
                                <button type="button" class="btn btn-outline-primary btn-edit" 
                                    data-id="${critere.Critere_ID}"
                                    data-description="${escapeHtml(critere.Critere_description)}"
                                    data-statut="${critere.Critere_statut}"
                                    data-points="${critere.Critere_points || ''}">
                                    Modifier
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger btn-delete"
                                    data-id="${critere.Critere_ID}">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
                <td>${critere.Critere_statut}</td>
                <td>${critere.Critere_points}</td>
            `;
        });
        
        // Ajoute les event listeners sur les boutons
        setupButtonEvents();
        updateResultCount(data.length);
    }
    
    // Configure les événements sur les boutons Modifier/Supprimer
    function setupButtonEvents() {
        // Boutons Modifier
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const description = this.dataset.description;
                const statut = this.dataset.statut;
                const points = this.dataset.points;
                
                openEditModal(id, description, statut, points);
            });
        });
        
        // Boutons Supprimer
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                if (confirm(`Êtes-vous sûr de vouloir supprimer le critère #${id} ?`)) {
                    deleteCriteria(id);
                }
            });
        });
    }
    
    // Ouvre la modal avec les données pré-remplies
    function openEditModal(id, description, statut, points) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-statut').value = statut;
        document.getElementById('edit-points').value = points;
        
        // Change le titre de la modal
        document.getElementById('editModalLabel').innerHTML = 
            `<i class="fas fa-edit"></i> Modifier le critère #${id}`;
        
        // Ouvre la modal
        if (editModal) {
            editModal.show();
        }
    }
    
    // Gère la sauvegarde
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'saveBtn') {
            saveCriteria();
        }
    });
    
    // Sauvegarde les modifications
    function saveCriteria() {
        const id = document.getElementById('edit-id').value;
        const description = document.getElementById('edit-description').value;
        const statut = document.getElementById('edit-statut').value;
        const points = document.getElementById('edit-points').value;
        
        // Validation
        if (!description.trim()) {
            alert('La description ne peut pas être vide !');
            return;
        }
        
        // Prépare les données
        const formData = new FormData();
        formData.append('id', id);
        formData.append('description', description);
        formData.append('statut', statut);
        formData.append('points', points);
        
        // Envoie au serveur (à adapter selon ton API)
        fetch('models/crud/updateCriteria.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Critère modifié avec succès !');
                editModal.hide();
                // Recharge les données
                location.reload();
            } else {
                alert('Erreur lors de la modification : ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la modification');
        });
    }
    
    // Fonction de suppression
    function deleteCriteria(id) {
        fetch(`models/crud/deleteCriteria.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Critère supprimé avec succès !');
                location.reload();
            } else {
                alert('Erreur lors de la suppression : ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
    
    // Échappe les caractères HTML pour éviter les injections
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
                    const points = critere.Critere_points !== null && critere.Critere_points !== undefined 
                        ? critere.Critere_points.toString() 
                        : '';
                    return points.includes(searchTerm);
                case 'all':
                default:
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
