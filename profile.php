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


// Fetch total hours worked for the current month
$currentMonthStart = date('Y-m-01');
$currentMonthEnd = date('Y-m-t');

$stmt = $conn->prepare("SELECT SUM(hours_worked) as total_hours FROM timesheets WHERE email = ? AND date BETWEEN ? AND ?");
$stmt->bind_param("sss", $userEmail, $currentMonthStart, $currentMonthEnd);
$stmt->execute();
$result = $stmt->get_result();
$totalHours = $result->fetch_assoc()['total_hours'] ?? 0;

// Fetch timesheet data with pagination and date filter
$limit = 5; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Set date filter range
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch filtered timesheet records
$stmt = $conn->prepare("SELECT * FROM timesheets WHERE email = ? AND date BETWEEN ? AND ? ORDER BY date DESC LIMIT ? OFFSET ?");
$stmt->bind_param("sssii", $userEmail, $startDate, $endDate, $limit, $offset);
$stmt->execute();
$timesheetResult = $stmt->get_result();

// Fetch the total number of records for pagination
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM timesheets WHERE email = ? AND date BETWEEN ? AND ?");
$stmt->bind_param("sss", $userEmail, $startDate, $endDate);
$stmt->execute();
$countResult = $stmt->get_result();
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Handle timesheet download
if (isset($_POST['download_timesheet'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="timesheet.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Date', 'Hours Worked', 'Description']);

    $downloadStmt = $conn->prepare("SELECT date, hours_worked, description FROM timesheets WHERE email = ? AND date BETWEEN ? AND ?");
    $downloadStmt->bind_param("sss", $userEmail, $startDate, $endDate);
    $downloadStmt->execute();
    $downloadResult = $downloadStmt->get_result();

    while ($row = $downloadResult->fetch_assoc()) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

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
            <?php if ($role == 1) { // Show this option only for Administrators ?>
                <a href="?tab=manage_requests" class="<?php echo $activeTab == 'manage_requests' ? 'active' : ''; ?>"><i class="fas fa-user-check"></i> Manage Requests</a>
            <?php } ?>


            <a href="rvapp.php">
                <i class="fas fa-cogs"></i> My Apps
            </a>
            <a href="?tab=change_password" class="<?php echo $activeTab == 'change_password' ? 'active' : ''; ?>"><i class="fas fa-key"></i> Change Password</a>

            <?php if ($role == 2): // Show this option only for contributors ?>
                <a href="?tab=timesheet" class="<?php echo $activeTab == 'timesheet' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Timesheet
                </a>

            <?php endif; ?>            
            
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

                <?php elseif ($activeTab == 'manage_requests' && $role == 2): ?>
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

                <?php elseif ($activeTab == 'timesheet' && $role == 1): ?>
                        <!-- Timesheet Section for Contributors -->
                        
                        <h4>Timesheet</h4>

                        <!-- Display Total Hours Worked This Month -->
                        <p><strong>Total Hours Worked (<?php echo date('F Y'); ?>):</strong> <?php echo htmlspecialchars(number_format($totalHours, 2)); ?> hours</p>

                        <!-- Date Filter Form -->
                        <form method="get" class="d-flex mb-3">
                            <input type="hidden" name="tab" value="timesheet">
                            <input type="date" name="start_date" class="form-control me-2" value="<?php echo htmlspecialchars($startDate); ?>">
                            <input type="date" name="end_date" class="form-control me-2" value="<?php echo htmlspecialchars($endDate); ?>">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>

                        <!-- Download Timesheet Button -->
                        <form method="post" class="mb-3">
                            <button type="submit" name="download_timesheet" class="btn btn-secondary">Download Timesheet</button>
                        </form>

                        <!-- Display Timesheet Entries -->
                        <table class="table table-bordered mt-4">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Hours Worked</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($timesheet = $timesheetResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($timesheet['date']); ?></td>
                                        <td><?php echo htmlspecialchars($timesheet['hours_worked']); ?></td>
                                        <td><?php echo htmlspecialchars($timesheet['description']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if ($timesheetResult->num_rows == 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No records found for the selected date range.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?tab=timesheet&start_date=<?php echo htmlspecialchars($startDate); ?>&end_date=<?php echo htmlspecialchars($endDate); ?>&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>

                        <!-- Add New Timesheet Entry Form -->
                        <form method="post" class="mt-4">
                            <div class="mb-3">
                                <label for="date" class="form-label required">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="hours_worked" class="form-label required">Hours Worked</label>
                                <input type="number" class="form-control" id="hours_worked" name="hours_worked" min="1" step="0.5" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_timesheet" class="btn btn-primary">Add Entry</button>
                        </form>

                        <?php
                        // Handle new timesheet entry
                        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_timesheet'])) {
                            $date = $_POST['date'];
                            $hoursWorked = $_POST['hours_worked'];
                            $description = $_POST['description'];

                            // Insert new timesheet entry into the database
                            $stmt = $conn->prepare("INSERT INTO timesheets (email, date, hours_worked, description) VALUES (?, ?, ?, ?)");
                            $stmt->bind_param("ssds", $userEmail, $date, $hoursWorked, $description);

                            if ($stmt->execute()) {
                                echo '<div class="alert alert-success mt-3">Timesheet entry added successfully.</div>';
                            } else {
                                echo '<div class="alert alert-danger mt-3">Failed to add entry. Please try again.</div>';
                            }
                        }
                        ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<br>
<br>
<?php include('footer.php'); ?>
