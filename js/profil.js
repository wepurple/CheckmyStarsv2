async function getInfos() {
    const url = "models/crud/afficherUtilisateur.php?IdPersonne="+id
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0]
    return result
}



document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    const list = await getInfos()
    console.log(list)

    //informations
    document.getElementById('nom').textContent = list['Utilisateur_Nom']
    document.getElementById('prenom').textContent = list['Utilisateur_Prenom']
    document.getElementById('civilite').textContent = list['Utilisateur_Civilite']
    document.getElementById('mail').textContent = list['Utilisateur_Mail']
    document.getElementById('tel').textContent = list['Utilisateur_Telephone']
    document.getElementById('societe').textContent = list['Utilisateur_Societe']

    //adresse
    document.getElementById('numRue').textContent = list['AdressePostale_NumeroRue']
    document.getElementById('nomRue').textContent = list['AdressePostale_NomRue']
    document.getElementById('complement').textContent = list['AdressePostale_Complement']
    document.getElementById('codePost').textContent = list['AdressePostale_CodePostal']
    document.getElementById('ville').textContent = list['AdressePostale_Ville']
    document.getElementById('pays').textContent = list['AdressePostale_Pays']

});