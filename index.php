<?php session_start();

if(isset( $_SESSION['username']))
{ session_destroy();}


include('head.html');



/*echo '<meta http-equiv="refresh" content="0;URL=https://campuscoud.com">';
exit();*/
?>

 <!--script langage='javascript'>
                 alert('Information: \nLes  etudiants etant toujours bloques dans le processus par la non disponibilite du mot de passe de leur email institutionnel peuvent envoyer leurs identifiants par WhatsApp au 77.708.98.12 pour obtenir un mot de passe (par defaut) et acceder directement dans CAMPUSCOUD.\n\nNous vous rappelons quapres retrait des conventions dhebergement, il est obligatoire de valider son attribution avant de pouvoir poursuivre le reste de la procedure.\n\nPlus dinfos:\nInformatique: 77.708.98.12 (Message WhatsApp Uniquement) / 76.878.53.88\nHerbergement: 78.539.26.92 / 78.539.02.73 ')
                 </script-->
				 
			
				 
				 <!--script langage='javascript'>
                 alert('Aucune codification nest en cours sur la plateforme!!!')
                 </script-->
				 

<!DOCTYPE html>
<!--[if lt IE 9 ]><html class="no-js oldie" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="no-js oldie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><-->
<!--html class="no-js" lang="en"-->
<html  lang="en">

   <!-- home
   ================================================== -->
<section id="home" class="s-home page-hero target-section" data-parallax="scroll" data-image-src="assets/images/hero-bg.jpg" data-natural-width="3000" data-natural-height="2000" data-position-y="center">

        <div class="overlay"></div>
        <div class="shadow-overlay"></div>

        <div class="home-content">

            <div class="row home-content__main">

                <h3>CODIFICATION 2024-2025 (EN COURS ...)</h3>

                <h1>
                    Bienvenue dans CAMPUSCOUD<br>
                    votre plateforme numerique<br>
                    pour la codification!
                </h1>

                <div class="home-content__buttons">
                    
                    <!--a href="quota" class="smoothscroll5 btn btn--stroke">VOIR LES QUOTAS</a-->
				<a href="guide" class="smoothscroll5 btn btn--stroke">GUIDE D'UTILISATION</a>
                <a href="insc" class="smoothscroll5 btn btn--stroke"> CREER UN COMPTE</a>
				<a href="log" class="smoothscroll6 btn btn--stroke"> SE CONNECTER </a>
				</div>
		

                <div class="home-content__scroll">
                    <a href="#about" class="scroll-link smoothscroll">
                        <span>Defiler en bas</span>
                    </a>
                </div>

            </div>

        </div> <!-- end home-content -->

      

    </section> <!-- end s-home -->


    <!-- about
    ================================================== -->
    

    <!-- works
    ================================================== -->
    
    <!-- testimonials
    ================================================== -->


    <!-- s-cta
    ================================================== -->
   
    <!-- s-stats
    ================================================== -->

 <!-- end s-stats -->

<?php
include('foot.html');
?>
    <!-- s-stats
    ================================================== -->
    <!--section id="contact" class="s-contact target-section">

        <div class="overlay"></div>

        <div class="row narrow section-intro">
            <div class="col-full">
                <h3>Contact</h3>
                <h1>SERVICE HEBERGEMENT</h1>
                
                <p class="lead">Le Service Hebergement du COUD gere les logements des étudiants chaque année dans les differents sites de l'Université Cheikh Anta DIOP</p>
            </div>
        </div>

        <div class="row contact__main">
            <div class="col-eight tab-full contact__form">
                <form name="contactForm" id="contactForm" method="post" action="">
                    <fieldset>
    
                    <div class="form-field">
                        <input name="contactName" type="text" id="contactName" placeholder="Name" value="" minlength="2" required="" aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="contactEmail" type="email" id="contactEmail" placeholder="Email" value="" required="" aria-required="true" class="full-width">
                    </div>
                    <div class="form-field">
                        <input name="contactSubject" type="text" id="contactSubject" placeholder="Subject" value="" class="full-width">
                    </div>
                    <div class="form-field">
                        <textarea name="contactMessage" id="contactMessage" placeholder="message" rows="10" cols="50" required="" aria-required="true" class="full-width"></textarea>
                    </div>
                    <div class="form-field">
                        <button class="full-width btn--primary">Envoyez</button>
                        <div class="submit-loader">
                            <div class="text-loader">Envoi encours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    
                    </fieldset>
                </form-->

                <!-- contact-warning >
                <div class="message-warning">
                    Veiullez remplir tous les champs.
                </div> 
            
                <!-- contact-success >
                <div class="message-success">
                    Message envoyé, Merci!<br>
                </div-->
                        
            </div>
            <div class="col-four tab-full contact__infos">
                <!--h4 class="h06">Telephone</h4>
                <p>Fixe: (+221) 78 539 02 73<br>
                   Portable: (+221) 78 539 26 92<br>
                </p-->

                <h4 class="h06">Contact</h4>
                <p><a href='rc'>Espace Reclamation: cliquer ici</a><br>
                </p>

                <h4 class="h06">Adresses:</h4>
                <p>
                 <u>Hebergement:</u><br>
				 Rez de chaussee<br>
                 Pavillon E<br>
                 Grand Campus COUD
                </p>
				<p>
                 <u>Informatique:</u><br>
				 Rez de chaussee<br>
                 Pavillon B<br>
                 Grand Campus COUD
                </p>
            </div>

        </div>

    </section--> <!-- end s-contact -->


    <!-- footer
    ================================================== -->
    <footer>
        <div class="row">
            <div class="col-full">

                <div class="footer-logo">
                    <a class="footer-site-logo" href="#0"><img src="assets/images/logo.png" alt="Homepage"></a>
                </div>

               
                    
            </div>
        </div>

        <div class="row footer-bottom">

            <div class="col-twelve">
                <div class="copyright">
                    <span>&copy;Copyright Centre des Oeuvres universitaires de Dakar</span> 
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
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>