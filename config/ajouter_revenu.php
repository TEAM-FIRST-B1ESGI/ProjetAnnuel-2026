
<?php
session_start();
require_once "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $montant = $_POST['montant'] ?? null;
    $source = $_POST['source'] ?? '';
    $date = $_POST['date_revenu'] ?? null;
    $user_id = $_SESSION['user_id'] ?? 1;

    if ($montant && $source && $date) {

        $sql = "INSERT INTO revenus (user_id, montant, date_revenu, source)
                VALUES (:user_id, :montant, :date_revenu, :source)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':user_id' => $user_id,
            ':montant' => $montant,
            ':date_revenu' => $date,
            ':source' => $source
        ]);

        header("Location: ajouter_revenu.php?success=1");
        exit();
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

if (isset($_GET['success'])) {
    $message = "Revenu ajouté avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<link rel="stylesheet" href="assets/css/style.css">-->
    <link rel="stylesheet" href="assets/css/style.css?php echo time(); ?>">
    <title>Accueil</title>
</head>
<body>
    <form method="POST">
        <label>Montant :</label>
        <input type="number" name="montant" required>
        <label>Source :</label>
        <input type="text" name="source" required>
        <label>Date :</label>
        <input type="date" name="date_revenu" required>
        <button type="submit">Ajouter</button>
    </form>
    <script src="formulaire.js"></script>
</body>
</html>
