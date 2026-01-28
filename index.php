<?php
session_start();
include "config/db.php";
include "config/constants.php";

$base_path = calculateBasePath();
$css_path = $base_path;
$page_title = PAGE_TITLES['home'];

$new_bikes_sql = "SELECT * FROM bike_ads WHERE vehicle_condition = 'new' ORDER BY created_at DESC LIMIT 4";
$new_bikes_result = mysqli_query($conn, $new_bikes_sql);

$used_bikes_sql = "SELECT * FROM bike_ads WHERE vehicle_condition = 'used' ORDER BY created_at DESC LIMIT 4";
$used_bikes_result = mysqli_query($conn, $used_bikes_sql);

$new_cars_sql = "SELECT * FROM car_ads WHERE vehicle_condition = 'new' ORDER BY created_at DESC LIMIT 4";
$new_cars_result = mysqli_query($conn, $new_cars_sql);

$used_cars_sql = "SELECT * FROM car_ads WHERE vehicle_condition = 'used' ORDER BY created_at DESC LIMIT 4";
$used_cars_result = mysqli_query($conn, $used_cars_sql);

$total_cars_sql = "SELECT COUNT(*) as count FROM car_ads";
$total_cars_result = mysqli_query($conn, $total_cars_sql);
$total_cars = mysqli_fetch_assoc($total_cars_result)['count'];

$total_bikes_sql = "SELECT COUNT(*) as count FROM bike_ads";
$total_bikes_result = mysqli_query($conn, $total_bikes_sql);
$total_bikes = mysqli_fetch_assoc($total_bikes_result)['count'];

$total_users_sql = "SELECT COUNT(*) as count FROM users";
$total_users_result = mysqli_query($conn, $total_users_sql);
$total_users = mysqli_fetch_assoc($total_users_result)['count'];

$total_deals_sql = "SELECT (SELECT COUNT(*) FROM car_ads) + (SELECT COUNT(*) FROM bike_ads) as total_deals";
$total_deals_result = mysqli_query($conn, $total_deals_sql);
$total_deals = mysqli_fetch_assoc($total_deals_result)['total_deals'];

include "includes/header.php";
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/index.css">
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center justify-content-center text-center">
                <div class="col-lg-10 hero-content">
                    <h1 class="hero-title">Find Your Perfect Ride</h1>
                    <p class="hero-subtitle">Pakistan's #1 Platform for Buying & Selling Vehicles</p>
                    <div class="hero-buttons d-flex gap-3 flex-wrap justify-content-center">
                        <a href="<?php echo $base_path; ?>search.php" class="btn btn-hero btn-hero-primary">
                            <i class="fas fa-search me-2"></i>Browse Vehicles
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="<?php echo $base_path; ?>post_vehicle_ad.php" class="btn btn-hero btn-hero-outline">
                                <i class="fas fa-plus-circle me-2"></i>Sell Your Vehicle
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-car"></i></div>
                        <div class="stat-number"><?php echo number_format($total_cars); ?>+</div>
                        <div class="stat-label">Cars Listed</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-motorcycle"></i></div>
                        <div class="stat-number"><?php echo number_format($total_bikes); ?>+</div>
                        <div class="stat-label">Bikes Available</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-number"><?php echo number_format($total_users); ?>+</div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-handshake"></i></div>
                        <div class="stat-number"><?php echo number_format($total_deals); ?>+</div>
                        <div class="stat-label">Total Listings</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <!-- New Bikes Section -->
        <div class="mb-5 new-bikes">
            <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-motorcycle me-3" style="color: #10b981;"></i>New Bikes
                    </h2>
                    <span class="section-badge bg-success text-white ms-3">
                        <i class="fas fa-star"></i> Latest Models
                    </span>
                </div>
                <a href="<?php echo $base_path; ?>search.php?type=new_bike" class="btn btn-view-all btn-outline-success">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($new_bikes_result) > 0): ?>
                    <?php while ($bike = mysqli_fetch_assoc($new_bikes_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="vehicle-card">
                                <div class="vehicle-card-img-wrapper">
                                    <?php
                                    $img_path = !empty($bike['image_1']) ? UPLOAD_DIR_BIKES . $bike['image_1'] : '';
                                    ?>
                                    <?php if ($img_path && file_exists($img_path)): ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Bike">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-motorcycle fa-4x text-success"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="vehicle-badge bg-success text-white">NEW</span>
                                </div>

                                <div class="vehicle-card-body">
                                    <h6 class="vehicle-title"><?php echo htmlspecialchars($bike['bike_info']); ?></h6>
                                    <div class="vehicle-price"><?php echo formatPrice($bike['price']); ?></div>
                                    <div class="vehicle-meta">
                                        <span><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($bike['city']); ?></span>
                                        <span><i class="fas fa-tachometer-alt me-1"></i><?php echo number_format($bike['mileage']); ?> KM</span>
                                    </div>
                                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $bike['id']; ?>&type=bike"
                                        class="btn btn-view-details btn-success">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4" style="border-radius: 15px;">
                            <i class="fas fa-info-circle me-2 fa-2x d-block mb-3"></i>
                            <h5>No new bikes available at the moment</h5>
                            <p class="mb-0">Check back later for exciting new listings!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Used Bikes Section -->
        <div class="mb-5 used-bikes">
            <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-motorcycle me-3" style="color: #3b82f6;"></i>Used Bikes
                    </h2>
                    <span class="section-badge bg-info text-white ms-3">
                        <i class="fas fa-certificate"></i> Certified
                    </span>
                </div>
                <a href="<?php echo $base_path; ?>search.php?type=used_bike" class="btn btn-view-all btn-outline-info">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($used_bikes_result) > 0): ?>
                    <?php while ($bike = mysqli_fetch_assoc($used_bikes_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="vehicle-card">
                                <div class="vehicle-card-img-wrapper">
                                    <?php
                                    $img_path = !empty($bike['image_1']) ? UPLOAD_DIR_BIKES . $bike['image_1'] : '';
                                    ?>
                                    <?php if ($img_path && file_exists($img_path)): ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Bike">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-motorcycle fa-4x text-info"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="vehicle-badge bg-info text-white">USED</span>
                                </div>

                                <div class="vehicle-card-body">
                                    <h6 class="vehicle-title"><?php echo htmlspecialchars($bike['bike_info']); ?></h6>
                                    <div class="vehicle-price"><?php echo formatPrice($bike['price']); ?></div>
                                    <div class="vehicle-meta">
                                        <span><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($bike['city']); ?></span>
                                        <span><i class="fas fa-tachometer-alt me-1"></i><?php echo number_format($bike['mileage']); ?> KM</span>
                                    </div>
                                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $bike['id']; ?>&type=bike"
                                        class="btn btn-view-details btn-info">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4" style="border-radius: 15px;">
                            <i class="fas fa-info-circle me-2 fa-2x d-block mb-3"></i>
                            <h5>No used bikes available at the moment</h5>
                            <p class="mb-0">Check back later for great deals!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- New Cars Section -->
        <div class="mb-5 new-cars">
            <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-car me-3" style="color: #8b5cf6;"></i>New Cars
                    </h2>
                    <span class="section-badge bg-primary text-white ms-3">
                        <i class="fas fa-star"></i> Brand New
                    </span>
                </div>
                <a href="<?php echo $base_path; ?>search.php?type=new_car" class="btn btn-view-all btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($new_cars_result) > 0): ?>
                    <?php while ($car = mysqli_fetch_assoc($new_cars_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="vehicle-card">
                                <div class="vehicle-card-img-wrapper">
                                    <?php
                                    $img_path = !empty($car['image_1']) ? UPLOAD_DIR_CARS . $car['image_1'] : '';
                                    ?>
                                    <?php if ($img_path && file_exists($img_path)): ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Car">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-car fa-4x text-primary"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="vehicle-badge bg-primary text-white">NEW</span>
                                </div>

                                <div class="vehicle-card-body">
                                    <h6 class="vehicle-title"><?php echo htmlspecialchars($car['car_info']); ?></h6>
                                    <div class="vehicle-price"><?php echo formatPrice($car['price']); ?></div>
                                    <div class="vehicle-meta">
                                        <span><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($car['city']); ?></span>
                                        <span><i class="fas fa-tachometer-alt me-1"></i><?php echo number_format($car['mileage']); ?> KM</span>
                                    </div>
                                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $car['id']; ?>&type=car"
                                        class="btn btn-view-details btn-primary">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4" style="border-radius: 15px;">
                            <i class="fas fa-info-circle me-2 fa-2x d-block mb-3"></i>
                            <h5>No new cars available at the moment</h5>
                            <p class="mb-0">Check back later for exciting new listings!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Used Cars Section -->
        <div class="mb-5 used-cars">
            <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="section-title">
                        <i class="fas fa-car me-3" style="color: #f59e0b;"></i>Used Cars
                    </h2>
                    <span class="section-badge bg-warning text-dark ms-3">
                        <i class="fas fa-check-circle"></i> Verified
                    </span>
                </div>
                <a href="<?php echo $base_path; ?>search.php?type=used_car" class="btn btn-view-all btn-outline-warning">
                    View All <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php if (mysqli_num_rows($used_cars_result) > 0): ?>
                    <?php while ($car = mysqli_fetch_assoc($used_cars_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="vehicle-card">
                                <div class="vehicle-card-img-wrapper">
                                    <?php
                                    $img_path = !empty($car['image_1']) ? UPLOAD_DIR_CARS . $car['image_1'] : '';
                                    ?>
                                    <?php if ($img_path && file_exists($img_path)): ?>
                                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Car">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-car fa-4x text-warning"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="vehicle-badge bg-warning text-dark">USED</span>
                                </div>

                                <div class="vehicle-card-body">
                                    <h6 class="vehicle-title"><?php echo htmlspecialchars($car['car_info']); ?></h6>
                                    <div class="vehicle-price"><?php echo formatPrice($car['price']); ?></div>
                                    <div class="vehicle-meta">
                                        <span><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($car['city']); ?></span>
                                        <span><i class="fas fa-tachometer-alt me-1"></i><?php echo number_format($car['mileage']); ?> KM</span>
                                    </div>
                                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $car['id']; ?>&type=car"
                                        class="btn btn-view-details btn-warning">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center py-4" style="border-radius: 15px;">
                            <i class="fas fa-info-circle me-2 fa-2x d-block mb-3"></i>
                            <h5>No used cars available at the moment</h5>
                            <p class="mb-0">Check back later for great deals!</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Featured Categories Section -->
    <div class="featured-categories">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-white display-5 fw-bold mb-3">Browse By Category</h2>
                <p class="text-white-50 fs-5">Find exactly what you're looking for</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo $base_path; ?>search.php?type=new_car" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-car"></i>
                            </div>
                            <h4 class="category-title">New Cars</h4>
                            <p class="category-count">Browse Latest Models</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo $base_path; ?>search.php?type=used_car" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-car-side"></i>
                            </div>
                            <h4 class="category-title">Used Cars</h4>
                            <p class="category-count">Best Deals Available</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo $base_path; ?>search.php?type=new_bike" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <h4 class="category-title">New Bikes</h4>
                            <p class="category-count">Latest Two Wheelers</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo $base_path; ?>search.php?type=used_bike" class="text-decoration-none">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-biking"></i>
                            </div>
                            <h4 class="category-title">Used Bikes</h4>
                            <p class="category-count">Affordable Options</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Why Choose Us Section -->
    <div class="why-choose-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Why Choose PakWheels?</h2>
                <p class="text-muted fs-5">Pakistan's Most Trusted Vehicle Marketplace</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="feature-title">100% Verified</h4>
                        <p class="feature-description">All listings are verified to ensure authenticity and quality</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h4 class="feature-title">Best Prices</h4>
                        <p class="feature-description">Competitive pricing with transparent deals and no hidden charges</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4 class="feature-title">24/7 Support</h4>
                        <p class="feature-description">Our team is always ready to help you with any queries</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="feature-title">Quick Process</h4>
                        <p class="feature-description">Fast and easy buying and selling process in just a few steps</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>