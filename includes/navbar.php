<nav class="sticky-top navbar navbar-expand-lg nav-underline border-bottom border-3 bg-dark" data-bs-theme="dark">

    <div class="container-fluid">

        <a class="navbar-brand " href="/Checkmystars/CheckMyStars/dashboard">
            <img src="/Checkmystars/CheckMyStars/assets/pictures/logosm.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            CheckMyStars
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "/checkmystars/dashboard")){ echo ' active'; } ?>" href="/Checkmystars/CheckMyStars/dashboard">
                        Tableau de bord
                    </a>
                </li>

            <?php if($_SESSION['Role']['Administrateur']){ ?>

                <li class="nav-item">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "gestion_utilisateurs")){ echo ' active'; } ?>" href="/Checkmystars/CheckMyStars/gestion/gestion_utilisateurs">
                        Gestion des utilisateurs
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "critereback") || str_contains(strtolower($_SERVER["PHP_SELF"]), "criterebackstar")){ echo ' active'; } ?>" href="/Checkmystars/CheckMyStars/criteres/critereBack">
                        Gestion des critères
                    </a>
                </li>

            <?php } ?>

            <?php if($_SESSION['Role']['Inspecteur'] || $_SESSION['Role']['Administrateur']){ ?>

                <li class="nav-item">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "gestion_dossiers") || str_contains(strtolower($_SERVER["PHP_SELF"]), "front_dossier")){ echo ' active'; } ?>" href="/Checkmystars/CheckMyStars/gestion/gestion_dossiers">
                        Gestion des dossiers
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "facture")){ echo ' active'; } ?>" href="/Checkmystars/CheckMyStars/factures/facture">
                        Factures et devis
                    </a>
                </li>

            <?php } ?>

            </ul>

            <hr>

            <ul class="navbar-nav ms-2 mb-2 mb-lg-0">

                <li class="nav-item ">
                    <a class=" nav-link<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "/profil")){ echo ' active'; } ?>"<?php if(str_contains(strtolower($_SERVER["PHP_SELF"]), "/profil")){ echo ' aria-current="page"'; } ?> href="/Checkmystars/CheckMyStars/profil">
                        Connecté<?php if(isset($_SESSION['Prenom']) && isset($_SESSION['Nom'])){ echo " en tant que " . $_SESSION['Prenom'] . " " . $_SESSION['Nom']; } ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class=" nav-link" href="/Checkmystars/CheckMyStars/deco">Se déconnecter</a>
                </li>

            </ul>

        </div>

    </div>

</nav>
