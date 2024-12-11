<?php
include('../db.php');

$query1 = "SELECT id FROM form_submissions ORDER BY id DESC LIMIT 1";
$result1 = mysqli_query($db, $query1);
$last_id = mysqli_fetch_assoc($result1)['id'] + 1;

$query2 = "SELECT * FROM seva_selected WHERE form_submission_id='$last_id' ORDER BY id DESC";
$result2 = mysqli_query($db, $query2);



$showOutputTable = False;
$output = '<table border="1" class="tableStyle" id="tblSeva">';
$output .= '<tr>';
$output .= "<th>सेवा</th>";
$output .= "<th>बंधू</th>";
$output .= "<th>भगिनी</th>";
$output .= "<th></th>";  // Added Action column
$output .= '</tr>';

while ($row = mysqli_fetch_assoc($result2)) {
    $showOutputTable = True;
    $output .= "<tr>";
    $output .= "<td>" . htmlspecialchars($row['seva_type']) . "</td>";
    $output .= "<td>" . htmlspecialchars($row['seva_brothers']) . "</dt>";
    $output .= "<td>" . htmlspecialchars($row['seva_sisters']) . "</td>";
    $output .= "<td>";
    $output .= "<form method='POST' action='delete_seva.php' onsubmit='return confirm(\"सेवा तपशील मधून काढली जाईल\");'>";
    $output .= "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "' />";
    $output .= "<input type='submit' value='Delete' />";
    $output .= "</form>";
    $output .= "</td>";
    $output .= "</tr>";
}
$output .= "</table>";

if($showOutputTable)
{
    echo $output;
}

?>
