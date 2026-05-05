<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!-----Notification de connexion popup-------->
<!-----Logout ?------->

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
    <!--------Bloc nav---------->
    <div class="header">
        <div>
            <img class="logo" src="assets/img/logo.png">
        </div>    
        <div class="item-revenu">
            <strong>MON REVENU (€)</strong>
            <div class="montant">
                <div class="affichage-montant">
                    2005,00
                </div>
                <button class="btn-modifier">Modifier</button>
            </div>
        </div>
    </div>

    <!--------Bloc point dépense---------->
    <section>
        <div class="bloc-repartion-depenses">
            <div>Reste à vivre<div>
            <div>Total dépenses<div>
            <div>répartition</div>
        </div>
        <div class="bloc-alertes">Alertes</div>
    <!--------Bloc fonctionnalité épargne restante---------->
    </section>

    <!--------Bloc intéractif décla dépenses---------->
    <section>
        <h2>Déclarer une dépense</h2>
        <p>Utilise ce formulaire pour ajouter une nouvelle transaction. Toutes les valeurs sont calculées après validation</p>
        <div class="calculateur">
            <form method="POST">
                <label>Date :</label>
                <input type="date" name="date_revenu" required>
                <label>Montant :</label>
                <input type="number" name="montant" required>
                <label>Catégorie :</label>
                <input type="text" name="source" required>                
                <button type="submit">Valider et calculer</button>
            </form>            
        </div>
    </section>

    <!--------Bloc historique---------->
    <section>
        <h2>Historique des dépenses</h2>
    </section>
</body>
</html>
