<?php
include('../db.php');

if (isset($_POST['type'])) {
    $type = $_POST['type'];

    $query = "SELECT start_date, end_date FROM sevautsav WHERE Category_Name = '$type'";
    $result = mysqli_query($db, $query);

    $dates = [];
    if ($row = mysqli_fetch_assoc($result)) {
        $dates['start_date'] = $row['start_date'];
        $dates['end_date'] = $row['end_date'];
    }

    echo json_encode($dates);
}
?>
