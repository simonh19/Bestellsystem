<?php
if (session_id() == '') {
    session_start();
}

include_once 'helper/database_functions.php';

?>

<?php
        $query = "select k.Id,concat(k.vorname,' ',k.nachname) as Name,k.Adresse,count(kb.Bestellung_id) as 'Anzahl Bestellungen'
        from kunde k
        left join Kunde_has_Bestellung kb on k.Id = kb.Kunde_Id
        group by k.Id,concat(k.vorname,' ',k.nachname),k.Adresse;";
        
        $stmt = executeQuery($conn,$query);
        
        echo generateTableFromQueryKunden($conn, $stmt,'Id','kunden');
    ?>