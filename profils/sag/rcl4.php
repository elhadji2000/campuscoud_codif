<?php session_start(); ?>  
<html lang="en">

<?php 
if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=../">'; exit();}  
include('head.html');	  
    include ('../../traitement/fonction.php');
    $link = connexionBD();
		  

if(! isset( $_SESSION['sag'] ) )
{header("location: ../");}		 // deconnexion();

 include('../../activite.php'); 
 
 if (isset($_GET['var'])){
		$var = $_GET['var'];
	} elseif (isset($_POST['var'])){
		$var = $_POST['var'];
	}else {
		$var  = null;
	}	
	
	 if (isset($_GET['ne'])){
		$ne = $_GET['ne'];
	} elseif (isset($_POST['ne'])){
		$ne = $_POST['ne'];
	}else {
		$ne  = null;
	}	
	
		 if (isset($_GET['nr'])){
		$nr = $_GET['nr'];
	} elseif (isset($_POST['nr'])){
		$nr = $_POST['nr'];
	}else {
		$nr  = null;
	}	
	
?>


<section id="homedesigne" class="s-homedesigne">   
<p class="lead">
<?php
echo "Espace S.A.G: Bienvenue!";
 ?>
</p>
</section> <!-- end s-stats -->

<?php
if($var=='mi') {
	
 if (isset($_GET['dateNaissance'])){
		$dateNaissance = $_GET['dateNaissance'];
	} elseif (isset($_POST['dateNaissance'])){
		$dateNaissance = $_POST['dateNaissance'];
	}else {
		$dateNaissance  = null;
	}	
	
	 if (isset($_GET['numIdentite'])){
		$numIdentite = $_GET['numIdentite'];
	} elseif (isset($_POST['numIdentite'])){
		$numIdentite = $_POST['numIdentite'];
	}else {
		$numIdentite  = null;
	}
	
	 if (isset($_GET['telephone'])){
		$telephone = $_GET['telephone'];
	} elseif (isset($_POST['telephone'])){
		$telephone = $_POST['telephone'];
	}else {
		$telephone  = null;
	}	
	
$rr=("update  `codif_etudiant` set dateNaissance='$dateNaissance',numIdentite='$numIdentite',
telephone='$telephone'  where num_etu ='$ne'");                               							 
$ee = mysqli_query($link, $rr);

echo "<meta http-equiv='refresh' content='0;URL=rcl2?nr=$nr'>";
//echo "<meta http-equiv='refresh' content='0;URL=index'>";
	exit();
}

elseif($var=='sms') {
	
 if (isset($_GET['numtel'])){
		$numtel = $_GET['numtel'];
	} elseif (isset($_POST['numtel'])){
		$numtel = $_POST['numtel'];
	}else {
		$numtel  = null;
	}
	
	 if (isset($_GET['msg'])){
		$msg = $_GET['msg'];
	} elseif (isset($_POST['msg'])){
		$msg = $_POST['msg'];
	}else {
		$msg  = null;
	}	
	//echo $numtel." ".$msg; exit();
sms_reclamation($numtel,$msg) ; 						
			echo '<script type="text/javascript">alert("SMS envoye au '.$numtel.'!")</script>';
	
}

elseif($var=='su') {
	
	
$rr=("delete from  `codif_user`  where username_user ='$ne'");                               							 
$ee = mysqli_query($link, $rr);

echo "<meta http-equiv='refresh' content='0;URL=rcl2?nr=$nr'>";	
//echo "<meta http-equiv='refresh' content='0;URL=index'>";
	exit();
}
elseif($var=='cl') {
	
$rr=("update  `codif_reclamation` set statut='OK' where num_etu ='$ne'");                               							 
$ee = mysqli_query($link, $rr);	

echo "<meta http-equiv='refresh' content='0;URL=rcl2?nr=$nr'>";
//echo "<meta http-equiv='refresh' content='0;URL=index'>";
	exit();	
}
 ?>	

<center>

<a href="javascript:history.back()" id="retour" >Retour</a><br>
<?php
//include('../foot.html');
?>
</center>
    <!-- footer
    ================================================== -->
   
</body>

</html>