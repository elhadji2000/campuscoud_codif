<html>
<?php 

if (!isset($_POST['num_etu']))
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}


include('activite.php');
include('traitement/fonction.php');
include ('mpo1.php');
	
	if (isset($_POST['num_etu'])){
		$numeroetudiant= $_POST['num_etu'];
	} else {
		$numeroetudiant =  null;
	}

	if (isset($_POST['dateNaissance'])){
		$datedenaissance = $_POST['dateNaissance'];
	} else {
		$datedenaissance  = null;
	}

	if (isset($_POST['numIdentite'])){
		$numeroidentite= $_POST['numIdentite'];
	} else {
		$numeroidentite= null;
	}

?>

<body background="assets/images/coud.png" style ="background-repeat: no-repeat; background-position:center;">
<?php

        //$datedenaissance=changedateusfr($datedenaissance);
        $link = connexionBD(); //echo $datedenaissance." ".$numeroidentite." ".$numeroetudiant;exit();


//Verification de l'existence de l'etudiant        
      $requet ="select * from codif_etudiant where num_etu='$numeroetudiant'  and numIdentite = '$numeroidentite'";          //and dateNaissance =  '$datedenaissance'
        $resultat = mysqli_query($link, $requet) or die ('connexion impossible'.$requet.'<br />'.mysqli_error($link)); //
        $n_rows = mysqli_num_rows($resultat);
        if(!$n_rows)
        {
                 ?>
                 <script langage='javascript'>
                 alert('Les informations saisies semblent incorrectes ou inexistantes!')
                 </script>
                 <?php
                 echo '<meta http-equiv="refresh" content="0;URL=mpo1">';
                     exit();
                 
        } //Fin verification
		else {
			
	//Verification de l'existence d'un compte       
      $rr ="select * from codif_user where username_user='$numeroetudiant' and type_mdp='updated'";     
        $ee = mysqli_query($link, $rr) or die ('connexion impossible'.$rr.'<br />'.mysqli_error($link)); //
        $ss = mysqli_num_rows($ee);
        if(!$ss)
        {
			 ?>
                 <script langage='javascript'>
                 alert('Soit vous navez pas encore de compte, soit votre mot de passe nest pas encore personnalisé!')
                 </script>
                 <?php
                 echo '<meta http-equiv="refresh" content="0;URL=rc">';
                     exit();
		}
	//Fin verification

        $datesys=date("Y-m-d H:i:s");
		$default_mdp= generer_mdp(); 
		$default_mdp_encrypt = SHA1($default_mdp);
		$donnee=mysqli_fetch_array($resultat);$numeroetudiant=$donnee['num_etu'];
        $updat = "UPDATE `codif_user` SET `password_user` = '$default_mdp_encrypt', `type_mdp` = 'default', `datesys` = '$datesys' 
		WHERE `codif_user`.`username_user` ='$numeroetudiant'";
        $ex = mysqli_query($link, $updat);
        
        
							if($ex){
							     $telephone=getTelephoneEtudiant($numeroetudiant); 
								 
//Envoi
sms_compte_created($telephone,$numeroetudiant,$default_mdp) ;

//Stockage				
enreg_sms($numeroetudiant, $telephone, 'mdp_oublie');
								 
                               echo '<script type="text/javascript">alert("Vos nouvelles informations de connexion vous ont été envoyées par SMS au '.$telephone.'")</script>';
							   echo '<meta http-equiv="refresh" content="0;URL=log">';
                     exit();
							} else {
								echo "<center><h3><font color='red'>Erreur: la reinitialisation du mot de passe a échoué.</font></h3> </center>";
								//echo '<center><a href="index">Connexion</a></center>';
                                    exit();
							}
					
		deconnexion ($link) ;
            

                 
	}
                     
?>

</html>
