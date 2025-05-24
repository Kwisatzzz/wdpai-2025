<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM decks WHERE user_id = :uid ORDER BY created_at DESC");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Decks – FlashAI";
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Your Decks</h1>

  <?php if (empty($decks)): ?>
    <p>You haven't created any decks yet.</p>
  <?php else: ?>
    <ul class="deck-list">
      <?php foreach ($decks as $deck): ?>
        <li class="deck-item">
          <a href="deck_view.php?id=<?php echo $deck['deck_id']; ?>">
            <?php echo htmlspecialchars($deck['name']); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
