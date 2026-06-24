<html>
<?php $_SESSION['annee'] = $_POST['annee'];

if (!isset($_POST['num_etu'])) {
    echo '<meta http-equiv="refresh" content="0;URL=index">';
    exit();
}

include('activite.php');
include('traitement/fonction.php');
include('mpo1.php');

$numeroetudiant = $_POST['num_etu'] ?? null;
$datedenaissance = $_POST['dateNaissance'] ?? null;
$numeroidentite  = $_POST['numIdentite'] ?? null;

?>

<body background="assets/images/coud.png" style="background-repeat: no-repeat; background-position:center;">
<?php

$link = connexionBD();

// Vérification d'abord s’il s’agit d’un **étudiant**
$requeteEtudiant = "SELECT * FROM codif_etudiant WHERE num_etu='$numeroetudiant' AND numIdentite='$numeroidentite'";
$resultEtudiant = mysqli_query($link, $requeteEtudiant) or die('Erreur SQL: '.mysqli_error($link));
$isEtudiant = mysqli_num_rows($resultEtudiant);

// ============================
// CAS 1 : C’est un ÉTUDIANT
// ============================
if ($isEtudiant) {
    $donnee = mysqli_fetch_array($resultEtudiant);
    $numeroCompte = $donnee['num_etu'];
    $telephone = getTelephoneEtudiant($numeroCompte);
}
// ============================
// CAS 2 : Sinon, on teste si c’est un PERSONNEL
// ============================
else {
    $requetePersonnel = "SELECT * FROM codif_user WHERE username_user='$numeroetudiant' AND username_user NOT IN (SELECT num_etu FROM codif_etudiant)";
    $resultPersonnel = mysqli_query($connexion_user, $requetePersonnel) or die('Erreur SQL: '.mysqli_error($link));
    $isPersonnel = mysqli_num_rows($resultPersonnel);

    if (!$isPersonnel) {
        ?>
        <script langage='javascript'>
            alert('Les informations saisies semblent incorrectes ou inexistantes !');
        </script>
        <?php
        echo '<meta http-equiv="refresh" content="0;URL=mpo1">';
        exit();
    }

    // On récupère les infos du personnel
    $donnee = mysqli_fetch_array($resultPersonnel);
    $numeroCompte = $donnee['username_user'];

    // Si tu as une fonction pour récupérer le téléphone du personnel
    if (function_exists('getTelephonePersonnel')) {
        $telephone = getTelephonePersonnel($numeroCompte);
    } else {
        $telephone = $donnee['telephone_user'] ?? null;
    }
}

// ============================
// Vérification du compte utilisateur
// ============================
$reqUser = "SELECT * FROM codif_user WHERE username_user='$numeroCompte' AND type_mdp='updated'";
$resUser = mysqli_query($connexion_user, $reqUser) or die('Erreur SQL: '.mysqli_error($link));
$hasAccount = mysqli_num_rows($resUser);

if (!$hasAccount) {
    ?>
    <script langage='javascript'>
        alert("Soit vous n'avez pas encore de compte, soit votre mot de passe n'est pas encore personnalisé !");
    </script>
    <?php
    echo '<meta http-equiv="refresh" content="0;URL=rc">';
    exit();
}

// ============================
// Réinitialisation du mot de passe
// ============================
$datesys = date("Y-m-d H:i:s");
$default_mdp = generer_mdp();
$default_mdp_encrypt = SHA1($default_mdp);

$update = "
    UPDATE codif_user 
    SET password_user='$default_mdp_encrypt', type_mdp='default', datesys='$datesys' 
    WHERE username_user='$numeroCompte'
";
$exec = mysqli_query($connexion_user, $update);

if ($exec) {
    // Envoi SMS (même logique pour étudiant ou personnel)
    if ($telephone) {
        sms_compte_created($telephone, $numeroCompte, $default_mdp);
        enreg_sms($numeroCompte, $telephone, 'mdp_oublie');
    }

    echo "<script type='text/javascript'>alert('Vos nouvelles informations de connexion vous ont été envoyées par SMS au $telephone');</script>";
    echo '<meta http-equiv="refresh" content="0;URL=log">';
    exit();
} else {
    echo "<center><h3><font color='red'>Erreur: la réinitialisation du mot de passe a échoué.</font></h3></center>";
    exit();
}

deconnexion($link);
?>

</body>
</html>
