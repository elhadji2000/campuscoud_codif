<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');
connexionBD();

verif_type_mdp_2($_SESSION['username']);

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
if (isset($_GET['erreurMois'])) {
    $_SESSION['erreurMois'] = $_GET['erreurMois'];
} else {
    $_SESSION['erreurMois'] = '';
}

$test = "false";
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    // Recuperation du date debut de codification du niveauFormation de l'etudiant
    $date_debut = getAllDelai("depart", info($data['num_etu'])[5]);
    $date_debut = dateFromat($date_debut['data_limite']);
    // Calcul nombre de mois entre date debut et date systeme
    $nbr_mois_systeme_debut = calcul_nbreMois($date_debut);

    // Nombre de mois deja payer par l'etudiant
    $tableau_situation_paye = getAllSituation($data['num_etu']);
    $i = 0;
    while ($situation = mysqli_fetch_array($tableau_situation_paye)) {
        $libelle[$i] = $situation['libelle'];
        $i++;
        //$_montant_restant = $situation['restant'];
    }
    if (isset($libelle)) {
        global $nbr_mois_impaye;
        $chaine_libelle = json_encode($libelle);
        $chaine_libelle = str_replace(['[', ']', '"', 'CAUTION'], ' ', $chaine_libelle);
        $nbr_mois_payer = countWords($chaine_libelle);
        $nbr_mois_impaye = $nbr_mois_systeme_debut - $nbr_mois_payer;
    } else {
        global $nbr_mois_impaye;
        $nbr_mois_payer = 0;
        $nbr_mois_impaye = $nbr_mois_systeme_debut;
    }

    /*if (isset($_montant_restant)) {
        global $_a_payer;
        $_a_payer = $_montant_restant;
    } else {
        global $_a_payer;
        $_a_payer = getMontantPaye($data['num_etu']);
    }*/

    if ($nbr_mois_systeme_debut <= $nbr_mois_payer) {
        $test = "true";
        //$_SESSION['a_jour'] = "ETUDIANT A JOUR AUX PAIEMENTS";
    }
} else {
    unset($_SESSION['a_jour']);
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

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css" rel="stylesheet">
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <h2>PAIEMENTS DES LITS</h2>
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
            <?php } elseif ($_SESSION['erreurMois']) { ?>
                <div class="col-md-6">
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        ATTENTION: <?= $_SESSION['erreurMois']; ?> A DEJA ETE PAYE !
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </div>
                </div>
            <?php } elseif (isset($_SESSION['a_jour'])) { ?>
                <div class="col-md-6">
                    <div class="alert alert-info" role="alert">
                        <?= $_SESSION['a_jour']; ?>
                    </div>
                </div>
            <?php } ?>
            <form action="requestPaiement" method="POST" style="display: flex;justify-content: center">
                <div class="row">
                    <div class="col-md-10">
                        <input id="numEtudiant" name="numEtudiant" type="text" class="form-control" placeholder="NUMERO CARTE ETUDIANT" oninput="checkInput()" onblur="validateInput()">
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
                    if (isset($data)) {
                        $tableau_data_etudiant = getAllSituation($data['num_etu']);
                    ?>
                        <form action="requestPaiement" method="POST">
                            <div class="row" style="display: flex;justify-content: center;color:black;">
                                <div class="col-md-4 mb-3">
                                    <input type="text" class="form-control" placeholder="Prénom: <?= $data['prenoms'] ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" placeholder="Nom: <?= $data['nom'] ?>" disabled>
                                </div>
                            </div>
                            <div class="row" style="display: flex;justify-content: center;color:black;">
                                <div class="col-md-4 mb-3">
                                    <input class="form-control" placeholder="Faculté: <?= $data['etablissement'] ?>" disabled>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control" placeholder="Niveau: <?= $data['niveauFormation'] ?>" disabled>
                                </div>
                            </div><br> 
                            <?php
                            if (isset($_GET['statut']) && $_GET['statut'] == 'Forclos(e)') { ?>
							
							
							<?php $type=$data['type'];          if($data['type']=='auto'){$type='Automatique';} 
								      $motif=$data['motif_manuel']; if($data['type']=='auto'){$motif='Retard';} 								
								?>				
							
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Type : <?= $type ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Motif :<?= $motif ?>" disabled>
                                    </div>
                                </div><br>
								
                            <?php } ?>
                            <?php if (isset($data['id_aff'])) { ?>
                                <!-- <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="CNI: <?= $data['numIdentite'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Campus: <?= $data['campus'] ?>" disabled>
                                    </div>
                                </div><br-->
                                <div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Pavillon: <?= $data['pavillon'] ?>" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control" placeholder="Lit: <?= $data['lit'] ?>" disabled>
                                        <input class="form-control" name="id_etu" value="<?= $data['id_etu'] ?>" style="visibility: hidden;">
                                    </div>
                                </div>
                                <!--div class="row" style="display: flex;justify-content: center;color:black;">
                                    <div class="col-md-4 mb-3">
                                        <input class="form-control" placeholder="Date Choix Lit: <?= dateFromat($data['dateTime_aff']) ?>" disabled>
                                    </div-->
                                    <div class="col-md-4 mb-3" style="margin-left:30%">
									<?php $date=$data['dateTime_val'];$date=changedateusfr($date); ?>
                                        <input class="form-control" placeholder="Validation faite le <?= $date ?>" disabled>
                                    </div>
                                <!--/div> -->
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
                                <?php

                               // if ($test == "true") {
                                    // $_SESSION['a_jour'] = "ETUDIANT A JOUR AUX PAIEMENTS"
                                ?>
                                    <!-- <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4 mb-3">
                                            <input type="number" class="form-control" name="montant" placeholder="Montant: <?= $data['montant']; ?> Fr cfa" disabled>
                                        </div>
                                        <div class="col-md-4 mb-3"><?php $libelle = str_replace(['[', ']', '"'], '', $data['libelle']); ?>
                                            <textarea class="form-control" placeholder="Libelle: <?= $libelle; ?>" name="libelle" disabled></textarea>
                                        </div>
                                    </div>
                                    <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4 mb-3">
                                            <input class="form-control" placeholder="Date Paiement: <?= dateFromat($data['dateTime_paie']) ?>" disabled>
                                        </div>
                                    </div> -->
                                    <!--a class="btn btn-secondary" href="/campuscoud.com/profils/paiement/paiement" type="button">RETOUR</a-->
                                <?php
                              //  } else {
                                ?>
                                    <!--div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="montant" disabled placeholder="Montant à payer : <?php //$_a_payer; ?> fr cfa">
                                            <input type="number" class="form-control" name="montant" value="<?php //$_a_payer; ?>" style="visibility: hidden;">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" disabled placeholder="Mois impayer : <?php //$nbr_mois_impaye; ?> mois">
                                        </div>
                                    </div-->
                                    <div class="row" style="display: flex;justify-content: center;color:black;">
                                        <div class="col-md-4">
                                            <input type="number" name="montant_recu" class="form-control" placeholder="Montant recu"    required>
                                        </div>
                                        <div class="col-md-4">
                                            <select id="libelle" name="libelle[]" multiple class="selectpicker form-control" data-live-search="true" placeholder="SELECTIONNER ICI ..." required>
                                                <option value="CAUTION">CAUTION</option>
												<option value="OCTOBRE">OCTOBRE</option>
                                                <option value="NOVEMBRE">NOVEMBRE</option>
                                                <option value="DECEMBRE">DECEMBRE</option>
                                                <option value="JANVIER">JANVIER</option>
                                                <option value="FEVRIER">FEVRIER</option>
                                                <option value="MARS">MARS</option>
                                                <option value="AVRIL">AVRIL</option>
                                                <option value="MAI">MAI</option>
                                                <option value="JUIN">JUIN</option>
                                                <option value="JUILLET">JUILLET</option>
                                                <option value="AOUT">AOUT</option>
                                                <option value="SEPTEMBRE">SEPTEMBRE</option>
                                                
                                            </select>
                                        </div>
                                    </div>
									<br>
                                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmationModal">ENCAISSER</button>
                                <?php //}
                            } else { ?>
                                <a class="btn btn-secondary" href="/campuscoud.com/profils/paiement/paiement" type="button">RETOUR</a>
                            <?php } ?>
                            <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
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
                            <?php if (isset($data['id_val'])) { ?>
                                <input class="form-control" name="valide" value="<?= $data['id_val'] ?>" style="visibility: hidden;">
                            <?php } ?>
                        </form>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <script src="../../assets/js/jquery-3.2.1.min.js"></script>
        <script src="../../assets/js/plugins.js"></script>
        <script src="../../assets/js/main.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Bootstrap Select JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
</body>
<script src="../../assets/js/script.js"></script>
</body>