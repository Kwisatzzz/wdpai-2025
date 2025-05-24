<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid deck ID");
}

$deck_id = (int) $_GET['id'];

$stmt = $db->prepare("SELECT name FROM decks WHERE deck_id = :id AND user_id = :uid");
$stmt->execute([':id' => $deck_id, ':uid' => $_SESSION['user_id']]);
$deck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$deck) {
    die("Deck not found or access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $front = trim($_POST['front']);
    $back = trim($_POST['back']);
    if ($front && $back) {
        $stmt = $db->prepare("INSERT INTO flashcards (deck_id, front, back) VALUES (:deck_id, :front, :back)");
        $stmt->execute([
            ':deck_id' => $deck_id,
            ':front' => $front,
            ':back' => $back
        ]);
        header("Location: deck_view.php?id=$deck_id");
        exit;
    }
}

$stmt = $db->prepare("SELECT * FROM flashcards WHERE deck_id = :deck_id ORDER BY created_at DESC");
$stmt->execute([':deck_id' => $deck_id]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Deck: " . htmlspecialchars($deck['name']) . " – FlashAI";
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Deck: <?php echo htmlspecialchars($deck['name']); ?></h1>

  <h2>Add a new flashcard</h2>
  <form method="post" class="card-form">
    <input type="text" name="front" placeholder="Front side" required />
    <input type="text" name="back" placeholder="Back side" required />
    <button type="submit">Add Flashcard</button>
  </form>

  <h2>Flashcards</h2>
  <?php if (empty($cards)): ?>
    <p>No flashcards yet.</p>
  <?php else: ?>
    <ul class="flashcard-list">
    <?php foreach ($cards as $card): ?>
      <?php
        $stmt = $db->prepare("
          SELECT status, seen_good_count, next_review_at
          FROM flashcard_progress
          WHERE user_id = :uid AND card_id = :cid
        ");
        $stmt->execute([
          ':uid' => $_SESSION['user_id'],
          ':cid' => $card['card_id']
        ]);
        $progress = $stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <li class="flashcard" data-id="<?php echo $card['card_id']; ?>">
        <button class="delete-x-btn" data-id="<?php echo $card['card_id']; ?>" title="Delete">&times;</button>
        <div class="flashcard-text">
          <strong>Front:</strong> <span class="editable front"><?php echo htmlspecialchars($card['front']); ?></span><br />
          <strong>Back:</strong> <span class="editable back"><?php echo htmlspecialchars($card['back']); ?></span>

          <?php if ($progress): ?>
            <div class="flashcard-status">
              <small>Status: <strong><?php echo htmlspecialchars($progress['status']); ?></strong></small><br />
              <?php if ($progress['status'] === 'good'): ?>
                <small>Seen good: <?php echo (int)$progress['seen_good_count']; ?>×</small><br />
                <small>Next review: <?php echo date('Y-m-d', strtotime($progress['next_review_at'])); ?></small>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<script src="assets/js/flashcards.js"></script>
<?php include '../includes/footer.php'; ?>
