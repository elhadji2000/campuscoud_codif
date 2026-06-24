<html>
<?php
include ('traitement/fonction.php');
include ('activite.php');
include ('insc.php');

if (!isset($_POST['num_etu'])) {
    echo '<meta http-equiv="refresh" content="0;URL=index">';
    exit();
}

if (isset($_POST['num_etu'])) {
    $numeroetudiant = $_POST['num_etu'];
} else {
    $numeroetudiant = null;
}
// Mettre en majuscule et eliminer lespace eventuel
$numeroetudiant = strtoupper($numeroetudiant);
$numeroetudiant = str_replace(' ', '', $numeroetudiant);

if (isset($_POST['dateNaissance'])) {
    $datedenaissance = $_POST['dateNaissance'];
} else {
    $datedenaissance = null;
}
// echo $_SESSION['annee']; exit();
if (strlen($datedenaissance) != 10) {
    ?>
<script langage='javascript'>
alert('Veuillez corriger le format de la date de naissance:JJ/MM/AAAA!')
window.history.back();
</script>
<?php
    exit();
}

if (isset($_POST['numIdentite'])) {
    $numeroidentite = $_POST['numIdentite'];
} else {
    $numeroidentite = null;
}

/*if (isset($_POST['telephone'])){
        $telephone= $_POST['telephone'];
    } else {
        $telephone= null;
    }

        if (strlen($telephone) != 9 or !is_numeric($telephone)){

    ?>
<script langage='javascript'>
alert('Veuillez saisir un numero de telephone valide!')
window.history.back();
</script>
<?php exit();
        }*/

?>

<body background="images/coud.png" style="background-repeat: no-repeat; background-position:center;">
    <?php

    // $datedenaissance=changedateusfr($datedenaissance);
    $link = connexionBD();  // echo $datedenaissance." ".$numeroidentite." ".$numeroetudiant;exit();

    // Verification de l'existence de l'etudiant
    $requet = "select * from codif_etudiant where num_etu='$numeroetudiant' and numIdentite = '$numeroidentite'";  // and dateNaissance =  '$datedenaissance'
    //	if($numeroetudiant=='201607I83'){echo $requet;exit();}
    $resultat = mysqli_query($link, $requet) or die('connexion impossible' . $requet . '<br />' . mysqli_error($link));  //
    $n_rows = mysqli_num_rows($resultat);
    if (!$n_rows) {
        $api = getDonneesEtudiant_2($numeroetudiant);
        if ($api) {
            $user = [
                'nom' => $api['nom'],
                'prenoms' => $api['prenom'],
                'dateNaissance' => $api['date_naissance'],
                'telephone' => $api['telephone'],
                'email_ucad' => $api['email_ucad'],
                'lieuNaissance' => $api['lieu_naissance'],
                'sexe' => $api['sexe'],
                'annee' => $api['annee'],
                'numIdentite' => $api['num_identite']
            ];
            $nom = $user['nom'];
            $prenoms = $user['prenoms'];
            $dateNaissance = $user['dateNaissance'];
            $lieuNaissance = $user['lieuNaissance'];
            $sexe = $user['sexe'];
            $nationalite = 'NULL';
            $numIdentite = $user['numIdentite'];
            $telephone = $user['telephone'];
            $username = $numeroetudiant;
            $datesys = date('Y-m-d H:i:s');
            $default_mdp = generer_mdp();
            $default_mdp_encrypt = SHA1($default_mdp);

            $anneeEtudiant = $user['annee'];  // ex: 2024_2025
            $anneeFin = intval(substr($anneeEtudiant, -4));  // 2025
            $annee = isset($_POST['annee']) ? $_POST['annee'] : date('Y');
            $anneeCourante = intval(substr($annee, -4));
            $profil = 'user';
            $var = 'user non dans codif_etudiant';

            if (($anneeCourante - $anneeFin) >= 2) {
                echo '<script type="text/javascript">alert("ETUDIANT Non Inscrit(e) !!!")</script>';
                echo '<meta http-equiv="refresh" content="0;URL=log">';
                exit();
            }

            try {
                // $execution = mysqli_query($link, $insertion2);
                $execution = enregistrerUtilisateur_2($connexion_user, $nom, $prenoms, $var, $sexe, $telephone, $username, $profil, $default_mdp);

                // DEBUT_SMS

                // Envoi
                // sms_compte_created($telephone, $numeroetudiant, $default_mdp);
                $telephone = '784413400';
                sms_compte_created_2($telephone, $login, $prenoms, $default_mdp);

                // Stockage
                enreg_sms($numeroetudiant, $telephone, 'creation_compte');

                echo '<script type="text/javascript">alert("Creation du compte reussie: Vos informations de connexion vous ont été envoyées par SMS au ' . $telephone . ', allez les recuperer!")</script>';
                echo '<meta http-equiv="refresh" content="0;URL=log">';
                exit();

                // FIN_SMS
            } catch (Exception $e) {
                echo $e;
                echo '<br>';
            }
        } else {
            ?>
    <script langage='javascript'>
    alert('Les informations saisies semblent incorrectes ou inexistantes dans lannee academique choisie!')
    </script>
    <script langage='javascript'>
    alert('Vous pouvez consulter le Guide dUtilisation pour plus dinformations!')
    </script>
    <?php
            echo '<meta http-equiv="refresh" content="0;URL=guide">';
            exit();
        }
    } 
    else {
        $datesys = date('Y-m-d H:i:s');
        $default_mdp = generer_mdp();
        $default_mdp_encrypt = SHA1($default_mdp);
        $telephone = getTelephoneEtudiant($numeroetudiant);

        $insertion2 = "INSERT INTO `codif_user`(`username_user`,`password_user`,`datesys`)
            VALUES('$numeroetudiant','$default_mdp_encrypt','$datesys')";
        try {
            $execution = mysqli_query($connexion_user, $insertion2);

            // DEBUT_SMS

            // Envoi
            sms_compte_created($telephone, $numeroetudiant, $default_mdp);

            // Stockage
            enreg_sms($numeroetudiant, $telephone, 'creation_compte');

            echo '<script type="text/javascript">alert("Creation du compte reussie: Vos informations de connexion vous ont été envoyées par SMS au ' . $telephone . ', allez les recuperer!")</script>';
            echo '<meta http-equiv="refresh" content="0;URL=log">';
            exit();

            // FIN_SMS
        } catch (Exception $e) {
            echo '<script type="text/javascript">alert("Il semble que vous avez deja un compte, allez recuperer vos infos de connexion par SMS au ' . $telephone . '")</script>';
            echo 'Il semble que vous avez deja un compte, allez recuperer vos infos de connexion par SMS au ' . $telephone;
            echo '<meta http-equiv="refresh" content="0;URL=log">';
            exit();
            echo '<br>';
            exit();
        }
    }

    deconnexion($link);

    ?>

</html>