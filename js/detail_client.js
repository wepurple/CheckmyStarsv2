function preFillClientInfo()
{
    var clientLastName = document.getElementById("leNom");
    var clientFirstName = document.getElementById("leNom");
    var clientCompany = document.getElementById("laSociete");

    const currentUrl = window.location.search;
    var query = new URLSearchParams(currentUrl);
    var clientId = query.get('id');

    var clientInformation = getUserById(clientId);
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