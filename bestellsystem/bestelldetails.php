<?php
if (session_id() == '') {
    session_start();
}
include_once 'helper/form_functions.php';
include_once 'helper/database_functions.php';

$site = $_GET['site'];
$parts = explode("?", $site);
$bestellid = getUrlParam($parts[1]);

$queryKunde ="Select concat(k.vorname,' ',k.nachname)
from kunde_has_bestellung kb join kunde k on kb.Kunde_Id = k.Id
join bestellung b on kb.Bestellung_id = b.Id
where b.id = ?;";

$preparedStmt = $conn->prepare($queryKunde);
$preparedStmt->execute([$bestellid]);
$kundenname = $preparedStmt->fetch(PDO::FETCH_COLUMN);

$queryStatus ="Select s.bezeichnung
from kunde_has_bestellung kb join kunde k on kb.Kunde_Id = k.Id
join bestellung b on kb.Bestellung_id = b.Id
join status s on b.Status_Id = s.Id
where b.id = ?;";

$preparedStmt = $conn->prepare($queryStatus);
$preparedStmt->execute([$bestellid]);
$status = $preparedStmt->fetch(PDO::FETCH_COLUMN);

$produktliste="";
$queryProdukte="select Id as value,name as label from artikel a
where a.Id not in (Select artikel_id from artikel_has_bestellung where bestellung_id = ?)";

$stmt = $conn->prepare($queryProdukte);
$stmt->execute([$bestellid]);
$produktliste = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $menge = 1;

  $ausgewaehlteProdukte = isset($_POST['produkte']) ? $_POST['produkte'] : [];
  $queryArtikelHasBestellung = "Insert into artikel_has_bestellung(Artikel_Id,Bestellung_id,menge)
  values(?,?,?)";
  foreach($ausgewaehlteProdukte as $artikelId){
      $stmt = $conn->prepare($queryArtikelHasBestellung);
      $stmt->execute([$artikelId,$bestellid,$menge]);
  }
  header("Location: index.php?site=bestelldetails?edit_id=" . $bestellid );
}
?>

<div class="jumbotron w-100">
  <h1 class="display-5">Name: <?php echo $kundenname ?></h1>
  <p class="lead">Status: <?php echo $status ?></p>
  <h3>Artikel hinzufügen</h3>
  <form action="" method="post">
    <div class="form-group">
      <label for="etage">Produkte</label>
      <?php echo createMultiselect('produkte[ ]', $produktliste); ?>
    </div>
    <button type="submit" class="btn btn-primary">Speichern</button>
  </form>
  <hr class="my-4">
</div>


<?php
$site = $_GET['site'];
$parts = explode("?", $site);
$id = getUrlParam($parts[1]);

/*Artikel der Bestellungen: Name Artikel, Menge, Preis*/
$query = "Select a.id as ArtikelId,b.id as BestellId,a.name as Artikel,ab.menge as Menge,(ab.menge * a.preis) as Preis
from artikel a join artikel_has_bestellung ab on a.Id = ab.Artikel_Id
join bestellung b on ab.Bestellung_id = b.id
where b.id=?;";


$preparedStmt = $conn->prepare($query);
$preparedStmt->execute([$id]);
$preparedStmt->fetch(PDO::FETCH_ASSOC);

echo generateTableFromOrderdetails($conn, $preparedStmt,'ArtikelId','BestellId','Artikel_has_Bestellung','Artikelübersicht');
?>