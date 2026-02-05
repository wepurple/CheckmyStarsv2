<style>
    /* couleur de la navbar*/
    .navbar{
        background: #00165e;
    }
</style>
<nav class="sticky-top navbar navbar-expand-lg nav-underline">

    <div class="container-fluid">

        <a class="navbar-brand text-light" href="/checkmystars/admin">
            <img src="/CheckMyStars/assets/pictures/logosm.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            CheckMyStars
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <!--
                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/admin.php"){ echo ' active'; } ?>" href="./admin">Accueil</a>
                </li>
            -->
                <li class="nav-item">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/dashboard.php"){ echo ' active'; } ?>" href="/checkmystars/dashboard">
                        Tableau de bord
                    </a>
                </li>

            <?php if($_SESSION['Role']['Administrateur']){ ?>
            
                <li class="nav-item">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion/gestion_utilisateurs.php"){ echo ' active'; } ?>" href="/checkmystars/gestion/gestion_utilisateurs">
                        Gestion des utilisateurs
                    </a>
                </li>

             
                <li class="nav-item">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/criteres/critereback.php" || strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/criteres/criterebackstar.php"){ echo ' active'; } ?>" href="/checkmystars/criteres/critereBack">
                        Gestion des critères
                    </a>
                </li>

            <?php } ?>

            <?php if($_SESSION['Role']['Inspecteur'] || $_SESSION['Role']['Administrateur']){ ?>

                <li class="nav-item">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion/gestion_dossiers.php" || strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/front_dossier.php"){ echo ' active'; } ?>" href="/checkmystars/gestion/gestion_dossiers">
                        Gestion des dossiers
                    </a>
                </li>

                <li class="nav-item">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/factures/facture.php"){ echo ' active'; } ?>" href="/checkmystars/factures/facture">
                        Factures et devis
                    </a>
                </li>

            <?php } ?>

            </ul>

            <hr>

            <ul class="navbar-nav ms-2 mb-2 mb-lg-0">

                <li class="nav-item text-light">
                    <a class="text-light nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/profil.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/profil.php"){ echo ' aria-current="page"'; } ?> href = "/checkmystars/profil">
                        Connecté<?php if(isset($_SESSION['Prenom']) && isset($_SESSION['Nom'])){ echo " en tant que " . $_SESSION['Prenom'] . " " . $_SESSION['Nom']; } ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="text-light nav-link" href="/checkmystars/deco">Se déconnecter</a>
                </li>

            </ul>

        </div>

    </div>

</nav>
