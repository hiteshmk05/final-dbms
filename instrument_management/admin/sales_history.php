<?php
include '../includes/db_connection.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'Admin') {
    die("Access Denied!");
}

$message = "";
$salesHistory = [];

if (isset($_POST['view_sales_by_date'])) {
    $selectedDate = $_POST['date_of_sale'];

    // Adjust for the entire day
    $startDate = $selectedDate . " 00:00:00";
    $endDate = $selectedDate . " 23:59:59";

    $stmt = $conn->prepare("SELECT * FROM sales WHERE date_of_purchase BETWEEN ? AND ?");
    $stmt->bind_param("ss", $startDate, $endDate);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $salesHistory = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $message = "Error fetching sales data: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Sales History</title>
    <link rel="stylesheet" href="..\assets\admin\admin_sales_history.css">
</head>

<body>
    <div class="container">
        <h2>View Sales History</h2>

        <!-- Date Selector Form -->
        <form action="" method="post">
            <label for="date_of_sale">Select Date:</label>
            <input type="date" name="date_of_sale" required>
            <input type="submit" name="view_sales_by_date" value="View Sales">
        </form>

        <!-- Sales Table -->
        <?php if (!empty($salesHistory)): ?>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Email ID</th>
                    <th>Instrument ID</th>
                    <th>Total Price</th>
                    <th>Date of Purchase</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesHistory as $sale): ?>
                <tr>
                    <td><?php echo $sale['sale_id']; ?></td>
                    <td><?php echo $sale['email_id']; ?></td>
                    <td><?php echo $sale['instrument_id']; ?></td>
                    <td><?php echo $sale['total_price']; ?></td>
                    <td><?php echo $sale['date_of_purchase']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No sales for the selected date.</p>
        <?php endif; ?>

        <a href="admin_dashboard.php">Back to Admin Dashboard</a>
    </div>
</body>

</html>
