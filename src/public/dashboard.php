<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();

$stmt = $db->prepare("
  SELECT * FROM decks
  WHERE user_id = :uid AND last_studied_at IS NOT NULL
  ORDER BY last_studied_at DESC
  LIMIT 1
");
$stmt->execute([':uid' => $_SESSION['user_id']]);
$recent_deck = $stmt->fetch(PDO::FETCH_ASSOC);

$pageTitle = "Dashboard – FlashAI";
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Welcome to FlashAI!</h1>
  <p>You're now logged in. Let's get learning.</p>
</section>

<section class="cards">
  <div class="card clickable-card" onclick="window.location.href='deck_creation.php'">
    <h2>Create New Deck</h2>
  </div>

  <div class="card">
    <h2>Continue Learning</h2>

    <?php if ($recent_deck): ?>
      <div class="recent-deck-preview">
        <p><strong><?php echo htmlspecialchars($recent_deck['name']); ?></strong></p>

          <?php
          $stmt = $db->prepare("SELECT COUNT(*) FROM flashcards WHERE deck_id = :deck_id");
          $stmt->execute([':deck_id' => $recent_deck['deck_id']]);
          $total_cards = $stmt->fetchColumn();
          $stmt = $db->prepare("
            SELECT COUNT(*) FROM flashcard_progress p
            JOIN flashcards f ON f.card_id = p.card_id
            WHERE p.user_id = :uid AND p.status = 'good' AND f.deck_id = :deck_id
          ");
          $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':deck_id' => $recent_deck['deck_id']
          ]);
          $good_cards = $stmt->fetchColumn();

          $progress = $total_cards > 0 ? round(($good_cards / $total_cards) * 100) : 0;
          ?>
          <div class="progress-bar">
            <div class="progress" style="width: <?php echo $progress; ?>%"></div>
          </div>
          <p><?php echo $progress; ?>% complete</p>

          <a class="study-link" href="study.php?deck_id=<?php echo $recent_deck['deck_id']; ?>">Resume</a>

      </div>
    <?php else: ?>
      <p>No recent decks.</p>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>AI Enhancement</h2>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
