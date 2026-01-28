<?php
session_start();
include "config/db.php";
include "config/constants.php";

requireLogin("auth/login.php");

$base_path = calculateBasePath();

$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? sanitize($conn, $_GET['type']) : '';
$user_id = $_SESSION['user_id'];

if (!in_array($type, ['car', 'bike'])) {
    header("Location: my_ads.php");
    exit;
}

$table = $type === 'car' ? 'car_ads' : 'bike_ads';
$info_column = $type === 'car' ? 'car_info' : 'bike_info';
$page_title = $type === 'car' ? PAGE_TITLES['edit_car'] : PAGE_TITLES['edit_bike'];

$sql = "SELECT * FROM $table WHERE id = $ad_id AND user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$ad = mysqli_fetch_assoc($result);

if (!$ad) {
    header("Location: my_ads.php");
    exit;
}

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_condition = sanitize($conn, $_POST['vehicle_condition']);
    $vehicle_info = sanitize($conn, $_POST['vehicle_info']);
    $registered_in = sanitize($conn, $_POST['registered_in']);
    $city = sanitize($conn, $_POST['city']);
    $color = sanitize($conn, $_POST['color']);
    $mileage = intval($_POST['mileage']);
    $engine_type = sanitize($conn, $_POST['engine_type']);
    $price = sanitize($conn, $_POST['price']);
    $description = sanitize($conn, $_POST['description']);
    $mobile_number = sanitize($conn, $_POST['mobile_number']);
    $secondary_number = sanitize($conn, $_POST['secondary_number']);
    $whatsapp_enabled = isset($_POST['whatsapp_enabled']) ? 1 : 0;

    $features = [];
    if ($type === 'car') {
        foreach (CAR_FEATURES as $key => $label) {
            if (isset($_POST[$key])) $features[] = $label;
        }
    } else if ($type === 'bike') {
        foreach (BIKE_FEATURES as $key => $label) {
            if (isset($_POST[$key])) $features[] = $label;
        }
    }
    $features_json = json_encode($features);

    $upload_dir = getUploadDir($type);
    $images = [];

    for ($i = 1; $i <= 5; $i++) {
        $img_field = "image_$i";

        if (isset($_FILES[$img_field]) && $_FILES[$img_field]['error'] == 0) {
            $validation = validateImage($_FILES[$img_field]);
            
            if ($validation['valid']) {
                $file_name = generateUniqueFilename($_FILES[$img_field]['name'], $i);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES[$img_field]['tmp_name'], $target_file)) {
                    if (!empty($ad[$img_field])) {
                        $old_img = $upload_dir . $ad[$img_field];
                        if (file_exists($old_img)) {
                            unlink($old_img);
                        }
                    }
                    $images[$img_field] = $file_name;
                } else {
                    $images[$img_field] = $ad[$img_field];
                }
            } else {
                $images[$img_field] = $ad[$img_field];
            }
        } else {
            $images[$img_field] = $ad[$img_field];
        }
    }

    if ($type === 'car') {
        $sql_update = "UPDATE $table SET 
                       vehicle_condition = '$vehicle_condition',
                       $info_column = '$vehicle_info',
                       registered_in = '$registered_in',
                       city = '$city',
                       exterior_color = '$color',
                       engine_type = '$engine_type',
                       mileage = '$mileage',
                       price = '$price',
                       description = '$description',
                       features = '$features_json',
                       mobile_number = '$mobile_number',
                       secondary_number = '$secondary_number',
                       whatsapp_enabled = '$whatsapp_enabled',
                       image_1 = '{$images['image_1']}',
                       image_2 = '{$images['image_2']}',
                       image_3 = '{$images['image_3']}',
                       image_4 = '{$images['image_4']}',
                       image_5 = '{$images['image_5']}'
                       WHERE id = $ad_id AND user_id = $user_id";
    } else {
        $sql_update = "UPDATE $table SET 
                       vehicle_condition = '$vehicle_condition',
                       $info_column = '$vehicle_info',
                       registered_in = '$registered_in',
                       city = '$city',
                       color = '$color',
                       engine_type = '$engine_type',
                       mileage = '$mileage',
                       price = '$price',
                       description = '$description',
                       features = '$features_json',
                       mobile_number = '$mobile_number',
                       secondary_number = '$secondary_number',
                       whatsapp_enabled = '$whatsapp_enabled',
                       image_1 = '{$images['image_1']}',
                       image_2 = '{$images['image_2']}',
                       image_3 = '{$images['image_3']}',
                       image_4 = '{$images['image_4']}',
                       image_5 = '{$images['image_5']}'
                       WHERE id = $ad_id AND user_id = $user_id";
    }

    if (mysqli_query($conn, $sql_update)) {
        header("Location: ad_details.php?id=$ad_id&type=$type");
        exit;
    } else {
        $message = "Failed to update " . ucfirst($type) . " ad!";
        $message_type = "danger";
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
    <link rel="stylesheet" href="assets/css/edit_vehicle_ad.css">
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <div class="container py-5">
        <div class="page-header">
            <h2 class="page-title">
                <i class="fas fa-edit"></i>
                <span>Edit <?php echo ucfirst($type); ?> Ad</span>
            </h2>
            <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad_id; ?>&type=<?php echo $type; ?>" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Ad</span>
            </a>
        </div>

        <?php if ($message): displayAlert($message, $message_type); endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" id="editVehicleForm">
                <h5 class="section-title">
                    <i class="fas fa-<?php echo $type === 'car' ? 'car' : 'motorcycle'; ?>"></i>
                    <span><?php echo ucfirst($type); ?> Information</span>
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo ucfirst($type); ?> Info <span class="text-danger">*</span></label>
                        <input type="text" name="vehicle_info" class="form-control"
                            placeholder="e.g., <?php echo $type === 'car' ? 'Honda Civic 2020' : 'Honda CD 70 2021'; ?>"
                            value="<?php echo htmlspecialchars($ad[$info_column]); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Condition <span class="text-danger">*</span></label>
                        <select name="vehicle_condition" class="form-select" required>
                            <option value="">Select Condition</option>
                            <?php foreach (VEHICLE_CONDITIONS as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php if ($ad['vehicle_condition'] == $key) echo 'selected'; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registered In <span class="text-danger">*</span></label>
                        <select name="registered_in" class="form-select" required>
                            <option value="">Select Registration</option>
                            <?php foreach (REGISTERED_IN_OPTIONS as $reg): ?>
                                <option value="<?php echo htmlspecialchars($reg); ?>" 
                                    <?php if ($ad['registered_in'] == $reg) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($reg); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <select name="city" class="form-select" required>
                            <option value="">Select City</option>
                            <?php foreach (CITIES as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>" 
                                    <?php if ($ad['city'] == $city) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($city); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color <span class="text-danger">*</span></label>
                        <select name="color" id="color" class="form-select" required>
                            <option value="">Select Color</option>
                            <?php 
                            $color_field = $type === 'car' ? 'exterior_color' : 'color';
                            $colors = $type === 'car' ? CAR_COLORS : BIKE_COLORS;
                            foreach ($colors as $color): 
                            ?>
                                <option value="<?php echo htmlspecialchars($color); ?>" 
                                    <?php if ($ad[$color_field] == $color) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($color); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Engine Type <span class="text-danger">*</span></label>
                        <select name="engine_type" id="engine_type" class="form-select" required>
                            <option value="">Select Engine Type</option>
                            <?php 
                            $engines = $type === 'car' ? CAR_ENGINE_TYPES : BIKE_ENGINE_TYPES;
                            foreach ($engines as $engine): 
                            ?>
                                <option value="<?php echo htmlspecialchars($engine); ?>" 
                                    <?php if ($ad['engine_type'] == $engine) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($engine); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mileage (KM) <span class="text-danger">*</span></label>
                        <input type="number" name="mileage" class="form-control"
                            placeholder="e.g., <?php echo $type === 'car' ? '50000' : '15000'; ?>"
                            value="<?php echo $ad['mileage']; ?>" required
                            min="<?php echo VALIDATION_RULES['mileage_min']; ?>"
                            max="<?php echo VALIDATION_RULES['mileage_max']; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price (PKR) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control"
                            placeholder="e.g., <?php echo $type === 'car' ? '2500000' : '85000'; ?>"
                            value="<?php echo $ad['price']; ?>" required
                            min="<?php echo VALIDATION_RULES['price_min']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4"
                        placeholder="Describe your <?php echo $type; ?> in detail..." required
                        maxlength="<?php echo $type === 'car' ? VALIDATION_RULES['description_max_car'] : VALIDATION_RULES['description_max_bike']; ?>"><?php echo htmlspecialchars($ad['description']); ?></textarea>
                </div>

                <?php 
                $existing_features = !empty($ad['features']) ? json_decode($ad['features'], true) : [];
                ?>

                <?php if ($type === 'car'): ?>
                    <div class="divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-list-check"></i>
                        <span>Car Features</span>
                    </h5>
                    
                    <div class="row" id="featuresContainer">
                        <?php 
                        foreach (CAR_FEATURES as $key => $label): 
                        $is_checked = in_array($label, $existing_features);
                        ?>
                            <div class="col-md-3 mb-3">
                                <div class="custom-checkbox">
                                    <input class="form-check-input" type="checkbox" 
                                        name="<?php echo $key; ?>" id="<?php echo $key; ?>"
                                        <?php if ($is_checked) echo 'checked'; ?>>
                                    <label class="form-check-label" for="<?php echo $key; ?>">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($type === 'bike'): ?>
                    <div class="divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-list-check"></i>
                        <span>Bike Features</span>
                    </h5>
                    
                    <div class="row" id="featuresContainer">
                        <?php 
                        foreach (BIKE_FEATURES as $key => $label): 
                        $is_checked = in_array($label, $existing_features);
                        ?>
                            <div class="col-md-4 mb-3">
                                <div class="custom-checkbox">
                                    <input class="form-check-input" type="checkbox" 
                                        name="<?php echo $key; ?>" id="<?php echo $key; ?>"
                                        <?php if ($is_checked) echo 'checked'; ?>>
                                    <label class="form-check-label" for="<?php echo $key; ?>">
                                        <?php echo $label; ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="divider"></div>

                <h5 class="section-title">
                    <i class="fas fa-phone"></i>
                    <span>Contact Information</span>
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control"
                            placeholder="<?php echo PHONE_PLACEHOLDER; ?>"
                            value="<?php echo htmlspecialchars($ad['mobile_number']); ?>" required
                            pattern="<?php echo PHONE_PATTERN; ?>"
                            maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Secondary Number</label>
                        <input type="text" name="secondary_number" class="form-control"
                            placeholder="Optional"
                            value="<?php echo htmlspecialchars($ad['secondary_number']); ?>"
                            pattern="<?php echo PHONE_PATTERN; ?>"
                            maxlength="<?php echo VALIDATION_RULES['phone_exact_length']; ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="whatsapp_enabled" id="whatsapp"
                            <?php if ($ad['whatsapp_enabled']) echo 'checked'; ?>>
                        <label class="form-check-label" for="whatsapp">
                            <i class="fab fa-whatsapp text-success"></i> Enable WhatsApp Contact
                        </label>
                    </div>
                </div>

                <div class="divider"></div>

                <h5 class="section-title">
                    <i class="fas fa-images"></i>
                    <span>Images</span>
                </h5>

                <p class="form-hint">
                    <i class="fas fa-info-circle"></i> Leave blank to keep existing images. Upload new images to replace them.
                </p>

                <div class="row">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Image <?php echo $i; ?> <?php if ($i == 1) echo '<span class="text-danger">*</span>'; ?></label>
                            <?php
                            $img_field = "image_$i";
                            $upload_path = getUploadDir($type);
                            if (!empty($ad[$img_field])):
                            ?>
                                <div class="image-preview">
                                    <img src="<?php echo $upload_path . htmlspecialchars($ad[$img_field]); ?>" alt="Current Image">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="<?php echo $img_field; ?>" class="form-control" 
                                accept="image/<?php echo implode(',image/', IMAGE_ALLOWED_EXTENSIONS); ?>">
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="btn-group-custom">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i>
                        <span>Update <?php echo ucfirst($type); ?> Ad</span>
                    </button>
                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad_id; ?>&type=<?php echo $type; ?>" class="cancel-btn">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>