<?php
//Das ist notwendig, damit deleteRecord ausgeführt wird.
require_once 'conf.php';
include_once 'helper/database_functions.php';
global $conn;
//DELETE

if (isset($_GET['site'])) {
        $param = $_GET['site'];
        $separator = "?";
        if(str_contains($param,$separator) && !str_contains($param,"edit_id"))
        {
            $parts = explode($separator, $param);
            $paramName = getUrlParamName($parts[1]);
            $paramValue = getUrlParam($parts[1]);
            $tableName = getUrlParam($parts[2]);
            //Variable für deleteRecordMultible
$tables=[
    [
        'name'=>'artikel_has_bestellung',
        'id'=>$paramValue,
        'spalte'=>'Bestellung_id'
    ],
    [
        'name'=>'kunde_has_bestellung',
        'id'=>$paramValue,
        'spalte'=>'Bestellung_id'
    ],
    [
        'name'=>'bestellung',
        'id'=>$paramValue,
        'spalte'=>'id'
    ]  
];

            deleteRecordMultible($conn,$tables);
            header('Location: index');
exit();
        }

}