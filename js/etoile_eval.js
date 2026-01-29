document.addEventListener('DOMContentLoaded', function() {
    let liste = document.getElementById('selectEtoiles');

    let allData = [];

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
                displayDara(data);
                setupFilters(data);
            })
            .catch(error => console.log("Erreur fetch : ", error));

        function displayData(data) {
            const tbody = document.getElementById('table-body');

            if(!tbody) {
                console.error("Erreur: table-body introuvable");
                return;
            }

            tbody.innerHTML = "";

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
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal"
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
                `;
            });
        }

        console.log(etoiles);

    return etoiles;

    });

});