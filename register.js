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
    var additionalAddressValue = document.getElementById("additionalAddress").value;
    var cityValue = document.getElementById("city").value;
    var companyValue = document.getElementById("company").value;

    var civiliteHommeValue = document.getElementById("civilite_homme").value;
    var civiliteFemmeValue = document.getElementById("civilite_femme").value;

    var civiliteValue;

    if (civiliteHommeValue.checked)
    {
        var civiliteValue = "Monsieur";
    }
    else
    {
        var civiliteValue = "Madame";
    }

    var adresseNum = addressValue.substring(0, 2);
    var adresseNom = addressValue.substring(2);
    

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

