<?php
session_start();

include('connection.php'); // Include your database connection file

$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $term = mysqli_real_escape_string($conn, $_POST['term']);
    $marks = floatval($_POST['marks']);

    // Check if student already exists for the selected term
    $sql_check = "SELECT * FROM student_details WHERE student_name = '$student_name' AND term = '$term'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Update marks for the subject in the existing term record
        $sql_update = "UPDATE student_details 
                       SET $subject = $marks 
                       WHERE student_name = '$student_name' AND term = '$term'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Marks updated successfully!";
        } else {
            echo "Error updating marks: " . $conn->error;
        }
    } else {
        // Insert new student record with term and subject marks
        $sql_insert = "INSERT INTO student_details (student_name, term, $subject) 
                       VALUES ('$student_name', '$term', $marks)";

        if ($conn->query($sql_insert) === TRUE) {
            echo "Marks inserted successfully!";
        } else {
            echo "Error inserting marks: " . $conn->error;
        }
    }
}

$conn->close();
?>
