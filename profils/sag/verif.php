<?php session_start(); ?>  
<html lang="en">

<?php
if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=../">'; exit();}  
include('head.html');	  
    include ('../fonction.php');
    $link = connexionBD();
		  

if(! isset( $_SESSION['sag'] ) )
{header("location: ../");}		 // deconnexion();

 include('../activite.php'); 
 
 
 		/*	$data=$_SESSION['sag'] ;
			$login=$data['login']; 

$niveauFormation=info($login)['7'];
$sexe=info($login)['11'];
$sexeL=info($login)['12'];

$rang=rang($login); 
$statut=quota_statut($login)['1'];*/
?>


<section id="homedesigne" class="s-homedesigne">   
<p class="lead">
<?php
echo "Espace S.A.G: Bienvenue!";
 ?>
</p>
</section>  <!-- end s-stats -->

<!--form>
<center>
<input  type="search" name="q" placeholder="Entrer numero carte..." />
<input type="submit" value="Valider" />
</center>
</form-->
  <section id="styles" class="s-styles">	  
	   <div class="row add-bottom">
            <div class="col-twelve">
                <p><b>Verifier l'existence d'un etudiant !!!</b></p>

                <div class="table-responsive">
<form>				
<table> <tr>
<center>
<td align='right'><input required type="search" name="q" placeholder="Numero de carte ..."></td>
<td align='left'><input type="submit" value="Rechercher"></td>
</center>
</tr></table>
</form>	
                    <table>
                            <thead>
                            <tr>
							    
                                <th>Carte</th>
								<th>Nom</th>
                                <th>Prenom</th>
								<th>Classe</th>
                                <th>Moyenne</th>
								<th>Rang</th>
                            </tr>
                            </thead>
							<tbody>
<?php							

  if (isset($_GET['q']) and !empty($_GET['q']))
  {
	  $q=htmlspecialchars($_GET['q']);
	  $requet=("SELECT * FROM  `codif_etudiant` where num_etu ='$q' order by sessionId ASC,  moyenne desc, id_etu asc");
  
                                 
							 
$reponse = mysqli_query($link, $requet);
$cpt = mysqli_num_rows($reponse);
if(!$cpt)
{
	echo "<center><h3><font color='red'>Etudiant, avec le numero de carte '$q', introuvable!</font></h3> </center>";
	//exit();
}
      while($rst_cons = mysqli_fetch_array($reponse)){
                   
$num_etu=$rst_cons['num_etu'];
$nom=$rst_cons['nom'];
$prenom=$rst_cons['prenoms'];
$moyenne=$rst_cons['moyenne'];
$niveauFormation=$rst_cons['niveauFormation'];
$etablissement=$rst_cons['etablissement'];                                            
 ?> 
							
                            
                            <tr>
							    
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $nom ;?></td>
                                <td><?php echo $prenom ;?></td> 
								<td><?php echo $niveauFormation."/".$etablissement ; ?></td>
									 <td><?php echo $moyenne ;?></td> 
								<td><?php echo rang($num_etu) ;?></td>
                            </tr>
                         							
<?php
	  }}
 ?> 								
                            </tbody>
                    </table>

                </div>

            </div>
          
        </div> <!-- end row -->
	  </section> <!-- end styles -->


    
<?php

include('../foot.html');
?> <!-- end s-stats -->




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
                    <span>© Copyright COUD</span> 
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