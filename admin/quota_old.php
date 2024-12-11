<?php
include('../db.php'); // Ensure this path is correct
$db->set_charset("utf8mb4");

// Autoload dependencies
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$message = "";

if (isset($_POST['export_occasions'])) {
    try {

        // Clear the output buffer to avoid issues with headers
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $occasion_id = $_POST['occasion_id'];

        if (!$occasion_id) {
            echo '<script>alert("Please select an occasion for bulk upload");</script>';
            echo '<script>window.location.href="quota.php";</script>';
            exit;
        }


        $query = "SELECT id, occasion, batch, occasion_key FROM occasions WHERE id = '$occasion_id'";
        $result = mysqli_query($db, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $result_row = mysqli_fetch_assoc($result);
                $occasion = $result_row['occasion'];
                $batch = $result_row['batch'];
                $occasion_key = $result_row['occasion_key'];
                
                $query_shakhas = "SELECT unique_code, shakha FROM mandir_branch";
                $shakhas = mysqli_query($db, $query_shakhas);

                // Query to get existing quotas
                $already_added_quotas_query = "SELECT shakha_id, bandhu_count, bhagini_count FROM occasion_quota WHERE occasion_id = '$occasion_id'";
                $already_added_quotas = mysqli_query($db, $already_added_quotas_query);

                // Create an associative array to store the quotas
                $quotas = [];
                if ($already_added_quotas && mysqli_num_rows($already_added_quotas) > 0) {
                    while ($quota_row = mysqli_fetch_assoc($already_added_quotas)) {
                        $quotas[$quota_row['shakha_id']] = [
                            'bandhu_count' => $quota_row['bandhu_count'],
                            'bhagini_count' => $quota_row['bhagini_count']
                        ];
                    }
                }

                if (mysqli_num_rows($shakhas) > 0) {
                    // Create a new spreadsheet
                    $spreadsheet = new Spreadsheet();

                    // error_log("Hello --> ".$spreadsheet);
                    $sheet = $spreadsheet->getActiveSheet();

                    if ($batch) {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Batch');
                        $sheet->setCellValue('D1', 'Occasion Code');
                        $sheet->setCellValue('E1', 'Shakha');
                        $sheet->setCellValue('F1', 'Shakha Code');
                        $sheet->setCellValue('G1', 'Bandhu Count');
                        $sheet->setCellValue('H1', 'Bhagini Count');

                        $row = 2; // Data starts from the second row
                        $index = 1;
                        while ($data = mysqli_fetch_assoc($shakhas)) {

                            // Check if the shakha has existing quotas
                            $shakha_id = $data['unique_code'];
                            $bandhu_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bandhu_count'] : 0;
                            $bhagini_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bhagini_count'] : 0;
                            
                            // Fill the spreadsheet with data
                            $sheet->setCellValue('A' . $row, $index);
                            $sheet->setCellValue('B' . $row, $occasion);
                            $sheet->setCellValue('C' . $row, $batch);
                            $sheet->setCellValue('D' . $row, $occasion_key);
                            $sheet->setCellValue('E' . $row, $data['shakha']);
                            $sheet->setCellValue('F' . $row, $data['unique_code']);
                            $sheet->setCellValue('G' . $row, $bandhu_count);
                            $sheet->setCellValue('H' . $row, $bhagini_count);
                            $row++;
                            $index++;
                        }
                    } else {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Occasion Code');
                        $sheet->setCellValue('D1', 'Shakha');
                        $sheet->setCellValue('E1', 'Shakha Code');
                        $sheet->setCellValue('F1', 'Bandhu Quota');
                        $sheet->setCellValue('G1', 'Bhagini Quota');

                        $row = 2; // Data starts from the second row
                        $index = 1;
                        while ($data = mysqli_fetch_assoc($shakhas)) {

                            // Check if the shakha has existing quotas
                            $shakha_id = $data['unique_code'];
                            $bandhu_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bandhu_count'] : 0;
                            $bhagini_count = isset($quotas[$shakha_id]) ? $quotas[$shakha_id]['bhagini_count'] : 0;
                            
                            // Fill the spreadsheet with data
                            $sheet->setCellValue('A' . $row, $index);
                            $sheet->setCellValue('B' . $row, $occasion);
                            $sheet->setCellValue('C' . $row, $occasion_key);
                            $sheet->setCellValue('D' . $row, $data['shakha']);
                            $sheet->setCellValue('E' . $row, $data['unique_code']);
                            $sheet->setCellValue('F' . $row, $bandhu_count);
                            $sheet->setCellValue('G' . $row, $bhagini_count);
                            $row++;
                            $index++;
                        }
                    }

                    // Write the spreadsheet to a file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = 'data-export.xlsx';

                    // Send file to the browser for download
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="' . $fileName . '"');
                    // header('Cache-Control: max-age=0');
                    // header('Cache-Control: max-age=1');
                    // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    // header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
                    // header('Pragma: public');

                    // Save to output
                    $writer->save('php://output');
                    exit;
                    
                } else {
                    throw new Exception("Error in retrieving shakhas");
                }

            } else {
                // Ocassion doesn't exist
                throw new Exception("Ocassion doesn't exist.");
            }
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
}

// Handle INSERT request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'insert') {
    try {
        // Code that may throw an exception
        $occasion_id = $_POST['occasion'];
        $shakha_id = $_POST['shakha_id'];
        $bandhu_count = $_POST['bandhu_count'];
        $bhagini_count = $_POST['bhagini_count'];
        $total_count = $bandhu_count + $bhagini_count;

        if ($total_count <= 0) {
            echo '<script>alert("Total quota should be greater than zero");</script>';
            echo '<script>window.location.href="quota.php";</script>';
            exit;
        }

        $query = "SELECT occasion, batch, occasion_key FROM occasions WHERE id = '$occasion_id'";
        $result = mysqli_query($db, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $result_row = mysqli_fetch_assoc($result);
                $occasion = $result_row['occasion'];
                $batch = $result_row['batch'];
                $occasion_key = $result_row['occasion_key'];
                $occasion_quota_key = $occasion_key.$shakha_id;

                $query_check = "SELECT * FROM occasion_quota WHERE occasion_quota_key = '$occasion_quota_key'";
                $result_check = mysqli_query($db, $query_check);
                if (mysqli_num_rows($result_check) > 0) {
                    throw new Exception("Error: Duplicate entry for occasion_quota_key.");
                }

                $query = "INSERT INTO occasion_quota (occasion, occasion_id, occasion_quota_key, batch, shakha_id, bandhu_count, bhagini_count, total) VALUES ('$occasion', '$occasion_id', '$occasion_quota_key', '$batch', '$shakha_id', '$bandhu_count', '$bhagini_count', '$total_count')";

                if (mysqli_query($db, $query)) {
                    $message = "Record inserted successfully.";
                } else {
                    throw new Exception("Error: " . mysqli_error($db));
                }

            } else {
                // Ocassion doesn't exist
                throw new Exception("Ocassion doesn't exist.");
            }
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
    
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $id = $_POST['quota_id'];
        $bandhu_count = $_POST['bandhu_count'];
        $bhagini_count = $_POST['bhagini_count'];
        $total_count = $bandhu_count + $bhagini_count;

        if ($total_count <= 0) {
            echo '<script>alert("Total quota should be greater than zero");</script>';
            echo '<script>window.location.href="quota.php";</script>';
            exit;
        }
        
        $query = "UPDATE occasion_quota SET bandhu_count='$bandhu_count', bhagini_count='$bhagini_count', total='$total_count' WHERE id='$id'";
        if (mysqli_query($db, $query)) {
            $message = "Record updated successfully.";
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    try {
        $id = $_POST['id'];
        $query = "DELETE FROM occasion_quota WHERE id='$id'";

        if (mysqli_query($db, $query)) {
            $message = "Record deleted successfully.";
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
    
}
?>


<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Shakha Management</title>
  <meta content="" name="description">
  <meta content="" name="keywords">


  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin - v2.2.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <!-- Input type marathi -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.js"></script>
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.inputmethods.js"></script>

    <script type="text/javascript">
      $(document).ready(function () {
        $('#marathiInput').ime();
        $('#marathiInput').ime('select', 'mr-transliteration');
      });
    </script>

  <!-- Close Input type marathi -->



  
    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="dropdownassets/select2.min.js"></script>
    <!-- <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #ff9900; /* Shri Hari Mandiram orange */
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        form input, form textarea {
            width: 20%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form input[type="submit"] {
            background: #ff9900;
            color: #fff;
            border: none;
            padding: 0px 0px; /* Ensured same padding as submit buttons */
            cursor: pointer;
            text-decoration: none;
            font-size: 16px; /* Ensured font size is consistent */
        }
        form input[type="submit"]:hover {
            background: #e68a00;
        }
        form .btn {
            background: #FFA500;
            color: #fff;
            border: none;
            /* padding: 0px; Ensures consistent padding */
            cursor: pointer;
            text-decoration: none;
            font-size: 16px; /* Consistent font size */
            margin: 0; /* Reset any margins that may be inherited */
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background: #e6ffe6;
            color: #333;
        }
        .message.error {
            background: #ffe6e6;
            color: #c00;
        }
        h1 {
            color: #ff9900;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #ff9900;
            color: #fff;
        }
        .btn {
            background: #ff9900;
            color: #fff;
            border: none;
            padding: 10px 15px; /* Ensured same padding as submit buttons */
            cursor: pointer;
            text-decoration: none;
            font-size: 16px; /* Ensured font size is consistent */
        }
        .btn:hover {
            background: #e68a00;
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style> -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>


    <!-- <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.8;
            margin: 40px;
        }
        .center {
            text-align: center;
            color: #800000; /* Dark Maroon */
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            max-width: 800px;
            margin: auto;
            background-color: #fff8e1; /* Light Yellowish Background */
            border: 2px solid #b8860b; /* Golden Rod Border */
            padding: 20px;
            border-radius: 10px;
        }
        .left {
            float: left;
        }
        .right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        .signature {
            margin-top: 50px;
        }
        .phone-numbers {
            margin-top: 20px;
        }
    </style>
    <style>
        html {
            margin: auto;
            width: 80%;
            border: 5px solid #b8860b; /* Golden Rod Border */
            padding: 10px;
            border-radius: 15px;
        }
    </style> -->

    <!-- <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.8;
            margin: 40px;
        }
        .center {
            text-align: center;
            color: #800000; /* Dark Maroon */
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            max-width: 800px;
            margin: auto;
            background-color: #fff8e1; /* Light Yellowish Background */
            border: 2px solid #b8860b; /* Golden Rod Border */
            padding: 20px;
            border-radius: 10px;
        }
        .left {
            float: left;
        }
        .right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        .signature {
            margin-top: 50px;
        }
        .phone-numbers {
            margin-top: 20px;
        }
    </style> -->
    <!-- <style>
        html {
            margin: auto;
            width: 100%;
            border: 5px solid #b8860b; /* Golden Rod Border */
            padding: 10px;
            border-radius: 15px;
        }
    </style> -->


    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.8;
            margin: 40px;
        }
        .center {
            text-align: center;
            color: #800000; /* Dark Maroon */
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            max-width: 800px;
            margin: auto;
            background-color: #fff8e1; /* Light Yellowish Background */
            border: 2px solid #b8860b; /* Golden Rod Border */
            padding: 20px;
            border-radius: 10px;
        }
        .left {
            float: left;
        }
        .right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        .signature {
            margin-top: 50px;
        }
        .phone-numbers {
            margin-top: 20px;
        }
        html {
            margin: auto;
            width: 95%;
            padding: 5px;
            border-radius: 5px;
        }
    </style>

</head>

<body>

  <!-- ======================== Header ======================== -->
    <?php include('header.php'); ?>
  <!------====================== End Header ==============------->


 <!--------------------- Sidebar --------------------------------->
    <?php include('sidebar.php'); ?>
  <!--------------------- End Sidebar ----------------------------->

    <section class="section dashboard">
        <div class="row">
            <?php if ($message): ?>
                <div class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <br>
        </div>
    </section>
    <div class="container">
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <br>
        <div class="container text-center">
            <div class="row">
                <div class="col-md-auto">
                    <h2>Insert Ocassion Quota Record</h2>
                </div>
                <div class="col-md">
                    <form id="export_occasions" method="POST" action="quota.php">
                        <input type="hidden" name="occasion_id" id="occasion_id" value="">
                        <button class="btn" type="submit" onclick="getOccasionAndSubmit()" name="export_occasions">Export Data to Excel for Bulk Upload</button>
                    </form>
                </div>
                <div class="col-md-auto">
                <form action="import.php" method="post" enctype="multipart/form-data">
                    <label for="file">Choose Excel file:</label>
                    <input type="file" name="import_file" id="import_file" accept=".xlsx, .xls">
                    <button class="btn" type="submit" name="import">Import</button>
                </form>
                </div>
            </div>
        </div>
        <!-- <h2>Insert Ocassion Quota Record</h2> -->
        <form class="row g-3" action="" method="POST" accept-charset="UTF-8">
            <!-- <div class="col-md-4"> 

            </div> -->

            <input type="hidden" name="action" value="insert">

            <div class="col-md-4"> 
                <label for="occasion">Occasion name:</label>
                <select name="occasion" class="form-control" id="occasion" required>
                    <option value="">Please Select</option>
                    <?php 
                        $query_utsav = "SELECT id, occasion, batch FROM occasions";
                        $result_utsav = mysqli_query($db, $query_utsav);
                        while ($row_utsav = mysqli_fetch_assoc($result_utsav)) {
                        // Check if the 'batch' column is present and concatenate it if it exists
                        $displayValue = $row_utsav['occasion'];
                        if (!empty($row_utsav['batch'])) {
                            $displayValue .= ' B - ' . $row_utsav['batch'];
                        }
                        echo '<option value="' . htmlspecialchars($row_utsav['id']) . '">' . htmlspecialchars($displayValue) . '</option>';
                    }
                    ?>
                </select>
            </div>


            <div class="col-md-4"> 
                <label for="shakha">Shakha</label>
                <input type="text" name="shakha" class="form-control" list="shakha" id="branchInput" required />
                <datalist id="shakha">
                    <?php 
                        $query_shakha = "SELECT * FROM mandir_branch";
                        $result_shakha = mysqli_query($db, $query_shakha);
                        while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                            $unique_code = $row_shakha['unique_code'];
                    ?>
                        <option value="<?php echo htmlspecialchars($row_shakha['shakha']);?>" data-unique="<?php echo htmlspecialchars($unique_code); ?>">
                            <?php echo $unique_code; ?>
                        </option>
                    <?php
                        }
                    ?>
                </datalist>
            </div>

            <div class="col-md-4"> 
                <label for="shakha_id">Shakha id: </label>
                <input type="text" id="shakha_id" name="shakha_id" class="form-control" value="" readonly>
            </div>

            <script>
                // JavaScript to update the read-only input based on selected option in the datalist
                document.getElementById('branchInput').addEventListener('input', function() {
                    const datalist = document.getElementById('shakha');
                    const shakha_id = document.getElementById('shakha_id');
                    const selectedValue = this.value;

                    // Find the selected option in the datalist and get its data-unique attribute
                    let uniqueCode = '';
                    for (let i = 0; i < datalist.options.length; i++) {
                        if (datalist.options[i].value === selectedValue) {
                            uniqueCode = datalist.options[i].getAttribute('data-unique');
                            break;
                        }
                    }

                    // Set the unique code to the read-only input field
                    shakha_id.value = uniqueCode;
                });
            </script>


            <!-- Include Select2 JS -->
            <script src="dropdownassets/select2.min.js"></script>

            
                <script>
                    $(document).ready(function() {
                        $('#shakhaSelect').select2({
                            placeholder: "Select an option",
                            allowClear: true
                        });
                    });
            </script>

            <div class="col-md-4">
                <label for="batch">Bandhu Count:</label>
                <input type="number" name="bandhu_count" min="0" required>
            </div>

            <div class="col-md-4">
                <label for="batch">Bhagini Count:</label>
                <input type="number" name="bhagini_count" min="0" required>
            </div>

            <input type="submit" value="Insert">
        </form>

        <h2>Update/Delete Quota Utsav Seva Records</h2>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Occasion</th>
                    <th>Batch</th>
                    <th>Shakha</th>
                    <th>Bandhu Count</th>
                    <th>Bhagini Count</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Fetch existing records
                    $query = "SELECT occasion_quota.*, mandir_branch.shakha
                                FROM occasion_quota
                                JOIN mandir_branch ON occasion_quota.shakha_id = mandir_branch.unique_code;";
                    $result = mysqli_query($db, $query);
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $i = $i + 1;
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($row['occasion']) ?></td>
                        <td><?= $row['batch'] ?  htmlspecialchars($row['batch']) : "" ?></td>
                        <td><?= htmlspecialchars($row['shakha']) ?></td>
                        <td><?= htmlspecialchars($row['bandhu_count']) ?></td>
                        <td><?= htmlspecialchars($row['bhagini_count']) ?></td>
                        <td><?= htmlspecialchars($row['total']) ?></td>
                        <td>
                            <!-- Update Button -->
                            <button class="btn btn-primary btn-sm" onclick="openModal(<?= htmlspecialchars($row['id']) ?>, '<?= htmlspecialchars($row['occasion']) ?>', '<?= htmlspecialchars($row['shakha']) ?>', '<?= htmlspecialchars($row['batch']) ?>', '<?= htmlspecialchars($row['bandhu_count']) ?>', '<?= htmlspecialchars($row['bhagini_count']) ?>')">Update</button>
                            
                            <!-- Delete Form -->
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');"> Delete </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="UpdateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Update Record</h2>
            <form id="UpdateForm" action="" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="quota_id" id="quota_id">
                Occasion: <input type="text" name="occasion" id="modalOccasion" required readonly><br>
                Batch: <input type="text" name="batch" id="modalBatch" required readonly><br>
                Shakha: <input type="text" name="shakha" id="modalShakha" required readonly><br>
                Bandhu Count: <input type="number" name="bandhu_count" id="modalBandhuCount" required min="0"><br>
                Bhagini Count: <input type="number" name="bhagini_count" id="modalBhaginiCount" required min="0"><br>
                <input type="submit" value="Update" class="btn">
            </form>
        </div>
    </div>

    <script>
        function openModal(id, occasion, shakha, batch, bandhuCount, bhaginiCount) {
            document.getElementById('quota_id').value = id;
            document.getElementById('modalOccasion').value = occasion;
            document.getElementById('modalShakha').value = shakha;
            document.getElementById('modalBatch').value = batch;
            document.getElementById('modalBandhuCount').value = bandhuCount;
            document.getElementById('modalBhaginiCount').value = bhaginiCount;

            document.getElementById('UpdateModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('UpdateModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('UpdateModal')) {
                closeModal();
            }
        }
    </script>

<script>
function getOccasionAndSubmit() {
    // Get values from occasion drop down
    const occasion_id = document.getElementById('occasion').value;

    // // Create hidden inputs to append to Form 
    // const form = document.getElementById('export_occasions');
    
    // // Check if the hidden input already exists to avoid duplicates
    // let occasionInput = document.querySelector('input[name="occasion_input"]');
    // if (!occasionInput) {
    //     occasionInput = document.createElement('input');
    //     occasionInput.type = 'hidden';
    //     occasionInput.name = 'occasion_input';
    //     form.appendChild(occasionInput);
    // }
    
    // occasionInput.value = occasion_id;

    // Submit the Form after adding the hidden input
    // form.submit();

    document.getElementById("occasion_id").value = occasion_id;
}
</script>
      <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.min.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>
</html>
