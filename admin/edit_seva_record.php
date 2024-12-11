
<?php
include("../db.php");

// Handle search/filtering
if (isset($_POST['string_search'])) {
    $valueToSearch = $_POST['valueToSearch'];
    // Search in specific columns
    $query_all_record = "SELECT * FROM `sevautsav` WHERE CONCAT(id, Category, Category_Name) LIKE '%" . mysqli_real_escape_string($db, $valueToSearch) . "%'";
} else {
    $query_all_record = "SELECT `id`,
                `Category`,
                `Category_Name`,
                `Start_Date`,
                `End_Date`,
                `Gat_Kramank`,
                `Partial_group_yes_no`,
                `Edited_By`,
                `Edited_Date` FROM sevautsav";
}
$result_all_record = mysqli_query($db, $query_all_record);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editable Table Example</title>
    <script src="../assets/js/jquery_3_6_0_min.js"></script>
    <script>
        $(document).ready(function() {
            $("td.editable").blur(function() {
                var cell = $(this);
                var newValue = cell.text();
                var columnName = cell.data("column");
                var id = cell.closest("tr").data("row-id");

                $.ajax({
                    url: "edit_seva_record_server.php",
                    method: "POST",
                    data: {
                        id: id,
                        columnName: columnName,
                        newValue: newValue
                    },
                    success: function(response) {
                        // Handle success, if needed
                        console.log(response);
                    }
                });
            });
        });
    </script>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Add New Record</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css2?family=Lohit+Marathi&display=swap" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Lohit Marathi', sans-serif;
    }
    .inline-form {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .inline-form input, .inline-form select {
      margin: 5px 0;
    }
    .chart-container {
      width: 100%;
      height: 400px;
    }
    table, th, td {
  border: 1px solid;
}
  </style>
</head>

<body class="sidebar-toggled">

  <!-- Header -->
  <?php include('header.php'); ?>
  <!-- End Header -->

    <div class="col-12">
        <form action="edit_seva_record.php" method="post">
            <p>Search Record by id</p>
            <input type="text" name="valueToSearch" placeholder="Search">
            <input type="submit" class="btn btn-danger btn-sm" name="string_search" value="Filter"><br><br>
        </form>
    </div>
    <br>

    <table border="1">
        <thead>
            <tr>
                <?php
                // Generate table headers dynamically
                $columnNames = mysqli_fetch_fields($result_all_record);
                foreach ($columnNames as $column) {
                    echo '<th>' . $column->name . '</th>';
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display data rows
            while ($row_all_record = mysqli_fetch_assoc($result_all_record)) {
                echo '<tr data-row-id="' . $row_all_record['id'] . '">';
                foreach ($row_all_record as $columnName => $value) {
                    echo '<td class="editable" data-column="' . $columnName . '" contenteditable="true">' . $value . '</td>';
                }
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</body>
</html>
