<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$card_id = (int) $data['id'];

$db = Database::connect();

$stmt = $db->prepare("DELETE FROM flashcards 
  WHERE card_id = :cid AND deck_id IN (
    SELECT deck_id FROM decks WHERE user_id = :uid
  )");
$stmt->execute([':cid' => $card_id, ':uid' => $_SESSION['user_id']]);
