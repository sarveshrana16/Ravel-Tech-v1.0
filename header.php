<?php 
session_start(); // Start the session to check login status
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ravel Tech</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet"> 

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="lib/animate/animate.min.css"/>
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<!-- Spinner Start -->
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<!-- Spinner End -->

<!-- Topbar Start -->
<div class="container-fluid topbar bg-light px-5 d-none d-lg-block">
    <div class="row gx-0 align-items-center">
        <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
            <div class="d-flex flex-wrap">
                <a href="#" class="text-muted small me-4"><i class="fas fa-map-marker-alt text-primary me-2"></i>Canada</a>
                <a href="tel:+01234567890" class="text-muted small me-4"><i class="fas fa-phone-alt text-primary me-2"></i>+01234567890</a>
                <a href="mailto:example@gmail.com" class="text-muted small me-0"><i class="fas fa-envelope text-primary me-2"></i>Example@gmail.com</a>
            </div>
        </div>
        <div class="col-lg-4 text-center text-lg-end">
            <div class="d-inline-flex align-items-center" style="height: 45px;">
                <!-- Show Register and Login links if the user is not logged in -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="rvregister.php"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Register</small></a>
                    <a href="rvlogin.php"><small class="me-3 text-dark"><i class="fa fa-sign-in-alt text-primary me-2"></i>Login</small></a>
                <?php else: ?>
                    <!-- Show My Dashboard dropdown only if the user is logged in -->
                    
                    <a href=""><small class="me-3 text-dark"><i class="fa text-primary me-2"></i>Hi! <?php echo $_SESSION['username'] ?></small></a>
                    <div class="dropdown">
                        <a href="dashboard/index.php" class="dropdown-toggle text-dark" data-bs-toggle="dropdown"><small><i class="fa fa-home text-primary me-2"></i> My Dashboard</small></a>
                        <div class="dropdown-menu rounded">
                            <a href="dashboard/index.php" class="dropdown-item"><i class="fas fa-user-alt me-2"></i>Dashboard</a>
                            <a href="profile.php" class="dropdown-item"><i class="fas fa-user-alt me-2"></i> My Profile</a>
                            <a href="notifications.php" class="dropdown-item"><i class="fas fa-bell me-2"></i> Notifications</a>
                            <a href="settings.php" class="dropdown-item"><i class="fas fa-cog me-2"></i> Account Settings</a>
                            <a href="logout.php" class="dropdown-item"><i class="fas fa-power-off me-2"></i>Log Out</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Topbar End -->

<!-- Navbar & Hero Start -->
<div class="container-fluid position-relative p-0">
    <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
        <a href="index.php" class="navbar-brand p-0">
            <img src="img/raveltechlogo.png" alt="Ravel Tech Logo" class="img-fluid" style="max-height: 100px;">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <?php 
                    $current_page = basename($_SERVER['PHP_SELF']);
                ?>
                <a href="index.php" class="nav-item nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link <?php echo ($current_page == 'webapp.php' || $current_page == 'dbmanage.php' || $current_page == 'PBIreport.php' || $current_page == 'ecomm.php' || $current_page == 'azbisupport.php') ? 'active' : ''; ?>" data-bs-toggle="dropdown">
                        <span class="dropdown-toggle">Services</span>
                    </a>
                    <div class="dropdown-menu m-0">
                        <a href="webapp.php" class="dropdown-item <?php echo ($current_page == 'webapp.php') ? 'active' : ''; ?>">Web Application</a>
                        <a href="dbmanage.php" class="dropdown-item <?php echo ($current_page == 'dbmanage.php') ? 'active' : ''; ?>">Database Management</a>
                        <a href="PBIreport.php" class="dropdown-item <?php echo ($current_page == 'PBIreport.php') ? 'active' : ''; ?>">Power BI Reporting</a>
                        <a href="ecomm.php" class="dropdown-item <?php echo ($current_page == 'ecomm.php') ? 'active' : ''; ?>">eCommerce Solution</a>
                        <a href="azbisupport.php" class="dropdown-item <?php echo ($current_page == 'azbisupport.php') ? 'active' : ''; ?>">Azure BI Support</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link <?php echo ($current_page == 'feature.php' || $current_page == 'team.php' || $current_page == 'testimonial.php') ? 'active' : ''; ?>" data-bs-toggle="dropdown">
                        <span class="dropdown-toggle">Products</span>
                    </a>
                    <div class="dropdown-menu m-0">
                        <a href="feature.php" class="dropdown-item <?php echo ($current_page == 'feature.php') ? 'active' : ''; ?>">Ravel Express Render (RER)</a>
                        <a href="team.php" class="dropdown-item <?php echo ($current_page == 'team.php') ? 'active' : ''; ?>">Plan Check Pro (FPAR)</a>
                        <a href="testimonial.php" class="dropdown-item <?php echo ($current_page == 'testimonial.php') ? 'active' : ''; ?>">Auto Spec Pro (MSAS)</a>
                        <a href="RPP/index.php" class="dropdown-item <?php echo ($current_page == 'testimonial.php') ? 'active' : ''; ?>">Product Polish (RPP)</a>
                    </div>
                </div>
                <a href="service.php" class="nav-item nav-link <?php echo ($current_page == 'service.php') ? 'active' : ''; ?>">Pricing</a>
                <a href="about.php" class="nav-item nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">About</a>
                <a href="blog.php" class="nav-item nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">Innovation Alley</a>
                <a href="contact.php" class="nav-item nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact Us</a>
            </div>
            <a href="#" class="btn btn-primary rounded-pill py-2 px-4 my-3 my-lg-0 flex-shrink-0">Get Started</a>
        </div>
    </nav>
</div>
<!-- Navbar & Hero End -->
