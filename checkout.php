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
  // FIXED: Changed 'confirmed' to 'booked' to match ENUM values
  $stmt = mysqli_prepare($link, "INSERT INTO tickets (user_id, event_id, quantity, status, created_at) VALUES (?, ?, 1, 'booked', NOW())");
  
  mysqli_stmt_bind_param($stmt, "ii", $userId, $item['id']);
  
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

$_SESSION['cart'] = [];
header("Location: tickets.php");
exit;
?>