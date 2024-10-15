<?php

require_once 'player.php';

try {

$Player = new Player(null, "Will Smith");
$Player->createPlayer();

echo "New player created with ID: " . $Player->getId() . "<br>";
echo "Fetched Player Name: " . $player->getName() . "<br>";

$player->name = "Will Smith Updated";
$player->updatePlayer();

echo "Player name updated to: " . $player->getName() . "<br>";

$player->$fetchPlayerById($player->getId());
echo "After Update, Fetched Player Name: " . $player->getName() . "<br>";

$player->fetchPlayerById($player->getId());
echo "After Deletion, Fetched Player Name: " . $player->getName() . "<br>";


} catch (Exception $e) {
    echo $e->getMessage();
}

?>


