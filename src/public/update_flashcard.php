<?php
require_once '../includes/auth.php';
require_login();
require_once '../includes/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$card_id = (int) $data['id'];
$field = $data['field'];
$value = trim($data['value']);

if (!in_array($field, ['front', 'back'])) exit;

$db = Database::connect();

$stmt = $db->prepare("SELECT f.card_id FROM flashcards f JOIN decks d ON f.deck_id = d.deck_id WHERE f.card_id = :cid AND d.user_id = :uid");
$stmt->execute([':cid' => $card_id, ':uid' => $_SESSION['user_id']]);

if (!$stmt->fetch()) exit;

$stmt = $db->prepare("UPDATE flashcards SET $field = :value WHERE card_id = :id");
$stmt->execute([':value' => $value, ':id' => $card_id]);
