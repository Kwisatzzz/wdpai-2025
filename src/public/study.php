<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate'], $_POST['card_id'], $_GET['deck_id'])) {
    $valid = ['bad', 'ok', 'good'];
    $status = $_POST['rate'];
    $card_id = (int) $_POST['card_id'];
    $deck_id = (int) $_GET['deck_id'];

    if (in_array($status, $valid)) {
        $seen = 0;
        $nextReview = null;

        if ($status === 'good') {
            $stmt = $db->prepare("
                SELECT seen_good_count FROM flashcard_progress
                WHERE user_id = :uid AND card_id = :cid
            ");
            $stmt->execute([':uid' => $_SESSION['user_id'], ':cid' => $card_id]);
            $seen = (int) $stmt->fetchColumn();
            $seen++;
            $nextReview = date('Y-m-d H:i:s', strtotime('+' . pow(10, $seen) . ' days'));
        }

        $stmt = $db->prepare("
            INSERT INTO flashcard_progress (user_id, card_id, status, seen_good_count, next_review_at)
            VALUES (:uid, :cid, :status, :seen, :next)
            ON CONFLICT (user_id, card_id)
            DO UPDATE SET
                status = EXCLUDED.status,
                seen_good_count = EXCLUDED.seen_good_count,
                next_review_at = EXCLUDED.next_review_at,
                updated_at = NOW()
        ");
        $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':cid' => $card_id,
            ':status' => $status,
            ':seen' => $seen,
            ':next' => $nextReview
        ]);
    }

    $nextIndex = isset($_GET['i']) ? (int)$_GET['i'] + 1 : 1;
    header("Location: study.php?deck_id=$deck_id&i=$nextIndex");
    exit;
}

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

$stmt = $db->prepare("UPDATE decks SET last_studied_at = NOW() WHERE deck_id = :id");
$stmt->execute([':id' => $deck_id]);

$stmt = $db->prepare("
  SELECT f.* FROM flashcards f
  LEFT JOIN flashcard_progress p 
    ON f.card_id = p.card_id AND p.user_id = :uid
  WHERE f.deck_id = :deck_id
    AND (p.status IS NULL OR p.status != 'good' OR p.next_review_at <= NOW())
  ORDER BY f.card_id ASC
");
$stmt->execute([
  ':deck_id' => $deck_id,
  ':uid' => $_SESSION['user_id']
]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($cards);
if ($total === 0) {
    die("🎉 You've studied all cards in this deck! <a href='my_decks.php'>Return to My Decks</a>");
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
      <?php if ($index < $total - 1): ?>
        <form method="post" style="display: flex; gap: 10px;">
          <input type="hidden" name="card_id" value="<?php echo $current['card_id']; ?>">
          <button name="rate" value="bad" style="background: #ef4444; color: white; border: none; padding: 10px 16px; border-radius: 6px;">Bad</button>
          <button name="rate" value="ok" style="background: #facc15; color: black; border: none; padding: 10px 16px; border-radius: 6px;">Ok</button>
          <button name="rate" value="good" style="background: #10b981; color: white; border: none; padding: 10px 16px; border-radius: 6px;">Good</button>
        </form>
      <?php else: ?>
        <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;">
          <a href="study.php?deck_id=<?php echo $deck_id; ?>&i=0">
            <button style="background: #4f46e5; color: white; padding: 10px 16px; border: none; border-radius: 6px;">🔁 Again</button>
          </a>
          <a href="my_decks.php">
            <button style="background: #9ca3af; color: black; padding: 10px 16px; border: none; border-radius: 6px;">📚 See other decks</button>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
