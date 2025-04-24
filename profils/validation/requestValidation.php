<?php session_start();
// Verifier la session si elle est actif, sinon on redirige vers la racine

if (empty($_SESSION['username']) && empty($_SESSION['mdp'])) {
    header('Location: /campuscoud.com/');
    exit();
}

include('../../traitement/fonction.php');

if (isset($_POST['numEtudiant'])) {
    $num_etu = $_POST['numEtudiant'];

    if (getIsForclu($num_etu)) {
        $queryString = http_build_query(['data' => getIsForclu($num_etu)]);
        header('Location: validation.php?erreurForclo=Etudiant(e) Forclos(e) !!!&statut=Forclos(e)&' . $queryString);
    } else {
        $dataStudentConnect = studentConnect($num_etu);
        if ($dataStudentConnect) {
            $moyenneStudentSearch = studentConnect($num_etu)['moyenne'];
            $sexeStudentSearch =  studentConnect($num_etu)['sexe'];
            $classeStudentSearch = studentConnect($num_etu)['niveauFormation'];
            $idEtuStudentSearch = studentConnect($num_etu)['id_etu'];
            $quotaClasseStudentConnecte = getQuotaClasse($classeStudentSearch, $sexeStudentSearch)['COUNT(*)'];
            // $dataStatutStudentSearch = getStatutByOneStudent($quotaClasseStudentConnecte, $classeStudentSearch, $sexeStudentSearch, $moyenneStudentSearch, $num_etu);
            $dataStatutStudentSearch = getOnestudentStatus($quotaClasseStudentConnecte, $classeStudentSearch, $sexeStudentSearch, $num_etu);
            $rangStudentSearch = $dataStatutStudentSearch['rang'];
            if ($dataStatutStudentSearch['statut'] == 'Attributaire') {
                // Appel de la fonction de verification si l'etudiant a deja choisi un lit
                $data = getOneByAffectation($num_etu);
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_array($data)) {
                        $array = $row;
                    }
                    if ($array['migration_status'] == 'Non migré') {
                        $queryString = http_build_query(['data' => $array]);
                        header("location: validation.php?" . $queryString);
                        exit();
                    } else {
                        $queryString = http_build_query(['data' => $array]);
                        header('Location: validation.php?erreurValider=Etudiant Titulaire déja validé !&' . $queryString);
                        exit();
                    }
                } else {
                    header("location: validation.php?erreurNonTrouver=Etudiant Attributaire, mais n'ayant pas encore fait le choix de lit !!!");
                }
                // Libérer la mémoire du résultat
                mysqli_free_result($data);
            } else if ($dataStatutStudentSearch['statut'] == 'Suppleant(e)') {
                // numero carte etudiant du Suppleant(e)
                $numEtudiantSuppleant = $_SESSION['numEtudiantSuppleant'] = $dataStatutStudentSearch['num_etu'];
                // les informations de l'etudiant titulaire son statut inclu
                $statutTitulaireOfStudentSearch = getStatutByOneStudentTitulaireOfSuppl($quotaClasseStudentConnecte, $classeStudentSearch, $sexeStudentSearch, $rangStudentSearch);
                // numero carte etudiant du titulaire
                $numStudentTitulaireOfSuppleant = $statutTitulaireOfStudentSearch['num_etu'];
                //  fonction pour verifier si le titulaire a deja choisi son lit
                $data = getOneByAffectation($numStudentTitulaireOfSuppleant);
                // si la condition est verifier, c'est a dire le titulaire a deja choisi son lit
                if (mysqli_num_rows($data) > 0) {
                    while ($row = mysqli_fetch_array($data)) {
                        $arrayTitulaire = $row;
                    }
                    // verification si le titulaire a valider son codification
                    $dataValiteTitulaire = getOneByValidate($numStudentTitulaireOfSuppleant);
                    if (mysqli_num_rows($dataValiteTitulaire) > 0) {
                        // Appel de la fonction pour verifier si le Suppleant(e) a deja choisi son lit ou pas encore
                        $dataSuppleantIfChoiseLit = getOneByAffectation($numEtudiantSuppleant);
                        // Si le resultat de mysqli_num_rows($dataSuppleantIfChoiseLit) est superieur à zero c'est dire, le Suppleant(e) à deja choisi son lit
                        if (mysqli_num_rows($dataSuppleantIfChoiseLit) > 0) {
                            while ($rowSuppleant = mysqli_fetch_array($dataSuppleantIfChoiseLit)) {
                                $arraySuppleant = $rowSuppleant;
                            }
                            if ($arraySuppleant['migration_status'] == 'Non migré') {
                                $queryString = http_build_query(['data' => $arraySuppleant]);
                                header("location: validation.php?erreurValider=Lit Suppleant(e) non encore validé !!!&" . $queryString);
                                exit();
                            } else {
                                $queryString = http_build_query(['data' => $arraySuppleant]);
                                header('Location: validation.php?erreurValider=Lit Suppleant(e) déja validé !!!&' . $queryString);
                                exit();
                            }
                            // Si le resultat de mysqli_num_rows($dataSuppleantIfChoiseLit) est inferieur à zero c'est dire, le Suppleant(e) n'en pas choisi son lit
                        } else {
                            if ($arrayTitulaire['id_lit']) {
                                $idLitTitulaireOnSuppleant = $arrayTitulaire['id_lit'];
                                // Affecter le lit du titulaire à son Suppleant(e)
                                // $resulotatAffectationSuppleant = addAffectationOnSuppleant($idLitTitulaireOnSuppleant, $idEtuStudentSearch);
                                $queryString = http_build_query(['data' => $dataStudentConnect]);
                                header("location: validation.php?statut=Suppleant(e)&idLit=" . $idLitTitulaireOnSuppleant . '&' . $queryString);
                                exit();
                            }
                        }
                    } else {
                        header("location: validation.php?erreurNonTrouver=Votre titulaire a choisi un lit mais n'a pas encore validé, veuillez patienter !");
                        exit();
                    }
                } else {
                    header("location: validation.php?erreurNonTrouver=Votre titulaire n'a pas encore choisi de lit, veuillez patienter !");
                }
                // Libérer la mémoire du résultat
                mysqli_free_result($data);
            } else {
                header("location: validation.php?erreurNonTrouver=Etudiant Non Attributaire !");
            }
        } else {
            header("location: validation.php?erreurNonTrouver=Etudiant non trouvé dans la base de données !");
        }
    }
}

if (isset($_POST['valide'])) {
    try {
        $id_aff = $_POST['valide'];
        $user = $_SESSION['username'];
        // Appel de la fonction d'enregistrement de la validation du lit
        $requete = setValidation($id_aff, $user);
        print_r($requete);
        if ($requete == 1) {
            header('Location: validation.php?successValider=Codification validee avec success !!!');
        }
    } catch (mysqli_sql_exception $e) {
        header('Location: validation.php?erreurValider=Etudiant déja valider !!!');
    }
} elseif (isset($_POST['idLit']) && isset($_POST['id_etu'])) {
    $idLit = $_POST['idLit'];
    $idEtudiantSuppleant = $_POST['id_etu'];
    $numEtudiantSuppleant = $_SESSION['numEtudiantSuppleant'];
    // Affecter le lit du titulaire à son Suppleant(e)
    $resulotatAffectationSuppleant = addAffectationOnSuppleant($idLit, $idEtudiantSuppleant);
    if ($resulotatAffectationSuppleant == 1) {
        $dataSuppleantAffectation = getOneByAffectation($numEtudiantSuppleant);
        $user = $_SESSION['username'];
        if (mysqli_num_rows($dataSuppleantAffectation) > 0) {
            while ($rowSuppleantAff = mysqli_fetch_array($dataSuppleantAffectation)) {
                $arraySuppleant = $rowSuppleantAff;
            }
            unset($_SESSION['numEtudiantSuppleant']);
        }
        $id_aff = $arraySuppleant[0];
        $requete = setValidation($id_aff, $user);
        if ($requete == 1) {
            header('Location: validation.php?successValider=Suppleant(e) validé avec success !!!');
        }
    }
}
