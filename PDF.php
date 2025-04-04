<?php
session_start();

// Ensure the session contains the logged-in user's name
if (!isset($_SESSION['username'])) {
    // If no session username is set, assign a default value (e.g., "Guest")
    $_SESSION['username'] = "Guest";
}

require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    // Add a property to store the logged-in user's name
    private $loggedInName;

    // Constructor to accept the logged-in user's name
    function __construct($loggedInName) {
        parent::__construct();
        $this->loggedInName = $loggedInName;
    }

    function Header() {
        // Set font for the title
        $this->SetFont('Arial', 'B', 15);

        // Add some spacing
        $this->Cell(80);

        // Use the logged-in user's name in the title (no border)
        $this->Cell(30, 10, 'Report for: ' . $this->loggedInName, 0, 0, 'C'); // Border removed

        // Line break
        $this->Ln(30);
    }

    function Footer() {
        // Position at 1.5 cm from the bottom
        $this->SetY(-15);

        // Set font for the footer
        $this->SetFont('Arial', 'I', 8);

        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Function to create a table
    function CreateTable($header, $data) {
        // Column widths
        $columnWidths = [40, 30, 30, 30, 30, 30, 30, 30]; // Adjust column widths as needed

        // Add a bold font for the table header
        $this->SetFont('Arial', 'B', 12);

        // Header row
        foreach ($header as $index => $col) {
            $this->Cell($columnWidths[$index], 10, $col, 1, 0, 'C');
        }
        $this->Ln();

        // Data rows
        $this->SetFont('Arial', '', 12); // Regular font for data rows
        foreach ($data as $row) {
            foreach ($row as $index => $cell) {
                $this->Cell($columnWidths[$index], 10, $cell, 1, 0, 'C');
            }
            $this->Ln();
        }
    }
}

// Retrieve the logged-in user's name from the session
$loggedInName = $_SESSION['username'];

// Database connection
$host = 'localhost'; // Replace with your database host
$dbname = 'report'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from the student_details table
    $stmt = $pdo->query("SELECT english, sepedi, mathematics, geography, life_science, physical_science, life_orientation FROM student_details");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the header for the table
    $header = ['Subject', 'English', 'Sepedi', 'Mathematics', 'Geography', 'Life Science', 'Physical Science', 'Life Orientation'];

    // Transform the data into a format suitable for the table
    $tableData = [];
    foreach ($data as $index => $row) {
        $tableData[] = [
            $index + 1, // Row number
            $row['english'],
            $row['sepedi'],
            $row['mathematics'],
            $row['geography'],
            $row['life_science'],
            $row['physical_science'],
            $row['life_orientation']
        ];
    }

    // Create a new PDF instance with the logged-in user's name
    $pdf = new PDF($loggedInName);

    // Alias for total number of pages
    $pdf->AliasNbPages();

    // Set default font
    $pdf->SetFont('Arial', '', 12);

    // Add a page
    $pdf->AddPage();

    // Create the table
    $pdf->CreateTable($header, $tableData);

    // Output the PDF
    $pdf->Output();

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>