<?php
session_start();
require_once 'config/database.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

<!-----Note sécu : Hash password et regex----->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?php echo time(); ?>">
    <title>Connexion</title>
</head>
<body>
    <h2>Votre compte a été créé</h2>
    <h3>Saisissez vos identifiants pour vous connecter</h3>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

        <form method="POST">
            <label>Email :</label><br>
            <input type="email" name="email" required><br><br>

            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">Se connecter</button>
        </form>
    <p>Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
</body>
</html>