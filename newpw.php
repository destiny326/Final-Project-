<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";

// Define variables
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
$valid_token = false;

// Check if token is provided
if (isset($_GET["token"]) && !empty(trim($_GET["token"]))) {
    $token = trim($_GET["token"]);
    
    // Verify token
    $sql = "SELECT id FROM Users WHERE reset_token = ? AND reset_expiry > NOW()";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) == 1) {
                $valid_token = true;
            } else {
                echo "Invalid or expired reset token.";
                exit;
            }
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "No reset token provided.";
    exit;
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid_token) {
    
    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Update password
    if (empty($new_password_err) && empty($confirm_password_err)) {
        $sql = "UPDATE Users SET user_password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $token);
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php?reset=success");
                exit;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Set New Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .wrapper { width: 350px; padding: 20px; background: white; margin: 50px auto; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 3px; }
        .btn { background: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 3px; cursor: pointer; width: 100%; }
        .btn:hover { background: #218838; }
        .invalid-feedback { color: #dc3545; font-size: 14px; }
        .is-invalid { border-color: #dc3545; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Set New Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?token=" . urlencode($token); ?>" method="post">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="<?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($new_password); ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="<?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Change Password">
            </div>
        </form>
    </div>
</body>
</html>