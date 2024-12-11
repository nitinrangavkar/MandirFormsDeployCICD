<?php
include('../db.php');
$utsav_type = $_POST['utsav_type'];
$query_seva = "SELECT DISTINCT CONCAT(Category_Name, ' ', Gat_Kramank) AS Category_Gat, id FROM sevautsav WHERE Category = 'मीटिंग/शिकवणी'";
if ($utsav_type) {
    $query_seva .= " AND utsavache_naav = '" . mysqli_real_escape_string($db, $utsav_type) . "'";
}
$result_seva = mysqli_query($db, $query_seva);
$options = '<option></option>';
while ($row_seva = mysqli_fetch_assoc($result_seva)) {
    $options .= '<option sevautsavid="'.$row_seva['id'].'" value="' . htmlspecialchars($row_seva['Category_Gat']) . '">' . htmlspecialchars($row_seva['Category_Gat']) . '</option>';
}
echo $options;
?>
