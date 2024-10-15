<?php
include('db.php'); 
error_reporting(E_ALL);
ini_set('display_errors', 1);


class ChoiceCards {
    public $nombre_paires;

    public function __construct($nombre_paires = 3) {
        $this->nombre_paires = isset($_POST['nombre_paires']) ? $_POST['nombre_paires'] : 3;
        $this->nombre_paires = max(3, min($this->nombre_paires, 6));
    }
}


class GestionCards {
    
        private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCards($nombre_paires) {
        $query = $this->pdo->query("SELECT * FROM cards ORDER BY RAND() LIMIT $nombre_paires");//random
        $cards = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_merge($cards, $cards); // Duplique les cartes
    }
}


class ShuffleCards {
    public function shuffle_cards($cards) {
        shuffle($cards);
        return $cards;
    }
}

$choixCartes = new ChoiceCards();
$gestionCartes = new GestionCards($pdo);
$cards = $gestionCartes->getCards($choixCartes->nombre_paires);
$tirageAleatoire = new ShuffleCards();
$cartes_dupliquees = $tirageAleatoire->shuffle_cards($cards);

// Initialiser l'état des cartes si non défini
$_SESSION['etat_cartes'] = $_SESSION['etat_cartes'] ?? array_fill(0, count($cartes_dupliquees), true);

// Gestion du clic sur une carte rectp
if (isset($_POST['card_index'])) {
    $_SESSION['etat_cartes'][$_POST['card_index']] = false;
}




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cards.css">
    <title>Cartes</title>
</head>
<body>
    <form method="POST" action="">
        <label for="nombre_paires">Festival d'automne pokemons</label>
        <p>Trouve les paires:</p>
    </form>

<div class="cartes_block">
    <?php foreach ($cartes_dupliquees as $index => $card): ?>
        <form method="POST" style="display: inline-block;">
            <input type="hidden" name="card_index" value="<?= $index ?>">
            <button type="submit" style="border: none; background: none;">
                <img src="<?= htmlspecialchars($card[$_SESSION['etat_cartes'][$index] ? 'verso' : 'recto']) ?>"
                     alt="Carte" width="200" height="250">
            </button>
        </form>
    <?php endforeach; ?>
</div>
    </div>
</body>
</html>



