<?php
require_once '../classes/User.php';
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
<form method="post">
    <h2>Register</h2>
    <input name="name" required placeholder="Name"><br>
    <input type="email" name="email" required placeholder="Email"><br>
    <input type="password" name="password" required placeholder="Password"><br>
    <button type="submit">Register</button>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>
