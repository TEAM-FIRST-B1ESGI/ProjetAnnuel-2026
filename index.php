<?php
session_start();

// US6_Authentification
if (!isset($_SESSION['user_id'])) { //Vérif connection utilisateur
    header("Location: login.php"); //redirection vers la page de connection si null
    exit();
}
// Notification de connexion popup
// Logout à mettre en place

require_once 'config/database.php';

$user_id = $_SESSION['user_id']; // id utilisateur connecté


// US1_Déclarer une dépenses - Insertion d'une dépense

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['date_depense']) &&
        isset($_POST['montant']) &&
        isset($_POST['source'])
    ) {
        $date_depense = $_POST['date_depense'];
        $montant = $_POST['montant'];
        $source = $_POST['source'];

        // Requête SQL
        $query = "INSERT INTO depenses (user_id, date_depense, montant, source)
                VALUES (?, ?, ?, ?)";

        // Préparation
        $stmt = $pdo->prepare($query);

        // Exécution
        $ajoutdep = $stmt->execute([
            $user_id,
            $date_depense,
            $montant,
            $source
        ]);

        if ($ajoutdep) {
            header("Location: index.php");
            exit();
        }
    }
}

// Total des dépenses (pour les statistiques et le reste à vivre)
$stmtTotal = $pdo->prepare("SELECT SUM(montant) AS total_depenses FROM depenses WHERE user_id = ?");
$stmtTotal->execute([$user_id]);
$totalDepenses = $stmtTotal->fetchColumn();

// US2_Historique - Sélection des dernières dépenses
$requeteDepense = "
    SELECT date_depense, source, montant
    FROM depenses
    WHERE user_id = ?
    ORDER BY date_depense DESC
    LIMIT 5
";

$stmtDepenses = $pdo->prepare($requeteDepense);
$stmtDepenses->execute([$user_id]);

$depenses = $stmtDepenses->fetchAll(PDO::FETCH_ASSOC);

// US4_Statistiques - Sur la base du total des dépenses
$stmtTotal = $pdo->prepare("SELECT SUM(montant) FROM depenses WHERE user_id = ?");
$stmtTotal->execute([$user_id]);
$totalDepenses = $stmtTotal->fetchColumn();

// Récupérer la somme par catégorie
$stmtCat = $pdo->prepare("
    SELECT source, SUM(montant) as total
    FROM depenses
    WHERE user_id = ?
    GROUP BY source
");
$stmtCat->execute([$user_id]);
$depensesCat = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Calculer les pourcentages
$depensesPourcentages = [];
foreach ($depensesCat as $depense) {
    $depensesPourcentages[] = [
        'source' => $depense['source'],
        'total' => $depense['total'],
        'pourcentage' => $totalDepenses > 0 ? round(($depense['total'] / $totalDepenses) * 100) : 0
    ];
}

// US3_Déclarer Revenu et US1_Déclarer dépense pour calculer le montant restant à vivre
//Total des revenus
$stmtRevenus = $pdo->prepare("SELECT SUM(montant) AS total_revenus FROM revenus WHERE user_id = ?");
$stmtRevenus->execute([$user_id]);
$totalRevenus = $stmtRevenus->fetchColumn();

// Total des dépenses
$stmtTotalDepenses = $pdo->prepare("SELECT SUM(montant) AS total_depenses FROM depenses WHERE user_id = ?");
$stmtTotalDepenses->execute([$user_id]);
$totalDepenses = $stmtTotalDepenses->fetchColumn();

// Calcul du montant restant à vivre
$resteAVivre = $totalRevenus - $totalDepenses;


//US5_Recevoir des alertes (sur la base du reste à vivre)
$alerte = "";
$classeAlerte = "";

// Alertes selon le solde restant
if ($resteAVivre <= 0) {

    $alerte = "⚠️ Ressource indisponible.";    
    $classeAlerte = "alerte-info";

} elseif ($resteAVivre <= 20) {

    $alerte = "⚠️ Attention, ton solde devient très faible.";
    $classeAlerte = "alerte-prevention";

} else {

    $alerte = "Tout va bien, ton budget est équilibré";
    $classeAlerte = "alerte-succes";
}


// US7_Budget ou seuil personnalisé - Récupération en bdd du montant inséré par l'utilisateur
if (isset($_POST['btn-montant-perso'])) {

    $seuil_alerte = $_POST['budget_perso'];

    $querySeuil = "
        UPDATE users
        SET budget_perso = ?
        WHERE id = ?
    ";

    $stmtSeuil = $pdo->prepare($querySeuil);

    $stmtSeuil->execute([
        $seuil_alerte,
        $user_id
    ]);
}

//Récupration de la donnée pour affichage
$queryUser = "
    SELECT budget_perso
    FROM users
    WHERE id = ?
";

$stmtUser = $pdo->prepare($queryUser);
$stmtUser->execute([$user_id]);

$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$seuilAlerte = $user['budget_perso'];


//US8_Nouvelle information (Notifications)

if ($resteAVivre <= $seuilAlerte) {

    $alerte2 = "⚠️ Ton budget n'est pas encore atteint.";

    $classeAlerte2 = "alerte-prevention";

} else {

    $alerte2 = "🎉 Ton budget est disponible !";

    $classeAlerte2 = "alerte-dispo";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time(); ?>">
    
    <title>Accueil</title>
</head>
<body>
    <!--------Bloc header---------->
    <section class="bloc-header">
        <div>
            <img class="logo" src="assets/img/logo.png">
        </div>    
        <div class="item-revenu">
            <strong>MON REVENU (€)</strong>
            <div class="montant">
                <!--<div class="affichage-montant">2005,00</div>-->
                <button class="btn-modifier" id="btnModifier">Ajouter</button>
            </div>
        </div>
    </section>

    <!--------Bloc point dépenses---------->
    <section class="bloc-point-depenses">
        <div class="bloc-conteneur">            
            <div class="bloc-repartion-depenses">
                <div class="reste-a-vivre">
                    <h4>Reste à vivre</h4>
                    <p><strong><?= number_format($resteAVivre, 2) ?> €</strong></p>
                </div>            
                <div class="total-depenses">
                    <h4>Total des dépenses</h4>                    
                    <p><strong><?= number_format($totalDepenses ?? 0, 2) ?> €</strong></p>                    
                </div>                     
            </div>
        
            <div class="statistiques">
                <h3>Répartition des dépenses</h3>

                <?php foreach ($depensesPourcentages as $dep): ?>
                    <div class="barre-container">
                        <span class="label"><?= htmlspecialchars($dep['source']) ?> (<?= $dep['pourcentage'] ?>%)</span>
                        <div class="barre" style="width: <?= $dep['pourcentage'] ?>%;"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bloc-notifications">
            <h3>ALERTE SYSTEM</h3>
            <div class="bloc-alerte <?= $classeAlerte ?>">
                    
                    <p><?= $alerte ?></p>
            </div>
    <!--------Bloc fonctionnalité épargne restante---------->
            <div class="budget-perso">                
            <h3>Budget personnalisé</h3>
            <p>Budget actuel : <strong><?= number_format($seuilAlerte ?? 0, 2) ?> €</strong></p>
                <form method="POST">
                    <label>Modifier :</label>
                    <input type="number" name="budget_perso" min="0" step="0.01" required>
                    <button type="submit" name="btn-montant-perso">Valider</button>
                </form>
                <div class="notif-budget <?= $classeAlerte2 ?>">
                    <p><?= $alerte2 ?></p>
                </div>
            </div>
        </div>    
    </section>


    <!--------Bloc déclaration dépenses---------->
    <section class="bloc-declarer-depenses">
        <div class="declarer-infos">
            <h2>DÉCLARER UNE DÉPENSE</h2>
            <p>Utilise ce formulaire pour ajouter une nouvelle transaction.</br>
            Toutes les valeurs sont calculées après validation</p>
        </div>
        <div class="form-depenses">
            <form class="form-inputs" method="POST">
                <div class="form-item-conteneur">
                    <div class="item-form-depenses">
                        <label>DATE :</label>
                        <input class="input-form-depenses" type="date" name="date_depense" required>
                    </div>
                    <div class="item-form-depenses">
                        <label>MONTANT(€) :</label>
                        <input class="input-form-depenses" type="number" name="montant" required>
                    </div>
                    <div class="item-form-depenses">
                        <label>CATÉGORIE :</label>
                        <input class="input-form-depenses" type="text" name="source" required>
                    </div>
                </div>                    
                <button class="btn-declarer" type="submit">Valider et calculer</button>                
            </form>                        
    </section>

    <!--------Bloc historique---------->
    <section class="bloc-historique-depenses">
        <div class="historique-depenses">
            <h3>Historique des dépenses</h3>
            <?php if (!empty($depenses)): ?>
                <table>
                    <thead>
                        <tr>
                            <th><h4>Date</h4></th>
                            <th><h4>Source</h4></th>
                            <th><h4>Montant (€)</h4></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($depenses as $depense): ?>
                            <tr>
                                <td><?= htmlspecialchars($depense['date_depense']) ?></td>
                                <td><?= htmlspecialchars($depense['source']) ?></td>
                                <td><?= number_format($depense['montant'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune dépense enregistrée.</p>
            <?php endif; ?>
        </div>
    </section>
    <script src="assets/js/main.js"></script>
</body>
</html>
