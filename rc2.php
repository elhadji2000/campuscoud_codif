<html>
<?php 
if (!isset($_POST['num_etu']))
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}  


include('activite.php');
require('traitement/fonction.php');
include ('rc.php');

if (isset($_POST['num_etu'])){
		$numeroetudiant= $_POST['num_etu'];
	} else {
		$numeroetudiant =  null;
	}

	
	if (isset($_POST['type'])){
		$type= $_POST['type'];
	} else {
		$type= null;
	}
	
		if (isset($_POST['contact'])){
		$contact= $_POST['contact'];
	} else {
		$contact= null;
	}
	
	if (strlen($contact) != 9 or !is_numeric($contact)){ 
	
	?>
                 <script langage='javascript'>
                 alert('Veuillez saisir un numero de telephone valide!')
                 window.history.back();
                 </script>
                 <?php
		}
	
	
	if($type=='Type de reclamation?')
        {
                 ?>
                 <script langage='javascript'>
                 alert('Veuillez preciser le type de reclamation en utilisant la liste deroulante!')
                 window.history.back();
                 </script>
                 <?php
                 
        }
	
	if (isset($_POST['reclamation'])){
		$reclamation= $_POST['reclamation'];
	} else {
		$reclamation= null;
	}

?>

<body background="images/coud.png" style ="background-repeat: no-repeat; background-position:center;">
<?php

        $link = connexionBD(); 


//Verification de l'existence de l'etudiant        
        $requet ="select * from codif_etudiant where num_etu='$numeroetudiant'";    
        $resultat = mysqli_query($link, $requet) or die ('connexion impossible'.$requet.'<br />'.mysqli_error($link)); //
        $n_rows = mysqli_num_rows($resultat);
        if(!$n_rows)
        {
                 ?>
                 <script langage='javascript'>
                 alert('Votre numero de carte est introuvable! Il se peut que la codification de votre FAC na pas encore demarre.')
                 window.history.back();
                 </script>
                 <?php
                 
        } else {

        $datesys=date("Y-m-d H:i:s");
		$reclamation=addslashes($reclamation);
        $insertion2 = "INSERT INTO `codif_reclamation`(`num_etu`,`contenu`,`type`,`contact`,`datesys`)
            VALUES('$numeroetudiant','$reclamation','$type','$contact','$datesys')";
			//echo $insertion2; exit();
        $execution = mysqli_query($link, $insertion2);
        
        
							if($execution){
								
		/*$rr ="select numreclame from codif_reclamation where num_etu='$numeroetudiant' order by datesys desc";    
        $ee = mysqli_query($link, $rr) ;$ss = mysqli_fetch_array($ee);$numreclame=$ss['numreclame'];*/

				 echo '<script type="text/javascript">alert("Reclamation enregistree avec succes!")</script>';

  /*  echo "Numero de reclamation: ".$numreclame.", veuillez bien le noter pour pouvoir suivre l'evolution"; 
	?> 	<a href='s8rc'>ici</a>      <?php  */
	
                 echo '<meta http-equiv="refresh" content="0;URL=guide">';
                     exit();
							
							} 
							else {
								?>
                 <script langage='javascript'>
                 alert('Erreur denregistrement!')
                 </script>
                 <?php
                     exit();
							}					
		deconnexion ($link) ;                       
	}
?>
</html>