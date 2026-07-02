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
if (isset($_GET['error'])) {
    $_SESSION['error'] = $_GET['error'];
} else {
    $_SESSION['error'] = '';
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

if (isset($_GET['montantMinAvantLoger'])) {
    $_SESSION['montantMinAvantLoger'] = $_GET['montantMinAvantLoger'];
} else {
    $_SESSION['montantMinAvantLoger'] = '';
}

$test = "false";
if (isset($_GET['data'])) {
    $data = $_GET['data'];
    
    
    // Recuperation du date debut de codification du niveauFormation de l'etudiant
    $date_debut = getAllDelai("depart", info($data['num_etu'])[5]);
    $date_debut = dateFromat($date_debut['data_limite']);  
    
    // Calcul du Montant du
    $montantDu=0;
    $moisFactures=0;
    $montantLit=0;
    $moisFactures = getNbreMois2($data['num_etu']);
    $montantLit = getPrixMensuelLit($data['num_etu']); 
    $montantDu=$moisFactures * $montantLit;

    $montant_reduction = getNbrsReduction($data['num_etu']);
    echo $montant_reduction;

    
    	

    // Nombre de mois deja payer par l'etudiant
    /* 
    $tableau_situation_paye = getAllSituation($data['num_etu']);
    $i = 0;
    while ($situation = mysqli_fetch_array($tableau_situation_paye)) {
        $libelle[$i] = $situation['libelle'];
        $i++;
        //$_montant_restant = $situation['restant'];
    } */
   
   
    /* 
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
    */

   /* 
   if ($nbr_mois_systeme_debut <= $nbr_mois_payer) {
        $test = "true";
        //$_SESSION['a_jour'] = "ETUDIANT A JOUR AUX PAIEMENTS";
    }
    */
    $fac_1   = $data['etablissement'];
$annee_1 = $_SESSION["annee"];  



//DESACTIVEE CAR EMPECHE AU TITULAIRE EN ANNEE N DEVENU SUPPLEANT EN N-1 DE PAYER SES ARRIERES
/*if (codificationAnneeSuivanteExiste($connexion, $fac_1, $annee_1)) {

    echo "
    <script>
        alert('La codification de l\\'année académique choisie a été cloturée. Veuillez vous connecter à l\\'année académique suivante.');
        window.location.href = 'paiement';
    </script>
    ";

    exit();
}*/


//var_dump($data["id_val"]);
//exit;


    
} else {
    unset($_SESSION['a_jour']);
}

//echo $fac_1; exit();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css"
        rel="stylesheet">
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
            <?php } elseif ($_SESSION['error']) { ?>
            <div class="col-md-6">
                <div class="alert alert-danger" role="alert">
                    <?= $_SESSION['error']; ?>
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

            <?php } elseif ($_SESSION['montantMinAvantLoger']) { ?>
            <div class="col-md-6">
                <div class="alert alert-danger" role="alert">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    ATTENTION: <?= $_SESSION['montantMinAvantLoger']; ?>
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
                        <input name="numEtudiant" id="numEtudiant" type="text" class="form-control"
                            placeholder="NUMERO CARTE ETUDIANT">
                        <script>
                        var inputElement = document.getElementById('numEtudiant');
                        if (inputElement) {
                            inputElement.addEventListener('input', function() {
                                var texte = inputElement.value;
                                var texteMajuscule = texte.toUpperCase();
                                inputElement.value = texteMajuscule;
                                var affichageElement = document.getElementById('affichage');
                                if (affichageElement) {
                                    affichageElement.textContent = texteMajuscule;
                                }
                            });
                        }
                        </script>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Rechercher</button>
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
                    <form action="requestPaiement" method="POST" id="form-paiement">
                        <div class="row" style="display: flex;justify-content: center;color:black;">
                            <div class="col-md-4 mb-3">
                                <input type="text" class="form-control" placeholder="Prénom: <?= $data['prenoms'] ?>"
                                    disabled>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" placeholder="Nom: <?= $data['nom'] ?>" disabled>
                            </div>
                        </div>
                        <div class="row" style="display: flex;justify-content: center;color:black;">
                            <div class="col-md-4 mb-3">
                                <input class="form-control" placeholder="Faculté: <?= $data['etablissement'] ?>"
                                    disabled>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" placeholder="Niveau: <?= $data['niveauFormation'] ?>"
                                    disabled>
                            </div>
                        </div><br>
                        <?php
                            if (isset($_GET['statut']) && $_GET['statut'] == 'Forclos(e)') { 
                                $type=$data['type']; 
                                if($data['type']=='auto'){$type='Automatique';} 
                                $motif=$data['motif_manuel']; 
                                if($data['type']=='auto'){$motif='Retard';}
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
                        <div class="row" style="display: flex;justify-content: center;color:black;">
                            <div class="col-md-4 mb-3">
                                <input class="form-control" placeholder="Pavillon: <?= $data['pavillon'] ?>" disabled>
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" placeholder="Lit: <?= $data['lit'] ?>" disabled>
                                <input class="form-control" name="id_etu" value="<?= $data['id_etu'] ?>"
                                    style="visibility: hidden;">
                            </div>
                        </div>


                        <div class="col-md-6 mb-3" style="margin-left:20%; text-align:center;">
                            <?php $date=$data['dateTime_val'];$date=changedateusfr($date); $totalF=0; $montantApayer=0;  ?>
                            <!--input class="form-control" placeholder="Validation faite le <?= $date ?>" disabled-->

                            <?php $arr = getMontantArrierer($connexion, $_SESSION['num_etu']); 
                            $totalF=(int)$arr + 5000 + (int)$montantDu;
                            ?>
                            <p style="font-size: 16px; font-weight: bold; background-color:#FFFF00;padding:10px 10px;">
                                Arrierés(N-1)=<?=  $arr ?>F. Caution=5.000F. <?= $moisFactures ?>
                                Mensualité(s)=<?= $montantDu ?>F </br> Total Facturé=<?= $totalF ?>F .</p>


                        </div>


                        <input type="text" class="form-control" name="montantDu" id="montantDu"
                            value="<?= $montantDu ?>" style="visibility: hidden;">

                        <!-- Tableau des paiements -->
                        <div class="col-md-8" style="margin-left:17%">
                            <table align='center' class="table table-hover">
                                <tr class="table" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                                    <th>Quittance</th>
                                    <th>Date Paie</th>
                                    <th>Libelle</th>
                                    <th>Montant</th>
                                    <th>Agent ACP</th>
                                </tr>
                                <?php
                                $compt = 1;
                                $totalP = 0;
                                if($tableau_data_etudiant) {
                                    while ($row = mysqli_fetch_array($tableau_data_etudiant)) {
                                ?>
                                <tr class="table" style="font-size: 14px; background-color: rgba(50, 115, 220, 0.1) ;">
                                    <td><?= $row['quittance'] ?></td>
                                    <td><?= dateFromat($row['dateTime_paie']) ?></td>
                                    <td><?= $row['libelle'] ?></td>
                                    <td><?= number_format($row['montant'], 0, ',', ' ') ?> FCFA</td>
                                    <td><?= $row[2] ?></td>
                                </tr>
                                <?php 
                                    $compt++; 
                                    $totalP += $row['montant']; 
                                    } 
                                } ?>
                                <tr class="table" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                                    <td align='center' colspan="3">TOTAL PAYE</td>
                                    <td align='center'><?= number_format($totalP, 0, ',', ' ') ?> FCFA
                                    </td>
                                    <td align='center'>-</td>
                                </tr>
                            </table>
                        </div>

                        <?php 
                        $montantApayer = $totalF - $totalP;
                        $resteArrieres = max(0, $arr - ($totalP > 0 ? $totalP : 0));
                        ?>

                        <?php $montantApayer=$totalF-$totalP ;   ?>

                        <div class="col-md-6 mb-3" style="margin-left:20%; text-align:center;">


                        </div>
                        <div class="col-md-6 mb-3" style="margin-left:20%; text-align:center;">
                            <p style="font-size: 16px; font-weight: bold; background-color:#FFFF00;padding:10px 10px;">
                                Total A PAYER= <?= $montantApayer ?>F .</p>
                            <?php if($resteArrieres > 0) { ?>
                            <p style="font-size: 14px; color: red;">
                                <i class="fa fa-exclamation-triangle"></i> Dont restants =
                                <?= number_format($resteArrieres, 0, ',', ' ') ?> FCFA
                            </p>
                            <?php } ?>
                        </div>

                        <!-- Formulaire de paiement normal -->
                        <div class="row" style="display: flex;justify-content: center;color:black;">
                            <div class="col-md-4">
                                <input type="number" name="montant_recu" id="montant_recu" class="form-control"
                                    placeholder="Montant reçu" required>
                            </div>
                            <div class="col-md-4">
                                <select id="libelle" name="libelle[]" multiple class="selectpicker form-control"
                                    data-live-search="true" placeholder="SELECTIONNER ICI ..." required>
                                    <option value="ARRIERES">ARRIERES</option>
                                    <option value="CAUTION">CAUTION</option>
                                    <option value="JANVIER_<?= date('Y') ?>">JANVIER_<?= date('Y') ?></option>
                                    <option value="FEVRIER_<?= date('Y') ?>">FEVRIER_<?= date('Y') ?></option>
                                    <option value="MARS_<?= date('Y') ?>">MARS_<?= date('Y') ?></option>
                                    <option value="AVRIL_<?= date('Y') ?>">AVRIL_<?= date('Y') ?></option>
                                    <option value="MAI_<?= date('Y') ?>">MAI_<?= date('Y') ?></option>
                                    <option value="JUIN_<?= date('Y') ?>">JUIN_<?= date('Y') ?></option>
                                    <option value="JUILLET_<?= date('Y') ?>">JUILLET_<?= date('Y') ?></option>
                                    <option value="OCTOBRE_<?= date('Y') ?>">OCTOBRE_<?= date('Y') ?></option>
                                    <option value="NOVEMBRE_<?= date('Y') ?>">NOVEMBRE_<?= date('Y') ?></option>
                                    <option value="DECEMBRE_<?= date('Y') ?>">DECEMBRE_<?= date('Y') ?></option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-success" type="button" data-toggle="modal"
                            data-target="#confirmationModal">ENCAISSER PAIEMENT NORMAL</button>

                        <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fa fa-info-circle"></i> Aucune affiliation trouvée pour cet étudiant.
                        </div>
                        <?php } ?>

                        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
                            aria-labelledby="confirmationModalLabel" aria-hidden="true">
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
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Confirmer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($data['id_val'])) { ?>
                        <input class="form-control" name="valide" value="<?= $data['id_val'] ?>"
                            style="visibility: hidden;">
                        <?php } ?>
                    </form>

                    <!-- ENCAISSEMENT DES ARRIERES - COMPLETEMENT EN DEHORS DE LA CONDITION if (isset($data['id_aff'])) -->
                    <?php 
                    $black = getBlackListInfo($data["num_etu"], $connexion);
                    
                    if ($black && $black["is_blacklisted"]) { 
                        $montant_arriere = number_format($black["reste_a_payer"], 0, ',', ' ');
                    ?>
                    <div class="alert alert-danger mt-3" style="font-weight:bold; border-left: 5px solid red;">
                        <i class="fa fa-exclamation-circle"></i>
                        <b>ARRIERES DETECTES :</b> Cet étudiant a un arriéré de
                        <b><?= $montant_arriere ?> FCFA</b>
                        pour l'année dernière concernant le Lit: <b><?= $black["lit"] ?></b>.
                    </div>
                    <div style="margin-left:2%">
                        <form action="traitement_arrieres" method="POST" class="mt-4 d-flex justify-content-center">
                            <input type="hidden" name="num_etu" value="<?= htmlspecialchars($data['num_etu']) ?>">
                            <input type="hidden" class="form-control" name="id_etu" value="<?= $data['id_etu'] ?>">
                            <input type="hidden" name="montant_total"
                                value="<?= htmlspecialchars($black['reste_a_payer']) ?>">
                            <?php if (isset($data['valide'])) { ?>
                            <input type="hidden" name="valide" value="<?= $data['valide'] ?>">
                            <?php } ?>

                            <div class="d-flex align-items-center gap-3 bg-light p-3 rounded shadow-sm"
                                style="max-width: 700px; width: 100%; justify-content: center; border: 1px solid #ddd;">
                                <label for="montant_encaisse" class="fw-bold mb-0" style="white-space: nowrap;">
                                    Montant à encaisser pour arriérés (FCFA) :
                                </label>

                                <input type="number" name="montant_encaisse" id="montant_encaisse"
                                    class="form-control form-control-lg"
                                    style="max-width: 250px; min-width: 200px; height: 50px; font-size: 1.1rem;"
                                    required min="1" max="<?= $black['reste_a_payer'] ?>">

                                <button type="submit" class="btn btn-danger btn-lg"
                                    style="height: 50px; font-size: 15px; padding: 5px 5px;">
                                    <i class="fa fa-money"></i> ENCAISSER
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php } ?>

                    <?php } else { ?>
                    <div class="text-center">
                        <p>Aucun étudiant trouvé. Veuillez rechercher un étudiant.</p>
                    </div>
                    <a class="btn btn-secondary" href="/campuscoud.com/profils/paiement/paiement"
                        type="button">RETOUR</a>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <script>
        document.getElementById("form-paiement")?.addEventListener("submit", function(event) {
            const montantDu = parseFloat(document.getElementById("montantDu")?.value || 0);
            const montantRecu = parseFloat(document.getElementById("montant_recu")?.value || 0);

            const premierPaiement = <?= verifPremierPaiement($_SESSION['num_etu']) ? 'true' : 'false' ?>;

            if (montantRecu < montantDu && !premierPaiement) {
                event.preventDefault();

                const confirmContinue = confirm(
                    "Le montant est insuffisant. Voulez-vous quand même continuer ?");
                if (confirmContinue) {
                    this.submit();
                } else {
                    window.location.href = "paiement.php?montantMinAvantLoger=MONTANT INSUFFISANT !!!";
                }
            }
        });
        </script>

        <script src="../../assets/js/jquery-3.2.1.min.js"></script>
        <script src="../../assets/js/plugins.js"></script>
        <script src="../../assets/js/main.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js">
        </script>
</body>
<script src="../../assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>