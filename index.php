<?php
// Tutubalin (Antimage404)
// Laden der Datenbankverbindung
require 'db_connection.php';

// Fragt die letzten 10 Spiele aus der Tabelle games ab, bei denen der Status finshed ist
// Die Ergebnisse werden in der Variablen $recentGames gespeichert. Jedes
// Spiel enthält die Namen der Spieler, den Gewinner (oder ein
// Unentschieden, falls winner_name leer ist) und das Abschlussdatum.
$stmt = $pdo->prepare("SELECT player1_name, player2_name, winner_name, finished_at 
                       FROM games 
                       WHERE status = 'finished' 
                       ORDER BY finished_at DESC 
                       LIMIT 10");
$stmt->execute();
$recentGames = $stmt->fetchAll();
?>

<!-- Erzeugung eine HTML-Seite mit einem Formular für neue Spieler -->
<!-- und einer Tabelle, die die letzten 10 abgeschlossenen Spiele anzeigt. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vier Gewinnt</title>
	<link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to |Vier Gewinnt|</h1>
	
<!-- Spielstart Form -->
<!-- Spieler können ihren Namen eingeben und mit Klick auf den Button ein -->	
<!-- neues Spiel starten. Die Daten werden per POST an die Datei waiting.php gesendet. -->		
    <form action="waiting.php" method="post">
        <input type="text" name="player_name" placeholder="Your Name" required>
        <button type="submit">Start Game</button>
    </form>

<!-- Leaderboard -->
    <h2>Leaderboard - Recent Games</h2>

<!-- Es wird eine Tabelle erstellt, die die letzten Spiele anzeigt. Die Spalten -->
<!-- enthalten die Namen der Spieler, den Gewinner (oder Draw für ein Unentschieden) -->
<!-- und das Abschlussdatum. -->
    <table>
        <thead>
            <tr>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>Winner</th>
                <th>Finished At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recentGames)): ?>
                <?php foreach ($recentGames as $game): ?>
                    <tr>
                        <td><?= htmlspecialchars($game['player1_name']) ?></td>
                        <td><?= htmlspecialchars($game['player2_name']) ?></td>
                        <td><?= htmlspecialchars($game['winner_name'] ?: 'Draw') ?></td>
                        <td><?= htmlspecialchars($game['finished_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
<!-- Falls keine Spiele verfügbar sind, wird die Nachricht "No games available" angezeigt. -->
                    <td colspan="4">No games available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
