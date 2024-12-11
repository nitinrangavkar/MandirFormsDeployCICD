<?php
include('../db.php'); // Ensure this path is correct
require '../vendor/autoload.php'; // Include PhpSpreadsheet via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db->set_charset("utf8mb4");
$message = "";

if (isset($_POST['export_occasions'])) {
    try {
        $occasion_id = $_POST['occasion_input'];

        $query = "SELECT id, occasion, batch, occasion_key FROM occasions WHERE id = '$occasion_id'";
        $result = mysqli_query($db, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $result_row = mysqli_fetch_assoc($result);
                $occasion = $result_row['occasion'];
                $batch = $result_row['batch'];
                $occasion_key = $result_row['occasion_key'];
                
                $query_shakhas = "SELECT unique_code, shakha FROM mandir_branch";
                $shakhas = mysqli_query($db, $query_shakhas);

                if (mysqli_num_rows($shakhas) > 0) {
                    
                    // Create a new spreadsheet
                    $spreadsheet = new Spreadsheet();

                    error_log("Hello --> ".$spreadsheet);
                    $sheet = $spreadsheet->getActiveSheet();

                    error_log("Hello --> ".$batch);

                    if ($batch) {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Batch');
                        $sheet->setCellValue('D1', 'Occasion Code');
                        $sheet->setCellValue('E1', 'Shakha');
                        $sheet->setCellValue('F1', 'Shakha Code');
                        $sheet->setCellValue('G1', 'Bandhu Quota');
                        $sheet->setCellValue('H1', 'Bhagini Quota');

                        $row = 2; // Data starts from the second row
                        $index = 1;
                        while ($data = $result->fetch_assoc()) {
                            // Fill the spreadsheet with data
                            $sheet->setCellValue('A' . $row, $index);
                            $sheet->setCellValue('B' . $row, $occasion); // This cell will be locked
                            $sheet->setCellValue('C' . $row, $batch);
                            $sheet->setCellValue('D' . $row, $occasion_key);
                            $sheet->setCellValue('E' . $row, $data['shakha']);
                            $sheet->setCellValue('F' . $row, $data['unique_code']);
                            $sheet->setCellValue('G' . $row, 0);
                            $sheet->setCellValue('H' . $row, 0);
                            $row++;
                            $index++;
                        }
                    } else {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Occasion Code');
                        $sheet->setCellValue('D1', 'Shakha');
                        $sheet->setCellValue('E1', 'Shakha Code');
                        $sheet->setCellValue('F1', 'Bandhu Quota');
                        $sheet->setCellValue('G1', 'Bhagini Quota');

                        $row = 2; // Data starts from the second row
                        $index = 1;
                        while ($data = $result->fetch_assoc()) {
                            // Fill the spreadsheet with data
                            $sheet->setCellValue('A' . $row, $index);
                            $sheet->setCellValue('B' . $row, $occasion); // This cell will be locked
                            $sheet->setCellValue('C' . $row, $occasion_key);
                            $sheet->setCellValue('D' . $row, $data['shakha']);
                            $sheet->setCellValue('E' . $row, $data['unique_code']);
                            $sheet->setCellValue('F' . $row, 0);
                            $sheet->setCellValue('G' . $row, 0);
                            $row++;
                            $index++;
                        }
                    }

                    // Write the spreadsheet to a file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = 'data-export.xlsx';

                    // Send file to the browser for download
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="' . $fileName . '"');
                    header('Cache-Control: max-age=0');
                    $writer->save('php://output');
                    exit;
                    
                } else {
                    throw new Exception("Error in retrieving shakhas");
                }

            } else {
                // Ocassion doesn't exist
                throw new Exception("Ocassion doesn't exist.");
            }
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
}

?>