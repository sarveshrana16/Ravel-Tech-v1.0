<?php
// Process password reset request if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add your password reset request logic here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Include the header from index.php -->
</head>
<body>
    <!-- Include the navigation from index.php -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Forgot Password</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include the footer from index.php -->
</body>
</html>
