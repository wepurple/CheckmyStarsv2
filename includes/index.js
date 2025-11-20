function login() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    //console.log(email);

    const request = new XMLHttpRequest()
    request.open("get", "login.php?email=${email}&password=${password}", true)
    request.send()
    request.onreadystatechange = function(){
        if (request.readyState === 4 && request.status === 200){
            console.log(JSON.parse(request.responseText))
        }
    }
}

//  <script>
//       const loginForm = document.getElementById("loginForm");
//       const errorAlert = document.getElementById("errorAlert");
//       const successAlert = document.getElementById("successAlert");
//       const errorMessage = document.getElementById("errorMessage");

//       loginForm.addEventListener("submit", function (e) {
//         e.preventDefault();

//         const email = document.getElementById("email").value;
//         const password = document.getElementById("password").value;
//         const remember = document.getElementById("remember").checked;

//         // Validation simple
//         if (!email || !password) {
//           showError("Veuillez remplir tous les champs");
//           return;
//         }

//         if (password.length < 6) {
//           showError("Le mot de passe doit contenir au moins 6 caractères");
//           return;
//         }

//         // Simulation d'une connexion réussie
//         hideError();
//         successAlert.style.display = "block";

//         setTimeout(() => {
//           console.log("Connexion avec:", { email, password, remember });
//           // Ici vous ajouteriez votre logique de connexion (API, etc.)
//         }, 1000);
//       });

//       function showError(message) {
//         errorMessage.textContent = message;
//         errorAlert.style.display = "block";
//         successAlert.style.display = "none";
//       }

//       function hideError() {
//         errorAlert.style.display = "none";
//       }

//       function socialLogin(provider) {
//         console.log("Connexion avec " + provider);
//         alert("Connexion avec " + provider + " (fonctionnalité à implémenter)");
//       }

//       function forgotPassword() {
//         alert("Fonctionnalité de récupération de mot de passe (à implémenter)");
//       }

//       function createAccount() {
//         alert("Redirection vers la page de création de compte (à implémenter)");
//       }
