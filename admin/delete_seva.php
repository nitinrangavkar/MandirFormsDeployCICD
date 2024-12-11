<?php
include('../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);  // Get the ID of the record to delete

    // Prepare the deletion query
    $query = "DELETE FROM seva_selected WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Record deleted successfully
        echo "Record deleted successfully.";
    } else {
        // Record not found or deletion failed
        echo "Failed to delete the record.";
    }

    $stmt->close();
}

// Redirect back to the original page (change the path as necessary)
header("Location: addnewrecord.php");
exit();
?>
