<?php
require 'db_connection.php';

$gameId = $_POST['game_id'] ?? null;
$playerName = $_POST['player_name'] ?? null;
$column = (int)($_POST['column'] ?? -1);

// Spiel aus der Datenbank laden
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found.");
}

// Spielfeld-Daten dekodieren
$boardState = json_decode($game['board_state'], true);

// Prüfen, ob der Spieler am Zug ist
$currentTurn = $game['current_turn'];
if (
    ($playerName === $game['player1_name'] && $currentTurn !== 'player1') ||
    ($playerName === $game['player2_name'] && $currentTurn !== 'player2')
) {
    die("It's not your turn.");
}

// Setzen des Steins in der Spalte
for ($row = 5; $row >= 0; $row--) {
    if ($boardState[$row][$column] === "") {
        $boardState[$row][$column] = ($currentTurn === 'player1') ? 'X' : 'O';
        break;
    }
}

// Überprüfen, ob die Spalte voll ist
if ($row < 0) {
    die("Invalid move: column is full.");
}

// Funktion zur Siegprüfung
function checkWin($boardState, $symbol) {
    // Horizontale Prüfung
    for ($row = 0; $row < 6; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($boardState[$row][$col] === $symbol &&
                $boardState[$row][$col + 1] === $symbol &&
                $boardState[$row][$col + 2] === $symbol &&
                $boardState[$row][$col + 3] === $symbol) {
                return true;
            }
        }
    }

    // Vertikale Prüfung
    for ($col = 0; $col < 7; $col++) {
        for ($row = 0; $row < 3; $row++) {
            if ($boardState[$row][$col] === $symbol &&
                $boardState[$row + 1][$col] === $symbol &&
                $boardState[$row + 2][$col] === $symbol &&
                $boardState[$row + 3][$col] === $symbol) {
                return true;
            }
        }
    }

    // Diagonale Prüfung (\)
    for ($row = 0; $row < 3; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($boardState[$row][$col] === $symbol &&
                $boardState[$row + 1][$col + 1] === $symbol &&
                $boardState[$row + 2][$col + 2] === $symbol &&
                $boardState[$row + 3][$col + 3] === $symbol) {
                return true;
            }
        }
    }

    // Diagonale Prüfung (/)
    for ($row = 3; $row < 6; $row++) {
        for ($col = 0; $col < 4; $col++) {
            if ($boardState[$row][$col] === $symbol &&
                $boardState[$row - 1][$col + 1] === $symbol &&
                $boardState[$row - 2][$col + 2] === $symbol &&
                $boardState[$row - 3][$col + 3] === $symbol) {
                return true;
            }
        }
    }

    return false;
}

// Siegprüfung
$symbol = ($currentTurn === 'player1') ? 'X' : 'O';
if (checkWin($boardState, $symbol)) {
    $update = $pdo->prepare("UPDATE games SET status = 'finished', winner_name = ? WHERE id = ?");
    $update->execute([$playerName, $gameId]);

    // Weiterleitung für den Gewinner
    header("Location: result.php?game_id=$gameId&winner=$playerName");
    exit;
}

// Spielfeld und Zug aktualisieren
$newBoardState = json_encode($boardState);
$nextTurn = ($currentTurn === 'player1') ? 'player2' : 'player1';
$update = $pdo->prepare("UPDATE games SET board_state = ?, current_turn = ? WHERE id = ?");
$update->execute([$newBoardState, $nextTurn, $gameId]);

// Zurück zur Spielseite
header("Location: play.php?game_id=$gameId&player_name=$playerName");
exit;
?>
