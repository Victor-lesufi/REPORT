<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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

// Initialize the search term
$searchTerm = "";

// Flag to determine if a search has been performed
$isSearchPerformed = false;

// If form is submitted with a search term
if (isset($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search']);
    $isSearchPerformed = true;
}

// Fetch student records with optional search filter
$sql = "SELECT * FROM students WHERE name LIKE '%$searchTerm%' OR surname LIKE '%$searchTerm%' OR id_number LIKE '%$searchTerm%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="CSS/dashboard.css">
    <style>
        /* Hide the table by default */
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <!-- <button><a href ="details.php">view details</a></button> -->
    <!-- Welcome message on a separate line -->
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    
    <br>

    <!-- Search Form -->
    <form method="POST" action="">
        <input type="text" name="search" placeholder="Search by name, surname, or ID" value="<?php echo $searchTerm; ?>">
        <button type="submit">Search</button>
    </form>

    <br>

    <!-- Student Records heading -->
    

    <?php
    // Only show the table if a search has been performed and there are results
    if ($isSearchPerformed) {
        if ($result->num_rows > 0) {
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Name</th>';
            echo '<th>Surname</th>';
            echo '<th>ID Number</th>';
            echo '<th>Gender</th>';
            echo '</tr>';

            while ($row = $result->fetch_assoc()) {
                echo "records found";
                echo "<tr>";
               
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['surname'] . "</td>";
                echo "<td>" . $row['id_number'] . "</td>";
                echo "<td>" . $row['gender'] . "</td>";
                echo "</tr>";
            }

            echo '</table>';
        } else {
            // Display a message if no results are found
            echo '<p>No students found.</p>';
        }
    } else {
        // Initially hide the table if no search has been performed
        echo '<p class="hidden">No search performed yet.</p>';
    }
    ?>

    <br>
  
    <div class="container">
    <button class="btn">  <a href ="details.php">view details</a> </button>
    <button class="btn"><a href="teacher.php">teacher</a></button>
    <button class="btn">  <a href="logout.php">logout</a> </button>
   
   
</div>


<?php
$conn->close();
?>

</body>
</html>