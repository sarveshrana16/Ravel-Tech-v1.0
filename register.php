<?php
// Process registration if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add your registration logic here
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('header.php');?>
</head>
<body>
    <!-- Include the navigation from index.php -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Register</h2>
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="userrole" class="form-label">User Role</label>
                        <select class="form-select" id="userrole" name="userrole" required>
                            <option value="1">User</option>
                            <option value="2">Admin</option>
                            <!-- Add more roles as needed -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include the footer from index.php -->
</body>
</html>
