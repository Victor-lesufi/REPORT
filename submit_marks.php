<?php
// Start session (optional)
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $marks = floatval($_POST['marks']); // Convert marks to a decimal number

    // Check if the student already exists in the database
    $sql_check = "SELECT * FROM student_details WHERE student_name = '$student_name'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Update the existing record 
        $sql_update = "UPDATE student_details 
                       SET $subject = $marks 
                       WHERE student_name = '$student_name'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Marks updated successfully!";
        } else {
            echo "Error updating marks: " . $conn->error;
        }
    } else {
        // Insert a new record
        $sql_insert = "INSERT INTO student_details (student_name, $subject) 
                       VALUES ('$student_name', $marks)";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Marks inserted successfully!";
        } else {
            echo "Error inserting marks: " . $conn->error;
        }
    }
}

$conn->close();
?>