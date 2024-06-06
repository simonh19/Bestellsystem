<?php
if (session_id() == '') {
    session_start();
}
include_once 'helper/database_functions.php';

global $conn;
$artikelname = "";
$validation =true;
$site = $_GET['site'];
$parts = explode("?", $site);

if(!empty($parts))
{
    $paramValue = getUrlParam($parts[1]);
    $artikelname = getValue($conn,'artikel','name','Id',$paramValue);

}

?>
<div class="card border-0 p-4 container d-flex align-items-center flex-column mt-4 gap-4">
    <h2>Anbei die Bestellungen für <?php echo $artikelname?></h2>
    <?php
        $query = "select a.Id,concat(k.vorname,' ',k.nachname) as Name,k.Adresse,stb.bezeichnung as 'Status der Bestellung',b.bestelldatum as Bestelldatum,b.liefertermin as Liefertermin,l.Unternehmen as Lieferant
        from artikel_has_bestellung ab
        join Bestellung b on ab.Bestellung_id = b.Id
        join status stb on b.Status_Id = stb.Id
        join Artikel a on ab.Artikel_Id = a.Id
        join Artikel_Variante av on a.Artikel_Variante_Id = av.Id
        left join Artikel_has_Lieferant al on a.Id = al.Artikel_Id
        left join Lieferant l on al.Lieferant_Id = l.Id
        join kunde_has_bestellung kb on b.Id = kb.Bestellung_Id
        join Kunde k on kb.Kunde_Id = k.Id
        where a.Id = ?";
        $stmt = $conn -> prepare($query);
        $stmt -> execute([$paramValue]);
        echo generateTableFromQuery2($conn, $stmt,'Id','artikel');  
    ?>
</div>