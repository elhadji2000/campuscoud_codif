<?php session_start();  

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$pavillonDonne = $_SESSION['pavillon'];
$result = getPaymentDetailsByPavillon($pavillonDonne, $connexion);

if (isset($_GET['alert']) && $_GET['alert'] == 'success') {
    echo "<script type='text/javascript'>alert('Rappel envoyé avec succès!');</script>";
}
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['etudiant_id'])) {
    $etudiant_id = intval($_GET['etudiant_id']); // Sanitize the input
    
    // Appeler la fonction de rappel
    sms_recouvrement($etudiant_id, $pavillonDonne);	
	
    rappel("Rappel envoyé avec succès pour l'étudiant ID: $etudiant_id", $etudiant_id, $connexion);
    
    // Ajouter un message de confirmation (utilisation de session ou redirection)
    $message = "Rappel envoyé avec succès pour l'étudiant ID: $etudiant_id";

    // Rediriger vers la même page sans les paramètres GET
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=true");
    exit();
}




 //include('../../head.php'); ?>


<script>
function confirmRappel(form) {
    const confirmation = confirm("Le SMS de rappel ne peut etre envoyé qu'une seule fois par mois.  Etes-vous sûr de vouloir envoyer un SMS de rappel à cet étudiant ?");
    if (confirmation) {
        return true; // Permet de soumettre le formulaire
    } else {
        return false; // Empêche la soumission du formulaire
    }
}
</script>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <!-- CSS================================================== -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php include('../../head.php'); ?>
</head>

<body>
    <!--header class="s-header">
        <div class="header-logo">
            <a class="site-logo" href="#"><img src="/campuscoud.com/assets/images/logo.png" alt="Homepage" /></a>
            CAMPUSCOUD
        </div>
		
		<?php //if (($_SESSION['profil'] == 'chef_residence')) { ?>
		<nav class="header-nav-wrap">
      <ul class="header-nav">
          <li class="nav-item active">
            <a class="nav-link" href="recouvr" title="Suivi recouvrement">Recouvrement</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="pavillon" title="Voir occupants">Pavillon</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="loger" title="Loger etudiant">Loger_un_etudiant</a>
          </li>
		  <li class="nav-item">
          <a class="nav-link" href="/campuscoud.com/" title="Déconnexion"><i class="fa fa-sign-out" aria-hidden="true"></i> Déconnexion</a>
        </li>
		        </ul>
    </nav>
        <?php //} ?>
		
    </header-->
    <!--section id="homedesigne" class="s-homedesigne">
        <p class="lead">Bienvenue dans l'espace de connexion !</p>
    </section-->
    <div class="container-fluid">
        <center>
        <br>   <u> <h2>SUIVI DES RECOUVREMENTS DU PAVILLON <?= htmlspecialchars($pavillonDonne) ?></h2></u><br>
        </center>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Chambre</th>
                        <th scope="col">Lit</th>
                        <th scope="col">Carte Etudiant</th>
                        <th scope="col">Prenom et Nom</th>
                        <th scope="col">Total Facturé</th>
                        <th scope="col">Total Payé</th>
                        <th scope="col">Montant Dû</th>
                        <th scope="col">SMS de Rappel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    $currentChambre = null;
                    $litCount = 0;
                    $chambreRows = [];

                    foreach ($result as $row):
                        if ($currentChambre !== $row['chambre']):
                            if ($currentChambre !== null):
                                ?>
                                <tr>
                                    <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                                    <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                                    <?php foreach ($chambreRows as $i => $litRow): ?>
                                        <?php 
                                        // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                        $resteAPayer = (int)$litRow['reste_a_payer'];
                                        $canRemind = false;

                                        // Vérification du montant restant à payer et de la date du dernier rappel
                                        if ($resteAPayer >= 6000) {
                                            if (!empty($litRow['rappel_envoye'])) {
                                                $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                                $currentDate = new DateTime();
                                                $interval = $lastReminderDate->diff($currentDate);

                                                // Si le dernier rappel a plus de 1 mois, autoriser le rappel
                                                if ($interval->m >= 1) {
                                                    $canRemind = true;
                                                }
                                            } else {
                                                $canRemind = true;  // Si aucun rappel n'a été envoyé
                                            }
                                        }
                                        ?>
                                        <?php if ($i > 0): ?>
                                            <tr>
                                        <?php endif; ?>
                                            <td><?= htmlspecialchars($litRow['lit']) ?></td>
                                            <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                                            <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                                            <td><?= number_format($litRow['montant_facture'], 0, ',', ' ') ?> F CFA</td>
											
											 <td>
                            <a
                                href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                                <?= number_format($litRow['montant_paye'], 0, ',', ' ') ?> F CFA
                            </a>
                        </td>
										
                                            <td><?= number_format($litRow['reste_a_payer'], 0, ',', ' ') ?> F CFA</td>
                                            <td>
                                             <form method="GET" action="" onsubmit="return confirmRappel(this);">
    <input type="hidden" name="etudiant_id" value="<?= $litRow['etudiant_id'] ?>">
    <button type="submit" class="btn <?= $canRemind ? 'btn-success' : 'btn-secondary' ?>"
        <?= $canRemind ? '' : 'disabled' ?>>ENVOYER</button>
</form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php
                                $counter++;
                            endif;

                            $currentChambre = $row['chambre'];
                            $litCount = 1;
                            $chambreRows = [$row];
                        else:
                            $litCount++;
                            $chambreRows[] = $row;
                        endif;
                    endforeach;

                    if ($currentChambre !== null):
                        ?>
                        <tr>
                            <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                            <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                            <?php foreach ($chambreRows as $i => $litRow): ?>
                                <?php 
                                // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                $resteAPayer = (int)$litRow['reste_a_payer'];
                                $canRemind = false;

                                if ($resteAPayer >= 6000) {
                                    if (!empty($litRow['rappel_envoye'])) {
                                        $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                        $currentDate = new DateTime();
                                        $interval = $lastReminderDate->diff($currentDate);

                                        if ($interval->m >= 1) {
                                            $canRemind = true;
                                        }
                                    } else {
                                        $canRemind = true; // Si aucun rappel n'a été envoyé
                                    }
                                }
                                ?>
                                <?php if ($i > 0): ?>
                                    <tr>
                                <?php endif; ?>
                                    <td><?= htmlspecialchars($litRow['lit']) ?></td>
                                    <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                                    <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                                    <td><?= number_format($litRow['montant_facture'], 0, ',', ' ') ?> F CFA</td>
                                     <td>
                            <a
                                href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                                <?= number_format($litRow['montant_paye'], 0, ',', ' ') ?> F CFA
                            </a>
                        </td>
                                    <td><?= number_format($litRow['reste_a_payer'], 0, ',', ' ') ?> F CFA</td>
                                    <td>
                                      <form method="GET" action="" onsubmit="return confirmRappel(this);">
    <input type="hidden" name="etudiant_id" value="<?= $litRow['etudiant_id'] ?>">
    <button type="submit" class="btn <?= $canRemind ? 'btn-success' : 'btn-secondary' ?>"
        <?= $canRemind ? '' : 'disabled' ?>>ENVOYER</button>
</form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </center>
    </div>
    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>
