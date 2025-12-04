function clearTab(){
    tab = document.getElementById("table-body")
    child = tab.lastElementChild
    while (child){
        tab.removeChild(child)
        child = tab.lastElementChild
    }
}

function updateTab(result, k){
    clearTab()
    tab = document.getElementById("table-body")

    for (let i = 0; i < result.length; i++){
        //console.log(result[i])
        tab.appendChild(document.createElement("tr"))

        e=tab.lastElementChild
        e.appendChild(document.createElement('th'))
        e.lastElementChild.scope = "row"
        e.lastElementChild.textContent = result[i]["Utilisateur_ID"]

        for(let j=1;j<5;j++){
            e.appendChild(document.createElement('td'))
            console.log(result[i][k[j]])
            e.lastElementChild.textContent = result[i][k[j]]
        }
    }    
}

document.addEventListener("DOMContentLoaded", function() {//quand la page est chargée

    const request = new XMLHttpRequest()
    request.open("POST", `getInspecteurs.php`, true)
    request.send()
    request.onreadystatechange = function(){
        if (request.readyState === 4 && request.status === 200){
            result = JSON.parse(request.responseText)
            if(result){
                k = Object.keys(result[0])
            }
            updateTab(result, k)
        }
    }   

    let timer
    document.getElementById('recherche').addEventListener('input', function(){
        clearTimeout(timer)
        timer = setTimeout(function(){
            console.log("Recherche lancée")
            form = new FormData()
            form.append(document.getElementById('recherche').name, document.getElementById('recherche').value) /* faut améiliorer ça */
        }, 300)
    })
});

