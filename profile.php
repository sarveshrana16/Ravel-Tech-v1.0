<?php
include('header.php');
include('connection_dydb.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<script>window.location.href="rvlogin.php";</script>';
    exit;
}

// Fetch logged-in user details from the session
$userEmail = $_SESSION['email'];

// Fetch user details from the database
$stmt = $conn->prepare("SELECT username, email, userrole FROM registrations WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

$username = $userData['username'];
$email = $userData['email'];
$role = $userData['userrole'];

// Handle change password request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    // Fetch the hashed password for verification
    $stmt = $conn->prepare("SELECT password FROM registrations WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $userPasswordData = $result->fetch_assoc();
    $hashedPassword = $userPasswordData['password'];

    // Verify the current password
    if (!password_verify($currentPassword, $hashedPassword)) {
        $error = "Current password is incorrect.";
    } elseif ($newPassword !== $confirmNewPassword) {
        $error = "New passwords do not match.";
    } else {
        // Hash the new password and update it in the database
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE registrations SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $newHashedPassword, $userEmail);

        if ($stmt->execute()) {
            $success = "Password changed successfully.";
        } else {
            $error = "Failed to update password. Please try again.";
        }
    }
}

if($role==1){
    // Handle approve and reject actions for member requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $requestId = $_POST['request_id'];
    $action = $_POST['action'];
    $recemail = $_POST['email'];

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE registrations SET isapproved = 1 WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        if ($stmt->execute()) {
            $message = "Member request approved successfully.";
        } else {
            $message = "Failed to approve the request. Please try again.";
        }
        include 'rvsendmail.php';

        // Sending Email on approval or rejection.
        $receiverEmail = $recemail;
        $subject = 'Member Registration Status';
        $content = $message;
        $result = sendEmail($receiverEmail, $subject, $content);
        echo $result;
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->bind_param("i", $requestId);
        if ($stmt->execute()) {
            $message = "Member request rejected successfully.";
        } else {
            $message = "Failed to reject the request. Please try again.";
        }
    }
}

// Fetch all unapproved member requests if the role is admin
$requests = [];
if ($role == 1) {
    $query = "SELECT id, username, email, userrole FROM registrations WHERE isapproved = 0";
    $result = $conn->query($query);
    $requests = $result->fetch_all(MYSQLI_ASSOC);
}
}

// Determine the active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile'; // Default to profile tab
?>

<style>
    .profile-page {
        display: flex;
        min-height: 80vh;
        background-color: #f4f7fa;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .sidebar {
        width: 250px;
        padding: 20px;
        background-color: #343a40;
        color: #fff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #ddd;
    }

    .sidebar h5 {
        font-weight: bold;
        margin-bottom: 20px;
        color: #ffc107;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #ddd;
        border-radius: 5px;
        margin-bottom: 5px;
        transition: background-color 0.3s, color 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #ffc107;
        color: #343a40;
    }

    .content {
        flex-grow: 1;
        padding: 30px;
        background-color: #ffffff;
    }

    .content h4 {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        background-color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table th, .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .table th {
        background-color: #ffc107;
        color: black;
    }

    .table td {
        color: #333;
    }

    .btn-approve {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-approve:hover {
        background-color: #218838;
    }

    .btn-reject {
        background-color: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-reject:hover {
        background-color: #c82333;
    }

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a {
        padding: 10px 15px;
        margin: 0 5px;
        text-decoration: none;
        color: #007bff;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #007bff;
        color: #fff;
    }

    .pagination .active {
        background-color: #007bff;
        color: #fff;
        border: 1px solid #007bff;
    }
</style>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Welcome, <?php echo htmlspecialchars($username); ?></h4>
    </div>
</div>
<br>
<br>
<div class="container">
    <div class="profile-page">
        <div class="sidebar">
            <h5>Dashboard</h5>
            <a href="?tab=profile" class="<?php echo $activeTab == 'profile' ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i> Profile Details</a>
            <?php if ($role == 1) { ?>
                <a href="?tab=manage_requests" class="<?php echo $activeTab == 'manage_requests' ? 'active' : ''; ?>"><i class="fas fa-user-check"></i> Manage Requests</a>
            <?php } ?>


            <a href="rvapp.php">
                <i class="fas fa-cogs"></i> My Apps
            </a>
            <a href="?tab=change_password" class="<?php echo $activeTab == 'change_password' ? 'active' : ''; ?>"><i class="fas fa-key"></i> Change Password</a>            
            
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>

        </div>

        <div class="content">
            <?php if ($activeTab == 'profile'): ?>
                <h4>Profile Details</h4>
                <div class="mb-3">
                    <label class="form-label"><strong>Username:</strong></label>
                    <p><?php echo htmlspecialchars($username); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Email:</strong></label>
                    <p><?php echo htmlspecialchars($email); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Role:</strong></label>
                    <p><?php echo htmlspecialchars($role); ?></p>
                </div>

            <?php elseif ($activeTab == 'change_password'): ?>
                <h4>Change Password</h4>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php elseif (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>

                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>

                <?php elseif ($activeTab == 'manage_requests' && $role == 1): ?>
                <h4>Manage Member Requests</h4>

                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['username']); ?></td>
                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    <td>
                                        <?php
                                        // Display user role based on numeric value
                                        switch ($request['userrole']) {
                                            case 1:
                                                echo 'Administrator';
                                                break;
                                            case 2:
                                                echo 'Contributor';
                                                break;
                                            case 3:
                                                echo 'User';
                                                break;
                                            case 4:
                                                echo 'Client';
                                                break;
                                            default:
                                                echo 'Unknown';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($request['email']); ?>">
                                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($request['username']); ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-approve btn-sm"><i class="fas fa-check"></i> Approve</button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                            <button type="submit" name="action" value="reject" class="btn btn-reject btn-sm"><i class="fas fa-times"></i> Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No pending member requests found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<br>
<br>
<?php include('footer.php'); ?>
