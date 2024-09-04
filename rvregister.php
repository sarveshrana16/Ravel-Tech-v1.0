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

    .required:after {
        content: " *";
        color: red;
    }
</style>

<?php
include('connection_dydb.php'); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $userrole = $_POST['userrole'];
    $contact_details = $_POST['contact_details'] ?? '';
    $join_code = $_POST['join_code'] ?? '';
    $message = '';

    if ($userrole == 3) {
        $join_code = "userrole";
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        $message =  "Passwords do not match. Please try again";
    } else {
        $stmt = $conn->prepare("CALL RegisterUser(?, ?, ?, ?, ?, ?, @p_status)");
        $stmt->bind_param("ssssss", $username, $email, $password, $userrole, $contact_details, $join_code);
        $stmt->execute();
        $stmt->close();

        $result = $conn->query("SELECT @p_status AS status")->fetch_assoc();
        $message = $result['status'];

        include 'rvsendmail.php';

        // Parameters for sending the email
        $receiverEmail = $email;
        $subject = 'New Member Registration';
        $content = $message;

        // Call the sendEmail function
        $result = sendEmail($receiverEmail, $subject, $content);

        // Display the result of the email sending attempt
        echo $result;
    }
}
?>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Member Registration to Ravel Tech</h4>
    </div>
</div>
<!-- Header End -->
<br>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4 rounded border-0">                
                <h6>
                    Note: Registration is restricted to clients and employees only. Upon submitting a registration request, access will be granted only after approval by the administrator. All registration and approval communications will be conducted via email, typically within 1-2 business days.
                </h6>
                <?php if($message!=''){
                    ?><div class='alert alert-info text-center'><?php echo htmlspecialchars($message)?></div>
                <?php }?>
                <form method="post" onsubmit="return validateForm()">
                    <div class="mb-3">
                        <label for="username" class="form-label required">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label required">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contact_details" class="form-label">Contact Details (Optional)</label>
                        <input type="text" class="form-control" id="contact_details" name="contact_details" placeholder="Enter your contact details">
                    </div>
                    <div class="mb-3">
                        <label for="userrole" class="form-label required">Access Role</label>
                        <select class="form-select" id="userrole" name="userrole" required onchange="toggleJoinCode()">
                            <option value="3">User</option>
                            <option value="4">Client</option>                            
                            <option value="2">Contributor</option>
                            <option value="1">Administrator</option>
                            <!-- Add more roles as needed -->
                        </select>
                    </div>
                    <div class="mb-3" id="joinCodeContainer" style="display: none;">
                        <label for="join_code" class="form-label required">Join Code</label>
                        <input type="text" class="form-control" id="join_code" name="join_code" placeholder="Enter join code">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <!-- Add prompt for existing users -->
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<br><br>
<?php include('footer.php'); ?>

<script>
    // JavaScript function to validate if the passwords match
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        if (password !== confirmPassword) {
            alert('Passwords do not match. Please try again.');
            return false;
        }
        return true;
    }

    // JavaScript function to toggle the join code field based on selected role
    function toggleJoinCode() {
        const userrole = document.getElementById('userrole').value;
        const joinCodeContainer = document.getElementById('joinCodeContainer');
        const joinCodeInput = document.getElementById('join_code');

        if (userrole == 3) {
            joinCodeContainer.style.display = 'none';
            joinCodeInput.value = "userrole"; // Set default value for the user role
            joinCodeInput.removeAttribute('required');
        } else {
            joinCodeContainer.style.display = 'block';
            joinCodeInput.value = ""; // Clear value when role is not user
            joinCodeInput.setAttribute('required', 'required');
        }
    }

    // JavaScript function to validate the form
    function validateForm() {
        return validatePasswords();
    }

    // Initialize the join code field state on page load
    window.onload = toggleJoinCode;
</script>
