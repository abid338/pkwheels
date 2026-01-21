<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$message = "";
$base_path = "../";
$css_path = "../";
$page_title = "Post an Ad - PakWheels";

$colors_result = mysqli_query($conn, "SELECT * FROM colors ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $selected_colors = $_POST['colors'] ?? [];

    $sql_vehicle = "INSERT INTO vehicles (user_id,title,brand,model,year,price,city,description) 
                    VALUES ('$user_id','$title','$brand','$model','$year','$price','$city','$description')";

    if (mysqli_query($conn, $sql_vehicle)) {
        $vehicle_id = mysqli_insert_id($conn);

        foreach ($selected_colors as $color_id) {
            mysqli_query($conn, "INSERT INTO vehicle_colors (vehicle_id,color_id) VALUES ('$vehicle_id','$color_id')");
        }

        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = "../uploads/ads/";
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $filename = basename($_FILES['images']['name'][$key]);
                $target_file = $upload_dir . time() . "_" . $filename;
                if (move_uploaded_file($tmp_name, $target_file)) {
                    mysqli_query($conn, "INSERT INTO vehicle_images (vehicle_id,image_path) VALUES ('$vehicle_id','$target_file')");
                }
            }
        }

        header("Location: details.php?id=$vehicle_id");
        exit;
    } else {
        $message = "Failed to post vehicle!";
    }
}

include "../includes/header.php";
include "../includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/add.css">
    <title>Document</title>
</head>

<body>


    <!-- Header Section -->
    <div class="post-ad-header">
        <div class="container text-center">
            <h1><i class="fas fa-plus-circle me-3"></i>Post Your Ad</h1>
            <p>Sell your vehicle in just a few simple steps!</p>
        </div>
    </div>

    <div class="container pb-5">

        <?php if ($message): ?>
            <div class="alert alert-danger alert-modern">
                <i class="fas fa-exclamation-circle"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="post-ad-card">
            <div class="form-section">
                <form method="POST" enctype="multipart/form-data" id="postAdForm">
                    <!-- Basic Information Section -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-heading"></i> Ad Title
                                </label>
                                <input type="text" name="title" class="form-control modern-form-control"
                                    placeholder="e.g., Honda Civic 2020 VTi Oriel" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-copyright"></i> Brand
                                </label>
                                <input type="text" name="brand" class="form-control modern-form-control"
                                    placeholder="e.g., Honda, Toyota" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-car"></i> Model
                                </label>
                                <input type="text" name="model" class="form-control modern-form-control"
                                    placeholder="e.g., Civic, Corolla" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-calendar-alt"></i> Year
                                </label>
                                <input type="number" name="year" class="form-control modern-form-control"
                                    placeholder="e.g., 2020" min="1990" max="2026" required>
                            </div>
                        </div>
                    </div>
                    <!-- Pricing & Location Section -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Pricing & Location
                        </h3>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-tag"></i> Price (PKR)
                                </label>
                                <input type="number" name="price" class="form-control modern-form-control"
                                    placeholder="e.g., 5000000" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="modern-form-label">
                                    <i class="fas fa-map-marker-alt"></i> City
                                </label>
                                <input type="text" name="city" class="form-control modern-form-control"
                                    placeholder="e.g., Lahore, Karachi" required>
                            </div>
                        </div>
                    </div>
                    <!-- Description Section -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-align-left"></i>
                            Description
                        </h3>

                        <div class="mb-3">
                            <label class="modern-form-label">
                                <i class="fas fa-file-alt"></i> Vehicle Description
                            </label>
                            <textarea name="description" class="form-control modern-form-control" rows="6"
                                placeholder="Provide detailed information about your vehicle..." required></textarea>
                            <small class="text-muted">Describe the condition, features, and any special details</small>
                        </div>
                    </div>
                    <!-- Colors Section -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-palette"></i>
                            Available Colors
                        </h3>
                        <div class="color-select-wrapper">
                            <div class="color-checkbox-group">
                                <?php
                                mysqli_data_seek($colors_result, 0);
                                while ($color = mysqli_fetch_assoc($colors_result)):
                                ?>
                                    <div class="color-checkbox-item">
                                        <input type="checkbox" name="colors[]"
                                            id="color_<?php echo $color['id']; ?>"
                                            value="<?php echo $color['id']; ?>">
                                        <label for="color_<?php echo $color['id']; ?>">
                                            <?php echo htmlspecialchars($color['name']); ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Images Section -->
                    <div class="mb-5">
                        <h3 class="section-title">
                            <i class="fas fa-images"></i>
                            Upload Images
                        </h3>

                        <div class="image-upload-zone" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload images or drag and drop</p>
                            <small>PNG, JPG, JPEG (Max 5MB per image)</small>
                        </div>
                        <input type="file" name="images[]" id="imageInput" class="form-control"
                            multiple accept="image/*" required onchange="previewImages(event)">

                        <div id="imagePreview" class="image-preview-container"></div>
                    </div>
                    <!-- Submit Buttons -->
                    <div class="text-center mt-5">
                        <button type="submit" class="btn btn-submit-ad me-3">
                            <i class="fas fa-check-circle me-2"></i>Post Ad Now
                        </button>
                        <a href="../index.php" class="btn btn-cancel">
                            <i class="fas fa-times-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image Preview Function
        function previewImages(event) {
            const previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = '';

            const files = event.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="image-preview-remove" onclick="removeImage(${i})">
                    <i class="fas fa-times"></i>
                </button>
            `;
                    previewContainer.appendChild(div);
                }

                reader.readAsDataURL(file);
            }
        }

        function removeImage(index) {
            const input = document.getElementById('imageInput');
            const dt = new DataTransfer();
            const files = input.files;

            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }

            input.files = dt.files;
            previewImages({
                target: input
            });
        }
        document.getElementById('postAdForm').addEventListener('submit', function(e) {
            const year = document.querySelector('input[name="year"]').value;
            const currentYear = new Date().getFullYear();

            if (year < 1990 || year > currentYear + 1) {
                e.preventDefault();
                alert('Please enter a valid year between 1990 and ' + (currentYear + 1));
                return false;
            }

            const price = document.querySelector('input[name="price"]').value;
            if (price <= 0) {
                e.preventDefault();
                alert('Please enter a valid price');
                return false;
            }
        });
    </script>

    <?php include "../includes/footer.php"; ?>
</body>

</html>