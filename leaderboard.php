<?php

global $mysqlClient;
$mysqlClient = new PDO(
    'mysql:host=localhost;dbname=card_game;charset=utf8',
    'root',
    ''
);

// Fonction pour initialiser le leaderboard
function initLeaderboard()
{
    $GLOBALS["mysqlClient"]->query("DELETE from leaderboard");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (1, 'local', 15)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (2, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (3, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (4, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (5, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (6, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (7, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (8, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (9, 'local', 20)");
    $GLOBALS["mysqlClient"]->query("INSERT INTO leaderboard VALUES (10, 'local', 20)");
}

// Fonction pour insérer le nouveau score
function fillLeaderboard($pseudo, $score)
{
    $pos = 0;
    $onLeader = FALSE;
    // On cherche la position où insérer le nouveau score
    while (!$onLeader && $pos < 10) {
        $pos++;
        $user = $GLOBALS["mysqlClient"]->prepare("SELECT * FROM leaderboard WHERE pos = :pos LIMIT 1");
        $user->bindValue(":pos", $pos, PDO::PARAM_STR);
        $user->execute();
        $values = $user->fetchAll();
        if ($values[0]["score"] > $score)
            $onLeader = TRUE;
    }
    // Si le nouveau score ne rentre pas dans le leaderboard on ne fait rien
    if (!$onLeader)
        return 0;
    // Si le nouveau score rentre dans le leaderboard on décale tous ceux qui sont en-dessous
    for ($j = 10; $j > $pos; $j--) {
        $user = $GLOBALS["mysqlClient"]->prepare("SELECT * FROM leaderboard WHERE pos = :j LIMIT 1");
        $user->bindValue(":j", $j - 1, PDO::PARAM_STR);
        $user->execute();
        $values = $user->fetchAll();
        $user = $GLOBALS["mysqlClient"]->prepare("UPDATE leaderboard SET pseudo = :pseudo,  score = :score WHERE pos = $j");
        $user->bindValue(":pseudo", $values[0]["pseudo"], PDO::PARAM_STR);
        $user->bindValue(":score", $values[0]["score"], PDO::PARAM_STR);
        $user->execute();
    }
    // On insère le nouveau score dans le leaderboard
    $user = $GLOBALS["mysqlClient"]->prepare("UPDATE leaderboard SET pseudo = :pseudo,  score = :score WHERE pos = $j");
    $user->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
    $user->bindValue(":score", $score, PDO::PARAM_STR);
    $user->execute();
    return 1;
}

if (!$GLOBALS["mysqlClient"])
    echo "L'accès PDO a fail<br/>";
// else
    // initLeaderboard();

?>