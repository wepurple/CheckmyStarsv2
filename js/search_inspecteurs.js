function clearTab(){//vide le tableau
    tab = document.getElementById("table-body")
    child = tab.lastElementChild
    while (child){
        tab.removeChild(child)
        child = tab.lastElementChild
    }
}

function updateTab(z){//vide le tableau et le remplit avec les nouvelles données
    //console.log("Mise à jour du tableau")
    //console.log(z)
    if (z.length>0){
        k=Object.keys(z[0])
        clearTab()
        tab = document.getElementById("table-body")

        for (let i = 0; i < z.length; i++){
            //console.log(result[i])
            tab.appendChild(document.createElement("tr"))

            e=tab.lastElementChild
            e.appendChild(document.createElement('th'))
            e.lastElementChild.scope = "row"
            e.lastElementChild.textContent = z[i]["Utilisateur_ID"]

            for(let j=1;j<5;j++){
                e.appendChild(document.createElement('td'))
                //console.log(result[i][k[j]])
                e.lastElementChild.textContent = z[i][k[j]]
            }
            
            e.appendChild(document.createElement('td'))
            e.lastElementChild.classList.add("text-end",)

            //création bouton modifier
            e.lastElementChild.appendChild(document.createElement('button'))
                e.lastElementChild.lastElementChild.classList.add("btn","btn-warning","btn-sm")
                e.lastElementChild.lastElementChild.textContent = "Modifier"
                e.lastElementChild.lastElementChild.setAttribute("onclick", "modalEdit("+ z[i]["Utilisateur_ID"] +")")
                //icône
                e.lastElementChild.lastElementChild.appendChild(document.createElement('i'))
                e.lastElementChild.lastElementChild.lastElementChild.classList.add("fa-solid", "fa-pen-to-square", "mx-1")
            
            //création bouton reset password
            e.lastElementChild.appendChild(document.createElement('button'))
                e.lastElementChild.lastElementChild.classList.add("btn","btn-warning","btn-sm", "ms-2")
                e.lastElementChild.lastElementChild.textContent = "Reset mot de passe"
                e.lastElementChild.lastElementChild.setAttribute("onclick", "resetPassword("+
                    z[i]["Utilisateur_ID"] +
                    ", '" +
                    z[i]["Utilisateur_Nom"] +
                    "', '" +
                    z[i]["Utilisateur_Prenom"] +
                    "', '"+ 
                    z[i]["Utilisateur_Civilite"] +
                    "')")
                //icône
                e.lastElementChild.lastElementChild.appendChild(document.createElement('i'))
                e.lastElementChild.lastElementChild.lastElementChild.classList.add("fa-solid", "fa-key", "mx-1")
            
            //création bouton supprimer
            e.lastElementChild.appendChild(document.createElement('button'))
                e.lastElementChild.lastElementChild.classList.add("btn","btn-danger","btn-sm", "ms-2")
                e.lastElementChild.lastElementChild.textContent = "Supprimer"
                e.lastElementChild.lastElementChild.setAttribute(
                    "onclick",
                    "modalSuppr("+
                    z[i]["Utilisateur_ID"] +
                    ", '" +
                    z[i]["Utilisateur_Nom"] +
                    "', '" +
                    z[i]["Utilisateur_Prenom"] +
                    "', '"+ 
                    z[i]["Utilisateur_Civilite"] +
                    "')"
                )
                //icône
                e.lastElementChild.lastElementChild.appendChild(document.createElement('i'))
                e.lastElementChild.lastElementChild.lastElementChild.classList.add("fa-regular", "fa-trash-can", "mx-1")
        }
    }else{
        clearTab()
        tab = document.getElementById("table-body")
        tab.appendChild(document.createElement("tr"))
        e=tab.lastElementChild
        e.appendChild(document.createElement('td'))
        e.lastElementChild.colSpan = "6"
        e.lastElementChild.classList.add("text-center")
        e.lastElementChild.textContent = "Aucun résultat"
    }
}

function recherche(){
    //console.log("Recherche lancée")
    let form = new FormData()
    form.append("type", document.getElementById('type').value)
    form.append("value", document.getElementById('recherche').value)

    const request = new XMLHttpRequest()
    request.open("POST", `getInspecteurs.php`, true)
    request.send(form)
    request.onreadystatechange = function(){
        if (request.readyState === 4 && request.status === 200){
            //console.log(JSON.parse(request.responseText))
            updateTab(JSON.parse(request.responseText))
        }
    }
}


function modalSuppr(id, nom, prenom, genre){
    if (genre == "Madame"){
        leGenre = "inspectrice "
    }else{
        leGenre = "inspecteur "
    }
    document.getElementById("supprText").textContent = "Voulez-vous vraiment supprimer l'" + leGenre + prenom + " " + nom + " ?"
    document.getElementById("supprConfirm").setAttribute('onclick', 'suppr("'+id+'")')
    leModal.show()
}

async function suppr(id){
    /*
    let form = new FormData()
    form.append("IdPersonne", id)
    console.log(form)

    const requete = new XMLHttpRequest()
    requete.open("DELETE", "models/crud/supprimer.php", true)
    requete.send(form)

    requete.onreadystatechange = function(){
        if (requete.readyState === 4 && requete.status === 200){
            console.log(JSON.parse(requete.responseText))
            console.log("delete id " + id)
            recherche()
        }
    }
    */
    const url = "models/crud/supprimer.php"
    const data = {
        IdPersonne : id
    }
    const response = await fetch(url, {
        method : "DELETE",
        headers : {
            'Content-Type' : "application/json"
        },
        body : JSON.stringify(data)
    })

    const responseText = await response.text();
    console.log('Réponse :\n', responseText)
    
    recherche()
    leModal.hide()
}

async function modalEdit(id){
    const url = "models/crud/afficherUtilisateur.php?IdPersonne="+id
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0];

    console.log(result)
    document.getElementById('editLeNom').value = result["Utilisateur_Nom"]
    document.getElementById('editLePrenom').value = result["Utilisateur_Prenom"]
    document.getElementById('editLeMail').value = result["Utilisateur_Mail"]

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

    document.getElementById('editLaSociete').value = result["Utilisateur_Societe"]
    document.getElementById('editLeTel').value = result["Utilisateur_Telephone"]
    document.getElementById('editLeNumRue').value = result["AdressePostale_NumeroRue"]
    document.getElementById('editLaAdresse').value = result["AdressePostale_NomRue"]
    document.getElementById('editLeComplement').value = result["AdressePostale_Complement"]
    document.getElementById('editLeCode').value = result["AdressePostale_CodePostal"]
    document.getElementById('editLaVille').value = result["AdressePostale_Ville"]
    document.getElementById('editLePays').value = result["AdressePostale_Pays"]

    editModal.show()
}

function edit(){
    console.log("edit ok")
    editModal.hide()
}

function resetPassword(id, nom, prenom, genre){
    if (genre == "Madame"){
        leGenre = "inspectrice "
    }else{
        leGenre = "inspecteur "
    }
    document.getElementById("resetText").textContent = "Voulez-vous vraiment réinitialiser le mot de passe de l'" + leGenre + prenom + " " + nom + " ?"
    document.getElementById("resetConfirm").setAttribute('onclick', 'reset("' + id + '")')
    confirmResetModal.show()
    /*mail(
        "piverdier@stpbb.org",
        "reset mdp utilisateur id ".id,
        "Salutations, un administrateur a ordonné la réinitialisation du mot de passe de l'utilisateur avec l'identifiant n°".id,
    )*/
}

async function reset(id){
    console.log("reset id " + id)
    confirmResetModal.hide()
}

document.addEventListener("DOMContentLoaded", function() {//quand la page est chargée
    
    leModal = new bootstrap.Modal(document.getElementById('confirmModal'))
    editModal = new bootstrap.Modal(document.getElementById('editModal'))
    confirmResetModal = new bootstrap.Modal(document.getElementById('confirmResetPasswordModal'))

    //remplissage initial du tableau
    recherche()

    let timer
    document.getElementById('recherche').addEventListener('input', function(){//recherche toute les 300ms après la dernière frappe
        clearTimeout(timer)
        timer = setTimeout(function(){
            recherche()
        }, 300)
    })

    document.getElementById('type').addEventListener('change', function(){//recherche quand on change le type de critère
        if (document.getElementById('recherche').value!=""){
            recherche()
        }
    })
});