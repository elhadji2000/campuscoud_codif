<?php session_start();  

include('../../traitement/fonction.php');

$pavillon = $_SESSION['pavillon'];

$data = getTitulaireByPavillon($pavillon, $connexion);

// Regrouper les lits par chambre
$chambres = [];
foreach ($data as $row) {
    $chambres[$row['chambre']][] = $row;
}

verif_type_mdp_2($_SESSION['username']);
?>
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
            <br><h2><u>LISTE DES RESIDENTS DU PAVILLON <?= htmlspecialchars($pavillon) ?></u></h2><br>
         
        </center>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Chambre</th>
                        <th scope="col">Lit</th>
                        <th scope="col">Num Etudiant</th>
                        <th scope="col">Nom Titulaire</th>
                        <th scope="col">Voisins</th>
                    </tr>
                </thead>


<tbody>
                <?php if (!empty($chambres)) : ?>
                <?php $index = 1; ?>
                <?php foreach ($chambres as $chambre => $lits) : ?>
                <tr>
                    <td rowspan="<?= count($lits) ?>"><?= $index++ ?></td>
                    <td rowspan="<?= count($lits) ?>"><?= htmlspecialchars($chambre) ?></td>
                    <td><?= htmlspecialchars($lits[0]['lit']) ?></td>
                    <td><?= htmlspecialchars($lits[0]['num_etu']) ?></td>
                    <td><?= htmlspecialchars($lits[0]['titulaire_nom']) ?></td>
                    <td>
                        <a
                            href="lit.php?id_paie=<?= htmlspecialchars($lits[0]['id_paie'], ENT_QUOTES) ?>&lit=<?= htmlspecialchars($lits[0]['lit'], ENT_QUOTES) ?>">Voir
                            détails</a>
                    </td>
                </tr>
                <?php foreach (array_slice($lits, 1) as $lit) : ?>
                <tr>
                    <td><?= htmlspecialchars($lit['lit']) ?></td>
                    <td><?= htmlspecialchars($lit['num_etu']) ?></td>
                    <td><?= htmlspecialchars($lit['titulaire_nom']) ?></td>
                    <td>
                        <a
                            href="lit.php?id_paie=<?= htmlspecialchars($lit['id_paie'], ENT_QUOTES) ?>&lit=<?= htmlspecialchars($lit['lit'], ENT_QUOTES) ?>">Voir
                            détails</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endforeach; ?>
                <?php else : ?>
                <tr>
                    <td colspan="6">Aucun étudiant trouvé pour ce pavillon.</td>
                </tr>
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