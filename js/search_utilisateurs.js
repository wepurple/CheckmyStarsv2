let seeModal = null;
let editModal = null;
let deleteUserId = null;
let confirmModal = null;

async function getAllusers()
{
    const url = "models/Read/users.php";
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    });

    const result = await response.json();
    return result;
}

async function getUserById(id)
{
    const url = "models/Read/users.php?IdPersonne="+id;
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
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

async function showUserUpdateModal(id) 
{
    try
    {
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

        switch(user["Utilisateur_Civilite"]){
            case "Monsieur" :
                document.getElementById('editLeGenre').value = "1";
                break;
            case "Madame" :
                document.getElementById('editLeGenre').value = "2";
                break;
            case "Iel" :
                document.getElementById('editLeGenre').value = "3";
                break;
            default :
                document.getElementById('editLeGenre').value = "3";
        }

        if (user.Societe_ID) {
            document.getElementById('editLaSociete').value = user.Societe_ID;
        }

        var roleUser;

        if (user && user.admin == 1)
        {
            roleUser = "3";
        }
        else if (user && user.inspecteur == 1)
        {
            roleUser = "2";
        }
        else if (user && user.donneurordre == 1)
        {
            roleUser = "1";
        }
        else if (user && user.proprietaire == 1)
        {
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

    } catch (error) 
    {
        console.error("Erreur:", error);
        alert("Impossible d'ouvrir le modal: " + error.message);
    }
}

async function showUserInfoModal(id)
{
    try 
    {
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

        if (user && user.admin == 1)
        {
            roleUser = "Administrateur";
        }
        else if (user && user.inspecteur == 1)
        {
            roleUser = "Inspecteur";
        }
        else if (user && user.donneurordre == 1)
        {
            roleUser = "Donneur d'ordre";
        }
        else if (user && user.proprietaire == 1)
        {
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
    } catch (error) 
    {
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


document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('supprConfirm');
  if (!btn) return;

  btn.addEventListener('click', async () => {
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
});

async function loadTable() {
  try {
    const users = await getAllusers();
    const tab = document.getElementById("table-body");
    if (!tab) return;

    tab.innerHTML = "";

    for (const user of users) {
      const userId = user.Utilisateur_ID ?? user.UtilisateurID ?? user.IdPersonne;
      const nom = user.Utilisateur_Nom ?? user.UtilisateurNom ?? '';
      const prenom = user.Utilisateur_Prenom ?? user.UtilisateurPrenom ?? '';

      let roleUser = "";
      if (user && user.admin == 1) roleUser = "Administrateur";
      else if (user && user.inspecteur == 1) roleUser = "Inspecteur";
      else if (user && user.donneurordre == 1) roleUser = "Donneur d'ordre";
      else if (user && user.proprietaire == 1) roleUser = "Proprietaire";

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${userId ?? ''}</td>
        <td>${nom}</td>
        <td>${prenom}</td>
        <td>${user.Utilisateur_Mail ?? user.UtilisateurMail ?? ''}</td>
        <td>${roleUser}</td>
        <td>${user.Societe_Nom ?? user.SocieteNom ?? ''}</td>
        <td class="text-end">
          <button class="btn btn-secondary btn-sm me-2">Voir</button>
          <button class="btn btn-sm btn-warning me-2">Modifier</button>
          <button class="btn btn-sm btn-danger">Supprimer</button>
        </td>
      `;

      const btns = tr.querySelectorAll('button');
      btns[0].addEventListener('click', () => showUserInfoModal(userId));
      btns[1].addEventListener('click', () => showUserUpdateModal(userId));
      btns[2].addEventListener('click', () => openDeleteModal(userId, nom, prenom));

      tab.appendChild(tr);
    }
  } catch (error) {
    console.error("Erreur lors du chargement du tableau:", error);
    alert("Impossible de charger les données: " + error.message);
  }
}

document.addEventListener("DOMContentLoaded", function() {
  loadTable();
});