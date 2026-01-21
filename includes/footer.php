<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/footer.css">
    <title>footer</title>
</head>

<body>
    <footer class="modern-footer text-white mt-5">
        <div class="container py-5">
            <div class="row g-4">
                <!-- About Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">PakWheels</h5>
                    <p class="footer-description">
                        Pakistan's #1 platform for buying and selling vehicles. Find your dream car or bike today with thousands of verified listings!
                    </p>
                    <div class="mt-4">
                        <img src="https://cdn-icons-png.flaticon.com/128/5968/5968534.png" alt="Verified" width="30" class="me-2">
                        <span class="text-white-50 small">Trusted by 100K+ Users</span>
                    </div>
                </div>
                <!-- Contact Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Contact Us</h5>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <a href="tel:+923096527842" class="text-decoration-none" style="color: inherit;">
                                +92 309 6527842
                            </a>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <a href="mailto:abid6527842@gmail.com" class="text-decoration-none" style="color: inherit;">
                                abid6527842@gmail.com
                            </a>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>Lahore, Punjab, Pakistan</div>
                    </div>
                </div>
                <!-- Quick Links Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <div class="d-flex flex-column">
                        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>search.php?type=new_bike" class="footer-link">New Bikes</a>
                        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>search.php?type=used_bike" class="footer-link">Used Bikes</a>
                        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>search.php?type=new_car" class="footer-link">New Cars</a>
                        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>search.php?type=used_car" class="footer-link">Used Cars</a>
                        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>search.php" class="footer-link">Browse All</a>
                    </div>
                </div>
                <!-- Follow Us Section -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Follow Us</h5>
                    <p class="footer-description mb-3">Stay connected with us on social media for the latest updates and offers!</p>
                    <div class="social-links">
                        <a href="#" class="social-icon youtube" target="_blank">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-icon instagram" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon facebook" target="_blank">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="social-icon tiktok" target="_blank">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="footer-copyright mb-0">
                        &copy; 2026 <strong>PakWheels</strong>. All Rights Reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="footer-copyright mb-0">
                        Designed by <i class="fas fa-heart text-danger"></i> Abdullah Abid
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/923096527842?text=Hello! I'm interested in PakWheels" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <!-- Scroll to Top Button -->
    <button class="scroll-top-btn" id="scrollTopBtn" title="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo isset($js_path) ? $js_path : '../'; ?>assets/js/main.js"></script>
    <script>
        // Scroll to Top Functionality
        const scrollTopBtn = document.getElementById('scrollTopBtn');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

</body>

</html>