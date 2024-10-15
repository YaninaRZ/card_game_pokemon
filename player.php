<?php

require_once 'db.php';

class player {
    private $id;
    public $name;
    public $scores;
    private $pdo;

    public function __construct ($id = null, $name = null) {
        $this->id = $id;
        $this->name = $name;
        $this->scores = [];
        $this->pdo = (new Database())->getConnection();
    }

    public function createPlayer() {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO players (name) VALUES(:name)");
            $stmt->bindparam(':name', $this->name);
            $stmt->execute();
            $this->id = $this->pdo->lastInsertId();
    } catch (PDOException $e) {
        echo "Error creating player: " . $e->getMessage();
    }

}

    public function updatePlayer() {
        try {
            if($this->id !== null) {
                $stmt = $this->pdo->prepare("UPDATE players SET name = :name WHERE id = :id");
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':id', $this->id);
                $stmt->execute();
            }

        } catch (PDOException $e) {
        echo "Error updating player: " . $e->getMessage();
    }
}

    public function deletePlayer() {
        try {
            if($this->id !== null) {
                $stmt = $this->pdo->prepare("DELETE from players WHERE id = :id");
                $stmt->bindParam(':id', $this->id);
                $stmt->execute();
                $this->id = null;
                $this->name = null;
            }
        } catch (PDOException $e) {
        echo "Error deleting player: " . $e->getMessage();
    }

}

    public function fetchPlayerById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM players WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $playerData = $stmt->fetch();

            if ($playerData) {
                $this->id = $playerData['id'];
                $this->name = $playerData['name'];
            }  
        } catch (PDOException $e) {
            echo "Error fetching player: " . $e->getMessage();
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    // public function getInsertId() {
    //     return $this->insertId;
    // }

    // public function getDeletePlayer() {
    //     return $this->deletePlayer;
    // }

   
}

?>

