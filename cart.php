<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = array_sum(array_map(fn($i)=>$i['price'], $cart));
?>
<!DOCTYPE html>
<html>
<head><title>Cart</title></head>
<body>
<h1>Your Cart</h1>
<?php if (empty($cart)): ?>
  <p>No items in cart.</p>
<?php else: ?>
  <table border="1">
    <tr><th>Event</th><th>Date</th><th>Venue</th><th>Price</th></tr>
    <?php foreach ($cart as $item): ?>
      <tr>
        <td><?php echo $item['title']; ?></td>
        <td><?php echo $item['date']; ?></td>
        <td><?php echo $item['venue']; ?></td>
        <td>$<?php echo $item['price']; ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
  <p><strong>Total:</strong> $<?php echo $total; ?></p>
  <a href="checkout.php">Proceed to Checkout</a>
<?php endif; ?>
</body>
</html>
