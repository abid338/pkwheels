<?php

// PAGE TITLES

define('PAGE_TITLES', [
    'home' => 'PakWheels - Buy & Sell Cars, Bikes, Used & New',
    'search' => 'Search Vehicles - PakWheels',
    'profile' => 'My Profile - PakWheels',
    'my_ads' => 'My Ads - PakWheels',
    'post_ad' => 'Post Vehicle Ad - PakWheels',
    'edit_car' => 'Edit Car Ad - PakWheels',
    'edit_bike' => 'Edit Bike Ad - PakWheels',
    'register' => 'Register - PakWheels',
    'login' => 'Login - PakWheels'
]);


// UPLOAD DIRECTORIES

define('UPLOAD_DIR_CARS', 'uploads/cars/');
define('UPLOAD_DIR_BIKES', 'uploads/bikes/');


// IMAGE VALIDATION

define('IMAGE_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('IMAGE_MAX_SIZE', 5 * 1024 * 1024); // 5MB in bytes
define('IMAGE_MAX_SIZE_MB', 5);


// VALIDATION RULES
define('VALIDATION_RULES', [
    'name_min_length' => 3,
    'phone_min_length' => 11,
    'phone_exact_length' => 11,
    'password_min_length' => 6,
    'mileage_min' => 0,
    'mileage_max' => 999999,
    'price_min' => 1000,
    'description_max_car' => 1000,
    'description_max_bike' => 1000
]);


// PHONE NUMBER PATTERNS

define('PHONE_PATTERN', '[0-9]{11}');
define('PHONE_PLACEHOLDER', '03XXXXXXXXX');


// SEARCH KEYWORDS
define('SEARCH_KEYWORDS', [
    'bike' => ['bike', 'motorcycle', 'motorbike',],
    'car' => ['car', 'vehicle',],
    'new' => ['new', 'brand new', 'fresh', 'unused'],
    'used' => ['used', 'second hand', 'old', 'pre-owned']
]);


// PRICE RANGES

define('PRICE_RANGES', [
    '0-100000' => 'Under 1 Lakh',
    '100000-300000' => '1 - 3 Lakh',
    '300000-500000' => '3 - 5 Lakh',
    '500000-1000000' => '5 - 10 Lakh',
    '1000000-2000000' => '10 - 20 Lakh',
    '2000000-5000000' => '20 - 50 Lakh',
    '5000000-10000000' => '50 Lakh - 1 Crore',
    '10000000-99999999' => 'Above 1 Crore'
]);


// CITIES

define('CITIES', [
    'Karachi',
    'Lahore',
    'Islamabad',
    'Rawalpindi',
    'Faisalabad',
    'Multan',
    'Peshawar',
    'Quetta',
    'Sialkot',
    'Gujranwala',
    'Hyderabad',
    'Bahawalpur',
    'Sargodha',
    'Sukkur',
    'Larkana',
    'Mardan',
    'Abbottabad',
    'Gujrat',
    'Kasur',
    'Rahim Yar Khan',
    'Sahiwal',
    'Okara',

]);


// VEHICLE CONDITIONS

define('VEHICLE_CONDITIONS', [
    'new' => 'New',
    'used' => 'Used'
]);


// REGISTERED IN OPTIONS

define('REGISTERED_IN_OPTIONS', [
    'Un-Registered',
    'Karachi',
    'Lahore',
    'Islamabad',
    'Rawalpindi',
    'Faisalabad',
    'Multan',
    'Peshawar',
    'Quetta',
    'Sialkot',
    'Gujranwala',
    'Hyderabad',
    'Bahawalpur'
]);


// CAR COLORS

define('CAR_COLORS', [
    'White',
    'Black',
    'Silver',
    'Grey',
    'Blue',
    'Red',
    'Green',
    'Yellow',
    'Brown',
    'Gold',


    'Maroon',
    'Other'
]);


// BIKE COLORS

define('BIKE_COLORS', [
    'Black',
    'Red',
    'Blue',
    'White',
    'Silver',
    'Grey',
    'Green',

    'Other'
]);


// CAR ENGINE TYPES

define('CAR_ENGINE_TYPES', [
    'Petrol',
    'Diesel',
    'Hybrid',
    'Electric',
    'LPG',
    'CNG'
]);


// BIKE ENGINE TYPES

define('BIKE_ENGINE_TYPES', [
    '2-Stroke',
    '4-Stroke',
    'Electric'
]);


// CAR FEATURES

define('CAR_FEATURES', [
    'abs' => 'ABS',
    'air_conditioning' => 'Air Conditioning',
    'power_steering' => 'Power Steering',
    'power_windows' => 'Power Windows',
    'airbags' => 'Airbags',
    'alloy_rims' => 'Alloy Rims',
    'cruise_control' => 'Cruise Control',
    'sunroof' => 'Sunroof',
    'navigation_system' => 'Navigation System',

    'cd_player' => 'CD Player'
]);


// BIKE FEATURES

define('BIKE_FEATURES', [
    'electric_start' => 'Electric Start',
    'self_start' => 'Self Start',
    'disc_brake' => 'Disc Brake',
    'alloy_rims' => 'Alloy Rims',
    'fuel_injection' => 'Fuel Injection',
    'abs' => 'ABS',
    'led_lights' => 'LED Lights',
    'digital_meter' => 'Digital Meter'
]);


// CONTACT INFORMATION

define('CONTACT_INFO', [
    'phone' => '+92 300 1234567',
    'email' => 'info@pakwheels.com',
    'location' => 'Lahore, Pakistan',
    'whatsapp' => '923001234567'
]);


// SOCIAL MEDIA LINKS

define('SOCIAL_LINKS', [
    'youtube' => 'https://youtube.com/@pakwheels',
    'instagram' => 'https://instagram.com/pakwheels',
    'facebook' => 'https://facebook.com/pakwheels',
    'tiktok' => 'https://tiktok.com/@pakwheels'
]);


// CSS/JS ALERT CLASSES

define('ALERT_CLASSES', [
    'success' => 'custom-alert-success',
    'danger' => 'custom-alert-danger',
    'warning' => 'custom-alert-warning',
    'info' => 'custom-info-alert'
]);



/**
 * Sanitize input data
 */
function sanitize($conn, $data)
{
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($data)));
}

/**
 * Format price with PKR currency
 */
function formatPrice($price)
{
    return 'PKR ' . number_format($price);
}

/**
 * Check if user is logged in
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin($redirect_path = "auth/login.php")
{
    if (!isLoggedIn()) {
        header("Location: $redirect_path");
        exit;
    }
}

/**
 * Redirect if already logged in
 */
function redirectIfLoggedIn($redirect_path = "../index.php")
{
    if (isLoggedIn()) {
        header("Location: $redirect_path");
        exit;
    }
}

/**
 * Calculate base path based on current directory
 */
function calculateBasePath()
{
    $current_dir = basename(getcwd());
    return ($current_dir === 'auth') ? '../' : './';
}

/**
 * Get upload directory based on vehicle type
 */
function getUploadDir($type)
{
    return ($type === 'car') ? UPLOAD_DIR_CARS : UPLOAD_DIR_BIKES;
}

/**
 * Validate email format
 */
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number (must be exactly 11 digits)
 */
function validatePhone($phone)
{
    return preg_match('/^[0-9]{11}$/', $phone);
}

/**
 * Validate image file
 */
function validateImage($file)
{
    $allowed_extensions = IMAGE_ALLOWED_EXTENSIONS;
    $max_size = IMAGE_MAX_SIZE;

    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        return [
            'valid' => false,
            'error' => 'Invalid file type. Allowed: ' . implode(', ', array_map('strtoupper', $allowed_extensions))
        ];
    }

    if ($file['size'] > $max_size) {
        return [
            'valid' => false,
            'error' => 'File size exceeds ' . IMAGE_MAX_SIZE_MB . 'MB limit'
        ];
    }

    return ['valid' => true];
}

/**
 *  unique filename for uploads
 */
function generateUniqueFilename($original_name, $index = 1)
{
    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    return uniqid('img_' . $index . '_') . '.' . $extension;
}

/**
 * Display alert message
 */
function displayAlert($message, $type = 'info')
{
    $alert_class = ALERT_CLASSES[$type] ?? 'alert alert-info';
    $icon = ($type == 'success') ? 'check-circle' : (($type == 'danger') ? 'exclamation-circle' : 'info-circle');

    echo '<div class="' . $alert_class . ' mb-4">';
    echo '<i class="fas fa-' . $icon . ' me-2"></i>' . htmlspecialchars($message);
    echo '</div>';
}
