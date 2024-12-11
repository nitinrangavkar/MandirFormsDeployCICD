<?php
include('../db.php');
$db->set_charset("utf8mb4");


// Session and form handling (unchanged)
if (!isset($_SESSION['usern'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: https://www.godjn.com');
    exit;
  }
  
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['usern']);
    header("location: https://www.godjn.com");
    exit;
  }
  
  
  $ip = $_SERVER['REMOTE_ADDR'];
  
  $date_time = date('d-m-Y H:i');
  $usern = $_SESSION['usern'];
  $page_visit = "occasionform";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

$current_datetime = date('Y-m-d H:i:s');
$username = $_SESSION['usern'];
$date_time = date("d-m-Y") . " | " . date("h:i:sa");

// Handle form submission
if(isset($_POST['reg_user']))
{
    $formrefno = $_POST['formRefNo'];
    $occasioncode = $_POST['occasioncode'];
    $occasionId = $_POST['occasionId'];
    $shakha = $_POST['shakha'];
    $branch_code = $_POST['branch_code'];
    $total_brothers = $_POST['total_brothers'];
    $total_sisters = $_POST['total_sisters'];
    $total_brothers_sisters = $_POST['total_brothers_sisters'];
    $start_date = $_POST['utsavStartDate'];
    $end_date = $_POST['utsavEndDate'];
    $duration = $_POST['Duration'];
    $letter_dated = $_POST['letter_dated'];
    $form_dated = $_POST['form_dated'];
    $time_of_arrival = $_POST['time_of_arrival'];
    $utsavType = $_POST['utsav_type'];
    $general_start_date = $_POST['general_start_date'];
    $general_end_date = $_POST['general_end_date'];
    $refno = $_POST['refno'];
    $location = $_POST['location'];

    $upasanamorning_brothers = $_POST['upasanamorning_brothers'];
    $upasanamorning_sisters = $_POST['upasanamorning_sisters'];
    $sevamorning_brothers = $_POST['sevamorning_brothers'];
    $sevamorning_sisters = $_POST['sevamorning_sisters'];
    $anugrahamorning_brothers = $_POST['anugrahamorning_brothers'];
    $anugrahamorning_sisters = $_POST['anugrahamorning_sisters'];
    $overlapmorning_brothers = $_POST['overlapmorning_brothers'];
    $overlapmorning_sisters = $_POST['overlapmorning_sisters'];

    $upasanaafternoon_brothers = $_POST['upasanaafternoon_brothers'];
    $upasanaafternoon_sisters = $_POST['upasanaafternoon_sisters'];
    $sevaafternoon_brothers = $_POST['sevaafternoon_brothers'];
    $sevaafternoon_sisters = $_POST['sevaafternoon_sisters'];
    $anugrahaafternoon_brothers = $_POST['anugrahaafternoon_brothers'];
    $anugrahaafternoon_sisters = $_POST['anugrahaafternoon_sisters'];
    $overlapafternoon_brothers = $_POST['overlapafternoon_brothers'];
    $overlapafternoon_sisters = $_POST['overlapafternoon_sisters'];

    $upasanaevening_brothers = $_POST['upasanaevening_brothers'];
    $upasanaevening_sisters = $_POST['upasanaevening_sisters'];
    $sevaevening_brothers = $_POST['sevaevening_brothers'];
    $sevaevening_sisters = $_POST['sevaevening_sisters'];
    $anugrahaevening_brothers = $_POST['anugrahaevening_brothers'];
    $anugrahaevening_sisters = $_POST['anugrahaevening_sisters'];
    $overlapevening_brothers = $_POST['overlapevening_brothers'];
    $overlapevening_sisters = $_POST['overlapevening_sisters'];

    if ($total_brothers_sisters != null && $total_brothers_sisters != 0) {
        if ($branch_code != null && $branch_code != "") {
            if ($occasionId == "general") {
                $derived_occasionId = "0";
                $batchNumber = "0";
                $startMonth = "";
                $year = "";
                $formatGeneralExistanceQuery = "SELECT id, occasion FROM occasions 
                                                WHERE occasion LIKE ('%General%') 
                                                AND start_date = STR_TO_DATE('" . $general_start_date . "', '%d-%M-%Y') 
                                                AND end_date = STR_TO_DATE('" . $general_end_date . "', '%d-%M-%Y')";
                $result_generalId = mysqli_query($db, $formatGeneralExistanceQuery);
                $general_id = mysqli_fetch_assoc($result_generalId);
                if ($general_id != null) {
                    $derived_occasionId = $general_id['id'];
                    $dateTimeStartDate = new DateTime($general_start_date);
                    $startMonth = $dateTimeStartDate->format("M");
                    $year = $dateTimeStartDate->format("Y");
                    $utsavType = "{$occasionId}-{$startMonth}-{$year}-{$formrefno}";
                } else {
                    $dateTimeStartDate = new DateTime($general_start_date);
                    $dateTimeEndDate = new DateTime($general_end_date);
                    $startDate = $dateTimeStartDate->format("d");
                    $startMonth = $dateTimeStartDate->format("M");
                    $endDate = $dateTimeEndDate->format("d");
                    $endMonth = $dateTimeEndDate->format("M");
                    $year = $dateTimeStartDate->format("Y");
                    $occasion_name = "{$occasionId}-{$startMonth}-{$year}-{$formrefno}";
                    $utsavType = $occasion_name;
                    $occasion_key = "{$occasioncode}"."$startDate"."$startMonth"."$endDate"."$endMonth"."$year";
                    $formatOccasionQuery = "INSERT INTO occasions (occasion,start_date,end_date,created_by,created_date,occasion_key,occasion_code)
                                            VALUES('$occasion_name',STR_TO_DATE('" . $general_start_date . "', '%d-%M-%Y'),STR_TO_DATE('" . $general_end_date . "', '%d-%M-%Y'),'$username','$current_datetime','$occasion_key','$occasioncode')";
                    if(mysqli_query($db, $formatOccasionQuery)) {
                        $derived_occasionId = mysqli_insert_id($db);                        
                    } else {
                        echo '<script>alert("ERROR in Occasion creation");</script>';
                        //break;
                    }
                }
                $formatFormInsertQuery = "INSERT INTO form_submissions (sevautsav_id, occasion_id, branch_code, branch,
                                        brothers, sisters, total_people, start_date, end_date, duration, letter_dated, 
                                        form_dated, utsav_type, refno, location, created_by, created_date)
                                        VALUES ($derived_occasionId, $derived_occasionId, '$branch_code', '$shakha', $total_brothers, $total_sisters,
                                        $total_brothers_sisters, STR_TO_DATE('" . $general_start_date . "', '%d-%M-%Y'), STR_TO_DATE('" . $general_end_date . "', '%d-%M-%Y'),
                                        $duration, STR_TO_DATE('" . $letter_dated . "', '%d-%M-%Y'),STR_TO_DATE('" . $form_dated . "', '%d-%M-%Y'),
                                        '$utsavType', '$refno', '$location', '$usern', '$current_datetime');";
                if(mysqli_query($db, $formatFormInsertQuery)) {
                    $lastFormId = mysqli_insert_id($db);
                    $formatArrivalInsertQuery = "INSERT INTO form_arrival_time(form_submission_id,time_of_arrival,
                                                upasana_bandhu,upasana_bhagini,seva_bandhu,seva_bhagini,anugraha_bandhu,anugraha_bhagini,
                                                overlap_bandhu,overlap_bhagini, created_by, created_date)
                                                VALUES($lastFormId,1,$upasanamorning_brothers,$upasanamorning_sisters,$sevamorning_brothers,
                                                $sevamorning_sisters,$anugrahamorning_brothers,$anugrahamorning_sisters,$overlapmorning_brothers,
                                                $overlapmorning_sisters,'$usern','$current_datetime'),
                                                ($lastFormId,2,$upasanaafternoon_brothers,$upasanaafternoon_sisters,$sevaafternoon_brothers,
                                                $sevaafternoon_sisters,$anugrahaafternoon_brothers,$anugrahaafternoon_sisters,
                                                $overlapafternoon_brothers,$overlapafternoon_sisters,'$usern','$current_datetime'),
                                                ($lastFormId,3,$upasanaevening_brothers,$upasanaevening_sisters,$sevaevening_brothers,
                                                $sevaevening_sisters,$anugrahaevening_brothers,$anugrahaevening_sisters,$overlapevening_brothers,
                                                $overlapevening_sisters,'$usern','$current_datetime')";
                    if(mysqli_query($db, $formatArrivalInsertQuery)) {
                        echo '<script>alert("Data Saved Successfully with Form ID = ' . $refno . '");</script>';
                    } else {
                        echo '<script>alert("ERROR in Form Arrival save");</script>';
                    }
                } else {
                    echo '<script>alert("ERROR in Form creation");</script>';
                }
            } else {
                $formatInsertQuery = "INSERT INTO form_submissions (sevautsav_id, occasion_id, branch_code, branch,
                                    brothers, sisters, total_people, start_date, end_date, duration, letter_dated, 
                                    form_dated, utsav_type, refno, location, created_by, created_date)
                                    VALUES ($occasionId, $occasionId, '$branch_code', '$shakha', $total_brothers, $total_sisters,
                                    $total_brothers_sisters, STR_TO_DATE('" . $start_date . "', '%d-%M-%Y'), STR_TO_DATE('" . $end_date . "', '%d-%M-%Y'),
                                    $duration, STR_TO_DATE('" . $letter_dated . "', '%d-%M-%Y'),STR_TO_DATE('" . $form_dated . "', '%d-%M-%Y'),
                                    '$utsavType','$refno', '$location', '$usern', '$current_datetime');";
                if(mysqli_query($db, $formatInsertQuery)) {
                    $lastId = mysqli_insert_id($db);
                    $formatArrivalInsertQuery = "INSERT INTO form_arrival_time(form_submission_id,time_of_arrival,
                                                upasana_bandhu,upasana_bhagini,seva_bandhu,seva_bhagini,anugraha_bandhu,anugraha_bhagini,
                                                overlap_bandhu,overlap_bhagini, created_by, created_date)
                                                VALUES($lastId,1,$upasanamorning_brothers,$upasanamorning_sisters,$sevamorning_brothers,
                                                $sevamorning_sisters,$anugrahamorning_brothers,$anugrahamorning_sisters,$overlapmorning_brothers,
                                                $overlapmorning_sisters,'$usern','$current_datetime'),
                                                ($lastId,2,$upasanaafternoon_brothers,$upasanaafternoon_sisters,$sevaafternoon_brothers,
                                                $sevaafternoon_sisters,$anugrahaafternoon_brothers,$anugrahaafternoon_sisters,
                                                $overlapafternoon_brothers,$overlapafternoon_sisters,'$usern','$current_datetime'),
                                                ($lastId,3,$upasanaevening_brothers,$upasanaevening_sisters,$sevaevening_brothers,
                                                $sevaevening_sisters,$anugrahaevening_brothers,$anugrahaevening_sisters,$overlapevening_brothers,
                                                $overlapevening_sisters,'$usern','$current_datetime')";
                    if(mysqli_query($db, $formatArrivalInsertQuery)) {
                        echo '<script>alert("Data Saved Successfully with Form ID = ' . $refno . '");</script>';
                    } else {
                        echo '<script>alert("ERROR in Form Arrival save");</script>';
                    }
                } else {
                    echo '<script>alert("ERROR in Form Creation");</script>';
                }
            }
        } else {
            echo '<script>alert("Please select Shakha properly");</script>';
        }
    } else {
        echo '<script>alert("Please add atleast 1 Bandhu or Bhagini");</script>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add New Record</title>
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
        .calendarBackground {
            background-image: url('../assets/img/calendar.png');
            background-repeat: no-repeat;
            background-size: 20px 20px;
            background-position: right;
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

 
  <main id="main" class="main">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <form method="POST" action="addnewrecord.php" accept-charset="UTF-8">
            <?php
                $query_utsav = "SELECT DISTINCT id, occasion, Start_Date,
                                DATE_FORMAT(Start_Date, '%d-%M-%Y') AS FormattedStartDate,
                                End_Date,
                                DATE_FORMAT(End_Date, '%d-%M-%Y') AS FormattedEndDate,
                                DATEDIFF(end_date, start_date)+1 AS Duration,
                                batch, occasion_key, occasion_code,
                                CONCAT_WS(' B - ',
                                IF(LENGTH(`occasion`),`occasion`,NULL),
                                IF(LENGTH(`batch`),`batch`,NULL)) AS CombineName
                                FROM occasions  
                                WHERE occasion NOT LIKE ('%Punyatithi%') 
                                AND occasion NOT LIKE ('%General%')
                                AND DATE_SUB(Start_Date, INTERVAL 5 MONTH) <= CURRENT_DATE()
                                AND End_Date >= CURRENT_DATE()";
                $result_utsav = mysqli_query($db, $query_utsav);
                $row_utsav = mysqli_fetch_array($result_utsav);
                if(mysqli_num_rows($result_utsav) > 0)
                {
            ?>

                <div>
                    <div style="width:100%; position:relative; margin:0 15px 0 15px;">
                        <div style="float:left; width:50%;">Year: <?php echo date("Y"); ?></div>
                        <?php
                        $query_refno = "SELECT MAX(id)+1 AS refno FROM form_submissions";
                        $result_refno = mysqli_query($db, $query_refno);
                        $row_refno = mysqli_fetch_array($result_refno);
                        ?>
                        <input type="hidden" name="formRefNo" id="formRefNo" value="<?php echo htmlspecialchars($row_refno['refno']); ?>">
                        <div style="text-align:right; font-family:none; float:right; width:50%;">Ref No : <input type="text" id="occasionRefNo" name="refno" style="border:0; width:25%; background-color:#b4dbff;" readonly /></div>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="Category">Year</label>
                        <?php echo date("Y"); ?>
                    </div> -->
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                         <select class="form-control" id="ddlOccasion" name="occasionId" onchange="GetQuotaByOccasion(this)">
                            <option value="0">Select</option>
                            <?php
                            mysqli_data_seek($result_utsav, 0 );
                            while ($row_occasion = mysqli_fetch_array($result_utsav)) {
                                echo '<option value="'.$row_occasion['id'].'" startdate="' . $row_occasion['FormattedStartDate'] . '" enddate="' . $row_occasion['FormattedEndDate'] . '" duration="' . $row_occasion['Duration'] . '" occasioncode="' . $row_occasion['occasion_code'] . '">' . htmlspecialchars($row_occasion['CombineName']) . '</option>';
                            }
                            ?>
                            <option value="general" occasioncode="GEN" onchange="GetQuotaByOccasion(this)">General</option>
                         </select>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div id="txtBranchContainer" class="col-md-12">
                                <label for="Category">Shakha</label>
                                <input id="txtBranch" type="text" name="shakha" class="form-control" list="shakha" required
                                    value="<?php htmlspecialchars($_POST['branch']); ?>" branchcode="<?php htmlspecialchars($_POST['unique_code']); ?>" onchange="GetQuotaByShakha(this)" />
                                    <datalist id="shakha">
                                        <?php 
                                            $query_shakha = "SELECT * FROM mandir_branch";
                                            $result_shakha = mysqli_query($db, $query_shakha);
                                            while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                                                $shakha = $row_shakha['branch'];
                                            ?>
                                            <option value="<?php echo $shakha; ?>" branchcode="<?php echo htmlspecialchars($row_shakha['unique_code']); ?>">
                                                <?php echo htmlspecialchars($row_shakha['shakha']); ?>
                                            </option>
                                        <?php
                                            }
                                        ?>
                                    </datalist>
                            </div>
                            <div id="harimandirTextboxContainer" class="col-md-6" style="display: none;">
                                <label for="locationInfo">Location Information:</label>
                                <input type="text" id="locationInfo" name="locationInfo" class="form-control" placeholder="Enter location" onchange="setLocation()"/>
                            </div>
                        </div>
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
                        <input class="form-control calendarBackground" type="text" name="letter_dated" id="datepicker" autocomplete="off" required>
                    </div>
                    <div class="col-md-8">
                        <label for="Category">Date</label>
                        <input class="form-control" name="form_dated" value="<?php echo date('d-F-Y'); ?>" readonly></input>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="Category">Time Of Arrival</label>
                        <select class="form-control" name="time_of_arrival" required>
                            <option value="">Select</option>
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Evening">Evening</option>
                        </select>
                    </div> -->

                    <div class="col-md-4">
                        <label for="Category">No Of Bandhu</label>
                        <input class="form-control" type="number" id="total_brothers" name="total_brothers" min="0" max="100" value="0" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">No Of Bhagini</label>
                        <input class="form-control" type="number" id="total_sisters" name="total_sisters" min="0" max="100" value="0" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Total</label>
                        <input class="form-control" type="number" id="total_brothers_sisters" name="total_brothers_sisters" min="0" max="100" value="0" readonly>
                    </div>

                    <div class="col-md-8" id="stayOtherThanGeneral">
                        <label for="Category">Period of Stay</label>
                        <div id="periodOfStay" class="form-control" readonly>From <?php $convertedStartDate = date("d-F-Y", strtotime($row_utsav['Start_Date']));
                            echo $convertedStartDate; ?> To <?php $convertedEndDate = date("d-F-Y", strtotime($row_utsav['End_Date']));
                            echo $convertedEndDate; ?></div>
                    </div>
                    <div class="col-md-4" id="stayForGeneralStartDate">
                        <label for="Category">Start Date</label>
                        <input class="form-control calendarBackground" type="text" name="general_start_date" id="datepickerStartDate" autocomplete="off" onchange="setMinEndDate()">
                    </div>
                    <div class="col-md-4" id="stayForGeneralEndDate">
                        <label for="Category">End Date</label>
                        <input class="form-control calendarBackground" type="text" name="general_end_date" id="datepickerEndDate" autocomplete="off" onchange="CalculateDuration()">
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Duration</label>
                        <input class="form-control" type="number" id="Duration" name="Duration" min="0" max="100" readonly value="<?php echo $row_utsav['Duration'];?>">
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
                                                            <input class="form-control" type="number" id="upasanamorning_brothers" name="upasanamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanamorning_sisters" name="upasanamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="sevamorning_brothers" name="sevamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevamorning_sisters" name="sevamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="anugrahamorning_brothers" name="anugrahamorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahamorning_sisters" name="anugrahamorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="overlapmorning_brothers" name="overlapmorning_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapmorning_sisters" name="overlapmorning_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="upasanaafternoon_brothers" name="upasanaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaafternoon_sisters" name="upasanaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="sevaafternoon_brothers" name="sevaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaafternoon_sisters" name="sevaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="anugrahaafternoon_brothers" name="anugrahaafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaafternoon_sisters" name="anugrahaafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="overlapafternoon_brothers" name="overlapafternoon_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapafternoon_sisters" name="overlapafternoon_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="upasanaevening_brothers" name="upasanaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="upasanaevening_sisters" name="upasanaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="sevaevening_brothers" name="sevaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="sevaevening_sisters" name="sevaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="anugrahaevening_brothers" name="anugrahaevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="anugrahaevening_sisters" name="anugrahaevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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
                                                            <input class="form-control" type="number" id="overlapevening_brothers" name="overlapevening_brothers" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
                                                        </td>
                                                        <td style="width:20%">
                                                            <input class="form-control" type="number" id="overlapevening_sisters" name="overlapevening_sisters" onchange="CalculateBandhuBhagini(this)" min="0" max="100" value="0" placeholder="0">
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

                    <center>
                        <input type="hidden" name="occasioncode" id="occasioncode" value="">
                        <input type="hidden" name="utsavStartDate" id="utsavStartDate" value="<?php echo $row_utsav['Start_Date'];?>">
                        <input type="hidden" name="utsavEndDate" id="utsavEndDate" value="<?php echo $row_utsav['End_Date'];?>">
                        <input type="hidden" name="utsav_type" id="utsav_type" value="<?php echo $row_utsav['occasion'];?>">
                        <input type="hidden" name="branch_code" id="branch_code">
                        <input type="hidden" name="location" id="location" value="">
                        <input type="hidden" name="anugrahautsav" id="anugrahautsav" value="GDP,HJ,AT,GP,SS,RP,VD,DJ,MS">
                        <button id="btnSave" type="submit" name="reg_user" class="btn btn-success btn-sm">Save</button>
                    </center>    
                    </div>
                    
                </div>
            <?php
                }
                else
                {
                    echo 'Pudhil 5 mahinyat Punyatithi utsavachi tarikh nahi';
                }
            ?>
            </form>
        </div>
    </div>
</main>
<script>
    $(document).ready(function(){
        $('#btnSave').attr('disabled','disabled');
        $('#stayForGeneralStartDate').css('display','none');
        $('#stayForGeneralEndDate').css('display','none');
        $('#periodOfStay').html('');
        $('#Duration').val('');
    });
    function setMinEndDate() {
        var startDate = $('#datepickerStartDate').val();
        GetNewRefCodeOfOccasion($('#ddlOccasion')[0].selectedOptions[0].value, startDate);
        var endDate = $('#datepickerEndDate').val();
        endDate.startDate = startDate;
        var convertedStartDate = new Date(startDate);
        if ($('#ddlOccasion')[0].selectedOptions[0].value != "0") {
            var splitGenRefNo = $('#occasionRefNo').val().split('_');
            $('#occasionRefNo').val('GEN_' + convertedStartDate.toLocaleString('default', { month: 'short' }) + '_' + convertedStartDate.getFullYear() + '_' + splitGenRefNo[1]);
        }
        endDate.startDate = convertedStartDate;
    }
    $('#btnSave').click(function(){
        if(ValidateInputData()){
            return true;
        }
        return false;
    });
    function ValidateInputData() {
        var returnFlag = true;
        if ($('#total_brothers_sisters').val() == "0") {
            returnFlag = false;
            alert('Please add atleast 1 Bandhu or Bhagini');
        }
        if ($('#ddlOccasion')[0].selectedOptions[0].attributes['occasioncode'].value == "BS" && (parseInt($('#total_brothers').val()) > parseInt($('#bandhu_available').val()))) {
            alert('Bandhu count is greater than available Bandhu Count:(' + $('#bandhu_available').val() + ')');
        }
        if ($('#ddlOccasion')[0].selectedOptions[0].attributes['occasioncode'].value == "BS" && (parseInt($('#total_sisters').val()) > parseInt($('#bhagini_available').val()))) {
            alert('Bhagini count is greater than available Bhagini Count:(' + $('#bhagini_available').val() + ')');
        }
        if ($('#ddlOccasion')[0].selectedOptions[0].attributes['occasioncode'].value == "GEN" &&
        IsStartDateGreaterThanEndDate() && returnFlag) {
                returnFlag = false;
                alert('Please select End Date greater than From Date');
        }
        return returnFlag;
    }
    function IsStartDateGreaterThanEndDate() {
        var startDate = $('#datepickerStartDate').val();
        var endDate = $('#datepickerEndDate').val();
        var convertedStartDate = new Date(startDate);
        var convertedEndDate = new Date(endDate);
        if (convertedStartDate > convertedEndDate)
            return true;
        else
            return false;
    }
    $(function () {
      $("#datepicker").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        endDate: "today"
      });
      $("#datepickerStartDate,#datepickerEndDate").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        startDate: "today"
      });
    });
    
    function GetQuotaByOccasion(e) {
        if (e.selectedOptions[0].value != "0") {
            if (e.selectedOptions[0].value == "general") {
                $('#stayForGeneralStartDate').css('display','');
                $('#datepickerStartDate').val('');
                $('#datepickerStartDate').attr('required', 'required');
                $('#stayForGeneralEndDate').css('display','');
                $('#datepickerEndDate').attr('required', 'required');
                $('#stayOtherThanGeneral').css('display','none');
                $('#quota_total').val('0');
                $('#bandhu_available').val('0');
                $('#bhagini_available').val('0');
                $('#quota_available').val('0');
                $('#btnSave').removeAttr('disabled');
                $('#occasioncode').val('GEN');
                $('#occasionRefNo').val('GEN_');
                $('#Duration').val('0');
                if ($('#datepickerStartDate').val() != "" && $('#datepickerStartDate').val() != null) {
                    var convertedStartDate = new Date($('#datepickerStartDate').val());
                    $('#occasionRefNo').val('GEN_' + convertedStartDate.toLocaleString('default', { month: 'short' }) + '_' + convertedStartDate.getFullYear() + '_');
                }
            } else {
                $('#datepickerStartDate').removeAttr('required');
                $('#stayForGeneralStartDate').css('display','none');
                $('#datepickerEndDate').removeAttr('required');
                $('#stayForGeneralEndDate').css('display','none');
                $('#stayOtherThanGeneral').css('display','');
                var startDate = e.selectedOptions[0].attributes['startdate'].value;
                var endDate = e.selectedOptions[0].attributes['enddate'].value;
                $('#periodOfStay').html('From ' + startDate + ' To ' + endDate );
                $('#occasionRefNo').val(e.selectedOptions[0].attributes['occasioncode'].value + '_');
                $('#occasioncode').val(e.selectedOptions[0].attributes['occasioncode'].value);
                $('#Duration').val(e.selectedOptions[0].attributes['duration'].value);
                $('#utsavStartDate').val(startDate);
                $('#utsavEndDate').val(endDate);
                $('#utsav_type').val(e.selectedOptions[0].text);
                if (e.selectedOptions[0].attributes['occasioncode'].value != "BS") {
                    $('#btnSave').removeAttr('disabled');
                }                
            }
            
            var arr_anugraha_utsav = $('#anugrahautsav').val().split(',');
            if (arr_anugraha_utsav.indexOf(e.selectedOptions[0].attributes['occasioncode'].value) !== -1) {
                $('[id^=anugraha_]').val('0');
                $('[id^=overlap_]').val('0');
                $('[row^=shownoshow]').css('opacity','');
                $('[id^=anugrahamorning_brothers]').removeAttr('readonly');
                $('[id^=anugrahamorning_sisters]').removeAttr('readonly');
                $('[id^=overlapmorning_brothers]').removeAttr('readonly');
                $('[id^=overlapmorning_sisters]').removeAttr('readonly');
                $('[id^=anugrahaafternoon_brothers]').removeAttr('readonly');
                $('[id^=anugrahaafternoon_sisters]').removeAttr('readonly');
                $('[id^=overlapafternoon_brothers]').removeAttr('readonly');
                $('[id^=overlapafternoon_sisters]').removeAttr('readonly');
                $('[id^=anugrahevening_brothers]').removeAttr('readonly');
                $('[id^=anugrahevening_sisters]').removeAttr('readonly');
                $('[id^=overlaevening_brothers]').removeAttr('readonly');
                $('[id^=overlaevening_sisters]').removeAttr('readonly');
            } else {
                $('[id^=anugraha_]').val('0');
                $('[id^=overlap_]').val('0');
                $('[row^=shownoshow]').css('opacity','0.2');
                $('[id^=anugrahamorning_brothers]').attr('readonly', true);
                $('[id^=anugrahamorning_sisters]').attr('readonly', true);
                $('[id^=overlapmorning_brothers]').attr('readonly', true);
                $('[id^=overlapmorning_sisters]').attr('readonly', true);
                $('[id^=anugrahaafternoon_brothers]').attr('readonly', true);
                $('[id^=anugrahaafternoon_sisters]').attr('readonly', true);
                $('[id^=overlapafternoon_brothers]').attr('readonly', true);
                $('[id^=overlapafternoon_sisters]').attr('readonly', true);
                $('[id^=anugrahevening_brothers]').attr('readonly', true);
                $('[id^=anugrahevening_sisters]').attr('readonly', true);
                $('[id^=overlaevening_brothers]').attr('readonly', true);
                $('[id^=overlaevening_sisters]').attr('readonly', true);
            }
            CalculateBandhuBhagini();
            if ($('#txtBranch').val() != "" && e.selectedOptions[0].attributes['occasioncode'].value == "BS") {
                GetQuotaByShakhaAndBranch(GetAndSetBranchCode($('#txtBranch').val()), e.selectedOptions[0].value);
            } else {
                GetAndSetBranchCode($('#txtBranch').val());
            }
            GetNewRefCodeOfOccasion(e.selectedOptions[0].value, null);
        } else {
            $('#btnSave').attr('disabled','disabled');
            $('#quota_total').val('0');
            $('#bandhu_available').val('0');
            $('#bhagini_available').val('0');
            $('#quota_available').val('0');
            $('#occasionRefNo').val('');
        }
    }
    function setLocation () {
        document.getElementById("location").value = document.getElementById("locationInfo").value;
    }
    function GetQuotaByShakha(e) {
        const shakhaInput = document.getElementById("txtBranch");
        const txtBranchContainer = document.getElementById("txtBranchContainer");
        const harimandirTextboxContainer = document.getElementById("harimandirTextboxContainer");

        // Check if the selected value is 'harimandir'
        if (shakhaInput.value.toLowerCase() === "belagavi shriharimandir") {
            // Show the text box and make input occupy half width
            harimandirTextboxContainer.style.display = "block";
            txtBranchContainer.className = "col-md-6"; // Adjust to occupy half width
        } else {
            // Hide the text box and make input occupy full width
            harimandirTextboxContainer.style.display = "none";
            txtBranchContainer.className = "col-md-12"; // Adjust to occupy full width
            document.getElementById("location").value = "";
        }
        if ($('#ddlOccasion')[0].selectedOptions[0].value != "0" && $('#ddlOccasion')[0].selectedOptions[0].attributes['occasioncode'].value == "BS") {
            GetQuotaByShakhaAndBranch(GetAndSetBranchCode(e.value), $('#ddlOccasion')[0].selectedOptions[0].value);
        } else {
            GetAndSetBranchCode($('#txtBranch').val());
        }
    }
    function GetQuotaByShakhaAndBranch(branchcode, occasionid) {
        $.ajax({
            url: 'fetch_quota.php', // URL to PHP script that fetches dates
            type: 'POST',
            data: {branchcode: branchcode, occasionid: occasionid},
            success: function(response) {
                var res = JSON.parse(response);
                if(res.occasionquotaid != null) {
                    $('#quota_total').val(res.total);
                    $('#quota_available').val(res.availabletotal);
                    $('#bandhu_available').val(res.availablebandhu);
                    $('#bhagini_available').val(res.availablebhagini);
                    $('#btnSave').removeAttr('disabled');
                } else {
                    $('#quota_total').val(0);
                    $('#quota_available').val(0);
                    $('#bandhu_available').val(0);
                    $('#bhagini_available').val(0);
                    $('#btnSave').attr('disabled','disabled');
                    alert('Quota not available for selected Occasion and Shakha');
                }
            },
            error: function() {
                alert('Error fetching dates.');
            }
        });
    }
    function GetNewRefCodeOfOccasion(selectedOccasionId, selectedStartDate) {
        $.ajax({
            url: 'getnewrefnoofoccasion.php', // URL to PHP script that fetches dates
            type: 'POST',
            async: false,
            data: {occasionId: selectedOccasionId, startDate: selectedStartDate},
            success: function(response) {
                var res = JSON.parse(response);
                $('#occasionRefNo').val(res.refno);
            },
            error: function() {
                alert('Error fetching in RefCode.');
            }
        });
    }
    function GetAndSetBranchCode(branchName) {
        var branch_code = "";
        $('#shakha option').filter(function(){
            if (this.value.toUpperCase() === branchName.toUpperCase()) {
                branch_code = this.attributes["branchcode"].value;
            }       
        });
        $('#branch_code').val(branch_code);
        return branch_code;
    }
    function CalculateBandhuBhagini(e) {
        if (e != undefined) {
            var splitId = e.id.split('_');
            $('#' + splitId[0] + '_total').val(parseInt($('#' + splitId[0] + '_brothers').val()) + parseInt($('#' + splitId[0] + '_sisters').val()));
        }
        $('#total_brothers').val(parseInt($('#upasanamorning_brothers').val()) + parseInt($('#upasanaafternoon_brothers').val()) + parseInt($('#upasanaevening_brothers').val()) +
                                parseInt($('#sevamorning_brothers').val()) + parseInt($('#sevaafternoon_brothers').val()) + parseInt($('#sevaevening_brothers').val()) +
                                parseInt($('#anugrahamorning_brothers').val()) + parseInt($('#anugrahaafternoon_brothers').val()) + parseInt($('#anugrahaevening_brothers').val()) + 
                                parseInt($('#overlapmorning_brothers').val()) + parseInt($('#overlapafternoon_brothers').val()) + parseInt($('#overlapevening_brothers').val()));
        $('#total_sisters').val(parseInt($('#upasanamorning_sisters').val()) + parseInt($('#upasanaafternoon_sisters').val()) + parseInt($('#upasanaevening_sisters').val()) +
                                parseInt($('#sevamorning_sisters').val()) + parseInt($('#sevaafternoon_sisters').val()) + parseInt($('#sevaevening_sisters').val()) +
                                parseInt($('#anugrahamorning_sisters').val()) + parseInt($('#anugrahaafternoon_sisters').val()) + parseInt($('#anugrahaevening_sisters').val()) + 
                                parseInt($('#overlapmorning_sisters').val()) + parseInt($('#overlapafternoon_sisters').val()) + parseInt($('#overlapevening_sisters').val()));
        $('#total_brothers_sisters').val(parseInt($('#upasanamorning_total').val()) + parseInt($('#upasanaafternoon_total').val()) + parseInt($('#upasanaevening_total').val()) +
                                        parseInt($('#sevamorning_total').val()) + parseInt($('#sevaafternoon_total').val()) + parseInt($('#sevaevening_total').val()) + 
                                        parseInt($('#anugrahamorning_total').val()) + parseInt($('#anugrahaafternoon_total').val()) + parseInt($('#anugrahaevening_total').val()) +
                                        parseInt($('#overlapmorning_total').val()) + parseInt($('#overlapafternoon_total').val()) + parseInt($('#overlapevening_total').val()));
    }
    function CalculateDuration() {
        if ($('#datepickerStartDate').val() != "") {
            var startDate = new Date($('#datepickerStartDate').val())
            var endDate = new Date($('#datepickerEndDate').val())
            var diffDate = (endDate - startDate) / (1000 * 60 * 60 * 24);
            var days = Math.round(diffDate)+1;
            $('#Duration').val(days);
        }
    }
  </script>

  <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>


  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer"></footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>