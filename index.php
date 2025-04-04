<?php
session_start();

// Handle form submission via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "report";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
    }

    // Get raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract data
    $username = mysqli_real_escape_string($conn, $data['username']);
    $password = $data['password'];
    $role = mysqli_real_escape_string($conn, $data['role']);

    // Query to check username
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Store the role in the session

            // Redirect based on the role
            if ($role === 'class_teacher') {
                echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
                exit();
            } elseif ($role === 'teacher') {
                echo json_encode(['success' => true, 'redirect' => 'teacher.php']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username!']);
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
        }

        .login-container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            min-height: 20px; /* Ensure space for error messages */
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="error"></div>
    <h2 id="loginTitle">Login</h2>
    <form id="loginForm" method="post">
        <!-- Role Selection Dropdown -->
        <label for="role">Select Role:</label>
        <select id="role" name="role" required>
            <option value="" disabled selected>-- Select Role --</option>
            <option value="teacher">Teacher</option>
            <option value="class_teacher">Class Teacher</option>
        </select>

        <!-- Username Input -->
        <input type="text" id="username" name="username" placeholder="Enter your username" required>

        <!-- Password Input -->
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <!-- Signup Link -->
        <a href="signup.php">Don't have an account? Signup</a>

        <!-- Login Button -->
        <button type="submit">Login</button>
    </form>
</div>

<script>
    // Add event listener to the dropdown
    document.getElementById('role').addEventListener('change', function () {
        // Get the selected role
        const selectedRole = this.value;

        // Get the title element
        const loginTitle = document.getElementById('loginTitle');

        // Update the title based on the selected role
        if (selectedRole === 'teacher') {
            loginTitle.textContent = 'Logging in as a Teacher';
        } else if (selectedRole === 'class_teacher') {
            loginTitle.textContent = 'Logging in as a Class Teacher';
        } else {
            loginTitle.textContent = 'Login'; // Default title
        }
    });

    // Handle form submission using AJAX
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        // Clear previous error messages
        document.querySelector('.error').textContent = '';

        // Collect form data
        const formData = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };

        // Send data to the server using Fetch API
        fetch('', { // Submit to the same file
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // Identify AJAX request
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                // Redirect to the appropriate page
                window.location.href = data.redirect;
            } else {
                // Display error message in the .error div
                document.querySelector('.error').textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('.error').textContent = 'An unexpected error occurred. Please try again.';
        });
    });
</script>

</body>
</html>