<?php
if (session_id() == '') {
    session_start();
}

include_once 'helper/database_functions.php';

$site = $_GET['site'];
$parts = explode("?", $site);

if(!empty($parts))
{
    $paramValue = getUrlParam($parts[1]);

}

?>

<?php
        $query = "Select kb.bestellung_id, concat(a.name,' ',av.groesse,' ',av.farbe,' ',av.speicher) as Produkt,a.artikelnummer,a.Id as Id,
        a.preis,a.lagerbestand
        from Artikel a
        join artikel_variante av on a.Artikel_Variante_Id = av.Id
        join Artikel_has_Bestellung ab on a.Id = ab.Artikel_Id
        join Kunde_has_Bestellung kb on ab.Bestellung_id = kb.Bestellung_id
        join Kunde k on k.Id = kb.Kunde_Id
        where k.Id = ?
        order by kb.bestellung_id";
        $stmt = $conn -> prepare($query); 
        $stmt -> execute([ $paramValue]);
        echo generateTableFromQuery2($conn, $stmt,'Id','artikel');
        ?>