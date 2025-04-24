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
 
 
 			/*$data=$_SESSION['sag'] ;
			$login=$data['login']; 

$niveauFormation=info($login)['7'];
$sexe=info($login)['11'];
$sexeL=info($login)['12'];

$rang=rang($login); 
$statut=quota_statut($login)['1'];*/

//Fin
?>


<section id="homedesigne" class="s-homedesigne">   
<p class="lead">
<?php
echo "Espace S.A.G: Bienvenue!";
 ?>
</p>
</section> <!-- end s-stats -->

<!--form>
<center>
<input  type="search" name="q" placeholder="Entrer numero carte..." />
<input type="submit" value="Valider" />
</center>
</form-->
	
  <section id="styles" class="s-styles">
    

	  
	  
	   <div class="row add-bottom">

            <div class="col-twelve">

                <p>Affichage des Validations:choix de l'etablissement</p>

                <div class="table-responsive">
<form action='vl2'>	
<center>			
<table>

<tr>
<td align="center">Etablissement:</td>
      <td align="center"><span class="etoile" style="color:#ff6600;"></span>
        <SELECT name="et"  required>  <option ></option>
         		  <?php 
      
      $identite="SELECT DiSTiNCT (etablissement) as etablissement FROM codif_etudiant ORDER BY etablissement ASC ";
	  $execution=mysqli_query($link,$identite);	
	  while($stokaj=mysqli_fetch_assoc($execution))  {
	  ?>
	        <option ><?php echo $stokaj['etablissement']; ?></option>
<?php }

?>
        </select>
</td>
</tr>


<tr>
<td align='CENTER'><input type="submit" value="Valider"></td>
</center>
</tr>
</table>
</form>
                   

                </div>

            </div>
          
        </div> <!-- end row -->
	  </section> <!-- end styles -->

    <?php
include('../foot.html');
?>

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