<html class="no-js" lang="en">
<?php 

include('head.html');
include('activite.php');
?>

	<section id="homedesigne" class="s-homedesigne">
        
       <p class="lead">Bienvenue dans l'espace de suivi des reclamations!</p>
    </section> <!-- end s-stats -->
	
  <section id="styles" class="s-styles">
    
        <div class="row narrow section-intro add-bottom text-center">

            

        </div>
	  
	  
	   <div class="row add-bottom">

            <div class="row contact__main">
            <div class="col-eight tab-full contact__form1">
                <form name="contactForm" id="contactForm" method="post" action="s8rc2">
<?php

if (isset($_SERVER['HTTPS']) &&    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') 
	{
  $protocol = 'https';
}
else {
  $protocol = 'http';
}
?>

<!--script type="text/javascript"> 
function noBack(){window.history.forward()} 
noBack(); 
window.onload=noBack; 
window.onpageshow=function(evt){if(evt.persisted)noBack()} 
window.onunload=function(){void(0)} 
</script-->
<body>
<form action="s8rc2" method="post">

<tr>
    <td colspan="4"><center>
        <strong>VEUILLEZ SAISIR VOTRE NUMERO DE RECLAMATION</strong>
    </center>
    </td>
</tr>

 <fieldset>
    
                    <div class="form-field">
                        <input name="num_reclam" required type="text" id="num_reclam" placeholder="Numero de reclamation" value="" class="full-width">
                    </div>

                    <div class="form-field">
                        <button class="full-width btn--primary">Visualiser ma reclamation</button>
                        <div class="submit-loader">
                            <div class="text-loader">Création de compte encours...</div>
                            <div class="s-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                    </div>
    <br>
						 <a href='index'>Retour à la page d'accueil</a> <br>
                    </fieldset>

</html>
