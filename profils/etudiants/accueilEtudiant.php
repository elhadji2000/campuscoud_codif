<?php session_start();   // Démarre une nouvelle session ou reprend une session existante


if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}
//connexion à la base de données
require('../../traitement/fonction.php');
// Sélectionnez les options à partir de la base de données avec une pagination
require('../../traitement/requete.php');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
     integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" 
     crossorigin="anonymous">
    <title>Guide</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="row">
      <div class="col-md-6">
        <ul class ="nav navbar-nav">
          <li class ="nav-item">
            <a class ="nav-link"><img src="../../assets/images/logo.png" alt="photo" style="width: 35%;"></a>
            </li>
            <li class ="nav-item">
            <a class ="nav-link" style="margin-top: -5%; font-size:10px"><strong>CAMPUSCOUD</strong> </a>
          </li>
        </ul>  
      <div>
    </div>  
  </nav>

    <div class="container">		
		 <div class="card-body">
         <div class="card text-center " style="background-color: blue; width:100%;">
          <h3 style="color:white">EXTRAIT DU REGLEMENT INTERIEUR</h3>
         </div>
     </div>
		
        <form action="traitementPolitique" method="post">
            <div id="sections">
                <div class="section">
                    <!-- <p><strong>Section 1 : Collecte des données</strong></p> -->
                    <p>
                        
						
						<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Règlement du Logement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 20px;
            margin-top: 20px;
        }
        ol {
            margin-left: 20px;
        }
        li {
            margin-bottom: 10px;
        }
        .note {
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- <h1>Règlement du Logement</h1> -->

    <h2><b>Article 16</b></h2>
    <ol>
	
	    <li>Le resident doit, à son installation, vérifier et signer l’inventaire du mobilier et de literie de la chambre qui lui est attribuée et dont il est entierement responsable.</li>
        <!--li>Le president doit, à son installation, vérifier et signer l’inventaire du mobilier et de literie de la chambre qui lui est attribuée et dont il est entierement responsable.</li-->
         <li>Toute entrée et toute sortie de mobilier personnel doit être autorisée par le service de l’hebergement, en rapport avec la comptabilité des matières.</li>
        <li>Le locataire ne peut :
            <ul>
                <!--li>ni héberger un autre étudiant, même bénéficiaire des œuvres.</li-->
                <li>ni sous-louer son lit ou sa chambre.</li>
                <li>ni céder son lit ou sa chambre.</li>
            </ul>
        </li>
		<li>Toute forme de commerce est interdite dans les chambres.</li>
    </ol>

    <h2><b>Article 17</b></h2>
    <p>Il est formellement interdit de recevoir des visites susceptibles de troubler l’ordre et la bonne renommée de la cité.</p>

    <h2><b>Article 18 (alinéa 3)</b></h2>
    <p>L’installation des appareils électroménagers et audiovisuels n’est pas autorisée.</p>
    <p>Toute violation de l’une des dispositions de ces articles sera sanctionnée par l’exclusion du contrevenant.</p>

    <p><b>NB:</b> Tout étudiant attributaire d’un lit dispose d’un délai limité afin d’effectuer les formalités requises pour l’hébergement. Passé ce délai, le lit sera réaffecté sans préavis.</p>

</body>
						
                    </p>
                    <!-- Ajoutez autant de contenu que nécessaire pour cette section -->
                    <input type="checkbox" id="section1" name="section1">
                    <label for="section1">Conditions lues et approuvées</label>
                    <div class="section">
                    </div>
                </div>
                <!-- Ajoutez autant de sections que nécessaire -->
            </div>
            <button type="submit" id="acceptButton" disabled>J'accepte</button>
        </form>
    </div>
    <script>
        // Récupérer toutes les cases à cocher
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const acceptButton = document.getElementById('acceptButton');

        // Vérifier si toutes les cases sont cochées
        function checkAllCheckboxes() {
            let allChecked = true;
            checkboxes.forEach(checkbox => {
                if (!checkbox.checked) {
                    allChecked = false;
                }
            });
            acceptButton.disabled = !allChecked; // Activer le bouton si toutes les cases sont cochées
        }
        // Ajouter des écouteurs d'événements pour les cases à cocher
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', checkAllCheckboxes);
        });
    </script>
</body>
</html>


