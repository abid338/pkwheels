<?php
session_start();
include "config/db.php";
include "config/constants.php";

requireLogin("auth/login.php");

$user_id = $_SESSION['user_id'];
$base_path = "./";
$css_path = "./";
$page_title = PAGE_TITLES['profile'];

$sql = "SELECT * FROM users WHERE id='$user_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);
} else {
    session_destroy();
    header("Location: auth/login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['update_profile'])) {
    $name = sanitize($conn, $_POST['name']);
    $phone = sanitize($conn, $_POST['phone']);
    
    if(strlen($name) < VALIDATION_RULES['name_min_length']) {
        $error_msg = "Name must be at least " . VALIDATION_RULES['name_min_length'] . " characters!";
    } elseif(!validatePhone($phone)) {
        $error_msg = "Phone number must be exactly " . VALIDATION_RULES['phone_exact_length'] . " digits!";
    } else {
        $update_sql = "UPDATE users SET name='$name', phone='$phone' WHERE id='$user_id'";
        
        if(mysqli_query($conn, $update_sql)) {
            $_SESSION['user_name'] = $name;
            $success_msg = "Profile updated successfully!";
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' LIMIT 1");
            $user = mysqli_fetch_assoc($result);
        } else {
            $error_msg = "Failed to update profile!";
        }
    }
}

include "includes/header.php";
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/profile.css">
    <title><?php echo $page_title; ?></title>
</head>
<body>
<div class="profile-container">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                
                <div class="card profile-header-card mb-4">
                    <div class="card-body text-center p-5">
                        <div class="profile-avatar mb-4">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2 class="profile-name mb-3"><?php echo htmlspecialchars($user['name']); ?></h2>
                        <div class="mb-3">
                            <div class="profile-info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="profile-info-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            </div>
                        </div>
                        <span class="member-badge">
                            <i class="fas fa-star me-1"></i>
                            Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                        </span>
                    </div>
                </div>

                <?php if(isset($success_msg)): ?>
                    <div class="custom-alert-success mb-4">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error_msg)): ?>
                    <div class="custom-alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card edit-card mb-4">
                    <div class="card-header edit-card-header">
                        <h5>
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label form-label-gradient">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg custom-form-input" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required 
                                       minlength="<?php echo VALIDATION_RULES['name_min_length']; ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-label-gradient">Email Address</label>
                                <input type="email" class="form-control form-control-lg custom-form-input" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <div class="mt-2">
                                    <span class="locked-info">
                                        <i class="fas fa-lock"></i>
                                        <span>Email cannot be changed</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label form-label-gradient">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-lg custom-form-input" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>" required 
                                       minlength="<?php echo VALIDATION_RULES['phone_min_length']; ?>"
                                       maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>"
                                       pattern="<?php echo PHONE_PATTERN; ?>"
                                       placeholder="<?php echo PHONE_PLACEHOLDER; ?>">
                            </div>
                            
                            <button type="submit" name="update_profile" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card account-info-card">
                    <div class="card-body p-4">
                        <h6 class="account-info-title mb-4">Account Information</h6>
                        <ul class="account-info-list">
                            <li class="account-info-item">
                                <i class="fas fa-id-badge"></i>
                                <strong>User ID:</strong> #<?php echo $user['id']; ?>
                            </li>
                            <li class="account-info-item">
                                <i class="fas fa-calendar"></i>
                                <strong>Joined:</strong> <?php echo date('d M Y, h:i A', strtotime($user['created_at'])); ?>
                            </li>
                            <li class="account-info-item">
                                <i class="fas fa-shield-alt"></i>
                                <strong>Security:</strong> Your data is private and secure
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>