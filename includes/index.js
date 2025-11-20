function login() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    //regex pour le mail
    checkMail = /^((?!\.)[\w\-_.]*[^.])(@\w+)(\.\w+(\.\w+)?[^.\W])$/.test(email)

    if (checkMail){
        document.getElementById("email").classList.remove("is-invalid")
        if(password!=""){
            document.getElementById("password").classList.remove("is-invalid")
            let post = new FormData()
            post.append("email", email)
            post.append("password", password)

            const request = new XMLHttpRequest()
            request.open("POST", `login.php`, true)
            request.send(post)
            request.onreadystatechange = function(){
                if (request.readyState === 4 && request.status === 200){
                    result = JSON.parse(request.responseText)
                    console.log(result)

                    if (!result){//si la co échoue

                    }
                }
            }
        } else {
            document.getElementById("password").classList.add("is-invalid")
        }
    } else {
        if(!checkMail){
            document.getElementById("email").classList.add("is-invalid")
        }
    }
}

//jsp cque c'est mais jle laisse au cas où (mort à l'IA btw)

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
