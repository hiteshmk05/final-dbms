<?php
include '../includes/db_connection.php';
session_start();

if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'User') {
    die("Access Denied!");
}

$email_id = $_SESSION['email_id'];

$stmt = $conn->prepare("SELECT * FROM Users WHERE email_id = ?");
$stmt->bind_param("s", $email_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$instrumentsQuery = "SELECT * FROM Instrument";
$instruments = $conn->query($instrumentsQuery);

$purchasesQuery = "SELECT Instrument.* FROM Instrument INNER JOIN Sales ON Instrument.instrument_id = Sales.instrument_id WHERE Sales.email_id = ?";
$stmt = $conn->prepare($purchasesQuery);
$stmt->bind_param("s", $email_id);
$stmt->execute();
$purchasedInstruments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="..\assets\user\user_dashboard_profile.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $user['username']; ?>!</h2>

        <h3>Available Instruments:</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Quantity Available</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($instrument = $instruments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $instrument['instrument_name']; ?></td>
                        <td><?php echo $instrument['description']; ?></td>
                        <td><?php echo $instrument['QuantityAvailable']; ?></td>
                        <td><?php echo $instrument['price']; ?></td>
                        <td>
                            <?php if($instrument['QuantityAvailable'] > 0): ?>
                                <a href="buy_instrument.php?id=<?php echo $instrument['instrument_id']; ?>">Buy</a>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3>Your Purchased Instruments:</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while($instrument = $purchasedInstruments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $instrument['instrument_name']; ?></td>
                        <td><?php echo $instrument['description']; ?></td>
                        <td><?php echo $instrument['price']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="/instrument_management/logout.php" class="logout-btn">Logout</a>
                        
    </div>
</body>
</html>
