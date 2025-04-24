<?php

//connexion à la base de données
include( '../../traitement/fonction.php' );
connexionBD();
// Sélectionnez les options à partir de la base de données avec une pagination
//include( '../../traitement/requete.php' );
session_start();
if ( isset( $_SESSION[ 'data' ] ) ) {
    $data = $_SESSION[ 'data' ];
    unset( $_SESSION[ 'data' ] );
    // Nettoyer après utilisation
} else {
    $date_debut = '';
    $date_fin = '';
    $username = '';
    $libelle = '';
    $data = getPaiementWithDateInterval_2( $date_debut, $date_fin, $username, $libelle );
}
// Stocker les données retournées dans des variables séparées
$tabPaiment = $data[ 'data' ];
// Tableau des paiements
$totalMontant = $data[ 'totalMontant' ];

// Calculer le montant total
$Total = calculateMontantTotal();
// Calculer la somme totale pour le libellé 'Caution'
$cautionSum = calculateCautionSum();
$mens = ( $Total - $cautionSum );
// Somme totale des montants
$regisseurs = getAllRegisseurs($connexion);
?>
<!DOCTYPE html>
<html lang='fr'>

<head>
    <meta charset='utf-8' />
    <title>GESCOUD</title>
    <meta name='viewport' content='width=device-width, initial-scale=1' />
    <link rel='stylesheet' href='../../assets/css/base.css' />
    <link rel='stylesheet' href='../../assets/css/vendor.css' />
    <link rel='stylesheet' href='../../assets/css/main.css' />
    <link rel='stylesheet' href='../../assets/css/login.css' />
    <link rel='stylesheet' href='../../assets/bootstrap/css/bootstrap.min.css'>
    <!-- script ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  ===  == -->
    <script src='../../assets/js/modernizr.js'></script>
    <script src='../../assets/js/pace.min.js'></script>
    <style>
    .custom-height {
        height: 4.5rem;
        /* Ajuste la hauteur des inputs et select */
        font-size: 15px;
    }
    </style>
    <style>
    td,
    th,
    tr {
        font-size: 15px;
        text-align: center;
        vertical-align: middle;
    }
    </style>
<?php include('../../head.php'); ?>    
</head>

<body>


    <div class="container">
        <div class="row ">
            <div class="text-center">
                <h1>ETAT DES ENCAISSEMENTS PERIODIQUES</h1><br>
            </div>
        </div>
        <br><br> <br>

        <!-- Interval date -->
        <div class="container">
            <form action="requestEtatPaiement_cs.php" method="POST" onsubmit="return validateForm()">

                <div class="row g-3 align-items-center justify-content-center">
                    <!-- Colonne pour la date de début -->
                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">Date début :</label>
                        <input type="date" id="date_debut" name="date_debut" class="form-control custom-height" />
                    </div>
                    <!-- Colonne pour la date de fin -->
                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date fin :</label>
                        <input type="date" id="date_fin" name="date_fin" class="form-control custom-height" />
                    </div>
                    <!-- Colonne pour le sélecteur -->
                    <div class="col-md-3">
                        <label for="regisseur" class="form-label">Regisseur :</label>
                        <select class="form-select custom-height" name="regisseur" id="regisseur">
                            <option value="">Sélectionnez un regisseur</option>
                            <?php
            // Boucle pour ajouter les options
            foreach ($regisseurs as $regisseur) {
                echo "<option value='" . htmlspecialchars($regisseur) . "'>" . htmlspecialchars($regisseur) . "</option>";
            }
            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="libelle" class="form-label">Paiement :</label>
                        <select class="form-select custom-height" name="libelle" id="libelle">
                            <option value="">Sélectionnez un libelle</option>
                            <option value="CAUTION">CAUTION</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-3 justify-content-center">
                    <!-- Boutons -->
                    <div class="col-md-2">
                        <button id="submitBtn" name="rechercher" type="submit" class="btn btn-success w-100">
                            Rechercher
                        </button>
                    </div>
                    <!-- div class="col-md-2">
                        <button id="printBtn" name="imprimer" type="submit" class="btn btn-info w-100">
                            Imprimer
                        </button>
                    </div -->
                </div>
            </form>

            <script>
            function validateForm() {
                const dateDebut = document.getElementById('date_debut').value;
                const dateFin = document.getElementById('date_fin').value;

                // Date minimale fixée à 01/01/2025
                const dateMin = new Date('2025-01-01');

                // Vérification : Si la date de début est renseignée
                if (dateDebut) {
                    const debut = new Date(dateDebut);
                    if (debut < dateMin) {
                        alert("La date de début doit être superieure au 31/12/2024.");
                        return false;
                    }
                }

                // Vérification : Si la date de fin est renseignée
                if (dateFin) {
                    const fin = new Date(dateFin);

                    // Si dateDebut n'est pas renseignée, vérifier si dateFin >= dateMin
                    if (!dateDebut && fin < dateMin) {
                        alert(
                            "Si la date de début n'est pas renseignée, la date de fin doit être le 01/01/2025 ou après."
                            );
                        return false;
                    }

                    // Si dateDebut est renseignée, vérifier si dateFin >= dateDebut
                    if (dateDebut) {
                        const debut = new Date(dateDebut);
                        if (fin < debut) {
                            alert("La date de fin doit être postérieure ou égale à la date de début.");
                            return false;
                        }
                    }
                }

                return true; // Le formulaire est valide
            }
            </script>

            <div class="row g-3 align-items-center justify-content-center">
                <!-- Boutons -->
                <div class="col-md-3">
                    <h2>Total Filtre:</h2>
                </div>
                <div class="col-md-3">
                    <input style="text-align:center;font-size:15px;" readonly type="text"
                        value="<?php echo number_format($totalMontant, 0, ', ', ' ') . ' F CFA'; ?>"
                        class="form-control custom-height" />
                </div>
            </div>
            <div class="row g-3 align-items-center justify-content-center">
                <!-- Colonne pour la date de début -->
                <div class="col-md-3">
                    <label for="date_debut" class="form-label">Montant Total :</label>
                    <input type="text" style="text-align:center;" readonly
                        value="<?php echo number_format($Total, 0, ', ', ' ') . ' F CFA'; ?>"
                        class="form-control custom-height" />
                </div>
                <!-- Colonne pour la date de fin -->
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Total Caution :</label>
                    <input type="text" style="text-align:center;" readonly
                        value="<?php echo number_format($cautionSum, 0, ', ', ' ') . ' F CFA'; ?>"
                        class="form-control custom-height" />
                </div>
                <div class="col-md-3">
                    <label for="date_fin" class="form-label">Total Loyer :</label>
                    <input type="text" style="text-align:center;" readonly
                        value="<?php echo number_format($mens, 0, ', ', ' ') . ' F CFA'; ?>"
                        class="form-control custom-height" />
                </div>
            </div>

        </div>


        <br><br>

        <!-- table -->
        <div class="row">
            <div class="col-md-12">
                <div class="text-center ">
                    <?php 
                   /*  if(isset($_SESSION['debut']) && $_SESSION['fin']){
                    $timeD= strtotime($_SESSION['debut']);
                        $timeF= strtotime($_SESSION['fin']);
						$username=$_SESSION['username'];
                        $dateD = date('d-m-Y',$timeD);
                        $dateF = date('d-m-Y',$timeF);
                        echo "Date debut: ".$dateD." et Date fin: ".$dateF;
						$dateDen = date('Y-m-d',$timeD);
                        $dateFen = date('Y-m-d',$timeF);
						$donnees=getPaiementWithDateInterval($dateDen, $dateFen,$username);
                    } */
                    ?>
                </div>
            </div>
        </div>
        <div>
            <table class="table table-hover">
                <tr class="table-secondary" style="font-size: 16px; font-weight: 400;">
                    <th>Num_Quittance</th>
                    <th>Date_paye</th>
                    <th>Libelle</th>
                    <th>Num_Carte</th>
                    <th>Prénom et NOM</th>
                    <th>Montant</th>
                    <th>Regisseur</th>

                </tr>
                <?php 
				if (!empty($tabPaiment)) : ?>
                <?php foreach ($tabPaiment as $index => $row) :?>
                <tr style="font-size: 14px;">
                    <td class="text-center"><?php echo htmlspecialchars($row['quittance']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['dateTime_paie']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['libelle']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['num_etu']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['prenoms']." ".$row['nom']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['montant']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['username_user']); ?></td>

                </tr>
                <?php endforeach; ?>
                <?php else : ?>
                <tr>
                    <?php if (!empty($_GET['data'])) ?>
                    <td colspan="6">Aucun résultat trouvé</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
        <?php
      //  require_once('../footer.php')
        ?>
    </div>

    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- JavaScript de Bootstrap (assurez-vous d'ajuster le chemin si nécessaire ) -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
</body>
<script src='../../assets/js/script.js'></script>
</body>

</html>