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

    $query = $dbcon->prepare('insert into tiedot (user, etunimi, sukunimi, email) values (:user, :etunimi, _:sukunimi, :email)');
    $query->bindValue(':user',$user, PDO::PARAM_STR);
    $query->bindValue(':etunimi',$hash_password, PDO::PARAM_STR);
    $query->bindValue(':sukunimi',$hash_password, PDO::PARAM_STR);
    $query->bindValue(':email',$hash_password, PDO::PARAM_STR);
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
