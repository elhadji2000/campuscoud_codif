<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
include('../../traitement/fonction.php');
connexionBD();
include('../../traitement/requete.php');

verif_type_mdp_2($_SESSION['username']);

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
    <link rel="stylesheet" href="../../assets/css/main.css">
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h2>CLANDOTER UN ETUDIANT</h2>
            </div>
        </div>
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
			
            <form action="requestClando.php" method="POST" style="display: flex;justify-content: center">
                <div class="row">
                    <div class="col-md-10">
					Renseigner Le Numero de Carte Etudiant de l'Attributaire qui recoit le Clando
                        <input id="numEtudiant"  name="numEtudiant" type="text" class="form-control" placeholder="NUMERO CARTE ATTRIBUTAIRE HOTE" oninput="checkInput()" onblur="validateInput()">
                        <script>
                            var inputElement = document.getElementById('numEtudiant');
                            inputElement.addEventListener('input', function() {
                                var texte = inputElement.value;
                                var texteMajuscule = texte.toUpperCase();
                                inputElement.value = texteMajuscule;
                                var affichageElement = document.getElementById('affichage');
                                affichageElement.textContent = texteMajuscule;
                            });
                        </script>
                    </div>
                    <div class="col-md-2">
                        <button id="submitBtn" type="submit" class="btn btn-primary" disabled>Rechercher</button>
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
                         //print_r($data);
                        if ((isset($_GET['statut']) && $_GET['statut'] == 'Suppleant(e)') || (isset($_GET['statut']) && $_GET['statut'] == 'Forclos(e)')) {
                    ?>
                            <form action="requestClando.php" method="POST">
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input type="text" class="form-control" placeholder="<?= $data['prenoms'] ?>" disabled>
                                        <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                            <input class="form-control" name="id_val" value="<?= $data[23] ?>" style="visibility: hidden;">
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['nom'] ?>" disabled>
                                        <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                            <input class="form-control" name="statut" value="<?= $data['statut'] ?>" style="visibility: hidden;">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black; margin-top:-3%; margin-bottom:1%;">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="STATUT : <?= $_GET['statut'] ?>" disabled style="text-align: center;">
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="<?= $data['nom'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['niveauFormation'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Suppleant(e)') { ?>
                                        <div class="col-md-4 mb-3">
                                            <input class="form-control" placeholder="Type : <?= $data['type'] ?>" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <input class="form-control" placeholder="Motif : <?= $data['motif_manuel'] ?>" disabled>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($_GET['statut']) && $_GET['statut'] != 'Forclos(e)') { ?>
                                        <div class="col-md-4">
                                            <input class="form-control" placeholder="Campus : <?= $data['campus'] ?>" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <input class="form-control" placeholder="Pavillon : <?= $data['pavillon'] ?>" disabled>
                                        </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Validé le : <?= dateFromat($data['dateTime_val']);  ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Lit : <?= $data['lit'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <?php if ($data['etat_id_val'] == 'Migré') { ?>
                                        <div class="col-md-4 mb-3">
                                            <input class="form-control" placeholder="Loger le : <?= dateFromat($data['45']);  ?>" disabled>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php
                            if ((isset($data['etat_id_val']) && $data['etat_id_val'] == 'Migré') || (isset($_GET['statut']) && $_GET['statut'] == 'Forclos(e)')) {
                            ?>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <a class="btn btn-secondary" href="/campuscoud.com/profils/loger/clando.php" type="button">RETOUR</a>
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <?php
                                if (isset($data)) {
                                    $quotaStudentConnect = getQuotaClasse($data['niveauFormation'], $data['sexe'])['COUNT(*)'];
                                    $statutStudentConnect = getOnestudentStatus($quotaStudentConnect, $data['niveauFormation'], $data['sexe'], $data['num_etu']);
                                    $monTitulaire = getOneTitulaireBySuppleant($quotaStudentConnect, $data['niveauFormation'], $data['sexe'], $statutStudentConnect['rang']);
                                    $tableau_data_etudiant = getAllSituation($monTitulaire['num_etu']);
                                ?>
                                    <div class="col-md-8" style="margin-left:17%">
                                        <table class="table table-hover">
                                            <tr class="table" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                                                <td>Quittance</td>
                                                <td>Date Paie</td>
                                                <td>Libelle</td>
                                                <td>Montant</td>
                                            </tr>
                                            <?php while ($row = mysqli_fetch_array($tableau_data_etudiant)) {
                                            ?>
                                                <tr class="table" style="font-size: 14px; background-color: rgba(50, 115, 220, 0.1) ;">
                                                    <td><?= $row['id_paie'] ?></td>
                                                    <td><?= dateFromat($row['dateTime_paie']) ?></td>
                                                    <td><?= $row['libelle'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
									
									
                                    <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4 mb-3">
                                            <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmationModal">LOGER</button>
                                        </div>
                                    </div>
									
									
									
                            <?php }
                            } ?>
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
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn btn-primary">Confirmer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        <?php } else { ?>
                            <form action="requestClando.php" method="POST">
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input type="text" class="form-control" placeholder="<?= $data['prenoms'] ?>" disabled>
                                        <?php if (isset($data[34])) { ?>
                                            <input class="form-control" name="id_paie" value="<?= $data['id_paie'] ?>" style="visibility: hidden;">
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['nom'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="<?= $data['etablissement'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="<?= $data['niveauFormation'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="PAVILLON: <?= $data['pavillon'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="LIT: <?= $data['lit'] ?>" disabled>
                                    </div>
                                </div>
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Validé le : <?= dateFromat($data['dateTime_val']);  ?>" disabled>
                                    </div>

                                    
                                </div>
                                <?php
                                if (isset($data)) {
                                    $tableau_data_etudiant = getAllSituation($data['num_etu']);
                                }
                                if (($data['etat_id_paie'] == 'Migré') || (isset($data['id_paie'])) || ($data['etat_id_paie'] == 'Non migré')) {
                                ?>
								
                                    <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <?php if (isset($data['id_paie'])) { ?>
                                           <div class="col-md-4">
                                        <input class="form-control" placeholder="Payé le : <?= dateFromat($data['dateTime_paie']) ?>" disabled>
                                    </div></div>
									
									  
                                           
                                        <?php } ?>
                                    
									<?php if ($data['etat_id_paie'] == 'Migré') { ?> <br> 
									<div class="row" style="display: flex;justify-content: center;color:black;">
									 <div class="col-md-4">
                                                <input required type="text" class="form-control" placeholder="NUMERO CARTE COUD DU CLANDO" name="num_etu" id="num_etu">
                                                <script>
                                                    var inputElement = document.getElementById('num_etu');
                                                    inputElement.addEventListener('input', function() {
                                                        var texte = inputElement.value;
                                                        var texteMajuscule = texte.toUpperCase();
                                                        inputElement.value = texteMajuscule;
                                                        var affichageElement = document.getElementById('affichage');
                                                        affichageElement.textContent = texteMajuscule;
                                                    });
                                                </script>
                                            </div>
											</div> <br> 
                                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmationModal">LOGER</button>
									<?php }
                                      else										  
										  {  ?>
											<div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <a class="btn btn-secondary" href="/campuscoud.com/profils/loger/clando.php" type="button">RETOUR</a>
                                    </div>
                                </div>  
										<?php  }
									?>
                                <?php
                                }
                                ?>
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>

</html>