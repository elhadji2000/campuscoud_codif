<?php session_start(); 

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$etudiant = null;

if (isset($_GET["numCarte"])) {
    $numCarte = $_GET["numCarte"];
    $etudiant = studentConnect2($numCarte); // Doit retourner un tableau ou null
    $quota = getQuotaClasse($etudiant['niveauFormation'], $etudiant['sexe'])['COUNT(*)'];
    $statut = getOnestudentStatus($quota, $etudiant['niveauFormation'], $etudiant['sexe'], $numCarte);
    //var_dump($statut);die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si toutes les données requises sont présentes
    if (isset($_POST["num_etu"], $_POST["prenoms"], $_POST["nom"], $_POST["telephone"], $_POST["lieuNaissance"], 
              $_POST["dateNaissance"], $_POST["etablissement"], $_POST["departement"], 
              $_POST["niveauFormation"], $_POST["moyenne"], $_POST["numIdentite"], $_POST["sexe"])) {

        // Récupération des données
        $num_etu = $_POST["num_etu"];
        $prenoms = $_POST["prenoms"];
        $nom = $_POST["nom"];
        $telephone = $_POST["telephone"];
        $lieuNaissance = $_POST["lieuNaissance"];
        $dateNaissance = $_POST["dateNaissance"];
        $etablissement = $_POST["etablissement"];
        $departement = $_POST["departement"];
        $niveauFormation = $_POST["niveauFormation"];
        $moyenne = $_POST["moyenne"];
        $numIdentite = $_POST["numIdentite"];
        $sexe = $_POST["sexe"];
        

        // Enregistrement de l'étudiant
        enregistrerEtudiant($connexion, $num_etu, $prenoms, $nom, $telephone, $lieuNaissance, $dateNaissance, 
                            $etablissement, $departement, $niveauFormation, $moyenne, $numIdentite, $sexe);
    } else {
        echo "Tous les champs du formulaire doivent être remplis.";
    }
}


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
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="../../assets/css/login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <?php include('../../head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        crossorigin="anonymous" />

    <style>
    /* Amélioration des champs de saisie */
    .form-control {
        background-color: rgba(161, 187, 228, 0.1);
        font-size: 16px;
        height: 60px;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    /* Centrage et style du conteneur */
    .container {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .container-fluid {
        max-width: 90%;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style du tableau */
    table {
        border-radius: 10px;
        overflow: hidden;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Message d'erreur */
    .alert {
        font-size: 18px;
        text-align: center;
        padding: 15px;
        margin-top: 20px;
    }

    /* Style des boutons */
    .btn-custom {
        font-size: 14px;
        padding: 10px 15px;
        border-radius: 5px;
        transition: all 0.3s ease-in-out;
    }

    .btn-custom:hover {
        transform: scale(1.05);
    }
    </style>

<body>
    <div class="container">
        <h2 class="text-center text-primary">Rechercher un étudiant</h2>

        <form method="GET" action="etudiant.php" class="text-center mt-4">
            <center>
                <div class="text-center row">

                    <div class="col-md-6">
                        <input type="text" required placeholder="Numéro Étudiant" name="numCarte"
                            class="form-control" />
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-block btn-custom">
                            <i class="fas fa-search"></i>RECHERCHER
                        </button>
                    </div>

                </div>
            </center>
        </form>
    </div>
    <?php if ($etudiant) : ?>
    <div class="container-fluid">
        <!-- Affichage des résultats -->
        <div class="mt-4">
            <h3 class="text-center text-success">Informations de l'étudiant</h3>
            <table class="table table-bordered table-hover mt-3">
                <thead class="table-info">
                    <tr>
                        <th>Num_Carte</th>
                        <th>Prénom&Nom</th>
                        <th>Téléphone</th>
                        <th>Classe</th>
                        <th>date_naissance</th>
                        <th>Statut</th>
                        <th>Choix</th>
                        <th>Validation</th>
                        <th>Paiement</th>
                        <th>Loger</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?= htmlspecialchars($etudiant['num_etu']) ?></th>
                        <td><?= htmlspecialchars($etudiant['prenoms']) ?> <?= htmlspecialchars($etudiant['nom']) ?></td>
                        <td><?= htmlspecialchars($etudiant['telephone']) ?></td>
                        <td><?= htmlspecialchars($etudiant['niveauFormation']) ?></td>
                        <td><?= htmlspecialchars($etudiant['dateNaissance']) ?></td>
                        <td><?= $statut['statut'] ?></td>
                        <td><?= !empty($etudiant['dateTime_aff']) ? htmlspecialchars($etudiant['dateTime_aff']) : 'NON' ?> / <?= !empty($etudiant['lit']) ? htmlspecialchars($etudiant['lit']) : 'NON' ?>
                        </td>
                        <td><?= !empty($etudiant['dateTime_val']) ? htmlspecialchars($etudiant['dateTime_val']) : 'NON' ?>
                        </td>
                         <td><?= !empty($etudiant['dateTime_paie']) ? htmlspecialchars($etudiant['dateTime_paie']) : 'NON' ?></td>
                        <td><?= !empty($etudiant['dateTime_loger']) ? htmlspecialchars($etudiant['dateTime_loger']) : 'NON' ?></td>
                       

                    </tr>
                </tbody>
            </table>
            <div class="text-center mt-3">
                <a href="etudiant.php" class="btn btn-secondary btn-custom"><i class="fas fa-arrow-left"></i> Retour à
                    la recherche</a>
            </div>
        </div>
    </div>
    <?php else : ?>
    <div class="container">
        <?php if (isset($_GET["numCarte"])) : ?>
        <?php  $num_etu=$_GET["numCarte"];  ?>
        <div class="alert alert-danger">
            <h3>Étudiant non trouvé</h3>
            <p>Aucun étudiant trouvé avec ce numéro.</p>
                <a href="etudiant.php?ajouter&num_etu=<?php echo $num_etu; ?>" class="btn btn-success btn-custom">
                <i class="fas fa-user-plus"></i> Ajouter un étudiant
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>

    <!-- Condition pour afficher le formulaire d'ajout d'étudiant -->
    <?php if (isset($_GET["ajouter"])) : ?>
<?php  
$num_etu=$_GET["num_etu"];  
$data=getDonneesEtudiant($num_etu); //var_dump ($data); //die;						
$faculte= $data[0];
$departement= $data[1]; 
$nom= $data[2]; 
$prenom= $data[3];
$date_naissance= $data[4]; 
$lieu_naissance= $data[5];
//$sexe= $data[6]; 
$num_identite= $data[7]; 
$var=substr($num_identite,0,1); 
if($var=='1'){$sexe='G';}if($var=='2'){$sexe='F';} //echo $sexe; exit();
$telephone= $data[8];
?>
    
    <div class="container">
        <div class="mt-4">
            <h3 class="text-center text-primary">Ajouter un étudiant</h3>
            <form action="etudiant.php" method="POST" class="mt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numéro Étudiant</label>
                        <input type="text" name="num_etu" style="background-color: rgba(161, 187, 228, 0.1);" required value="<?php  echo $num_etu;  ?>"
                            class="form-control" placeholder="">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" style="background-color: rgba(161, 187, 228, 0.1);" name="prenoms" required value="<?php  echo $prenom;  ?>"
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" style="background-color: rgba(161, 187, 228, 0.1);" required value="<?php  echo $nom;  ?>"
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" style="background-color: rgba(161, 187, 228, 0.1);" required value="<?php  echo $telephone;  ?>"
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lieu de Naissance</label>
                        <input type="text" name="lieuNaissance" style="background-color: rgba(161, 187, 228, 0.1);"
                            required value="<?php  echo $lieu_naissance;  ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de Naissance</label>
                        <input type="text" name="dateNaissance" style="background-color: rgba(161, 187, 228, 0.1);"
                            required value="<?php  echo $date_naissance;  ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Etablissement</label>
                        <input type="text" name="etablissement" style="background-color: rgba(161, 187, 228, 0.1);"
                            required value="<?php  echo $faculte;  ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Département</label>
                        <input type="text" name="departement" style="background-color: rgba(161, 187, 228, 0.1);"
                            required value="<?php  echo $departement;  ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Niveau de Formation</label>
                        <input type="text" name="niveauFormation" style="background-color: rgba(161, 187, 228, 0.1);"
                            required  class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Moyenne</label>
                        <input type="text" name="moyenne" style="background-color: rgba(161, 187, 228, 0.1);" required 
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Numero Identitè</label>
                        <input type="text" name="numIdentite" style="background-color: rgba(161, 187, 228, 0.1);"
                            required value="<?php  echo $num_identite;  ?>" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Sexe</label>
                        <input type="text" name="sexe" style="background-color: rgba(161, 187, 228, 0.1);" required value="<?php  echo $sexe;  ?>"
                            class="form-control">
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success btn-custom">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <a href="etudiant.php" class="btn btn-secondary btn-custom"><i class="fas fa-times"></i> Annuler</a>
                </div>
            </form>
        </div>
        <?php endif; ?>


    </div>
    <!-- footer ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">
                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="/campuscoud.com/assets/images/logo.png"
                            alt="Homepage"></a>
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
</body>

</html>