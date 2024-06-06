<?php
if (session_id() == '') {
    session_start();
}
include_once  'helper/suche.php';
include_once 'helper/form_functions.php';
include_once 'helper/database_functions.php';

global $conn;
$tableArtikel = 'artikel';

$validation = true;

$query = 'Select id as value, concat(groesse,farbe,speicher) as label from Artikel_Variante;';
$stmt = $conn->prepare($query);
$stmt->execute();
// wird die daten in einem array speichern
$artikelvariantenListe = $stmt->fetchAll(PDO::FETCH_ASSOC);

 //Validation
 $validationArtikelnummerBeschreibung = "Bitte Artikelnummer auswählen.";
 $validationPreisBeschreibung = "Bitte Preis auswählen.";
 $validationLagerBeschreibung = "Bitte Zahl eingeben.";
 $validationBeschreibung= "Bitte Beschreibung eingeben.";
 $validationNameBeschreibung = "Bitte Artikelname eingeben.";
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sammle und verarbeite hier die Formulardaten
    //Hier bekomme ich den Wert von dem Input-Attribut
    $artikelnummer = getPostParameter("artikelnummer");
    $preis = getPostParameter("preis");
    $lagerbestand = getPostParameter("lagerbestand");
    $beschreibung = getPostParameter("beschreibung");
    $name = getPostParameter("name");
    $artikel_variante = getPostParameter("artikel_variante");
    
    if($artikelnummer < 0){
        $validationArtikelnummer = false;
        $artikelnummerMessage = "Artikelnummer: Bitte keine negativen Zahlen eingeben.";
        echo '<div class="alert alert-warning">'. $artikelnummerMessage . '</div>';
    }

    if($preis < 0){
        $validation = false;
        $validationPreis = "Preis: Bitte keine negativen Zahlen eingeben.";
        echo '<div class="alert alert-warning">'. $validationPreis . '</div>';
    }

    if($lagerbestand < 0){
        $validation = false;
        $validationLager = "Lager: Bitte keine negativen Zahlen eingeben.";
        echo '<div class="alert alert-warning">'. $validationLager . '</div>';
    }
    
    $insertQuery = 'Insert into artikel (artikelnummer,preis,lagerbestand,beschreibung,name,Artikel_Variante_Id) values(?,?,?,?,?,?)';

    if($validation){
        $stmt = $conn -> prepare($insertQuery);
        $insertParameter = [$artikelnummer,$preis,$lagerbestand,$beschreibung,$name,$artikel_variante];
        $stmt -> execute($insertParameter);
        $success = true;
    }

}
?>

<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4 shadow">
    <h2>Formular Artikel erstellen</h2>
    <form class="needs-validation" action="" method="post" novalidate>
        <div class="row m-3">
            <label for="validationArtikelnummer" class="col-md-6" for="artikelnummer" novalidate>Artikelnummer</label>
            <input type="number" name ="artikelnummer" class="form-control" placeholder="Artikelnummer" id="validationArtikelnummer" required>
            <?php echo inputValidation($validationArtikelnummerBeschreibung) ?>
        </div>
        <div class="row m-3">
            <label for="validationArtikelname" class="col-md-6" for="name">Artikelname</label>
            <input type="text" name ="name" class="form-control" placeholder="name" id="validationArtikelname" required>
            <?php echo inputValidation($validationNameBeschreibung) ?>      
        </div>
        <div class="row m-3">
            <label for="validationPreis" class="col-md-6" for="preis">Preis</label>
            <input type="number" name ="preis" class="form-control" placeholder="preis" id="validationPreis" required>
            <?php echo inputValidation($validationPreisBeschreibung) ?>
        </div>
        <div class="row m-3">
            <label for="validationLager" class="col-md-6" for="lagerbestand">Lagerbestand</label>
            <input type="number" name ="lagerbestand" class="form-control" placeholder="lagerbestand" id="validationLager" required>
            <?php echo inputValidation($validationLagerBeschreibung) ?>
        </div>
        <div class="row m-3">
            <label for="validationBeschreibung" class="col-md-6" for="beschreibung">Beschreibung</label>
            <input type="text" name ="beschreibung" class="form-control" placeholder="beschreibung" id="validationBeschreibung" required>
            <?php echo inputValidation($validationBeschreibung) ?>
        </div>
        <div class="form-group">
            <label for="etage">Artikelvariante</label>
            <?php echo createDropdown('artikel_variante', $artikelvariantenListe); ?>
        </div>
        <div class="mt-3 justify-content-end d-flex">
            <button type="submit" class="btn btn-success align-items-end">Erstellen</button>
        </div>
    </form>
   
<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
</script>