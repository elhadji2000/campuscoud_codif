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
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">


    <!-- Bootstrap JS (nécessaire pour les modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
        font-size: 13px;
        text-align: center;
        vertical-align: middle;
    }

    body {
        font-family: 'Arial', sans-serif;
        /* Change la police globale */
        font-size: 16px;
        /* Augmente la taille globale du texte */
    }

    .table th,
    .table td {
        font-size: 15px;
        /* Agrandit la taille du texte dans le tableau */
    }

    .modal-body,
    .modal-header,
    .modal-footer {
        font-size: 18px;
        /* Augmente la taille du texte dans les modals */
    }
    </style>
    <?php include('../../head.php'); ?>
</head>

<body>


    <div class="container-fluid">
        <div class="row ">
            <center>
                <div class="text-center">
                    <h1>ETAT DES ENCAISSEMENTS PERIODIQUES</h1><br>
                </div>
            </center>
        </div>
        <br>
        <!-- Interval date -->
        <div class="container">
            <br><br>
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
                            <option value="LOYER">LOYER</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-3 justify-content-center">
                    <!-- Boutons -->
                    <div class="col-3">
                        <button id="submitBtn" name="rechercher" type="submit" class="btn btn-primary w-100">
                            <strong>Rechercher</strong>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button id="printBtn" name="imprimer" type="submit" class="btn btn-info w-100">
                            <strong>Imprimer</strong>
                        </button>
                    </div>
                </div>
            </form>

            <script>
            function validateForm() {
                const dateDebut = document.getElementById('date_debut').value;
                const dateFin = document.getElementById('date_fin').value;

                // Date minimale fixée à 01/01/2025
                const dateMin = new Date('2024-01-01');

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
                            "Si la date de début n'est pas renseignée, la date de fin doit être le 01/01/2024 ou après."
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
            <br><br>
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
        <br><br>
        <div class="container-fluid">
            <table class="table table-hover">
                <tr class="table-secondary" style="font-size: 16px; font-weight: 400;">
                    <th>Num_Quittance</th>
                    <th>Date_paye</th>
                    <th>Libelle</th>
                    <th>Num_Carte</th>
                    <th>Prénom</th>
                    <th>NOM</th>
                    <th>Montant</th>
                    <th>Regisseur</th>
                    <th>Modifier</th>
                </tr>
                <?php 
        if (!empty($tabPaiment)) : ?>
                <?php foreach ($tabPaiment as $index => $row) :?>
                <tr style="font-size: 14px;">
                    <td class="text-center"><?php echo htmlspecialchars($row['quittance']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['dateTime_paie']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['libelle']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['num_etu']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['prenoms']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['montant']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['username_user']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#editModal<?php echo $row['id_paie']; ?>">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                    </td>
                </tr>

                <!-- Modal de modification -->
                <div class="modal fade" id="editModal<?php echo $row['id_paie']; ?>" tabindex="-1"
                    aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Modifier le paiement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="etatPaiement_cs.php" method="POST" style="font-size:17px;">
                                <div class="modal-body" style="font-size:18px;">
                                    <input type="hidden" name="id_paie" value="<?php echo $row['id_paie']; ?>">
                                    <label>Numèro Etudiant :</label>
                                    <input type="text" name="montant" class="form-control"
                                        value="<?php echo htmlspecialchars($row['num_etu']); ?>" readonly>
                                    <label>Montant :</label>
                                    <select name="montant" class="form-control" required>
                                        <option value="" disabled selected>Choisir un montant</option>
                                        <option value="3000"<?php echo ($row['montant'] == '3000') ? 'selected' : ''; ?>>3 000 FCFA</option>
                                        <option value="4000"<?php echo ($row['montant'] == '4000') ? 'selected' : ''; ?>>4 000 FCFA</option>
                                        <option value="5000"
                                            <?php echo ($row['montant'] == '5000') ? 'selected' : ''; ?>>5 000 FCFA
                                        </option>
                                        <option value="6000"
                                            <?php echo ($row['montant'] == '6000') ? 'selected' : ''; ?>>6 000 FCFA
                                        </option>
                                        <option value="8000"
                                            <?php echo ($row['montant'] == '8000') ? 'selected' : ''; ?>>8 000 FCFA
                                        </option>
                                        <option value="9000"
                                            <?php echo ($row['montant'] == '9000') ? 'selected' : ''; ?>>9 000 FCFA
                                        </option>
                                        <option value="11000"
                                            <?php echo ($row['montant'] == '11000') ? 'selected' : ''; ?>>11 000 FCFA
                                        </option>
                                        <option value="12000"
                                            <?php echo ($row['montant'] == '12000') ? 'selected' : ''; ?>>12 000 FCFA
                                        </option>
                                        <option value="13000"
                                            <?php echo ($row['montant'] == '13000') ? 'selected' : ''; ?>>13 000 FCFA
                                        </option>
                                        <option value="15000"
                                            <?php echo ($row['montant'] == '15000') ? 'selected' : ''; ?>>15 000 FCFA
                                        </option>
                                        <option value="16000"
                                            <?php echo ($row['montant'] == '16000') ? 'selected' : ''; ?>>16 000 FCFA
                                        </option>
                                        <option value="17000"
                                            <?php echo ($row['montant'] == '17000') ? 'selected' : ''; ?>>17 000 FCFA
                                        </option>
                                        <option value="18000"
                                            <?php echo ($row['montant'] == '18000') ? 'selected' : ''; ?>>18 000 FCFA
                                        </option>
                                        <option value="20000"
                                            <?php echo ($row['montant'] == '20000') ? 'selected' : ''; ?>>20 000 FCFA
                                        </option>
                                        <option value="21000"
                                            <?php echo ($row['montant'] == '21000') ? 'selected' : ''; ?>>21 000 FCFA
                                        </option>
                                        <option value="24000"
                                            <?php echo ($row['montant'] == '24000') ? 'selected' : ''; ?>>24 000 FCFA
                                        </option>
                                        <option value="27000"
                                            <?php echo ($row['montant'] == '27000') ? 'selected' : ''; ?>>27 000 FCFA
                                        </option>
                                        <option value="28000"
                                            <?php echo ($row['montant'] == '28000') ? 'selected' : ''; ?>>28 000 FCFA
                                        </option>
                                        <option value="30000"
                                            <?php echo ($row['montant'] == '30000') ? 'selected' : ''; ?>>30 000 FCFA
                                        </option>
                                        <option value="32000"
                                            <?php echo ($row['montant'] == '32000') ? 'selected' : ''; ?>>32 000 FCFA
                                        </option>
                                        <option value="36000"
                                            <?php echo ($row['montant'] == '36000') ? 'selected' : ''; ?>>36 000 FCFA
                                        </option>
                                        <option value="40000"
                                            <?php echo ($row['montant'] == '40000') ? 'selected' : ''; ?>>40 000 FCFA
                                        </option>
                                    </select>

                                    <label>Libellé :</label>
                                    <input type="text" name="libelle" class="form-control"
                                        value="<?php echo htmlspecialchars($row['libelle']); ?>" required>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else : ?>
                <tr>
                    <td colspan="8">Aucun résultat trouvé</td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_paie'])) {

    $id_paie = intval($_POST['id_paie']);
    $new_montant = $_POST['montant'];
    $new_libelle = $_POST['libelle'];
    $modified_by = $_SESSION['username'];

    $message = modifierPaiement($connexion, $id_paie, $new_montant, $new_libelle, $modified_by);

    echo "<script>alert('$message'); window.location.href='etatPaiement_cs.php';</script>";
}

?>


    </div>

    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="../../assets/images/logo.png" alt="Homepage"></a>
                </div>



            </div>
        </div>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>&copy;Copyright Centre des Oeuvres universitaires de Dakar</span>
                </div>

                <div class="go-top">
                    <a class="smoothscroll" title="Back to Top" href="#top"><i class="im im-arrow-up"
                            aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

    </footer> <!-- end footer -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>

    <!-- JavaScript de Bootstrap (assurez-vous d' ajuster le chemin si nécessaire ) -->
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'>
    </script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'>
    </script>
    <script src='../../assets/js/script.js'></script>
</body>

</html>