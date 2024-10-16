<?php

require_once 'Player.php';

try {
    
    $player = new Player(null, "Will Smith"); 
    
    if ($player) {
        
        $player->createPlayer();

        if ($player->getId()) {
            echo "New player created with ID: " . $player->getId() . "<br>";
            echo "Fetched Player Name: " . $player->getName() . "<br>";

    
            $player->name = "Will Smith Updated";
            $player->updatePlayer();
            echo "Player name updated to: " . $player->getName() . "<br>";

            
            $player->fetchPlayerById($player->getId());
            echo "After Update, Fetched Player Name: " . $player->getName() . "<br>";

            
            $player->deletePlayer();
            echo "Player deleted.<br>";

    
            $player->fetchPlayerById($player->getId());
            echo "After Deletion, Fetched Player Name: " . $player->getName() . "<br>"; 
        } else {
            echo "Failed to create player. <br>";
        }
    } else {
        echo "Failed to instantiate Player object.<br>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
