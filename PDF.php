<?php
session_start();


if (!isset($_SESSION['username'])) {
   
    $_SESSION['username'] = "Guest";
}

require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    private $loggedInName;

   
    function __construct($loggedInName) {
        parent::__construct();
        $this->loggedInName = $loggedInName;
    }

    function Header() {
       
        $this->SetFont('Arial', 'B', 15);

        
        $this->Cell(80);

        
        $this->Cell(30, 10, 'Report for: ' . $this->loggedInName, 0, 0, 'C'); // Border removed

      
        $this->Ln(30);
    }

    function Footer() {
        
        $this->SetY(-15);

   
        $this->SetFont('Arial', 'I', 8);

       
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function CreateTable($header, $data) {
  
        $columnWidths = [40, 30, 30, 30, 30, 30, 30, 30]; 

        
        $this->SetFont('Arial', 'B', 12);

       
        foreach ($header as $index => $col) {
            $this->Cell($columnWidths[$index], 10, $col, 1, 0, 'C');
        }
        $this->Ln();

        
        $this->SetFont('Arial', '', 12);
        foreach ($data as $row) {
            foreach ($row as $index => $cell) {
                $this->Cell($columnWidths[$index], 10, $cell, 1, 0, 'C');
            }
            $this->Ln();
        }
    }
}


$loggedInName = $_SESSION['username'];


$host = 'localhost'; 
$dbname = 'report'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $stmt = $pdo->query("SELECT english, sepedi, mathematics, geography, life_science, physical_science, life_orientation FROM student_details");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $header = ['Subject', 'English', 'Sepedi', 'Mathematics', 'Geography', 'Life Science', 'Physical Science', 'Life Orientation'];

    $tableData = [];
    foreach ($data as $index => $row) {
        $tableData[] = [
            $index + 1, 
            $row['english'],
            $row['sepedi'],
            $row['mathematics'],
            $row['geography'],
            $row['life_science'],
            $row['physical_science'],
            $row['life_orientation']
        ];
    }

    
    $pdf = new PDF($loggedInName);

   
    $pdf->AliasNbPages();

  
    $pdf->SetFont('Arial', '', 12);

  
    $pdf->AddPage();

    
    $pdf->CreateTable($header, $tableData);

    
    $pdf->Output();

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>