<?php
// Initialize the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables
$email = "";
$email_err = $message = "";

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check if email exists
        $sql = "SELECT id, username FROM Users WHERE email = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Email exists, generate reset token
                    $reset_token = bin2hex(random_bytes(32));
                    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
                    
                    // Store token in database (add this column to your Users table)
                    $sql_update = "UPDATE Users SET reset_token = ?, reset_expiry = ? WHERE email = ?";
                    if ($stmt_update = mysqli_prepare($link, $sql_update)) {
                        mysqli_stmt_bind_param($stmt_update, "sss", $reset_token, $expiry, $email);
                        if (mysqli_stmt_execute($stmt_update)) {
                            // Send reset email (in a real application)
                            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/new-password.php?token=" . $reset_token;
                            $message = "Password reset link has been sent to your email. For demo: <a href='$reset_link'>Click here</a>";
                        }
                        mysqli_stmt_close($stmt_update);
                    }
                } else {
                    $email_err = "No account found with that email address.";
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .wrapper { width: 350px; padding: 20px; background: white; margin: 50px auto; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #ffc107; color: #212529; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; width: 100%; }
        .btn:hover { background: #e0a800; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 3px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .invalid-feedback { color: #dc3545; font-size: 14px; }
        .is-invalid { border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please enter your email address to receive a password reset link.</p>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="<?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Send Reset Link">
            </div>
            <p>Remember your password? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>