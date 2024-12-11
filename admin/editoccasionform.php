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
  $page_visit = "UpdateOccasionForm";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

$name_dr_o = $_SESSION['usern'];

$current_datetime = date('Y-m-d H:i:s');

$formId = "0";
$row_form_records = array();

if(isset($_GET['id'])){
    $formId =$_GET['id']; 
}

if(isset($_POST['updateoccasionform'])) {
    $formSubmissionId = $_POST['formSubmissionId'];
    $total_brothers = $_POST['total_brothers'];
    $total_sisters = $_POST['total_sisters'];
    $total_brothers_sisters = $_POST['total_brothers_sisters'];

    $arrivalidrow0 = $_POST['arrivalidrow0'];
    $upasanamorning_brothers = $_POST['upasanamorning_brothers'];
    $upasanamorning_sisters = $_POST['upasanamorning_sisters'];
    $sevamorning_brothers = $_POST['sevamorning_brothers'];
    $sevamorning_sisters = $_POST['sevamorning_sisters'];
    $anugrahamorning_brothers = $_POST['anugrahamorning_brothers'];
    $anugrahamorning_sisters = $_POST['anugrahamorning_sisters'];
    $overlapmorning_brothers = $_POST['overlapmorning_brothers'];
    $overlapmorning_sisters = $_POST['overlapmorning_sisters'];

    $arrivalidrow1 = $_POST['arrivalidrow1'];
    $upasanaafternoon_brothers = $_POST['upasanaafternoon_brothers'];
    $upasanaafternoon_sisters = $_POST['upasanaafternoon_sisters'];
    $sevaafternoon_brothers = $_POST['sevaafternoon_brothers'];
    $sevaafternoon_sisters = $_POST['sevaafternoon_sisters'];
    $anugrahaafternoon_brothers = $_POST['anugrahaafternoon_brothers'];
    $anugrahaafternoon_sisters = $_POST['anugrahaafternoon_sisters'];
    $overlapafternoon_brothers = $_POST['overlapafternoon_brothers'];
    $overlapafternoon_sisters = $_POST['overlapafternoon_sisters'];

    $arrivalidrow2 = $_POST['arrivalidrow2'];
    $upasanaevening_brothers = $_POST['upasanaevening_brothers'];
    $upasanaevening_sisters = $_POST['upasanaevening_sisters'];
    $sevaevening_brothers = $_POST['sevaevening_brothers'];
    $sevaevening_sisters = $_POST['sevaevening_sisters'];
    $anugrahaevening_brothers = $_POST['anugrahaevening_brothers'];
    $anugrahaevening_sisters = $_POST['anugrahaevening_sisters'];
    $overlapevening_brothers = $_POST['overlapevening_brothers'];
    $overlapevening_sisters = $_POST['overlapevening_sisters'];

    if ($formSubmissionId != null && $formSubmissionId != "") {
        $updateReasonBandhuBhaginiCountQuery = "UPDATE form_submissions
                                                SET brothers=$total_brothers, sisters=$total_sisters, total_people=$total_brothers_sisters, updated_by='$name_dr_o', updated_date='$current_datetime'
                                                WHERE id='$formSubmissionId'";

        $updateArrivalRow0 = "UPDATE form_arrival_time SET upasana_bandhu=IFNULL('$upasanamorning_brothers',0),upasana_bhagini=IFNULL('$upasanamorning_sisters',0),
                            seva_bandhu=IFNULL('$sevamorning_brothers',0),seva_bhagini=IFNULL('$sevamorning_sisters',0),anugraha_bandhu=IFNULL('$anugrahamorning_brothers',0),
                            anugraha_bhagini=IFNULL('$anugrahamorning_sisters',0),overlap_bandhu=IFNULL('$overlapmorning_brothers',0),overlap_bhagini=IFNULL('$overlapmorning_sisters',0),
				    updated_by='$name_dr_o', updated_date='$current_datetime'
                            WHERE id=$arrivalidrow0";

        $updateArrivalRow1 = "UPDATE form_arrival_time SET upasana_bandhu=IFNULL('$upasanaafternoon_brothers',0),upasana_bhagini=IFNULL('$upasanaafternoon_sisters',0),
                            seva_bandhu=IFNULL('$sevaafternoon_brothers',0),seva_bhagini=IFNULL('$sevaafternoon_sisters',0),anugraha_bandhu=IFNULL('$anugrahaafternoon_brothers',0),
                            anugraha_bhagini=IFNULL('$anugrahaafternoon_sisters',0),overlap_bandhu=IFNULL('$overlapafternoon_brothers',0),overlap_bhagini=IFNULL('$overlapafternoon_sisters',0),
				    updated_by='$name_dr_o', updated_date='$current_datetime'
                            WHERE id=$arrivalidrow1";

        $updateArrivalRow2 = "UPDATE form_arrival_time SET upasana_bandhu=IFNULL('$upasanaevening_brothers',0),upasana_bhagini=IFNULL('$upasanaevening_sisters',0),
                            seva_bandhu=IFNULL('$sevaevening_brothers',0),seva_bhagini=IFNULL('$sevaevening_sisters',0),anugraha_bandhu=IFNULL('$anugrahaevening_brothers',0),
                            anugraha_bhagini=IFNULL('$anugrahaevening_sisters',0),overlap_bandhu=IFNULL('$overlapevening_brothers',0),overlap_bhagini=IFNULL('$overlapevening_sisters',0),
				    updated_by='$name_dr_o', updated_date='$current_datetime'
                            WHERE id=$arrivalidrow2";

                            print($updateReasonBandhuBhaginiCountQuery);
                            print($updateArrivalRow0);
                            print($updateArrivalRow1);
                            print($updateArrivalRow2);

        mysqli_query($db, "START TRANSACTION");

        $query1 = mysqli_query($db, $updateReasonBandhuBhaginiCountQuery);
        $query2 = mysqli_query($db, $updateArrivalRow0);
        $query3 = mysqli_query($db, $updateArrivalRow1);
        $query4 = mysqli_query($db, $updateArrivalRow2);

        if ($query1 and $query2 and $query3 and $query4) {
            mysqli_query($db, "COMMIT"); //Commits the current transaction
            echo '<script>alert("Record Updated successfully");</script>';
            header("Location: editoccasionform.php?id=$formSubmissionId");
         } else {        
            mysqli_query($db, "ROLLBACK");//Even if any one of the query fails, the changes will be undone
         }
    } else {
        echo '<script>alert("Form ID is invalid");</script>';
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

  <link href=
"https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" 
          rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<!-- Bootstrap JS and Dependencies -->
  <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

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
        .table-bordered>tbody>tr>th {
            border: 1px solid #000 !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
        }
        .nav-item-custom {
            background-color: #f0f4f8; /* Light pastel blue */
            color: #495057; /* Dark gray text for readability */
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
            border: 1px solid #000;
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
            <form method="POST" action="editoccasionform.php" accept-charset="UTF-8">
            <?php 
                $query_form_records = "SELECT *, form_submissions.id AS formid, form_arrival_time.id AS arrivalid 
                                        FROM form_submissions
                                        INNER JOIN occasions ON form_submissions.occasion_id=occasions.id
                                        INNER JOIN form_arrival_time ON form_submissions.id=form_arrival_time.form_submission_id
                                        WHERE form_submissions.id='$formId'";
                $result_form_records = mysqli_query($db,$query_form_records);
                while ($row = mysqli_fetch_array($result_form_records)) {
                    $row_form_records[] = $row;
                }
            ?>
            <div>
                    <div style="width:100%;">
                        <?php if ($row_form_records[0]['occasion_code'] != 'GEN') { ?>
                        <div style="text-align:right; font-family:none;">Ref No : <span id="refno"><?php echo htmlspecialchars($row_form_records[0]['refno']); ?></div>
                        <?php } else {
                            $month=date("M",strtotime($row_form_records[0]['start_date']));
                            $year=date("Y",strtotime($row_form_records[0]['start_date']));
                             ?>
                        <div style="text-align:right; font-family:none;">Ref No : <span id="refno"><?php echo htmlspecialchars($row_form_records[0]['refno']); ?></div>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                        <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_records[0]['utsav_type']); ?></b></div>
                    </div>
                    <?php if ($row_form_records[0]['branch'] === "Belagavi Shriharimandir"): ?>
                        <div class="col-md-8">
                            <div class="row">
                                <div class = "col-md-6">
                                    <label for="Category">Shakha</label>
                                    <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_records[0]['branch']); ?></b></div>
                                </div>
                                <div class = "col-md-6">
                                    <label for="locationInfo">Location Information:</label>
                                    <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_records[0]['location']); ?></b></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-8">
                            <label for="Category">Shakha</label>
                            <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_records[0]['branch']); ?></b></div>
                        </div>                   
                    <?php endif; ?>             
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
                        <div class="form-control" readonly><?php $convertedLetterDate = date("d-F-Y", strtotime($row_form_records[0]['letter_dated']));
                            echo $convertedLetterDate; ?></div>
                    </div>
                    <div class="col-md-8">
                        <label for="Category">Date</label>
                        <div class="form-control" readonly><?php $convertedFormDate = date("d-F-Y", strtotime($row_form_records[0]['form_dated']));
                            echo $convertedFormDate; ?></div>
                    </div>

                    <div class="col-md-4">
                        <label for="Category">No Of Bandhu</label>
                        <input class="form-control" type="number" id="total_brothers" name="total_brothers" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['brothers']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">No Of Bhagini</label>
                        <input class="form-control" type="number" id="total_sisters" name="total_sisters" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['sisters']); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Total</label>
                        <input class="form-control" type="number" id="total_brothers_sisters" name="total_brothers_sisters" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['total_people']); ?>" readonly>
                    </div>

                    <div class="col-md-8">
                        <label for="Category">Period of Stay</label>
                        <div class="form-control" readonly>From <?php $convertedStartDate = date("d-F-Y", strtotime($row_form_records[0]['start_date']));
                            echo $convertedStartDate; ?> To <?php $convertedEndDate = date("d-F-Y", strtotime($row_form_records[0]['end_date']));
                            echo $convertedEndDate; ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Duration</label>
                        <input class="form-control" type="number" name="Duration" readonly value="<?php echo $row_form_records[0]['duration'];?>">
                    </div>

                    <div class="col-md-12" style="margin-top:10px;">
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
                                    <div class="tab-pane show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                                        <div style="margin:5px;">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">
                                                            Reason
                                                        </th>
                                                        <th class="text-center">
                                                            Bandhu
                                                        </th>
                                                        <th class="text-center">
                                                            Bhagini
                                                        </th>
                                                        <th class="text-center">
                                                            Total
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id='addr1'>
                                                        <th style="width:40%">Upasana</th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanamorning_brothers" name="upasanamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['upasana_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanamorning_sisters" name="upasanamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['upasana_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanamorning_total" name="upasanamorning_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr2'>
                                                        <th style="width:40%">
                                                        Seva
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevamorning_brothers" name="sevamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['seva_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevamorning_sisters" name="sevamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['seva_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevamorning_total" name="sevamorning_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr3' row="shownoshow">
                                                        <th style="width:40%">
                                                        Anugraha
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahamorning_brothers" name="anugrahamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['anugraha_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahamorning_sisters" name="anugrahamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['anugraha_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahamorning_total" name="anugrahamorning_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr4' row="shownoshow">
                                                        <th style="width:40%">
                                                        Overlap
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapmorning_brothers" name="overlapmorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['overlap_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapmorning_sisters" name="overlapmorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[0]['overlap_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapmorning_total" name="overlapmorning_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                            

                                    <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                        <div style="margin:5px;">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">
                                                            Reason
                                                        </th>
                                                        <th class="text-center">
                                                            Bandhu
                                                        </th>
                                                        <th class="text-center">
                                                            Bhagini
                                                        </th>
                                                        <th class="text-center">
                                                            Total
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id='addr5'>
                                                        <th style="width:40%">Upasana</th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaafternoon_brothers" name="upasanaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['upasana_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaafternoon_sisters" name="upasanaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['upasana_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaafternoon_total" name="upasanaafternoon_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr6'>
                                                        <th style="width:40%">
                                                        Seva
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaafternoon_brothers" name="sevaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['seva_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaafternoon_sisters" name="sevaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['seva_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaafternoon_total" name="sevaafternoon_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr7' row="shownoshow">
                                                        <th style="width:40%">
                                                        Anugraha
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaafternoon_brothers" name="anugrahaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['anugraha_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaafternoon_sisters" name="anugrahaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['anugraha_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaafternoon_total" name="anugrahaafternoon_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr8' row="shownoshow">
                                                        <th style="width:40%">
                                                        Overlap
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapafternoon_brothers" name="overlapafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['overlap_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapafternoon_sisters" name="overlapafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[1]['overlap_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapafternoon_total" name="overlapafternoon_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                                        <div style="margin:5px;">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">
                                                            Reason
                                                        </th>
                                                        <th class="text-center">
                                                            Bandhu
                                                        </th>
                                                        <th class="text-center">
                                                            Bhagini
                                                        </th>
                                                        <th class="text-center">
                                                            Total
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id='addr9'>
                                                        <th style="width:40%">Upasana</th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaevening_brothers" name="upasanaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['upasana_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaevening_sisters" name="upasanaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['upasana_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaevening_total" name="upasanaevening_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr10'>
                                                        <th style="width:40%">
                                                        Seva
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaevening_brothers" name="sevaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['seva_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaevening_sisters" name="sevaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['seva_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaevening_total" name="sevaevening_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr11' row="shownoshow">
                                                        <th style="width:40%">
                                                        Anugraha
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaevening_brothers" name="anugrahaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['anugraha_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaevening_sisters" name="anugrahaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['anugraha_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaevening_total" name="anugrahaevening_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr id='addr12' row="shownoshow">
                                                        <th style="width:40%">
                                                        Overlap
                                                        </th>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapevening_brothers" name="overlapevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['overlap_bandhu']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapevening_sisters" name="overlapevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="<?php echo htmlspecialchars($row_form_records[2]['overlap_bhagini']); ?>" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapevening_total" name="overlapevening_total" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0" readonly>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="formSubmissionId" id="formSubmissionId" value="<?php echo $row_form_records[0]['formid']; ?>">                
                    <input type="hidden" name="arrivalidrow0" id="arrivalidrow0" value="<?php echo $row_form_records[0]['arrivalid']; ?>">
                    <input type="hidden" name="arrivalidrow1" id="arrivalidrow1" value="<?php echo $row_form_records[1]['arrivalid']; ?>">
                    <input type="hidden" name="arrivalidrow2" id="arrivalidrow2" value="<?php echo $row_form_records[2]['arrivalid']; ?>">
                    <input type="hidden" name="batch" id="batch" value="<?php echo $row_form_records[0]['batch']; ?>">
                    <input type="hidden" name="branchcode" id="branchcode" value="<?php echo $row_form_records[0]['branch_code']; ?>">
                    <input type="hidden" name="occasionid" id="occasionid" value="<?php echo $row_form_records[0]['occasion_id']; ?>">
                    <input type="hidden" name="occasioncode" id="occasioncode" value="<?php echo $row_form_records[0]['occasion_code']; ?>">
                    </div>
                    <center>
                        <input type="hidden" name="anugrahautsav" id="anugrahautsav" value="GDP,HJ,AT,GP,SS,RP,VD,DJ,MS">
                        <button id="btnSave" type="submit" name="updateoccasionform" class="btn btn-success btn-sm">Update</button>
                    </center>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer"></footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script>
    $(document).ready(function(){
        var arr_anugraha_utsav = $('#anugrahautsav').val().split(',');
        if (arr_anugraha_utsav.indexOf($('#refno').html().split('_')[0]) !== -1) {
            $('[row^=shownoshow]').css('opacity','');
            $('[id^=anugrahamorning_]').removeAttr('readonly');
            $('[id^=overlapmorning_]').removeAttr('readonly');
            $('[id^=anugrahaafternoon_]').removeAttr('readonly');
            $('[id^=overlapafternoon_]').removeAttr('readonly');
            $('[id^=anugrahevening_]').removeAttr('readonly');
            $('[id^=overlaevening_]').removeAttr('readonly');
        } else {
            $('[row^=shownoshow]').css('opacity','0.2');
            $('[id^=anugrahamorning_]').attr('readonly', true);
            $('[id^=overlapmorning_]').attr('readonly', true);
            $('[id^=anugrahaafternoon_]').attr('readonly', true);
            $('[id^=overlapafternoon_]').attr('readonly', true);
            $('[id^=anugrahevening_]').attr('readonly', true);
            $('[id^=overlaevening_]').attr('readonly', true);
        }
        CalculateEachReasonTotal();
        if ($('#occasioncode').val() == 'BS') {
            $.ajax({
                url: 'fetch_quota.php', // URL to PHP script that fetches dates
                type: 'POST',
                data: {branchcode: $('#branchcode').val(), occasionid: $('#occasionid').val()},
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
        }
    });
    function CalculateBandhuBhagini(e) {
        if (e != undefined) {
            var splitId = e.id.split('_');
            $('#' + splitId[0] + '_total').val((parseInt($('#' + splitId[0] + '_brothers').val()) || 0) + (parseInt($('#' + splitId[0] + '_sisters').val()) || 0));
        }
        $('#total_brothers').val((parseInt($('#upasanamorning_brothers').val()) || 0) + (parseInt($('#upasanaafternoon_brothers').val()) || 0) + (parseInt($('#upasanaevening_brothers').val()) || 0) +
                                (parseInt($('#sevamorning_brothers').val()) || 0) + (parseInt($('#sevaafternoon_brothers').val()) || 0) + (parseInt($('#sevaevening_brothers').val()) || 0) +
                                (parseInt($('#anugrahamorning_brothers').val()) || 0) + (parseInt($('#anugrahaafternoon_brothers').val()) || 0) + (parseInt($('#anugrahaevening_brothers').val()) || 0) + 
                                (parseInt($('#overlapmorning_brothers').val()) || 0) + (parseInt($('#overlapafternoon_brothers').val()) || 0) + (parseInt($('#overlapevening_brothers').val()) || 0));
        $('#total_sisters').val((parseInt($('#upasanamorning_sisters').val()) || 0) + (parseInt($('#upasanaafternoon_sisters').val()) || 0) + (parseInt($('#upasanaevening_sisters').val()) || 0) +
                                (parseInt($('#sevamorning_sisters').val()) || 0) + (parseInt($('#sevaafternoon_sisters').val()) || 0) + (parseInt($('#sevaevening_sisters').val()) || 0) +
                                (parseInt($('#anugrahamorning_sisters').val()) || 0) + (parseInt($('#anugrahaafternoon_sisters').val()) || 0) + (parseInt($('#anugrahaevening_sisters').val()) || 0) + 
                                (parseInt($('#overlapmorning_sisters').val()) || 0) + (parseInt($('#overlapafternoon_sisters').val()) || 0) + (parseInt($('#overlapevening_sisters').val()) || 0));
        $('#total_brothers_sisters').val((parseInt($('#upasanamorning_total').val()) || 0) + (parseInt($('#upasanaafternoon_total').val()) || 0) + (parseInt($('#upasanaevening_total').val()) || 0) +
                                        (parseInt($('#sevamorning_total').val()) || 0) + (parseInt($('#sevaafternoon_total').val()) || 0) + (parseInt($('#sevaevening_total').val()) || 0) + 
                                        (parseInt($('#anugrahamorning_total').val()) || 0) + (parseInt($('#anugrahaafternoon_total').val()) || 0) + (parseInt($('#anugrahaevening_total').val()) || 0) +
                                        (parseInt($('#overlapmorning_total').val()) || 0) + (parseInt($('#overlapafternoon_total').val()) || 0) + (parseInt($('#overlapevening_total').val()) || 0));
    }
    function CalculateEachReasonTotal() {
        $('#upasanamorning_total').val((parseInt($('#upasanamorning_brothers').val()) || 0) + (parseInt($('#upasanamorning_sisters').val()) || 0));
        $('#sevamorning_total').val((parseInt($('#sevamorning_brothers').val()) || 0) + (parseInt($('#sevamorning_sisters').val()) || 0));
        $('#anugrahamorning_total').val((parseInt($('#anugrahamorning_brothers').val()) || 0) + (parseInt($('#anugrahamorning_sisters').val()) || 0));
        $('#overlapmorning_total').val((parseInt($('#overlapmorning_brothers').val()) || 0) + (parseInt($('#overlapmorning_sisters').val()) || 0));

        $('#upasanaafternoon_total').val((parseInt($('#upasanaafternoon_brothers').val()) || 0) + (parseInt($('#upasanaafternoon_sisters').val()) || 0));
        $('#sevaafternoon_total').val((parseInt($('#sevaafternoon_brothers').val()) || 0) + (parseInt($('#sevaafternoon_sisters').val()) || 0));
        $('#anugrahaafternoon_total').val((parseInt($('#anugrahaafternoon_brothers').val()) || 0) + (parseInt($('#anugrahaafternoon_sisters').val()) || 0));
        $('#overlapafternoon_total').val((parseInt($('#overlapafternoon_brothers').val()) || 0) + (parseInt($('#overlapafternoon_sisters').val()) || 0));

        $('#upasanaevening_total').val((parseInt($('#upasanaevening_brothers').val()) || 0) + (parseInt($('#upasanaevening_sisters').val()) || 0));
        $('#sevaevening_total').val((parseInt($('#sevaevening_brothers').val()) || 0) + (parseInt($('#sevaevening_sisters').val()) || 0));
        $('#anugrahaevening_total').val((parseInt($('#anugrahaevening_brothers').val()) || 0) + (parseInt($('#anugrahaevening_sisters').val()) || 0));
        $('#overlapevening_total').val((parseInt($('#overlapevening_brothers').val() || 0)) + (parseInt($('#overlapevening_sisters').val()) || 0));
    }
</script>

<script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>