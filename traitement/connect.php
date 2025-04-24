<?php 
include('fonction.php');
$error = "";

if (!empty($_POST['username_user']) && !empty($_POST['password_user'])) {
    $username = $_POST['username_user'];
    $password = $_POST['password_user'];
	
		//Mettre en majuscule et eliminer lespace eventuel
      $username=strtoupper($username); $username = str_replace(' ','',$username);	
    /**************************************************************************/
	
    
    $row = login($username, $password);
    if ($row) {
		
        ancien_eligible($username); 
		
        session_start();
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['username'] = $row['username_user'];
        $_SESSION['mdp'] = $row['password_user'];
        $_SESSION['sexe_agent'] = $row['sexe_user'];
        $_SESSION['profil'] = $row['profil_user'];
        $_SESSION['prenom'] = $row['prenom_user'];
        $_SESSION['nom'] = $row['nom_user'];
        if ($row['profil_user'] == 'quota') {
            header('Location: ../profils/personnels/niveau');
            exit();
        } else if ($row['profil_user'] == 'delai') {
            header('Location: ../profils/personnels/add_delai');
            exit();
        } else if ($row['profil_user'] == 'forclusion') {
            header('Location: ../profils/forclusion/forclore');
            exit();
        } else if ($row['profil_user'] == 'validation') {
            header('Location: ../profils/validation/validation');
            exit();
        } else if ($row['profil_user'] == 'paiement') {
            header('Location: ../profils/paiement/paiement');
            exit();
        } else if ($row['profil_user'] == 'chef_residence') {
            $_SESSION['pavillon'] = $row['pavillon'];
            header('Location: ../profils/loger/loger');
            exit();
			} else if ($row['profil_user'] == 'sag') {
		 $_SESSION['sag'] = $row['profil_user'];
            header('Location: ../profils/sag/index');
            exit();
        } else if ($row['profil_user'] == 'cs_acp') {
		 $_SESSION['chef_acp'] = $row['profil_user'];
            header('Location: ../profils/cs_acp/etatPaiement_cs');
            exit();
        } else if ($row['profil_user'] == 'dba') {
		 $_SESSION['dba'] = $row['profil_user'];
            header('Location: ../profils/dba/addUser');
            exit();
        }
        else if ($row['profil_user'] == 'chef_campus') {
		 $_SESSION['chef_campus'] = $row['profil_user'];
		 $_SESSION['campus'] = $row['campus'];
            header('Location: ../profils/cs_campus/index');
            exit();
        }
        else if ($row['profil_user'] == 'chef_departement') {
		 $_SESSION['chef_departement'] = $row['profil_user'];
            header('Location: ../profils/cs_departement/index');
            exit();
        }
        else if ($row['profil_user'] == 'chef_recette') {
		 $_SESSION['chef_recette'] = $row['profil_user'];
            header('Location: ../profils/cs_recettes/index');
            exit();
        }
        else if ($row['profil_user'] == 'pcs') {
		 $_SESSION['pcs'] = $row['profil_user'];
		 $_SESSION['fac'] = $row['fac'];
            header('Location: ../profils/pcs/evolution');
            exit();
        } else if ($row['profil_user'] == 'user') {
			
			$_SESSION['type_mdp'] = $row['type_mdp'];
			
            $dataStudent = studentConnect($username);
            $_SESSION['id_etu'] = $dataStudent['id_etu'];
            $_SESSION['nationalite'] = $dataStudent['nationalite'];
            $_SESSION['niveau'] = $dataStudent['niveau'];
            $_SESSION['num_etu'] = $dataStudent['num_etu'];
            $_SESSION['etablissement'] = $dataStudent['etablissement'];
            $_SESSION['num_etu'] = $dataStudent['num_etu'];
            $_SESSION['classe'] = $dataStudent['niveauFormation'];
            $_SESSION['dateNaissance'] = $dataStudent['dateNaissance'];
            $_SESSION['lieuNaissance'] = $dataStudent['lieuNaissance'];
            $_SESSION['sexe_etudiant'] = $dataStudent['sexe'];
            
			$resultat = getPolitiqueConf($_SESSION['id_etu']);
            if ($resultat) {
                header('Location: ../profils/etudiants/resultat');
                exit();
            } else {
                header('Location: ../profils/etudiants/accueilEtudiant');
                exit();
            }
			
			
        }
    } else {
        			?>
<script langage='javascript'>
alert('Nom dutilisateur et/ou mot de passe incorrect ou Compte inactif.')
</script>
<?php
echo '<meta http-equiv="refresh" content="0;URL=../index">';
	exit();
    }
}
?>