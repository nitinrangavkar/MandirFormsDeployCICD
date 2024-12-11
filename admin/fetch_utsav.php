<?php
include('../db.php');

if(isset($_POST['state'])) {
    $state = $_POST['state'];
    $query_district = "SELECT DISTINCT VENDOR_DISTRICT FROM main_record WHERE ACCIDENT_STATE = '$state' ORDER BY VENDOR_DISTRICT ASC";
    $result_district = mysqli_query($db, $query_district);

    while($row_district = mysqli_fetch_array($result_district)) {
        echo '<option value="'.$row_district['VENDOR_DISTRICT'].'">'.$row_district['VENDOR_DISTRICT'].'</option>';
    }
}
?>
