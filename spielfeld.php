<?php
session_start(); // Sicherstellen, dass die Session gestartet ist

// Optional: Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html"); // Zur Login-Seite umleiten, falls nicht eingeloggt
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Spielfeld</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        h1 {
            font-size: 3em;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Spielfeld</h1>
</body>
</html>

