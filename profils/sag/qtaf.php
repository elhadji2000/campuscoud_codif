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
 
 
/* if (isset($_GET['etablissement'])){
		$etablissement = $_GET['etablissement'];
	} elseif (isset($_POST['etablissement'])){
		$etablissement = $_POST['etablissement'];
	}else {
		$etablissement  = null;
	}*/

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

                <p>Voir Quotas (Lits) par Etablissement et par Sexe</p>

                <div class="table-responsive">
<form>	
<center>			
<table>

<tr>
<td align="center">Etablissement:</td>
      <td align="center"><span class="etoile" style="color:#ff6600;"></span>
        <SELECT name="q"  required>  <option ></option>
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
<td align="center">Sexe:</td>
      <td align="center"><span class="etoile" style="color:#ff6600;"></span>
        <SELECT name="q2"  required>  <option ></option>

	        <option >G</option>
			 <option >F</option>
        </select>
</td>
</tr>

<tr>
<td align='CENTER'><input type="submit" value="Rechercher"></td>
</center>
</tr>
</table>
</form>
                                   <table>
                            <thead>
                            <tr>
							     <th>NiveauFormation</th>
                                <th>Nombre de lits</th>
								
                            </tr>
                            </thead>
							<tbody>
<?php   							

  if (isset($_GET['q']) and !empty($_GET['q']))
  {   
$etablissement=htmlspecialchars($_GET['q']);
$sexe=htmlspecialchars($_GET['q2']); $sexeL="";if($sexe=="G"){$sexeL="Garçons";}	if($sexe=="F"){$sexeL="Filles";}

	  $requet=("SELECT * FROM  `codif_quota` where sexe='$sexe' and niveauFormation in(select niveauFormation from codif_etudiant where etablissement='$etablissement') order by niveauFormation asc");
 
$reponse = mysqli_query($link, $requet);
$ordre=0; 
echo "Quota ".$etablissement." / ".$sexeL;
while($rst_cons = mysqli_fetch_array($reponse))

{
$ordre+=1;                     
$niveauFormation=$rst_cons['niveauFormation']; $nombre=$rst_cons['nombre'];
                                            
 ?> 
							
                            
                            <tr>
							    <td><?php echo $niveauFormation ;?></td>
                                <td><?php echo $nombre ;?></td>
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
 }

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