<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CheckMyStars</title>
    <link href="bootstrap 5.3/css/bootstrap.css" rel="stylesheet" />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      rel="stylesheet" />
    <link rel="stylesheet" href="bootstrap 5.3/css/style.css" />
  </head>
  <body>
    <div class="login-container">
      <div class="login-header">
        <i class="fas fa-user-circle fa-3x mb-3"></i>
        <h2>Bienvenue</h2>
        <p>Connectez-vous à votre compte</p>
      </div>

      <div class="login-body">
        <div class="alert alert-danger" id="errorAlert" role="alert">
          <i class="fas fa-exclamation-circle"></i>
          <span id="errorMessage"></span>
        </div>

        <div class="alert alert-success" id="successAlert" role="alert">
          <i class="fas fa-check-circle"></i> Connexion réussie !
        </div>






        <form action="connect/connexion.php" method="POST" id="loginForm">
          <div class="form-floating mb-3">
            <input
              type="email"
              class="form-control"
              id="email"
              placeholder="name@example.com"
              required />
            <label for="email">
                <i class="fas fa-envelope me-2"></i>
                Adresse email
            </label>
          </div>

          <div class="form-floating mb-3">
            <input
              type="password"
              class="form-control"
              id="password"
              placeholder="Mot de passe"
              required />
            <label for="password">
                <i class="fas fa-lock me-2"></i>
                Mot de passe
            </label>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember" />
            <label class="form-check-label" for="remember">
              Se souvenir de moi
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-login w-100">
            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
          </button>
        </form>






        <div class="divider">
          <span>OU</span>
        </div>

        <div class="links">
          <a href="#" onclick="forgotPassword(); return false;"
            >Mot de passe oublié ?</a
          >
          <a href="#" onclick="createAccount(); return false;"
            >Créer un compte</a
          >
        </div>
      </div>
    </div>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
      const loginForm = document.getElementById("loginForm");
      const errorAlert = document.getElementById("errorAlert");
      const successAlert = document.getElementById("successAlert");
      const errorMessage = document.getElementById("errorMessage");

      loginForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const remember = document.getElementById("remember").checked;

        // Validation simple
        if (!email || !password) {
          showError("Veuillez remplir tous les champs");
          return;
        }

        if (password.length < 6) {
          showError("Le mot de passe doit contenir au moins 6 caractères");
          return;
        }

        // Simulation d'une connexion réussie
        hideError();
        successAlert.style.display = "block";

        setTimeout(() => {
          console.log("Connexion avec:", { email, password, remember });
          // Ici vous ajouteriez votre logique de connexion (API, etc.)
        }, 1000);
      });

      function showError(message) {
        errorMessage.textContent = message;
        errorAlert.style.display = "block";
        successAlert.style.display = "none";
      }

      function hideError() {
        errorAlert.style.display = "none";
      }

      function socialLogin(provider) {
        console.log("Connexion avec " + provider);
        alert("Connexion avec " + provider + " (fonctionnalité à implémenter)");
      }

      function forgotPassword() {
        alert("Fonctionnalité de récupération de mot de passe (à implémenter)");
      }

      function createAccount() {
        alert("Redirection vers la page de création de compte (à implémenter)");
      }
    </script> -->
  </body>
</html>
