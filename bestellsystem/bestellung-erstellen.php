<?php
if (session_id() == '') {
    session_start();
}
include_once  'helper/suche.php';
include_once 'helper/form_functions.php';
include_once 'helper/database_functions.php';

global $conn;
$kundenliste ="";
$query = "select Id as value,concat(vorname,' ',nachname) as label from kunde;";
$stmt = $conn->prepare($query);
$stmt->execute();
// wird die daten in einem array speichern
$kundenliste = $stmt->fetchAll(PDO::FETCH_ASSOC);

$produktliste="";
$queryProdukte="select Id as value,name as label from artikel;";
$stmt = $conn->prepare($queryProdukte);
$stmt->execute();
$produktliste = $stmt->fetchAll(PDO::FETCH_ASSOC);

$inBearbeitung = 1;
$statusliste ="";
$query = "select Id as value,bezeichnung as label from status;";
$stmt = $conn->prepare($query);
$stmt->execute();
// wird die daten in einem array speichern
$statusliste = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Form
    $menge = 1;
    $kundenliste = getPostParameter("kunden","");
    $statusliste = getPostParameter("status","");
    $heute= new DateTime('now');
    $bestelldatum= $heute->format('Y-m-d');
    $ausgewaehlteProdukte = isset($_POST['produkte']) ? $_POST['produkte'] : [];
    $liefertermin = getPostParameter("liefertermin","");
    $gesamtpreis = 0;
    foreach($ausgewaehlteProdukte as $produkt){
        $queryProduktePreis="select preis from artikel where id = ?;";
        $stmt = $conn->prepare($queryProduktePreis);
        $stmt->execute([$produkt]);
        $produktpreis = $stmt->fetch(PDO::FETCH_COLUMN);
        $gesamtpreis += $produktpreis;
    }

    //Speichern in die Datenbank
    $queryBestellung = "Insert into bestellung(gesamtpreis,bestelldatum,liefertermin,status_id)
    values(?,?,?,?)";
    $stmt = $conn->prepare($queryBestellung);
    $stmt->execute([$gesamtpreis,$bestelldatum,$liefertermin,$statusliste]);
    $bestellungId = $conn -> lastInsertId();

    $queryArtikelHasBestellung = "Insert into artikel_has_bestellung(Artikel_Id,Bestellung_id,menge)
    values(?,?,?)";
    foreach($ausgewaehlteProdukte as $artikelId){
        $stmt = $conn->prepare($queryArtikelHasBestellung);
        $stmt->execute([$artikelId,$bestellungId,$menge]);
    }

    $queryKundeHasBestellung = "Insert into kunde_has_bestellung(Kunde_Id,Bestellung_id)
    values(?,?)";
    $stmt = $conn->prepare($queryKundeHasBestellung);
    $stmt->execute([$kundenliste,$bestellungId]);

    $validation = $message == "";
    if($validation){
        $success = true;
    header('Location: index');
    }
}
?>

<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4 shadow">
    <h2>Bestellung erstellen</h2>
    <div class="container mt-5">
    <h2>Bestellung erstellen</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="etage">Kunden</label>
            <?php echo createDropdown('kunden', $kundenliste); ?>
        </div>
        <div class="form-group">
            <label for="etage">Produkte</label>
            <?php echo createMultiselect('produkte[ ]', $produktliste); ?>
        </div>
        <div class="form-group">
            <label for="etage">Status</label>
            <?php echo createDropdown('status', $statusliste,$inBearbeitung); ?>
        </div>
        <div class="form-group">
            <label for="liefertermin">Liefertermin</label>
            <input type="date" class="form-control" id="liefertermin" name="liefertermin" required>
        </div>
        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>
    
 <!--    <div>
       <!--  <?php if ($stateChanged) { echo generateTableFromQuery($conn,$selectPersonenQuery,'per_id',"person"); } ?> -->
    <!-- </div>
</div> -->
   <!--  <div class="card">
<!-- <?php if($validation && $success){
    echo showSuccess("Datensatz wurde erfolgreich eingetragen.");
}else if(!$validation){
    echo showAlertWarning($message);
}
?> -->
</div>

