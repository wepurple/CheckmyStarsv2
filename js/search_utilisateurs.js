let seeModal = null;
let editModal = null;
let deleteUserId = null;
let confirmModal = null;

async function getAllusers() {
    const url = "models/Read/users.php";
    const response = await fetch(url, {
        method: "GET",
        headers: {
            'Content-Type': "application/json"
        }
    });
    const result = await response.json();
    return result;
}

async function getUserById(id) {
    const url = "models/Read/users.php?IdPersonne=" + id;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            'Content-Type': "application/json"
        }
    });
    const result = await response.json();
    console.log("Données reçues:", result);
    return result;
}

async function updateUserById() {
    try {
        const id = document.getElementById('editIdUser').value;
        const nom = document.getElementById('editLeNom').value;
        const prenom = document.getElementById('editLePrenom').value;
        const email = document.getElementById('editLeMail').value;
        const civiliteValue = document.getElementById('editLeGenre').value;
        
        let civilite;
        switch(civiliteValue) {
            case "1":
                civilite = "Monsieur";
                break;
            case "2":
                civilite = "Madame";
                break;
            case "3":
                civilite = "Iel";
                break;
            default:
                civilite = "Iel";
        }

        if (!civilite || civilite.trim() === '') {
            civilite = "Iel";
        }
        
        const societe_id = document.getElementById('editLaSociete').value;
        const role_id = document.getElementById('editLeRole').value;
        const telephone = document.getElementById('editLeTel').value;
        const num_rue = document.getElementById('editLeNumRue').value;
        const nom_rue = document.getElementById('editLaAdresse').value;
        const complement = document.getElementById('editLeComplement').value;
        const code_postal = document.getElementById('editLeCode').value;
        const ville = document.getElementById('editLaVille').value;
        const pays = document.getElementById('editLePays').value;

        const data = {
            id, nom, prenom, email, civilite, societe_id, role_id, telephone, 
            num_rue, nom_rue, complement, code_postal, ville, pays
        };

        const response = await fetch("models/Update/users.php", {
            method: "POST",
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        const result = await response.json();
        console.log("Réponse:", result);

        if (result.success) {
            if (editModal) {
                editModal.hide();
            }
            await loadTable();
            alert("Utilisateur modifié avec succès!");
        } else {
            alert("Erreur: " + result.error);
        }
    } catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite : " + error.message);
    }
}

async function showUserUpdateModal(id) {
    try {
        if (!editModal) {
            const editModalElement = document.getElementById('editModal');
            if (editModalElement) {
                editModal = new bootstrap.Modal(editModalElement);
            } else {
                console.error("editModal element not found!");
                return;
            }
        }

        var user = await getUserById(id);

        document.getElementById('editIdUser').value = user.Utilisateur_ID;
        document.getElementById('editLeNom').value = user.Utilisateur_Nom;
        document.getElementById('editLePrenom').value = user.Utilisateur_Prenom;
        document.getElementById('editLeMail').value = user.Utilisateur_Mail;

        switch(user["Utilisateur_Civilite"]) {
            case "Monsieur":
                document.getElementById('editLeGenre').value = "1";
                break;
            case "Madame":
                document.getElementById('editLeGenre').value = "2";
                break;
            case "Iel":
                document.getElementById('editLeGenre').value = "3";
                break;
            default:
                document.getElementById('editLeGenre').value = "3";
        }

        if (user.Societe_ID) {
            document.getElementById('editLaSociete').value = user.Societe_ID;
        }

        var roleUser;

        if (user && user.admin == 1) {
            roleUser = "3";
        } else if (user && user.inspecteur == 1) {
            roleUser = "2";
        } else if (user && user.donneurordre == 1) {
            roleUser = "1";
        } else if (user && user.proprietaire == 1) {
            roleUser = "0";
        }

        document.getElementById('editLeRole').value = roleUser;
        
        document.getElementById('editLeTel').value = user.Utilisateur_Telephone || '';
        document.getElementById('editLeNumRue').value = user.AdressePostale_NumeroRue || '';
        document.getElementById('editLaAdresse').value = user.AdressePostale_NomRue || '';
        document.getElementById('editLeComplement').value = user.AdressePostale_Complement || '';
        document.getElementById('editLeCode').value = user.AdressePostale_CodePostal || '';
        document.getElementById('editLaVille').value = user.AdressePostale_Ville || '';
        document.getElementById('editLePays').value = user.AdressePostale_Pays || '';

        editModal.show();

    } catch (error) {
        console.error("Erreur:", error);
        alert("Impossible d'ouvrir le modal: " + error.message);
    }
}

async function showUserInfoModal(id) {
    try {
        if (!seeModal) {
            const seeModalElement = document.getElementById('seeModal');
            if (seeModalElement) {
                seeModal = new bootstrap.Modal(seeModalElement);
            } else {
                console.error("seeModal element not found!");
                return;
            }
        }

        var user = await getUserById(id);

        var roleUser;

        if (user && user.admin == 1) {
            roleUser = "Administrateur";
        } else if (user && user.inspecteur == 1) {
            roleUser = "Inspecteur";
        } else if (user && user.donneurordre == 1) {
            roleUser = "Donneur d'ordre";
        } else if (user && user.proprietaire == 1) {
            roleUser = "Proprietaire";
        }

        const fields = {
            'seeLeNom': user.Utilisateur_Nom || "",
            'seeLePrenom': user.Utilisateur_Prenom || "",
            'seeLeMail': user.Utilisateur_Mail || "",
            'seeGenre': user.Utilisateur_Civilite || "",
            'seeLaSociete': user.Societe_Nom || "",
            'seeRole': roleUser,
            'seeLeTel': user.Utilisateur_Telephone || "",
            'seeLeNumRue': user.AdressePostale_NumeroRue || "",
            'seeLaAdresse': user.AdressePostale_NomRue || "",
            'seeLeComplement': user.AdressePostale_Complement || "",
            'seeLeCode': user.AdressePostale_CodePostal || "",
            'seeLaVille': user.AdressePostale_Ville || "",
            'seeLePays': user.AdressePostale_Pays || ""
        };

        for (const [fieldId, value] of Object.entries(fields)) {
            const element = document.getElementById(fieldId);
            if (element) {
                element.value = value;
            } else {
                console.warn(`Element with id '${fieldId}' not found in the DOM`);
            }
        }

        seeModal.show();
    } catch (error) {
        console.error("Erreur:", error);
        alert("Impossible d'ouvrir le modal: " + error.message);
    }
}

function openDeleteModal(id, nom, prenom) {
    deleteUserId = id;

    const p = document.getElementById('supprText');
    if (p) p.textContent = `Voulez-vous vraiment supprimer l'utilisateur ${nom} ${prenom} (ID ${id}) ?`;

    if (!confirmModal) {
        const el = document.getElementById('confirmModal');
        confirmModal = new bootstrap.Modal(el);
    }
    confirmModal.show();
}

async function deleteUserById(id) {
    const resp = await fetch('models/Delete/users.php', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });

    if (!resp.ok) {
        throw new Error(`Erreur HTTP ${resp.status}`);
    }

    const result = await resp.json();
    
    if (!result.success) {
        throw new Error(result.error || 'Suppression impossible');
    }
    
    return result;
}

// Fonction de recherche avec filtres
function searchTable() {
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const filterValue = searchInput.value.toLowerCase().trim();
    const selectedFilter = filterType ? filterType.value : 'all';
    const tableBody = document.getElementById('table-body');
    const rows = tableBody.getElementsByTagName('tr');
    
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        
        // Vérifier que ce n'est pas la ligne de chargement
        if (cells.length >= 6) {
            // Ordre: ID (0), Nom (1), Prénom (2), Rôle (3), Société (4), Actions (5)
            const id = cells[0]?.textContent.toLowerCase() || '';
            const nom = cells[1]?.textContent.toLowerCase() || '';
            const prenom = cells[2]?.textContent.toLowerCase() || '';
            const role = cells[3]?.textContent.toLowerCase() || '';
            const societe = cells[4]?.textContent.toLowerCase() || '';
            
            let match = false;
            
            // Filtre selon le type sélectionné
            switch(selectedFilter) {
                case 'all':
                    match = id.includes(filterValue) ||
                           nom.includes(filterValue) ||
                           prenom.includes(filterValue) ||
                           role.includes(filterValue) ||
                           societe.includes(filterValue);
                    break;
                case 'id':
                    match = id.includes(filterValue);
                    break;
                case 'nom':
                    match = nom.includes(filterValue);
                    break;
                case 'prenom':
                    match = prenom.includes(filterValue);
                    break;
                case 'email':
                    match = nom.includes(filterValue) || prenom.includes(filterValue);
                    break;
                case 'role':
                    match = role.includes(filterValue);
                    break;
                case 'societe':
                    match = societe.includes(filterValue);
                    break;
            }
            
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        }
    }
    
    updateResultInfo(visibleCount);
}

// Met à jour les informations de résultat
function updateResultInfo(visibleCount) {
    const resultInfo = document.getElementById('resultInfo');
    const searchInput = document.getElementById('searchInput');
    
    if (resultInfo) {
        if (searchInput.value.trim() !== '') {
            resultInfo.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''} trouvé${visibleCount > 1 ? 's' : ''}`;
        } else {
            resultInfo.textContent = 'Affichage de tous les utilisateurs';
        }
    }
}

// Efface la recherche
function clearSearch() {
    document.getElementById('searchInput').value = '';
    const filterType = document.getElementById('filterType');
    if (filterType) {
        filterType.value = 'all';
    }
    searchTable();
}

// Charge le tableau
async function loadTable() {
    try {
        const users = await getAllusers();
        const tab = document.getElementById("table-body");
        const userCount = document.getElementById("userCount");
        
        if (!tab) return;
        
        tab.innerHTML = "";
        
        if (!users || users.length === 0) {
            tab.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun utilisateur trouvé</p>
                    </td>
                </tr>
            `;
            if (userCount) userCount.textContent = "0 utilisateur";
            return;
        }
        
        if (userCount) {
            userCount.textContent = `${users.length} utilisateur${users.length > 1 ? 's' : ''}`;
        }

        for (const user of users) {
            const userId = user.Utilisateur_ID ?? user.UtilisateurID ?? user.IdPersonne;
            const nom = user.Utilisateur_Nom ?? user.UtilisateurNom ?? '';
            const prenom = user.Utilisateur_Prenom ?? user.UtilisateurPrenom ?? '';
            const societe = user.Societe_Nom ?? user.SocieteNom ?? 'N/A';

            let roleUser = "Donneur d'ordre";
            let roleClass = "secondary";
            
            if (user.admin == 1) {
                roleUser = "Administrateur";
                roleClass = "danger";
            } else if (user.inspecteur == 1) {
                roleUser = "Inspecteur";
                roleClass = "warning";
            } else if (user.donneurordre == 1) {
                roleUser = "Donneur d'ordre";
                roleClass = "info";
            } else if (user.proprietaire == 1) {
                roleUser = "Propriétaire";
                roleClass = "success";
            }

            const tr = document.createElement("tr");
            // Ordre: ID, Nom, Prénom, Rôle, Société, Actions
            tr.innerHTML = `
                <td class="text-center align-middle"><strong>${userId}</strong></td>
                <td class="align-middle">${nom}</td>
                <td class="align-middle">${prenom}</td>
                <td class="align-middle">
                    <span class="badge bg-${roleClass}">${roleUser}</span>
                </td>
                <td class="align-middle">${societe}</td>
                <td class="text-center align-middle table-actions">
                    <button class="btn btn-sm btn-info me-1" title="Voir les détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning me-1" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            const btns = tr.querySelectorAll('button');
            btns[0].addEventListener('click', () => showUserInfoModal(userId));
            btns[1].addEventListener('click', () => showUserUpdateModal(userId));
            btns[2].addEventListener('click', () => openDeleteModal(userId, nom, prenom));

            tab.appendChild(tr);
        }
        
        updateResultInfo(users.length);

    } catch (error) {
        console.error("Erreur lors du chargement du tableau:", error);
        const tab = document.getElementById("table-body");
        if (tab) {
            tab.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <p>Erreur lors du chargement des données</p>
                        <small>${error.message}</small>
                    </td>
                </tr>
            `;
        }
    }
}

function addCancel() {
    document.getElementById('addForm').reset();
    const addModalElement = document.getElementById('addModal');
    const addModal = bootstrap.Modal.getInstance(addModalElement);
    if (addModal) {
        addModal.hide();
    }
}

async function addUser() {
    try {
        const nom = document.getElementById('leNom').value.trim();
        const prenom = document.getElementById('lePrenom').value.trim();
        const civiliteValue = document.getElementById('leGenre').value;
        const email = document.getElementById('leMail').value.trim();
        const societe_id = document.getElementById('laSociete').value;
        const role_id = document.getElementById('leRole').value;
        const telephone = document.getElementById('leTel').value.trim();
        const num_rue = document.getElementById('leNumRue').value.trim();
        const nom_rue = document.getElementById('laAdresse').value.trim();
        const complement = document.getElementById('leComplement').value.trim();
        const code_postal = document.getElementById('leCode').value.trim();
        const ville = document.getElementById('laVille').value.trim();
        const pays = document.getElementById('lePays').value.trim();
        const password = document.getElementById('leMdp').value;

        if (!nom || !prenom || !email || !societe_id || !role_id || !telephone || 
            !num_rue || !nom_rue || !code_postal || !ville || !pays || !password) {
            alert("Veuillez remplir tous les champs obligatoires (*)");
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("Veuillez entrer un email valide");
            return;
        }

        if (password.length < 8) {
            alert("Le mot de passe doit contenir au moins 8 caractères");
            return;
        }

        let civilite;
        switch(civiliteValue) {
            case "1":
                civilite = "Monsieur";
                break;
            case "2":
                civilite = "Madame";
                break;
            case "3":
                civilite = "Iel";
                break;
            default:
                civilite = "Iel";
        }

        const data = {
            nom,
            prenom,
            civilite,
            email,
            password,
            societe_id: societe_id === "" ? null : societe_id,
            role_id: parseInt(role_id),
            telephone,
            num_rue,
            nom_rue,
            complement,
            code_postal,
            ville,
            pays
        };

        console.log("Données envoyées:", data);

        const response = await fetch("models/Create/users.php", {
            method: "POST",
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });

        const result = await response.json();
        console.log("Réponse:", result);

        if (result.success) {
            const addModalElement = document.getElementById('addModal');
            const addModal = bootstrap.Modal.getInstance(addModalElement);
            if (addModal) {
                addModal.hide();
            }
            
            document.getElementById('addForm').reset();
            
            await loadTable();
            
            alert("Utilisateur créé avec succès ! ID: " + (result.new_user_id || 'N/A'));
        } else {
            alert("Erreur lors de la création : " + result.error);
        }
    } catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur s'est produite : " + error.message);
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    // Charger le tableau
    loadTable();
    
    // Gestion du bouton de suppression
    const supprConfirm = document.getElementById('supprConfirm');
    if (supprConfirm) {
        supprConfirm.addEventListener('click', async () => {
            try {
                if (!deleteUserId) return;
                await deleteUserById(deleteUserId);
                if (confirmModal) confirmModal.hide();
                deleteUserId = null;
                await loadTable();
                alert('Utilisateur supprimé.');
            } catch (e) {
                alert(e.message);
            }
        });
    }
    
    // Gestion de la recherche
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    
    if (searchInput) {
        searchInput.addEventListener('input', searchTable);
        searchInput.addEventListener('paste', () => {
            setTimeout(searchTable, 10);
        });
    }
    
    if (filterType) {
        filterType.addEventListener('change', searchTable);
    }
});
