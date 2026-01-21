<?php
session_start();
include "../config/db.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$vehicle_id = $_GET['id'] ?? 0;
$vehicle_id = intval($vehicle_id);
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM vehicles WHERE id=$vehicle_id AND user_id=$user_id LIMIT 1";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) === 1) {
    mysqli_query($conn, "DELETE FROM vehicle_colors WHERE vehicle_id=$vehicle_id");
    
    $images_result = mysqli_query($conn, "SELECT image_path FROM vehicle_images WHERE vehicle_id=$vehicle_id");
    while($img = mysqli_fetch_assoc($images_result)) {
        if(file_exists($img['image_path'])) {
            unlink($img['image_path']);
        }
    }
    
    mysqli_query($conn, "DELETE FROM vehicle_images WHERE vehicle_id=$vehicle_id");
    mysqli_query($conn, "DELETE FROM vehicles WHERE id=$vehicle_id");
}

header("Location: ../index.php");
exit;
?>