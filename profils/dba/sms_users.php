<html>
<meta charset="UTF-8" />
<body>

<?php
//Estimation: 80 SMS/Minute


include('../../traitement/fonction.php');

$connexion = connexionBD(); 


//echo "Stop"; die; 
$requet = ("SELECT * FROM codif_user where username_user='903108/C3' ");                          
				$reponse = mysqli_query($connexion,$requet) or die ('ERREUR DE Recherche'.mysqli_error());
				
				while($rst_cons = mysqli_fetch_array($reponse))
					{
						//$numetu=$rst_cons['numetu'];
						$matricule=$rst_cons['username_user'];						
						$telephone=$rst_cons['telephone_user'];
						$nom=$rst_cons['prenom_user']." ".$rst_cons['nom_user'];						
						
					//	$pavillon=$rst_cons['pavillon'];
						
						//echo $nom;  die;
												
						sms_agents($telephone,$nom,$matricule); 						
					}



/*
$endroit="dans l'enceinte du stade de foot du campus";
//$faculte='F.A.S.E.G.';
//$requet = ("SELECT * FROM etudiant where statut='CARTE PRODUITE' and boite>'223' and faculte='$faculte' order by numauto asc;");   
$requet = ("SELECT * FROM etudiant order by numauto asc;");                         
				$reponse = mysqli_query($link,$requet) or die ('ERREUR DE Recherche'.mysqli_error());
				
				while($rst_cons = mysqli_fetch_array($reponse))
					{
						//$numetu=$rst_cons['numetu'];
						$num_etu=$rst_cons['num_etu'];						
						$telephone=$rst_cons['telephone'];
						$boite=$rst_cons['boite'];
						
						//echo $prenom." / "; 
												
						sms_carte($telephone,$num_etu,$boite,$endroit); 						
					}
*/					
					
	//die;				
					
//$faculte='F.A.S.E.G.';
//$requet = ("SELECT * FROM etudiant where statut='CARTE PRODUITE' and boite>'223' and faculte='$faculte' order by numauto asc;");   


?>
</html>