<html lang="en">

<?php
if (!isset($_POST['num_reclam']))
{echo '<meta http-equiv="refresh" content="0;URL=index">'; exit();}

include('head.html');

if($_SERVER["HTTP_REFERER"]==""){echo '<meta http-equiv="refresh" content="0;URL=">'; exit();}  

include('head.html');	  
    include ('connexion.php');
	include('activite.php');
    $link = maconnexion();	
	
	if (isset($_POST['num_reclam'])){
		$num_reclam= $_POST['num_reclam'];
	} else {
		$num_reclam =  null;
	} 
?>
<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace de suivi des reclamations!</p>
    </section> <!-- end s-stats -->

<section id="homedesigne" class="s-homedesigne">   
<p class="lead">
</p>
</section> <!-- end s-stats -->
<?php  
$rr ="select numreclame from codif_reclamation where numreclame='$num_reclam'";    
        $ee = mysqli_query($link, $rr) ;$n_rows = mysqli_num_rows($ee);
        if(!$n_rows)
        {
			?>
                 <script langage='javascript'>
                 alert('Désolé, ce numero de reclamation nexiste pas!')
                 </script>
                 <?php echo '<meta http-equiv="refresh" content="0;URL=s8rc">';
                     exit();
		}
?>		
		
  <section id="styles" class="s-styles">	  
	   <div class="row add-bottom">
            <div class="col-twelve">
                <p><b>Evolution de la reclamation N° <?php echo $num_reclam; ?></b></p>

                <div class="table-responsive">	
                    <table>
                            <thead>
                            <tr>
							    <th>Action</th>
                                <th>Date</th>
								<th>Libelle</th>
                            </tr>
                            </thead>
							<tbody>
<?php	
	  $requet = ("SELECT * FROM  `codif_reclamation_evolue` where numreclame='$num_reclam' order by datesys desc");
                                  
						 
$reponse = mysqli_query($link, $requet);
      while($rst_cons = mysqli_fetch_array($reponse)){
                    ;
$numauto=$rst_cons['numauto'];
$contenu=$rst_cons['contenu'];
$date=$rst_cons['datesys'];
                                      
 ?> 
							
                            
                            <tr>
							<td><?php echo $numauto ;?></td> 
							   <td><?php echo $date ;?></td> 
                               <td><?php echo $contenu;?></td> 
							   
                                
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
include('foot.html');
?> <!-- end s-stats -->




    <!-- footer
    ================================================== -->
	<br><br>
						<center> <a href='s8rc'>Retour</a> </center> <br>
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="images/logo.png" alt="Homepage"></a>
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
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

</body>

</html>