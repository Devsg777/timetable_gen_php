<?php
require_once '../config/database.php';
require_once '../models/Timetable.php';
require '../../vendor/autoload.php'; // Ensure PhpSpreadsheet & TCPDF are installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use TCPDF;

$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);

$combinations = $timetable->getAllCombinations(); // Fetch all combinations
$time_slots = ['10:00 - 11:00','11:00 - 12:00','12:00 - 13:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00', '04:00 - 05:00', '05:00 - 06:00'];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

$exportType = $_GET['type'] ?? 'excel'; // Default to Excel export

if ($exportType == 'excel') {
    exportExcel($combinations, $days, $time_slots, $timetable);
} elseif ($exportType == 'pdf') {
    exportPDF($combinations, $days, $time_slots, $timetable);
} else {
    die("Invalid export type.");
}

// ✅ **Function to Export Timetable to Excel**
function exportExcel($combinations, $days, $time_slots, $timetable)
{
    ob_end_clean();
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $row = 1;

    foreach ($combinations as $combination) {
        $data = $timetable->getTimetableByCombination($combination['combination_id']);

        // Add combination name as a header
        $sheet->setCellValue('A' . $row, $combination['name'] . " (" . $combination['department'] . " - Semester " . $combination['semester'] . ")");
        $sheet->mergeCells("A$row:" . chr(65 + count($time_slots)) . "$row");
        $sheet->getStyle("A$row")->getFont()->setSize(16);
        $sheet->getStyle("A$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A$row")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A$row")->getAlignment()->setShrinkToFit(true);
        $sheet->getStyle("A$row")->getAlignment()->setWrapText(true);
        $row++;

        // Table Header
        $sheet->setCellValue('A' . $row, 'Day');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $col = 'B';
        foreach ($time_slots as $slot) {
            $sheet->setCellValue($col . $row, $slot);
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle("A$row")->getAlignment()->setShrinkToFit(true);
            $col++;
        }
        $row++;

        // Fill Data
        foreach ($days as $day) {
            $sheet->setCellValue('A' . $row, $day);
            $col = 'B';
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row)->getAlignment()->setShrinkToFit(true);
            $sheet->getColumnDimension('A')->setAutoSize(true);

            foreach ($time_slots as $slot) {
                $entry = $data[$day][$slot] ?? ['subject' => '', 'teacher' => '', 'classroom' => ''];
                $sheet->setCellValue($col . $row, $entry['subject'] . "\n" . $entry['teacher'] . "\n" . $entry['classroom']);
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($col . $row)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($col . $row)->getAlignment()->setShrinkToFit(true);
                $sheet->getStyle($col . $row)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }
            $row++;
        }

        $row++; // Add spacing
    }

    // Set Headers for Download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Timetable.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    exit();
}

// ✅ **Function to Export Timetable to PDF**
function exportPDF($combinations, $days, $time_slots, $timetable)
{
    ob_end_clean();

    $pdf = new TCPDF();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // ** Set font & title **
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(190, 10, 'Timetable', 0, 1, 'C');

    foreach ($combinations as $combination) {
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(190, 8, $combination['name'] . " (" . $combination['department'] . " - Semester " . $combination['semester'] . ")", 0, 1, 'C');

        // ** Calculate dynamic column widths **
        $pageWidth = 190; // A4 page width without margins
        $dayColWidth = 20; // Reduced "Day" column width
        $numSlots = count($time_slots);
        $slotColWidth = ($pageWidth - $dayColWidth) / $numSlots; // Dynamic width for time slots

        // ** Table Header (Bold & Centered) **
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(200, 200, 200); // Light gray background for header
        $pdf->Cell($dayColWidth, 10, 'Day', 1, 0, 'C', true);
        foreach ($time_slots as $slot) {
            $pdf->Cell($slotColWidth, 10, $slot, 1, 0, 'C', true);
        }
        $pdf->Ln();

        // ** Table Data **
        $pdf->SetFont('helvetica', '', 9);
        $data = $timetable->getTimetableByCombination($combination['combination_id']);

        foreach ($days as $day) {
            // ** Calculate max row height for this day **
            $maxHeight = 10; // Default minimum row height
            $rowData = [];

            foreach ($time_slots as $slot) {
                $entry = $data[$day][$slot] ?? ['subject' => '', 'teacher' => '', 'classroom' => ''];
                $text = $entry['subject'] . "\n" . $entry['teacher'] . "\n" . $entry['classroom'];

                // Get the number of lines required for the text
                $numLines = $pdf->getNumLines($text, $slotColWidth);
                $cellHeight = max(10, $numLines * 5); // 5 per line height
                $maxHeight = max($maxHeight, $cellHeight);

                $rowData[] = $text; // Store text for later use
            }

            // ** Print Day column **
            $pdf->Cell($dayColWidth, $maxHeight, $day, 1, 0, 'C', true);

            // ** Print Time Slot Data with MultiCell**
            foreach ($rowData as $text) {
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell($slotColWidth, $maxHeight, $text, 1, 'C', false);
                $pdf->SetXY($x + $slotColWidth, $y);
            }

            $pdf->Ln();
        }
    }

    // ** Output PDF **
    $pdf->Output('Timetable.pdf', 'D');
    exit();
}



?>
