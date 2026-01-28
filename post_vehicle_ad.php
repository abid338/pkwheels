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
$page_title = PAGE_TITLES['post_ad'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $category = sanitize($conn, $_POST['category']);
    $city = sanitize($conn, $_POST['city']);
    $vehicle_condition = sanitize($conn, $_POST['vehicle_condition']);
    $vehicle_info = sanitize($conn, $_POST['vehicle_info']);
    $registered_in = sanitize($conn, $_POST['registered_in']);
    $mileage = intval($_POST['mileage']);
    $description = sanitize($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $mobile_number = sanitize($conn, $_POST['mobile_number']);
    $secondary_number = sanitize($conn, $_POST['secondary_number']);
    $whatsapp_enabled = isset($_POST['whatsapp_enabled']) ? 1 : 0;

    $color = isset($_POST['color']) ? sanitize($conn, $_POST['color']) : '';
    $engine_type = isset($_POST['engine_type']) ? sanitize($conn, $_POST['engine_type']) : '';

    $features = [];
    if ($category === 'car') {
        foreach (CAR_FEATURES as $key => $label) {
            if (isset($_POST[$key])) $features[] = $label;
        }
    } else if ($category === 'bike') {
        foreach (BIKE_FEATURES as $key => $label) {
            if (isset($_POST[$key])) $features[] = $label;
        }
    }
    $features_json = json_encode($features);

    if (empty($category) || empty($vehicle_info) || empty($description) || empty($vehicle_condition)) {
        $message = "Please fill all required fields!";
        $message_type = "danger";
    } elseif ($mileage < VALIDATION_RULES['mileage_min'] || $mileage > VALIDATION_RULES['mileage_max']) {
        $message = "Please enter valid mileage (" . VALIDATION_RULES['mileage_min'] . "-" . VALIDATION_RULES['mileage_max'] . " KM)!";
        $message_type = "danger";
    } elseif ($price < VALIDATION_RULES['price_min']) {
        $message = "Please enter a valid price!";
        $message_type = "danger";
    } elseif (!validatePhone($mobile_number)) {
        $message = "Please enter a valid 11-digit mobile number!";
        $message_type = "danger";
    } elseif (!empty($secondary_number) && !validatePhone($secondary_number)) {
        $message = "Secondary number must be 11 digits!";
        $message_type = "danger";
    } else {
        $upload_dir = ($category === 'car') ? UPLOAD_DIR_CARS : UPLOAD_DIR_BIKES;
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $images = [];
        $upload_errors = [];

        for ($i = 1; $i <= 5; $i++) {
            if (isset($_FILES["image_$i"]) && $_FILES["image_$i"]['error'] == 0) {
                $validation = validateImage($_FILES["image_$i"]);

                if (!$validation['valid']) {
                    $upload_errors[] = "Image $i: " . $validation['error'];
                    $images["image_$i"] = NULL;
                    continue;
                }

                $new_filename = generateUniqueFilename($_FILES["image_$i"]['name'], $i);
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES["image_$i"]['tmp_name'], $upload_path)) {
                    $images["image_$i"] = $new_filename;
                } else {
                    $upload_errors[] = "Failed to upload image $i";
                    $images["image_$i"] = NULL;
                }
            } else {
                $images["image_$i"] = NULL;
            }
        }

        if (!empty($upload_errors)) {
            $message = implode(', ', $upload_errors);
            $message_type = "warning";
        }

        if ($category === 'car') {
            $table = 'car_ads';
            $columns = 'user_id, city, car_info, registered_in, exterior_color, mileage, engine_type, vehicle_condition, description, price, features, mobile_number, secondary_number, whatsapp_enabled, image_1, image_2, image_3, image_4, image_5';

            $params = [
                $user_id,
                $city,
                $vehicle_info,
                $registered_in,
                $color,
                $mileage,
                $engine_type,
                $vehicle_condition,
                $description,
                $price,
                $features_json,
                $mobile_number,
                $secondary_number,
                $whatsapp_enabled,
                $images['image_1'],
                $images['image_2'],
                $images['image_3'],
                $images['image_4'],
                $images['image_5']
            ];
            $types = 'issssisssdsisssssss';
        } else {
            $table = 'bike_ads';
            $columns = 'user_id, city, bike_info, registered_in, color, mileage, engine_type, vehicle_condition, description, price, features, mobile_number, secondary_number, whatsapp_enabled, image_1, image_2, image_3, image_4, image_5';

            $params = [
                $user_id,
                $city,
                $vehicle_info,
                $registered_in,
                $color,
                $mileage,
                $engine_type,
                $vehicle_condition,
                $description,
                $price,
                $features_json,
                $mobile_number,
                $secondary_number,
                $whatsapp_enabled,
                $images['image_1'],
                $images['image_2'],
                $images['image_3'],
                $images['image_4'],
                $images['image_5']
            ];
            $types = 'issssisssdssisssss';
        }

        $placeholders = rtrim(str_repeat('?, ', count($params)), ', ');
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $message = ucfirst($category) . " ad posted successfully!";
            $message_type = "success";
        } else {
            $message = "Database error: " . $stmt->error;
            $message_type = "danger";
        }

        $stmt->close();
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
    <link rel="stylesheet" href="assets/css/post_vehicle_ad.css">
    <title><?php echo $page_title; ?></title>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-5 hero-section">
            <h1 class="display-5 fw-bold gradient-heading">Sell your Vehicle With 3 Easy & Simple Steps!</h1>
            <p class="text-muted fs-5">It's free and takes less than a minute</p>

            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="step-card">
                        <div class="step-icon step-1">
                            <i class="fas fa-car fa-3x"></i>
                        </div>
                        <h5 class="mt-3">Enter Your Vehicle Information</h5>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="step-card">
                        <div class="step-icon step-2">
                            <i class="fas fa-images fa-3x"></i>
                        </div>
                        <h5 class="mt-3">Upload Photos</h5>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="step-card">
                        <div class="step-icon step-3">
                            <i class="fas fa-tag fa-3x"></i>
                        </div>
                        <h5 class="mt-3">Enter Your Selling Price</h5>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($message): displayAlert($message, $message_type);
        endif; ?>

        <form method="POST" enctype="multipart/form-data" id="vehicleForm">
            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-primary">
                    <h4 class="mb-0"><i class="fas fa-car me-2"></i>Vehicle Information</h4>
                    <small>(All fields marked with * are mandatory)</small>
                </div>
                <div class="card-body p-4">
                    <div class="alert custom-info-alert">
                        <i class="fas fa-lightbulb me-2"></i>We don't allow duplicates of same ad.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold gradient-label">Category *</label>
                        <select name="category" id="category" class="form-select form-select-lg custom-select" required onchange="toggleFields()">
                            <option value="">Select Category</option>
                            <option value="car">Car</option>
                            <option value="bike">Bike</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">City *</label>
                            <select name="city" class="form-select form-select-lg custom-select" required>
                                <option value="">Select City</option>
                                <?php foreach (CITIES as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Condition *</label>
                            <select name="vehicle_condition" class="form-select form-select-lg custom-select" required>
                                <option value="">Select Condition</option>
                                <?php foreach (VEHICLE_CONDITIONS as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Is this vehicle brand new or used?</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold gradient-label"><span id="vehicle-label">Vehicle</span> Information *</label>
                        <input type="text" name="vehicle_info" class="form-control form-control-lg custom-input"
                            placeholder="e.g. Honda CD 70 2020 or Toyota Corolla 2018" required maxlength="200">
                        <small class="text-muted">Make/Model/Year</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Registered In *</label>
                            <select name="registered_in" class="form-select form-select-lg custom-select" required>
                                <?php foreach (REGISTERED_IN_OPTIONS as $reg): ?>
                                    <option value="<?php echo htmlspecialchars($reg); ?>"><?php echo htmlspecialchars($reg); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Color *</label>
                            <select name="color" id="color" class="form-select form-select-lg custom-select" required>
                                <option value="">Select Color</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Mileage * (km)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text gradient-input-icon">KM</span>
                                <input type="number" name="mileage" class="form-control custom-input"
                                    placeholder="Enter mileage" required
                                    min="<?php echo VALIDATION_RULES['mileage_min']; ?>"
                                    max="<?php echo VALIDATION_RULES['mileage_max']; ?>">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Engine Type *</label>
                            <select name="engine_type" id="engine_type" class="form-select form-select-lg custom-select" required>
                                <option value="">Select Engine Type</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold gradient-label">Price * (Rs.)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text gradient-input-icon">PKR</span>
                            <input type="number" name="price" class="form-control custom-input"
                                placeholder="Enter price" required min="<?php echo VALIDATION_RULES['price_min']; ?>">
                        </div>
                        <div class="alert custom-warning-alert mt-2 mb-0">
                            <small><i class="fas fa-lightbulb me-1"></i>Please enter a realistic price to get more genuine responses.</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold gradient-label">Describe Your <span id="desc-label">Vehicle</span> *</label>
                        <textarea name="description" class="form-control custom-textarea" rows="5"
                            placeholder="Example: first owner, genuine parts, maintained by authorized workshop, excellent condition etc."
                            required maxlength="1000"></textarea>
                        <div class="text-end">
                            <small class="text-muted">Remaining Characters: <span id="charCount" class="gradient-text-primary">1000</span></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card custom-card shadow-lg mb-4" id="featuresSection" style="display: none;">
                <div class="card-header gradient-header-info">
                    <h4 class="mb-0"><i class="fas fa-list-check me-2"></i><span id="features-title">Vehicle</span> Features</h4>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-bold gradient-label mb-3">Select Features</label>
                    <div class="row" id="featuresContainer">
                        <!-- Features will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-success">
                    <h4 class="mb-0"><i class="fas fa-images me-2"></i>Upload Photos</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">Add up to 5 photos (<?php echo implode(', ', array_map('strtoupper', IMAGE_ALLOWED_EXTENSIONS)); ?> - Max <?php echo IMAGE_MAX_SIZE_MB; ?>MB each)</p>
                    <div class="row">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="col-md-4 mb-3">
                                <label class="form-label gradient-label">Photo <?php echo $i; ?></label>
                                <input type="file" name="image_<?php echo $i; ?>" class="form-control custom-file-input image-input"
                                    accept="image/<?php echo implode(',image/', IMAGE_ALLOWED_EXTENSIONS); ?>"
                                    data-max-size="<?php echo IMAGE_MAX_SIZE; ?>">
                                <small class="text-muted" id="size-info-<?php echo $i; ?>"></small>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-danger">
                    <h4 class="mb-0"><i class="fas fa-phone me-2"></i>Contact Information</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Mobile Number *</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text gradient-input-icon"><i class="fas fa-mobile-alt"></i></span>
                                <input type="text" name="mobile_number" class="form-control custom-input"
                                    placeholder="<?php echo PHONE_PLACEHOLDER; ?>" required
                                    maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>"
                                    pattern="<?php echo PHONE_PATTERN; ?>">
                            </div>
                            <small class="text-muted">Enter a genuine 11 digit mobile no. with format <?php echo PHONE_PLACEHOLDER; ?>. All inquires will come on this number.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Secondary Number (Optional)</label>
                            <input type="text" name="secondary_number" class="form-control form-control-lg custom-input"
                                placeholder="<?php echo PHONE_PLACEHOLDER; ?>"
                                maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>"
                                pattern="<?php echo PHONE_PATTERN; ?>">
                        </div>
                    </div>

                    <div class="custom-switch-container">
                        <input class="form-check-input custom-switch" type="checkbox" name="whatsapp_enabled" id="whatsapp" checked>
                        <label class="form-check-label" for="whatsapp">
                            <i class="fab fa-whatsapp"></i> Allow WhatsApp Contact
                        </label>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-gradient-submit btn-lg px-5">
                    <i class="fas fa-paper-plane me-2"></i>SUBMIT & CONTINUE
                </button>
            </div>
        </form>
    </div>
    <script>
        // Pass PHP data to JavaScript
        window.carEngines = <?php echo json_encode(CAR_ENGINE_TYPES); ?>;
        window.bikeEngines = <?php echo json_encode(BIKE_ENGINE_TYPES); ?>;
        window.carColors = <?php echo json_encode(CAR_COLORS); ?>;
        window.bikeColors = <?php echo json_encode(BIKE_COLORS); ?>;
        window.carFeatures = <?php echo json_encode(CAR_FEATURES); ?>;
        window.bikeFeatures = <?php echo json_encode(BIKE_FEATURES); ?>;
        window.imageMaxSize = <?php echo IMAGE_MAX_SIZE; ?>;
        window.imageMaxSizeMB = <?php echo IMAGE_MAX_SIZE_MB; ?>;
    </script>
    <script src="assets/js/post_vehicle_ad.js"></script>

    <?php include "includes/footer.php"; ?>
</body>

</html>