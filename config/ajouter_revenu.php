<?php
session_start();
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $montant = $_POST['montant'] ?? null;
    $user_id = $_SESSION['user_id'] ?? 1;

    if ($montant) {

        $sql = "INSERT INTO revenus (user_id, montant)
                VALUES (:user_id, :montant)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id,
            ':montant' => $montant,
        ]);

        header("Location: ../index.php");
        exit();
    } 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css?php echo time(); ?>">
    <title>Accueil</title>
</head>
<body>
    <!--------Bloc header---------->
    <section class="bloc-header">
        <div>
            <a href="../index.php">
            <img class="logo" src="../assets/img/logo.png">
            </a>            
        </div>        
    </section>
    <form method="POST">
        <label>Montant :</label>
        <input type="number" name="montant" required>
        <button type="submit">Ajouter</button>
    </form>
    <script src="formulaire.js"></script>
</body>
</html>
