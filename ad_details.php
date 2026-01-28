<?php
session_start();
include "config/db.php";
include "config/constants.php";

$base_path = calculateBasePath();
$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ad_type = isset($_GET['type']) ? $_GET['type'] : '';

if (!$ad_id || !in_array($ad_type, ['car', 'bike'])) {
    header("Location: index.php");
    exit;
}

if ($ad_type == 'car') {
    $sql = "SELECT c.*, u.name as seller_name, u.phone as seller_phone, u.email as seller_email 
            FROM car_ads c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = $ad_id
            LIMIT 1";
} else {
    $sql = "SELECT b.*, u.name as seller_name, u.phone as seller_phone, u.email as seller_email 
            FROM bike_ads b
            JOIN users u ON b.user_id = u.id
            WHERE b.id = $ad_id
            LIMIT 1";
}

$result = mysqli_query($conn, $sql);
$ad = mysqli_fetch_assoc($result);

if (!$ad) {
    header("Location: index.php");
    exit;
}

$page_title = ($ad_type == 'car' ? $ad['car_info'] : $ad['bike_info']) . " - PakWheels";

$images = [];
for ($i = 1; $i <= 5; $i++) {
    $img_field = "image_$i";
    if (!empty($ad[$img_field])) {
        $images[] = getUploadDir($ad_type) . $ad[$img_field];
    }
}

$related_sql = "SELECT *, '$ad_type' as type FROM " . ($ad_type == 'car' ? 'car_ads' : 'bike_ads') . " 
                WHERE city = '{$ad['city']}' AND id != $ad_id
                ORDER BY created_at DESC LIMIT 4";
$related_result = mysqli_query($conn, $related_sql);

include "includes/header.php";
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/ad_details.css">
    <title><?php echo $page_title; ?></title>
</head>

<body>
    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>search.php">Search</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($ad_type == 'car' ? $ad['car_info'] : $ad['bike_info']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <div class="header-section">
                    <div>
                        <h2 class="ad-title"><?php echo htmlspecialchars($ad_type == 'car' ? $ad['car_info'] : $ad['bike_info']); ?></h2>
                        <?php if (isset($ad['vehicle_condition'])): ?>
                            <span class="condition-badge condition-<?php echo $ad['vehicle_condition']; ?>">
                                <i class="fas fa-<?php echo $ad['vehicle_condition'] == 'new' ? 'star' : 'history'; ?>"></i>
                                <?php echo strtoupper($ad['vehicle_condition']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (isLoggedIn() && $_SESSION['user_id'] == $ad['user_id']): ?>
                        <a href="<?php echo $base_path; ?>edit_vehicle_ad.php?id=<?php echo $ad_id; ?>&type=<?php echo $ad_type; ?>" class="edit-btn">
                            <i class="fas fa-edit"></i>
                            <span>Edit Ad</span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 0): ?>
                    <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($images as $i => $img): ?>
                                <div class="carousel-item <?php if ($i == 0) echo 'active'; ?>">
                                    <img src="<?php echo htmlspecialchars($img); ?>" class="d-block w-100">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                        <div class="thumbnail-grid">
                            <?php foreach ($images as $i => $img): ?>
                                <img src="<?php echo htmlspecialchars($img); ?>"
                                    class="thumbnail-img <?php if ($i == 0) echo 'active-thumb'; ?>"
                                    data-bs-target="#carouselImages"
                                    data-bs-slide-to="<?php echo $i; ?>"
                                    onclick="changeSlide(<?php echo $i; ?>)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <script>
                        function changeSlide(index) {
                            document.querySelectorAll('.thumbnail-img').forEach(img => {
                                img.classList.remove('active-thumb');
                            });
                            event.target.classList.add('active-thumb');
                        }

                        document.getElementById('carouselImages')?.addEventListener('slide.bs.carousel', function(e) {
                            document.querySelectorAll('.thumbnail-img').forEach((img, idx) => {
                                if (idx === e.to) {
                                    img.classList.add('active-thumb');
                                } else {
                                    img.classList.remove('active-thumb');
                                }
                            });
                        });
                    </script>
                <?php endif; ?>

                <div class="info-card">
                    <div class="price-tag"><?php echo formatPrice($ad['price']); ?></div>

                    <h5 class="section-title">Description</h5>
                    <p class="description-text"><?php echo nl2br(htmlspecialchars($ad['description'])); ?></p>

                    <h5 class="section-title mt-4">Specifications</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="spec-list">
                                <li class="spec-item">
                                    <span class="spec-label"><?php echo $ad_type == 'car' ? 'Car Info:' : 'Bike Info:'; ?></span>
                                    <span class="spec-value"><?php echo htmlspecialchars($ad_type == 'car' ? $ad['car_info'] : $ad['bike_info']); ?></span>
                                </li>
                                <?php if (isset($ad['vehicle_condition'])): ?>
                                    <li class="spec-item">
                                        <span class="spec-label">Condition:</span>
                                        <span class="condition-badge condition-<?php echo $ad['vehicle_condition']; ?>" style="padding: 4px 12px; font-size: 12px;">
                                            <?php echo strtoupper($ad['vehicle_condition']); ?>
                                        </span>
                                    </li>
                                <?php endif; ?>
                                <li class="spec-item">
                                    <span class="spec-label">Registered In:</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($ad['registered_in']); ?></span>
                                </li>
                                <?php if ($ad_type == 'car'): ?>
                                    <li class="spec-item">
                                        <span class="spec-label">Exterior Color:</span>
                                        <span class="spec-value"><?php echo htmlspecialchars($ad['exterior_color']); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($ad_type == 'bike'): ?>
                                    <li class="spec-item">
                                        <span class="spec-label">Engine Type:</span>
                                        <span class="spec-value"><?php echo htmlspecialchars($ad['engine_type']); ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="spec-list">
                                <li class="spec-item">
                                    <span class="spec-label">Mileage:</span>
                                    <span class="spec-value"><?php echo number_format($ad['mileage']); ?> KM</span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">City:</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($ad['city']); ?></span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">Posted:</span>
                                    <span class="spec-value"><?php echo date('M d, Y', strtotime($ad['created_at'])); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php if (!empty($ad['features'])): ?>
                        <div class="mt-4">
                            <h5 class="section-title">Features</h5>
                            <div class="features-box">
                                <?php
                                $features_array = json_decode($ad['features'], true);
                                if (!empty($features_array)) {
                                    echo '<ul class="features-list">';
                                    foreach ($features_array as $feature) {
                                        echo '<li><i class="fas fa-check-circle text-success me-2"></i>' . htmlspecialchars($feature) . '</li>';
                                    }
                                    echo '</ul>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="seller-card">
                    <h5 class="seller-title">Contact Seller</h5>

                    <div class="seller-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>

                    <div class="seller-name"><?php echo htmlspecialchars($ad['seller_name']); ?></div>

                    <div class="seller-info">
                        <i class="fas fa-phone"></i>
                        <strong>Phone:</strong> <?php echo htmlspecialchars($ad['mobile_number']); ?>
                    </div>

                    <?php if (!empty($ad['secondary_number'])): ?>
                        <div class="seller-info">
                            <i class="fas fa-phone-alt"></i>
                            <strong>Secondary:</strong> <?php echo htmlspecialchars($ad['secondary_number']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($ad['whatsapp_enabled']): ?>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $ad['mobile_number']); ?>?text=<?php echo urlencode("Hello! I'm interested in " . ($ad_type == 'car' ? $ad['car_info'] : $ad['bike_info']) . " (" . formatPrice($ad['price']) . ")"); ?>"
                            class="contact-btn whatsapp-btn" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                    <?php endif; ?>

                    <a href="tel:<?php echo htmlspecialchars($ad['mobile_number']); ?>" class="contact-btn call-btn">
                        <i class="fas fa-phone"></i>
                        <span>Call Now</span>
                    </a>

                    <?php if (isset($ad['status']) && $ad['status'] == 'sold'): ?>
                        <div class="sold-badge">
                            <i class="fas fa-check-circle"></i> <strong>SOLD</strong>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (mysqli_num_rows($related_result) > 0): ?>
            <div class="related-section">
                <h4 class="related-title">Related <?php echo ucfirst($ad_type); ?>s in <?php echo htmlspecialchars($ad['city']); ?></h4>
                <div class="row g-4">
                    <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="related-card">
                                <?php
                                $rel_img = !empty($related['image_1']) ? getUploadDir($ad_type) . $related['image_1'] : '';
                                ?>
                                <?php if ($rel_img && file_exists($rel_img)): ?>
                                    <img src="<?php echo htmlspecialchars($rel_img); ?>" alt="Vehicle">
                                <?php else: ?>
                                    <div class="related-placeholder">
                                        <i class="fas fa-<?php echo $ad_type == 'car' ? 'car' : 'motorcycle'; ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="related-card-body">
                                    <h6 class="related-title-text"><?php echo htmlspecialchars($ad_type == 'car' ? $related['car_info'] : $related['bike_info']); ?></h6>
                                    <p class="related-price"><?php echo formatPrice($related['price']); ?></p>
                                    <p class="related-meta">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($related['city']); ?>
                                    </p>
                                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $related['id']; ?>&type=<?php echo $ad_type; ?>"
                                        class="view-details-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>