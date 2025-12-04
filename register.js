async function createUser() 
{
    var nomValue = document.getElementById("nom").value;
    var prenomValue = document.getElementById("prenom").value;
    var emailValue = document.getElementById("email").value;
    var passwordValue = document.getElementById("password").value;
    var confirmPasswordValue = document.getElementById("confirmPassword").value;
    var phoneValue = document.getElementById("phone").value;
    var addressValue = document.getElementById("address").value;
    var additionalAddressValue = document.getElementById("additionalAddress").value;
    var postalCodeValue = document.getElementById("postalCode").value;
    var cityValue = document.getElementById("city").value;
    var companyValue = document.getElementById("company").value;
    var civiliteHomme = document.getElementById("civilite_homme");
    var civiliteFemme = document.getElementById("civilite_femme");

    var civiliteValue = "";
    if (civiliteHomme && civiliteHomme.checked) {
        civiliteValue = "Monsieur";
    } else if (civiliteFemme && civiliteFemme.checked) {
        civiliteValue = "Madame";
    }

    var adresseNum = "";
    var adresseNom = addressValue;
    var m = addressValue.trim().match(/^\s*(\d+)\s*(.*)$/);
    if (m) {
        adresseNum = m[1];
        adresseNom = m[2];
    }

    if (additionalAddressValue === "")
    {
        additionalAddressValue = null;
    }

    try 
    {
        console.log(nomValue, prenomValue)

        const url = 'http://172.20.33.6/checkmystars/models/crud/creer.php';
        const data = 
        {
            Nom: nomValue,
            Prenom: prenomValue,
            Civilite: civiliteValue,
            Telephone: phoneValue,
            Email: emailValue,
            AdresseNum: adresseNum,
            AdresseNom: adresseNom,
            Complement: additionalAddressValue,
            CodePostal: postalCodeValue,
            Ville: cityValue,
            Pays: "France",
            Societe: companyValue,
            MotPasse: passwordValue

        };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

    }
    catch (error) 
    {

    }
}

function checkLastName()
{
    let regex = /^[a-z]+$/i;
    regex.test(nomValue)
}

