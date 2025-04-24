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
	
$rr=("SELECT dateNaissance,numIdentite,telephone FROM  `codif_etudiant` where num_etu ='$ne'");                               							 
$ee = mysqli_query($link, $rr);
$stk = mysqli_fetch_array($ee);
$dateNaissance=$stk['dateNaissance'];$numIdentite=$stk['numIdentite'];$telephone=$stk['telephone'];
 ?>	
<form action="rcl4" method="post"> 
 <tr>
    <td colspan="4"><center>
        <!--strong>VEUILLEZ RENSEIGNER LES CHAMPS OU APPELER AU 78 539 02 73 / 78 539 26 92</strong-->
		<strong>MODIFICATION DES INFOS</strong>
    </center>
    </td>
</tr>
 <fieldset>
    
                    <div class="form-field">
                        <input onkeydown="upperCaseF(this)"  name="dateNaissance" required type="text" onkeydown='return sansEspace();' id="dateNaissance" placeholder="dateNaissance" value="<?php echo $dateNaissance; ?>" class="full-width">
                    </div>
                   
					
					<div class="form-field">
                        <input name="numIdentite" required  type="text"  placeholder="C.N.I" value="<?php echo $numIdentite; ?>" class="full-width">
                    </div>
					
					
					<div class="form-field">
                        <input name="telephone" required type="text" id="telephone" placeholder="Telephone" value="<?php echo $telephone; ?>" class="full-width">
                    </div>
                   
                    </fieldset>
					
					<input  name="ne" type="hidden" value="<?php echo $ne; ?>">
					<input  name="var" type="hidden" value="<?php echo $var; ?>">
					<input  name="nr" type="hidden" value="<?php echo $nr; ?>">
<tr>
<td align='CENTER'><input type="submit" value="Valider"></td>
</tr><br><br>

</form> 
<?php
}
elseif($var=='sms') {
$rr=("SELECT contact FROM  `codif_reclamation` where numreclame ='$nr'");                               							 
$ee = mysqli_query($link, $rr);$stk = mysqli_fetch_array($ee);
$numtel=$stk['contact'];
 ?>	
<form action="rcl4" method="post"> 
 <tr>
    <td colspan="4"><center>
		<strong>ENVOI SMS</strong>
    </center>
    </td>
</tr>
 <fieldset>					
					<div class="form-field">
 <input name="msg" required type="text" id="msg" placeholder="SMS" value="Reclamation sur CAMPUSCOUD recue: " class="full-width">
                    </div>    
                    </fieldset>
					
					<input  name="numtel" type="hidden" value="<?php echo $numtel; ?>">
					<input  name="var" type="hidden" value="<?php echo $var; ?>">
					<input  name="nr" type="hidden" value="<?php echo $nr; ?>">
<tr><td align='CENTER'><input type="submit" value="Valider"></td></tr><br><br>
</form> 
<?php
}	

elseif($var=='su') {
	
	
echo "Voulez vous supprimer le user cree ?"; ?>	 <a href="rcl4?var=su&ne=<?php echo $ne; ?>&nr=<?php echo $nr; ?>">Oui</a>


	
<?php	
}
elseif($var=='cl') {
	
	
echo "Voulez vous archiver la reclamation ?"; ?> <a href="rcl4?var=cl&ne=<?php echo $ne; ?>&nr=<?php echo $nr; ?>">Oui</a>
	
<?php	
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