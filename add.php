<?php

require_once './functions.php';
$input = json_decode(file_get_contents('php://input'));

$user = filter_var($input->user, FILTER_SANITIZE_STRING);
$password = filter_var($input->password, FILTER_SANITIZE_STRING);
/* $etunimi = filter_var($input->etunimi, FILTER_SANITIZE_STRING);
$sukunimi = filter_var($input->sukunimi, FILTER_SANITIZE_STRING);
$email = filter_var($input->email, FILTER_SANITIZE_STRING); */

try{
    $dbcon = openDb();
    $hash_password = password_hash($password, PASSWORD_DEFAULT); //salasanan hash
    $query = $dbcon->prepare('insert into tunnus (user, password) values (:user, :password)');
    //insert into tiedot (user, etunimi, sukunimi, email) values (:user, :etunimi, :sukunimi, :email)');
    $query->bindValue(':user',$user, PDO::PARAM_STR);
    $query->bindValue(':password',$hash_password, PDO::PARAM_STR);
   /*  $query->bindValue(':etunimi',$etunimi, PDO::PARAM_STR);
    $query->bindValue(':sukunimi',$sukunimi, PDO::PARAM_STR);
    $query->bindValue(':email',$email, PDO::PARAM_STR); */
    $query->execute();
    header('HTTP/1.1 200 OK');
    $data = array($dbcon, 'user' => $user, 'password' => $hash_password);
/*     $data2 = array($dbcon, 'user' => $user, 'etunimi' => $etunimi, 'sukunimi' => $sukunimi, 'email' => $email); */
    print json_encode($data);
   /*  print json_encode($data2); */
}catch(PDOException $e){
    echo '<br>'.$e->getMessage();
}

