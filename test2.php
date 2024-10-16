<?php
session_start();

class Card {
    private $id;
    private $image;
    private $isFlipped = false;
    private $isMatched = false;

    public function __construct($id, $image) { // initialise l’id et l’image 
        $this->id = $id;
        $this->image = $image;
    }

    public function flip() { $this->isFlipped = true; }//retourne la carte
    public function match() { $this->isMatched = $this->isFlipped = true; }// apparie et retourne
    public function reset() { if (!$this->isMatched) $this->isFlipped = false; }//Remet la carte face cachée
    public function isFlipped() { return $this->isFlipped; }//envoie l’état de la carte (retournée ou non).
    public function isMatchedWith(Card $other) { return $this->image === $other->getImage(); }// Compare l’image de cette carte avec une autre pour vérifier si elles sont identiques.
    public function getID() { return $this->id; }// Renvoient respectivement l’ID et l’image de la carte.
    public function getImage() { return $this->image; }//Renvoient respectivement l’ID et l’image de la carte.

    public function display() {// Affiche la carte
        $img = $this->isFlipped ? $this->image : 'card_back.webp';
        echo "<form method='POST' style='display:inline;'>
                <input type='hidden' name='flip' value='{$this->id}'>
                <button type='submit' style='background:none;border:none;padding:0;cursor:pointer;'>
                    <img src='$img' class='imginverse' />
                </button>
              </form>";
    }

    public function handleFlip($id) {//Si l’ID de la carte correspond à celui envoyé via le formulaire, la carte est retournée.

        if ($this->id == $id) $this->flip();
    }
}

function displayPairSelectionForm() {//Affiche un formulaire permettant au joueur de choisir le nombre de paires pour la partie.
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

// Initialisation du jeu
$allImages = ['arcanin.png', 'evoli.png', 'goupix.png', 'mysdibule.png', 'rondoudou.png'];
if (!isset($_SESSION['num_pairs'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num_pairs'])) {
        $_SESSION['num_pairs'] = (int)$_POST['num_pairs'];
    } else {
        displayPairSelectionForm();
        exit;
    }
}

$numPairs = $_SESSION['num_pairs'];
$images = array_slice($allImages, 0, $numPairs);
$cards = [];

foreach ($images as $index => $image) {
    $cards[] = new Card($index + 1, $image);
    $cards[] = new Card($index + $numPairs + 1, $image);
}
//Crée une liste de cartes en double pour chaque image sélectionnée.

if (!isset($_SESSION['shuffled_cards'])) {
    shuffle($cards);
    $_SESSION['shuffled_cards'] = $cards;
} else {
    $cards = $_SESSION['shuffled_cards'];
}

// Gestion du retournement de cartes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['flip'])) {
    $flippedCards = $_SESSION['flipped_cards'] ?? [];
    $flipId = $_POST['flip'];

    foreach ($cards as $card) {
        if (in_array($card->getID(), $flippedCards)) $card->reset();
        $card->handleFlip($flipId);
    }

    if (!in_array($flipId, $flippedCards)) {
        $flippedCards[] = $flipId;
        $_SESSION['flipped_cards'] = $flippedCards;
    }

    if (count($flippedCards) == 2) {
        [$firstID, $secondID] = $flippedCards;
        $firstCard = $secondCard = null;

        foreach ($cards as $card) {
            if ($card->getID() == $firstID) $firstCard = $card;
            if ($card->getID() == $secondID) $secondCard = $card;
        }

        if ($firstCard && $secondCard && $firstCard->isMatchedWith($secondCard)) {
            $firstCard->match();
            $secondCard->match();
        }

        $_SESSION['turn_count'] = ($_SESSION['turn_count'] ?? 0) + 1;
        $_SESSION['flipped_cards'] = [];
    }
}

if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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
<p>3 ans et + </p></br>
    <div class="container">
        <p class="texte">Nombre de coups</p>
        <p class="nbrtour"><?= $_SESSION['turn_count'] ?? 0 ?></p>
    </div>

    <div class="container-cards">
        <?php foreach ($cards as $card) { ?>
            <div class='img-container'>
                <?= $card->display() ?>
            </div>
        <?php } ?>
    </div>

    <div class="container2">
        <form method="POST">
            <button type="submit" name="reset" class="texte2">Redémarrer</button>
        </form>
    </div>
</body>
</html>
