// Fonction pour définir un cookie
function setCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

// Fonction pour récupérer la valeur d'un cookie
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(";");
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function populateData2() {
  var selectFac = document.getElementById("selectFac");
  var selectDep = document.getElementById("selectDep");
  var selectClasse = document.getElementById("selectClasse");
  var fac = selectFac.value;
  var dep = selectDep.value;
  var classe = selectClasse.value;
  // selectData.innerHTML = "";

  var newURL = "niveau2.php?fac=" + fac + "&dep=" + dep + "&classe=" + classe+"&actu=0";
  window.history.pushState({ path: newURL }, "", newURL);

  // Sauvegarder la sélection dans un cookie
  setCookie("lastSelectedfac", fac, 30); // Le cookie expire dans 30 jours
  setCookie("lastSelectedep", dep, 30); // Le cookie expire dans 30 jours
  setCookie("lastSelecteClasse", classe, 30); // Le cookie expire dans 30 jours
}

// Au chargement initial, peuple les données en fonction de la valeur sélectionnée par défaut ou en fonction du cookie
window.onload = function () {
  var lastSelectedfac = getCookie("lastSelectedfac");
  var lastSelectedep = getCookie("lastSelectedep");
  var lastSelecteClasse = getCookie("lastSelecteClasse");
  var selectFac = document.getElementById("selectFac");
  var selectDep = document.getElementById("selectDep");
  var selectClasse = document.getElementById("selectClasse");
  if (lastSelectedfac) {
    selectFac.value = lastSelectedfac;
  }
  if (lastSelectedep) {
    selectDep.value = lastSelectedep;
  }
  if (lastSelecteClasse) {
    selectClasse.value = lastSelecteClasse;
  }
  populateData2();
  selectFac.addEventListener("change", function () {
    populateData2();
    document.getElementById("selectForm").submit();
  });
  // populateData2();
  selectDep.addEventListener("change", function () {
    populateData2();
    document.getElementById("selectForm").submit();
  });
  // populateData2();
  selectClasse.addEventListener("change", function () {
    populateData2();
    document.getElementById("selectForm").submit();
  });
};
