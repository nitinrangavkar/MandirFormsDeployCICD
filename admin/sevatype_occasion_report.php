<?php
include('../db.php');
$db->set_charset("utf8mb4");

ob_start(); // Start output buffering
require '../vendor/autoload.php'; // Adjust the path as necessary
header('Content-Type: text/html; charset=utf-8');

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


$selectedDate = $_POST['selectedDate'];
$selectedOccasionId = $_POST['selectedOccasionId'];
$selectedOccasionCode = $_POST['selectedOccasionCode'];
$isExport = $_POST['isExport'];
$selectedOccasionNameSevaOccasionReport = $_POST['selectedOccasionNameSevaOccasionReport'];
$query_sevaoccasion_report = "";

if ($selectedOccasionCode == 'PU') {
    $query_sevaoccasion_report = "SELECT 
                               utsavbandhubhagini.form_submission_id,
                                utsavbandhubhagini.id,
                                utsavbandhubhagini.name,
                                utsavbandhubhagini.gender,
                                utsavbandhubhagini.seva,
                                prakarmaster.type,
                                STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY,
                                IFNULL(SUM(CASE WHEN utsavbandhubhagini.gender = 1 THEN 1 END),0) AS Bandhu,
                                IFNULL(SUM(CASE WHEN utsavbandhubhagini.gender = 2 THEN 1 END),0) AS Bhagini,
                                COUNT(1) AS Total
                                FROM form_submissions 
                                INNER JOIN utsavbandhubhagini ON form_submissions.id=utsavbandhubhagini.form_submission_id
                                INNER JOIN prakarmaster ON utsavbandhubhagini.seva=prakarmaster.id
                                WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= utsavbandhubhagini.start_date
                                AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= utsavbandhubhagini.end_date
                                AND occasion_id=$selectedOccasionId
                                AND utsavbandhubhagini.isDeleted = false
                                GROUP BY utsavbandhubhagini.seva;";

$result_sevaoccasion_report = mysqli_query($db, $query_sevaoccasion_report);

$sevaOccasionTable;

if(mysqli_num_rows($result_sevaoccasion_report) > 0) {
    $sevaOccasionTable .= '<table class="tableSevaTypeOccasionReport" border="1" width="100%">';
    $sevaOccasionTable .= '<tr><th></th><th style="text-align:center;">Bandhu</th><th style="text-align:center;">Bhagini</th><th style="text-align:center;">Total</th></tr>';
    while($row_sevaoccasion = mysqli_fetch_array($result_sevaoccasion_report))
    {
        $sevaOccasionTable .= '<tr><td style="text-align:center;">' . $row_sevaoccasion['type'] . '</td><td style="text-align:center;">' . $row_sevaoccasion['Bandhu'] . '</td><td style="text-align:center;">' . $row_sevaoccasion['Bhagini'] . '</td><td style="text-align:center;">' . $row_sevaoccasion['Total'] . '</td></tr>';
    }
    $sevaOccasionTable .= '</table>';
} else {
    $sevaOccasionTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
}
} else {
    // $query_sevaoccasion_report = "SELECT 
    //                             form_submissions.id,
    //                             form_submissions.brothers,
    //                             form_submissions.sisters,
    //                             'Upasana' AS type,
    //                             SUM(upasana_brothers) AS Bandhu,
    //                             SUM(upasana_sisters) AS Bhagini,
    //                             SUM(upasana_brothers + upasana_sisters) AS Total,
    //                             STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
    //                             FROM `form_submissions` 
    //                             WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= form_submissions.start_date
    //                             AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= form_submissions.end_date
    //                             AND occasion_id=$selectedOccasionId
    //                             UNION ALL
    //                             SELECT 
    //                             form_submissions.id,
    //                             form_submissions.brothers,
    //                             form_submissions.sisters,
    //                             'Seva' AS type,
    //                             SUM(seva_brothers) AS Bandhu,
    //                             SUM(seva_sisters) AS Bhagini,
    //                             SUM(seva_brothers + seva_sisters) AS Total,
    //                             STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
    //                             FROM `form_submissions` 
    //                             WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= form_submissions.start_date
    //                             AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= form_submissions.end_date
    //                             AND occasion_id=$selectedOccasionId
    //                             UNION ALL
    //                             SELECT 
    //                             form_submissions.id,
    //                             form_submissions.brothers,
    //                             form_submissions.sisters,
    //                             'Anugraha' AS type,
    //                             SUM(anugraha_brothers) AS Bandhu,
    //                             SUM(anugraha_sisters) AS Bhagini,
    //                             SUM(anugraha_brothers + anugraha_sisters) AS Total,
    //                             STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
    //                             FROM `form_submissions` 
    //                             WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= form_submissions.start_date
    //                             AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= form_submissions.end_date
    //                             AND occasion_id=$selectedOccasionId
    //                             UNION ALL
    //                             SELECT 
    //                             form_submissions.id,
    //                             form_submissions.brothers,
    //                             form_submissions.sisters,
    //                             'Overlap' AS type,
    //                             SUM(overlap_brothers) AS Bandhu,
    //                             SUM(overlap_sisters) AS Bhagini,
    //                             SUM(overlap_brothers + overlap_sisters) AS Total,
    //                             STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
    //                             FROM `form_submissions` 
    //                             WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= form_submissions.start_date
    //                             AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= form_submissions.end_date
    //                             AND occasion_id=$selectedOccasionId";

    $query_sevaoccasion_report = "SELECT 
                                form_submissions.id,
                                form_submissions.brothers,
                                form_submissions.sisters,
                                SUM(form_arrival_time.upasana_bandhu) AS UpasanaBandhu,
                                SUM(form_arrival_time.upasana_bhagini) AS UpasanaBhagini,
                                SUM(form_arrival_time.upasana_bandhu+form_arrival_time.upasana_bhagini) AS UpasanaTotal,
                                SUM(form_arrival_time.seva_bandhu) AS SevaBandhu,
                                SUM(form_arrival_time.seva_bhagini) AS SevaBhagini,
                                SUM(form_arrival_time.seva_bandhu+form_arrival_time.seva_bhagini) AS SevaTotal,
                                SUM(form_arrival_time.anugraha_bandhu) AS AnugrahaBandhu,
                                SUM(form_arrival_time.anugraha_bhagini) AS AnugrahaBhagini,
                                SUM(form_arrival_time.anugraha_bandhu+form_arrival_time.anugraha_bhagini) AS AnugrahaTotal,
                                SUM(form_arrival_time.overlap_bandhu) AS OverlapBandhu,
                                SUM(form_arrival_time.overlap_bhagini) AS OverlapBhagini,
                                SUM(form_arrival_time.overlap_bandhu+form_arrival_time.overlap_bhagini) AS OverlapTotal,
                                STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
                                FROM `form_submissions` 
                                INNER JOIN form_arrival_time ON form_submissions.id=form_arrival_time.form_submission_id
                                WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= form_submissions.start_date
                                AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= form_submissions.end_date
                                AND occasion_id=$selectedOccasionId";

    $result_sevaoccasion_report = mysqli_query($db, $query_sevaoccasion_report);

    $sevaOccasionTable;

    if(mysqli_num_rows($result_sevaoccasion_report) > 0) {    
        while($row_sevaoccasion = mysqli_fetch_array($result_sevaoccasion_report))
        {
            if ($row_sevaoccasion['id'] != null)
            {
                $sevaOccasionTable .= '<table class="table">';
                $sevaOccasionTable .= '<tr><th></th><th>Bandhu</th><th>Bhagini</th><th>Total</th></tr>';
                $sevaOccasionTable .= '<tr><td>Upasana</td><td>' . $row_sevaoccasion['UpasanaBandhu'] . '</td><td>' . $row_sevaoccasion['UpasanaBhagini'] . '</td><td>' . $row_sevaoccasion['UpasanaTotal'] . '</td></tr>';
                $sevaOccasionTable .= '<tr><td>Seva</td><td>' . $row_sevaoccasion['SevaBandhu'] . '</td><td>' . $row_sevaoccasion['SevaBhagini'] . '</td><td>' . $row_sevaoccasion['SevaTotal'] . '</td></tr>';
                $sevaOccasionTable .= '<tr><td>Anugraha</td><td>' . $row_sevaoccasion['AnugrahaBandhu'] . '</td><td>' . $row_sevaoccasion['AnugrahaBhagini'] . '</td><td>' . $row_sevaoccasion['AnugrahaTotal'] . '</td></tr>';
                $sevaOccasionTable .= '<tr><td>Overlap</td><td>' . $row_sevaoccasion['OverlapBandhu'] . '</td><td>' . $row_sevaoccasion['OverlapBhagini'] . '</td><td>' . $row_sevaoccasion['OverlapTotal'] . '</td></tr>';
                $sevaOccasionTable .= '</table>';
            }
            else {
                $sevaOccasionTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
            }
        }
    } else {
        $sevaOccasionTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
    }
}


if ($isExport == "true") {
    $finalHtml = "<div style='text-align:center; font-weight:bold;'>Seva Type Occasion Report - " . $selectedDate . " - " . $selectedOccasionNameSevaOccasionReport . "</div><div>";
    $finalHtml .= $sevaOccasionTable . "</div>";

    $customTempDir = __DIR__ . '/mpdf_temp';
    if (!is_dir($customTempDir)) {
        mkdir($customTempDir, 0777, true);
    }

    // Initialize mPDF
    $mpdf = new Mpdf([
        'tempDir' => $customTempDir,
        'default_font' => 'freeserif'
    ]);


    // Write the HTML content to the PDF
    $mpdf->WriteHTML($finalHtml);


    $fileName = "Seva Type Occasion Report - " . $selectedDate . " - " . $selectedOccasionNameSevaOccasionReport . ".pdf";
    print($fileName);

    ob_end_clean();
    // Output the PDF to browser or download
    $mpdf->Output($fileName, 'D');

    exit;
} else {

echo $sevaOccasionTable;
}

ob_end_flush();
?>
