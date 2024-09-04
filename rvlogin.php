<?php include('header.php'); 
include('connection_dydb.php'); 
session_start(); 

// Process the login form if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare statement to call stored procedure
    $stmt = $conn->prepare("CALL ValidateUser(?, @p_hashed_password, @p_isApproved, @p_status, @p_username)");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();

    // Fetch results from stored procedure
    $result = $conn->query("SELECT @p_hashed_password AS hashed_password, @p_isApproved AS isApproved, @p_status AS status, @p_username AS username")->fetch_assoc();
    $hashed_password = $result['hashed_password'];
    $isApproved = $result['isApproved'];
    $status = $result['status'];
    $username = $result['username'];

    // Display status message if there is an issue
    if ($status !== 'User found and approved.') {
        echo "<div class='alert alert-info text-center'>$status</div>";
    } elseif ($hashed_password && password_verify($password, $hashed_password)) {

        // Set session on successful login
        $_SESSION['user_id'] = $email; // Set user session data as needed
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;

        // Redirect to dashboard or desired page
        echo "<script>window.location.href='index.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Invalid password. Please try again.</div>";
    }
}
?>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Member Login</h4>
    </div>
</div>
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
<body>
<br>
    
    <br><br>  

    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 rounded border-0">
                <h4 class="text-center mb-4">Connect to Your Dashboard</h4>
                <form method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php" class="text-decoration-none">Forgot your password?</a>
                    </div>
                    <div class="text-center mt-3">
                        <p>Not registered yet? <a href="rvregister.php" class="text-decoration-none">Submit your request here</a>.</p>
                    </div>
                </form>

               
            </div>
        </div>
    </div>
</div>

<br><br>

</body>

<!-- Footer -->
<?php include('footer.php'); ?>
