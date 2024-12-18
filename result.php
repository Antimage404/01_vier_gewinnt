<?php
// Wir holen die Spiel-ID und den Gewinner aus den GET-Daten
$gameId = $_GET['game_id'];
$winner = $_GET['winner'];

// Spielerinformationen aus der Datenbank abrufen
require 'db_connection.php';
$stmt = $pdo->prepare("SELECT player1_name, player2_name FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

// Wenn das Spiel nicht gefunden wurde, brechen wir ab
if (!$game) {
    die("Game not found.");
}

// Bestimme den Verlierer: Wenn der Gewinner Spieler 1 ist, ist der Verlierer Spieler 2 und umgekehrt
$loser = ($game['player1_name'] === $winner) ? $game['player2_name'] : $game['player1_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Game Over</title>
</head>
<body>
    <!-- Anzeige der Gewinner- und Verliererinformationen -->
    <h1>Game Over</h1>
    <p><strong>Winner:</strong> <?= htmlspecialchars($winner) ?></p>
    <p><strong>Loser:</strong> <?= htmlspecialchars($loser) ?></p>
    <!-- Ein Button, der zurück zur Startseite führt -->
    <form action="index.php">
        <button type="submit">Back to Start</button>
    </form>
</body>
</html>

