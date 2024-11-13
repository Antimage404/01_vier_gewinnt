<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verbindung zur Datenbank
$connection = new mysqli("localhost", "root", "", "vier_gewinnt");

if ($connection->connect_error) {
    die("Verbindung fehlgeschlagen: " . $connection->connect_error);
}

// Wenn das Formular über POST abgesendet wird
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Registrierung
    if (isset($_POST['register'])) {
        if (empty($username) || empty($password)) {
            header('Location: login.html?error=Benutzername und Passwort dürfen nicht leer sein');
            exit();
        }

        // Passwort hashen
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Überprüfen, ob der Benutzername bereits existiert
        $checkSql = "SELECT * FROM users WHERE benutzername = ?";
        $checkStmt = $connection->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            // Benutzer erstellen
            $insertSql = "INSERT INTO users (benutzername, passwort) VALUES (?, ?)";
            $insertStmt = $connection->prepare($insertSql);
            $insertStmt->bind_param("ss", $username, $passwordHash);

            if ($insertStmt->execute()) {
                header('Location: login.html?error=Registrierung erfolgreich! Du kannst dich nun einloggen');
                exit();
            } else {
                header('Location: login.html?error=Fehler bei der Registrierung');
                exit();
            }
            $insertStmt->close();
        } else {
            header('Location: login.html?error=Benutzername bereits vergeben');
            exit();
        }
        $checkStmt->close();
    }

    // Login
    else {
        if (empty($username) || empty($password)) {
            header('Location: login.html?error=Benutzername und Passwort dürfen nicht leer sein');
            exit();
        }

        // Benutzer suchen
        $sql = "SELECT * FROM users WHERE benutzername = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Passwort überprüfen
            if (password_verify($password, $user['passwort'])) {
                session_regenerate_id(true); // Session neu generieren
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['benutzername'];

                header('Location: spielfeld.html'); // Erfolgreich eingeloggt, weiter zur Spielfeld-Seite
                exit();
            } else {
                header('Location: login.html?error=Ungültige Anmeldedaten');
                exit();
            }
        } else {
            header('Location: login.html?error=Benutzername nicht gefunden');
            exit();
        }
        $stmt->close();
    }
}

$connection->close();
?>

