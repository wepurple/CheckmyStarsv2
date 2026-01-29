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
    
async function getInfos() {//va chercher les infos relatives à la personne connectée
    const url = "models/crud/myInfo.php"
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

function valider(){//s'execute après avoir pressé le bouton valider dans la modification des infos perso
    let verif = true
    for (let k = 0 ; k<ids.length ; k++){
        if(document.getElementById(ids[k]).value == ""){
            verif = false
            document.getElementById(ids[k]).classList.add('is-invalid')
        }else{
            document.getElementById(ids[k]).classList.remove('is-invalid')
        }
    }
    if(verif){
        console.log("ok")
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
                const url = 'models/crud/updatePwd.php'
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

document.addEventListener("DOMContentLoaded", async function() {//quand la page est chargée
    updateInfos()

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
});

function editPasswordBtn(){
    modalEditMdp.show()
}