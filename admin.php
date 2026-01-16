<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur'])){
        if(!$_SESSION['Role']['Administrateur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body  class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
            var_dump($_SESSION);

            //var_dump(hash("sha3-512", "pass123"));

            var_dump(password_hash("pass123", PASSWORD_BCRYPT));

            var_dump(password_verify("pass123", '$2y$10$D3Jo4foDq6DvbNzsHt5RYeX6034SSXd/22dzgn9xDVjA0RNduqxLW'));
            //var_dump(password_algos());
        ?>
    </body>
</html>