<?php
function openDb(): object {

    try{
        $dbcon = new PDO('mysql:host=localhost;port=3307;dbname=n0peii00', 'root', '');
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo '<br>' .$e->getMessage();
    }
    
    return $dbcon;
}