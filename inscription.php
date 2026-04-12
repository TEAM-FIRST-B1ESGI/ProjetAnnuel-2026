<?php
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

<!-----Note sécu : Hash password et regex----->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <link rel="stylesheet" href="assets/css/style.css?php echo time(); ?>">
    <title>Inscription</title>
</head>
<body>

<h2>Inscription</h2>
    <form method="POST">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>
        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</body>
</html>