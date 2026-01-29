document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    let allData = [];

    let i = 0;

    liste.addEventListener('change', function() {
        
        let etoiles = this.value 
        
        fetch(`models/crud/getCriteriaByEtoile.php?star=${etoiles}`)
            .then(response => {
                console.log('Reponse fetch; ', response);
                return response.json();
            })
            .then(data => {
                console.log("Données recues: ", data);
                allData = data;
                displayData(data);
                setupFilters();
            })
            .catch(error => console.log("Erreur fetch : ", error));

        function displayData(data) {
            const tbody = document.getElementById('table-body');

            if(!tbody) {
                console.error("Erreur: table-body introuvable");
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
                        </div>
                    </td>
                    <td>${critere.Critere_statut}</td>
                    <td>${critere.Critere_points}</td>
                    <td>
                        <label for="case-${i}">Choisir un nombre : </label>
                        <input type="number" id="case-${i}" name="nombre" min="0" max=${critere.Critere_points}>
                    </td>
                `;
                i = i + 1;
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        console.log(etoiles);

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

    return etoiles;

    });

    

});

let Nb_case_max = 10;

let total = 0;

function pointsTotal() {
        for (let i =0; i < Nb_case_max; i++) {
            const elementTest = document.getElementById(`case-${i}`).value || 0;
            console.log('Element ', i, ': ', elementTest);

            total += Number(elementTest);
            console.log('Total intermédiaire:', total);
        }
        console.log('Total:', Number(total));
    }

    