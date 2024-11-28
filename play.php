<?php
require 'db_connection.php';

$gameId = $_GET['game_id'];
$playerName = $_GET['player_name'];

// Spiel aus der Datenbank laden
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found.");
}

// Spielfeld-Daten dekodieren
$boardState = json_decode($game['board_state'], true);
$currentTurn = $game['current_turn'];

// Überprüfen, ob der Spieler am Zug ist
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
    <p>Game ID: <?= htmlspecialchars($gameId) ?></p>
    <p>Current Turn: <?= $isMyTurn ? 'Your Turn' : htmlspecialchars($game['current_turn'] === 'player1' ? $game['player2_name'] : $game['player1_name']) ?></p>
    <p>You are: <?= htmlspecialchars($playerName) ?></p>

    <div class="game-board">
        <?php foreach ($boardState as $rowIndex => $row): ?>
            <?php foreach ($row as $colIndex => $cell): ?>
                <div class="cell <?= $cell === 'X' ? 'player1' : ($cell === 'O' ? 'player2' : '') ?>" 
                     data-row="<?= $rowIndex ?>" 
                     data-col="<?= $colIndex ?>">
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <form id="moveForm" action="process_move.php" method="POST" style="display:none;">
        <input type="hidden" name="game_id" value="<?= $gameId ?>">
        <input type="hidden" name="player_name" value="<?= htmlspecialchars($playerName) ?>">
        <input type="hidden" name="column" id="moveColumn">
    </form>
</body>
</html>
