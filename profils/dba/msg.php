<html>
<meta charset="UTF-8" />
<body>

<?php
//Estimation: 80 SMS/Minute


include('../../traitement/fonction.php');

$connexion = connexionBD(); 


//echo "Stop"; die; 
$requet = ("SELECT num_etu,prenoms,telephone FROM codif_etudiant where id_etu='2051'");                        
				$reponse = mysqli_query($connexion,$requet) or die ('ERREUR DE Recherche'.mysqli_error());
				
				while($rst_cons = mysqli_fetch_array($reponse))
					{
					
						$prenoms=$rst_cons['prenoms'];						
						$telephone=$rst_cons['telephone']; 
					
						if($telephone==''){
						echo "Introuvable !"; die;}
						
//echo $numetu.$prenoms.$telephone; die;
												
			//	sms_nv_attributaire($telephone,$prenoms); 
								sms_nv_suppleant($telephone,$prenoms); 
					}



?>
</html>