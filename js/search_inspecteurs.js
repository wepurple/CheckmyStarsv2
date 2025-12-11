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
            e.lastElementChild.appendChild(document.createElement('button'))
            e.lastElementChild.lastElementChild.classList.add("btn")
            e.lastElementChild.lastElementChild.classList.add("btn-danger")
            e.lastElementChild.lastElementChild.textContent = "Supprimer"
        }
    }else{
        clearTab()
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

document.addEventListener("DOMContentLoaded", function() {//quand la page est chargée
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