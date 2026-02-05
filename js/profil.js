const ids = [
    "nom",
    "prenom",
    "civilite",
    "mail",
    "tel",
    "societe",
    "laAdresseComplete",
    "complement"

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
    console.log(list)
    const adresseComplete = [
    list.AdressePostale_NumeroRue,
    list.AdressePostale_NomRue,
    list.AdressePostale_CodePostal,
    list.AdressePostale_Ville
    ].filter(Boolean).join(' ');

    for (let i = 0 ; i<ids.length ; i++){
        if (i == 6)
        {
           document.getElementById(ids[i]).value = adresseComplete;
        }
        else if (i == 7)
        {
            document.getElementById(ids[i]).value = list.AdressePostale_Complement;
        }
        else
        {
            document.getElementById(ids[i]).value = list[elements[i]]
        }
    }
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
    margin-top: 7%;
    margin-left: 30.5%;
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
        // Récupère les valeurs actuelles côté serveur si certains champs d'adresse sont vides
        const existing = await getInfos();
        const data = {
            nom: document.getElementById(ids[0]).value,
            prenom: document.getElementById(ids[1]).value,
            genre: document.getElementById(ids[2]).value,
            mail: document.getElementById(ids[3]).value,
            tel: document.getElementById(ids[4]).value,
            societe: document.getElementById(ids[5]).value,
            numRue: document.getElementById("leNumRue").value || existing.AdressePostale_NumeroRue || "",
            nomRue: document.getElementById("laAdresse").value || existing.AdressePostale_NomRue || "",
            complement: document.getElementById(ids[7]).value || existing.AdressePostale_Complement || "",
            cp: document.getElementById("leCode").value || existing.AdressePostale_CodePostal || "",
            ville: document.getElementById("laVille").value || existing.AdressePostale_Ville || "",
            pays: document.getElementById("lePays").value || existing.AdressePostale_Pays || ""
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

    setupAdresseAutocomplete({
        adresseCompleteId: "laAdresseComplete",
        numRueId: "leNumRue",
        adresseId: "laAdresse",
        codeId: "leCode",
        villeId: "laVille",
        paysId: "lePays"
    });

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
