<?php
include('../db.php'); // Ensure this path is correct
$db->set_charset("utf8mb4");

// Session and form handling (unchanged)
if (!isset($_SESSION['usern'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: ../login.php');
    exit;
  }
  
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['usern']);
    header("location: ../login.php");
    exit;
  }  
  
  $ip = $_SERVER['REMOTE_ADDR'];
  
  $date_time = date('d-m-Y H:i');
  $usern = $_SESSION['usern'];
  $page_visit = "SetQuota";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

  $name_dr_o = $_SESSION['usern'];

  $current_datetime = date('Y-m-d H:i:s');


// Autoload dependencies
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$message = "";
$error_message ="";

echo $message;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {

    try {
        $file = $_FILES['excel_file']['tmp_name'];

        // Check if file is uploaded
        if (!empty($file)) {
            // Load the Excel file
            try {
                $spreadsheet = IOFactory::load($file);
            } catch (Exception $e) {
                throw new Exception("Error loading file: " . $e->getMessage());
            }
    
            // Get the first worksheet in the spreadsheet
            $worksheet = $spreadsheet->getActiveSheet();
    
            // Define expected headers (order doesn't matter), with "Batch" being optional
            $expectedHeaders = [
                "Occasion",
                "Occasion Code",
                "Occasion Id",
                "Branch",
                "Shakha",
                "Shakha Code",
                "Bandhu Count",
                "Bhagini Count"
            ];
    
            $optionalHeaders = ["Batch"];
    
            // Extract headers from the first row
            $headerRow = $worksheet->getRowIterator()->current(); // Get the first row
            $actualHeaders = [];
    
            if ($headerRow) {
                $cellIterator = $headerRow->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
    
                foreach ($cellIterator as $cell) {
                    $header = trim($cell->getValue());
                    if (!empty($header)) {
                        $actualHeaders[] = $header;
                    }
                }
    
                // Check if all required headers are present (ignoring order)
                $missingHeaders = array_diff($expectedHeaders, $actualHeaders);
                if (!empty($missingHeaders)) {
                    throw new Exception(("Missing required headers: " . implode(", ", $missingHeaders)));
                }
            } else {
                throw new Exception("Unable to read headers from the file.");
            }
            $data = [];

            $successfulInserts=0;
    
            // Start reading data from the second row onwards
            foreach ($worksheet->getRowIterator(2) as $rowIndex => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                
    
                // Map the extracted headers to the actual data values
                $headerIndex = 0;
                foreach ($cellIterator as $cell) {
                    if (isset($actualHeaders[$headerIndex])) {
                        $data[$actualHeaders[$headerIndex]] = $cell->getValue();
                    }
                    $headerIndex++;
                }
    
                // Validate required data
                $errors = [];
                if (empty($data["Occasion"])) {
                    $errors[] = "Occasion is required.";
                }
                if (empty($data["Occasion Code"])) {
                    $errors[] = "Occasion Code is required.";
                }
                if (empty($data["Occasion Id"])) {
                    $errors[] = "Occasion Id is required.";
                }
                if (empty($data["Branch"])) {
                    $errors[] = "Branch is required.";
                }
                if (empty($data["Shakha"])) {
                    $errors[] = "Shakha is required.";
                }
                if (empty($data["Shakha Code"])) {
                    $errors[] = "Shakha Code is required.";
                }
                
    
                // Convert counts to integers for validation and summing
                $bandhu_count = (int)($data["Bandhu Count"] ?? 0);
                $bhagini_count = (int)($data["Bhagini Count"] ?? 0);
    
                if ($bandhu_count < 0) {
                    $errors[] = "Bandhu Count must be a non-negative number.";
                }
                if ($bhagini_count < 0) {
                    $errors[] = "Bhagini Count must be a non-negative number.";
                }
    
                // Check if the sum of counts is greater than 0
                if (($bandhu_count + $bhagini_count) <= 0) {
                    continue; // Skip rows with zero or negative sum of counts
                }
    
                // Report errors for this row
                if (!empty($errors)) {
                    throw new Exception("Errors in row " . ($rowIndex + 1) . ": " . implode("; ", $errors) . "<br>");
                    continue; // Skip inserting this row
                }
                // Proceed with database insertion logic
                $occasion = $data['Occasion'];
                $occasion_code = $data['Occasion Code'];
                $shakha_id = $data['Shakha Code'];
                $occasion_id = $data['Occasion Id'];
                $occasion_quota_key = $occasion_code.$shakha_id;
                $batch = in_array("Batch", $actualHeaders) ? $data["Batch"] : null;
                $total = $bandhu_count + $bhagini_count;
    
                // Prepare the UPSERT SQL statement where conflict would be checked on occasion_quota_key.
                $query = "INSERT INTO occasion_quota (occasion, occasion_quota_key, batch, shakha_id, occasion_id, bandhu_count, bhagini_count, total, created_by, created_date) 
                            VALUES (
                                '$occasion',
                                '$occasion_quota_key',
                                '$batch',
                                '$shakha_id',
                                '$occasion_id',
                                '$bandhu_count',
                                '$bhagini_count',
                                '$total',
                                '$name_dr_o',
                                '$current_datetime')
                            ON DUPLICATE KEY UPDATE
                                bandhu_count = VALUES(bandhu_count),
                                bhagini_count = VALUES(bhagini_count),
                                total = VALUES(total);";

                
                // Execute
                if (mysqli_query($db, $query)) {
                    if (mysqli_affected_rows($db) > 0) {
                        $successfulInserts++;
                    }
                } else {
                    throw new Exception("Error: " . mysqli_error($db));
                }
            }

            if ($successfulInserts > 0) {
                $message = "Success! $successfulInserts entries have been added/modified.";
            } else {
                throw new Exception("No valid entries to insert.");
            }
        } else {
            throw new Exception("No file uploaded or file is empty.");
        }
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        print_r($error_message);
        $error_message = $e->getMessage();
    }
}

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
                $query_shakhas = "SELECT unique_code, shakha, branch FROM mandir_branch";
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

                    $sheet = $spreadsheet->getActiveSheet();

                    $fileName = '';

                    if ($batch) {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Batch');
                        $sheet->setCellValue('D1', 'Occasion Code');
                        $sheet->setCellValue('E1', 'Occasion Id');
                        $sheet->setCellValue('F1', 'Branch');
                        $sheet->setCellValue('G1', 'Shakha');
                        $sheet->setCellValue('H1', 'Shakha Code');
                        $sheet->setCellValue('I1', 'Bandhu Count');
                        $sheet->setCellValue('J1', 'Bhagini Count');

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
                            $sheet->setCellValue('E' . $row, $occasion_id);
                            $sheet->setCellValue('F' . $row, $data['branch']);
                            $sheet->setCellValue('G' . $row, $data['shakha']);
                            $sheet->setCellValue('H' . $row, $data['unique_code']);
                            $sheet->setCellValue('I' . $row, $bandhu_count);
                            $sheet->setCellValue('J' . $row, $bhagini_count);
                            $row++;
                            $index++;
                        }
                        $fileName = $occasion.' B - '.$batch;
                    } else {
                        // Set header row in Excel
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Occasion');
                        $sheet->setCellValue('C1', 'Occasion Code');
                        $sheet->setCellValue('D1', 'Occasion Id');
                        $sheet->setCellValue('E1', 'Branch');
                        $sheet->setCellValue('F1', 'Shakha');
                        $sheet->setCellValue('G1', 'Shakha Code');
                        $sheet->setCellValue('H1', 'Bandhu Count');
                        $sheet->setCellValue('I1', 'Bhagini Count');

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
                            $sheet->setCellValue('D' . $row, $occasion_id);
                            $sheet->setCellValue('E' . $row, $data['branch']);
                            $sheet->setCellValue('F' . $row, $data['shakha']);
                            $sheet->setCellValue('G' . $row, $data['unique_code']);
                            $sheet->setCellValue('H' . $row, $bandhu_count);
                            $sheet->setCellValue('I' . $row, $bhagini_count);
                            $row++;
                            $index++;
                        }
                        $fileName = $occasion;
                    }
                    $currentDate = date('d-M-Y');

                    $fileName = $fileName."_".$currentDate.".xlsx";

                    // Write the spreadsheet to a file
                    $writer = new Xlsx($spreadsheet);

                    // Send file to the browser for download
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="' . $fileName . '"');

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
        $error_message = $e->getMessage();
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

                $query = "INSERT INTO occasion_quota (occasion, occasion_id, occasion_quota_key, batch, shakha_id, bandhu_count, bhagini_count, total, created_by, created_date) VALUES ('$occasion', '$occasion_id', '$occasion_quota_key', '$batch', '$shakha_id', '$bandhu_count', '$bhagini_count', '$total_count', '$name_dr_o', '$current_datetime')";

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
        $error_message = $e->getMessage();
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
        
        $query = "UPDATE occasion_quota SET bandhu_count='$bandhu_count', bhagini_count='$bhagini_count', total='$total_count', updated_by='$name_dr_o', updated_date='$current_datetime' WHERE id='$id'";
        if (mysqli_query($db, $query)) {
            $message = "Record updated successfully.";
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        $error_message = $e->getMessage();
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
        $error_message = $e->getMessage();
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

  <!-- Bootstrap JS and Dependencies -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  
    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="dropdownassets/select2.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>

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
        #message, #errorMessage {
            z-index: 9999; /* High value to ensure it stays on top */
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .alert {
            margin-top: 15px; /* To avoid overlap with any content above */
            padding: 15px 20px;
            z-index: 1000; /* High z-index to ensure the alert is above other elements */
            position: relative;
        }

        .alert .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }


        /* .nav-tabs .nav-link.active {
            background-color: #ff9900; /* Custom color for active tab */
            /* color: white; */
        /* } */
        
        /* Light pastel blue background for nav-item */
        .nav-item-custom {
            background-color: #f0f4f8; /* Light pastel blue */
            color: #495057; /* Dark gray text for readability */
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        /* Styling the nav-link */
        .nav-item-custom .nav-link {
            color: #495057; /* Dark gray text */
            padding: 10px 15px;
            border-radius: 4px;
        }

        /* Hover state with a slightly darker blue */
        .nav-item-custom .nav-link:hover {
            background-color: #d9e2ec; /* Slightly darker pastel blue */
            color: #495057;
        }

        /* Active state with a soft orange accent */
        .nav-item-custom .nav-link.active {
            background-color: #3f66af; /* Soft orange */
            color: white; /* White text for contrast */
        }

        /* Optional: If you want to remove bottom border from nav-tabs */
        .nav-tabs {
            border-bottom: none;
        }

        /* Custom styles for uniform button alignment */
        #export_occasions .btn, #import_occasions .btn {
            width: 100%;
        }

        #export_occasions, #import_occasions {
            display: flex;
            align-items: center;
            gap: 8px; /* Adjust gap between elements */
        }

        #export_occasions .btn i, #import_occasions .btn i {
            margin-right: 6px; /* Adjust icon spacing */
        }

        /* File Input Container with more width */
        .file-input-container {
            flex: 6; /* Takes 3 times more space compared to the button */
        }

        /* Upload Button Styling */
        .upload-btn {
            flex: 0.5; /* Takes 1 unit of space compared to the file input */
            padding: 5px 12px; /* Adjust padding for a smaller button */
            min-width: 80px; /* Minimum width to prevent it from shrinking too much */
        }

        .card-body {
            padding-left: 0 !important;
        }

        .tab-container {
            padding-top: 20px;
            padding-left: 20px;
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

  <main id="main" class="main">

    <section class="section dashboard">
        <div class="row align-items-center">
            <div class="col-6">
                <h2>Insert Occasion Quota Record</h2>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($message)): ?>
                <div id="message" class="alert alert-success" style="display: none;">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="close" aria-label="Close" style="float: right; background: none; border: none; color: inherit; cursor: pointer;">
                        &times;
                    </button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div id="errorMessage" class="alert alert-danger" style="display: none;">
                    <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="close" aria-label="Close" style="float: right; background: none; border: none; color: inherit; cursor: pointer;">
                        &times;
                    </button>
                </div>
            <?php endif; ?>
            <br>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="entryTypeTab" role="tablist">
                    <li class="nav-item nav-item-custom">
                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Single Entry</a>
                    </li>
                    <li class="nav-item nav-item-custom">
                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Bulk Entry</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Tab 1 Content -->
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        <div class="container tab-container">
                            <form class="row g-3 " action="" method="POST" accept-charset="UTF-8">

                                <input type="hidden" name="action" value="insert">

                                <div class="col-md-4"> 
                                    <label for="occasion">Occasion name:</label>
                                    <select name="occasion" class="form-control" id="occasion" required>
                                        <option value="">Please Select</option>
                                        <?php 
                                            $query_utsav = "SELECT id, occasion, batch FROM occasions WHERE occasion LIKE 'Bhajan Shikavani%' OR occasion LIKE 'Punyatithi%';";
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
                                    <select name="shakha" id="shakha" class="form-select" required>
                                        <option value="">Select Shakha</option>
                                        <?php 
                                            $query_shakha = "SELECT * FROM mandir_branch";
                                            $result_shakha = mysqli_query($db, $query_shakha);
                                            while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                                                $unique_code = $row_shakha['unique_code'];
                                        ?>
                                            <option value="<?php echo htmlspecialchars($row_shakha['unique_code']);?>">
                                                <?php echo htmlspecialchars($row_shakha['shakha']) . " (" . htmlspecialchars($row_shakha['branch']) . ")"; ?>
                                            </option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-4"> 
                                    <label for="shakha_id">Shakha id: </label>
                                    <input type="text" id="shakha_id" name="shakha_id" class="form-control" value="" readonly>
                                </div>

                                <script>
                                    $('#shakha').change(function() {
                                        var selectedValue = $(this).val(); // Get the selected unique_code
                                        $('#shakha_id').val(selectedValue); // Update the shakha_id input box
                                    });
                                </script>


                                <!-- Include Select2 JS -->
                                <script src="dropdownassets/select2.min.js"></script>
                                    <script>
                                        $(document).ready(function() {
                                            $('#shakha').select2({
                                                placeholder: "Select an option",
                                                allowClear: true,
                                                tags: true
                                            });
                                        });
                                </script>

                                <div class="col-md-2">
                                    <label for="batch">Bandhu</label>
                                    <input type="number" class="form-control" name="bandhu_count" min="0" required>
                                </div>

                                <div class="col-md-2">
                                    <label for="batch">Bhagini</label>
                                    <input type="number" class="form-control" name="bhagini_count" min="0" required>
                                </div>

                                <button type="submit" class="btn btn-primary">Insert</button>
                            </form>
                        </div>
                    </div>
                    <!-- Tab 2 Content -->
                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <div class="container tab-container">
                            <div class="row">
                                <!-- Dropdown for Occasion -->
                                <div class="col-md-4">
                                    <label for="occasion">Occasion name:</label>
                                    <select name="occasion" class="form-control" id="occasion_dropdown_export" required>
                                        <option value="">Please Select</option>
                                        <?php 
                                            $query_utsav = "SELECT id, occasion, batch FROM occasions WHERE occasion LIKE 'Bhajan Shikavani%' OR occasion LIKE 'Punyatithi%';";
                                            $result_utsav = mysqli_query($db, $query_utsav);
                                            while ($row_utsav = mysqli_fetch_assoc($result_utsav)) {
                                                $displayValue = $row_utsav['occasion'];
                                                if (!empty($row_utsav['batch'])) {
                                                    $displayValue .= ' B - ' . $row_utsav['batch'];
                                                }
                                                echo '<option value="' . htmlspecialchars($row_utsav['id']) . '">' . htmlspecialchars($displayValue) . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <!-- Export Button -->
                                <div class="col-2 d-flex align-items-center">
                                    <form id="export_occasions" method="POST" action="quota.php">
                                        <input type="hidden" name="occasion_id" id="occasion_id_export_excel" value="">
                                        <button class="btn btn-secondary" type="submit" onclick="getOccasionAndSubmit()" name="export_occasions">
                                            <i class="bi bi-download"></i> Export to Excel
                                        </button>
                                    </form>
                                </div>

                                <!-- Import Button with form to upload the Excel file -->
                                <div class="col-6 d-flex align-items-center">
                                    <form id="import_occasions" method="POST" enctype="multipart/form-data" action="quota.php" class="d-flex w-100 align-items-center">
                                        <div class="mb-3 flex-grow-1 me-3 file-input-container">
                                            <label for="fileInput" class="form-label">Import from Excel</label>
                                            <input class="form-control" type="file" id="fileInput" name="excel_file" accept=".xlsx, .xls" required>
                                        </div>
                                        <button class="btn btn-primary upload-btn" type="submit">Upload</button>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <div class="container">
        <div class="row align-items-center">
                <div class="col-6">
                    <h2>Update/Delete Quota Records</h2>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <form method="post" action="generate_pdf.php">
                        <input type="hidden" name="page_source" value="export_occasion_quota_pdf">
                        <input type="hidden" name="occasion_id" id="occasion_id_export_pdf" value="">
                        <button type="submit" onclick="getOccasionForPDFExport()" class="btn btn-primary">Export to PDF</button>
                    </form>
                </div>
            </div>
        <!-- <h2>Update/Delete Quota Utsav Seva Records</h2> -->
        <table class="table datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Occasion</th>
                    <th>Batch</th>
                    <th>Shakha</th>
                    <th>Branch</th>
                    <th>Bandhu</th>
                    <th>Bhagini</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Fetch existing records
                    $query = "SELECT occasion_quota.*, mandir_branch.shakha, mandir_branch.branch
                                FROM occasion_quota
                                JOIN mandir_branch ON occasion_quota.shakha_id = mandir_branch.unique_code ORDER BY last_modified DESC;";
                    $result = mysqli_query($db, $query);
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $i = $i + 1;
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($row['occasion']) ?></td>
                        <td><?= $row['batch'] ?  htmlspecialchars($row['batch']) : "" ?></td>
                        <td><?= htmlspecialchars($row['branch']) ?></td>
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
    <div id="UpdateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="UpdateForm" action="" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="quota_id" id="quota_id">
                        <div class="form-group">
                            <label for="modalOccasion">Occasion:</label>
                            <input type="text" name="occasion" id="modalOccasion" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalBatch">Batch:</label>
                            <input type="text" name="batch" id="modalBatch" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalShakha">Shakha:</label>
                            <input type="text" name="shakha" id="modalShakha" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalBandhuCount">Bandhu</label>
                            <input type="number" name="bandhu_count" id="modalBandhuCount" class="form-control" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="modalBhaginiCount">Bhagini</label>
                            <input type="number" name="bhagini_count" id="modalBhaginiCount" class="form-control" required min="0">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                            <input type="submit" value="Update" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
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

            // Show the modal using Bootstrap's modal method
            $('#UpdateModal').modal('show');
        }

        function closeModal() {
            // Close the modal using Bootstrap's modal method
            $('#UpdateModal').modal('hide');
        }

        $(document).ready(function () {
            // Show the success message
            if ($('#message').length) {
                $('#message').fadeIn();

                // Fade out after 3 seconds
                const fadeOutMessage = setTimeout(function () {
                    $('#message').fadeOut();
                }, 3000); // 3000 milliseconds = 3 seconds

                // Close button functionality for success message
                $('#message .close').on('click', function () {
                    clearTimeout(fadeOutMessage); // Clear the timeout if the button is clicked
                    $('#message').fadeOut();
                });
            }

            // Show the error message
            if ($('#errorMessage').length) {
                $('#errorMessage').fadeIn();

                // Fade out error message after 3 seconds
                const errorFadeOut = setTimeout(function () {
                    $('#errorMessage').fadeOut();
                }, 3000); // 3000 milliseconds = 3 seconds

                // Close button functionality for error message
                $('#errorMessage .close').on('click', function () {
                    clearTimeout(errorFadeOut); // Clear the timeout if the button is clicked
                    $('#errorMessage').fadeOut();
                });
            }
        });
    </script>



<script>
function getOccasionAndSubmit() {
    // Get values from occasion drop down
    const occasion_id = document.getElementById('occasion_dropdown_export').value;
    document.getElementById("occasion_id_export_excel").value = occasion_id;
}

function getOccasionForPDFExport() {
    const activeTab = document.querySelector('.nav-link.active');
    const occasion_id = activeTab.id == "tab1-tab" ? document.getElementById('occasion').value : document.getElementById('occasion_dropdown_export').value;
    document.getElementById("occasion_id_export_pdf").value = occasion_id;
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
