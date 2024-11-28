<?php
require 'db_connection.php';

$playerName = $_POST['player_name'] ?? 'Guest';

// Suche nach einem wartenden Spiel
$stmt = $pdo->query("SELECT * FROM games WHERE status = 'waiting' LIMIT 1");
$game = $stmt->fetch();

if ($game) {
    // Spieler 2 tritt bei
    $update = $pdo->prepare("UPDATE games SET player2_name = ?, status = 'ongoing' WHERE id = ?");
    $update->execute([$playerName, $game['id']]);
    $gameId = $game['id'];
} else {
    // Neues Spiel erstellen
    $insert = $pdo->prepare("INSERT INTO games (player1_name) VALUES (?)");
    $insert->execute([$playerName]);
    $gameId = $pdo->lastInsertId();
}

// Weiterleitung zur Warteseite
header("Location: waiting_room.php?game_id=$gameId&player_name=$playerName");
exit;
?>
