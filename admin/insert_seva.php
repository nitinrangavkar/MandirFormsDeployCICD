<?php
include('../db.php');

// Get form data from the AJAX request
$seva_type = $_POST['seva_type'];
$seva_brothers = $_POST['seva_brothers'];
$seva_sisters = $_POST['seva_sisters'];

// Fetch the last form_submission_id and increment by 1
$query = "SELECT id FROM form_submissions ORDER BY id DESC LIMIT 1";
$result = mysqli_query($db, $query);
$last_id = mysqli_fetch_assoc($result)['id'] + 1;

// Insert the data into the seva_selected table
$sql = "INSERT INTO seva_selected (form_submission_id, seva_type, seva_brothers, seva_sisters) 
        VALUES ('$last_id', '$seva_type', '$seva_brothers', '$seva_sisters')";

if (mysqli_query($db, $sql)) {
    echo "Seva added successfully!";
} else {
    echo "Error: " . mysqli_error($db);
}

mysqli_close($db);
?>
