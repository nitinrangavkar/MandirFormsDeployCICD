<?php
include('../db.php');
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
  $page_visit = "EditPunyatithi";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

$current_datetime = date('Y-m-d H:i:s');

$formId = "0";

if(isset($_GET['id'])){
    $formId =$_GET['id']; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    try {
        $rowId = intval($_POST['rowId']);
        $formSubmissionId = intval($_POST['formSubmissionId']);
        $genderId = $_POST['genderId'];
        $arrivalTime = $_POST['arrivalTime'];
        $reason = strtolower($_POST['reason']);

        $updateArrivalTime = "";
        if ($genderId == '1') {
            $updateArrivalTime = "UPDATE form_arrival_time SET " . $reason . "_bandhu=(" . $reason . "_bandhu - 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$arrivalTime";
        } else {
            $updateArrivalTime = "UPDATE form_arrival_time SET " . $reason . "_bhagini=(" . $reason . "_bhagini - 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$arrivalTime";
        }

        // Mark record as deleted
        $deleteBandhuBhaginiQuery = "UPDATE utsavbandhubhagini SET isDeleted=true, updated_by='$usern', updated_date='$current_datetime' WHERE id='$rowId'";
        // if (mysqli_query($db, $deleteBandhuBhaginiQuery)) {
            // Update the gender-based counters
            if ($genderId == "1") {
                $updateBandhuBhaginiCount = "UPDATE form_submissions
                                            SET brothers = (brothers - 1),
                                                total_people = (total_people - 1),
								updated_by='$usern',
								updated_date='$current_datetime'
                                            WHERE id='$formSubmissionId'";
            } else {
                $updateBandhuBhaginiCount = "UPDATE form_submissions
                                            SET sisters = (sisters - 1),
                                                total_people = (total_people - 1),
								updated_by='$usern',
								updated_date='$current_datetime'
                                            WHERE id='$formSubmissionId'";
            }

        //     if (mysqli_query($db, $updateBandhuBhaginiCount)) {
        //         // Success response to AJAX
        //         echo json_encode(['status' => 'success']);
        //         exit();
        //     } else {
        //         // Error while updating form submission
        //         echo json_encode(['status' => 'error', 'message' => 'Error updating form submission']);
        //         exit();
        //     }
        // } else {
        //     // Error while deleting the record
        //     echo json_encode(['status' => 'error', 'message' => 'Error deleting record']);
        //     exit();
        // }

        mysqli_query($db, "START TRANSACTION");

        $query1 = mysqli_query($db, $updateArrivalTime);
        $query2 = mysqli_query($db, $deleteBandhuBhaginiQuery);
        $query3 = mysqli_query($db, $updateBandhuBhaginiCount);

        if ($query1 and $query2 and $query3) {
            mysqli_query($db, "COMMIT"); //Commits the current transaction
            // echo '<script>alert("Record Updated successfully");</script>';
            header("Location: editpunyatithi.php?id=$formSubmissionId");
            echo json_encode(['status' => 'success']);
         } else {        
            mysqli_query($db, "ROLLBACK");//Even if any one of the query fails, the changes will be undone
         }

    } catch (Exception $e) {
        // Handle exceptions
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}

// if(isset($_POST['remove'])) {
//     $rowId = $_POST['rowId'];
//     $formSubmissionId = $_POST['formSubmissionId'];
//     $genderId = $_POST['genderId'];
//     $arrivalTime = $_POST['arrivalTime'];
//     $type = $_POST['type'];

//     $updateArrivalTime = "";

//     switch($arrivalTime)
//     {
//         case '1':
//             if ($genderId == '1') {
//                 $updateArrivalTime = "UPDATE form_arrival_time SET $type_bandhu=($type_bandhu - 1) WHERE id=$rowId";
//             } else {
//                 $updateArrivalTime = "UPDATE form_arrival_time SET $type_bhagini=($type_bhagini - 1) WHERE id=$rowId";
//             }
//             break;
//         case '2':
//             if ($genderId == '1') {
//                 $updateArrivalTime = "UPDATE form_arrival_time SET $type_bandhu=($type_bandhu - 1) WHERE id=$rowId";
//             } else {
//                 $updateArrivalTime = "UPDATE form_arrival_time SET $type_bhagini=($type_bhagini - 1) WHERE id=$rowId";
//             }
//             break;
//     }

//     $deleteBandhuBhanginiQuery = "UPDATE utsavbandhubhagini SET isDeleted=true WHERE id='$rowId'";

//     if(mysqli_query($db, $deleteBandhuBhanginiQuery)) {
//         $updateBandhuBhaginiCount = "";
//         if ($genderId == "1") {
//             $updateBandhuBhaginiCount = "UPDATE form_submissions
//                                         SET brothers=((SELECT brothers FROM form_submissions WHERE id='$formSubmissionId')-1),
//                                         total_people=((SELECT total_people FROM form_submissions WHERE id='$formSubmissionId')-1)
//                                         WHERE id='$formSubmissionId'";
//         }
//         else {
//             $updateBandhuBhaginiCount = "UPDATE form_submissions
//                                         SET sisters=((SELECT sisters FROM form_submissions WHERE id='$formSubmissionId')-1),
//                                         total_people=((SELECT total_people FROM form_submissions WHERE id='$formSubmissionId')-1)
//                                         WHERE id='$formSubmissionId'";
//         }

//         if(mysqli_query($db, $updateBandhuBhaginiCount)) {
//             echo '<script>alert("Record deleted successfully");</script>';
//             header("Location: editpunyatithi.php?id=$formSubmissionId");
//             exit();
//         }
//     }
// }

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $id = $_POST['utsavbandhubhagini_id'];
        $formSubmissionId = $_POST['formSubmissionId'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        $modalDdlTImeOfArrival = $_POST['modalDdlTImeOfArrival'];
        $modalOldTimeOfArrival = $_POST['modalOldTimeOfArrival'];
        $modalGender = $_POST['modalGender'];
        $type = strtolower($_POST['type']);
        
        $query = "UPDATE utsavbandhubhagini SET name='$name', age='$age', time_of_arrival=$modalDdlTImeOfArrival, updated_by='$usern', updated_date='$current_datetime' WHERE id='$id'";
        if ($modalDdlTImeOfArrival != $modalOldTimeOfArrival) {
            $queryUpdateArrivalTimeMinus = "";
            $queryUpdateArrivalTimePlus = "";
            if ($modalGender == 'Male') {
                $queryUpdateArrivalTimeMinus = "UPDATE form_arrival_time SET " . $type . "_bandhu=(" . $type . "_bandhu - 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$modalOldTimeOfArrival";
                $queryUpdateArrivalTimePlus = "UPDATE form_arrival_time SET " . $type . "_bandhu=(" . $type . "_bandhu + 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$modalDdlTImeOfArrival";
            } else {
                $queryUpdateArrivalTimeMinus = "UPDATE form_arrival_time SET " . $type . "_bhagini=(" . $type . "_bhagini - 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$modalOldTimeOfArrival";
                $queryUpdateArrivalTimePlus = "UPDATE form_arrival_time SET " . $type . "_bhagini=(" . $type . "_bhagini + 1), updated_by='$usern', updated_date='$current_datetime' WHERE form_submission_id=$formSubmissionId AND time_of_arrival=$modalDdlTImeOfArrival";
            }
            mysqli_query($db, "START TRANSACTION");
            $query1 = mysqli_query($db, $query);
            $query2 = mysqli_query($db, $queryUpdateArrivalTimeMinus);
            $query3 = mysqli_query($db, $queryUpdateArrivalTimePlus);
            if ($query1 and $query2 and $query3) {
                mysqli_query($db, "COMMIT"); //Commits the current transaction
                echo '<script>alert("Record updated successfully");</script>';
                echo '<script>window.location.replace("editpunyatithi.php?id=' . $formSubmissionId . '");</script>';
                exit();
             } else {       
                echo '<script>alert("error");</script>'; 
                mysqli_query($db, "ROLLBACK");//Even if any one of the query fails, the changes will be undone
                throw new Exception("Error: " . mysqli_error($db));
             }
        } else {
            if (mysqli_query($db, $query)) {
                echo '<script>alert("Record updated successfully");</script>';
                echo '<script>window.location.replace("editpunyatithi.php?id=' . $formSubmissionId . '");</script>';
                exit();
    
            } else {
                throw new Exception("Error: " . mysqli_error($db));
            }
        }
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        $error_message = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Punyatithi Utsav</title>
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.js"></script>
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.inputmethods.js"></script>

  <!-- <link href=
"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" 
          rel="stylesheet" type="text/css" /> -->
          <!-- <link href="..assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> -->
    <link rel="stylesheet" href=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<!-- Bootstrap JS and Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <!-- <script src="..assets/js/bootstrap.bundle.min.js"></script> -->

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

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
        .datepicker {
          z-index: none !important;
          margin: 40px 0 0 90px;
        }
        .table-bordered>thead>tr>th {
            border: 1px solid #000 !important;
        }
        .table-bordered>tbody>tr>td {
            border: 1px solid #000 !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
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
            background-color: #3f66af !important; /* Soft orange */
            color: #fff !important; /* White text for contrast */
            border: 1px solid #000 !important;
        }
        .nav-item-custom .active {
            background-color: #3f66af !important; /* Soft orange */
            color: #fff !important; /* White text for contrast */
            border: 1px solid #000 !important;
        }
        .card-body {
            padding: 0 !important;
        }
        .tab-content {
            border: 1px solid #000;
            border-radius: 4px;
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

  <!------------------------------------------ Main part Start --------------------------------------------------->

<main id="main" class="main">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <form method="POST" action="editpunyatithi.php" accept-charset="UTF-8">
            <?php 
                $query_form_records = "SELECT *,
                                        (CASE WHEN form_submissions.start_date = occasions.start_date AND form_submissions.end_date = occasions.end_date 
                                                THEN CONCAT('1_Utsav',' - From - ',DATE_FORMAT(occasions.start_date, '%d-%M-%Y'),' - To - ',DATE_FORMAT(occasions.end_date, '%d-%M-%Y'),' - ',DATEDIFF(occasions.end_date, occasions.start_date)+1,' Days')
                                            WHEN form_submissions.start_date = occasions.start_date AND form_submissions.end_date <> occasions.end_date 
                                                THEN CONCAT('2_Batch - 1',' - From - ',DATE_FORMAT(occasions.start_date, '%d-%M-%Y'),' - To - ',DATE_FORMAT(DATE_ADD(occasions.start_date, INTERVAL 3 DAY), '%d-%M-%Y'),' - ',DATEDIFF(DATE_ADD(occasions.start_date, INTERVAL 3 DAY), occasions.start_date)+1,' Days')
                                            WHEN form_submissions.start_date <> occasions.start_date AND form_submissions.end_date = occasions.end_date 
                                                THEN CONCAT('3_Batch - 2',' - From - ',DATE_FORMAT(DATE_ADD(occasions.start_date, INTERVAL 3 DAY), '%d-%M-%Y'),' - To - ',DATE_FORMAT(occasions.end_date, '%d-%M-%Y'),' - ',DATEDIFF(occasions.end_date,DATE_ADD(occasions.start_date, INTERVAL 3 DAY))+1,' Days') END) AS Batch 
                                        FROM form_submissions
                                        INNER JOIN occasions ON form_submissions.occasion_id=occasions.id
                                        WHERE form_submissions.id='$formId'";
                $result_form_records = mysqli_query($db,$query_form_records);
                $row_form_record = mysqli_fetch_array($result_form_records);            ?>
            <div>
                    <div style="width:100%;">
                        <div style="text-align:right; font-family:none;">Ref No : <?php echo htmlspecialchars($row_form_record['refno']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                        <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['utsav_type']); ?></b></div>
                    </div>
                    <?php if ($row_form_record['branch'] === "Belagavi Shriharimandir"): ?>
                        <div class="col-md-8">
                            <div class="row">
                                <div class = "col-md-6">
                                    <label for="Category">Shakha</label>
                                    <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['branch']); ?></b></div>
                                </div>
                                <div class = "col-md-6">
                                    <label for="locationInfo">Location Information:</label>
                                    <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['location']); ?></b></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-8">
                            <label for="Category">Shakha</label>
                            <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['branch']); ?></b></div>
                        </div>                   
                    <?php endif; ?>   
                    <div class="col-md-12">
                        <label for="Category">Batch</label>
                        <?php
                        $splitBatch = explode('_', $row_form_record['Batch']);
                        ?>
                        <div class="form-control" readonly><b><?php echo htmlspecialchars($splitBatch[1]); ?></b></div>
                        <input type="hidden" name="batchNumber" id="batchNumber" value="<?php echo htmlspecialchars($splitBatch[0]); ?>">
                    </div>                
                    <div class="col-md-3">
                        <label for="Category">Quota</label>
                        <input type="text" class="form-control" id="quota_total" name="quota_total" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label for="Category">Available Bandhu</label>
                        <input type="text" class="form-control" id="bandhu_available" name="bandhu_available" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label for="Category">Available Bhagini</label>
                        <input type="text" class="form-control" id="bhagini_available" name="bhagini_available" value="0" readonly />
                    </div>
                    <div class="col-md-3">
                        <label for="Category">Available Total</label>
                        <input type="text" class="form-control" id="quota_available" name="quota_available" value="0" readonly />
                    </div>
                    
                    <div class="col-md-4">
                        <label for="Category">Letter Dated</label>
                        <div class="form-control" readonly><?php $convertedLetterDate = date("d-F-Y", strtotime($row_form_record['letter_dated']));
                            echo $convertedLetterDate; ?></div>
                    </div>
                    <div class="col-md-8">
                        <label for="Category">Date</label>
                        <div class="form-control" readonly><?php $convertedFormDate = date("d-F-Y", strtotime($row_form_record['form_dated']));
                            echo $convertedFormDate; ?></div>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="Category">Time Of Arrival</label>
                        <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['time_of_arrival']); ?></div>
                    </div> -->

                    <div class="col-md-4">
                        <label for="Category">No Of Bandhu</label>
                        <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['brothers']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">No Of Bhagini</label>
                        <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['sisters']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Total</label>
                        <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['total_people']); ?></div>
                    </div>

                    <!-- <div class="col-md-8">
                        <label for="Category">Period of Stay</label>
                        <div class="form-control" readonly>From <?php $convertedStartDate = date("d-F-Y", strtotime($row_form_record['start_date']));
                            echo $convertedStartDate; ?> To <?php $convertedEndDate = date("d-F-Y", strtotime($row_form_record['end_date']));
                            echo $convertedEndDate; ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Duration</label>
                        <input class="form-control" type="number" name="Duration" readonly value="<?php echo $row_form_record['duration'];?>">
                    </div> -->

                    <div class="col-md-12" style="margin-top:10px;">
                    <?php 
                        $query_bandhuBhagini = "SELECT utsavbandhubhagini.*, gendermaster.Gender, prakarmaster.type
                                                FROM utsavbandhubhagini
                                                INNER JOIN gendermaster ON gendermaster.id = utsavbandhubhagini.gender
                                                INNER JOIN prakarmaster ON prakarmaster.id = utsavbandhubhagini.seva
                                                WHERE form_submission_id='$formId'
                                                AND isDeleted=false
                                                ORDER BY time_of_arrival ASC";
                        $result_bandhuBhagini = mysqli_query($db,$query_bandhuBhagini);
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs" id="entryTypeTab" role="tablist">
                                <li class="nav-item nav-item-custom" style="width:33%; text-align:center;">
                                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Morning</a>
                                </li>
                                <li class="nav-item nav-item-custom" style="width:33%; text-align:center;">
                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Afternoon</a>
                                </li>
                                <li class="nav-item nav-item-custom" style="width:34%; text-align:center;">
                                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Evening</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <!-- Tab 1 Content -->
                                <div class="tab-pane show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab" style="padding:5px;">
                                    <table class="table table-bordered table-hover" id="tblBandhuBhaginiDetails_morning">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    #
                                                </th>
                                                <th class="text-center">
                                                    Name
                                                </th>
                                                <th class="text-center">
                                                    Age
                                                </th>
                                                <th class="text-center">
                                                    Gender
                                                </th>
                                                <th class="text-center">
                                                    Reason
                                                </th>
                                                <th class="text-center">Start Date</th>
                                                <th class="text-center">End Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            $index = 0;
                                            while($row_bandhuBhagini = mysqli_fetch_array($result_bandhuBhagini))
                                            {
                                                if ($row_bandhuBhagini['time_of_arrival'] == '1') {
                                                    $index++;
                                                ?>
                                                <tr <?php if($row_bandhuBhagini['gender']=="1"){ ?> style="background-color:#c3c5f7;" <?php }else{ ?> style="background-color:#ffccff;" <?php } ?>>
                                                    <td style="width:2%"><?php echo $index ?></td>
                                                    <td style="width:30%"><?php echo $row_bandhuBhagini['name'];?></td>
                                                    <td style="width:5%"><?php echo $row_bandhuBhagini['age'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['Gender'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['type'];?></td>
                                                    <td style="width:10%"><?php $convertedStartDate = date("d-M-Y", strtotime($row_bandhuBhagini['start_date']));
                                                        echo $convertedStartDate;?>
                                                    </td>
                                                    <td style="width:10%"><?php $convertedEndDate = date("d-M-Y", strtotime($row_bandhuBhagini['end_date']));
                                                        echo $convertedEndDate;?>
                                                    </td>
                                                    <td style="width:15%">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal(<?= htmlspecialchars($row_bandhuBhagini['id']) ?>, <?= htmlspecialchars($row_bandhuBhagini['form_submission_id']) ?>, '<?= htmlspecialchars($row_bandhuBhagini['name']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['age']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['Gender']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['type']) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['start_date']))) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['end_date']))) ?>','<?= htmlspecialchars($row_bandhuBhagini['time_of_arrival']) ?>')">Update</button>
                                                    <button type="submit" rowId="<?php echo $row_bandhuBhagini['id'];?>" formSubmissionId="<?php echo $row_bandhuBhagini['form_submission_id'];?>" genderId="<?php echo $row_bandhuBhagini['gender'];?>" onclick="RemoveRecord(this)" name="remove" class="btn btn-danger btn-sm" arrivaltime="1" reason="<?php echo $row_bandhuBhagini['type'];?>">Remove</button>
                                                    </td>
                                                </tr>
                                        <?php } } ?>   
                                        <tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab" style="padding:5px;">
                                    <table class="table table-bordered table-hover" id="tblBandhuBhaginiDetails_afternoon">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    #
                                                </th>
                                                <th class="text-center">
                                                    Name
                                                </th>
                                                <th class="text-center">
                                                    Age
                                                </th>
                                                <th class="text-center">
                                                    Gender
                                                </th>
                                                <th class="text-center">
                                                    Reason
                                                </th>
                                                <th class="text-center">Start Date</th>
                                                <th class="text-center">End Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            mysqli_data_seek($result_bandhuBhagini, 0 );
                                            $index = 0;
                                            while($row_bandhuBhagini = mysqli_fetch_array($result_bandhuBhagini))
                                            {
                                                if ($row_bandhuBhagini['time_of_arrival'] == '2') {
                                                    $index++;
                                                ?>
                                                <tr <?php if($row_bandhuBhagini['gender']=="1"){ ?> style="background-color:#c3c5f7;" <?php }else{ ?> style="background-color:#ffccff;" <?php } ?>>
                                                    <td style="width:2%"><?php echo $index ?></td>
                                                    <td style="width:30%"><?php echo $row_bandhuBhagini['name'];?></td>
                                                    <td style="width:5%"><?php echo $row_bandhuBhagini['age'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['Gender'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['type'];?></td>
                                                    <td style="width:10%"><?php $convertedStartDate = date("d-M-Y", strtotime($row_bandhuBhagini['start_date']));
                                                        echo $convertedStartDate;?>
                                                    </td>
                                                    <td style="width:10%"><?php $convertedEndDate = date("d-M-Y", strtotime($row_bandhuBhagini['end_date']));
                                                        echo $convertedEndDate;?>
                                                    </td>
                                                    <td style="width:15%">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal(<?= htmlspecialchars($row_bandhuBhagini['id']) ?>, <?= htmlspecialchars($row_bandhuBhagini['form_submission_id']) ?>, '<?= htmlspecialchars($row_bandhuBhagini['name']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['age']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['Gender']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['type']) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['start_date']))) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['end_date']))) ?>', '<?= htmlspecialchars($row_bandhuBhagini['time_of_arrival']) ?>')">Update</button>
                                                    <button type="submit" rowId="<?php echo $row_bandhuBhagini['id'];?>" formSubmissionId="<?php echo $row_bandhuBhagini['form_submission_id'];?>" genderId="<?php echo $row_bandhuBhagini['gender'];?>" onclick="RemoveRecord(this)" name="remove" class="btn btn-danger btn-sm" arrivaltime="2" reason="<?php echo $row_bandhuBhagini['type'];?>">Remove</button>
                                                    </td>
                                                </tr>
                                        <?php } } ?>   
                                        <tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="tab3-tab" style="padding:5px;">
                                    <table class="table table-bordered table-hover" id="tblBandhuBhaginiDetails_evening">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    #
                                                </th>
                                                <th class="text-center">
                                                    Name
                                                </th>
                                                <th class="text-center">
                                                    Age
                                                </th>
                                                <th class="text-center">
                                                    Gender
                                                </th>
                                                <th class="text-center">
                                                    Reason
                                                </th>
                                                <th class="text-center">Start Date</th>
                                                <th class="text-center">End Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            mysqli_data_seek($result_bandhuBhagini, 0 );
                                            $index = 0;
                                            while($row_bandhuBhagini = mysqli_fetch_array($result_bandhuBhagini))
                                            {
                                                if ($row_bandhuBhagini['time_of_arrival'] == '3') {
                                                    $index++;
                                                ?>
                                                <tr <?php if($row_bandhuBhagini['gender']=="1"){ ?> style="background-color:#c3c5f7;" <?php }else{ ?> style="background-color:#ffccff;" <?php } ?>>
                                                    <td style="width:2%"><?php echo $index ?></td>
                                                    <td style="width:30%"><?php echo $row_bandhuBhagini['name'];?></td>
                                                    <td style="width:5%"><?php echo $row_bandhuBhagini['age'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['Gender'];?></td>
                                                    <td style="width:10%"><?php echo $row_bandhuBhagini['type'];?></td>
                                                    <td style="width:10%"><?php $convertedStartDate = date("d-M-Y", strtotime($row_bandhuBhagini['start_date']));
                                                        echo $convertedStartDate;?>
                                                    </td>
                                                    <td style="width:10%"><?php $convertedEndDate = date("d-M-Y", strtotime($row_bandhuBhagini['end_date']));
                                                        echo $convertedEndDate;?>
                                                    </td>
                                                    <td style="width:15%">
                                                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal(<?= htmlspecialchars($row_bandhuBhagini['id']) ?>, <?= htmlspecialchars($row_bandhuBhagini['form_submission_id']) ?>, '<?= htmlspecialchars($row_bandhuBhagini['name']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['age']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['Gender']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['type']) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['start_date']))) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['end_date']))) ?>', '<?= htmlspecialchars($row_bandhuBhagini['time_of_arrival']) ?>')">Update</button>
                                                    <button type="submit" rowId="<?php echo $row_bandhuBhagini['id'];?>" formSubmissionId="<?php echo $row_bandhuBhagini['form_submission_id'];?>" genderId="<?php echo $row_bandhuBhagini['gender'];?>" onclick="RemoveRecord(this)" name="remove" class="btn btn-danger btn-sm" arrivaltime="3" reason="<?php echo $row_bandhuBhagini['type'];?>">Remove</button>
                                                    </td>
                                                </tr>
                                        <?php } } ?>   
                                        <tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div>
                        
                        <input type="hidden" name="rowId" id="rowId">
                        <input type="hidden" name="formSubmissionId" id="formSubmissionId">                
                        <input type="hidden" name="genderId" id="genderId">
                        <input type="hidden" name="batch" id="batch" value="<?php echo $row_form_record['batch']; ?>">
                        <input type="hidden" name="branchcode" id="branchcode" value="<?php echo $row_form_record['branch_code']; ?>">
                        <input type="hidden" name="occasionid" id="occasionid" value="<?php echo $row_form_record['occasion_id']; ?>">
                        </div>
                    </div>                    
                </div>
            </form>
        </div>
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
                        <input type="hidden" name="utsavbandhubhagini_id" id="utsavbandhubhagini_id">
                        <input type="hidden" name="formSubmissionId" id="formSubmissionIdModal">

                        <div class="form-group">
                            <label for="modalName">Name:</label>
                            <input type="text" name="name" id="modalName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="modalAge">Age:</label>
                            <input type="text" name="age" id="modalAge" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="modalTimeOfArrival">Time Of Arrival:</label>
                            <select id="modalDdlTImeOfArrival" name="modalDdlTImeOfArrival" class="form-control">
                                <option value="1">Morning</option>
                                <option value="2">Afternoon</option>
                                <option value="3">Evening</option>
                            </select>
                            <input type="hidden" name="modalOldTimeOfArrival" id="modalOldTimeOfArrival">
                        </div>
                        <div class="form-group">
                            <label for="modalGender">Gender:</label>
                            <input type="text" name="gender" id="modalGender" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalType">Type:</label>
                            <input type="text" name="type" id="modalType" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalStartDate">Start Date:</label>
                            <input type="text" name="start_date" id="modalStartDate" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalEndDate">End Date:</label>
                            <input type="text" name="end_date" id="modalEndDate" class="form-control" required readonly>
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
</main>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer"></footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script>
    $(document).ready(function(){
        $.ajax({
            url: 'fetch_quota.php', // URL to PHP script that fetches dates
            type: 'POST',
            data: {branchcode: $('#branchcode').val(), occasionid: $('#occasionid').val(), selectedBatch:$('#batchNumber').val()},
            success: function(response) {
                var res = JSON.parse(response);
                if(res.id != null) {
                    $('#quota_total').val(res.total);
                    $('#quota_available').val(res.availabletotal);
                    $('#bandhu_available').val(res.availablebandhu);
                    $('#bhagini_available').val(res.availablebhagini);
                } else {
                    $('#quota_total').val(0);
                    $('#quota_available').val(0);
                    $('#bandhu_available').val(0);
                    $('#bhagini_available').val(0);
                    alert('Quota not available for selected Batch and Shakha');
                }
            },
            error: function() {
                alert('Error fetching dates.');
            }
        });
    });
    // function RemoveRecord(e) {
    //     $('#rowId').val(e.attributes["rowid"].value);
    //     $('#formSubmissionId').val(e.attributes["formsubmissionid"].value);
    //     $('#genderId').val(e.attributes["genderId"].value);
    //     if (confirm('Are you sure, want to Remove this record') == true){
    //         return true;
    //     }
    //     else {
    //         return false;
    //     }
    // }
    function RemoveRecord(e) {
        debugger;
        if (confirm("Are you sure you want to delete this entry?")) {
            $.ajax({
                url: 'editpunyatithi.php',
                type: 'POST',
                data: {
                    rowId: e.attributes['rowId'].value,
                    formSubmissionId: e.attributes['formSubmissionId'].value,
                    genderId: e.attributes['genderId'].value,
                    arrivalTime: e.attributes['arrivaltime'].value,
                    reason: e.attributes['reason'].value,
                    action: 'remove'
                },
                success: function(response) {
                    debugger;
                    var data = JSON.parse(response); // Parse JSON response from PHP

                    if (data.status === 'success') {
                        alert("Record deleted successfully");
                        window.location.replace(window.location.href); // Redirect without re-submitting the form
                    } else {
                        alert("Error: " + data.message); // Display error message if deletion failed
                    }
                },
                error: function() {
                    alert("An unexpected error occurred.");
                }
            });
        }
    }
    function openModal(id, formSubmissionId, name, age, gender, type, start_date, end_date, timeOfArrival) {
        debugger;
        document.getElementById('utsavbandhubhagini_id').value = id;
        document.getElementById('formSubmissionIdModal').value = formSubmissionId;
        document.getElementById('modalName').value = name;
        document.getElementById('modalAge').value = age;
        document.getElementById('modalGender').value = gender;
        document.getElementById('modalType').value = type;

        document.getElementById('modalStartDate').value = start_date;
        document.getElementById('modalEndDate').value = end_date;
        $('#modalDdlTImeOfArrival').val(timeOfArrival);
        $('#modalOldTimeOfArrival').val(timeOfArrival);

        // Show the modal using Bootstrap's modal method
        $('#UpdateModal').modal('show');
    }

    function closeModal() {
        // Close the modal using Bootstrap's modal method
        $('#UpdateModal').modal('hide');
    }
</script>

<script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>