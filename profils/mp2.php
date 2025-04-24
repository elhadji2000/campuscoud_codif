<?php include('mp.php'); ?>  
<html lang="en">

<?php  
if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}  

 
 			$login=$_SESSION['username'] ;

include('../head.php');			

$mdp_bd=info2($login)['0']; //echo $mdp;


if (isset($_POST['mdp'])){
		$mdp= $_POST['mdp'];
	} else {
		$mdp= null;
	}
	
	if (isset($_POST['mdp_new'])){
		$mdp_new= $_POST['mdp_new'];
	} else {
		$mdp_new= null;
	}
	
	if (isset($_POST['mdp_conf'])){
		$mdp_conf= $_POST['mdp_conf'];
	} else {
		$mdp_conf= null;
	}
	
	
	
	
	if($mdp_bd==SHA1($mdp) && SHA1($mdp_new)==SHA1($mdp_conf))
				 {
					 if($mdp==$mdp_conf)
				    {
					echo "<script langage='javascript'>";								
					echo "window.alert('Vous ne pouvez pas garder le meme mot de passe, veuillez reessayer !!!')";
					echo "</script>";
				    echo '<meta http-equiv="refresh" content="0;URL=mp">';
	                   exit();
					}
					 
					 $datesys=date("Y-m-d H:i");
					 $link = connexionBD();
					$requet =("UPDATE  `codif_user` SET `password_user` =SHA1('$mdp_conf'),`type_mdp` ='updated',`datesys` ='$datesys'
								WHERE  `codif_user`.`username_user` =  '".$login."'");
					$resultat  = mysqli_query($link,$requet)or die('IMPOSSIBLE DE FAIRE LA MODIFICATION '.mysqli_error());
					if($resultat)
					{
		
					echo "<script langage='javascript'>";
								
					echo "window.alert('Mot de passe modifie avec succes! !!!')";
					echo "</script>";
					$_SESSION['type_mdp']='updated';
					 echo '<meta http-equiv="refresh" content="0;URL=../log">';
					 
					}
				 }
				 else{
					echo "<script langage='javascript'>";
								
					echo "window.alert('Les donnees saisies ne sont pas correctes, veuillez reessayer !!!')";
					echo "</script>";
					 echo '<meta http-equiv="refresh" content="0;URL=mp">';
	                   exit();
					}
            
?>