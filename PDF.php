<?php
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    private $loggedInName;

    function __construct($loggedInName = "Guest") {
        parent::__construct();
        $this->loggedInName = $loggedInName;
    }

    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Report for: ' . $this->loggedInName, 0, 1, 'C');
        $this->Ln(5);
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


if (!isset($_GET['student']) || empty(trim($_GET['student']))) {
    die("No student specified.");
}
$studentName = trim(urldecode($_GET['student']));


$host = 'localhost';
$dbname = 'report';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->prepare("SELECT student_name, english, sepedi, mathematics, geography, life_science, physical_science, life_orientation FROM student_details WHERE student_name = ?");
    $stmt->execute([$studentName]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($data) === 0) {
        die("Student not found: $studentName");
    }

    
    $header = ['Student Name', 'English', 'Sepedi', 'Mathematics', 'Geography', 'Life Science', 'Physical Science', 'Life Orientation', 'Result'];
    $tableData = [];

    foreach ($data as $row) {
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

        $result = ($failCount >= 3) ? 'Fail' : 'Pass';

        $tableData[] = array_merge([$row['student_name']], $subjects, [$result]);
    }

    
    $pdf = new PDF($studentName);
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->CreateStudentTable($header, $tableData);

    
    $pdf->Output('I', "Report_$studentName.pdf");  

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>