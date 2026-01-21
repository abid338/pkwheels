<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$message = "";
$page_title = "Edit Ad - PakWheels";
$vehicle_id = $_GET['id'] ?? 0;
$vehicle_id = intval($vehicle_id);
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM vehicles WHERE id=$vehicle_id AND user_id=$user_id LIMIT 1";
$result = mysqli_query($conn, $sql);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    header("Location: ../index.php");
    exit;
}

$colors_result = mysqli_query($conn, "SELECT * FROM colors ORDER BY name ASC");
$selected_colors_result = mysqli_query($conn, "SELECT color_id FROM vehicle_colors WHERE vehicle_id=$vehicle_id");
$selected_colors = [];
while ($row = mysqli_fetch_assoc($selected_colors_result)) {
    $selected_colors[] = $row['color_id'];
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $new_colors = $_POST['colors'] ?? [];

    $sql_update = "UPDATE vehicles SET title='$title', brand='$brand', model='$model', 
                   year='$year', price='$price', city='$city', description='$description' 
                   WHERE id=$vehicle_id AND user_id=$user_id";

    if (mysqli_query($conn, $sql_update)) {
        mysqli_query($conn, "DELETE FROM vehicle_colors WHERE vehicle_id=$vehicle_id");
        foreach ($new_colors as $color_id) {
            mysqli_query($conn, "INSERT INTO vehicle_colors (vehicle_id,color_id) VALUES ('$vehicle_id','$color_id')");
        }

        header("Location: details.php?id=$vehicle_id");
        exit;
    } else {
        $message = "Failed to update vehicle!";
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
    <link rel="stylesheet" href="assets/css/edit.css">
    <title>Document</title>
</head>

<body>


    <div class="edit-container">
        <div class="page-header">
            <h2 class="page-title">Edit Your Ad</h2>
            <p class="page-subtitle">Update your vehicle information</p>
        </div>
        <?php if ($message): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        <div class="edit-card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Title *</label>
                        <input type="text"
                            name="title"
                            class="form-input"
                            value="<?php echo htmlspecialchars($vehicle['title']); ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Brand *</label>
                        <input type="text"
                            name="brand"
                            class="form-input"
                            value="<?php echo htmlspecialchars($vehicle['brand']); ?>"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Model *</label>
                        <input type="text"
                            name="model"
                            class="form-input"
                            value="<?php echo htmlspecialchars($vehicle['model']); ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Year *</label>
                        <input type="number"
                            name="year"
                            class="form-input"
                            value="<?php echo $vehicle['year']; ?>"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price (PKR) *</label>
                        <input type="number"
                            name="price"
                            class="form-input"
                            value="<?php echo $vehicle['price']; ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input type="text"
                            name="city"
                            class="form-input"
                            value="<?php echo htmlspecialchars($vehicle['city']); ?>"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <textarea name="description"
                        class="form-textarea"
                        required><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Colors *</label>
                    <select name="colors[]" class="form-select" multiple>
                        <?php while ($color = mysqli_fetch_assoc($colors_result)): ?>
                            <option value="<?php echo $color['id']; ?>"
                                <?php if (in_array($color['id'], $selected_colors)) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($color['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <small class="form-hint">
                        <i class="fas fa-info-circle"></i> Hold Ctrl (Windows) / Cmd (Mac) to select multiple
                    </small>
                </div>

                <div class="button-group">
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Update Ad
                    </button>
                    <a href="details.php?id=<?php echo $vehicle_id; ?>" class="cancel-btn">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
</body>

</html>