<?php
// Wir stellen die Verbindung zur Datenbank her
require 'db_connection.php';

// Wir holen den Spielernamen aus den POST-Daten. Falls keiner übergeben wird, setzen wir 'Guest' als Standard.
$playerName = $_POST['player_name'] ?? 'Guest';

// Jetzt suchen wir nach einem Spiel, das auf einen zweiten Spieler wartet (Status 'waiting')
$stmt = $pdo->query("SELECT * FROM games WHERE status = 'waiting' LIMIT 1");
$game = $stmt->fetch();

// Wenn wir ein Spiel gefunden haben, bei dem ein zweiter Spieler noch fehlt...
if ($game) {
    // Der Spieler wird als zweiter Spieler dem Spiel zugeordnet und der Status des Spiels wird auf 'ongoing' geändert.
    $update = $pdo->prepare("UPDATE games SET player2_name = ?, status = 'ongoing' WHERE id = ?");
    $update->execute([$playerName, $game['id']]);
    $gameId = $game['id'];  // Die ID des Spiels merken wir uns für später
} else {
    // Wenn kein Spiel wartet, erstellen wir ein neues Spiel und setzen den ersten Spieler
    $insert = $pdo->prepare("INSERT INTO games (player1_name) VALUES (?)");
    $insert->execute([$playerName]);
    $gameId = $pdo->lastInsertId();  // Die ID des neuen Spiels holen wir uns
}

// Wir leiten den Spieler nun zur Warteseite weiter und übergeben die Spiel-ID und den Spielernamen in der URL
header("Location: waiting_room.php?game_id=$gameId&player_name=$playerName");
exit;
?>

