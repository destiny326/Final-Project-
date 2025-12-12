<?php
session_start();
require_once "config.php"; // DB connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        // Insert into database (optional)
        $stmt = $conn->prepare("INSERT INTO ContactMessages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();
        $stmt->close();

        $success = "Thank you for contacting us! We'll get back to you soon.";
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>StagePass - Contact Us</title>
  <link rel="stylesheet" href="index.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #111; color: #fff; }
    header { background: linear-gradient(90deg, #ff0080, #7928ca); padding: 20px; text-align: center; }
    header h1 { margin: 0; }
    .container { max-width: 600px; margin: 40px auto; background: #222; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 10px; }
    input, textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: none; }
    button { margin-top: 15px; padding: 10px 15px; background: #ff0080; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
    button:hover { background: #7928ca; }
    .success { color: #0f0; margin-top: 10px; }
    .error { color: #f00; margin-top: 10px; }
  </style>
</head>
<body>
    <nav id="top-menu">
    <ul>
      <li><a href="tickets.php">My tickets</a></li>
      <li><a href="Events.php">Events</a></li> 
      <li><a href="bookings.php">Book Ticket</a></li>
      <li><a href="Cart.php">Cart</a></li>
      <li><a href="contact.php">Contact Us</a></li>
      <li><a href="account.php"> Account </a></li>
     <button onclick="window.location.href= 'login.php'"> LOGIN</button>
    </ul>
  </nav>

  <header>
    <h1>Contact Us</h1>
  </header>

  <div class="container">
    <p>Have questions or feedback? Send us a message below.</p>

    <?php if (!empty($success)): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php elseif (!empty($error)): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" action="contact.php">
      <label for="name">Your Name</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Your Email</label>
      <input type="email" id="email" name="email" required>

      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" required></textarea>

      <button type="submit">Send Message</button>
    </form>
  </div>
</body>
</html>
