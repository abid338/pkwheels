<?php
session_start();
include "config/db.php";
include "config/constants.php";

requireLogin("auth/login.php");

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

$base_path = calculateBasePath();
$css_path = $base_path;
$page_title = PAGE_TITLES['my_ads'];

if (isset($_GET['delete']) && isset($_GET['type'])) {
    $ad_id = intval($_GET['delete']);
    $ad_type = $_GET['type'];
    
    if ($ad_type == 'car') {
        $img_query = mysqli_query($conn, "SELECT image_1, image_2, image_3, image_4, image_5 FROM car_ads WHERE id = $ad_id AND user_id = $user_id");
        if ($img_data = mysqli_fetch_assoc($img_query)) {
            for ($i = 1; $i <= 5; $i++) {
                $img_field = "image_$i";
                if (!empty($img_data[$img_field])) {
                    $img_path = UPLOAD_DIR_CARS . $img_data[$img_field];
                    if (file_exists($img_path)) {
                        unlink($img_path);
                    }
                }
            }
        }
        $delete_sql = "DELETE FROM car_ads WHERE id = $ad_id AND user_id = $user_id";
        if (mysqli_query($conn, $delete_sql)) {
            $message = "Car ad deleted successfully!";
            $message_type = "success";
        }
    } elseif ($ad_type == 'bike') {
        $img_query = mysqli_query($conn, "SELECT image_1, image_2, image_3, image_4, image_5 FROM bike_ads WHERE id = $ad_id AND user_id = $user_id");
        if ($img_data = mysqli_fetch_assoc($img_query)) {
            for ($i = 1; $i <= 5; $i++) {
                $img_field = "image_$i";
                if (!empty($img_data[$img_field])) {
                    $img_path = UPLOAD_DIR_BIKES . $img_data[$img_field];
                    if (file_exists($img_path)) {
                        unlink($img_path);
                    }
                }
            }
        }
        $delete_sql = "DELETE FROM bike_ads WHERE id = $ad_id AND user_id = $user_id";
        if (mysqli_query($conn, $delete_sql)) {
            $message = "Bike ad deleted successfully!";
            $message_type = "success";
        }
    }
}

$car_ads_query = "SELECT *, 'car' as type FROM car_ads WHERE user_id = $user_id ORDER BY created_at DESC";
$car_ads_result = mysqli_query($conn, $car_ads_query);

$bike_ads_query = "SELECT *, 'bike' as type FROM bike_ads WHERE user_id = $user_id ORDER BY created_at DESC";
$bike_ads_result = mysqli_query($conn, $bike_ads_query);

include "includes/header.php";
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/my_ads.css">
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold gradient-text"><i class="fas fa-list"></i> My Ads</h2>
            <div>
                <a href="<?php echo $base_path; ?>post_vehicle_ad.php" class="btn btn-gradient-primary">
                    <i class="fas fa-plus me-1"></i>Post Car Ad
                </a>
                <a href="<?php echo $base_path; ?>post_vehicle_ad.php" class="btn btn-gradient-success">
                    <i class="fas fa-plus me-1"></i>Post Bike Ad
                </a>
            </div>
        </div>

        <?php if ($message): displayAlert($message, $message_type); endif; ?>

        <!-- Car Ads Section -->
        <div class="mb-5">
            <h4 class="mb-3"><i class="fas fa-car" style="color: #667eea;"></i> My Car Ads (<?php echo mysqli_num_rows($car_ads_result); ?>)</h4>

            <?php if (mysqli_num_rows($car_ads_result) > 0): ?>
                <div class="row g-4">
                    <?php while ($ad = mysqli_fetch_assoc($car_ads_result)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-gradient shadow-sm h-100">
                                <?php if (!empty($ad['image_1'])): ?>
                                    <img src="<?php echo UPLOAD_DIR_CARS . htmlspecialchars($ad['image_1']); ?>"
                                        class="card-img-top"
                                        style="height:200px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-gradient-placeholder d-flex align-items-center justify-content-center" style="height:200px;">
                                        <i class="fas fa-car fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($ad['car_info']); ?></h5>
                                    <p class="price-tag fw-bold fs-5 mb-2"><?php echo formatPrice($ad['price']); ?></p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ad['city']); ?> |
                                        <i class="fas fa-tachometer-alt"></i> <?php echo number_format($ad['mileage']); ?> KM
                                    </p>
                                    <p class="text-muted small">
                                        <i class="fas fa-clock"></i> Posted: <?php echo date('M d, Y', strtotime($ad['created_at'])); ?>
                                    </p>

                                    <div class="d-flex gap-2">
                                        <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad['id']; ?>&type=car"
                                            class="btn btn-sm btn-gradient-view flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo $base_path; ?>edit_vehicle_ad.php?id=<?php echo $ad['id']; ?>&type=car"
                                            class="btn btn-sm btn-gradient-edit flex-fill">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $ad['id']; ?>&type=car"
                                            class="btn btn-sm btn-gradient-delete flex-fill"
                                            onclick="return confirm('Are you sure you want to delete this ad?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-gradient-info">
                    <i class="fas fa-info-circle"></i> You haven't posted any car ads yet.
                    <a href="<?php echo $base_path; ?>post_vehicle_ad.php" class="alert-link-gradient">Post your first car ad now!</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Bike Ads Section -->
        <div class="mb-5">
            <h4 class="mb-3"><i class="fas fa-motorcycle" style="color: #764ba2;"></i> My Bike Ads (<?php echo mysqli_num_rows($bike_ads_result); ?>)</h4>

            <?php if (mysqli_num_rows($bike_ads_result) > 0): ?>
                <div class="row g-4">
                    <?php while ($ad = mysqli_fetch_assoc($bike_ads_result)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-gradient shadow-sm h-100">
                                <?php if (!empty($ad['image_1'])): ?>
                                    <img src="<?php echo UPLOAD_DIR_BIKES . htmlspecialchars($ad['image_1']); ?>"
                                        class="card-img-top"
                                        style="height:200px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-gradient-placeholder d-flex align-items-center justify-content-center" style="height:200px;">
                                        <i class="fas fa-motorcycle fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>

                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($ad['bike_info']); ?></h5>
                                    <p class="price-tag fw-bold fs-5 mb-2"><?php echo formatPrice($ad['price']); ?></p>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ad['city']); ?> |
                                        <i class="fas fa-tachometer-alt"></i> <?php echo number_format($ad['mileage']); ?> KM
                                    </p>
                                    <p class="text-muted small">
                                        <i class="fas fa-clock"></i> Posted: <?php echo date('M d, Y', strtotime($ad['created_at'])); ?>
                                    </p>

                                    <div class="d-flex gap-2">
                                        <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad['id']; ?>&type=bike"
                                            class="btn btn-sm btn-gradient-view flex-fill">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo $base_path; ?>edit_vehicle_ad.php?id=<?php echo $ad['id']; ?>&type=bike"
                                            class="btn btn-sm btn-gradient-edit flex-fill">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="?delete=<?php echo $ad['id']; ?>&type=bike"
                                            class="btn btn-sm btn-gradient-delete flex-fill"
                                            onclick="return confirm('Are you sure you want to delete this ad?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-gradient-info">
                    <i class="fas fa-info-circle"></i> You haven't posted any bike ads yet.
                    <a href="<?php echo $base_path; ?>post_vehicle_ad.php" class="alert-link-gradient">Post your first bike ad now!</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <?php include "includes/footer.php"; ?>
</body>
</html>