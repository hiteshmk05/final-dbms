<?php
include '../../includes/db_connection.php';
session_start();

// Ensure only Admin can access
if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die("Access Denied!");
}

$message = "";

// Check if an instrument ID is provided
if(!isset($_GET['id'])) {
    die("Instrument ID not provided!");
}

$instrumentID = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    $stmt = $conn->prepare("DELETE FROM Instrument WHERE instrument_id = ?");
    $stmt->bind_param("i", $instrumentID);

    if ($stmt->execute()) {
        $message = "Instrument deleted successfully!";
    } else {
        $message = "Error deleting the instrument: " . $stmt->error;
    }
    $stmt->close();

    // Redirecting back to admin dashboard after deletion
    header("Location: admin_dashboard.php?message=" . urlencode($message));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Instrument</title>
    <link rel="stylesheet" href="../../assets/admin/delete_instrument.css">
</head>
<body>
    <h2>Confirm Delete</h2>

    <p>Are you sure you want to delete this instrument?</p>

    <form action="" method="post">
        <input type="submit" name="confirm_delete" value="Yes, Delete">
        <a href="../admin_dashboard.php">No, Go Back</a>
    </form>
</body>
</html>
