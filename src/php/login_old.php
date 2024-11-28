<?php
session_start();

// Verbindung zur Datenbank herstellen (Beispiel)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "viergewinnt";

$conn = new mysqli($servername, $username, $password, $dbname);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Wenn das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Datenbankabfrage, um Benutzer zu authentifizieren
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Benutzer gefunden, Session starten
        $user = $result->fetch_assoc();
        
        // Session-Variablen setzen
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];  // Die Benutzer-ID wird hier gespeichert

        // Weiterleitung zur Spielfeldseite
        header('Location: oberflaeche.php');
        exit();
    } else {
        // Fehlermeldung, wenn Login fehlschlägt
        echo "<p>Ungültige Anmeldedaten. Versuchen Sie es erneut.</p>";
    }
}
?>
