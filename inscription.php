<?php
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['email'], $_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Vérification si adresse
        $checkmail = "SELECT * FROM users WHERE email = ?";
        // Préparation de la requête (statement)
        $stmt = $pdo->prepare($checkmail);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo "Vous êtes déjà inscrit";
        } else {
            $query = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email, $password]);
            // Redirection vers la page de connexion
            header("Location: login.php"); 
            exit();
        }
    }
}
?>

<!-----Note sécu : Hash password et regex----->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
    <title>Inscription</title>
</head>
<body>
    <!--------Bloc header---------->
    <section class="bloc-header">
        <div>
            <a href="index.php">
            <img class="logo" src="assets/img/logo.png">
            </a>            
        </div>        
    </section>

    <!--------Bloc inscription------>
    <section class="bloc-inscription">
        <h2>Inscription</h2>
            <form class="form-inscription" method="POST" autocomplete="off" novalidate>
                <input class="input-form-inscription" type="text" name="fakeuser" value="" style="display:none">
                <input class="input-form-inscription" type="password" name="fakepass" value="" style="display:none">
                <input class="input-form-inscription" type="email" name="email" value="hugo@mail.com" autocomplete="off">
                <input class="input-form-inscription" type="password" name="password" value="Mot de passe" autocomplete="new-password">
                <button class="btn-form-inscription" type="submit">Valider</button>
            </form>
        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </section>
</body>
</html>