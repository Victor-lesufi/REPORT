<?php

session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "report";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $marks = floatval($_POST['marks']); 

    
    $sql_check = "SELECT * FROM student_details WHERE student_name = '$student_name'";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
       
        $sql_update = "UPDATE student_details 
                       SET $subject = $marks 
                       WHERE student_name = '$student_name'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Marks updated successfully!";
        } else {
            echo "Error updating marks: " . $conn->error;
        }
    } else {
       
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