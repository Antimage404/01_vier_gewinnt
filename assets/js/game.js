//Weinstein
// Variablen für Spiel-ID und Spielername aus der URL holen
const gameId = new URLSearchParams(window.location.search).get('game_id');
const playerName = new URLSearchParams(window.location.search).get('player_name');

// Event-Listener für Zellen hinzufügen
document.querySelectorAll('.cell').forEach(cell => {
    cell.addEventListener('click', () => {
        // Hole den aktuellen Zug-Text und trimme die Beschriftung
        const currentTurn = document.querySelector('p:nth-child(3)').textContent.replace('Current Turn: ', '').trim();
        console.log('Extracted Current Turn Text:', currentTurn); // Debugging

        // Überprüfen, ob es der Zug des Spielers ist
        if (currentTurn !== 'Your Turn') {
            console.log('It is not your turn. Click event aborted.');
            return; // Kein Formular-Submit, wenn es nicht der Zug des Spielers ist
        }

        // Spaltennummer auslesen
        const col = cell.getAttribute('data-col');
        console.log(`Column clicked: ${col}`); // Debugging: Geklickte Spalte

        // Formular ausfüllen und absenden
        document.getElementById('moveColumn').value = col;
        console.log('Form prepared for submission with column:', col); // Debugging: Formularwert setzen

        document.getElementById('moveForm').submit();
        console.log('Form submitted.'); // Debugging: Formular abgeschickt
    });
});

// Funktion zur Aktualisierung des Spielfelds
function updateGame() {
    fetch(`update_game.php?game_id=${gameId}&player_name=${playerName}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error); // Fehler ausgeben, falls vorhanden
                return;
            }

            // Prüfen, ob das Spiel beendet ist
            if (data.status === 'finished') {
                console.log('Game is finished. Redirecting to result page...');
                window.location.href = `result.php?game_id=${gameId}&winner=${data.winner}`;
                return; // Weitere Updates stoppen
            }

            // Debugging: JSON-Daten anzeigen
            console.log('Game update received:', data);

            // Spielfeld aktualisieren
            const board = data.board_state;
            const cells = document.querySelectorAll('.cell');
            cells.forEach(cell => {
                const row = cell.getAttribute('data-row');
                const col = cell.getAttribute('data-col');
                cell.className = 'cell'; // Entferne alle bestehenden Klassen
                if (board[row][col] === 'X') {
                    cell.classList.add('player1');
                } else if (board[row][col] === 'O') {
                    cell.classList.add('player2');
                }
            });

            // Aktuellen Spieler anzeigen
            document.querySelector('p:nth-child(3)').textContent = `Current Turn: ${data.current_turn_name}`;
        })
        .catch(error => console.error('Error updating game:', error));
}


// Automatische Aktualisierung alle 2 Sekunden
setInterval(updateGame, 2000);
