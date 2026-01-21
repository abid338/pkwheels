<?php
session_start();
include "../config/db.php";

$message = "";
$base_path = "../";
$css_path = "../";
$page_title = "Login - PakWheels";
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($pass, $user['password'])) {
            // Login successful 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: ../index.php");
            exit;
        } else {
            $message = "Incorrect password!";
        }
    } else {
        $message = "Email not registered!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/login.css">
    <title><?php echo $page_title; ?></title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="icon-wrapper">
                <i class="fas fa-user-circle"></i>
            </div>

            <div class="login-title">
                <h3>Login to PakWheels</h3>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email"
                        name="email"
                        class="form-input"
                        placeholder="Enter your email"
                        required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password"
                        name="password"
                        class="form-input"
                        placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </button>
            </form>

            <div class="divider"></div>

            <p class="register-link-wrapper">
                Don't have an account?
                <a href="register.php" class="register-link">Register here</a>
            </p>
        </div>
    </div>
</body>

</html>