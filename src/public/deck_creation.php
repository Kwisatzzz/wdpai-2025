<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$db = Database::connect();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['deck_name']);
    if ($name === '') {
        $errors[] = "Deck name cannot be empty.";
    } else {
        $stmt = $db->prepare("INSERT INTO decks (name, user_id) VALUES (:name, :user_id)");
        $stmt->execute([
            ':name' => $name,
            ':user_id' => $_SESSION['user_id']
        ]);
        $success = true;
    }
}

$pageTitle = "Create New Deck – FlashAI";
include '../includes/header.php';
?>

<section class="welcome">
  <h1>Create a New Deck</h1>

  <form method="post" class="card-form">
    <input type="text" name="deck_name" placeholder="Deck name" required />
    <button type="submit">Create Deck</button>
  </form>

  <?php if ($success): ?>
    <p style="color:green;">Deck created successfully!</p>
  <?php elseif (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
      <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<?php include '../includes/footer.php'; ?>
