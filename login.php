<?php
session_start();
require('headers.php');
require('functions.php');



//Tarkistetaan tuleeko palvelimelle basic login tiedot (Authorization: Basic asfkjsafdjsajflkasj)
if( isset($_SERVER['PHP_AUTH_USER']) ){
    if(checkUser(createDbConnection(), $_SERVER['PHP_AUTH_USER'],$_SERVER["PHP_AUTH_PW"] )){
        $_SESSION["user"] = $_SERVER['PHP_AUTH_USER'];

        // { "info":"Kirjauduit sisään" }
        //json_encode( array("info"=>"Kirjauduit sisään")  );

        echo '{"info":"Kirjauduit sisään"}';
        header('Content-Type: application/json');
        exit;
    }
}

//Ilmoitetaan käyttäjälle, että kirjaudupa sisään (avaa selaimessa login ikkunan)
//header('WWW-Authenticate: Basic');

//Käyttäjälle unauhtorized-otsikko
echo '{"info":"Failed to login"}';
header('Content-Type: application/json');
header('HTTP/1.1 401');
exit;

?>