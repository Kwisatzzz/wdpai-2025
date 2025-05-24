<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];
    $stmt = $db->prepare("DELETE FROM decks WHERE deck_id = :id AND user_id = :uid");
    $stmt->execute([':id' => $delete_id, ':uid' => $_SESSION['user_id']]);
}

$stmt = $db->prepare("SELECT * FROM decks WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Decks – FlashAI";
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Your Decks</h1>
  <div class="add-deck-container">
    <a href="deck_creation.php">
      <button class="add-deck-btn">+ Add Deck</button>
    </a>
  </div>

</section>

  <?php if (empty($decks)): ?>
    <p>You haven't created any decks yet.</p>
  <?php else: ?>
    <ul class="deck-list">
      <?php foreach ($decks as $deck): ?>
        <li class="deck-item">
          <div class="deck-name">
            <strong><?php echo htmlspecialchars($deck['name']); ?></strong>
          </div>
          <div class="deck-actions">
            <a href="study.php?deck_id=<?php echo $deck['deck_id']; ?>">
              <button>Study</button>
            </a>
            <a href="deck_view.php?id=<?php echo $deck['deck_id']; ?>">
              <button>Modify</button>
            </a>
            <form method="post" onsubmit="return confirm('Are you sure you want to delete this deck?');" style="display:inline;">
              <input type="hidden" name="delete_id" value="<?php echo $deck['deck_id']; ?>">
              <button type="submit">Delete</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
