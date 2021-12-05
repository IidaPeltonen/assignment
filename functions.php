<?php
//funktio tietokannan avaukseen
function openDb(): object {
    try{
        $dbcon = new PDO('mysql:host=localhost;port=3306;dbname=n0peii00', 'root', '');
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo '<br>' .$e->getMessage();
    }
    return $dbcon;
}

//funktio käyttäjän tietojen tuloksekseen resources.php:ssä
function selectAsJson(PDO $dbcon,$user){
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    try{
        $sql = "SELECT tunnus.user, etunimi, sukunimi, email from tiedot, tunnus 
        WHERE tiedot.user = tunnus.user and tiedot.user=?";  
        $prepare = $dbcon->prepare($sql); 
        $prepare->execute(array($user));  
        $results = $prepare->fetchAll(PDO::FETCH_ASSOC);
        header('HTTP/1.1 200 OK');
        echo json_encode($results);     
    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}

//funktio uuden käyttäjän luomiseen
function createUser(PDO $dbcon, $user, $password){
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    try{
        //salasanan hash
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
        //sql-lause, muuttujat paramentreina
        $sql = "INSERT IGNORE INTO tunnus VALUES (?,?)"; 
        //kannan valmistelu
        $prepare = $dbcon->prepare($sql); 
        //suoritus, syötetään tiedot kantaan
        $prepare->execute(array($user, $hash_password));  
    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}


//funktio käyttäjän ja salasanan tarkisteukseen
function checkUser(PDO $dbcon, $user, $password){
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    try{
        $sql = "SELECT password FROM tunnus WHERE user=?";  
        $prepare = $dbcon->prepare($sql);   
        $prepare->execute(array($user)); 
        $rows = $prepare->fetchAll(); 
        foreach($rows as $row){
            $pw = $row["password"];  /
            if( password_verify($password, $pw) ){ 
                return true;
            }
        }

        //Jos ei löytynyt vastaavuutta tietokannasta, palautetaan false
        return false;

    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}

function addTiedot(PDO $dbcon, $user) {
    $input = json_decode(file_get_contents('php://input'));
    $etunimi = filter_var($input ->etunimi, FILTER_SANITIZE_STRING);
    $sukunimi = filter_var($input ->sukunimi, FILTER_SANITIZE_STRING);
    $email = filter_var($input ->email, FILTER_SANITIZE_STRING);
    try{
        $sql = "INSERT INTO tiedot (user,etunimi,sukunimi,email) VALUES (?,?,?,?)";
        $prepare = $dbcon->prepare($sql);
        $prepare->bindValue(':user',$user, PDO::PARAM_STR);
        $prepare->bindValue(':etunimi',$etunimi, PDO::PARAM_STR);
        $prepare->bindValue(':sukunimi',$sukunimi, PDO::PARAM_STR);
        $prepare->bindValue(':email',$email, PDO::PARAM_STR);
        $prepare->execute(array($user,$etunimi,$sukunimi,$email));  
        header('HTTP/1.1 200 OK');
    echo "Data succesfully inserted into database!";
    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}