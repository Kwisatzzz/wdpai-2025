<?php
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
  </div>
  <div class="card">
    <h2>AI Enhancement</h2>
  </div>
</section>

<?php include '../includes/footer.php'; ?>