<?php

require_once './functions.php';
$input = json_decode(file_get_contents('php://input'));

//tietojen sanitointi
$user = filter_var($input->user, FILTER_SANITIZE_STRING);
$password = filter_var($input->password, FILTER_SANITIZE_STRING);

try{
    //avataan tietokanta
    $dbcon = openDb();
    //salasanan hash
    $hash_password = password_hash($password, PASSWORD_DEFAULT); 
    //syötetään kantaan uudet tiedot
    $query = $dbcon->prepare('insert into tunnus (user, password) values (:user, :password)');
    //bindataan arvot
    $query->bindValue(':user',$user, PDO::PARAM_STR);
    $query->bindValue(':password',$hash_password, PDO::PARAM_STR);
    $query->execute();
    header('HTTP/1.1 200 OK');
    //tietojen tulostus
    $data = array($dbcon, 'user' => $user, 'password' => $hash_password);
    print json_encode($data);
}catch(PDOException $e){
    echo '<br>'.$e->getMessage();
}

