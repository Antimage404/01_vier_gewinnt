<?php


/*die html braucht:
    id: active user, (this) user, column id (der button)
    jeder Kreis braucht eine id mit der form columnid || rowid, z.B. 23

*/
$column = $_POST['column'];  //id der column
$user = $_POST['user'];

if(test_active_user() == true) {
    if(test_free_column() == true) {
        $zugnumnmer = get_max_zugnummer();
        $rowcolumn = setField($column);   
        add_to_column($rowcolumn);
        if(check_win_condition) {
            assign_win($user);
        }
    }
}



//überprüfen, ob der Spieler an der Reihe ist
function test_active_user() {
    if($_POST['active_user'] == $user) return true;
    else return false;
}


//gibt die nächste zugnummer zurück
function get_max_zugnummer() {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");


    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
        $sql = "SELECT max(zugnummer) FROM currentgame";
        $stmt = $connection->prepare($sql);    
        $stmt->execute();
        $result= $stmt->get_result();
        $stmt->close();

        return $result +1;



    }
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
function add_to_column($rowcolumn) {
    $connection = new mysqli("localhost", "root", "", "vier_gewinnt");

    if ($connection->connect_error) {
        die("Verbindung fehlgeschlagen: " . $connection->connect_error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $sqlInsert = "  INSERT INTO currentgame(zugnummer, user, feld)
                        VALUES(:zugnr, :user, :rowcolumn)";
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
        asort(fieldArray);
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

        for(int i = 0;i < 8; i++) {
            colArray[i] = 0; 
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
        for(int i = 0;i < 8; i++) {
            if($colArray[i] > 4){
                $last = 1;
                $count = 0;
                for(int j = 0; j < count($fieldArray); j++) {
                    if($fieldArray[j] / 10 = i) {
                        if ($last + 1 = $fieldArray[j]) {
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
        for(int i = 0;i < 8; i++) {
            if($rowArray[i] > 4){
                $last = 1;
                $count = 0;
                for(int j = 0; j < count($fieldArray); j++) {
                    if($fieldArray[j] % 10 = i) {
                        if ($last + 10 = $fieldArray[j]) {
                            $count = $count + 1;
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
