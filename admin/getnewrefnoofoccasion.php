<?php
include('../db.php');
$db->set_charset("utf8mb4");


    $occasionId = $_POST['occasionId'];
    $startDate = $_POST['startDate'];

    if ($occasionId != 'general') {
        $queryToGetNewRefIdOfOccasion = "SELECT CONCAT(occasions.occasion_code,'_', COUNT(form_submissions.id)+1) AS refno 
                                        FROM form_submissions
                                        RIGHT JOIN occasions ON form_submissions.occasion_id=occasions.id
                                        WHERE occasions.id=$occasionId";
    } else {
        if ($startDate == "") {
            $startDate = date("d-F-Y");
        }
        $queryToGetNewRefIdOfOccasion = "SELECT CONCAT('GEN_', COUNT(form_submissions.id)+1) AS refno
                                        FROM form_submissions
                                        WHERE DATE_FORMAT(start_date,'%m') = DATE_FORMAT(STR_TO_DATE('" . $startDate . "', '%d-%M-%Y'),'%m')";
    }

    $newRefNoOfOccasion_record = mysqli_query($db, $queryToGetNewRefIdOfOccasion);

    $row_refno = mysqli_fetch_assoc($newRefNoOfOccasion_record);

    echo json_encode($row_refno);

?>