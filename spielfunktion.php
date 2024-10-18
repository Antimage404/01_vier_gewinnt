<?php

$column = htmlspecialchars($_POST['column']);  //id der column
$user = htmlspecialchars($_POST['user']);

if(test_active_user == true) {
    if(test_free_column == true) {
        add_to_column();
        setField($column);   
    }
    else{
                //wenn spalte voll: neue Spalte soll ausgewählt werden

    }
}



//überprüfen, ob der Spieler an der Reihe ist
function test_active_user() {
    if($_POST['active_user'] == §user) return true;
    else return false;
}

function get_max_zugnummer() {
    
}

//überprüft, ob die angegebene Spalte bereits voll ist
function test_free_column() {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        $sql = "SELECT * FROM currentgame WHERE feld = $column ";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
    
        $stmt->execute();
        $result= $stmt->get_result();
        $stmt->close();
        if ($result->num_rows < 6) {
            return true;
        }
        else {
            return false;
        }


    }
}

//fügt der Spalte eins für den entsprechenden Spieler hinzu
function add_to_column() {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");

    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    
    
    //überprüft, ob die angegebene Spalte bereits voll ist
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
            $sqlInsert = "  INSERT INTO currentgame(zugnummer, user, feld)
                            VALUES(:zugnr, :user, :column)";
            $stmt = $connection->prepare($sqlInsert);
            $stmt->execute();
        $stmt->close();
    } else {
        echo "Ungültige Anforderung.";
    }
}


//färbt den Kreis in der Farbe des Spielers 
function setField($column) {

    //suche die Anzahl der Steine in der Spalte
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");
    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $sqlInsert = "  INSERT INTO currentgame(zugnummer, user, feld)
                        VALUES(:zugnr, :user, :column)";
        $stmt = $connection->prepare($sqlInsert);
        $stmt->execute();
        $row= $stmt->get_result();
        $stmt->close();

        $rowcolumn = $column || $row;

        //setze das Feld mit der id rowcolumn auf die farbe des aktiven Users  fehlt
} else {
    echo "Ungültige Anforderung.";
}

}

?>