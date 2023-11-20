<?php

include 'includes/db_connection.php';
$feedback_message="";
$error_message="";



if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  
    $email_id = $_POST['email_id'];
    $date_registered = date("Y-m-d");

    $role = "user"; //defalut role user aayega isse 

    // Check if email is already registered
    $stmt = $conn->prepare("SELECT email_id FROM Users WHERE email_id = ?");
    $stmt->bind_param("s", $email_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $error_message = "Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO Users (username, password, role, email_id, date_registered) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $role, $email_id, $date_registered);
        $stmt->execute();
        $feedback_message = "Registration successful!";

    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="assets\user\user_register_styles.css">

    <title>Document</title>
</head>
<body>
    <div class="container">

        <?php 
        if(isset($error_message) && !empty($error_message)) {
            echo "<div class='error'>" . $error_message . "</div>";
        }

        if(isset($feedback_message) && !empty($feedback_message)) {
            echo "<div class='success'>" . $feedback_message . "</div>";
        }
        ?>

        <form action="" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email_id">Email ID</label>
            <input type="email" id="email_id" name="email_id" required><br>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required><br>
            
            <input type="submit" value="Register">
            
        </form>

        <a href="index.html" class="home-button">Back to Home</a> 
    </div>

</body>
</html>

