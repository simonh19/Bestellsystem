<?php

if (isset($_GET['site'])) {
    $param = $_GET['site'];
    $separator = "?";
    if(str_contains($param,$separator) && !str_contains($param,"edit_id"))
    {
        $parts = explode($separator, $param);
        $artikelIdValue = getUrlParam($parts[1]);
        $bestellIdValue = getUrlParam($parts[2]);
        deleteArtikelFromBestellung($conn,$artikelIdValue, $bestellIdValue);
        header("Location: index.php?site=bestelldetails?edit_id=" . $bestellIdValue );
exit();
    }

}

function deleteArtikelFromBestellung($conn,$idArtikel, $idBestellung)
{
    try {
        
        $deleteQuery = "Delete from Artikel_has_Bestellung where bestellung_id = ? AND Artikel_id = ?";
        $preparedStmt = $conn->prepare($deleteQuery);
        $preparedStmt->execute([$idBestellung,$idArtikel]);

        return $preparedStmt->rowCount();
    } catch (PDOException $exception) {
        echo 'Database Delete Error: ' . $exception->getMessage();
    }
}

?>

