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
        $stmt = $db->prepare("
            INSERT INTO flashcard_progress (user_id, card_id, status)
            VALUES (:uid, :cid, :status)
            ON CONFLICT (user_id, card_id)
            DO UPDATE SET status = EXCLUDED.status, updated_at = NOW()
        ");
        $stmt->execute([
            ':uid' => $_SESSION['user_id'],
            ':cid' => $card_id,
            ':status' => $status
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
    AND (p.status IS NULL OR p.status != 'good')
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

    <form method="post" style="margin-top: 20px; display: flex; gap: 10px;">
      <input type="hidden" name="card_id" value="<?php echo $current['card_id']; ?>">
      <button name="rate" value="bad">Bad</button>
      <button name="rate" value="ok">Ok</button>
      <button name="rate" value="good">Good</button>
    </form>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
