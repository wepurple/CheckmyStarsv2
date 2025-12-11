async function createUser() 
{
    // Validation du formulaire avant envoi
    if (!validateForm()) {
        return; // Arrête si la validation échoue
    }

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
        const url = 'models/crud/creer.php';
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

        // Essayer de lire la réponse comme texte d'abord
        const responseText = await response.text();
        console.log('Réponse brute:', responseText);
        
        // Parser en JSON
        const result = JSON.parse(responseText);
        console.log('Réponse JSON:', result);
        console.log('Status code:', response.status);
        
        if (response.ok || response.status === 201) {
            showToast("Succès", result.message, 'success');
            // Réinitialiser le formulaire
            document.getElementById('registerForm').reset();
            // Redirection après 2 secondes
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            showToast("Erreur", result.message, 'danger');
        }
    } catch (error) {
        console.error('Erreur complète:', error);
        showToast("Erreur réseau", error.message, 'danger');
    }
}

function showToast(title, message, type) {
    const toast = document.getElementById('liveToast');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');
    
    // Mettre à jour le contenu
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Réinitialiser les classes
    toast.classList.remove('text-bg-danger', 'text-bg-success', 'text-bg-warning');
    const header = toast.querySelector('.toast-header');
    header.classList.remove('bg-danger', 'bg-success', 'bg-warning');
    
    // Ajouter la classe appropriée
    if (type === 'success') {
        toast.classList.add('text-bg-success');
        header.classList.add('bg-success');
    } else if (type === 'warning') {
        toast.classList.add('text-bg-warning');
        header.classList.add('bg-warning');
    } else {
        toast.classList.add('text-bg-danger');
        header.classList.add('bg-danger');
    }
    
    // Afficher le toast
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(document.getElementById('liveToast'));
    toastBootstrap.show();
}

function validateForm() {
    let nomValue = document.getElementById("nom").value;
    let prenomValue = document.getElementById("prenom").value;
    let emailValue = document.getElementById("email").value;
    let passwordValue = document.getElementById("password").value;
    let confirmPasswordValue = document.getElementById("confirmPassword").value;
    let phoneValue = document.getElementById("phone").value;
    let postalCodeValue = document.getElementById("postalCode").value;

    let errors = [];

    // Valider le nom et prénom (lettres uniquement)
    if (!/^[a-z\s]+$/i.test(nomValue.trim())) {
        errors.push("Le nom ne doit contenir que des lettres");
    }
    if (!/^[a-z\s]+$/i.test(prenomValue.trim())) {
        errors.push("Le prénom ne doit contenir que des lettres");
    }

    // Valider l'email
    if (!/^\S+@\S+\.\S+$/.test(emailValue)) {
        errors.push("Veuillez entrer une adresse email valide");
    }

    // Valider le mot de passe
    if (passwordValue.length < 6) {
        errors.push("Le mot de passe doit contenir au moins 6 caractères");
    }

    // Vérifier la confirmation du mot de passe
    if (passwordValue !== confirmPasswordValue) {
        errors.push("Les mots de passe ne correspondent pas");
    }

    // Valider le téléphone
    if (!/^[0-9]{10}$/.test(phoneValue.replace(/\s/g, ''))) {
        errors.push("Le téléphone doit contenir 10 chiffres");
    }

    // Valider le code postal
    if (!/^[0-9]{5}$/.test(postalCodeValue)) {
        errors.push("Le code postal doit contenir 5 chiffres");
    }

    if (errors.length > 0) {
        const errorMessage = errors.join(" • ");
        showToast("Erreur de validation", errorMessage, 'danger');
        return false;
    }

    return true;
}