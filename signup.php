<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "report";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($checkEmail);
    
    if ($result->num_rows > 0) {
        echo "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, surname, email,username, password) VALUES ('$name', '$surname', '$email','$username' ,'$password')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Signup successful!";
            // Redirect to login page
            // header("Location: login.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
      <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .signup-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input, button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
        }
        button {
            background: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Signup</h2>
        <form action="signup.php" method="post">
            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="text" name="surname" placeholder="Enter your surname" required>
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="username" name="username" placeholder="Enter your username" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <a href="index.php">already signed up? Login</a>
            <button type="submit">Signup</button>
        </form>
    </div>
</body>
</html> 