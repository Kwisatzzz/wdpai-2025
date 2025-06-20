<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'FlashAI'; ?></title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="dashboard-body">

<header class="site-header">
  <a href="dashboard.php" class="logo">
    FlashAI <span class="badge">Beta</span>
  </a>
  
  <nav class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="my_decks.php">My Decks</a>
    <a href="ai_assistant.php">AI Assistant</a>
  </nav>

  <div class="header-right">
    <a class="logout-link" href="logout.php">Logout</a>
    <div class="avatar">
      <img src="https://ui-avatars.com/api/?name=u" alt="Avatar" />
    </div>
  </div>
</header>

<main class="main">
