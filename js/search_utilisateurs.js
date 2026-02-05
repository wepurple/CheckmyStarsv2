let seeModal = null;
let editModal = null;
let deleteUserId = null;
let confirmModal = null;
let societeModal = null;

const REGEX = {
  nom: /^[A-Za-zÀ-ÖØ-öø-ÿ'’ -]{2,50}$/,
  prenom: /^[A-Za-zÀ-ÖØ-öø-ÿ'’ -]{2,50}$/,

  email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  telFR: /^(?:(?:\+33)\s?|0)[1-9](?:[\s.-]?\d{2}){4}$/,

  password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,64}$/,

  numRue: /^(?:\d{1,5})(?:\s?(?:bis|ter|quater|[A-Za-z]))?$/i,
  nomRue: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{2,100}$/,
  complement: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’().,\-\/\s]{0,100}$/,
  codePostal: /^\d{5}$/,
  ville: /^[0-9A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,80}$/,
  pays: /^[A-Za-zÀ-ÖØ-öø-ÿ'’\-\/\s]{2,60}$/,

  civiliteValue: /^[1-3]$/,
  roleId: /^[0-3]$/,
  societeId: /^\d+$/
};

async function getAllusers() {
    const url = "../models/Read/users.php";
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
    const url = "../models/Read/users.php?IdPersonne=" + id;
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
    const v = {
      id: document.getElementById('editIdUser').value,
      nom: document.getElementById('editLeNom').value.trim(),
      prenom: document.getElementById('editLePrenom').value.trim(),
      email: document.getElementById('editLeMail').value.trim(),
      civiliteValue: document.getElementById('editLeGenre').value,
      societe_id: document.getElementById('editLaSociete').value,
      role_id: document.getElementById('editLeRole').value,
      telephone: document.getElementById('editLeTel').value.trim(),
      num_rue: document.getElementById('editLeNumRue').value.trim(),
      nom_rue: document.getElementById('editLaAdresse').value.trim(),
      complement: document.getElementById('editLeComplement').value.trim(),
      code_postal: document.getElementById('editLeCode').value.trim(),
      ville: document.getElementById('editLaVille').value.trim(),
      pays: document.getElementById('editLePays').value.trim(),
    };

    if (!checkRequired('editLeNom', v.nom, "Nom obligatoire")) return;
    if (!checkRequired('editLePrenom', v.prenom, "Prénom obligatoire")) return;
    if (!checkRequired('editLeMail', v.email, "Email obligatoire")) return;
    if (!checkRequired('editLaSociete', v.societe_id, "Société obligatoire")) return;
    if (!checkRequired('editLeRole', v.role_id, "Rôle obligatoire")) return;
    if (!checkRequired('editLeTel', v.telephone, "Téléphone obligatoire")) return;

    if (!checkRequired('editLeNumRue', v.num_rue, "Numéro de rue obligatoire")) return;
    if (!checkRequired('editLaAdresse', v.nom_rue, "Nom de rue obligatoire")) return;
    if (!checkRequired('editLeCode', v.code_postal, "Code postal obligatoire")) return;
    if (!checkRequired('editLaVille', v.ville, "Ville obligatoire")) return;
    if (!checkRequired('editLePays', v.pays, "Pays obligatoire")) return;

    if (!checkRegex('editLeNom', v.nom, REGEX.nom, "Nom invalide")) return;
    if (!checkRegex('editLePrenom', v.prenom, REGEX.prenom, "Prénom invalide")) return;
    if (!checkRegex('editLeMail', v.email, REGEX.email, "Email invalide")) return;
    const emailExists = await checkEmailExists(v.email, v.id);
    if (emailExists) 
    {
      markField('editLeMail', false);
      showToast("Cet email est déjà utilisé par un autre utilisateur", "error");
      document.getElementById('editLeMail').focus();
      return;
    }
    if (!checkRegex('editLeTel', v.telephone, REGEX.telFR, "Téléphone invalide")) return;

    if (!checkRegex('editLeGenre', v.civiliteValue, REGEX.civiliteValue, "Civilité invalide")) return;
    if (!checkRegex('editLeRole', String(v.role_id), REGEX.roleId, "Rôle invalide")) return;
    if (!checkRegex('editLaSociete', String(v.societe_id), REGEX.societeId, "Société invalide")) return;

    if (!checkRegex('editLeNumRue', v.num_rue, REGEX.numRue, "Numéro de rue invalide")) return;
    if (!checkRegex('editLaAdresse', v.nom_rue, REGEX.nomRue, "Adresse invalide")) return;
    if (v.complement !== "" && !checkRegex('editLeComplement', v.complement, REGEX.complement, "Complément invalide")) return;
    if (!checkRegex('editLeCode', v.code_postal, REGEX.codePostal, "Code postal invalide (5 chiffres)")) return;
    if (!checkRegex('editLaVille', v.ville, REGEX.ville, "Ville invalide")) return;
    if (!checkRegex('editLePays', v.pays, REGEX.pays, "Pays invalide")) return;

    const civilite = v.civiliteValue === "1" ? "Monsieur" : v.civiliteValue === "2" ? "Madame" : "Iel";

    const data = {
      id: v.id,
      nom: v.nom,
      prenom: v.prenom,
      email: v.email,
      civilite,
      societe_id: v.societe_id,
      role_id: v.role_id,
      telephone: v.telephone,
      num_rue: v.num_rue,
      nom_rue: v.nom_rue,
      complement: v.complement,
      code_postal: v.code_postal,
      ville: v.ville,
      pays: v.pays
    };

    const response = await fetch("../models/Update/users.php", {
      method: "POST",
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });

    const result = await response.json();
    if (result.success) {
      if (editModal) editModal.hide();
      clearValidationClasses('editForm');
      await loadTable();
      showToast("Utilisateur modifié avec succès!", "success");
    } else {
      showToast("Erreur: " + result.error, "error");
    }
  } catch (error) {
    console.error("Erreur:", error);
    showToast("Une erreur s'est produite : " + error.message, "error");
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

        clearValidationClasses('editForm');
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
       
        const adresseComplete = [
        user.AdressePostale_NumeroRue,
        user.AdressePostale_NomRue,
        user.AdressePostale_CodePostal,
        user.AdressePostale_Ville
        ].filter(Boolean).join(' ');

        document.getElementById('editLaAdresseComplete').value = adresseComplete;

        document.getElementById('editLeNumRue').value = user.AdressePostale_NumeroRue || '';
        document.getElementById('editLaAdresse').value = user.AdressePostale_NomRue || '';
        document.getElementById('editLeCode').value = user.AdressePostale_CodePostal || '';
        document.getElementById('editLaVille').value = user.AdressePostale_Ville || '';
        document.getElementById('editLePays').value = user.AdressePostale_Pays || '';

        editModal.show();

    } catch (error) {
        console.error("Erreur:", error);
        showToast("Impossible d'ouvrir le modal : " + error.message, "error");
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
        showToast("Impossible d'ouvrir le modal : " + error.message, "error");
    }
}

function openDeleteModal(id, nom, prenom) {
    deleteUserId = id;

    const p = document.getElementById('supprText');
    if (p) p.textContent = `Voulez-vous vraiment supprimer l'utilisateur ${nom} ${prenom} (ID : ${id}) ?`;

    if (!confirmModal) {
        const el = document.getElementById('confirmModal');
        confirmModal = new bootstrap.Modal(el);
    }
    confirmModal.show();
}

async function deleteUserById(id) {   
    try {
        const payload = { id: parseInt(id) };
        console.log("Payload envoyé:", JSON.stringify(payload));
        
        const resp = await fetch('../models/Delete/users.php', {
            method: 'POST',
            credentials: 'include',
            headers: { 
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        console.log("Status HTTP:", resp.status);
        
        const responseText = await resp.text();
        console.log("Réponse brute:", responseText);

        if (!resp.ok) {
            throw new Error(`Erreur HTTP ${resp.status}: ${responseText}`);
        }

        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            throw new Error("Réponse JSON invalide: " + responseText);
        }
        
        console.log("Résultat parsé:", result);
        
        if (!result.success) {
            throw new Error(result.error || 'Suppression impossible');
        }
        
        return result;
        
    } catch (error) {
        console.error("=== ERREUR ===");
        console.error("Message:", error.message);
        console.error("Stack:", error.stack);
        throw error;
    }
}

function searchTable() {
    const searchInput = document.getElementById('searchInput');
    const filterValue = searchInput.value.toLowerCase().trim();
    const tableBody = document.getElementById('table-body');
    const rows = tableBody.getElementsByTagName('tr');
    let visibleCount = 0;

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        if (cells.length >= 6) {
            // Recherche unifiée sur ID, nom, prénom, rôle et société
            const textContent = Array.from(cells)
                .slice(0, 5)  // ID, nom, prénom, rôle, société
                .map(cell => cell.textContent.toLowerCase())
                .join(' ');
            
            const match = textContent.includes(filterValue);
            row.style.display = match ? '' : 'none';
            if (match) visibleCount++;
        }
    }
    updateResultInfo(visibleCount);
}

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

function clearSearch() {
    document.getElementById('searchInput').value = '';
    const filterType = document.getElementById('filterType');
    if (filterType) {
        filterType.value = 'all';
    }
    searchTable();
}

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
    clearValidationClasses('addForm');
    document.getElementById('addForm').reset();
    const addModalElement = document.getElementById('addModal');
    const addModal = bootstrap.Modal.getInstance(addModalElement);
    if (addModal) {
        addModal.hide();
    }
}

async function addUser() {
  try {
    const v = {
      nom: document.getElementById('leNom').value.trim(),
      prenom: document.getElementById('lePrenom').value.trim(),
      civiliteValue: document.getElementById('leGenre').value,
      email: document.getElementById('leMail').value.trim(),
      societe_id: document.getElementById('laSociete').value,
      role_id: document.getElementById('leRole').value,
      telephone: document.getElementById('leTel').value.trim(),
      num_rue: document.getElementById('leNumRue').value.trim(),
      nom_rue: document.getElementById('laAdresse').value.trim(),
      complement: document.getElementById('leComplement').value.trim(),
      code_postal: document.getElementById('leCode').value.trim(),
      ville: document.getElementById('laVille').value.trim(),
      pays: document.getElementById('lePays').value.trim(),
      password: document.getElementById('leMdp').value
    };

    if (!checkRequired('leNom', v.nom, "Nom obligatoire")) return;
    if (!checkRequired('lePrenom', v.prenom, "Prénom obligatoire")) return;
    if (!checkRequired('leMail', v.email, "Email obligatoire")) return;
    if (!checkRequired('laSociete', v.societe_id, "Société obligatoire")) return;
    if (!checkRequired('leRole', v.role_id, "Rôle obligatoire")) return;
    if (!checkRequired('leTel', v.telephone, "Téléphone obligatoire")) return;
    if (!checkRequired('leMdp', v.password, "Mot de passe obligatoire")) return;

    if (!checkRegex('leNom', v.nom, REGEX.nom, "Nom invalide")) return;
    if (!checkRegex('lePrenom', v.prenom, REGEX.prenom, "Prénom invalide")) return;
    if (!checkRegex('leMail', v.email, REGEX.email, "Email invalide")) return;
    const emailExists = await checkEmailExists(v.email);
    if (emailExists) {
      markField('leMail', false);
      showToast("Cet email est déjà utilisé", "error");
      document.getElementById('leMail').focus();
      return;
    }
    if (!checkRegex('leTel', v.telephone, REGEX.telFR, "Téléphone invalide (ex: 06 12 34 56 78 ou +33 6 12 34 56 78)")) return;

    if (!checkRegex('leGenre', v.civiliteValue, REGEX.civiliteValue, "Civilité invalide")) return;
    if (!checkRegex('leRole', String(v.role_id), REGEX.roleId, "Rôle invalide")) return;
    if (!checkRegex('laSociete', String(v.societe_id), REGEX.societeId, "Société invalide")) return;

    if (!checkRegex('leMdp', v.password, REGEX.password, "Mot de passe trop faible (min 8, maj/min/chiffre/spécial)")) return;

    if (!checkRequired('laAdresseComplete', v.num_rue, "Numéro de rue obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.nom_rue, "Nom de rue obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.code_postal, "Code postal obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.ville, "Ville obligatoire")) return;
    if (!checkRequired('laAdresseComplete', v.pays, "Pays obligatoire")) return;

    if (!checkRegex('laAdresseComplete', v.num_rue, REGEX.numRue, "Numéro de rue invalide (ex: 12, 12 bis, 12B)")) return;
    if (!checkRegex('laAdresseComplete', v.nom_rue, REGEX.nomRue, "Adresse invalide")) return;
    if (v.complement !== "" && !checkRegex('leComplement', v.complement, REGEX.complement, "Complément invalide")) return;
    if (!checkRegex('laAdresseComplete', v.code_postal, REGEX.codePostal, "Code postal invalide (5 chiffres)")) return;
    if (!checkRegex('laAdresseComplete', v.ville, REGEX.ville, "Ville invalide")) return;
    if (!checkRegex('laAdresseComplete', v.pays, REGEX.pays, "Pays invalide")) return;

    const civilite = v.civiliteValue === "1" ? "Monsieur" : v.civiliteValue === "2" ? "Madame" : "Iel";

    const data = {
      nom: v.nom,
      prenom: v.prenom,
      civilite,
      email: v.email,
      password: v.password,
      societe_id: v.societe_id === "" ? null : v.societe_id,
      role_id: parseInt(v.role_id, 10),
      telephone: v.telephone,
      num_rue: v.num_rue,
      nom_rue: v.nom_rue,
      complement: v.complement,
      code_postal: v.code_postal,
      ville: v.ville,
      pays: v.pays
    };

    const response = await fetch("../models/Create/users.php", {
      method: "POST",
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });

    const result = await response.json();
    if (result.success) {
      const addModalElement = document.getElementById('addModal');
      const addModal = bootstrap.Modal.getInstance(addModalElement);
      if (addModal) addModal.hide();
      clearValidationClasses('addForm');
      document.getElementById('addForm').reset();
      await loadTable();
      showToast("Utilisateur créé avec succès !", "success");
    } else {
      showToast("Erreur lors de la création : " + result.error, "error");
    }
  } catch (error) {
    console.error("Erreur:", error);
    showToast("Une erreur s'est produite : " + error.message, "error");
  }
}

function showToast(message, type = 'success') {
  const typeConfig = {
    success: { bg: 'bg-success', icon: '<i class="fa-solid fa-check"></i>', title: 'Succès' },
    error: { bg: 'bg-danger', icon: '<i class="fa-solid fa-bug"></i>', title: 'Erreur' },
    warning: { bg: 'bg-warning', icon: '<i class="fa-solid fa-triangle-exclamation"></i>', title: 'Attention' },
    info: { bg: 'bg-info', icon: '<i class="fa-solid fa-info"></i>', title: 'Information' }
  };
  
  const config = typeConfig[type] || typeConfig['info'];
  
  // Créer l'élément toast
  const toastHTML = `
    <div class="toast align-items-center text-white ${config.bg} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <strong>${config.icon}</strong> ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  
  // Ajouter le toast au conteneur
  const container = document.querySelector('.toast-container');
  container.insertAdjacentHTML('beforeend', toastHTML);
  
  // Initialiser et afficher le toast
  const toastElement = container.lastElementChild;
  const toast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: type === 'error' ? 5000 : 3000
  });
  
  toast.show();
  
  // Supprimer le toast du DOM après fermeture
  toastElement.addEventListener('hidden.bs.toast', () => {
    toastElement.remove();
  });
}

function setupAdresseAutocomplete({ adresseCompleteId, numRueId, adresseId, codeId, villeId, paysId }) {
  const adresseCompleteInput = document.getElementById(adresseCompleteId);
  const numRueInput = document.getElementById(numRueId);
  const adresseInput = document.getElementById(adresseId);
  const codeInput = document.getElementById(codeId);
  const villeInput = document.getElementById(villeId);
  const paysInput = document.getElementById(paysId);

  if (!adresseCompleteInput) return;

  let lastFeatures = [];
  let abortController = null;

  // Créer la div de suggestions
  const suggestionsDiv = document.createElement('div');
  suggestionsDiv.className = 'autocomplete-suggestions';
  suggestionsDiv.style.cssText = `
    position: absolute;
    z-index: 9999;
    background: #2b3035;
    border: 1px solid #495057;
    border-radius: 4px;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
  `;
  adresseCompleteInput.parentElement.style.position = 'relative';
  adresseCompleteInput.parentElement.appendChild(suggestionsDiv);

  adresseCompleteInput.addEventListener("input", async () => {
    const q = adresseCompleteInput.value.trim();
    if (q.length < 3) {
      suggestionsDiv.style.display = 'none';
      suggestionsDiv.innerHTML = '';
      lastFeatures = [];
      return;
    }

    if (abortController) abortController.abort();
    abortController = new AbortController();

    const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(q)}&limit=8`;

    try {
      const resp = await fetch(url, { signal: abortController.signal });
      if (!resp.ok) return;

      const data = await resp.json();
      lastFeatures = data.features || [];

      if (lastFeatures.length === 0) {
        suggestionsDiv.style.display = 'none';
        return;
      }

      suggestionsDiv.innerHTML = lastFeatures
        .map((f, idx) => {
          const p = f.properties || {};
          return `
            <div class="suggestion-item" data-idx="${idx}" style="
              padding: 12px 14px;
              cursor: pointer;
              border-bottom: 1px solid #3a3f44;
              transition: background 0.15s;
            ">
              <div style="display: flex; align-items: start; gap: 10px;">
                <i class="fas fa-map-marker-alt" style="color: #0d6efd; margin-top: 3px;"></i>
                <div style="flex: 1;">
                  <div style="color: #dee2e6; font-size: 14px; font-weight: 500;">
                    ${p.name || p.label || ''}
                  </div>
                  <div style="color: #adb5bd; font-size: 12px; margin-top: 2px;">
                    ${p.city || ''} · ${p.postcode || ''} · ${p.context || ''}
                  </div>
                </div>
              </div>
            </div>
          `;
        })
        .join('');

      suggestionsDiv.style.display = 'block';
      suggestionsDiv.style.width = adresseCompleteInput.offsetWidth + 'px';

      suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
          item.style.backgroundColor = '#495057';
        });
        item.addEventListener('mouseleave', () => {
          item.style.backgroundColor = 'transparent';
        });
        item.addEventListener('click', () => {
          const idx = parseInt(item.dataset.idx);
          selectAddress(lastFeatures[idx]);
        });
      });

    } catch (e) {

    }
  });

  function selectAddress(feature) {
    if (!feature) return;
    const p = feature.properties || {};

    adresseCompleteInput.value = p.label || '';

    if (numRueInput) numRueInput.value = p.housenumber || '';
    if (adresseInput) adresseInput.value = p.street || p.name || '';
    if (codeInput) codeInput.value = p.postcode || '';
    if (villeInput) villeInput.value = p.city || '';
    if (paysInput) paysInput.value = 'France';

    suggestionsDiv.style.display = 'none';
    suggestionsDiv.innerHTML = '';

    adresseCompleteInput.classList.add('is-valid');
    setTimeout(() => adresseCompleteInput.classList.remove('is-valid'), 2000);
  }

  document.addEventListener('click', (e) => {
    if (!adresseCompleteInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
      suggestionsDiv.style.display = 'none';
    }
  });

  let selectedIndex = -1;
  adresseCompleteInput.addEventListener('keydown', (e) => {
    const items = suggestionsDiv.querySelectorAll('.suggestion-item');
    if (items.length === 0) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
      updateSelection(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedIndex = Math.max(selectedIndex - 1, 0);
      updateSelection(items);
    } else if (e.key === 'Enter' && selectedIndex >= 0) {
      e.preventDefault();
      const idx = parseInt(items[selectedIndex].dataset.idx);
      selectAddress(lastFeatures[idx]);
      selectedIndex = -1;
    } else if (e.key === 'Escape') {
      suggestionsDiv.style.display = 'none';
      selectedIndex = -1;
    }
  });

  function updateSelection(items) {
    items.forEach((item, i) => {
      item.style.backgroundColor = i === selectedIndex ? '#495057' : 'transparent';
    });
  }
}

function markField(id, ok) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('is-invalid', !ok);
  el.classList.toggle('is-valid', ok);
}

function checkRegex(id, value, regex, msg) {
  const ok = regex.test(value);
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function checkRequired(id, value, msg) {
  const ok = value !== "";
  markField(id, ok);
  if (!ok) {
    showToast(msg, "warning");
    const el = document.getElementById(id);
    if (el) el.focus();
  }
  return ok;
}

function addressBlockTouched(v) {
  return [v.num_rue, v.nom_rue, v.complement, v.code_postal, v.ville, v.pays].some(x => (x || "").trim() !== "");
}

async function checkEmailExists(email, excludeUserId = null) {
  try {
    const url = `../models/Read/checkEmail.php?email=${encodeURIComponent(email)}${excludeUserId ? `&excludeId=${excludeUserId}` : ''}`;
    const response = await fetch(url, {
      method: "GET",
      headers: { 'Content-Type': "application/json" }
    });
    const result = await response.json();
    return result.exists;
  } catch (error) {
    console.error("Erreur vérification email:", error);
    return false;
  }
}

function clearValidationClasses(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  
  const inputs = form.querySelectorAll('.is-valid, .is-invalid');
  inputs.forEach(input => {
    input.classList.remove('is-valid', 'is-invalid');
  });
}

function resetModalForm(modalId, formId) {
  const modalElement = document.getElementById(modalId);
  if (!modalElement) return;
  
  modalElement.addEventListener('hidden.bs.modal', () => {
    clearValidationClasses(formId);
    const form = document.getElementById(formId);
    if (form) form.reset();
  });
  
  modalElement.addEventListener('show.bs.modal', () => {
    clearValidationClasses(formId);
  });
}

function openSocieteModal() {
    const addModalEl = document.getElementById('addModal');
    if (addModalEl) {
        const addModalInstance = bootstrap.Modal.getInstance(addModalEl);
        if (addModalInstance) {
            addModalInstance.hide();
        }
    }

    const modalEl = document.getElementById('addSocieteModal');
    if (!societeModal) {
        societeModal = new bootstrap.Modal(modalEl);
    }
    societeModal.show();
    document.getElementById('addSocieteForm').reset();
}

async function submitSociete() {
  const v = {
    nom: document.getElementById('societeNom').value.trim(),
    mail: document.getElementById('societeMail').value.trim(),
    tel: document.getElementById('societeTel').value.trim(),
    num_rue: document.getElementById('societeNumRue').value.trim(),
    nom_rue: document.getElementById('societeNomRue').value.trim(),
    complement: document.getElementById('societeComplement').value.trim(),
    code_postal: document.getElementById('societeCodePostal').value.trim(),
    ville: document.getElementById('societeVille').value.trim(),
    pays: document.getElementById('societePays').value.trim()
  };

  if (!checkRequired('societeNom', v.nom, "Nom société obligatoire")) return;
  if (!checkRegex('societeNom', v.nom, REGEX.nomRue, "Nom société invalide")) return;

  if (!v.num_rue || !v.nom_rue || !v.code_postal || !v.ville) {
    showToast("Adresse incomplète - utilisez l'autocomplétion", "warning");
    return;
  }

  const payload = {
    num_rue: v.num_rue,
    nom_rue: v.nom_rue,
    complement: v.complement || null,
    code_postal: v.code_postal,
    ville: v.ville,
    pays: v.pays,
    societe_nom: v.nom,
    societe_mail: v.mail || null,
    societe_telephone: v.tel || null
  };

  try {
    const response = await fetch('../models/Create/company.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json;charset=utf-8' },
      body: JSON.stringify(payload)
    });

    const data = await response.json();

  if (data.success) {
    societeModal.hide();

    await refreshSocietes();

    const select = document.getElementById('laSociete');
    if (data.new_societe_id) {
        select.value = data.new_societe_id;
    } else if (data.new_user_id) {
        select.value = data.new_user_id;
    }

    const addModalEl = document.getElementById('addModal');
    const addModal = new bootstrap.Modal(addModalEl);
    addModal.show();
    
    showToast(`Société "${v.nom}" créée !`, "success");
    } else {
      showToast(data.error || "Erreur création société", "error");
    }
  } catch (error) {
    console.error(error);
    showToast("Erreur réseau", "error");
  }
}

async function refreshSocietes() {
    try {
        const response = await fetch('../models/Read/companies.php');
        const companies = await response.json();
        
        const addSelect = document.getElementById('laSociete');
        if (addSelect) {
            const currentValue = addSelect.value;
            addSelect.innerHTML = '<option value="">Sélectionner...</option><option value="new_company">Créer une nouvelle entreprise</option>';
            
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                addSelect.appendChild(option);
            });
            
            addSelect.value = currentValue;
        }
        
        const editSelect = document.getElementById('editLaSociete');
        if (editSelect) {
            const currentEditValue = editSelect.value;
            editSelect.innerHTML = '';
            companies.forEach(company => {
                const option = new Option(company.Societe_Nom, company.Societe_ID);
                editSelect.appendChild(option);
            });
            editSelect.value = currentEditValue;
        }
        
    } catch (error) {
        console.error('Erreur refresh sociétés:', error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
  setupAdresseAutocomplete({
    adresseCompleteId: "laAdresseComplete",
    numRueId: "leNumRue",
    adresseId: "laAdresse",
    codeId: "leCode",
    villeId: "laVille",
    paysId: "lePays"
  });

  setupAdresseAutocomplete({
    adresseCompleteId: "editLaAdresseComplete",
    numRueId: "editLeNumRue",
    adresseId: "editLaAdresse",
    codeId: "editLeCode",
    villeId: "editLaVille",
    paysId: "editLePays"
  });

  setupAdresseAutocomplete({
    adresseCompleteId: "societeAdresseComplete",
    numRueId: "societeNumRue",
    adresseId: "societeNomRue",
    codeId: "societeCodePostal",
    villeId: "societeVille",
    paysId: "societePays"
  });

  document.getElementById('laSociete').addEventListener('change', function () {
    if (this.value === 'new_company') {
        this.value = '';
        openSocieteModal();
    }
  });


  resetModalForm('addModal', 'addForm');
  resetModalForm('editModal', 'editForm');
  resetModalForm('addSocieteModal', 'addSocieteForm');

  loadTable();
  
  const supprConfirm = document.getElementById('supprConfirm');
  if (supprConfirm) {
      supprConfirm.addEventListener('click', async () => {
          try {
              if (!deleteUserId) return;
              await deleteUserById(deleteUserId);
              if (confirmModal) confirmModal.hide();
              deleteUserId = null;
              await loadTable();
              showToast("Utilisateur supprimé.", "success");
          } catch (e) {
              showToast("Erreur : " + e.message, "error");
          }
      });
  }
  
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