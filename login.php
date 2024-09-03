<?php
// Process login if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add your login logic here
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
                <h2 class="text-center">Login</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <a href="forgot_password.php" class="btn btn-link">Forgot your password?</a>
                </form>
            </div>
        </div>
    </div>

    <!-- Include the footer from index.php -->
</body>
</html>
