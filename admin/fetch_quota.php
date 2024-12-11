<?php

include('../db.php');

if (isset($_POST['branchcode']) && isset($_POST['occasionid'])) {
    $branchCode = $_POST['branchcode'];
    $occasionid = $_POST['occasionid'];
    $occasionName = $_POST['occasionName'];
    $selectedBatch = $_POST['selectedBatch'];
    
    $query_quota = "";
    
    if ($selectedBatch == '1') {
        $query_quota = "CALL Punyatithi_Utsav_Quota('$branchCode','$occasionid')";
        //$query_quota = "CALL Punyatithi_Utsav_Quota('THNDHLSBM01','42')";
    } else {
        $query_quota = "SELECT *, 
                    occasion_quota.id AS occasionquotaid,
                    SUM(IFNULL(form_submissions.brothers, 0)) AS 'addedbandhu',
                    SUM(IFNULL(form_submissions.sisters, 0)) AS 'addedbhagini',
                    SUM(IFNULL(form_submissions.total_people, 0)) AS 'addedtotal',
                    (occasion_quota.bandhu_count - SUM(IFNULL(form_submissions.brothers, 0))) AS 'availablebandhu',
                    (occasion_quota.bhagini_count - SUM(IFNULL(form_submissions.sisters, 0))) AS 'availablebhagini',
                    (occasion_quota.total - SUM(IFNULL(form_submissions.total_people, 0))) AS 'availabletotal'
                    FROM occasion_quota
                    LEFT OUTER JOIN occasions ON occasion_quota.occasion_id=occasions.id
                    LEFT OUTER JOIN form_submissions ON occasions.id=form_submissions.occasion_id
                    AND form_submissions.branch_code='$branchCode'
                    AND (CASE WHEN $selectedBatch = '1' THEN form_submissions.start_date BETWEEN occasions.start_date AND occasions.end_date
                            WHEN $selectedBatch = '2' THEN occasions.start_date BETWEEN form_submissions.start_date AND DATE_ADD(occasions.start_date, INTERVAL 3 DAY)
                            WHEN $selectedBatch = '3' THEN DATE_ADD(occasions.start_date, INTERVAL 3 DAY) BETWEEN  DATE_ADD(occasions.start_date, INTERVAL 3 DAY) AND form_submissions.end_date
                                                                                                                AND DATE_ADD(occasions.start_date, INTERVAL 3 DAY) <> form_submissions.end_date
                        END)
                    WHERE occasion_quota.occasion_id='$occasionid'
                    AND occasion_quota.shakha_id='$branchCode'";
    }

    

    $result_quota = mysqli_query($db, $query_quota);   

    if(mysqli_num_rows($result_quota) > 0)
    {
        $row_quota = mysqli_fetch_assoc($result_quota);
        echo json_encode($row_quota);
    } else {
        echo '<script>alert("Quota not available for selected Batch and Shakha");</script>';
    }
} else {
    echo '<script>alert("Batch or Shakha Code or Occasion ID not provided");</script>';
}

?>