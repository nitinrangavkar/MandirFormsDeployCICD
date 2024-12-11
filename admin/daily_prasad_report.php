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

$query_daily_report = "Select 
Sum(Bandhu) AS Bandhu,sum(Bhagini) AS Bhagini,sum(Total) AS Total, time_of_arrival,TODAY
FROM
(

SELECT
                        SUM(upasana_bandhu+seva_bandhu+anugraha_bandhu+overlap_bandhu) AS Bandhu,
                        SUM(upasana_bhagini+seva_bhagini+anugraha_bhagini+overlap_bhagini) AS Bhagini,
                       			 SUM(upasana_bandhu+seva_bandhu+anugraha_bandhu+overlap_bandhu+upasana_bhagini+seva_bhagini+anugraha_bhagini+overlap_bhagini) AS Total,
                        arrival_time.arrival AS time_of_arrival,
                        arrival_time.id AS arrival_time_id,
                        STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY
                        FROM form_submissions 
                        INNER JOIN form_arrival_time ON form_submissions.id=form_arrival_time.form_submission_id
                        INNER JOIN arrival_time ON form_arrival_time.time_of_arrival=arrival_time.id
                        WHERE STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= start_date AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= end_date 
                        AND form_submissions.id NOT IN (SELECT DISTINCT form_submission_id FROM utsavbandhubhagini)
                        GROUP BY arrival_time.id, arrival_time_id
                        ##ORDER BY arrival_time.id;
                        
UNION


SELECT SUM(CASE WHEN utsavbandhubhagini.gender=1 then 1 else 0 end) AS Bandhu,
SUM(CASE WHEN utsavbandhubhagini.gender=2 then 1 else 0 end) AS  Bhagini ,
count(utsavbandhubhagini.id) AS Total,
arrival_time.arrival AS time_of_arrival,
arrival_time.id AS arrival_time_id,
STR_TO_DATE('$selectedDate', '%d-%M-%Y') AS TODAY

FROM utsavbandhubhagini
INNER JOIN arrival_time ON utsavbandhubhagini.time_of_arrival=arrival_time.id
where 
 #STR_TO_DATE('$selectedDate', '%d-%M-%Y') =utsavbandhubhagini.start_date;
   STR_TO_DATE('$selectedDate', '%d-%M-%Y') >= start_date 
  AND STR_TO_DATE('$selectedDate', '%d-%M-%Y') <= end_date
  group by arrival_time.arrival,utsavbandhubhagini.time_of_arrival, arrival_time_id
                        ) A WHERE (Bandhu IS NOT NULL OR Bhagini IS NOT NULL) GROUP by time_of_arrival,TODAY
                        ORDER by arrival_time_id ASC;";
$TodayCount_record = mysqli_query($db, $query_daily_report);

$dailyPrasadTable;

if(mysqli_num_rows($TodayCount_record) > 0) {
    $dailyPrasadTable .= '<table class="tablePrasadReport" border="1" width="100%">';
    $dailyPrasadTable .= '<tr><th></th><th style="text-align:center;">Bandhu</th><th style="text-align:center;">Bhagini</th><th style="text-align:center;">Total</th></tr>';
    while($row_daily_prasad = mysqli_fetch_array($TodayCount_record))
    {
        $dailyPrasadTable .= '<tr><td style="text-align:center;">' . $row_daily_prasad['time_of_arrival'] . '</td><td style="text-align:center;">' . $row_daily_prasad['Bandhu'] . '</td><td style="text-align:center;">' . $row_daily_prasad['Bhagini'] . '</td><td style="text-align:center;">' . $row_daily_prasad['Total'] . '</td></tr>';
    }
    $dailyPrasadTable .= '</table>';
} else {
    $dailyPrasadTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
}

if ($isExport == "true") {
    $finalHtml = "<div style='text-align:center; font-weight:bold;'>Prasad Report - " . $selectedDate . "</div><div>";
    $finalHtml .= $dailyPrasadTable . "</div>";

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


    $fileName = "Prasad Report - " . $selectedDate . ".pdf";
    print($fileName);

    ob_end_clean();
    // Output the PDF to browser or download
    $mpdf->Output($fileName, 'D');

    exit;
} else {

    echo $dailyPrasadTable;
}

//echo $dailyPrasadTable;

ob_end_flush();
?>
