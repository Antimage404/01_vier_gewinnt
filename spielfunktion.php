<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$column = (int) $_POST['column'];
//$user = $_SESSION['id'];
//$active_user = $_SESSION['user_id'];
$user = 1; //test
$active_user = 1; //test

echo $user;
echo $active_user;
echo $column;

if(test_active_user($active_user, $user)) {
    if(test_free_column($column)) {

        $zugnumnmer = get_max_zugnummer();
        $rowcolumn = setField($column, $zugnumnmer, $user);   
        add_to_column($column, $zugnumnmer);
        if(check_win_condition()) {
            assign_win($user);
        }
    }
}



//überprüfen, ob der Spieler an der Reihe ist
function test_active_user($active_user, $user) {
    if($active_user == $user) return true;
    else return false;
}


//gibt die nächste zugnummer zurück
function get_max_zugnummer() {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        $sql = "SELECT max(zugnr) as zugnr FROM currentgame";

        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result= $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if($result['zugnr'] == null) $result = 0;
        return $result['zugnr'] +1;



    }
}

//überprüft, ob die angegebene Spalte bereits voll ist
function test_free_column($column) {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        $sql = "SELECT count(*) as count FROM currentgame WHERE feld = $column ";
        $stmt = $connection->prepare($sql);    
        $stmt->execute();
        $result= $stmt->get_result();
        $stmt->close();
        if ( $result->fetch_assoc()['count'] < 6) {
            return true;
        }
        else {
            return false;
        }

    }
}

//fügt der Spalte eins für den entsprechenden Spieler hinzu
function add_to_column($column, $zugnr) {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");

    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $sqlInsert = "  INSERT INTO currentgame(zugnr, user, feld)
                        VALUES($zugnr, $user, $column)";
        $stmt = $connection->prepare($sqlInsert);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Ungültige Anforderung.";
    }
}


//färbt den Kreis in der Farbe des Spielers 
function setField($column, $zugnumnmer, $user) {
    //suche die Anzahl der Steine in der Spalte
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");
    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $sqlInsert = "INSERT INTO currentgame(zugnr, user, feld)
                        VALUES($zugnumnmer, $user, $column)";
        $stmt = $connection->prepare($sqlInsert);
        $stmt->execute();
        //funktioniert bis hierher

        $sql = "SELECT count(*) as count FROM currentgame WHERE feld = $column ";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result= $stmt->get_result()->fetch_assoc();
        $row= $result['count'] + 1;
        $stmt->close();

        $rowcolumn = $column || $row;
        //setze das Feld mit der id rowcolumn auf die farbe des aktiven Users  fehlt

        return $rowcolumn;
    } 
}

//überprüft, ob das Spiel für den aktiven Spieler zu Ende ist
function check_win_condition() {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        //lädt alle Felder eines Spielers in ein array
        $sql = "SELECT feld FROM currentgame WHERE user = $user ";
        $stmt = $connection->prepare($sql);    
        $stmt->execute();
        $result= $stmt->fetch_row();
        $fieldArray = array();
        sort($fieldArray);
        $counter = 0;
        while(true) {
            $fieldArray[$counter] = $result;
            $counter ++;
            $result= $stmt->fetch_row();
            if ($result == null) break;
        }

//überprüft, ob mindestens 4 in einer Spalte sind. Wenn ja überpüft ob sie alle benachbart sind
        $column4 = false;
        $colArray = array();

        for($i = 0;$i < 8; $i++) {
            $colArray[i] = 0; 
        }
        $counter = 0;

        //lädt die anzahl der eigenen Steine in einer Spalte in ein eigenes array
        while (true) {
            $colnr = $fieldArray[$counter] / 10;
            $colArray[$colnr] = colArray[$colnr] + 1;
            $counter++;
            if ($fieldArray[$counter] == null) break;
        }
        if ($counter = 0) {
            return false;
        }
        //wenn 4 Steine in einer Spalte sind, wird geprüft ob diese benachbart sind
        for($i = 0;$i < 8; $i++) {
            if($colArray[$i] > 4){
                $last = 1;
                $count = 0;
                for($j = 0; $j < count($fieldArray); $j++) {
                    if($fieldArray[$j] / 10 == $i) {
                        if ($last + 1 == $fieldArray[$j]) {
                            $count = $count +1;
                            if ($count = 4) {
                                return true;
                            }
                        } else {
                            $count = 1;
                        }
                        $last = $fieldArray[j];

                    }
                }
            } 
        }
        

        //lädt die anzahl der eigenen Steine in einer Zeile in ein eigenes array
        while (true) {
            $colnr = $fieldArray[$counter] % 10;
            $rowArray[$colnr] = $rowArray[$colnr] + 1;
            $counter++;
            if ($fieldArray[$counter] == null) break;
        }
        if ($counter = 0) {
            return false;
        }
        //wenn 4 Steine in einer Zeile sind, wird geprüft ob diese benachbart sind
        for($i = 0;$i < 8; $i++) {
            if($rowArray[$i] > 4){
                $last = 1;
                $count = 0;
                for($j = 0; $j < count($fieldArray); $j++) {
                    if($fieldArray[$j] % 10 == $i) {
                        if ($last + 10 == $fieldArray[$j]) {
                            $count = $count + 1;
                            if ($count = 4) {
                                return true;
                            }
                        } else {
                            $count = 1;
                        }
                        $last = $fieldArray[$j];

                    }
                }
            } 
        }



        //überprüft, ob schräg 4 Steine nebeneinander sind fehlt

        $stmt->close();
        return false;
    }
}





//erhöht den Score des Siegers um 1
function assign_win($winner) {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        //lädt alle Felder eines Spielers in ein array
        $sql = "UPDATE users SET score = score + 1  WHERE user = $winner ";
        $stmt = $connection->prepare($sql);    
        $stmt->execute();
        $stmt->close();
    }
}

?>
