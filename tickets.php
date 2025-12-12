<?php 

session_start();
require_once "config.php"; // DB connection

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // If user is NOT logged in, redirect to login page
    header("location: login.php");
    exit;
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your tickets.");
}

$userId = $_SESSION['user_id'];

// Query tickets with event info
$sql = "SELECT T.id, T.quantity, T.status, T.created_at, 
               E.title, E.date, E.ticket_price
        FROM Tickets T
        JOIN Events E ON T.event_id = E.id
        WHERE T.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Separate active vs expired
$activeTickets = [];
$expiredTickets = [];
$today = date("Y-m-d");

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['date'] >= $today) {
        $activeTickets[] = $row;
    } else {
        $expiredTickets[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage Pass - My Tickets</title>
</head>

<body>
    <nav id="top-menu">
    <ul>
      <li><a href="tickets.php">My tickets</a></li>
      <li><a href="Events.php">Events</a></li> <!--in this page create hte thingy ith the photo and 
      when you howver informtion comes up and put a button on that to take them to the fully detailed page
      where they can add the ticket to their cart-->
      <li><a href="bookings.php">Book Ticket</a></li>
      <li><a href="Cart.php">Cart</a></li>
      <li><a href="contact.php">Contact Us</a></li>
      <li><a href="account.php"> Account </a></li>
     <button onclick="window.location.href= 'login.php'"> LOGIN</button>
    </ul>
  </nav>
  
    <h1>My Tickets</h1>

    <h2>Upcoming Tickets</h2>
    <table>
        <tr id="upcoming">
            <th>Date Purchased</th>
            <th>Event</th>
            <th>Event Date</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
        <?php if (empty($activeTickets)): ?>
            <tr><td colspan="6">No upcoming tickets</td></tr>
        <?php else: ?>
            <?php foreach ($activeTickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['date']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['quantity']); ?></td>
                    <td><?php echo $ticket['ticket_price'] * $ticket['quantity']; ?></td>
                    <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <h2>Expired Tickets</h2>
    <table>
        <tr id= "expire">
            <th>Date Purchased</th>
            <th>Event</th>
            <th>Event Date</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
        <?php if (empty($expiredTickets)): ?>
            <tr><td colspan="6">No expired tickets</td></tr>
        <?php else: ?>
            <?php foreach ($expiredTickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['date']); ?></td>
                    <td><?php echo htmlspecialchars($ticket['quantity']); ?></td>
                    <td><?php echo $ticket['ticket_price'] * $ticket['quantity']; ?></td>
                    <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    
</body>
</html>
