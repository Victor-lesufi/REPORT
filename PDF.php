<?php
require('fpdf186/fpdf.php');
include('connection.php'); // Adjust this path if needed

class PDF extends FPDF
{
    private $loggedInName;

    function __construct($loggedInName = "Guest") {
        parent::__construct();
        $this->loggedInName = $loggedInName;
    }

    function Header() {
        $imagePath = 'images/llifi.png.jpg';
        $logoWidth = 30;
        $xCenteredImage = (210 - $logoWidth) / 2;

        if (file_exists($imagePath)) {
            $this->Image($imagePath, $xCenteredImage, $y = 10, $logoWidth);
        }

        $this->SetY(40);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Report for: ' . $this->loggedInName, 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function CreateStudentTable($header, $data) {
        $columnWidths = [35, 18, 18, 20, 20, 22, 22, 22, 13];

        $this->SetFont('Arial', 'B', 7);
        foreach ($header as $index => $col) {
            $this->Cell($columnWidths[$index], 7, $col, 1, 0, 'C');
        }
        $this->Ln();

        $this->SetFont('Arial', '', 7);
        foreach ($data as $row) {
            foreach ($row as $index => $cell) {
                $this->Cell($columnWidths[$index], 6, $cell, 1, 0, 'C');
            }
            $this->Ln();
        }
    }
}

// Ensure student ID is passed via GET
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    die("No student ID specified.");
}
$studentId = trim(urldecode($_GET['id']));

// Database configuration
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student data by ID
$stmt = $conn->prepare("SELECT * FROM student_details WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found with ID: $studentId");
}

$row = $result->fetch_assoc();

// Prepare table data
$header = ['Student Name', 'English', 'Sepedi', 'Mathematics', 'Geography', 'Life Science', 'Physical Science', 'Life Orientation', 'Result'];

$subjects = [
    $row['english'] ?? '-',
    $row['sepedi'] ?? '-',
    $row['mathematics'] ?? '-',
    $row['geography'] ?? '-',
    $row['life_science'] ?? '-',
    $row['physical_science'] ?? '-',
    $row['life_orientation'] ?? '-'
];

$failCount = 0;
foreach ($subjects as $mark) {
    if ($mark !== '-' && (int)$mark < 50) {
        $failCount++;
    }
}

$resultStatus = ($failCount >= 3) ? 'Fail' : 'Pass';

$tableData = [[
    $row['student_name'],
    $row['english'] ?? '-',
    $row['sepedi'] ?? '-',
    $row['mathematics'] ?? '-',
    $row['geography'] ?? '-',
    $row['life_science'] ?? '-',
    $row['physical_science'] ?? '-',
    $row['life_orientation'] ?? '-',
    $resultStatus
]];

// Generate PDF
$pdf = new PDF($row['student_name']);
$pdf->SetMargins(10, 30, 10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->CreateStudentTable($header, $tableData);

// Output PDF
$pdf->Output('I', "Report_{$row['student_name']}.pdf");

exit;
?>