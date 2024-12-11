<?php
include('../db.php');
$db->set_charset("utf8mb4");

ob_start(); // Start output buffering
require '../vendor/autoload.php'; // Adjust the path as necessary
header('Content-Type: text/html; charset=utf-8');

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


$selectedDate = $_POST['selectedDate3'];
$isExport = $_POST['isExport'];

// $query_daily_report = "CREATE TEMPORARY TABLE tempDayTimes(daytime VARCHAR(10) NOT NULL, daytimeid INT(3) NOT NULL);
//                         INSERT INTO tempDayTimes(daytime,daytimeid) VALUES('Morning',1),('Afternoon',2),('Evening',3);
//                         SELECT 
//                         SUM(brothers) AS Bandhu,
//                         SUM(sisters) AS Bhagini,
//                         SUM(total_people) AS Total,
//                         time_of_arrival,
//                         STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
//                         FROM `form_submissions` 
//                         LEFT OUTER JOIN tempDayTimes ON form_submissions.time_of_arrival=tempDayTimes.daytime
//                         WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= start_date AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= end_date 
//                         GROUP BY TODAY,time_of_arrival;
//                         DROP TEMPORARY TABLE tempDayTimes;";

$query_daily_report = "SELECT 
    CASE 
        WHEN form_submissions.branch = 'Belagavi Shriharimandir' THEN CONCAT(form_submissions.branch, ' - ', form_submissions.location)
        ELSE form_submissions.branch
    END AS branch_with_location,
    
    CASE 
        WHEN form_submissions.branch = 'Belagavi Shriharimandir' THEN CONCAT(mandir_branch.shakha, ' - ', form_submissions.location)
        ELSE mandir_branch.shakha
    END AS shakha_with_location,
    mandir_branch.shakha,
    SUM(upasana_bandhu + seva_bandhu + anugraha_bandhu + overlap_bandhu) AS Bandhu,
    SUM(upasana_bhagini + seva_bhagini + anugraha_bhagini + overlap_bhagini) AS Bhagini,
    SUM(upasana_bandhu) AS upasana_bandhu,
    SUM(upasana_bhagini) AS upasana_bhagini,
    SUM(seva_bandhu) AS seva_bandhu,
    SUM(seva_bhagini) AS seva_bhagini,
    SUM(anugraha_bandhu) AS anugraha_bandhu,
    SUM(anugraha_bhagini) AS anugraha_bhagini,
    SUM(overlap_bandhu) AS overlap_bandhu,
    SUM(overlap_bhagini) AS overlap_bhagini,
    arrival_time.arrival AS time_of_arrival,
    STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
FROM 
    form_submissions 
INNER JOIN 
    form_arrival_time 
    ON form_submissions.id = form_arrival_time.form_submission_id
INNER JOIN 
    arrival_time 
    ON form_arrival_time.time_of_arrival = arrival_time.id
INNER JOIN 
    mandir_branch
    ON form_submissions.branch = mandir_branch.branch
WHERE 
    STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= start_date 
    AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= end_date
GROUP BY 
    branch_with_location, arrival_time.id
HAVING 
    Bandhu > 0 OR Bhagini > 0

UNION 

SELECT 
    CASE 
        WHEN form_submissions.branch = 'Belagavi Shriharimandir' THEN CONCAT(form_submissions.branch, ' - ', form_submissions.location)
        ELSE form_submissions.branch
    END AS branch_with_location,
    
    CASE 
        WHEN form_submissions.branch = 'Belagavi Shriharimandir' THEN CONCAT(mandir_branch.shakha, ' - ', form_submissions.location)
        ELSE mandir_branch.shakha
    END AS shakha_with_location,
    mandir_branch.shakha,
    SUM(CASE WHEN utsavbandhubhagini.gender = 1 THEN 1 ELSE 0 END) AS Bandhu,
    SUM(CASE WHEN utsavbandhubhagini.gender = 2 THEN 1 ELSE 0 END) AS Bhagini,
    SUM(CASE WHEN utsavbandhubhagini.gender = 1 AND utsavbandhubhagini.seva = 2 THEN 1 ELSE 0 END) AS upasana_bandhu,
    SUM(CASE WHEN utsavbandhubhagini.gender = 2 AND utsavbandhubhagini.seva = 2 THEN 1 ELSE 0 END) AS upasana_bhagini,
    SUM(CASE WHEN utsavbandhubhagini.gender = 1 AND utsavbandhubhagini.seva = 1 THEN 1 ELSE 0 END) AS seva_bandhu,
    SUM(CASE WHEN utsavbandhubhagini.gender = 2 AND utsavbandhubhagini.seva = 1 THEN 1 ELSE 0 END) AS seva_bhagini,
    SUM(CASE WHEN utsavbandhubhagini.gender = 1 AND utsavbandhubhagini.seva = 3 THEN 1 ELSE 0 END) AS anugraha_bandhu,
    SUM(CASE WHEN utsavbandhubhagini.gender = 2 AND utsavbandhubhagini.seva = 3 THEN 1 ELSE 0 END) AS anugraha_bhagini,
    SUM(CASE WHEN utsavbandhubhagini.gender = 1 AND utsavbandhubhagini.seva = 4 THEN 1 ELSE 0 END) AS overlap_bandhu,
    SUM(CASE WHEN utsavbandhubhagini.gender = 2 AND utsavbandhubhagini.seva = 4 THEN 1 ELSE 0 END) AS overlap_bhagini,
    arrival_time.arrival AS time_of_arrival,
    STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
FROM 
    utsavbandhubhagini
INNER JOIN 
    form_submissions 
    ON utsavbandhubhagini.form_submission_id = form_submissions.id
INNER JOIN 
    mandir_branch 
    ON form_submissions.branch = mandir_branch.branch
INNER JOIN 
    arrival_time 
    ON utsavbandhubhagini.time_of_arrival = arrival_time.id
WHERE 
    STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= utsavbandhubhagini.start_date 
    AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= utsavbandhubhagini.end_date
GROUP BY 
    branch_with_location, arrival_time.arrival
HAVING 
    Bandhu > 0 OR Bhagini > 0;";


$TodayCount_record = mysqli_query($db, $query_daily_report);

$deskReportTable;

if (mysqli_num_rows($TodayCount_record) > 0) {
    $deskReportTable .= '<table id="occassionTable" style="text-align: center;" class="table datatable" border="1">';
    $deskReportTable .= '<thead>
        <tr>
            <th>Time of Arrival</th>
            <th>Branch</th>
            <th>Shakha</th>
            <th colspan="2">Seva</th>
            <th colspan="2">Anugraha</th>
            <th colspan="2">Upasana</th>
            <th colspan="2">Overlap</th>
            <th>Total</th>
        </tr>
        <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Bandhu</td>
        <td>Bhagini</td>
        <td>Bandhu</td>
        <td>Bhagini</td>
        <td>Bandhu</td>
        <td>Bhagini</td>
        <td>Bandhu</td>
        <td>Bhagini</td>
        <td></td>
    </tr>
    </thead>';
    $deskReportTable .= '<tbody>';

    while ($row_sevaoccasion = mysqli_fetch_array($TodayCount_record)) {
        $totalCount = $row_sevaoccasion['seva_bandhu'] + $row_sevaoccasion['seva_bhagini'] + $row_sevaoccasion['anugraha_bandhu'] + $row_sevaoccasion['anugraha_bhagini'] + $row_sevaoccasion['upasana_bandhu'] + $row_sevaoccasion['upasana_bhagini'] + $row_sevaoccasion['overlap_bandhu'] + $row_sevaoccasion['overlap_bhagini'];
        $deskReportTable .= '<tr>
            <td>' . $row_sevaoccasion['time_of_arrival'] . '</td>
            <td>' . $row_sevaoccasion['branch_with_location'] . '</td>
            <td>' . $row_sevaoccasion['shakha_with_location'] . '</td>
            <td>' . $row_sevaoccasion['seva_bandhu'] . '</td>
            <td>' . $row_sevaoccasion['seva_bhagini'] . '</td>
            <td>' . $row_sevaoccasion['anugraha_bandhu'] . '</td>
            <td>' . $row_sevaoccasion['anugraha_bhagini'] . '</td>
            <td>' . $row_sevaoccasion['upasana_bandhu'] . '</td>
            <td>' . $row_sevaoccasion['upasana_bhagini'] . '</td>
            <td>' . $row_sevaoccasion['overlap_bandhu'] . '</td>
            <td>' . $row_sevaoccasion['overlap_bhagini'] . '</td>
            <td>' . $totalCount . '</td>
        </tr>';
    }

    $deskReportTable .= '</tbody>';
    $deskReportTable .= '</table>';
} else {
    $deskReportTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
}

if ($isExport == "true") {
    $finalHtml = "<div style='text-align:center; font-weight:bold;'>Desk Report - " . $selectedDate . "</div><div>";
    $finalHtml .= $deskReportTable . "</div>";

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


    $fileName = "Desk report - " . $selectedDate . ".pdf";
    print($fileName);

    ob_end_clean();
    // Output the PDF to browser or download
    $mpdf->Output($fileName, 'D');

    exit;
} else {

    echo $deskReportTable;
}

//echo $deskReportTable;

ob_end_flush();
?>
