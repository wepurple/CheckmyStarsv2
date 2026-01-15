<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap%205.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="stylesheet" href="bootstrap%205.3/css/style.css">
        
    </head>
    <body>
    <div class="register-container">
        <div class="login-header text-center">
            <i class="fas fa-user-circle fa-3x mb-3"></i>
            <h2>CheckMyStars</h2>
        </div>

        <div class="login-body">
            <!-- toast -->
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div id="liveToast" class="toast text-bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-warning">
                        <strong class="me-auto" id="toast-title">Échec de la connexion</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body" id="toast-message">Identifiants incorrects</div>
                </div>
            </div>
            <!-- end toast -->

            <form action="" method="post" id="registerForm">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nom" name="nom" placeholder="nom" required>
                            <label for="nom"><i class="fa-solid fa-person me-2"></i>Nom</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="prenom" name="prenom" placeholder="prenom" required>
                            <label for="prenom"><i class="fa-solid fa-person me-2"></i>Prénom</label>
                        </div>

                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="email" required>
                            <label for="email"><i class="fas fa-envelope me-2"></i>Adresse email</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="mdp" placeholder="Mot de passe" required>
                            <label for="password"><i class="fas fa-lock me-2"></i>Mot de passe</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Mot de passe" required>
                            <label for="confirmPassword"><i class="fas fa-lock me-2"></i>Confirmer le mot de passe</label>
                        </div>

                        <div class="mb-3">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2 mb-0">Civilité</legend>
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="civilite" id="civilite_homme" value="Homme" required>
                                        <label class="form-check-label" for="civilite_homme">
                                            <i class="fa-solid fa-person me-1"></i> Homme
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="civilite" id="civilite_femme" value="Femme">
                                        <label class="form-check-label" for="civilite_femme">
                                            <i class="fa-solid fa-person-dress me-1"></i> Femme
                                        </label>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Numero de téléphone" pattern="[0-9]{10}" required>
                            <label for="phone"><i class="fas fa-phone me-2"></i>Numéro de téléphone</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Adresse" required>
                            <label for="address"><i class="fa-solid fa-house me-2"></i>Adresse</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="additionalAddress" name="additionalAddress" placeholder="Complement d'adresse">
                            <label for="additionalAddress"><i class="fa-solid fa-house me-2"></i>Complément d'adresse</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Code postale" required>
                            <label for="postalCode"><i class="fa-solid fa-envelope me-2"></i>Code postal</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="city" name="city" placeholder="Ville" required>
                            <label for="city"><i class="fa-solid fa-city me-2"></i>Ville</label>
                        </div>

                        <div class="form-floating">
                            <input type="text" class="form-control" id="company" name="company" placeholder="société" required>
                            <label for="company"><i class="fa-solid fa-building me-2"></i>Société</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="createUser(); return false;">
                            <i class="fas fa-sign-in-alt me-2"></i>S'inscrire
                        </button>
                    </div>
                </div>
            </form>

            <div class="links text-center">
                <a href="index.php">Déjà un compte ?</a>
            </div>
        </div>
    </div>

  <script src="bootstrap%205.3/js/bootstrap.bundle.min.js"></script>
  <script src="js/register.js"></script>
</body>
</html>