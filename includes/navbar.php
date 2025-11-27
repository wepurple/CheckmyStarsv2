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
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/CheckMyStars/admin.php"){echo(' active" aria-current="page');} ?>" href="admin.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/CheckMyStars/formation.php"){echo(' active" aria-current="page');} ?>" href="formation.php">Formation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/CheckMyStars/gallerie.php"){echo(' active" aria-current="page');} ?>" href="gallerie.php">Gallerie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?php if($_SERVER["PHP_SELF"] == "/CheckMyStars/contact.php"){echo(' active" aria-current="page');} ?>" href="contact.php">Me contacter</a>
                    </li>

                </ul>

                <div class="navbar-nav ms-0 mb-2 mb-lg-0">
                    <span class="nav-text">Connecté en tant que <?php if(isset($_SESSION['Login'])){echo($_SESSION['Login']);} ?></span>
                </div>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">


                    <li class="nav-item">
                        <a class="nav-link" href="deco.php">Déconnexion</a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>