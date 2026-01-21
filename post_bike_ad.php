<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$script_dir = rtrim($script_dir, '/\\');
$base_path = $script_dir ? $script_dir . '/' : '';
$css_path = $base_path;
$page_title = "Sell Your Bike - PakWheels";

$cities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala', 'Hyderabad', 'Abbottabad'];
$registered_options = ['Un-Registered', 'Islamabad', 'Punjab', 'Sindh', 'KPK', 'Balochistan'];
$engine_types = ['2 Stroke', '4 Stroke', 'Electric'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $city = trim($_POST['city']);
    $vehicle_condition = trim($_POST['vehicle_condition']);
    $bike_info = trim($_POST['bike_info']);
    $registered_in = trim($_POST['registered_in']);
    $mileage = intval($_POST['mileage']);
    $engine_type = trim($_POST['engine_type']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $mobile_number = trim($_POST['mobile_number']);
    $secondary_number = trim($_POST['secondary_number']);
    $whatsapp_enabled = isset($_POST['whatsapp_enabled']) ? 1 : 0;

    $features = [];
    if (isset($_POST['anti_theft_lock'])) $features[] = 'Anti Theft Lock';
    if (isset($_POST['led_light'])) $features[] = 'Led Light';
    if (isset($_POST['disc_brake'])) $features[] = 'Disc Brake';
    if (isset($_POST['wind_shield'])) $features[] = 'Wind Shield';
    $features_json = json_encode($features);

    if (empty($bike_info) || empty($description) || empty($vehicle_condition)) {
        $message = "Please fill all required fields!";
        $message_type = "danger";
    } elseif ($mileage < 1 || $mileage > 1000000) {
        $message = "Please enter valid mileage (1-1000000 KM)!";
        $message_type = "danger";
    } elseif ($price < 1) {
        $message = "Please enter a valid price!";
        $message_type = "danger";
    } elseif (strlen($mobile_number) < 11 || !preg_match('/^[0-9]{11}$/', $mobile_number)) {
        $message = "Please enter a valid 11-digit mobile number!";
        $message_type = "danger";
    } elseif (!empty($secondary_number) && !preg_match('/^[0-9]{11}$/', $secondary_number)) {
        $message = "Secondary number must be 11 digits!";
        $message_type = "danger";
    } else {
        $upload_dir = "uploads/ads/bikes/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $images = [];
        $upload_errors = [];

        for ($i = 1; $i <= 5; $i++) {
            if (isset($_FILES["image_$i"]) && $_FILES["image_$i"]['error'] == 0) {
                $file_size = $_FILES["image_$i"]['size'];
                $file_ext = strtolower(pathinfo($_FILES["image_$i"]['name'], PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

                if ($file_size > 5242880) {
                    $upload_errors[] = "Image $i is too large (max 5MB)";
                    $images["image_$i"] = NULL;
                    continue;
                }

                if (in_array($file_ext, $allowed_ext)) {
                    $new_filename = uniqid() . '_' . time() . '_' . $i . '.' . $file_ext;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES["image_$i"]['tmp_name'], $upload_path)) {
                        $images["image_$i"] = $new_filename;
                    } else {
                        $upload_errors[] = "Failed to upload image $i";
                        $images["image_$i"] = NULL;
                    }
                } else {
                    $upload_errors[] = "Image $i has invalid format";
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

        $stmt = $conn->prepare("INSERT INTO bike_ads (user_id, city, bike_info, registered_in, mileage, engine_type, vehicle_condition, description, price, features, mobile_number, secondary_number, whatsapp_enabled, image_1, image_2, image_3, image_4, image_5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "isssissdssssssssss",
            $user_id,
            $city,
            $bike_info,
            $registered_in,
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
        );

        if ($stmt->execute()) {
            $message = "Bike ad posted successfully!";
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
    <link rel="stylesheet" href="assets/css/post_bike_ad.css">
    <title>Sell Your Bike - PakWheels</title>
</head>

<body>



    <div class="container py-5">

        <div class="text-center mb-5 hero-section">
            <h1 class="display-5 fw-bold gradient-heading">Sell your Bike With 3 Easy & Simple Steps!</h1>
            <p class="text-muted fs-5">It's free and takes less than a minute</p>

            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="step-card">
                        <div class="step-icon step-1">
                            <i class="fas fa-motorcycle fa-3x"></i>
                        </div>
                        <h5 class="mt-3">Enter Your Bike Information</h5>
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
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show custom-alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" id="bikeForm">
            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-primary">
                    <h4 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Bike Information</h4>
                    <small>(All fields marked with * are mandatory)</small>
                </div>
                <div class="card-body p-4">

                    <div class="alert custom-info-alert">
                        <i class="fas fa-lightbulb me-2"></i>We don't allow duplicates of same ad.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">City *</label>
                            <select name="city" class="form-select form-select-lg custom-select" required>
                                <option value="">Select City</option>
                                <?php foreach ($cities as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Condition *</label>
                            <select name="vehicle_condition" class="form-select form-select-lg custom-select" required>
                                <option value="">Select Condition</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                            </select>
                            <small class="text-muted">Is this bike brand new or used?</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold gradient-label">Bike Information *</label>
                        <input type="text" name="bike_info" class="form-control form-control-lg custom-input"
                            placeholder="e.g. Honda CD 70 2020" required maxlength="200">
                        <small class="text-muted">Make/Model/Year</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Registered In *</label>
                            <select name="registered_in" class="form-select form-select-lg custom-select" required>
                                <?php foreach ($registered_options as $reg): ?>
                                    <option value="<?php echo htmlspecialchars($reg); ?>"><?php echo htmlspecialchars($reg); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Mileage * (km)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text gradient-input-icon">KM</span>
                                <input type="number" name="mileage" class="form-control custom-input"
                                    placeholder="Enter mileage" required min="1" max="1000000">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Engine Type *</label>
                            <select name="engine_type" class="form-select form-select-lg custom-select" required>
                                <option value="">Select Engine Type</option>
                                <?php foreach ($engine_types as $engine): ?>
                                    <option value="<?php echo htmlspecialchars($engine); ?>"><?php echo htmlspecialchars($engine); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Price * (Rs.)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text gradient-input-icon">PKR</span>
                                <input type="number" name="price" class="form-control custom-input"
                                    placeholder="Enter price" required min="1">
                            </div>
                            <div class="alert custom-warning-alert mt-2 mb-0">
                                <small><i class="fas fa-lightbulb me-1"></i>Please enter a realistic price to get more genuine responses.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold gradient-label">Describe Your Bike *</label>
                        <textarea name="description" class="form-control custom-textarea" rows="5"
                            placeholder="Example: first owner, genuine parts, maintained by authorized workshop, excellent mileage etc."
                            required maxlength="995"></textarea>
                        <div class="text-end">
                            <small class="text-muted">Remaining Characters: <span id="charCount" class="gradient-text-primary">995</span></small>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-success">
                    <h4 class="mb-0"><i class="fas fa-images me-2"></i>Upload Photos</h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">Add up to 5 photos (JPG, PNG, GIF - Max 5MB each)</p>
                    <div class="row">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <div class="col-md-4 mb-3">
                                <label class="form-label gradient-label">Photo <?php echo $i; ?></label>
                                <input type="file" name="image_<?php echo $i; ?>" class="form-control custom-file-input image-input" accept="image/jpeg,image/png,image/gif" data-max-size="5242880">
                                <small class="text-muted" id="size-info-<?php echo $i; ?>"></small>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div class="card custom-card shadow-lg mb-4">
                <div class="card-header gradient-header-info">
                    <h4 class="mb-0"><i class="fas fa-list-check me-2"></i>Additional Information</h4>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-bold gradient-label mb-3">Features</label>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="anti_theft_lock" id="antiTheft">
                                <label class="form-check-label" for="antiTheft">
                                    <i class="fas fa-lock"></i> Anti Theft Lock
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="disc_brake" id="discBrake">
                                <label class="form-check-label" for="discBrake">
                                    <i class="fas fa-circle-notch"></i> Disc Brake
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="led_light" id="ledLight">
                                <label class="form-check-label" for="ledLight">
                                    <i class="fas fa-lightbulb"></i> Led Light
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="wind_shield" id="windShield">
                                <label class="form-check-label" for="windShield">
                                    <i class="fas fa-shield-alt"></i> Wind Shield
                                </label>
                            </div>
                        </div>
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
                                    placeholder="03XXXXXXXXX" required maxlength="11" pattern="[0-9]{11}">
                            </div>
                            <small class="text-muted">Enter a genuine 11 digit mobile no. with format 03XXXXXXXXX. All inquires will come on this number.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold gradient-label">Secondary Number (Optional)</label>
                            <input type="text" name="secondary_number" class="form-control form-control-lg custom-input"
                                placeholder="03XXXXXXXXX" maxlength="11" pattern="[0-9]{11}">
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
        document.querySelector('textarea[name="description"]').addEventListener('input', function() {
            const maxLength = 995;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            document.getElementById('charCount').textContent = remaining;
        });

        document.querySelectorAll('.image-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const maxSize = parseInt(this.dataset.maxSize);
                const file = this.files[0];
                const infoElement = document.getElementById('size-info-' + this.name.split('_')[1]);

                if (file) {
                    const fileSize = file.size;
                    const fileSizeMB = (fileSize / (1024 * 1024)).toFixed(2);

                    if (fileSize > maxSize) {
                        infoElement.textContent = 'File too large: ' + fileSizeMB + 'MB (max 5MB)';
                        infoElement.classList.add('text-danger');
                        this.value = '';
                    } else {
                        infoElement.textContent = 'Size: ' + fileSizeMB + 'MB';
                        infoElement.classList.remove('text-danger');
                        infoElement.classList.add('text-success');
                    }
                }
            });
        });
    </script>

    <?php include "includes/footer.php"; ?>
</body>

</html>