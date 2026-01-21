<?php
session_start();
include "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$script_dir = rtrim($script_dir, '/\\');
$base_path = $script_dir ? $script_dir . '/' : '';
$page_title = "Edit Bike Ad - PakWheels";

$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM bike_ads WHERE id = $ad_id AND user_id = $user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$ad = mysqli_fetch_assoc($result);

if (!$ad) {
    header("Location: my_ads.php");
    exit;
}

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_condition = mysqli_real_escape_string($conn, $_POST['vehicle_condition']);
    $bike_info = mysqli_real_escape_string($conn, $_POST['bike_info']);
    $registered_in = mysqli_real_escape_string($conn, $_POST['registered_in']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $mileage = mysqli_real_escape_string($conn, $_POST['mileage']);
    $engine_type = mysqli_real_escape_string($conn, $_POST['engine_type']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $features = mysqli_real_escape_string($conn, $_POST['features']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $secondary_number = mysqli_real_escape_string($conn, $_POST['secondary_number']);
    $whatsapp_enabled = isset($_POST['whatsapp_enabled']) ? 1 : 0;

    $upload_dir = "uploads/ads/bikes/";
    $images = [];

    for ($i = 1; $i <= 5; $i++) {
        $img_field = "image_$i";

        if (isset($_FILES[$img_field]) && $_FILES[$img_field]['error'] == 0) {
            $file_name = time() . "_" . $i . "_" . basename($_FILES[$img_field]['name']);
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
    }

    $sql_update = "UPDATE bike_ads SET 
                   vehicle_condition = '$vehicle_condition',
                   bike_info = '$bike_info',
                   registered_in = '$registered_in',
                   city = '$city',
                   mileage = '$mileage',
                   engine_type = '$engine_type',
                   price = '$price',
                   description = '$description',
                   features = '$features',
                   mobile_number = '$mobile_number',
                   secondary_number = '$secondary_number',
                   whatsapp_enabled = '$whatsapp_enabled',
                   image_1 = '{$images['image_1']}',
                   image_2 = '{$images['image_2']}',
                   image_3 = '{$images['image_3']}',
                   image_4 = '{$images['image_4']}',
                   image_5 = '{$images['image_5']}'
                   WHERE id = $ad_id AND user_id = $user_id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: ad_details.php?id=$ad_id&type=bike");
        exit;
    } else {
        $message = "Failed to update bike ad!";
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
    <link rel="stylesheet" href="assets/css/edit_bike_ad.css">
    <title>Edit Bike Ad - PakWheels</title>
</head>

<body>


    <div class="container py-5">
        <div class="page-header">
            <h2 class="page-title">
                <i class="fas fa-edit"></i>
                <span>Edit Bike Ad</span>
            </h2>
            <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad_id; ?>&type=bike" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Ad</span>
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data">

                <h5 class="section-title">
                    <i class="fas fa-motorcycle"></i>
                    <span>Bike Information</span>
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Info <span class="text-danger">*</span></label>
                        <input type="text" name="bike_info" class="form-control"
                            placeholder="e.g., Honda CD 70 2021"
                            value="<?php echo htmlspecialchars($ad['bike_info']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Condition <span class="text-danger">*</span></label>
                        <select name="vehicle_condition" class="form-select" required>
                            <option value="">Select Condition</option>
                            <option value="new" <?php if ($ad['vehicle_condition'] == 'new') echo 'selected'; ?>>New</option>
                            <option value="used" <?php if ($ad['vehicle_condition'] == 'used') echo 'selected'; ?>>Used</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registered In <span class="text-danger">*</span></label>
                        <input type="text" name="registered_in" class="form-control"
                            placeholder="e.g., Lahore"
                            value="<?php echo htmlspecialchars($ad['registered_in']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City <span class="text-danger">*</span></label>
                        <select name="city" class="form-select" required>
                            <option value="">Select City</option>
                            <?php
                            $cities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Gujranwala'];
                            foreach ($cities as $city) {
                                $selected = ($ad['city'] == $city) ? 'selected' : '';
                                echo "<option value='$city' $selected>$city</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Engine Type <span class="text-danger">*</span></label>
                        <input type="text" name="engine_type" class="form-control"
                            placeholder="e.g., 4-Stroke, 2-Stroke"
                            value="<?php echo htmlspecialchars($ad['engine_type']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mileage (KM) <span class="text-danger">*</span></label>
                        <input type="number" name="mileage" class="form-control"
                            placeholder="e.g., 15000"
                            value="<?php echo $ad['mileage']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price (PKR) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control"
                        placeholder="e.g., 85000"
                        value="<?php echo $ad['price']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="4"
                        placeholder="Describe your bike in detail..."
                        required><?php echo htmlspecialchars($ad['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Features</label>
                    <textarea name="features" class="form-control" rows="3"
                        placeholder="Optional - List special features like: Self Start, Alloy Rims, etc."><?php echo htmlspecialchars($ad['features']); ?></textarea>
                </div>

                <div class="divider"></div>

                <h5 class="section-title">
                    <i class="fas fa-phone"></i>
                    <span>Contact Information</span>
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_number" class="form-control"
                            placeholder="e.g., 03001234567"
                            value="<?php echo htmlspecialchars($ad['mobile_number']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Secondary Number</label>
                        <input type="text" name="secondary_number" class="form-control"
                            placeholder="Optional"
                            value="<?php echo htmlspecialchars($ad['secondary_number']); ?>">
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
                            if (!empty($ad[$img_field])):
                            ?>
                                <div class="image-preview">
                                    <img src="uploads/ads/bikes/<?php echo htmlspecialchars($ad[$img_field]); ?>" alt="Current Image">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="<?php echo $img_field; ?>" class="form-control" accept="image/*">
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="btn-group-custom">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i>
                        <span>Update Bike Ad</span>
                    </button>
                    <a href="<?php echo $base_path; ?>ad_details.php?id=<?php echo $ad_id; ?>&type=bike" class="cancel-btn">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>