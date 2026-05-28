<?php
session_start();
require_once 'config/database.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['email'], $_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email, $password]);

        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Identifiants incorrects";
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
    <title>Connexion</title>
</head>
<body>
    <!--------------Bloc header---------->
    <section class="bloc-header">
        <div>
            <a href="index.php">
            <img class="logo" src="assets/img/logo.png">
            </a>            
        </div>        
    </section>

    <!--------Bloc formulaire login------->
    <section class="bloc-inscription">
        <h2>Votre compte a été créé</h2>
        <h3>Saisissez vos identifiants</h3>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
            <form class="form-login" method="POST" autocomplete="off" novalidate>
                <input class="input-form-login" type="text" name="fakeuser" value="" style="display:none">
                <input class="input-form-login" type="password" name="fakepass" value="" style="display:none">
                <input class="input-form-login" type="email" name="email" value="hugo@mail.com" autocomplete="off">
                <input class="input-form-login" type="password" name="password" value="ggggg" autocomplete="new-password">
                <button class="btn-form-inscription" type="submit">Valider</button>
            </form>
        <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
    </section>
</body>
</html>