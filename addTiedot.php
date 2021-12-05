<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require('headers.php');
require('functions.php');

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

            $dbcon = openDb();
         
            addTiedot($dbcon,$user);

        } catch (Exception $e) {
            echo  json_encode(array("message" => "No access!!"));
        }
    }
}



/* 
<?php

// **EI VIELÄ TEE MITÄÄN**

require_once './functions.php';
$input = json_decode(file_get_contents('php://input'));

$user = filter_var($input->user, FILTER_SANITIZE_STRING);
$etunimi = filter_var($input->etunimi, FILTER_SANITIZE_STRING);
$sukunimi = filter_var($input->sukunimi, FILTER_SANITIZE_STRING);
$email = filter_var($input->email, FILTER_SANITIZE_STRING);

try{
    $dbcon = openDb();
    $query = $dbcon->prepare('update tiedot set user=:user, etunimi=:etunimi, sukunimi=:sukunimi, email=:email 
    where user=:user');
    $query->bindValue(':user',$user, PDO::PARAM_STR);
    $query->bindValue(':etunimi',$etunimi, PDO::PARAM_STR);
    $query->bindValue(':sukunimi',$sukunimi, PDO::PARAM_STR);
    $query->bindValue(':email',$email, PDO::PARAM_STR);
    $query->execute();
    header('HTTP/1.1 200 OK');
    $data = array($dbcon, 'user' => $user, 
                            'etunimi' => $etunimi,
                            'sukunimi' => $sukunimi,
                            'email' => $email);
    print json_encode($data);
}catch(PDOException $e){
    echo '<br>'.$e->getMessage();
}
 */