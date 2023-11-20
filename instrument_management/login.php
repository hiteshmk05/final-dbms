<?php
include 'includes/db_connection.php';
session_start();
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_id = $_POST['email_id'];
    $password = $_POST['password'];

    // Check email and fetch user
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email_id = ?");
    $stmt->bind_param("s", $email_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email_id'] = $user['email_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect to user dashboard for all users, regardless of role.
            header('Location: user/user_dashboard.php');
            exit;
        } else {
            $error_message = "<div class='error-message'>Invalid password!</div>";
        }
    } else {
        $error_message="<div class='error-message'>Email not registered!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/user/login_styles.css">
</head>
<body>

    <div class="container">
    <?php echo $error_message; ?>
        <form action="" method="post">
            <label for="email_id">Email ID</label>
            <input type="email" name="email_id" id="email_id" required><br>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required><br>
            <input type="submit" value="Login">
        </form>

        <a href="index.html" class="home-button">Back to Home</a> 
        

    </div>

</body>
</html>