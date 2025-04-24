<html>
   <script langage='javascript'>
                                alert('Un mot de passe par défault vous a été envoyé au "xx xxx xx xx"! ')
                                </script>
<?php /* if($_SERVER["HTTP_REFERER"]!="https://codification.ucad.sn/mp1")
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}  */


                 echo '<meta http-equiv="refresh" content="0;URL=log">';
                     exit();

include('activite.php');
include ('connexion.php');
include ('mp1.php');
	
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
	
		if (isset($_POST['mdp'])){
		$mdp = $_POST['mdp'];
	} else {
		$mdp  = null;
	}

	if (isset($_POST['mdp_conf'])){
		$mdp_conf= $_POST['mdp_conf'];
	} else {
		$mdp_conf= null;
	}
?>

<body background="images/coud.png" style ="background-repeat: no-repeat; background-position:center;">
<?php

	if ($numeroetudiant != "" and $datedenaissance != "" and $numeroidentite != ""){
        $datedenaissance=changedateusfr($datedenaissance);
        $link = maconnexion(); //echo $datedenaissance." ".$numeroidentite." ".$numeroetudiant;exit();


//Verification de l'existence de l'etudiant        
        $requet ="select * from codif_etudiant where num_etu='$numeroetudiant' and dateNaissance =  '$datedenaissance' and numIdentite = '$numeroidentite'"; //echo $requet;exit();     
        $resultat = mysqli_query($link, $requet) or die ('connexion impossible'.$requet.'<br />'.mysqli_error($link)); //
        $n_rows = mysqli_num_rows($resultat);
        if(!$n_rows)
        {
                 ?>
                 <script langage='javascript'>
                 alert('Informations incorrectes!')
                 </script>
                 <?php
                 echo '<meta http-equiv="refresh" content="0;URL=mp1">';
                     exit();
                 
        } else {
			
			if($mdp != $mdp_conf)
        {
                 ?>
                 <script langage='javascript'>
                 alert('Veuillez entrer le meme mot de passe deux fois.')
                 </script>
                 <?php
                 
        }
		else {
        $mdp_encrypt = SHA1($mdp);
		$datesys=date("Y-m-d H:i:s");
        $insertion2 = "INSERT INTO `codif_user`(`login`,`mdp`,`datesys`)
            VALUES('$numeroetudiant','$mdp_encrypt','$datesys')";
        $execution = mysqli_query($link, $insertion2);
        
        
							if($execution){
                                ?>
                                <script langage='javascript'>
                                alert('Votre compte a ete recupere avec succes! ')
                                </script>
                                <?php
                                echo '<meta http-equiv="refresh" content="0;URL=log">';
                                    exit();
							} else {
								echo "<center><h3><font color='red'>Erreur: veuillez reessayer.</font></h3> </center>";
								//echo '<center><a href="index">Connexion</a></center>';
                                    exit();
							}
					
		deconnexion ($link) ;
            
	}
                 
	}
                     
} else {
		echo "<br><center><h2><font color='red'>Veuillez renseigner tous les champs.</font></h2></center>";
		echo '<center><a href="javascript:history.back()">Retour</a></center>';
	}
?>

</html>
