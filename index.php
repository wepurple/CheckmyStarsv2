<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - CheckMyStars</title>

  <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
  <link rel="stylesheet" href="bootstrap 5.3/css/style.css">
  <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
</head>

<body>

    <!-- toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast text-bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-warning">
                <img src="" class="rounded me-2" alt="">
                <strong class="me-auto" id="titreToast">
                        Échec de la connexion
                </strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="motif">
                Identifiants incorrects
            </div>
        </div>
    </div>
    <!-- end toast -->

  <div class="login-container" >
    <div class="login-header text-center">
      <i class="fas fa-user-circle fa-3x mb-3"></i>
      <h2>CheckMyStars</h2>
    </div>

    <div class="login-body">
      <div class="alert alert-danger d-none" id="errorAlert">
        <i class="fas fa-exclamation-circle"></i>
        <span id="errorMessage"></span>
      </div>

      <form id="loginForm">
        <div class="form-floating mb-3">
            <input autofocus type="email" class="form-control" id="email" name="email" placeholder="email" value="marie.dupont@mail.com" required>
            <label for="email">
                <i class="fas fa-envelope me-2"></i>
                Adresse email
            </label>
        </div>

        <div class="form-floating mb-3">
          <input type="password" class="form-control" id="password" name="mdp" placeholder="Mot de passe" value="ep6ATgYuvZb%" required>
          <label for="password"><i class="fas fa-lock me-2"></i>Mot de passe</label>
        </div>

            <button type="button" onclick="login()" class="btn btn-primary w-100" id="valider">
                <i class="fas fa-sign-in-alt me-2"></i>
                Se connecter
            </button>
      </form>

      <div class="links text-center mt-3">
        <a href="#" onclick="forgotPassword(); return false;">Mot de passe oublié ?</a><br>
        <!-- <a href="register.php" onclick="createAccount(); return false;">Créer un compte</a> -->
      </div>

    </div>


  </div>

  <script src="bootstrap 5.3/js/bootstrap.js"></script>
  <script src="js/index.js"></script>
</body>
</html>