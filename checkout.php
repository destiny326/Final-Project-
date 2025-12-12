<?php
session_start();
require_once "config.php"; // DB connection

if (empty($_SESSION['cart'])) {
  echo "Cart is empty.";
  exit;
}

$userId = $_SESSION['user_id'] ?? 1; // assume logged in user id=1 for demo
$cart = $_SESSION['cart'];

foreach ($cart as $item) {
  $stmt = $conn->prepare("INSERT INTO Tickets (user_id, event_id, quantity, status, created_at) VALUES (?, ?, 1, 'confirmed', NOW())");
  $stmt->bind_param("ii", $userId, $item['id']);
  $stmt->execute();
  $stmt->close();
}

$_SESSION['cart'] = [];
header("Location: tickets.php");
exit;
