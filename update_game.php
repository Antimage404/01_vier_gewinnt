<?php
require 'db_connection.php';

$gameId = $_GET['game_id'];
$playerName = $_GET['player_name'];

$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    echo json_encode(['error' => 'Game not found']);
    exit;
}

// Bestimme den aktuellen Spieler
$currentTurnName = ($game['current_turn'] === 'player1') ? $game['player1_name'] : $game['player2_name'];
$isYourTurn = ($playerName === $currentTurnName);

// JSON-Antwort generieren
$response = [
    'board_state' => json_decode($game['board_state']),
    'current_turn_name' => $isYourTurn ? 'Your Turn' : $currentTurnName,
    'status' => $game['status'], // Füge den Spielstatus hinzu
    'winner' => $game['winner_name'] ?? null // Gewinner, falls verfügbar
];

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
