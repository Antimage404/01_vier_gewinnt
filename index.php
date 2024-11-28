<?php
require 'db_connection.php';

// Fetch the last 10 completed games
$stmt = $pdo->prepare("SELECT player1_name, player2_name, winner_name, finished_at 
                       FROM games 
                       WHERE status = 'finished' 
                       ORDER BY finished_at DESC 
                       LIMIT 10");
$stmt->execute();
$recentGames = $stmt->fetchAll();
?>
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
    <form action="waiting.php" method="post">
        <input type="text" name="player_name" placeholder="Your Name" required>
        <button type="submit">Start Game</button>
    </form>

    <!-- Leaderboard -->
    <h2>Leaderboard - Recent Games</h2>
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
                    <td colspan="4">No games available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
