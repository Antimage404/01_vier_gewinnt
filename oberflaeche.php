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

        <!-- Buttons für die Spalten -->
        <div class="button-container">
            <button1 id="1">Spalte 1</button1>
            <button1 id="2">Spalte 2</button1>
            <button1 id="3">Spalte 3</button1>
            <button1 id="4">Spalte 4</button1>
            <button1 id="5">Spalte 5</button1>
            <button1 id="6">Spalte 6</button1>
            <button1 id="7">Spalte 7</button1>
        </div>

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
    </div>
</body>
</html>