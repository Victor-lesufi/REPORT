<?php
// Start output buffering at the beginning
ob_start();
// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Log script execution
error_log("Script started: " . date('Y-m-d H:i:s'));
// Include the FPDF library
require('fpdf186/fpdf.php'); // Ensure the path is correct
// Database connection details
include 'connection.php'; // Include your database connection file
// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}
// Initialize variables
$searchTerm = "";
$result = null;
$pdfError = "";
// Handle search and export functionality
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search'])) {
        // Search students
        $searchTerm = trim($_POST['search']);
        if (!empty($searchTerm)) {
            $sql = "SELECT student_name, english, sepedi, mathematics, life_science, 
                           physical_science, life_orientation, geography 
                    FROM student_details 
                    WHERE student_name LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
        }
    } elseif (isset($_POST['export_pdf']) && !empty($_POST['search'])) {
        // Export PDF
        $searchTerm = trim($_POST['search']);
        try {
            error_log("Starting PDF generation for: " . $searchTerm);
            $sql = "SELECT student_name, english, sepedi, mathematics, life_science, 
                           physical_science, life_orientation, geography 
                    FROM student_details 
                    WHERE student_name LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchTerm = "%" . $searchTerm . "%";
            $stmt->bind_param("s", $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Clean all output buffers before sending PDF
                if (ob_get_level()) {
                    ob_end_clean();
                }
                // Create PDF
                $pdf = new FPDF();
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 16);
                $pdf->Cell(0, 10, 'Student Details Report', 0, 1, 'C');
                $pdf->Ln(10);
                // Table headers
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(50, 10, 'Student Name', 1, 0, 'C');
                $pdf->Cell(20, 10, 'English', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Sepedi', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Mathematics', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Life Science', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Physical Science', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Life Orientation', 1, 0, 'C');
                $pdf->Cell(20, 10, 'Geography', 1, 1, 'C');
                // Table data
                $pdf->SetFont('Arial', '', 12);
                while ($row = $result->fetch_assoc()) {
                    $pdf->Cell(50, 10, $row['student_name'], 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['english']) && $row['english'] !== '' ? $row['english'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['sepedi']) && $row['sepedi'] !== '' ? $row['sepedi'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['mathematics']) && $row['mathematics'] !== '' ? $row['mathematics'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['life_science']) && $row['life_science'] !== '' ? $row['life_science'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['physical_science']) && $row['physical_science'] !== '' ? $row['physical_science'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['life_orientation']) && $row['life_orientation'] !== '' ? $row['life_orientation'] : '-', 1, 0, 'C');
                    $pdf->Cell(20, 10, isset($row['geography']) && $row['geography'] !== '' ? $row['geography'] : '-', 1, 1, 'C');
                }
                // Send PDF headers and output
                header('Content-Type: application/pdf');
                // Uncomment the following line to force download instead of inline view
                // header('Content-Disposition: attachment; filename="Student_Details_Report.pdf"');
                $pdf->Output();
                error_log("PDF output completed");
                exit();
            } else {
                error_log("No results found for search: " . $searchTerm);
                $pdfError = "No records found for: " . htmlspecialchars($searchTerm);
            }
        } catch (Exception $e) {
            error_log("PDF Error: " . $e->getMessage());
            $pdfError = "Error generating PDF: " . $e->getMessage();
        }
    } elseif (isset($_POST['test_pdf'])) {
        // Simple test PDF
        try {
            error_log("Generating test PDF");
            if (ob_get_level()) {
                ob_end_clean();
            }
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(40, 10, 'Test PDF');
            header('Content-Type: application/pdf');
            // Uncomment the following line to force download instead of inline view
            // header('Content-Disposition: attachment; filename="test.pdf"');
            $pdf->Output();
            error_log("Test PDF sent");
            exit;
        } catch (Exception $e) {
            error_log("Test PDF Error: " . $e->getMessage());
            $pdfError = "Error generating test PDF: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Student Details</title>
   <link rel="stylesheet" href="CSS/details.css">
</head>
<body>
    <a href="teacher.php">Home</a>
    <h2>Search Student Details</h2>
    <!-- Search Form -->
    <form method="POST" action="">
        <input type="text" name="search" placeholder="Enter student name..." 
               value="<?php echo htmlspecialchars($searchTerm); ?>" required>
        <input type="submit" value="Search">
    </form>
    <!-- <button><a href="PDF.php">PDF</a></button> -->
    <!-- Test PDF Button -->
    <form method="POST" action="">
        <input type="hidden" name="test_pdf" value="1">
        <!-- <input type="submit" value="Generate Test PDF"> -->
    </form>
    <?php if (isset($pdfError)): ?>
        <p class="error"><?php echo $pdfError; ?></p>
    <?php endif; ?>
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($result)): ?>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>English</th>
                    <th>Sepedi</th>
                    <th>Mathematics</th>
                    <th>Life Science</th>
                    <th>Physical Science</th>
                    <th>Life Orientation</th>
                    <th>Geography</th>
                    <th>VIEW PDF</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo isset($row['english']) && $row['english'] !== '' ? htmlspecialchars($row['english']) : '-'; ?></td>
                            <td><?php echo isset($row['sepedi']) && $row['sepedi'] !== '' ? htmlspecialchars($row['sepedi']) : '-'; ?></td>
                            <td><?php echo isset($row['mathematics']) && $row['mathematics'] !== '' ? htmlspecialchars($row['mathematics']) : '-'; ?></td>
                            <td><?php echo isset($row['life_science']) && $row['life_science'] !== '' ? htmlspecialchars($row['life_science']) : '-'; ?></td>
                            <td><?php echo isset($row['physical_science']) && $row['physical_science'] !== '' ? htmlspecialchars($row['physical_science']) : '-'; ?></td>
                            <td><?php echo isset($row['life_orientation']) && $row['life_orientation'] !== '' ? htmlspecialchars($row['life_orientation']) : '-'; ?></td>
                            <td><?php echo isset($row['geography']) && $row['geography'] !== '' ? htmlspecialchars($row['geography']) : '-'; ?></td>
                     <td><button><a href="PDF.php?student=<?php echo urlencode($row['student_name']); ?>">PDF</a></button></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No records found for: <?php echo htmlspecialchars($searchTerm); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Export to PDF Form -->
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <form method="POST" action="">
                <input type="hidden" name="export_pdf" value="1">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <!-- <input type="submit" value="Export to PDF"> -->
            </form>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
<?php
// Close connection
$conn->close();
?>