<?php include('header.php'); ?>
<style>
    body {
        background-color: #f4f7fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        background: #ffffff;
        border-radius: 8px;
        border: none;
    }

    .card h4 {
        font-weight: bold;
        color: #333;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    input.form-control {
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    input.form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .text-decoration-none {
        color: #007bff;
    }

    .text-decoration-none:hover {
        color: #0056b3;
        text-decoration: underline;
    }
</style>

<?php
// Process forgot password request if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Dummy check - Replace with actual database check and email logic
    if (empty($email)) {
        echo "<div class='alert alert-danger text-center'>Please enter your email address.</div>";
    } else {
        // Add logic to handle password reset email (e.g., sending a reset link)
        echo "<div class='alert alert-success text-center'>A password reset link has been sent to your email.</div>";
    }
}
?>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Forgot Password</h4>
    </div>
</div>
<!-- Header End -->
<br>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 rounded border-0">
                <h2 class="text-center">Reset Your Password</h2>
                <p class="text-center mb-4">Enter your registered email address, and we'll send you a link to reset your password.</p>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>
                <!-- Add prompt for returning to login -->
                <div class="text-center mt-3">
                    <p>Remembered your password? <a href="login.php" class="text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br>
<?php include('footer.php'); ?>
