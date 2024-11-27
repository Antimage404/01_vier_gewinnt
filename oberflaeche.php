<-- Tutubalin (Antimage404) -->

<?php
session_start();

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.html');
    exit();
}

// Den Benutzernamen aus der Session holen
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Vier Gewinnt</title>
</head>
<body>
    <div class="main-container">
        <h1>Vier Gewinnt</h1>
        <h3>Willkommen, <?php echo htmlspecialchars($username); ?>!</h3>

    <!-- Buttons für die Spalten und Funktionsaufruf der Spielfunktion pro Klick auf ein Button -->

    <form  action="spielfunktion.php" method="POST">
            <input type="text" hidden name='user' id='user' value = 'tester'>
            <input  type="text"hidden name='active_user' id='active_user' value = 'tester'>

            <div class="button-container" style="margin: 20px;">
                <button type="submit" name="column"  id="column" value = "1" >Spalte 1</button1>       
                <button type="submit" name="column"  id="column" value = "2" >Spalte 2</button1>
                <button type="submit" name="column"  id="column" value = "3" >Spalte 3</button1>
                <button type="submit" name="column"  id="column" value = "4" >Spalte 4</button1>
                <button type="submit" name="column"  id="column" value = "5" >Spalte 5</button1>
                <button type="submit" name="column"  id="column" value = "6" >Spalte 6</button1>
                <button type="submit" name="column"  id="column" value = "7" >Spalte 7</button1>
            </div>
    </form>

        <!-- Spielfeld -->
        <div class="game-board">
            <?php 
            for ($row = 0; $row < 6; $row++) {
                for ($col = 0; $col < 7; $col++) {
                    echo "<div class='cell' id='cell-$row-$col'></div>";
                }
            }
            ?>
        </div>

        <!-- Logout Funktion -->
        <form action="logout.php" method="POST" >
            <div class="logout-button" style="padding: 35px;">
            <button type="submit" id="logout" >Logout</button>        
            </div>
        </form>

    </div>

    
    
</body>
</html>
