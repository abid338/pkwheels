<?php
session_start();
include "../config/db.php";
include "../config/constants.php";

$message = "";
$message_type = "";
$base_path = "../";
$css_path = "../";
$page_title = PAGE_TITLES['register'];

redirectIfLoggedIn("../index.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (strlen($name) < VALIDATION_RULES['name_min_length']) {
        $message = "Name must be at least " . VALIDATION_RULES['name_min_length'] . " characters!";
        $message_type = "danger";
    } elseif (!validateEmail($email)) {
        $message = "Invalid email format!";
        $message_type = "danger";
    } elseif (strlen($phone) < VALIDATION_RULES['phone_min_length']) {
        $message = "Phone number must be at least " . VALIDATION_RULES['phone_min_length'] . " digits!";
        $message_type = "danger";
    } elseif (!validatePhone($phone)) {
        $message = "Phone number must be exactly " . VALIDATION_RULES['phone_exact_length'] . " digits!";
        $message_type = "danger";
    } elseif ($pass !== $confirm_pass) {
        $message = "Passwords do not match!";
        $message_type = "danger";
    } elseif (strlen($pass) < VALIDATION_RULES['password_min_length']) {
        $message = "Password must be at least " . VALIDATION_RULES['password_min_length'] . " characters!";
        $message_type = "danger";
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");

        if (mysqli_num_rows($check_email) > 0) {
            $message = "This email is already registered! Please use a different email or login.";
            $message_type = "danger";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, phone, created_at) 
                    VALUES ('$name','$email','$hash','$phone', NOW())";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                header("Location: ../index.php");
                exit;
            } else {
                $message = "Registration failed! Please try again.";
                $message_type = "danger";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/register.css">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="icon-wrapper">
                <i class="fas fa-user-plus"></i>
            </div>

            <div class="register-title">
                <h3>Create Your Account</h3>
            </div>

            <p class="register-subtitle">Join PakWheels today!</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type == 'danger' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-input"
                        placeholder="Enter your full name" required
                        minlength="<?php echo VALIDATION_RULES['name_min_length']; ?>"
                        value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-input"
                        placeholder="Enter your email" required
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i> Each email can only be used once
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-input"
                        placeholder="<?php echo PHONE_PLACEHOLDER; ?>" required
                        minlength="<?php echo VALIDATION_RULES['phone_min_length']; ?>"
                        maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>"
                        pattern="<?php echo PHONE_PATTERN; ?>"
                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-input"
                        placeholder="Create a password" required
                        minlength="<?php echo VALIDATION_RULES['password_min_length']; ?>">
                    <small class="form-hint">Minimum <?php echo VALIDATION_RULES['password_min_length']; ?> characters</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-input"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i>
                    <span>Create Account</span>
                </button>
            </form>

            <div class="divider"></div>

            <p class="login-link-wrapper">
                Already have an account?
                <a href="login.php" class="login-link">Login here</a>
            </p>
        </div>
    </div>
</body>

</html>