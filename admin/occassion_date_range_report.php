<?php
include('../db.php');
$db->set_charset("utf8mb4");

ob_start(); // Start output buffering
require '../vendor/autoload.php'; // Adjust the path as necessary
header('Content-Type: text/html; charset=utf-8');

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


$selectedOccassionName = $_POST['selectedOccassionName'];
$selectedOccassionId = $_POST['selectedOccassionId'];
$selectedOccassionCode = $_POST['selectedOccassionCode'];
$query_occassion_report = "";
$isExport = $_POST['isExport'];

$occasion_id = "";


    // $occassionNameAndYear = preg_replace('/^(.* - \d{4}).*/', '$1', $selectedOccassionName);
    // $occassionNameAndYearPattern = $occassionNameAndYear . '%';

    if ($selectedOccassionCode === "PU") {
        $query_occassion_report = "WITH RECURSIVE DateRange AS (
            -- Initial query to generate the first date (start_date)
            SELECT utsavbandhubhagini.id, utsavbandhubhagini.start_date AS generated_date
            FROM utsavbandhubhagini
            #WHERE utsav_type LIKE '%Punyatithi%'
            
            UNION ALL
        
            SELECT utsavbandhubhagini.id, DATE_ADD(DateRange.generated_date, INTERVAL 1 DAY) AS generated_date
            FROM DateRange
            INNER JOIN utsavbandhubhagini 
                ON utsavbandhubhagini.id = DateRange.id
            WHERE DateRange.generated_date < utsavbandhubhagini.end_date
        )
      
      Select Day,
      Sum(brothers) As brothers ,
      SUM(sisters) AS sisters,
      SUM(UpasanaBandhu) As UpasanaBandhu,
      Sum(UpasanaBhagini) As UpasanaBhagini,
      Sum(UpasanaTotal) As UpasanaTotal,
      Sum(SevaBandhu) As SevaBandhu,
      Sum(SevaBhagini) As SevaBhagini,
      Sum(SevaTotal) As SevaTotal,
      Sum(AnugrahaBandhu) As AnugrahaBandhu,
      Sum(AnugrahaBhagini) AS AnugrahaBhagini,
      Sum( AnugrahaTotal) As  AnugrahaTotal,
      Sum(OverlapBandhu) AS OverlapBandhu,
      Sum(OverlapBhagini) As OverlapBhagini,
      Sum(OverlapTotal) As OverlapTotal
      
      from (
      
      SELECT 
            DateRange.generated_date AS Day,
            YEAR(DateRange.generated_date) AS Year,
            utsavbandhubhagini.id,
                    CASE WHEN  utsavbandhubhagini.gender=1 THEN 1 else 0 end as brothers,
            CASE WHEN  utsavbandhubhagini.gender=2 THEN 1 else 0 end as sisters,
            
            CASE WHEN  utsavbandhubhagini.gender=1 and utsavbandhubhagini.seva=2 THEN 1 else 0 end AS UpasanaBandhu,
            CASE WHEN  utsavbandhubhagini.gender=2 and utsavbandhubhagini.seva=2 THEN 1 else 0 end  AS UpasanaBhagini,
            CASE WHEN  utsavbandhubhagini.seva=2 THEN 1 else 0 end AS UpasanaTotal,
           # SUM(form_arrival_time.upasana_bhagini) AS UpasanaBhagini,
           # SUM(form_arrival_time.upasana_bandhu + form_arrival_time.upasana_bhagini) AS UpasanaTotal,
           CASE WHEN  utsavbandhubhagini.gender=1 and utsavbandhubhagini.seva=1 THEN 1 else 0 end AS SevaBandhu,
            CASE WHEN  utsavbandhubhagini.gender=2 and utsavbandhubhagini.seva=1 THEN 1 else 0 end  AS SevaBhagini,
            CASE WHEN  utsavbandhubhagini.seva=1 THEN 1 else 0 end AS SevaTotal,
           # SUM(form_arrival_time.seva_bandhu) AS SevaBandhu,
           # SUM(form_arrival_time.seva_bhagini) AS SevaBhagini,
            #SUM(form_arrival_time.seva_bandhu + form_arrival_time.seva_bhagini) AS SevaTotal,
            CASE WHEN  utsavbandhubhagini.gender=1 and utsavbandhubhagini.seva=3 THEN 1 else 0 end AS AnugrahaBandhu,
            CASE WHEN  utsavbandhubhagini.gender=2 and utsavbandhubhagini.seva=3 THEN 1 else 0 end AS AnugrahaBhagini,
            CASE WHEN  utsavbandhubhagini.seva=3 THEN 1 else 0 end AS AnugrahaTotal,
            #SUM(form_arrival_time.anugraha_bandhu) AS AnugrahaBandhu,
            # SUM(form_arrival_time.anugraha_bandhu + form_arrival_time.anugraha_bhagini) AS AnugrahaTotal,
                    CASE WHEN  utsavbandhubhagini.gender=1 and utsavbandhubhagini.seva=4 THEN 1 else 0 end AS OverlapBandhu,
            CASE WHEN  utsavbandhubhagini.gender=2 and utsavbandhubhagini.seva=4 THEN 1 else 0 end  AS OverlapBhagini,
            CASE WHEN  utsavbandhubhagini.seva=4 THEN 1 else 0 end AS OverlapTotal
           # SUM(form_arrival_time.overlap_bandhu) AS OverlapBandhu,
            #SUM(form_arrival_time.overlap_bhagini) AS OverlapBhagini,
            #SUM(form_arrival_time.overlap_bandhu + form_arrival_time.overlap_bhagini) AS OverlapTotal
        FROM DateRange
         #LEFT JOIN form_arrival_time 
          #  ON form_arrival_time.form_submission_id = DateRange.id
        INNER JOIN utsavbandhubhagini 
           ON (utsavbandhubhagini.id = DateRange.id) #and (DateRange.generated_date=utsavbandhubhagini.start_date )
        WHERE (DateRange.generated_date BETWEEN utsavbandhubhagini.start_date 
            AND utsavbandhubhagini.end_date)
             ##and utsavbandhubhagini.id=19;
          )A GROUP by Day;";
    } else {
        $query_occassion_report = "WITH RECURSIVE DateRange AS (
            -- Initial query to generate the first date (start_date)
            SELECT form_submissions.id, form_submissions.start_date AS generated_date
            FROM form_submissions
            WHERE form_submissions.occasion_id = '$selectedOccassionId'
            
            UNION ALL
        
            SELECT form_submissions.id, DATE_ADD(DateRange.generated_date, INTERVAL 1 DAY) AS generated_date
            FROM DateRange
            INNER JOIN form_submissions 
                ON form_submissions.id = DateRange.id
            WHERE DateRange.generated_date < form_submissions.end_date
        )
        SELECT 
            DateRange.generated_date AS Day,
            YEAR(DateRange.generated_date) AS Year,
            form_submissions.id,
            form_submissions.brothers,
            form_submissions.sisters,
            SUM(form_arrival_time.upasana_bandhu) AS UpasanaBandhu,
            SUM(form_arrival_time.upasana_bhagini) AS UpasanaBhagini,
            SUM(form_arrival_time.upasana_bandhu + form_arrival_time.upasana_bhagini) AS UpasanaTotal,
            SUM(form_arrival_time.seva_bandhu) AS SevaBandhu,
            SUM(form_arrival_time.seva_bhagini) AS SevaBhagini,
            SUM(form_arrival_time.seva_bandhu + form_arrival_time.seva_bhagini) AS SevaTotal,
            SUM(form_arrival_time.anugraha_bandhu) AS AnugrahaBandhu,
            SUM(form_arrival_time.anugraha_bhagini) AS AnugrahaBhagini,
            SUM(form_arrival_time.anugraha_bandhu + form_arrival_time.anugraha_bhagini) AS AnugrahaTotal,
            SUM(form_arrival_time.overlap_bandhu) AS OverlapBandhu,
            SUM(form_arrival_time.overlap_bhagini) AS OverlapBhagini,
            SUM(form_arrival_time.overlap_bandhu + form_arrival_time.overlap_bhagini) AS OverlapTotal
        FROM DateRange
        LEFT JOIN form_arrival_time 
            ON form_arrival_time.form_submission_id = DateRange.id
        INNER JOIN form_submissions 
            ON form_submissions.id = DateRange.id
        WHERE DateRange.generated_date BETWEEN form_submissions.start_date 
            AND form_submissions.end_date
        GROUP BY DateRange.generated_date, Year
        ORDER BY DateRange.generated_date;";
    }

   


$result_occassion_report = mysqli_query($db, $query_occassion_report);



$sevaOccasionTable;

if(mysqli_num_rows($result_occassion_report) > 0) {
    $sevaOccasionTable .= '<table style="text-align: center;" class="table" border="1">';
    $sevaOccasionTable .= '  <tr>
        <th>Date</th>
        <th colspan="2">Seva</th>
        <th colspan="2">Anugraha</th>
        <th colspan="2">Upasana</th>
        <th colspan="2">Overlap</th>
        <th>Total</th>
    </tr>
    <tr>
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
    </tr>';
    while($row_sevaoccasion = mysqli_fetch_array($result_occassion_report))
    {
        $totalCount = $row_sevaoccasion['UpasanaTotal'] + $row_sevaoccasion['SevaTotal'] + $row_sevaoccasion['AnugrahaTotal'] + $row_sevaoccasion['OverlapTotal'];
        $sevaOccasionTable .= '<tr><td>' . date('d-F-Y', strtotime($row_sevaoccasion['Day'])) . '</td><td class="text-center">' . $row_sevaoccasion['SevaBandhu'] . '</td><td class="text-center">' . $row_sevaoccasion['SevaBhagini'] . '</td><td class="text-center">' . $row_sevaoccasion['AnugrahaBandhu'] . '</td><td class="text-center">' . $row_sevaoccasion['AnugrahaBhagini'] . '</td> <td class="text-center">' . $row_sevaoccasion['UpasanaBandhu'] . '</td> <td class="text-center">' . $row_sevaoccasion['UpasanaBhagini'] . '</td> <td class="text-center">' . $row_sevaoccasion['OverlapBandhu'] . '</td> <td class="text-center">' . $row_sevaoccasion['OverlapBhagini'] . '</td> <td class="text-center">' . $totalCount . '</td></tr>';
    }
    $sevaOccasionTable .= '</table>';
} else {
    $sevaOccasionTable .= '<div class="NoDataFound"><h4>Data Not Available</h4></div>';
}

if ($isExport == "true") {
    print('inside is export');
    $finalHtml = "<div style='text-align:center; font-weight:bold;'>Occasion Date Range - " . $selectedOccassionName . "</div><div>";
    $finalHtml .= $sevaOccasionTable . "</div>";
print('before dir init');
    $customTempDir = __DIR__ . '/mpdf_temp';
    if (!is_dir($customTempDir)) {
        mkdir($customTempDir, 0777, true);
    }
print('before mpdf init');
    // Initialize mPDF
    $mpdf = new Mpdf([
        'tempDir' => $customTempDir,
        'default_font' => 'freeserif'
    ]);

print('before write html');
    // Write the HTML content to the PDF
    $mpdf->WriteHTML($finalHtml);
print('before file name');

    $fileName = "Occasion Date Range Report - " . $selectedOccassionName . ".pdf";
    print($fileName);
print('before clean');
    ob_end_clean();
    // Output the PDF to browser or download
    $mpdf->Output($fileName, 'D');

    exit;
} else {
    echo $sevaOccasionTable;
}
ob_end_flush();
?>
