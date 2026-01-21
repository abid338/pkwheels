<?php
session_start();
include "../config/db.php";
$vehicle_id = $_GET['id'] ?? 0;
$vehicle_id = intval($vehicle_id);
if (!$vehicle_id) {
    header("Location: ../index.php");
    exit;
}
$sql_vehicle = "SELECT v.*, u.name AS seller_name, u.phone AS seller_phone, u.email AS seller_email
                FROM vehicles v
                JOIN users u ON v.user_id = u.id
                WHERE v.id = $vehicle_id
                LIMIT 1";
$res_vehicle = mysqli_query($conn, $sql_vehicle);
$vehicle = mysqli_fetch_assoc($res_vehicle);

if (!$vehicle) {
    header("Location: ../index.php");
    exit;
}

$page_title = $vehicle['title'] . " - PakWheels";

$images_result = mysqli_query($conn, "SELECT image_path FROM vehicle_images WHERE vehicle_id=$vehicle_id");
$images = [];
while ($row = mysqli_fetch_assoc($images_result)) {
    $images[] = $row['image_path'];
}

$colors_result = mysqli_query($conn, "SELECT c.name FROM colors c 
                                      JOIN vehicle_colors vc ON c.id=vc.color_id
                                      WHERE vc.vehicle_id=$vehicle_id");
$colors = [];
while ($row = mysqli_fetch_assoc($colors_result)) {
    $colors[] = $row['name'];
}

$related_result = mysqli_query($conn, "SELECT v.*, vi.image_path 
                                       FROM vehicles v 
                                       LEFT JOIN vehicle_images vi ON v.id = vi.vehicle_id
                                       WHERE (v.brand='{$vehicle['brand']}' OR v.city='{$vehicle['city']}') 
                                       AND v.id != $vehicle_id
                                       GROUP BY v.id
                                       ORDER BY v.created_at DESC
                                       LIMIT 4");
$related = [];
while ($row = mysqli_fetch_assoc($related_result)) {
    $related[] = $row;
}

include "../includes/header.php";
include "../includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/details.css">
    <title>Document</title>
</head>

<body>



    <div class="container py-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="../search.php">Search</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($vehicle['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <h2 class="vehicle-title"><?php echo htmlspecialchars($vehicle['title']); ?></h2>

                <?php if (count($images) > 0): ?>
                    <div id="carouselImages" class="carousel slide mb-4" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($images as $i => $img): ?>
                                <button type="button" data-bs-target="#carouselImages" data-bs-slide-to="<?php echo $i; ?>"
                                    class="<?php if ($i == 0) echo 'active'; ?>"></button>
                            <?php endforeach; ?>
                        </div>

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
                <?php endif; ?>

                <div class="info-card">
                    <div class="price-tag">PKR <?php echo number_format($vehicle['price']); ?></div>

                    <h5 class="section-title">Description</h5>
                    <p class="description-text"><?php echo nl2br(htmlspecialchars($vehicle['description'])); ?></p>

                    <h5 class="section-title mt-4">Specifications</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="spec-list">
                                <li class="spec-item">
                                    <span class="spec-label">Brand:</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($vehicle['brand']); ?></span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">Model:</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($vehicle['model']); ?></span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">Year:</span>
                                    <span class="spec-value"><?php echo $vehicle['year']; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="spec-list">
                                <li class="spec-item">
                                    <span class="spec-label">City:</span>
                                    <span class="spec-value"><?php echo htmlspecialchars($vehicle['city']); ?></span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">Colors:</span>
                                    <span class="spec-value"><?php echo implode(", ", $colors); ?></span>
                                </li>
                                <li class="spec-item">
                                    <span class="spec-label">Posted:</span>
                                    <span class="spec-value"><?php echo date('M d, Y', strtotime($vehicle['created_at'])); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="seller-card">
                    <h5 class="seller-title">Contact Seller</h5>

                    <div class="seller-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>

                    <div class="seller-name"><?php echo htmlspecialchars($vehicle['seller_name']); ?></div>

                    <div class="seller-info">
                        <i class="fas fa-phone"></i>
                        <strong>Phone:</strong> <?php echo htmlspecialchars($vehicle['seller_phone']); ?>
                    </div>

                    <a href="https://wa.me/<?php echo htmlspecialchars($vehicle['seller_phone']); ?>?text=<?php echo urlencode("Hello! I'm interested in " . $vehicle['title'] . " (PKR " . number_format($vehicle['price']) . ")"); ?>"
                        class="contact-btn whatsapp-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>

                    <a href="tel:<?php echo htmlspecialchars($vehicle['seller_phone']); ?>" class="contact-btn call-btn">
                        <i class="fas fa-phone"></i>
                        <span>Call Now</span>
                    </a>
                </div>
            </div>
        </div>

        <?php if (count($related) > 0): ?>
            <div class="related-section">
                <h4 class="related-title">Related Vehicles</h4>
                <div class="row g-4">
                    <?php foreach ($related as $rv): ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="related-card">
                                <img src="<?php echo htmlspecialchars($rv['image_path']); ?>" alt="Vehicle">
                                <div class="related-card-body">
                                    <h6 class="related-title-text"><?php echo htmlspecialchars($rv['title']); ?></h6>
                                    <p class="related-price">PKR <?php echo number_format($rv['price']); ?></p>
                                    <p class="related-meta">
                                        <i class="fas fa-calendar"></i> <?php echo $rv['year']; ?> |
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($rv['city']); ?>
                                    </p>
                                    <a href="details.php?id=<?php echo $rv['id']; ?>" class="view-details-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>

</html>