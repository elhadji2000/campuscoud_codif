<?php session_start(); ?>  
<html lang="en">

<?php 
if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=../../">'; exit();}  
include('head.html');	  
    include ('../../traitement/fonction.php');
    $link = connexionBD();
		  

if(! isset( $_SESSION['sag'] ) )
{header("location: ../../");}		 // deconnexion();

 include('../../activite.php'); 
 
 if (isset($_GET['nr'])){
		$numreclame = $_GET['nr'];
	} elseif (isset($_POST['nr'])){
		$numreclame = $_POST['nr'];
	}else {
		$numreclame  = null;
	}	
	
	
?>


<section id="homedesigne" class="s-homedesigne">   
<p class="lead">
<?php
echo "Espace S.A.G: Bienvenue!";
 ?>
</p>
</section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    

	  
	  
	   <div class="row add-bottom">

            <div class="col-twelve">

                <p>Traitement de la Reclamation Numero <?php echo $numreclame; ?></p>

                <div class="table-responsive">
                    <table border="1">
                            <thead> Contenu de la reclamation
                            <tr>
                                <th>Carte</th>
								<th>Type</th>
                                <th>Contenu</th>
                                <th>Telephone</th>
								<!--th>Email</th-->
								<th>Date</th>
                            </tr>
                            </thead>
							<tbody>
<?php  

$requet=("SELECT * FROM `codif_reclamation` WHERE numreclame='$numreclame'");
$reponse = mysqli_query($link, $requet);
$rst_cons = mysqli_fetch_array($reponse);                     
$num_etu=$rst_cons['num_etu']; $type=$rst_cons['type']; $contenu=$rst_cons['contenu'];
$telephone=$rst_cons['contact'];$datesys=$rst_cons['datesys'];  

 ?> 							
                            
                            <tr>
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $type ; ?></td>
                                <td><?php echo $contenu ;?></td> 
								<td><?php echo $telephone ;?></td> 
								<!--td><?php //echo $email ;?></td--> 
								<td><?php echo $datesys ;?></td> 
                            </tr>
                         															
                            </tbody>
							
							
							 </table> 
							 
							 <br>
							 
							 <table border="1">									
							 <thead>Informations de la Base de Donnees
                            <tr>
                                <th>Carte</th>
								<th>Nom</th>
                                <th>Date_Nais</th>
                                <th>C.N.I</th>
								<th>Telephone</th>
								<th>Compte_cree?</th>
								<th>Compte_actif?</th>
                            </tr>
                            </thead>
							
							
														<tbody>
<?php  

$requet=("SELECT * FROM `codif_etudiant` WHERE num_etu='$num_etu'");
$reponse = mysqli_query($link, $requet);
$rst_cons = mysqli_fetch_array($reponse);                     
$nom=$rst_cons['nom']; $prenoms=$rst_cons['prenoms'];$nom=$prenoms." ".$nom;
$dateNaissance=$rst_cons['dateNaissance'];$numIdentite=$rst_cons['numIdentite'];  $telephone=$rst_cons['telephone'];  

$rr=("SELECT * FROM `codif_user` WHERE username_user='$num_etu'");
$ee = mysqli_query($link, $rr);

$datecreation="";
$type_mdp="";

if($cpt = mysqli_num_rows($ee))
{	
$st = mysqli_fetch_array($ee); 
$datecreation=$st['datesys'];  $type_mdp=$st['type_mdp']; $is_active=$st['is_active']; 
} 
?>						
                            
                            <tr>
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $nom ; ?></td>
                                <td><?php echo $dateNaissance ;?></td> 
								<td><?php echo $numIdentite ;?></td> 
								<td><?php echo $telephone ;?></td> 
								<td><?php echo $datecreation." ".$type_mdp ; ?></td> 
								<td><?php echo $is_active ;?></td>
                            </tr>
                         															
                            </tbody>	
							
							
							
                    </table>

                </div>

            </div>
          
        </div> <!-- end row -->
	  </section> <!-- end styles -->

<center>
<a href="javascript:history.back()" id="retour" >Retour</a><br><br>
<?php
//$data=$_SESSION['sag'] ;
//$privil=$data['username_user'];
//if($privil=='dba')
//{
?>
<a href="rcl3?var=mi&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">Modifier infos</a><br>
<a href="rcl3?var=su&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">Supprimer user</a><br>
<a href="rcl3?var=sms&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">SMS</a><br>
<a href="rcl3?var=cl&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">Archiver</a>
<?php
//}

//include('../foot.html');
?>
</center>

</body>

</html>