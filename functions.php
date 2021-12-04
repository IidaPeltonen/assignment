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

// *** EI TOIMI VIELÄ ***

// function selectAsJson(object $dbcon, string $user): void {

//     $user = filter_var($user, FILTER_SANITIZE_STRING);

//     $sql = 'SELECT tunnus.user, etunimi, sukunimi, email from tiedot, tunnus 
//     WHERE tiedot.user = tunnus.user and ($user)=tiedot.user';
//     var_dump($user);
//     $query = $dbcon->query($sql);
//     $results = $query->fetchAll(PDO::FETCH_ASSOC);
//     header('HTTP/1.1 200 OK');
//     echo json_encode($results);
// }

function selectAsJson(object $dbcon,string $sql): void {
    $query = $dbcon->query($sql);
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    header('HTTP/1.1 200 OK');
    echo json_encode($results);
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

/**
 * Tarkistaa onko käyttäjä tietokannassa ja onko salasana validi
 */
function checkUser(PDO $dbcon, $user, $password){

    //Sanitoidaan. Lisätty tuntien jälkeen
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    try{
        $sql = "SELECT password FROM tunnus WHERE user=?";  //komento, arvot parametreina
        $prepare = $dbcon->prepare($sql);   //valmistellaan
        $prepare->execute(array($user));  //kysely tietokantaan

        $rows = $prepare->fetchAll(); //haetaan tulokset (voitaisiin hakea myös eka rivi fetch ja tarkistus)

        //Käydään rivit läpi (max yksi rivi tässä tapauksessa) 
        foreach($rows as $row){
            $pw = $row["password"];  //password sarakkeen tieto (hash salasana tietokannassa)
            if( password_verify($password, $pw) ){  //tarkistetaan salasana tietokannan hashia vasten
                return true;
            }
        }

        //Jos ei löytynyt vastaavuutta tietokannasta, palautetaan false
        return false;

    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}

/*function checkTiedot(PDO $dbcon, $user, $id){
    try{
        $sql = "SELECT * FROM tiedot WHERE ";  //komento, arvot parametreina
        $prepare = $dbcon->prepare($sql);   //valmistellaan
        $prepare->execute(array($user));  //kysely tietokantaan
        $rows = $prepare->fetchAll(); //haetaan tulokset (voitaisiin hakea myös eka rivi fetch ja tarkistus)
        //Käydään rivit läpi (max yksi rivi tässä tapauksessa) 
        foreach($rows as $row){
            $pw = $row["password"];  //password sarakkeen tieto (hash salasana tietokannassa)
            if( password_verify($password, $pw) ){  //tarkistetaan salasana tietokannan hashia vasten
                return true;
            }
        }
        //Jos ei löytynyt vastaavuutta tietokannasta, palautetaan false
        return false;
    }catch(PDOException $e){
        echo '<br>'.$e->getMessage();
    }
}*/