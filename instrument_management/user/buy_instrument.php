<?php
include '../includes/db_connection.php';
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'User') {
    die("Access Denied!");
}

if(!isset($_SESSION['email_id'])) {
    die("Session email_id is missing!");
}

$email_id = $_SESSION['email_id'];

// Check if an instrument ID is provided
if(!isset($_GET['id'])) {
    die("Instrument ID not provided!");
}

$instrumentID = $_GET['id'];

// Fetch the instrument's available quantity and price
$query = "SELECT QuantityAvailable, price FROM Instrument WHERE instrument_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instrumentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Instrument not found!");
}

$instrument = $result->fetch_assoc();

// Check if the instrument is still available
if ($instrument['QuantityAvailable'] <= 0) {
    die("Sorry, the instrument is out of stock!");
}

// Deduct quantity of the instrument
$updateQuery = "UPDATE Instrument SET QuantityAvailable = QuantityAvailable - 1 WHERE instrument_id = ?";
$stmt = $conn->prepare($updateQuery);
$stmt->bind_param("i", $instrumentID);
$stmt->execute();

// Record the purchase in the Sales table
$purchaseQuery = "INSERT INTO Sales (email_id, instrument_id, total_price, date_of_purchase) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($purchaseQuery);
$stmt->bind_param("sii", $email_id, $instrumentID, $instrument['price']);
if(!$stmt->execute()) {
    die("Error recording the purchase: " . $stmt->error);
}

// Redirect back to user dashboard with a success message
header("Location: user_dashboard.php?message=Instrument Purchased Successfully");
exit();
?>
