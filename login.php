<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$connection = new mysqli("localhost", "root", "", "vier_gewinnt");


if ($connection->connect_error) {
    die("Verbindung fehlgeschlagen: " . $connection->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);

   
    $sql = "SELECT * FROM users WHERE vorname = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
       
        $user = $result->fetch_assoc();
        echo "<h2>Willkommen, " . $user['vorname'] . "!</h2>";
    } else {
   
        echo "<h2>Ungültige Anmeldedaten.</h2>";
    }

    $stmt->close();
} else {
    echo "Ungültige Anforderung.";
}

$connection->close();
?>



