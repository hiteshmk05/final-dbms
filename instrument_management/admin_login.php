<?php
include 'includes/db_connection.php';
session_start();
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_id = $_POST['email_id'];
    $password = $_POST['password'];

    // Check email and fetch user
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email_id = ? AND role = 'Admin'");
    $stmt->bind_param("s", $email_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify password
        if(password_verify($password, $admin['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email_id'] = $admin['email_id'];
            $_SESSION['role'] = $admin['role'];

            header('Location: admin\admin_dashboard.php');
            exit;
        } else {
            $error_message =  "Invalid password!";
        }
    } else {
        $error_message =  "No admin found with the provided email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets\admin\admin_login_styles.css">
    <title>Document</title>
</head>
<body>
    <div class="container">
    <div class="error-message"><?php echo $error_message; ?></div>
        <form action="" method="post">
            <label for="email_id">Admin Email ID</label>
            <input type="email" name="email_id" id="email_id" required><br>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required><br>
            <input type="submit" value="Login">
        </form>
        <a href="index.html" class="home-button">Back to Home</a>
    </div>


</body>
</html>

