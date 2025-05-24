<?php
require_once '../classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (User::login($_POST['email'], $_POST['password'])) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FlashAI – Login</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

  <div class="container">
    <div class="card">
      <h1>FlashAI</h1>
      <p>Welcome back</p>

      <form method="post">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required placeholder="Enter your email" />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required placeholder="Enter your password" />

        <div class="remember">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember">Remember me</label>
        </div>

        <button type="submit">Sign in</button>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
      </form>

      <p class="signup">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
  </div>

</body>
</html>
