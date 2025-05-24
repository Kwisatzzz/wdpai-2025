<?php
require_once '../classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = User::register($_POST['email'], $_POST['password'], $_POST['name']);
    if ($result === true) {
        header('Location: login.php?registered=1');
        exit;
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FlashAI – Register</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

  <div class="container">
    <div class="card">
      <h1>FlashAI</h1>
      <p>Create your account</p>

      <form method="post">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required placeholder="Your name" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email" />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Create a password" />

        <button type="submit">Sign up</button>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
      </form>

      <p class="signup">Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>

</body>
</html>
