# Vier Gewinnt - Browserbasiertes Spiel

Dieses Projekt implementiert das klassische "Vier Gewinnt"-Spiel als browserbasierte Anwendung. Zwei Spieler treten gegeneinander an, und das Matchmaking erfolgt automatisch, sobald beide Spieler beitreten.

---

## Voraussetzungen

- **XAMPP** (für PHP und MySQL)
- Ein moderner Browser (z. B. Chrome, Firefox)

---

## Schritt 1: XAMPP herunterladen und installieren

1. Besuche die [offizielle XAMPP-Website](https://www.apachefriends.org/de/index.html).
2. Lade die passende Version für dein Betriebssystem herunter.
3. Installiere XAMPP und starte das XAMPP Control Panel.
4. Aktiviere **Apache** und **MySQL**, indem du auf "Start" klickst.

---

## Schritt 2: Datenbank und Tabelle erstellen

1. Öffne **phpMyAdmin**:
   - Standardmäßig erreichbar unter: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Erstelle eine neue Datenbank:
   - Klicke im linken Menü auf **"Neu"**.
   - Gib den Namen `connect_four` ein und wähle den Zeichensatz `utf8mb4_general_ci`.
3. Erstelle die Tabelle `games`:
   - Öffne die SQL-Konsole in phpMyAdmin und füge den folgenden Befehl ein:
     ```sql
     CREATE TABLE games (
         id INT AUTO_INCREMENT PRIMARY KEY,
         player1_name VARCHAR(255) NOT NULL,
         player2_name VARCHAR(255) DEFAULT NULL,
         current_turn ENUM('player1', 'player2') DEFAULT 'player1',
         status ENUM('waiting', 'ongoing', 'finished') DEFAULT 'waiting',
         board_state TEXT DEFAULT NULL,
         winner_name VARCHAR(255) DEFAULT NULL,
         finished_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```

---

## Schritt 3: Projekt einrichten

1. Kopiere den Projektordner in das Verzeichnis `htdocs` von XAMPP:
   - Standardmäßig unter: `C:\xampp\htdocs\`.
2. Stelle sicher, dass die Datei `db_connection.php` die korrekten Datenbankeinstellungen enthält:
   ```php
   <?php
   $host = '127.0.0.1';
   $db = 'vier_gewinnt';
   $user = 'root'; // Standardnutzer
   $pass = ''; // Kein Passwort standardmäßig
   $charset = 'utf8mb4';

   $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
   $options = [
       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       PDO::ATTR_EMULATE_PREPARES => false,
   ];

   try {
       $pdo = new PDO($dsn, $user, $pass, $options);
   } catch (\PDOException $e) {
       throw new \PDOException($e->getMessage(), (int)$e->getCode());
   }
   ?>
