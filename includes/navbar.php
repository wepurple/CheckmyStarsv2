<link href="/CheckMyStars/bootstrap 5.3/css/bootstrap.min.css" rel="stylesheet">
<script src="/CheckMyStars/bootstrap 5.3/js/bootstrap.min.js"></script>

    <nav class=" sticky-top navbar navbar-expand-lg bg-primary nav-underline" data-bs-theme="dark">

        <div class="container-fluid">

            <a class="navbar-brand" href="/index/">
                <img src="/CheckMyStars/pictures/logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                CheckMyStars
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/checkmystars/admin.php"){echo(' active" aria-current="page');} ?>" href="admin.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/checkmystars/gestion_inspecteurs.php"){echo(' active" aria-current="page');} ?>" href="gestion_inspecteurs.php">Gestion des inscpecteurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/checkmystars/gallerie.php"){echo(' active" aria-current="page');} ?>" href="gallerie.php">Gallerie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/checkmystars/contact.php"){echo(' active" aria-current="page');} ?>" href="contact.php">Me contacter</a>
                    </li>

                </ul>

                <div class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <span class="nav-text">Connecté<?php if(isset($_SESSION['Prenom']) && isset($_SESSION['Nom'])){ echo(" en tant que " . $_SESSION['Prenom'] . " " . $_SESSION['Nom']); } ?></span>
                </div>

                <ul class="navbar-nav ms-2 mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="deco.php">Déconnexion</a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>