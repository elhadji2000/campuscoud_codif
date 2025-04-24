<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
// if (empty($_SESSION['classe'])) {
//     header('location: /campuscoud.com/profils/personnels/niveau.php');
//     exit();
// }
//connexion à la base de données
include('../../traitement/fonction.php');
connexionBD();
// Sélectionnez les options à partir de la base de données avec une pagination
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

// Comptez le nombre total d'options dans la base de données details lits affecter (quotas)

$countIn = 0;
if (isset($_GET['erreurValider'])) {
    $_SESSION['erreurValider'] = $_GET['erreurValider'];
} else {
    $_SESSION['erreurValider'] = '';
}
if (isset($_GET['successValider'])) {
    $_SESSION['successValider'] = $_GET['successValider'];
} else {
    $_SESSION['successValider'] = '';
}
if (isset($_GET['erreurNonTrouver'])) {
    $_SESSION['erreurNonTrouver'] = $_GET['erreurNonTrouver'];
} else {
    $_SESSION['erreurNonTrouver'] = '';
}
if (isset($_GET['erreurForclo'])) {
    $_SESSION['erreurForclo'] = $_GET['erreurForclo'];
} else {
    $_SESSION['erreurForclo'] = '';
}
?>
<!DOCTYPE html>
<html lang="en">

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
	
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h1>LOGER UN ETUDIANT</h1><br>
            </div>
        </div>
        <!-- <span style="color: red;"> <?= $_SESSION['erreurValider']; ?> </span> -->
        <div class="row" style="justify-content: center;">
            <?php if ($_SESSION['erreurValider']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-warning" role="alert">
                        <?= $_SESSION['erreurValider']; ?>
                    </div>
                </div>
            <?php } elseif ($_SESSION['successValider']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['successValider']; ?>
                    </div>
                </div>
            <?php } elseif ($_SESSION['erreurNonTrouver']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['erreurNonTrouver']; ?>
                    </div>
                </div>
            <?php } elseif ($_SESSION['erreurForclo']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-dark" role="alert">
                        <?= $_SESSION['erreurForclo']; ?>
                    </div>
                </div>
            <?php } ?>
            <form action="requestLoger.php" method="POST" style="display: flex;justify-content: center">
                <div class="row">
                    <div class="col-md-10">
                        <input id="numEtudiant" name="numEtudiant" type="text" class="form-control" placeholder="NUMERO CARTE ETUDIANT" oninput="checkInput()" onblur="validateInput()">
                        <script>
                            // Sélectionner l'élément input
                            var inputElement = document.getElementById('numEtudiant');

                            // Ajouter un écouteur d'événement sur l'input pour détecter les changements
                            inputElement.addEventListener('input', function() {
                                // Récupérer la valeur du champ input
                                var texte = inputElement.value;

                                // Convertir le texte en majuscule
                                var texteMajuscule = texte.toUpperCase();

                                // Mettre à jour la valeur du champ input
                                inputElement.value = texteMajuscule;

                                // Récupérer l'élément où afficher le texte
                                var affichageElement = document.getElementById('affichage');

                                // Mettre à jour le texte de l'élément
                                affichageElement.textContent = texteMajuscule;
                            });
                        </script>
                    </div>
                    <div class="col-md-2">
                        <button id="submitBtn" type="submit" class="btn btn-primary" >Rechercher</button>
                    </div>
                </div>
            </form>
        </div><br><br>
        <div class="row">
            <div class="col-md-12">
                <ul class="options">
                    <?php
                    if (isset($_GET['data'])) {
                        $data = $_GET['data'];
                        if ((isset($_GET['statut']) && $_GET['statut'] == 'Suppleant(e)') || (isset($_GET['statut']) && $_GET['statut'] == 'Forclos(e)')) {
							
							$info = info($data['num_etu']);  $id_etu = $info[15]; 
							
                    ?>
                            <form action="requestLoger.php" method="POST">
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input type="text" class="form-control" placeholder="<?= $data['prenoms'] ?>" disabled>
                                        <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                            <input class="form-control" name="id_val" value="<?= $data['id_val'] ?>" style="visibility: hidden;">
											<input class="form-control" name="id_etu" value="<?= $id_etu ?>" style="visibility: hidden;">
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['nom'] ?>" disabled>
                                        <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                            <input class="form-control" name="statut" value="<?= $_GET['statut'] ?>" style="visibility: hidden;">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="FAC: <?= $data['etablissement'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Niveau: <?= $data['niveauFormation'] ?>" disabled>
                                    </div>
                                </div>
                                <!--div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Type : <?= $data['type'] ?>" disabled>
                                    </div-->
                                    <!--div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Motif : <?= $data['motif_manuel'] ?>" disabled>
                                    </div>
                                    <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                        <div class="col-md-4">
                                            <input class="form-control" placeholder="Campus: <?= $data['campus'] ?>" disabled>
                                        </div>
                                </div-->
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Pavillon: <?= $data['pavillon'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Lit: <?= $data['lit'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Validé le : <?= dateFromat($data['dateTime_val']);  ?>" disabled>
                                    </div>
                                    <!--div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Logé le : <?= dateFromat($data['dateTime_loger']);  ?>" disabled>
                                    </div-->
                                </div>
                            <?php } ?>
                            <?php   //echo $data['etat_id_val']; echo $_GET['statut'];
                            if (((isset($data['etat_id_val']) ) && $data['etat_id_val']== "Migré") || (isset($_GET['statut']) && $_GET['statut'] == "Forclos(e)")) {
                            //if (((isset($data['id_val']) ) && $data['id_val']> 0) || (isset($_GET['statut']) && $_GET['statut'] == "Forclos(e)")) {
							?>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <a class="btn btn-secondary" href="/campuscoud.com/profils/loger/loger.php" type="button">RETOUR</a>
                                    </div>
                                </div>
                            <?php
                            } else {
								
								
                            ?>
													
							
							
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmationModal">LOGER</button>
                                    </div>
                                </div>
                            <?php } ?>
							
							
                            <!-- Modal -->
                            <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" -labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Êtes-vous sûr de vouloir effectuer cette action ?
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Boutons pour confirmer ou annuler -->
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Confirmer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        <?php } else { ?>
                            <form action="requestLoger.php" method="POST">
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input type="text" class="form-control" placeholder="<?= $data['prenoms'] ?>" disabled>
										
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['nom'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="FAC: <?= $data['etablissement'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Niveau: <?= $data['niveauFormation'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Numero carte: <?= $data['num_etu'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Campus: <?= $data['campus'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Pavillon: <?= $data['pavillon'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Lit: <?= $data['lit'] ?>" disabled>
                                    </div>
                                </div>
                                
                                <?php
                                if ($data['etat_id_paie'] == 'Migré') {
                                ?>
								<div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
									<?php $date=$data['dateTime_val'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="Validé le <?= $date  ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
									<?php $date=$data['dateTime_paie'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="A payé <?= $data['montant']?>F le <?= $date ?>" disabled>
                                    </div>
                                </div>
                                    <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4 mb-3">
										<?php $date=$data['dateTime_paie'];$date=changedateusfr($date); ?>
                                            <input class="form-control" placeholder="A été logé le <?= $date;  ?> par l'agent <?= $data['username_user'] ?>" disabled>
                                        </div>
                                    </div>
                                    <a class="btn btn-secondary" href="/campuscoud.com/profils/loger/loger.php" type="button">RETOUR</a>
                                <?php
                                } else {  
                                ?>
								
								    <?php if (getIdPay($data['id_etu'])!=0) { ?>		
                               
									  <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
									<?php $date=$data['dateTime_val'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="Validé le <?= $date  ?>" disabled>
                                    </div>
                                    <!--div class="col-md-4">
									<?php $date=$data['dateTime_paie'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="A payé <?php // echo $data['montant']; ?>F le <?php //echo $date; ?>" disabled>
                                    </div-->
                                </div>
								
								
								<?php $tableau_data_etudiant = getAllSituation($data['num_etu']);  ?>
								
									<div class="col-md-8" style="margin-left:17%">
                                    <table align='center' class="table table-hover">
                                        <tr class="table" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                                            <td>Quittance</td>
                                            <td>Date Paie</td>
                                            <td>Libelle</td>
                                            <td>Montant</td>
                                            <!--td>Recu</td>
                                            <td>Restant</td-->
                                            <td>Agent ACP</td>
                                        </tr>
                                        <?php while ($row = mysqli_fetch_array($tableau_data_etudiant)) {
                                        ?>
                                            <tr class="table" style="font-size: 14px; background-color: rgba(50, 115, 220, 0.1) ;">
                                                <td><?= $row['quittance'] ?></td>
                                                <td><?= dateFromat($row['dateTime_paie']) ?></td>
                                                <td><?= $row['libelle'] ?></td>
                                                <td><?= $row['montant'] ?></td>
                                                <!--td><?php //$row['montant_recu'] ?></td>
                                                <td><?php //$row['restant'] ?></td-->
                                                <td><?= $row[2] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
									
                                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmationModal">LOGER</button>
									<?php 
$id_etu=$data['id_etu'];
$id_paie=getIdPay($id_etu); 

										?>
                                        <input class="form-control" name="valide" value="<?= $id_paie ?>" style="visibility: hidden;">
										<input class="form-control" name="id_etu" value="<?= $id_etu ?>" style="visibility: hidden;">
									
									<?php } else { ?>
									
									<div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
									<?php $date=$data['dateTime_val'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="Validé le <?= $date  ?>" disabled>
                                    </div>
									 <div class="col-md-4 mb-3">
									
                                        <input class="form-control" placeholder="Paiement non effectué." disabled>
                                    </div>
                                </div>
								<a class="btn btn-secondary" href="/campuscoud.com/profils/loger/loger.php" type="button">RETOUR</a>
									
									<?php }} ?>
                                <!-- Modal -->
                                <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" -labelledby="confirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Êtes-vous sûr de vouloir effectuer cette action ?
                                            </div>
                                            <div class="modal-footer">
                                                <!-- Boutons pour confirmer ou annuler -->
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Confirmer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    <?php }
                    } ?>
                </ul>
            </div>
        </div>
        <script src="../../assets/js/jquery-3.2.1.min.js"></script>
        <script src="../../assets/js/plugins.js"></script>
        <script src="../../assets/js/main.js"></script>

        <!-- JavaScript de Bootstrap (assurez-vous d'ajuster le chemin si nécessaire) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>

</html>