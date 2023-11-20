<?php
include '../includes/db_connection.php';
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die("Access Denied!");
}

$message = "";

// Add Instrument
if(isset($_POST['add_instrument'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Using a prepared statement for insertion to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO Instrument (instrument_name, description, price, QuantityAvailable) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $description, $price, $quantity);

    if($stmt->execute()) {
        $message = "Instrument added!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetching Instruments for displaying and editing
$instruments = $conn->query("SELECT * FROM Instrument");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admin/admin_dashboard_styles.css">
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p><?php echo $message; ?></p>
        
        <h3>Add an Instrument</h3>
        <form action="admin_dashboard.php" method="post">
            Name: <input type="text" name="name" required><br>
            Description: <textarea name="description"></textarea><br>
            Price: <input type="text" name="price" required><br>
            Quantity: <input type="text" name="quantity" required><br>
            <input type="submit" name="add_instrument" value="Add Instrument">
        </form>

        <!-- ... [the rest of your code above this remains unchanged] ... -->

<h3>Instruments</h3>
<p>
    <?php 
    if(isset($_GET['message'])) {
        echo $_GET['message']; 
    }
    ?>
</p>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $instruments->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['instrument_id']; ?></td>
                <td><?php echo $row['instrument_name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['QuantityAvailable']; ?></td>
                <td>
                    <a href="changes_in_database/edit_instrument.php?id=<?php echo $row['instrument_id']; ?>">Edit</a> |
                    <a href="changes_in_database/quantity_instrument.php?id=<?php echo $row['instrument_id']; ?>">Change Quantity</a> |
                    <a href="changes_in_database/delete_instrument.php?id=<?php echo $row['instrument_id']; ?>" onclick="return confirm('Are you sure you want to delete this instrument?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="button-group">
    <a href="sales_history.php" class="btn">View Sales</a>
    <a href="admin_logout.php" class="btn btn-logout">Logout</a>
</div>


</body>
</html>
