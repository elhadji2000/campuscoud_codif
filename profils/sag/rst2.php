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
 
 if (isset($_GET['et'])){
		$etablissement = $_GET['et'];
	} elseif (isset($_POST['et'])){
		$etablissement = $_POST['et'];
	}else {
		$etablissement  = null;
	}
	
	
	
	//echo "etab".$etablissement;exit();
 	
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

                <p>Resultats par NiveauFormation et par Sexe</p>

                <div class="table-responsive">
<form>	
<center>			
<table>

<tr>
<td align="center">NiveauFormation:</td>
      <td align="center"><span class="etoile" style="color:#ff6600;"></span>
        <SELECT name="q"  required>  <option ></option>
         		  <?php 
				  
 if (isset($_GET['q3'])){
		$etablissement = $_GET['q3'];
	} elseif (isset($_POST['q3'])){
		$etablissement = $_POST['q3'];
	}/*else {
		$etablissement  = null;
	} */  
	
      $identite="SELECT DiSTiNCT (niveauFormation) as niveau FROM codif_etudiant where
	  etablissement='$etablissement' ORDER BY niveauFormation ASC ";
	  $execution=mysqli_query($link,$identite);	
	  while($stokaj=mysqli_fetch_assoc($execution))  {
	  ?>
	        <option ><?php echo $stokaj['niveau']; ?></option>
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
  <input type="hidden" name="q3" value="<?php echo $etablissement;?>">	 
<tr>
<td align='CENTER'><input type="submit" value="Rechercher"></td>
</center>
</tr>
</table>
</form>
                    <table>
                            <thead>
                            <tr>
							     <th>Ordre</th>
                                <th>Carte</th>
								<th>Nom</th>
                                <th>Prenom</th>
								<!--th>Date_naissance</th-->
								<!--th>Session</th-->
                                <th>Moyenne</th>
								<!--th>Rang</th-->
								<th>Statut</th>
                            </tr>
                            </thead>
							<tbody>
<?php  

  if (isset($_GET['q']) and !empty($_GET['q']))
  {   
$niveauFormation=htmlspecialchars($_GET['q']);
$sexe=htmlspecialchars($_GET['q2']); $sexeL="";if($sexe=="G"){$sexeL="Garçons";}	if($sexe=="F"){$sexeL="Filles";}

	  $requet=("SELECT * FROM  `codif_etudiant` where `niveauFormation` = '$niveauFormation' and sexe='$sexe' order by sessionId ASC,moyenne desc,dateNaissance desc,  id_etu asc");
  }

//echo $requet;exit();
$reponse = mysqli_query($link, $requet);
$ordre=0; 
echo $niveauFormation." / ".$sexeL;
while($rst_cons = mysqli_fetch_array($reponse))
{
$ordre+=1;                     
$num_etu=$rst_cons['num_etu']; $nom=$rst_cons['nom']; $dateNaissance=$rst_cons['dateNaissance'];
$prenoms=$rst_cons['prenoms'];$moyenne=$rst_cons['moyenne'];  $sessionId=$rst_cons['sessionId'];  
                                            $rang=rang($num_etu);
 ?> 							
                            
                            <tr>
							    <td><?php echo $ordre ;?></td>
                                <td><?php echo $num_etu ;?></td>
                                <td><?php echo $nom ; ?></td>
                                <td><?php echo $prenoms ;?></td> 
								<!--td><?php //echo $dateNaissance ;?></td--> 
								<!--td><?php //echo $sessionId ;?></td--> 
								<td><?php echo $moyenne ;?></td><!--td><?php //echo $rang ;?></td-->
								<td><?php $statut2=quota_statut($num_etu)['1']; echo $statut2 ;?></td> 
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