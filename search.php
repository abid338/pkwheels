<?php
session_start();
include "config/db.php";

$page_title = "Search Vehicles - PakWheels";
$css_path = "";
$base_path = "";

$keyword = $_GET['keyword'] ?? '';
$city = $_GET['city'] ?? '';
$price = $_GET['price'] ?? '';
$type = $_GET['type'] ?? '';

$results = [];
$keyword_lower = strtolower(trim($keyword));
$search_bikes = true;
$search_cars = true;
$condition_filter = '';
if (!empty($keyword_lower)) {
    if (strpos($keyword_lower, 'bike') !== false || strpos($keyword_lower, 'motorcycle') !== false) {
        $search_bikes = true;
        $search_cars = false;
    }
    if (strpos($keyword_lower, 'car') !== false) {
        $search_cars = true;
        $search_bikes = false;
    }
    if ((strpos($keyword_lower, 'bike') !== false || strpos($keyword_lower, 'motorcycle') !== false)
        && strpos($keyword_lower, 'car') !== false
    ) {
        $search_bikes = true;
        $search_cars = true;
    }
    if (strpos($keyword_lower, 'new') !== false) {
        $condition_filter = 'new';
    }
    if (strpos($keyword_lower, 'used') !== false) {
        $condition_filter = 'used';
    }
}
if (!empty($type)) {
    if ($type == 'new_bike') {
        $search_bikes = true;
        $search_cars = false;
        $condition_filter = 'new';
    } elseif ($type == 'used_bike') {
        $search_bikes = true;
        $search_cars = false;
        $condition_filter = 'used';
    } elseif ($type == 'new_car') {
        $search_cars = true;
        $search_bikes = false;
        $condition_filter = 'new';
    } elseif ($type == 'used_car') {
        $search_cars = true;
        $search_bikes = false;
        $condition_filter = 'used';
    }
}

$cities_sql = "SELECT DISTINCT city FROM car_ads UNION SELECT DISTINCT city FROM bike_ads ORDER BY city ASC";
$cities_result = mysqli_query($conn, $cities_sql);
function buildKeywordSearch($keyword, $info_field)
{
    if (empty($keyword)) return '';

    global $conn;
    $search_terms = preg_replace('/(bike|car|motorcycle|new|used)/i', '', $keyword);
    $search_terms = trim($search_terms);

    if (empty($search_terms)) {
        return '';
    }

    $keyword_safe = mysqli_real_escape_string($conn, $search_terms);

    return " AND (
        $info_field LIKE '%$keyword_safe%' OR 
        description LIKE '%$keyword_safe%'
    )";
}

// SEARCH CARS
if ($search_cars) {
    $car_sql = "SELECT *, 'car' as vehicle_type FROM car_ads WHERE 1=1";
    if (!empty($condition_filter)) {
        $car_sql .= " AND vehicle_condition = '$condition_filter'";
    }
    $car_sql .= buildKeywordSearch($keyword, 'car_info');
    if (!empty($city)) {
        $city_safe = mysqli_real_escape_string($conn, $city);
        $car_sql .= " AND city = '$city_safe'";
    }
    if (!empty($price)) {
        $parts = explode("-", $price);
        if (count($parts) === 2) {
            $min = floatval($parts[0]);
            $max = floatval($parts[1]);
            $car_sql .= " AND price BETWEEN $min AND $max";
        }
    }

    $car_sql .= " ORDER BY created_at DESC";

    $car_result = mysqli_query($conn, $car_sql);

    if ($car_result) {
        while ($row = mysqli_fetch_assoc($car_result)) {
            $results[] = $row;
        }
    }
}
if ($search_bikes) {
    $bike_sql = "SELECT *, 'bike' as vehicle_type FROM bike_ads WHERE 1=1";
    if (!empty($condition_filter)) {
        $bike_sql .= " AND vehicle_condition = '$condition_filter'";
    }
    $bike_sql .= buildKeywordSearch($keyword, 'bike_info');
    if (!empty($city)) {
        $city_safe = mysqli_real_escape_string($conn, $city);
        $bike_sql .= " AND city = '$city_safe'";
    }
    if (!empty($price)) {
        $parts = explode("-", $price);
        if (count($parts) === 2) {
            $min = floatval($parts[0]);
            $max = floatval($parts[1]);
            $bike_sql .= " AND price BETWEEN $min AND $max";
        }
    }

    $bike_sql .= " ORDER BY created_at DESC";

    $bike_result = mysqli_query($conn, $bike_sql);

    if ($bike_result) {
        while ($row = mysqli_fetch_assoc($bike_result)) {
            $results[] = $row;
        }
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
    <link rel="stylesheet" href="assets/css/search.css">
    <title>Search Vehicles - PakWheels</title>
</head>

<body>
    <div class="container py-5">
        <div class="search-header">
            <h2 class="search-title d-inline-block">
                Search All Vehicles
            </h2>
            <span class="result-count">
                <i class="fas fa-list me-1"></i>
                <?php echo count($results); ?> Results
            </span>
        </div>

        <div class="search-form-card">
            <form method="GET" class="row g-3">
                <?php if (!empty($type)): ?>
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                <?php endif; ?>

                <div class="col-md-3">
                    <input type="text" name="keyword" class="form-control search-input"
                        placeholder="Honda, bike, new car,..."
                        value="<?php echo htmlspecialchars($keyword); ?>">
                </div>
                <div class="col-md-3">
                    <select name="city" class="form-select search-select">
                        <option value="">All Cities</option>
                        <?php
                        mysqli_data_seek($cities_result, 0);
                        while ($c = mysqli_fetch_assoc($cities_result)):
                        ?>
                            <option value="<?php echo htmlspecialchars($c['city']); ?>"
                                <?php if ($c['city'] == $city) echo "selected"; ?>>
                                <?php echo htmlspecialchars($c['city']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="price" class="form-select search-select">
                        <option value="">Any Price</option>
                        <option value="0-500000" <?php if ($price == "0-500000") echo "selected"; ?>>Under 500K</option>
                        <option value="500001-1000000" <?php if ($price == "500001-1000000") echo "selected"; ?>>500K - 1M</option>
                        <option value="1000001-5000000" <?php if ($price == "1000001-5000000") echo "selected"; ?>>1M - 5M</option>
                        <option value="5000001-10000000" <?php if ($price == "5000001-10000000") echo "selected"; ?>>5M - 10M</option>
                        <option value="10000001-99999999" <?php if ($price == "10000001-99999999") echo "selected"; ?>>Above 10M</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn search-btn w-100">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <?php if (count($results) > 0): ?>
                <?php foreach ($results as $vehicle): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card vehicle-card h-100">
                            <?php
                            $img_path = '';
                            if (!empty($vehicle['image_1'])) {
                                $img_path = "uploads/ads/" . $vehicle['vehicle_type'] . "s/" . $vehicle['image_1'];
                            }
                            ?>

                            <?php if ($img_path && file_exists($img_path)): ?>
                                <img src="<?php echo htmlspecialchars($img_path); ?>"
                                    class="card-img-top vehicle-card-img" alt="Vehicle">
                            <?php else: ?>
                                <div class="vehicle-card-placeholder">
                                    <i class="fas fa-<?php echo $vehicle['vehicle_type'] == 'car' ? 'car' : 'motorcycle'; ?> fa-3x"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="vehicle-title mb-0">
                                        <?php
                                        echo htmlspecialchars($vehicle['vehicle_type'] == 'car' ?
                                            $vehicle['car_info'] : $vehicle['bike_info']);
                                        ?>
                                    </h6>
                                    <?php if (isset($vehicle['vehicle_condition'])): ?>
                                        <span class="badge-<?php echo $vehicle['vehicle_condition']; ?>">
                                            <?php echo strtoupper($vehicle['vehicle_condition']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="vehicle-price mb-2">
                                    PKR <?php echo number_format($vehicle['price']); ?>
                                </p>

                                <div class="mb-2">
                                    <div class="vehicle-info mb-1">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($vehicle['city']); ?></span>
                                    </div>
                                    <?php if (isset($vehicle['mileage']) && $vehicle['mileage'] > 0): ?>
                                        <div class="vehicle-info">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span><?php echo number_format($vehicle['mileage']); ?> KM</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <a href="ad_details.php?id=<?php echo $vehicle['id']; ?>&type=<?php echo $vehicle['vehicle_type']; ?>"
                                    class="btn view-details-btn w-100">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="no-results-title mb-3">No Vehicles Found</h4>
                        <p class="text-muted">
                            Try different search criteria to find what you're looking for!
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>