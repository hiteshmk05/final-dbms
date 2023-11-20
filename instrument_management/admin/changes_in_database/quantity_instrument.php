<?php
include '../../includes/db_connection.php';
session_start();

// Ensure only Admin can access
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die("Access Denied!");
}

$message = "";

// Check if an instrument ID is provided
if (!isset($_GET['id'])) {
    die("Instrument ID not provided!");
}

$instrumentID = $_GET['id'];

// If form is submitted, update the quantity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $newQuantity = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE Instrument SET QuantityAvailable = ? WHERE instrument_id = ?");
    $stmt->bind_param("ii", $newQuantity, $instrumentID);

    if ($stmt->execute()) {
        $message = "Quantity updated successfully!";
    } else {
        $message = "Error updating the quantity: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current details of the instrument
$stmt = $conn->prepare("SELECT * FROM Instrument WHERE instrument_id = ?");
$stmt->bind_param("i", $instrumentID);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Instrument not found!");
}
$instrument = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Instrument Quantity</title>
    <link rel="stylesheet" href="../../assets/admin/admin_edit_price.css">
</head>
<body>
    <h2>Edit Quantity for <?php echo $instrument['instrument_name']; ?></h2>

    <p><?php echo $message; ?></p>

    <form action="" method="post">
        Current Quantity: <input type="text" value="<?php echo $instrument['QuantityAvailable']; ?>" disabled><br>
        New Quantity: <input type="text" name="quantity" required><br>
        <input type="submit" name="update_quantity" value="Update Quantity">
    </form>

    <br>
    <a href="../admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
