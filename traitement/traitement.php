<?php
include('fonction.php');
//########## POUR INSERER ################

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["form_name"]) && $_GET["form_name"] == "form_enregistrer_utilisateur") { 
    $prenom = isset($_GET["prenom"]) ? test_input($_GET["prenom"]) : "";
    $nom = isset($_GET["nom"]) ? test_input($_GET["nom"]) : "";
    $telephone = isset($_GET["telephone"]) ? test_input($_GET["telephone"]) : "";
    $var = isset($_GET["var"]) && !empty($_GET["var"]) ? test_input($_GET["var"]) : null;
    $sexe = isset($_GET["sexe"]) ? test_input($_GET["sexe"]) : "";
    $username = isset($_GET["username"]) ? test_input($_GET["username"]) : "";
    $prof = isset($_GET["prof"]) ? test_input($_GET["prof"]) : "";
    $pav = isset($_GET["pav"]) && !empty($_GET["pav"]) ? test_input($_GET["pav"]) : null;
    $campus = isset($_GET["campus"]) && !empty($_GET["campus"]) ? test_input($_GET["campus"]) : null;
    $fac = isset($_GET["fac"]) && !empty($_GET["fac"]) ? test_input($_GET["fac"]) : null;
    
    $telephone = str_replace(' ', '', $telephone);
    $username = str_replace(' ', '', $username);

    

    // Vérification des doublons pour username_user
    $sql_check = "SELECT COUNT(*) as count FROM codif_user WHERE username_user = ?";
    $stmt_check = mysqli_prepare($connexion, $sql_check);

    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "s", $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row = mysqli_fetch_assoc($result_check);

        if ($row['count'] > 0) {
            // Username déjà utilisé, afficher une alerte
            echo "<script>
                alert('Ce nom d\'utilisateur est déjà utilisé !');
                window.location.href = '../profils/dba/addUser';
            </script>";
            exit();
        }
        mysqli_stmt_close($stmt_check);
    } else {
        echo "<script>
            alert('Erreur lors de la vérification du nom d\'utilisateur.');
            window.location.href = '../profils/dba/addUser';
        </script>";
        exit();
    }

    // Appel de la fonction pour enregistrer l'utilisateur
    $result = enregistrerUtilisateur($connexion, $nom, $prenom, $var, $sexe, $telephone, $username, $prof, $pav, $campus, $fac);
    
    if ($result) {
        // Affichage d'un message de succès
        echo "<script>
            alert('Utilisateur enregistré avec succès !');
            window.location.href = '../profils/dba/addUser.php';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Erreur lors de l\'enregistrement de l\'utilisateur.');
            window.location.href = '../profils/dba/addUser.php';
        </script>";
        exit();
    }
}
// ############# POUR MODIFIER UN UTILISATEUR ###########################

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["form_name"]) && $_GET["form_name"] == "form_modifier_utilisateur" && isset($_GET["id_user"])) {
    $id_user = isset($_GET["id_user"]) ? intval($_GET["id_user"]) : 0;  // ID de l'utilisateur à modifier
    $prenom = isset($_GET["prenom"]) ? test_input($_GET["prenom"]) : "";
    $nom = isset($_GET["nom"]) ? test_input($_GET["nom"]) : "";
    $telephone = isset($_GET["telephone"]) ? test_input($_GET["telephone"]) : "";
    $var = isset($_GET["var"]) ? test_input($_GET["var"]) : "";
    $sexe = isset($_GET["sexe"]) ? test_input($_GET["sexe"]) : "";
    $username = isset($_GET["username"]) ? test_input($_GET["username"]) : "";
    $prof = isset($_GET["prof"]) ? test_input($_GET["prof"]) : "";
    $pav = isset($_GET["pav"]) && !empty($_GET["pav"]) ? test_input($_GET["pav"]) : null;
    $campus = isset($_GET["campus"]) && !empty($_GET["campus"]) ? test_input($_GET["campus"]) : null;
    $fac = isset($_GET["fac"]) && !empty($_GET["fac"]) ? test_input($_GET["fac"]) : null;

    $telephone = str_replace(' ', '', $telephone);
    $username = str_replace(' ', '', $username);

    // Vérification des doublons pour username_user, en excluant l'utilisateur actuel
$sql_check = "SELECT COUNT(*) as count FROM codif_user WHERE username_user = ? AND id_user != ?";
$stmt_check = mysqli_prepare($connexion, $sql_check);

if ($stmt_check) {
    // Liaison des paramètres : vérifie que le username est unique pour les autres utilisateurs
    mysqli_stmt_bind_param($stmt_check, "si", $username, $id_user);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row = mysqli_fetch_assoc($result_check);

    if ($row['count'] > 0) {
        // Username déjà utilisé par un autre utilisateur, afficher une alerte
        echo "<script>
            alert('Ce nom d\'utilisateur est déjà utilisé par un autre utilisateur !');
            window.location.href = '../profils/dba/addUser.php?id=$id_user';
        </script>";
        exit();
    }
    mysqli_stmt_close($stmt_check);
} else {
    echo "<script>
        alert('Erreur lors de la vérification du nom d\'utilisateur.');
        window.location.href = '../profils/dba/addUser.php?id=$id_user';
    </script>";
    exit();
}


    // Appel de la fonction pour modifier l'utilisateur
    $result = modifierUtilisateur($connexion, $id_user, $nom, $prenom, $var, $sexe, $telephone, $username, $prof, $pav, $campus, $fac);
    
    if ($result) {
        // Affichage d'un message de succès
        echo "<script>
            alert('Utilisateur modifié avec succès !');
            window.location.href = '../profils/dba/index.php?modifiè';
        </script>";
        exit();
    } else {
        echo "<script>
            alert('Erreur lors de la modification de l\'utilisateur.');
            window.location.href = '../profils/dba/addUser.php?id=$id_user';
        </script>";
        exit();
    }
}

?>