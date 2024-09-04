<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "raveltech";

// Check if dbname is provided via POST method, otherwise use default
//$dbname = isset($_POST['dbname']) ? $_POST['dbname'] : $default_dbname;

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // Redirect to 404 page with error message
    header("Location: ../page-error-400.php?error=" . urlencode($e->getMessage()));
    exit();
}