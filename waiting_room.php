<?php
require 'db_connection.php';

$gameId = $_GET['game_id'];
$playerName = $_GET['player_name'];

// Lade Spielinformationen
$stmt = $pdo->prepare("SELECT * FROM games WHERE id = ?");
$stmt->execute([$gameId]);
$game = $stmt->fetch();

if (!$game) {
    die("Game not found.");
}

// Prüfe Spielstatus und leite nur weiter, wenn das Spiel bereit ist
if ($game['player2_name'] !== null && $game['status'] === 'ongoing') {
    header("Location: play.php?game_id=$gameId&player_name=$playerName");
    exit;
}

// Wenn kein zweiter Spieler da ist, warte weiter
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waiting Room</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta http-equiv="refresh" content="5"> <!-- Aktualisiert alle 5 Sekunden -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            font-size: 2.5rem;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            margin: 5px 0;
        }

        .container {
            text-align: center;
            background: white;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 80%;
            max-width: 400px;
        }

        .loading {
            margin-top: 20px;
        }

        .loading span {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 0 5px;
            background-color: #4CAF50;
            border-radius: 50%;
            animation: loading 1.5s infinite;
        }

        .loading span:nth-child(2) {
            animation-delay: 0.3s;
        }

        .loading span:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes loading {
            0%, 80%, 100% {
                transform: scale(0);
            }
            40% {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Waiting Room</h1>
        <p>Game ID: <?= htmlspecialchars($gameId) ?></p>
        <p>Your Name: <?= htmlspecialchars($playerName) ?></p>
        <div class="loading">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</body>
</html>