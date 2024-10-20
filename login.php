<?php 
session_start(); // Session starten
error_reporting(E_ALL);
ini_set('display_errors', 1);

$connection = new mysqli("localhost", "root", "", "vier_gewinnt");

if ($connection->connect_error) {
    die("Verbindung fehlgeschlagen: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        // Registrierung
        $username = htmlspecialchars($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

        // Überprüfen ob Benutzername existiert
        $checkSql = "SELECT * FROM users WHERE benutzername = ?";
        $checkStmt = $connection->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            // Benutzer erstellen
            $insertSql = "INSERT INTO users (benutzername, passwort) VALUES (?, ?)";
            $insertStmt = $connection->prepare($insertSql);
            $insertStmt->bind_param("ss", $username, $password);
            if ($insertStmt->execute()) {
                echo "<h2>Registrierung erfolgreich! Sie können sich jetzt anmelden.</h2>";
            } else {
                echo "<h2>Fehler bei der Registrierung.</h2>";
            }
            $insertStmt->close();
        } else {
            echo "<h2>Benutzername bereits vergeben.</h2>";
        }
        $checkStmt->close();
    } else {
        // Anmelden
        $username = htmlspecialchars($_POST['username']);
        $sql = "SELECT * FROM users WHERE benutzername = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Passwortüberprüfung
            if (password_verify($_POST['password'], $user['passwort'])) {
                // Session setzen
                $_SESSION['loggedin'] = true; 
                $_SESSION['username'] = $user['benutzername'];

                // Redirect zur Spielfeld-Seite
                header("Location: spielfeld.html");
                exit(); // Wichtig, um das Script zu beenden
            } else {
                echo "<h2>Ungültige Anmeldedaten.</h2>";
            }
        } else {
            echo "<h2>Ungültige Anmeldedaten.</h2>";
        }

        $stmt->close();
    }
} 

// Registrierungsformular
if ($_SERVER["REQUEST_METHOD"] == "GET" || (isset($_POST['register']) && empty($username))) {
    echo '
    <form method="POST" action="login.php">
        <h3>Registrierung</h3>
        Benutzername: <input type="text" name="username" required>
        Passwort: <input type="password" name="password" required>
        <input type="submit" name="register" value="Registrieren">
    </form>';
} else {
    // Anmeldeformular
    echo '
    <form method="POST" action="login.php">
        <h3>Anmeldung</h3>
        Benutzername: <input type="text" name="username" required>
        Passwort: <input type="password" name="password" required>
        <input type="submit" value="Einloggen">
    </form>';
}

$connection->close();
?>
