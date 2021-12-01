<?php
function openDb(): object {

    try{
        $dbcon = new PDO('mysql:host=localhost;port=3306;dbname=n0peii00', 'root', '');
        $dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo '<br>' .$e->getMessage();
    }
    
    return $dbcon;
}

// Luo tietokantaan uuden käyttäjän ja hashaa salasanan
function createUser(PDO $dbcon, $user, $password){

    //Sanitoidaan.
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    try{
        $hash_password = password_hash($password, PASSWORD_DEFAULT); //salasanan hash
        $sql = "INSERT IGNORE INTO tunnus VALUES (?,?)"; //komento, arvot parametreina
        $prepare = $dbcon->prepare($sql); //valmistellaan
        $prepare->execute(array($user, $hash_password));  //parametrit tietokantaan
    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}

function checkUser(PDO $dbcon, $username, $passwd){

    //Sanitoidaan. Lisätty tuntien jälkeen
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $passwd = filter_var($passwd, FILTER_SANITIZE_STRING);

    try{
        $sql = "SELECT password FROM user WHERE username=?";  //komento, arvot parametreina
        $prepare = $dbcon->prepare($sql);   //valmistellaan
        $prepare->execute(array($username));  //kysely tietokantaan

        $rows = $prepare->fetchAll(); //haetaan tulokset (voitaisiin hakea myös eka rivi fetch ja tarkistus)

        //Käydään rivit läpi (max yksi rivi tässä tapauksessa) 
        foreach($rows as $row){
            $pw = $row["password"];  //password sarakkeen tieto (hash salasana tietokannassa)
            if( password_verify($passwd, $pw) ){  //tarkistetaan salasana tietokannan hashia vasten
                return true;
            }
        }

        //Jos ei löytynyt vastaavuutta tietokannasta, palautetaan false
        return false;

    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}