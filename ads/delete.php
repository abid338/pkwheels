<?php
session_start();
include "config/db.php";
include "config/constants.php";

requireLogin("auth/login.php");

$ad_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$ad_type = isset($_GET['type']) ? sanitize($conn, $_GET['type']) : '';
$user_id = $_SESSION['user_id'];

// Validate type
if (!in_array($ad_type, ['car', 'bike'])) {
    header("Location: my_ads.php");
    exit;
}


$table = ($ad_type === 'car') ? 'car_ads' : 'bike_ads';
$upload_dir = getUploadDir($ad_type);

// Get image data for deletion
$img_query = "SELECT image_1, image_2, image_3, image_4, image_5 
              FROM $table 
              WHERE id = $ad_id AND user_id = $user_id 
              LIMIT 1";
$img_result = mysqli_query($conn, $img_query);

if ($img_data = mysqli_fetch_assoc($img_result)) {
    // Delete all images from server
    for ($i = 1; $i <= 5; $i++) {
        $img_field = "image_$i";
        if (!empty($img_data[$img_field])) {
            $img_path = $upload_dir . $img_data[$img_field];
            if (file_exists($img_path)) {
                unlink($img_path);
            }
        }
    }


    $delete_sql = "DELETE FROM $table WHERE id = $ad_id AND user_id = $user_id";
    if (mysqli_query($conn, $delete_sql)) {
        $_SESSION['delete_success'] = ucfirst($ad_type) . " ad deleted successfully!";
    } else {
        $_SESSION['delete_error'] = "Failed to delete " . $ad_type . " ad!";
    }
} else {
    $_SESSION['delete_error'] = "Ad not found or you don't have permission to delete it!";
}

// Redirect back to my ads page
header("Location: my_ads.php");
exit;
