<nav class="sticky-top navbar navbar-expand-lg bg-dark nav-underline"  data-bs-theme="dark">

    <div class="container-fluid">

        <a class="navbar-brand" href="admin.php">
            <img src="/CheckMyStars/pictures/logosm.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            CheckMyStars
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/admin.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/admin.php"){ echo ' aria-current="page"'; } ?> href="admin.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/dashboard.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/dashboard.php"){ echo ' aria-current="page"'; } ?> href="Dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion_inspecteurs.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion_inspecteurs.php"){ echo ' aria-current="page"'; } ?> href="gestion_inspecteurs.php">Gestion des inspecteurs</a>
                </li>

                 <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion_dossiers.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/gestion_dossiers.php"){ echo ' aria-current="page"'; } ?> href="gestion_dossiers.php">Gestion des dossiers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/critereback.php" || strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/critereback1star.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/critereback.php"){ echo ' aria-current="page"'; } ?> href="critereBack.php">Gestion des critères</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/facture.php" || strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/facture.php"){ echo ' active'; } ?>"<?php if(strtolower($_SERVER["PHP_SELF"]) == "/checkmystars/facture.php"){ echo ' aria-current="page"'; } ?> href="facture.php">Gestion des factures</a>
                </li>

            </ul>

            <hr>

            <div class="navbar-nav ms-auto mb-2 mb-lg-0">
                <span class="nav-text">Connecté<?php if(isset($_SESSION['Prenom']) && isset($_SESSION['Nom'])){ echo " en tant que " . $_SESSION['Prenom'] . " " . $_SESSION['Nom']; } ?></span>
            </div>

            <ul class="navbar-nav ms-2 mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="deco.php">Déconnexion</a>
                </li>

            </ul>

        </div>

    </div>

</nav>
