async function preFillClientInfo()
{
    var clientLastName = document.getElementById("leNom").placeholder;
    var clientFirstName = document.getElementById("lePrenom").placeholder;
    var clientCompany = document.getElementById("laSociete").placeholder;

    const currentUrl = window.location.search;
    var query = new URLSearchParams(currentUrl);
    var clientId = query.get('id');

    var clientInformation = await getUserById(clientId);

    clientLastName = clientInformation.Utilisateur_Nom;
    clientFirstName = clientInformation.Utilisateur_Prenom;
    clientCompany = clientInformation.Societe_Nom;

    console.log(clientInformation);
}

async function getUserById(id) {
    const url = "../../models/Read/users.php?IdPersonne=" + id;
    const response = await fetch(url, {
        method: "GET",
        headers: {
            'Content-Type': "application/json"
        }
    });
    const result = await response.json();

    return result;
}

document.addEventListener("DOMContentLoaded", () => {
    preFillClientInfo()
});