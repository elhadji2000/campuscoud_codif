<?php session_start();
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
require('../../traitement/fonction.php');
$tableau_data_etudiant = getAllSituation($_SESSION['num_etu']);   
$id_etu=info($_SESSION['num_etu'])['15'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include('../../head.php'); ?>
    <div class="container">
        <?php
        $montantLit = getPrixMensuelLit($_SESSION['num_etu']);
        if (isIndivLitStudent($_SESSION['num_etu']) == "oui") {
            $indiv = 'Lit individuel';
        } else {
            $indiv = 'Lit normal';
        }
        // Recuperation date debut codification du niveauFormation de l'etudiant
        $rdate_debut = getAllDelai("depart", info($_SESSION['num_etu'])[5]);
        $date_debut = dateFromat($rdate_debut['data_limite']);		
		
        // Calcul nombre de mois entre date debut et date systeme
        $nbr_mois_systeme_debut = calcul_nbreMois($date_debut);

        $tableau_situation_paye = getAllSituation($_SESSION['num_etu']);
        $i = 0;
       /* while ($situation = mysqli_fetch_array($tableau_situation_paye)) {
            $libelle[$i] = $situation['libelle'];
            $i++;
            $_montant_restant = $situation['restant'];
        }*/
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


        if (isset($_montant_restant)) {
            global $_a_payer;
            $_a_payer = $_montant_restant;
        } else {
            global $_a_payer;
            $_a_payer = getMontantPaye($_SESSION['num_etu']);
        }
        ?>
        <div class="row" style="margin-left:17%">
            <div class="col-md-3" > <caption>Infos de facturation</caption>
                <table class="table table-hover"> 
                    <tr class="table-primary" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Type de lit : <?= $indiv; ?> </td>
                    </tr>
					<tr class="table-info" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Caution : 5000 F </td>
                    </tr>
                    <tr class="table-secondary" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Mensualite : <?= $montantLit; ?> F</td>
                    </tr>
                    <tr class="table-info" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Nbr de Mois facturés : <?= $nbr_mois_systeme_debut; ?></td>
                    </tr>
					<?php 
					if(verifCaution($id_etu)){
					$totalfacture=($montantLit*$nbr_mois_systeme_debut)+5000; } else {
					$totalfacture=($montantLit*$nbr_mois_systeme_debut);    
					}
					
					?>
                    <tr class="table-primary" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Total facturé : <?= $totalfacture; ?> F</td>
                         </tr>
						 <?php $totalpaye=0; if(getTotalPaye($_SESSION['num_etu'])){$totalpaye=getTotalPaye($_SESSION['num_etu']);}?>
						 <tr class="table-secondary" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Total Payé : <?= $totalpaye; ?> F</td>
                         </tr>
						 
						 <?php $restant=0; $restant=$totalfacture-$totalpaye;
						 if($restant>=0)
						 {?>
							 <tr class="table-info" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Montant à payer : <?= $restant; ?> F</td>
                         </tr>
						 <?php
						 }
						  else
						 {$restant=-$restant;
							 ?>
							 <tr class="table-info" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Montant payé par Avance : <?= $restant; ?> F</td>
                         </tr>
						 <?php
						 }						 
						 ?>

                    <!--<tr class="table-dark" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>SOLDE :</td>
                    </tr> -->
                </table>
            </div>
            <div class="col-md-6">  <caption>Historique des paiements</caption>
                <table class="table table-hover" >
                    <tr class="table-success" style="font-size: 16px; font-weight: 400; background-color:#3777b0;">
                        <td>Qittance</td>
                        <td>Date</td>
                        <td>Libelle</td>
                        <td>Montant</td>
                        <!--td>Recu</td>
                        <td>Restant</td-->
                        <!--td>Agent ACP</td-->
                    </tr>
                    <?php while ($row = mysqli_fetch_array($tableau_data_etudiant)) {
                    ?>
                        <tr class="table-secondary" style="font-size: 14px;">
                            <td><?= $row['quittance'] ?></td>
                            <td><?= dateFromat($row['dateTime_paie']) ?></td>
                            <td><?= $row['libelle'] ?></td>
                            <td><?= $row['montant'] ?></td>
                            <!--td><?php //$row['montant_recu'] ?></td>
                            <td><?php //$row['restant'] ?></td>
                            <td><?php //$row[2] ?></td-->
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
</body>

</html>