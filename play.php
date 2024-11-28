<?php
// Tutubalin (Antimage404)
// Import der Datenbankverbindung
// Die Datenbankverbindung wird geladen, um Spielinformationen abzurufen.
require 'db_connection.php';

// Parameter extrahieren
// Der Spiel-ID (game_id) und der Spielername (player_name) werden aus der URL (per GET) entnommen.
$gameId = $_GET['game_id'];
$playerName = $_GET['player_name'];

// Spielinformationen abrufen
// Eine SQL-Abfrage lädt das Spiel mit der angegebenen ID aus der Tabelle games.
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

// Falls kein Spiel gefunden wird, wird das Skript mit der Fehlermeldung Game not found beendet:
if (!$game) {
    die("Game not found.");
}

// Spielfeld dekodieren
// Die Spielfeld-Daten werden aus dem JSON-Format in ein PHP-Array umgewandelt.
$boardState = json_decode($game['board_state'], true);

// Zug bestimmen
// Es wird überprüft, ob der aktuelle Spieler am Zug ist, basierend auf current_turn und player_name.
$currentTurn = $game['current_turn'];
$isMyTurn = ($playerName === $game['player1_name'] && $currentTurn === 'player1') ||
            ($playerName === $game['player2_name'] && $currentTurn === 'player2');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/game.js" defer></script>
    <title>Connect Four - Play</title>
</head>
<body>
    <h1>Vier Gewinnt</h1>

<!-- Dynamische Anzeige der Spielinformationen -->
<!-- Game ID: Zeigt die aktuelle Spiel-ID. -->
    <p>Game ID: <?= htmlspecialchars($gameId) ?></p>
<!-- Current Turn: Zeigt an, ob der Spieler am Zug ist oder welcher Gegner gerade spielt. -->
    <p>Current Turn: <?= $isMyTurn ? 'Your Turn' : htmlspecialchars($game['current_turn'] === 'player1' ? $game['player2_name'] : $game['player1_name']) ?></p>
<!-- You are: Identifiziert den Spieler. -->
    <p>You are: <?= htmlspecialchars($playerName) ?></p>

<!-- Spielfeld anzeigen -->
<!-- Jede Zelle des Spielfelds wird als div-Element gerendert. -->
    <div class="game-board">
        <?php foreach ($boardState as $rowIndex => $row): ?>
            <?php foreach ($row as $colIndex => $cell): ?>
<!-- Spieler 1: Klasse player1 (wenn der Zellenwert X ist). -->
<!-- Spieler 2: Klasse player2 (wenn der Zellenwert O ist). -->      
<!-- Leere Zellen erhalten keine spezielle Klasse. -->
                <div class="cell <?= $cell === 'X' ? 'player1' : ($cell === 'O' ? 'player2' : '') ?>" 
                     data-row="<?= $rowIndex ?>" 
                     data-col="<?= $colIndex ?>">
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

<!-- Form für Spielzüge -->
<!-- Das Formular wird benutzt, um die Spalte (column) des nächsten Spielzugs zu übermitteln. -->
<!-- Die Spalte, in die der Spieler seinen Zug macht, wird per JavaScript gesetzt (über id="moveColumn"). -->
    <form id="moveForm" action="process_move.php" method="POST" style="display:none;">
        <input type="hidden" name="game_id" value="<?= $gameId ?>">
        <input type="hidden" name="player_name" value="<?= htmlspecialchars($playerName) ?>">
        <input type="hidden" name="column" id="moveColumn">
    </form>
</body>
</html>
