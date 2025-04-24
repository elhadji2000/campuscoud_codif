<?php session_start(); ?>  
<html lang="en">

<?php 
if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=../">'; exit();}  
include('head.html');	  
    include ('../connexion.php');
    $link = maconnexion();
		  

if(! isset( $_SESSION['sag'] ) )
{header("location: ../");}		 // deconnexion();

 include('../activite.php'); 
 
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
$telephone=$rst_cons['contact'];$email=$rst_cons['email'];$datesys=$rst_cons['datesys'];  

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
								<!--th>Email</th-->
								<th>Compte cree?</th>
                            </tr>
                            </thead>
							
							
														<tbody>
<?php  

$requet=("SELECT * FROM `codif_etudiant` WHERE num_etu='$num_etu'");
$reponse = mysqli_query($link, $requet);
$rst_cons = mysqli_fetch_array($reponse);                     
$nom=$rst_cons['nom']; $prenoms=$rst_cons['prenoms'];$nom=$prenoms." ".$nom;
$dateNaissance=$rst_cons['dateNaissance'];$numIdentite=$rst_cons['numIdentite'];  $email_perso=$rst_cons['email_perso'];  

$rr=("SELECT * FROM `codif_user` WHERE login='$num_etu'");
$ee = mysqli_query($link, $rr);
$cpt = mysqli_num_rows($ee);	?>						
                            
                            <tr>
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $nom ; ?></td>
                                <td><?php echo $dateNaissance ;?></td> 
								<td><?php echo $numIdentite ;?></td> 
								<!--td><?php //echo $email_perso ;?></td--> 
								<td><?php if($cpt==1){ echo "Oui" ;} else {echo "Non";}?></td> 
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
$data=$_SESSION['sag'] ;
$login=$data['login'];
if($login=='dba')
{
?>
<a href="rcl3?var=mi&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">Modifier infos</a><br>
<a href="rcl3?var=su&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">Supprimer user</a><br>
<a href="rcl3?var=sms&ne=<?php echo $num_etu; ?>&nr=<?php echo $numreclame; ?>">SMS</a>
<!--a href="rcl3?var=cl&ne=<?php //echo $num_etu; ?>&nr=<?php //echo $numreclame; ?>">Cloturer</a-->
<?php
}

include('../foot.html');
?>
</center>
    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="../images/logo.png" alt="Homepage"></a>
                </div>

               
                    
            </div>
        </div>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>© Copyright COUD@2021</span> 
                </div>

                <div class="go-top">
                <a class="smoothscroll" title="Back to Top" href="#top"><i class="im im-arrow-up" aria-hidden="true"></i></a>
                </div>
            </div>

        </div> <!-- end footer-bottom -->

    </footer> <!-- end footer -->


    <!-- photoswipe background
    ================================================== -->
    <div aria-hidden="true" class="pswp" role="dialog" tabindex="-1">

        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">

            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>

            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title=
                    "Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title=
                    "Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button> <button class="pswp__button pswp__button--arrow--right" title=
                "Next (arrow right)"></button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>

        </div>

    </div><!-- end photoSwipe background -->

    <div id="preloader">
        <div id="loader"></div>
    </div>

    <!-- Java Script
    ================================================== -->
    <script src="../js/jquery-3.2.1.min.js"></script>
    <script src="../js/plugins.js"></script>
    <script src="../js/main.js"></script>

</body>

</html>