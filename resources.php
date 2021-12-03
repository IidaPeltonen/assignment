<?php

/**
 * Tälle tiedostolle tulee pyyntö resurssista. Resurssi annetaan vain, jos 
 * mukana on validi JWT bearer token.
 */

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require('headers.php');
require('functions.php');

//Haetaan otsikot pyynnöstä
//Kommentissa vaihtoehtoiset otsikkosijainnit, jotka eivät esim. xamppin kanssa taida toimia
$requestHeaders =  apache_request_headers(); //$_SERVER['Authorization'] tai $_SERVER['HTTP_AUTHORIZATION'])

//Onko auth header olemassa?
if (isset($requestHeaders['authorization'])) {

    //Halkaistaan osiin Bearer ja token
    $auth_value = explode(' ', $requestHeaders['authorization']);

    //Tarkistetaan onko Bearer sanaa
    if ($auth_value[0] === 'Bearer') {

        //Otetaan itse token talteen
        $token = $auth_value[1];

        try {
            //Tarkistetaan ja dekoodataan token. Jo ei validi, siirtyy catchiin.
            $decoded = JWT::decode($token, new Key(base64_encode('jokuhelppo'), 'HS256'));

            //Onnistunut dekoodaus sisältää sub-kentän, jossa käyttäjänimi
            $user = $decoded->sub;

            //Lähetetään clientille ykstyisen resurssi, koska oikeus tarkistettu
            echo  json_encode(array("message" => "This is your private resource " . $user));

            $dbcon = openDb();
            selectAsJson($dbcon, 'SELECT tunnus.user, etunimi, sukunimi, email from tiedot, tunnus 
                WHERE tiedot.user = tunnus.user and tiedot.user="'.$user.'"');
            // selectAsJson($dbcon, 'SELECT tunnus.user, etunimi, sukunimi, email from tiedot, tunnus 
            //     WHERE tiedot.user = tunnus.user and ($user) = tiedot.user');

            // selectAsJson($dbcon, $user);
        } catch (Exception $e) {
            echo  json_encode(array("message" => "No access!!"));
        }
    }
}
