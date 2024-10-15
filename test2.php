<?php
session_start(); 

class Card {
    private $id; 
    private $isFlipped; 
    private $isMatched;        
    private $image;      

    public function __construct($id, $image) {
        $this->id = $id;
        $this->image = $image;
        $this->isFlipped = false;
        $this->isMatched = false;
    }

    public function match() {
        $this->isMatched = true;
        $this->isFlipped = true;
    }

    public function flip() {
        $this->isFlipped = true;
    }

    public function isMatched(Card $otherCard) {
        return $this->image === $otherCard->getImage();
    }

    public function getID() {
        return $this->id;
    }

    public function getImage() {
        return $this->image;
    }

    public function isAssorted() {
        return $this->isMatched;
    }

    public function isFlipped() {
        return $this->isFlipped;
    }

    public function resetFlip() {
        if (!$this->isMatched) {
            $this->isFlipped = false;
        }
    }

    public function displayCard() {
        echo "<form method='POST' style='display: inline;'>
                <input type='hidden' name='flip' value='{$this->id}'>
                <button type='submit' style='background: none; border: none; padding: 0; cursor: pointer;'>
                    <img src='" . ($this->isFlipped ? $this->image : 'card_back.webp') . "' class='imginverse' />
                </button>
              </form>";
    }

    public function handleFlip($id) {
        if ($this->id == $id) {
            $this->flip();
        }
    }
}

// Liste d'images disponibles
$allImages = [
    'arcanin.png',
    'evoli.png',
    'goupix.png',
    'mysdibule.png',
    'rondoudou.png',
];

// Choix du nombre de paires si non défini
if (!isset($_SESSION['num_pairs'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_pairs'])) {
        $_SESSION['num_pairs'] = (int)$_POST['num_pairs'];
    } else {
        displayPairSelectionForm(); // Affiche le formulaire de sélection
        exit;
    }
}

// Générer les cartes en fonction du nombre de paires choisies
$numPairs = $_SESSION['num_pairs'];
$images = array_slice($allImages, 0, $numPairs);

$cards = [];
foreach ($images as $index => $image) {
    $cards[] = new Card($index + 1, $image);
    $cards[] = new Card($index + 4, $image);
}

// Mélanger les cartes au démarrage de la session
if (!isset($_SESSION['shuffled_cards'])) {
    shuffle($cards);
    $_SESSION['shuffled_cards'] = $cards;
} else {
    $cards = $_SESSION['shuffled_cards'];
}

// Gestion du retournement des cartes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['flip'])) {
    $cardIdToFlip = $_POST['flip'];

    if (count($_SESSION['flipped_cards'] ?? []) >= 2) {
        foreach ($cards as $card) {
            if (in_array($card->getID(), $_SESSION['flipped_cards'])) {
                $card->resetFlip();
            }
        }
        $_SESSION['flipped_cards'] = [];
    }

    foreach ($cards as $card) {
        $card->handleFlip($cardIdToFlip);
    }

    if (!isset($_SESSION['flipped_cards'])) {
        $_SESSION['flipped_cards'] = [];
    }

    if (!in_array($cardIdToFlip, $_SESSION['flipped_cards'])) {
        $_SESSION['flipped_cards'][] = $cardIdToFlip;
    }

    if (count($_SESSION['flipped_cards']) == 2) {
        $firstCardId = $_SESSION['flipped_cards'][0];
        $secondCardId = $_SESSION['flipped_cards'][1];

        $firstCard = null;
        $secondCard = null;

        foreach ($cards as $card) {
            if ($card->getID() == $firstCardId) {
                $firstCard = $card;
            } elseif ($card->getID() == $secondCardId) {
                $secondCard = $card;
            }
        }

        if ($firstCard && $secondCard) {
            if ($firstCard->isMatched($secondCard)) {
                $firstCard->match();
                $secondCard->match();
            }
        }
        $_SESSION['turn_count'] = $_SESSION['turn_count'] ?? 0;
        $_SESSION['turn_count']++;
    }
}

if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

function displayPairSelectionForm() {
    echo '<form method="POST">
            <label for="num_pairs">Choisissez le nombre de paires :</label>
            <select name="num_pairs" id="num_pairs">
                <option value="3">3 Paires</option>
                <option value="4">4 Paires</option>
                <option value="6">6 Paires</option>
            </select>
            <button type="submit">Commencer</button>
          </form>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cards.css">
    <title>Memory Game</title>
</head>
<body>
    <div class="container">
        <p class="texte">Nombre de coups </p>
        <p class="nbrtour"><?php echo $_SESSION['turn_count'] ?? 0; ?></p>
    </div>

    <div class="container-cards">
        <?php
        foreach ($cards as $card) {
            echo "<div class='img-container'>";
            $card->displayCard();
            echo '</div>';
        }
        ?>
    </div>

    <div class="container2">
        <form method="POST">
            <button type="submit" name="reset" class="texte2">Redémarrer</button>
        </form>
    </div>
</body>
</html>
