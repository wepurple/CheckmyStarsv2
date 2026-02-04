

document.addEventListener("DOMContentLoaded", () => {
    var clientLastName = document.getElementById("leNom");
    var clientFirstName = document.getElementById("leNom");
    var clientCompany = document.getElementById("laSociete");

    const currentUrl = window.location.search;
    var query = new URLSearchParams(currentUrl);
    var clientId = query.get('id');

    console.log(clientId);
});