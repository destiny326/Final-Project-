<?php
session_start();
require_once "config.php";

$userId = $_SESSION['user_id'] ?? 1;

$sql = "SELECT T.id, T.created_at, E.title, E.date, E.venue, E.price
        FROM Tickets T
        JOIN Events E ON T.event_id = E.id
        WHERE T.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>My Tickets</title></head>
<body>
<h1>My Tickets</h1>
<table border="1">
  <tr><th>Date Purchased</th><th>Event</th><th>Date</th><th>Venue</th><th>Price</th></tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['created_at']; ?></td>
      <td><?php echo $row['title']; ?></td>
      <td><?php echo $row['date']; ?></td>
      <td><?php echo $row['venue']; ?></td>
      <td>$<?php echo $row['price']; ?></td>
    </tr>
  <?php endwhile; ?>
</table>
</body>
</html>
