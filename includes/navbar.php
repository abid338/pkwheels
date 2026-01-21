<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <title>navbar</title>
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-dark modern-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $base_path; ?>index.php">
                <i class="fas fa-car"></i>
                <span>PakWheels</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo isset($base_path) ? $base_path : ''; ?>index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <!-- Vehicles Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="vehiclesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-th-large me-1"></i> Vehicles
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_path; ?>search.php?type=new_bike">
                                    <i class="fas fa-motorcycle text-success"></i> New Bikes
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_path; ?>search.php?type=used_bike">
                                    <i class="fas fa-motorcycle text-info"></i> Used Bikes
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_path; ?>search.php?type=new_car">
                                    <i class="fas fa-car text-primary"></i> New Cars
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_path; ?>search.php?type=used_car">
                                    <i class="fas fa-car text-warning"></i> Used Cars
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>search.php">
                            <i class="fas fa-search me-1"></i> Search
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <!-- POST AD DROPDOWN -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn-post-ad"
                                href="#" id="postAdDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-plus-circle me-1"></i> Post an Ad
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_path; ?>post_car_ad.php">
                                        <i class="fas fa-car text-primary"></i> Sell Your Car
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_path; ?>post_bike_ad.php">
                                        <i class="fas fa-motorcycle text-success"></i> Sell Your Bike
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_path; ?>my_ads.php">
                                <i class="fas fa-list me-1"></i> My Ads
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link user-profile-link" href="<?php echo $base_path; ?>profile.php">
                                <i class="fas fa-user-circle"></i> Profile
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link logout-link" href="<?php echo $base_path; ?>auth/logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>

                    <?php else: ?>

                        <li class="nav-item">
                            <a class="nav-link btn-auth-login" href="<?php echo $base_path; ?>auth/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link btn-auth-register" href="<?php echo $base_path; ?>auth/register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>