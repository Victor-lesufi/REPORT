<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f9f9f9;
        }
        .back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back:hover {
            background-color: #0056b3;
        }
        form {
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            max-width: 900px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        label {
            font-weight: bold;
            white-space: nowrap;
        }
        input[type="text"], select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 180px;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 9px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        a button {
            background-color: #6c757d;
        }
        a button:hover {
            background-color: #5a6268;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 900px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        h3 {
            color: #333;
            max-width: 900px;
        }
        p {
            max-width: 900px;
            font-size: 16px;
            color: #555;
        }
    </style>
</head>
<body>

<a class="back" href="teacher.php"><i class="fa-solid fa-arrow-left"></i> Back</a>

<?php
// --- Database Connection ---
include('connection.php'); // Ensure $servername, $username, $password, $dbname are defined

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle Search ---
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$selectedTerm = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';
$terms = ['term1' => 'Term 1', 'term2' => 'Term 2', 'term3' => 'Term 3'];

// --- Search Form ---
echo '
<form method="GET">
    <label for="search">Search Student:</label>
    <input type="text" name="search" id="search" placeholder="Enter student name" value="' . htmlspecialchars($searchTerm) . '" required>

    <label for="term">Select Term:</label>
    <select name="term" id="term" required>
        <option value="" disabled' . (empty($selectedTerm) ? ' selected' : '') . '>-- Select a Term --</option>';
foreach ($terms as $termValue => $termLabel) {
    $selected = ($termValue === $selectedTerm) ? ' selected' : '';
    echo "<option value=\"$termValue\"$selected>$termLabel</option>";
}
echo '
    </select>
    <button type="submit">Search</button>
    <a href="' . $_SERVER['PHP_SELF'] . '"><button type="button">Reset</button></a>
</form>
';

// --- Results ---
if (!empty($searchTerm) && !empty($selectedTerm)) {
    $sql = "SELECT * FROM student_details WHERE student_name LIKE '%$searchTerm%' AND term = '$selectedTerm'";
    $result = $conn->query($sql);

    echo '<h3>Here are the results for <em>' . htmlspecialchars($searchTerm) . '</em> in <em>' . htmlspecialchars($terms[$selectedTerm]) . '</em>:</h3>';

    if ($result->num_rows > 0) {
        echo "<table><tr>";
        // Show all columns except 'term'
        foreach ($result->fetch_fields() as $field) {
            if ($field->name !== 'term') {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
        }
        echo "<th>Action</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                if ($key !== 'term') {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }

            // 'id' is assumed to be the unique identifier
            echo '<td><a href="PDF.php?id=' . urlencode($row['id']) . '" target="_blank">
          <i>view</i>
      </a></td>';
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p style='color:red;'>No records found for student '" . htmlspecialchars($searchTerm) . "' in " . htmlspecialchars($terms[$selectedTerm]) . ".</p>";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['search']) || isset($_GET['term']))) {
    echo "<p>Please enter both student name and select a term to search.</p>";
}

$conn->close();
?>

</body>
</html>
