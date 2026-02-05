const ids = [
    "nom",
    "prenom",
    "civilite",
    "mail",
    "tel",
    "societe",
    "numRue",
    "nomRue",
    "complement",
    "codePost",
    "ville",
    "pays"
]
const facultatif = [//liste des champs facultatifs du formulaire, à remplir avec des champs contenus dans la liste ids
    "complement"
]
const elements = [
    "Utilisateur_Nom",
    "Utilisateur_Prenom",
    "Utilisateur_Civilite",
    "Utilisateur_Mail",
    "Utilisateur_Telephone",
    "Utilisateur_Societe",
    "AdressePostale_NumeroRue",
    "AdressePostale_NomRue",
    "AdressePostale_Complement",
    "AdressePostale_CodePostal",
    "AdressePostale_Ville",
    "AdressePostale_Pays"
]
const modalElements = [
    "oldPassword",
    "newPassword",
    "confirmPassword"
]
const regexMail = /^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/
    
async function getInfos() {//va chercher les infos relatives à la personne connectée
    const url = "models/read/myInfo.php"
    const response = await fetch(url, {
        method : "GET",
        headers : {
            'Content-Type' : "application/json"
        }
    })

    const result = JSON.parse(await response.text())["utilisateur"][0]
    return result
}

async function updateInfos(){
    const list = await getInfos()
    //console.log(list)

    for (let i = 0 ; i<ids.length ; i++){
        console.log(document.getElementById(ids[i]).value = list[elements[i]]);
        document.getElementById(ids[i]).value = list[elements[i]]
    }
}

function edit(){//active tous les champs et inverse les boutons
    for (let j = 0 ; j<ids.length ; j++){
        document.getElementById(ids[j]).removeAttribute("disabled")
    }
    document.getElementById("validerButton").removeAttribute("disabled")
    document.getElementById("cancelButton").removeAttribute("disabled")
    document.getElementById("editButton").setAttribute("disabled", "")
}

function cancel(){//désactive tous les champs puis les réinitialise, et inverse les boutons
    for (let j = 0 ; j<ids.length ; j++){
        document.getElementById(ids[j]).setAttribute("disabled", "")
        document.getElementById(ids[j]).classList.remove('is-invalid')
    }
    document.getElementById("validerButton").setAttribute("disabled", "")
    document.getElementById("cancelButton").setAttribute("disabled", "")
    document.getElementById("editButton").removeAttribute("disabled")
    updateInfos()
}

async function valider(){//s'execute après avoir pressé le bouton valider dans la modification des infos perso
    let verif = true
    for (let k = 0 ; k<ids.length ; k++){//pour chaque entrée du formulaire, répertoriées dans la liste ids[]
        let current = document.getElementById(ids[k])
        if(current.value == "" && !ids[k].includes(facultatif)){//si l'entrée est vide alors qu'elle n'est pas facultative
            verif = false
            current.classList.add('is-invalid')
        }else{//si l'entrée est renseignée, on va vérifier qu'elle respecte la regex correspondante
            let leRegex
            switch(ids[k]){
                case "mail"://si c'est l'entrée des mails par exemple, on applique la regex pour les mails
                    leRegex = regexMail
                    break;
                default :
                leRegex = /^/ //pour le reste des entrées, on appliquera une regex qui accepte tout
            }
            if(!leRegex.exec(current.value)){//test regex
                verif = false
                current.classList.add('is-invalid')
            }else{
                current.classList.remove('is-invalid')
            }
        }
    }
    if(verif){//si toutes les vérifications sont ok, on envoie le formulaire
        const url = 'models/Update/updateUser.php'
        const data = {
            nom: document.getElementById(ids[0]).value,
            prenom: document.getElementById(ids[1]).value,
            genre: document.getElementById(ids[2]).value,
            mail: document.getElementById(ids[3]).value,
            tel: document.getElementById(ids[4]).value,
            societe: document.getElementById(ids[5]).value,
            numRue: document.getElementById(ids[6]).value,
            nomRue: document.getElementById(ids[7]).value,
            complement: document.getElementById(ids[8]).value,
            cp: document.getElementById(ids[9]).value,
            ville: document.getElementById(ids[10]).value,
            pays: document.getElementById(ids[11]).value
        }
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const responseText = await response.text();
        const state = await response.status
        const test = await JSON.parse(responseText)

        if(state == 200){
            toastColor("success")
        }else{
            toastColor("warning")
        }
        document.getElementById("toastText").textContent = test["response"]
        leToast.show()
        updateInfos()
        cancel()
    }
}

function cancelPassword(){//reset les infos dans le modal
    for (let x = 0 ; x<modalElements.length ; x++){
        document.getElementById(modalElements[x]).value = ""
        document.getElementById(modalElements[x]).classList.remove('is-invalid')
    }
}

async function submitPassword(){//s'execute après avoir validé la modification de mdp
    const pwRegex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/
    /**
     * une majuscule
     * une minuscule
     * un chiffre
     * 12 caractères minimum
     * 1 caractère spécial
    */

    //vérifie si tous les champs sont remplis
    let verifRempli = true
    for (let k = 0 ; k<modalElements.length ; k++){
        if(document.getElementById(modalElements[k]).value == ""){
            verifRempli = false
            document.getElementById(modalElements[k]).classList.add('is-invalid')
        }else{
            document.getElementById(modalElements[k]).classList.remove('is-invalid')
        }
    }

    if(verifRempli){//si tous les champs sont remplis
        if(pwRegex.test(document.getElementById(modalElements[1]).value)){//si le regex est ok
            if(document.getElementById(modalElements[1]).value == document.getElementById(modalElements[2]).value){//si la confirmation est confirmée avec validation validée
                //envoi de la requete de changement de mdp
                const url = 'models/update/updatePwd.php'
                const data = {
                    old: document.getElementById(modalElements[0]).value,
                    new: document.getElementById(modalElements[1]).value,
                }
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const responseText = await response.text();
                const state = await response.status
                const test = await JSON.parse(responseText)

                if (await state == 200){
                    toastColor("success")
                    modalEditMdp.hide()
                    cancelPassword()
                }else{
                    toastColor("warning")
                }
                document.getElementById('toastText').textContent = test["response"]
                leToast.show()
            }else{
                toastColor("warning")
                document.getElementById('toastText').textContent = "Les champs ne correspondent pas"
                leToast.show()
            }
        }else{
            toastColor("warning")
            document.getElementById('toastText').textContent = "Mot de passe trop faible"
            leToast.show()
        }
    }
}

function toastColor(couleur) {//change la couleur du toast
    const t = document.getElementById("toast")
    const u = ["text-bg-primary", "text-bg-success", "text-bg-danger", "text-bg-warning"]
    for (let gg = 0 ; gg<u.length ; gg++){
        t.classList.remove(u[gg])
    }
    switch (couleur){
        case "success":
            t.classList.add(u[1])
            break;
        case "danger":
            t.classList.add(u[2])
            break;
        case "warning":
            t.classList.add(u[3])
            break;
        default :
            t.classList.add(u[0])
    }
    //leToast.show()
}

function editPasswordBtn(){
    modalEditMdp.show()
}

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée

    //déclaration des modals & toasts
    modalEditMdp = new bootstrap.Modal(document.getElementById('modalPassword'))
    leToast = new bootstrap.Toast(document.getElementById('toast'))

    //déclencheurs qui réagissent à la touche entrée
    document.getElementById("oldPassword").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("newPassword").focus();
    }
    });
    document.getElementById("newPassword").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("confirmPassword").focus();
    }
    });
    document.getElementById("confirmPassword").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("submitPwBtn").click();
    }
    });


    //remplissage du select de l'entreprise
    listCompanies = new XMLHttpRequest()
    listCompanies.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200) {
            result = JSON.parse(listCompanies.responseText)
            for(element in result){
                document.getElementById('societe').appendChild(document.createElement("option"))
                document.getElementById('societe').lastElementChild.value = result[element]["Societe_ID"]
                document.getElementById('societe').lastElementChild.textContent = result[element]["Societe_Nom"]
            }
        }
    }
    listCompanies.open("GET", "./models/Read/companies.php", true);
    listCompanies.send()


    //remplissage initial du tableau
    updateInfos()
});
