<?php
include('../db.php');
$db->set_charset("utf8mb4");


$category = $_POST['category'];
$categoryname = $_POST['categoryname'];
$utsavname = $_POST['utsavname'];

$whereClause = " WHERE sevautsav.Category_Name='" . ltrim(rtrim($categoryname)) . "' OR sevautsav.utsavache_naav='" . ltrim(rtrim($categoryname)) . "'";
$trimCategory = ltrim(rtrim($categoryname));
// if ($category == "उत्सव") {
//     $whereClause = " WHERE sevautsav.Category_Name='" . $categoryname . "' OR sevautsav.utsavache_naav='" . $categoryname . "'";
// } else {
//     $whereClause = " WHERE sevautsav.Category_Name='" . $categoryname . "'";
// }

$query_upasthiti = "SELECT
                    branch, SUM(brothers) AS Bandhu, SUM(sisters) AS Bhagini, SUM(total_people) AS Total,
                    SUM(seva_brothers_deleted + utsav_brothers_deleted + anugraha_brothers_deleted + meeting_brothers_deleted) AS DeletedBandhu,
                    SUM(seva_sisters_deleted + utsav_sisters_deleted + anugraha_sisters_deleted + meeting_sisters_deleted) AS DeletedBhagini,
                    SUM(seva_brothers_deleted + utsav_brothers_deleted + anugraha_brothers_deleted + meeting_brothers_deleted +
                    seva_sisters_deleted + utsav_sisters_deleted + anugraha_sisters_deleted + meeting_sisters_deleted) AS DeletedTotal
                    FROM `form_submissions` 
                    INNER JOIN seva_selected ON seva_selected.form_submission_id = form_submissions.id
                    WHERE seva_selected.seva_type='$trimCategory' 
                    GROUP BY branch
                    UNION
                    SELECT
                    branch, SUM(brothers) AS Bandhu, SUM(sisters) AS Bhagini, SUM(total_people) AS Total,
                    SUM(seva_brothers_deleted + utsav_brothers_deleted + anugraha_brothers_deleted + meeting_brothers_deleted) AS DeletedBandhu,
                    SUM(seva_sisters_deleted + utsav_sisters_deleted + anugraha_sisters_deleted + meeting_sisters_deleted) AS DeletedBhagini,
                    SUM(seva_brothers_deleted + utsav_brothers_deleted + anugraha_brothers_deleted + meeting_brothers_deleted +
                    seva_sisters_deleted + utsav_sisters_deleted + anugraha_sisters_deleted + meeting_sisters_deleted) AS DeletedTotal
                    FROM `form_submissions` 
                    WHERE form_submissions.seva_type='$trimCategory' OR 
                            utsav_type='$trimCategory' OR 
                            anugraha_type='$trimCategory' OR 
                            meeting_type='$trimCategory' 
                    GROUP BY branch";

$result_upasthiti = mysqli_query($db, $query_upasthiti);
$upasthitiTable;

if(mysqli_num_rows($result_upasthiti) > 0)
{
    $upasthitiTable = '<table class="table"><tr><th>शाखा</th><th>बंधू</th><th>भगिनी</th><th>एकूण</th><th>रद्द बंधू</th><th>रद्द भगिनी</th><th>एकूण रद्द</th></tr>';
    
        while ($row_upasthiti = mysqli_fetch_array($result_upasthiti)) {
            $upasthitiTable .= '<tr><td>' . $row_upasthiti['branch'] . '</td><td>' . $row_upasthiti['Bandhu'] . '</td><td>' . $row_upasthiti['Bhagini'] . '</td><td>' . $row_upasthiti['Total'] . '</td><td>' . $row_upasthiti['DeletedBandhu'] . '</td><td>' . $row_upasthiti['DeletedBhagini'] . '</td><td>' . $row_upasthiti['DeletedTotal'] . '</td></tr>';
        }

    $upasthitiTable .= '</table>';
}
else {
    $upasthitiTable = '<div class="NoDataFound"><h4>माहिती उपलब्ध नाही</h4></div>';
}

echo $upasthitiTable;
?>
