<?php session_start();
if(! isset( $_SESSION['sag'] ) )
{header("location: ../");}		 // deconnexion();

require_once('../../traitement/fonction.php'); ?>
  
<html lang="en">

<?php 
//if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=../">'; exit();}  

include('head.html');	  
   /* include ('../../fonction.php');*/
    $link =  connexionBD();
		  

include('../../activite.php'); 

	
verif_type_mdp_2($_SESSION['username']);	
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

                <p>Liste des Reclamations Etudiants</p>

                <div class="table-responsive">
                    <table border="1">
                            <thead>
                            <tr>
							     <th>Numero</th>
                                <th>Carte</th>
								<th>Type</th>
                                <th>Contenu</th>
								<th>Action</th>
								<th>Date</th>
                            </tr>
                            </thead>
							<tbody>
<?php  

	  $requet=("SELECT DISTINCT(num_etu) as num_etu FROM `codif_reclamation` WHERE statut is NULL  GROUP by codif_reclamation.numreclame asc, codif_reclamation.num_etu asc");

$reponse = mysqli_query($link, $requet);//die;
while($rst_cons = mysqli_fetch_array($reponse))
{                     
$num_etu=$rst_cons['num_etu']; 

$rr="SELECT * FROM `codif_reclamation` WHERE num_etu='$num_etu' order by datesys desc";
$rp = mysqli_query($link, $rr);
$st = mysqli_fetch_array($rp);

$type=$st['type']; $contenu=$st['contenu'];$numreclame=$st['numreclame']; $datesys=$st['datesys'];

 ?> 							
                            
                            <tr>
							    <td><?php echo $numreclame ;?></td>
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $type ; ?></td>
                                <td><?php echo $contenu ;?></td> 
								<td><a href="rcl2?nr=<?php echo $numreclame; ?>">Traiter</a></td>  
								<td><?php echo $datesys ;?></td> 
                            </tr>
                         							
<?php
	  }
 ?> 								
                            </tbody>
                    </table>

                </div>

            </div>
          
        </div> <!-- end row -->
	  </section> <!-- end styles -->

    <?php
include('../../foot.html'); 
?>


</body>

</html>