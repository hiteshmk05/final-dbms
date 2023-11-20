<?php
include '../includes/db_connection.php';
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die("Access Denied!");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Using a prepared statement for insertion to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO Instrument (instrument_name, description, price, quantityAvailable) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $description, $price, $quantity);

    if($stmt->execute()) {
        $message = "Instrument added!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!-- Add Instrument HTML Form -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instrument</title>
</head>
<body>
    <h2>Add a new Instrument</h2>
    <p><?php echo $message; ?></p>

    <form action="add_instrument.php" method="post">
        Name: <input type="text" name="name" required><br>
        Description: <textarea name="description"></textarea><br>
        Price: <input type="number" step="0.01" name="price" required><br>
        Quantity: <input type="number" name="quantity" required><br>
        <input type="submit" value="Add Instrument">
    </form>
    <br>
    <a href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
