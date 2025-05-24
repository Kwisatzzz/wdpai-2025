<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();

if (!isset($_GET['deck_id']) || !is_numeric($_GET['deck_id'])) {
    die("Invalid deck ID.");
}

$deck_id = (int) $_GET['deck_id'];

$stmt = $db->prepare("SELECT name FROM decks WHERE deck_id = :id AND user_id = :uid");
$stmt->execute([':id' => $deck_id, ':uid' => $_SESSION['user_id']]);
$deck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deck) {
    die("Deck not found or access denied.");
}

$stmt = $db->prepare("SELECT * FROM flashcards WHERE deck_id = :deck_id ORDER BY card_id ASC");
$stmt->execute([':deck_id' => $deck_id]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($cards);

if ($total === 0) {
    die("This deck has no flashcards yet.");
}

$index = isset($_GET['i']) ? (int) $_GET['i'] : 0;
if ($index < 0) $index = 0;
if ($index >= $total) $index = $total - 1;

$current = $cards[$index];

$pageTitle = "Study: " . htmlspecialchars($deck['name']);
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Studying: <?php echo htmlspecialchars($deck['name']); ?></h1>
  <p>Card <?php echo $index + 1; ?> of <?php echo $total; ?></p>
</section>

<section class="cards">
  <div class="card" style="max-width: 500px;">
    <h2>Front</h2>
    <p><?php echo htmlspecialchars($current['front']); ?></p>

    <hr>

    <h2>Back</h2>
    <p><?php echo htmlspecialchars($current['back']); ?></p>

    <div style="margin-top: 20px;">
      <?php if ($index > 0): ?>
        <a href="?deck_id=<?php echo $deck_id; ?>&i=<?php echo $index - 1; ?>">
          <button>Previous</button>
        </a>
      <?php endif; ?>

      <?php if ($index < $total - 1): ?>
        <a href="?deck_id=<?php echo $deck_id; ?>&i=<?php echo $index + 1; ?>">
          <button>Next</button>
        </a>
      <?php else: ?>
        <p><a href="my_decks.php">🎉 You've reached the end!</a></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
