<?php

include_once("leaderboard.php");

// session_start();
$turnCount = $_GET["score"] ?? 999; // Fetch the player's score
// session_destroy(); // Destroy the session to reset the game

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? 'Anonymous';
    $score = $turnCount;

    // Call the function to save the player's score
    fillLeaderboard($pseudo, $score);

    // Redirect to leaderboard display after saving the score
    header("Location: leaderboard2.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cards.css">
    <title>Game Over</title>
</head>
<body>
    <h1>Félicitations ! Vous avez terminé la partie.</h1>
    <p>Nombre de coups : <?= $turnCount ?></p>

    <form method="POST">
        <label for="pseudo">Entrez votre prénom :</label>
        <input type="text" id="pseudo" name="pseudo" required>
        <button type="submit">Sauvegarder le score</button>
    </form>

    <a href="test2.php">Rejouer</a>
</body>
</html>
