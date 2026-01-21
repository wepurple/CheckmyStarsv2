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

async function showUserUpdateModal(id) 
{
    try
    {
        console.log("ok")
        var user = await getUserById(id)
        console.log("ok 1")

        document.getElementById('editLeNom').value = user.Utilisateur_Nom;
        document.getElementById('editLePrenom').value = user.Utilisateur_Prenom;
        document.getElementById('editLeMail').value = user.Utilisateur_Mail;

        switch(result["Utilisateur_Civilite"]){
            case "Monsieur" :
                document.getElementById('editLeGenre').value = "1"
                break;
            case "Madame" :
                document.getElementById('editLeGenre').value = "2"
                break;
            case "Iel" :
                document.getElementById('editLeGenre').value = "3"
                break;
            default :
                document.getElementById('editLeGenre').value = "3"
        }

        document.getElementById('editLaSociete').value = "TODO";
        document.getElementById('editLeTel').value = user.Utilisateur_Telephone;
        document.getElementById('editLeNumRue').value = user.AdressePostale_NumeroRue;
        document.getElementById('editLaAdresse').value = user.AdressePostale_NomRue;
        document.getElementById('editLeComplement').value = user.AdressePostale_Complement;
        document.getElementById('editLeCode').value = user.AdressePostale_CodePostal;
        document.getElementById('editLaVille').value = user.AdressePostale_Ville;
        document.getElementById('editLePays').value = user.AdressePostale_Pays;

        editModal.show()

    } catch (error) 
    {
        console.error("Erreur:", error);
    }
}

async function showUserInfoModal(id)
{
    try 
    {
        var user = await getUserById(id)

        document.getElementById('seeLeNom').value = user.Utilisateur_Nom;
        document.getElementById('seeLePrenom').value = user.Utilisateur_Prenom;
        document.getElementById('seeLeMail').value = user.Utilisateur_Mail;
        document.getElementById('seeGenre').value = user.Utilisateur_Civilite;
        document.getElementById('seeLaSociete').value = user.Societe_Nom;
        document.getElementById('seeLeTel').value = user.Utilisateur_Telephone;
        document.getElementById('seeLeNumRue').value = user.AdressePostale_NumeroRue;
        document.getElementById('seeLaAdresse').value = user.AdressePostale_NomRue;
        document.getElementById('seeLeComplement').value = user.AdressePostale_Complement;
        document.getElementById('seeLeCode').value = user.AdressePostale_CodePostal;
        document.getElementById('seeLaVille').value = user.AdressePostale_Ville;
        document.getElementById('seeLePays').value = user.AdressePostale_Pays;

        seeModal.show()
    } catch (error) 
    {
        console.error("Erreur:", error);
    }
}

async function loadTable()
{
    try 
    {
        var users = await getAllusers()
        var tab = document.getElementById("table-body");
        tab.innerHTML = "";

        for (var i = 0; i < users.length; i++)
        {
            var user = users[i];
            
            var tr = document.createElement("tr");

            var roleUser;

            if (user && user.admin == 1)
            {
                roleUser = "Administrateur"
            }
            else if (user && user.inspecteur == 1)
            {
                roleUser = "Inspecteur"
            }
            else if (user && user.proprietaire == 1)
            {
                roleUser = "Proprietaire"
            }
            
            tr.innerHTML = `
                <td>${user.Utilisateur_ID || user.IdPersonne || ''}</td>
                <td>${user.Utilisateur_Nom || ''}</td>
                <td>${user.Utilisateur_Prenom || ''}</td>
                <td>${user.Utilisateur_Mail || ''}</td>
                <td>${roleUser}</td>
                <td>${user.Societe_Nom || ''}</td>
                <td class="text-end">
                    <button class="btn btn-secondary btn-sm me-2" onclick="showUserInfoModal(${user.Utilisateur_ID})"> <i class="fa-solid fa-eye"></i> </button>
                    <button class="btn btn-sm btn-warning me-2 onclick="showUserUpdateModal(${user.Utilisateur_ID})"">Edit</button>
                    <button class="btn btn-sm btn-danger">Delete</button>
                </td>
            `;
            tab.appendChild(tr);
        }
    } catch (error) 
    {
        console.error("Erreur:", error);
    }
}

// Charger le tableau au démarrage de la page
document.addEventListener("DOMContentLoaded", function() {
    loadTable()

    seeModal = new bootstrap.Modal(document.getElementById('seeModal'))
    editModal = new bootstrap.Modal(document.getElementById('editModal'))
});


