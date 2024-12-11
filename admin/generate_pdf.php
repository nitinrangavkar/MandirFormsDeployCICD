<?php

ob_start(); // Start output buffering
include('../db.php'); // Ensure this path is correct
require '../vendor/autoload.php'; // Adjust the path as necessary
header('Content-Type: text/html; charset=utf-8');

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customTempDir = __DIR__ . '/mpdf_temp';
    if (!is_dir($customTempDir)) {
        mkdir($customTempDir, 0777, true);
    }

    // Initialize mPDF
    $mpdf = new Mpdf([
        'tempDir' => $customTempDir,
        'default_font' => 'freeserif'
    ]);

    if (isset($_POST['page_source']) && $_POST['page_source'] === "export_occasion_quota_pdf") {
        try {
            $occasion_id = $_POST['occasion_id'];

            if (!$occasion_id) {
                echo '<script>alert("Please select an occasion for PDF export");</script>';
                echo '<script>window.location.href="quota.php";</script>';
                exit;
            }

            $query = "SELECT id, occasion, batch, occasion_key FROM occasions WHERE id = '$occasion_id'";
            $result = mysqli_query($db, $query);

            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    $result_row = mysqli_fetch_assoc($result);
                    $occasion = $result_row['occasion'];
                    $batch = $result_row['batch'];

                    $query_shakhas = "SELECT unique_code, shakha, branch FROM mandir_branch ORDER BY `branch` ASC";
                    $shakhas = mysqli_query($db, $query_shakhas);

                    // Query to get existing quotas
                    $already_added_quotas_query = "SELECT shakha_id, bandhu_count, bhagini_count FROM occasion_quota WHERE occasion_id = '$occasion_id'";
                    $already_added_quotas = mysqli_query($db, $already_added_quotas_query);

                    // Create an associative array to store the quotas
                    $quotas = [];
                    if ($already_added_quotas && mysqli_num_rows($already_added_quotas) > 0) {
                        while ($quota_row = mysqli_fetch_assoc($already_added_quotas)) {
                            $quotas[$quota_row['shakha_id']] = [
                                'bandhu_count' => $quota_row['bandhu_count'],
                                'bhagini_count' => $quota_row['bhagini_count']
                            ];
                        }
                    }

                    if (mysqli_num_rows($shakhas) > 0) {
                        $headerTitle = 'Quota Report - ' . $occasion;
                        if ($batch) {
                            $headerTitle .= ' B - ' . $batch;
                        }


                        // Add titlex
                        $mpdf->WriteHTML('<h1 style="text-align: center;">'.$headerTitle.'</h1>');
                        $mpdf->Ln(10); // Line break

                        // Custom styles with larger font size and freeserif font
                        $customFontCSS = "
                            <style>
                                body {
                                    font-family: 'freeserif', sans-serif;
                                    font-size: 14pt;
                                }
                                table {
                                    width: 100%;
                                    border-collapse: collapse;
                                }
                                th, td {
                                    font-size: 12pt;
                                    padding: 10px;
                                    border: 1px solid #000;
                                    text-align: left;
                                }
                            </style>
                        ";

                        // HTML table header
                        $htmlContent = $customFontCSS;
                        
                        $htmlContent .= "
                            <table>
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Shakha</th>
                                        <th>Branch</th>
                                        <th>Bandhu</th>
                                        <th>Bhagini</th>
                                    </tr>
                                </thead>
                                <tbody>";

                        $index = 1;
                        while ($data = mysqli_fetch_assoc($shakhas)) {
                            $shakha_id = $data['unique_code'];
                            $bandhu_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bandhu_count'] : "";
                            $bhagini_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bhagini_count'] : "";

                            $htmlContent .= '
                                <tr>
                                    <td>' . $index . '</td>
                                    <td>' . $data['shakha'] . '</td>
                                    <td>' . $data['branch'] . '</td>
                                    <td>' . $bandhu_count . '</td>
                                    <td>' . $bhagini_count . '</td>
                                </tr>
                            ';
                            $index++;
                        }

                        $htmlContent .= "</tbody></table>";

                        // Write the HTML content to the PDF
                        $mpdf->WriteHTML($htmlContent);


                        $currentDate = date('d-M-Y');
                        $fileName = $headerTitle . "_" . $currentDate . ".pdf";

                        ob_end_clean();
                        // Output the PDF to browser or download
                        $mpdf->Output($fileName, 'D');

                        exit;
                    } else {
                        throw new Exception("Error in retrieving shakhas");
                    }
                } else {
                    // Occasion doesn't exist
                    throw new Exception("Occasion doesn't exist.");
                }
            } else {
                throw new Exception("Error: " . mysqli_error($db));
            }
        } catch (Exception $e) {
            // Handle exceptions
            $error_message = $e->getMessage();
        }
    }
}
ob_end_flush();
