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
  $page_visit = "punyatithi";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

$current_datetime = date('Y-m-d H:i:s');

$occasion_name = "";

$formatInsertQueryParent = "Insert into form_submissions (sevautsav_id, occasion_id, branch_code, branch, brothers, sisters, total_people, start_date, end_date, letter_dated, form_dated, utsav_type, refno,location, created_by, created_date) values"; 
$formatInsertQueryChild = "Insert into utsavbandhubhagini (form_submission_id, name, age, gender, seva, time_of_arrival, isDeleted, start_date, end_date, created_by, created_date) values";

if(isset($_POST['save_BandhuBhaginiData']))
{
    $occasioncode = $_POST['occasioncode'];
    $occasionId = $_POST['occasionId'];
    $shakha = $_POST['shakha'];
    $branch_code = $_POST['branch_code'];
    $brothers = $_POST['brothers'];
    $sisters = $_POST['sisters'];
    $total_people = $_POST['total_people'];
    $start_date = $_POST['utsavStartDate'];
    $end_date = $_POST['utsavEndDate'];
    $letter_dated = $_POST['letter_dated'];
    $form_dated = $_POST['form_dated'];
    $time_of_arrival = $_POST['time_of_arrival'];
    $utsavType = $_POST['utsav_type'];
    $refno = $_POST['refno'];
    $arrBandhuBhaginiData = $_POST['arrBandhuBhaginiData'];
    $formStartDate = $_POST['start_date'];
    $formEndDate = $_POST['end_date'];
	$locationInfo= $_POST['locationInfo'];


    $morning_upasana_brothers = $_POST['morning_upasana_brothers'];
    $morning_upasana_sisters = $_POST['morning_upasana_sisters'];
    $morning_seva_brothers = $_POST['morning_seva_brothers'];
    $morning_seva_sisters = $_POST['morning_seva_sisters'];
    $morning_anugraha_brothers = $_POST['morning_anugraha_brothers'];
    $morning_anugraha_sisters = $_POST['morning_anugraha_sisters'];
    $morning_overlap_brothers = $_POST['morning_overlap_brothers'];
    $morning_overlap_sisters = $_POST['morning_overlap_sisters'];

    $afternoon_upasana_brothers = $_POST['afternoon_upasana_brothers'];
    $afternoon_upasana_sisters = $_POST['afternoon_upasana_sisters'];
    $afternoon_seva_brothers = $_POST['afternoon_seva_brothers'];
    $afternoon_seva_sisters = $_POST['afternoon_seva_sisters'];
    $afternoon_anugraha_brothers = $_POST['afternoon_anugraha_brothers'];
    $afternoon_anugraha_sisters = $_POST['afternoon_anugraha_sisters'];
    $afternoon_overlap_brothers = $_POST['afternoon_overlap_brothers'];
    $afternoon_overlap_sisters = $_POST['afternoon_overlap_sisters'];

    $evening_upasana_brothers = $_POST['evening_upasana_brothers'];
    $evening_upasana_sisters = $_POST['evening_upasana_sisters'];
    $evening_seva_brothers = $_POST['evening_seva_brothers'];
    $evening_seva_sisters = $_POST['evening_seva_sisters'];
    $evening_anugraha_brothers = $_POST['evening_anugraha_brothers'];
    $evening_anugraha_sisters = $_POST['evening_anugraha_sisters'];
    $evening_overlap_brothers = $_POST['evening_overlap_brothers'];
    $evening_overlap_sisters = $_POST['evening_overlap_sisters'];

    $selectedBatch = $_POST['ddlBatches'];
    $calculatedFromDate = "";
    $calculatedToDate = "";
    if ($selectedBatch == "1") {
        $calculatedFromDate = "'" . $start_date . "'";
        $calculatedToDate = "'" . $end_date . "'";
    } else if ($selectedBatch == "2") {
        $calculatedFromDate = "'" . $start_date . "'";
        $calculatedToDate = "DATE_ADD('$start_date', INTERVAL 3 DAY)";
    } else if ($selectedBatch == "3") {
        $calculatedFromDate = "DATE_ADD('$start_date' , INTERVAL 3 DAY)";
        $calculatedToDate = "'" . $end_date . "'";
    }
    // print($start_date);
    // print($end_date);
    //print($calculatedFromDate);
    //print($calculatedToDate);

    if ($branch_code != null && $branch_code != "") {
        if ($arrBandhuBhaginiData != "" && $arrBandhuBhaginiData != null) {
            // $formatInsertQueryParent = $formatInsertQueryParent . "(" . $occasionId . "," . $occasionId . ",'" . $branch_code . "','" . $shakha . "'," . $brothers . "," . $sisters . "," . $total_people . ",STR_TO_DATE('" . $formStartDate . "', '%d-%M-%Y'),STR_TO_DATE('" . $formEndDate . "', '%d-%M-%Y')," . $duration . ",STR_TO_DATE('" . $letter_dated . "', '%d-%M-%Y'),STR_TO_DATE('" . $form_dated . "', '%d-%M-%Y'),'" . $time_of_arrival . "','" . $utsavType . "','" . $refno . "'); ";
            $formatInsertQueryParent = $formatInsertQueryParent . "(" . $occasionId . "," . $occasionId . ",'" . $branch_code . "','" . $shakha . "'," . $brothers . "," . $sisters . "," . $total_people . "," . $calculatedFromDate . "," . $calculatedToDate . ",STR_TO_DATE('" . $letter_dated . "', '%d-%M-%Y'),STR_TO_DATE('" . $form_dated . "', '%d-%M-%Y'),'" . $utsavType . "','" . $refno . "','" . $locationInfo . "','" . $usern . "','" . $current_datetime . "'); ";
 //print($formatInsertQueryParent);
            if(mysqli_query($db, $formatInsertQueryParent)) {
                $lastId = mysqli_insert_id($db);
                if ($arrBandhuBhaginiData != "" && $arrBandhuBhaginiData != null) {
                    $bandhuBhaginiValues = "";
                    $splitByDollar = explode('$', $arrBandhuBhaginiData);
                    foreach($splitByDollar as &$data) {
                        $splitByPipe = explode('|', $data);
                        if ($selectedBatch == "1") {
                            $formStartDate = "DATE_SUB($calculatedFromDate, INTERVAL " . $splitByPipe[5] . " DAY)";
                            $formEndDate = "DATE_ADD($calculatedToDate, INTERVAL " . $splitByPipe[6] . " DAY)";
                        } else if ($selectedBatch == "2") {
                            $formStartDate = "DATE_SUB($calculatedFromDate, INTERVAL 1 DAY)";
                            $formEndDate = $calculatedToDate;
                        } else if ($selectedBatch == "3") {
                            $formStartDate = $calculatedFromDate;
                            $formEndDate = "DATE_ADD($calculatedToDate, INTERVAL 1 DAY)";
                        }
                        $bandhuBhaginiValues = $bandhuBhaginiValues == "" ? "(" . $lastId . ",'" . $splitByPipe[0] . "'," . $splitByPipe[1] . "," . $splitByPipe[2] . "," . $splitByPipe[3] . "," . $splitByPipe[4] . ",false," . $formStartDate . "," . $formEndDate . ",'" . $usern . "','" . $current_datetime . "')" : $bandhuBhaginiValues . ",(" . $lastId . ",'" . $splitByPipe[0] . "'," . $splitByPipe[1] . "," . $splitByPipe[2] . "," . $splitByPipe[3] . "," . $splitByPipe[4] . ",false," . $formStartDate . "," . $formEndDate .",'" . $usern . "','" . $current_datetime . "')";
                    }
                    $formatInsertQueryChild = $formatInsertQueryChild . $bandhuBhaginiValues . ";";
                     //print($formatInsertQueryChild);
                }
                if(mysqli_query($db, $formatInsertQueryChild)) {
                    $formatArrivalInsertQuery = "INSERT INTO form_arrival_time(form_submission_id,time_of_arrival,
                                                upasana_bandhu,upasana_bhagini,seva_bandhu,seva_bhagini,anugraha_bandhu,anugraha_bhagini,
                                                overlap_bandhu,overlap_bhagini,created_by,created_date)
                                                VALUES($lastId,1,$morning_upasana_brothers,$morning_upasana_sisters,$morning_seva_brothers,
                                                $morning_seva_sisters,$morning_anugraha_brothers,$morning_anugraha_sisters,$morning_overlap_brothers,
                                                $morning_overlap_sisters,'$usern','$current_datetime'),
                                                ($lastId,2,$afternoon_upasana_brothers,$afternoon_upasana_sisters,$afternoon_seva_brothers,
                                                $afternoon_seva_sisters,$afternoon_anugraha_brothers,$afternoon_anugraha_sisters,
                                                $afternoon_overlap_brothers,$afternoon_overlap_sisters,'$usern','$current_datetime'),
                                                ($lastId,3,$evening_upasana_brothers,$evening_upasana_sisters,$evening_seva_brothers,
                                                $evening_seva_sisters,$evening_anugraha_brothers,$evening_anugraha_sisters,$evening_overlap_brothers,
                                                $evening_overlap_sisters,'$usern','$current_datetime')";
                    if(mysqli_query($db, $formatArrivalInsertQuery)) {
                        echo '<script>alert("Data Saved Successfully with Form ID = ' . $refno . '");</script>';
                    } else {
                        echo '<script>alert("ERROR in Arrival time count Data Save");</script>';
                    }
                } else {
                    echo '<script>alert("ERROR in Bandhu-Bhagini Infromation Data Save");</script>';
                }
            } else {
                echo '<script>alert("ERROR in Form Submission Data Save");</script>';
            }
        }
    } else {
        echo '<script>alert("Please select Shakha properly");</script>';
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
            <form method="POST" action="punyatithi.php" accept-charset="UTF-8">
            <?php
                $query_utsav = "SELECT DISTINCT id, occasion, Start_Date,
                                DATE_FORMAT(Start_Date, '%d-%M-%Y') AS FormattedStartDate,
                                End_Date,
                                DATE_FORMAT(End_Date, '%d-%M-%Y') AS FormattedEndDate,
                                DATEDIFF(end_date, start_date)+1 AS Duration,
                                batch, occasion_key, occasion_code,
                                CONCAT_WS(' B - ',
                                IF(LENGTH(`occasion`),`occasion`,NULL),
                                IF(LENGTH(`batch`),`batch`,NULL)) AS CombineName,
                                CONCAT('Utsav',' - From - ',DATE_FORMAT(Start_Date, '%d-%M-%Y'),' - To - ',DATE_FORMAT(end_date, '%d-%M-%Y'),' - ',DATEDIFF(end_date, start_date)+1,' Days') AS Batch1,
                                CONCAT('Batch - 1',' - From - ',DATE_FORMAT(Start_Date, '%d-%M-%Y'),' - To - ',DATE_FORMAT(DATE_ADD(Start_Date, INTERVAL 3 DAY), '%d-%M-%Y'),' - ',DATEDIFF(DATE_ADD(Start_Date, INTERVAL 3 DAY), start_date)+1,' Days') AS Batch2,
                                CONCAT('Batch - 2',' - From - ',DATE_FORMAT(DATE_ADD(Start_Date, INTERVAL 3 DAY), '%d-%M-%Y'),' - To - ',DATE_FORMAT(end_date, '%d-%M-%Y'),' - ',DATEDIFF(end_date,DATE_ADD(Start_Date, INTERVAL 3 DAY))+1,' Days') AS Batch3
                                FROM occasions  
                                WHERE occasion LIKE ('%Punyatithi%') 
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
                        <div style="text-align:right; font-family:none; float:right; width:50%;">Ref No : <input type="text" id="occasionRefNo" name="refno" style="border:0; width:12%; background-color:#b4dbff;" readonly /></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                         <select class="form-control" id="ddlOccasion" name="occasionId" onchange="GetQuotaByOccasion(this)">
                            <!-- <option value="0">Select</option> -->
                            <?php
                            mysqli_data_seek($result_utsav, 0 );
                            while ($row_occasion = mysqli_fetch_array($result_utsav)) {
                                echo '<option value="'.$row_occasion['id'].'" startdate="' . $row_occasion['FormattedStartDate'] . '" enddate="' . $row_occasion['FormattedEndDate'] . '" duration="' . $row_occasion['Duration'] . '" occasioncode="' . $row_occasion['occasion_code'] . '">' . htmlspecialchars($row_occasion['CombineName']) . '</option>';
                            }
                            ?>
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
                                <input type="text" id="locationInfo" name="locationInfo" class="form-control" placeholder="Enter location" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="Category">Batch</label>
                        <select id="ddlBatches" name="ddlBatches" class="form-control" onchange="GetQuotaBySelectedBatch(this)">
                            <option value="0">Select</option>
                            <option value="1"><?php echo $row_utsav['Batch1'];?></option>
                            <option value="2"><?php echo $row_utsav['Batch2'];?></option>
                            <option value="3"><?php echo $row_utsav['Batch3'];?></option>
                        </select>
                    </div>
                    <!-- <div class="col-md-4">
                        <label for="Category">Duration</label>
                        <input class="form-control" type="number" id="Duration" name="Duration" min="0" max="100" readonly value="<?php echo $row_utsav['Duration'];?>">
                    </div> -->
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

                    <div class="col-md-4">
                        <label for="Category">No Of Bandhu</label>
                        <input class="form-control" type="number" id="totalBandhu" name="brothers" min="0" max="100" value="0" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">No Of Bhagini</label>
                        <input class="form-control" type="number" id="totalBhagini" name="sisters" min="0" max="100" value="0" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Total</label>
                        <input class="form-control" type="number" id="totalBandhuBhagini" name="total_people" min="0" max="100" value="0" readonly>
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
                                    <div class="tab-pane show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab" style="padding:5px 5px 0 5px;">
                                        <div>
                                            <div>
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id='addr0_morning'>
                                                            <td style="width:2%">
                                                            1
                                                            </td>
                                                            <td style="width:58%">
                                                                <input type="text" name='name_0_morning' id="name_0_morning" placeholder='Name' class="form-control"/>
                                                            </td>
                                                            <td style="width:10%">
                                                                <input type="number" min="15" max="99" name='age_0_morning' id="age_0_morning" placeholder='Age' class="form-control"/>
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="gender_0_morning" id="gender_0_morning" onchange="CalculateBandhuBhaginiCount_morning(this)">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_gender = "SELECT id, Gender FROM gendermaster";
                                                                    $result_gender = mysqli_query($db, $query_gender);
                                                                    while ($row_gender = mysqli_fetch_assoc($result_gender)) {
                                                                        echo '<option value="'.$row_gender['id'].'">' . htmlspecialchars($row_gender['Gender']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>  
                                                                <input type="hidden" id="selectedGender_0_morning" name="selectedGender_0_morning" />  
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="sevaUpasnaAnugraha_0_morning" id="sevaUpasnaAnugraha_0_morning">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_prakar = "SELECT id, type FROM prakarmaster";
                                                                    $result_prakar = mysqli_query($db, $query_prakar);
                                                                    while ($row_prakar = mysqli_fetch_assoc($result_prakar)) {
                                                                        echo '<option value="'.$row_prakar['id'].'">' . htmlspecialchars($row_prakar['type']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <tr id='addr1_morning'></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div>
                                                <a id="add_row_morning" class="btn btn-primary pull-left btn-sm">Add Row</a>
                                                <a id='delete_row_morning' class="pull-right btn btn-danger btn-sm">Delete Row</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2-tab" style="padding:5px 5px 0 5px;">
                                        <div>
                                            <div>
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id='addr0_afternoon'>
                                                            <td style="width:2%">
                                                            1
                                                            </td>
                                                            <td style="width:58%">
                                                                <input type="text" name='name_0_afternoon' id="name_0_afternoon" placeholder='Name' class="form-control"/>
                                                            </td>
                                                            <td style="width:10%">
                                                                <input type="number" min="15" max="99" name='age_0_afternoon' id="age_0_afternoon" placeholder='Age' class="form-control"/>
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="gender_0_afternoon" id="gender_0_afternoon" onchange="CalculateBandhuBhaginiCount_afternoon(this)">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_gender = "SELECT id, Gender FROM gendermaster";
                                                                    $result_gender = mysqli_query($db, $query_gender);
                                                                    while ($row_gender = mysqli_fetch_assoc($result_gender)) {
                                                                        echo '<option value="'.$row_gender['id'].'">' . htmlspecialchars($row_gender['Gender']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>  
                                                                <input type="hidden" id="selectedGender_0_afternoon" name="selectedGender_0_afternoon" />  
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="sevaUpasnaAnugraha_0_afternoon" id="sevaUpasnaAnugraha_0_afternoon">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_prakar = "SELECT id, type FROM prakarmaster";
                                                                    $result_prakar = mysqli_query($db, $query_prakar);
                                                                    while ($row_prakar = mysqli_fetch_assoc($result_prakar)) {
                                                                        echo '<option value="'.$row_prakar['id'].'">' . htmlspecialchars($row_prakar['type']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <tr id='addr1_afternoon'></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div>
                                                <a id="add_row_afternoon" class="btn btn-primary pull-left btn-sm">Add Row</a>
                                                <a id='delete_row_afternoon' class="pull-right btn btn-danger btn-sm">Delete Row</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tab3" role="tabpanel" aria-labelledby="tab3-tab" style="padding:5px 5px 0 5px;">
                                        <div>
                                            <div>
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id='addr0_evening'>
                                                            <td style="width:2%">
                                                            1
                                                            </td>
                                                            <td style="width:58%">
                                                                <input type="text" name='name_0_evening' id="name_0_evening" placeholder='Name' class="form-control"/>
                                                            </td>
                                                            <td style="width:10%">
                                                                <input type="number" min="15" max="99" name='age_0_evening' id="age_0_evening" placeholder='Age' class="form-control"/>
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="gender_0_evening" id="gender_0_evening" onchange="CalculateBandhuBhaginiCount_evening(this)">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_gender = "SELECT id, Gender FROM gendermaster";
                                                                    $result_gender = mysqli_query($db, $query_gender);
                                                                    while ($row_gender = mysqli_fetch_assoc($result_gender)) {
                                                                        echo '<option value="'.$row_gender['id'].'">' . htmlspecialchars($row_gender['Gender']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>  
                                                                <input type="hidden" id="selectedGender_0_evening" name="selectedGender_0_evening" />  
                                                            </td>
                                                            <td style="width:15%">
                                                                <select class="form-control" name="sevaUpasnaAnugraha_0_evening" id="sevaUpasnaAnugraha_0_evening">
                                                                <option value="0">Select</option>
                                                                <?php
                                                                    $query_prakar = "SELECT id, type FROM prakarmaster";
                                                                    $result_prakar = mysqli_query($db, $query_prakar);
                                                                    while ($row_prakar = mysqli_fetch_assoc($result_prakar)) {
                                                                        echo '<option value="'.$row_prakar['id'].'">' . htmlspecialchars($row_prakar['type']) . '</option>';
                                                                    }
                                                                ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <tr id='addr1_evening'></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div>
                                                <a id="add_row_evening" class="btn btn-primary pull-left btn-sm">Add Row</a>
                                                <a id='delete_row_evening' class="pull-right btn btn-danger btn-sm">Delete Row</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        
                        <center>
                        <!-- <input type="hidden" name="occasionId" id="occasionId" value="<?php echo $row_utsav['id'];?>"> -->
                        <input type="hidden" name="occasioncode" id="occasioncode" value="">
                        <input type="hidden" name="utsavStartDate" id="utsavStartDate" value="<?php echo $row_utsav['Start_Date'];?>">
                        <input type="hidden" name="utsavEndDate" id="utsavEndDate" value="<?php echo $row_utsav['End_Date'];?>">
                        <input type="hidden" name="utsav_type" id="utsav_type" value="<?php echo $row_utsav['occasion'];?>">
                        <input type="hidden" name="arrBandhuBhaginiData" id="arrBandhuBhaginiData">
                        <input type="hidden" name="added_bandhu" id="added_bandhu">
                        <input type="hidden" name="added_bhagini" id="added_bhagini">
                        <input type="hidden" name="added_total" id="added_total">
                        <input type="hidden" name="available_bandhu" id="available_bandhu">
                        <input type="hidden" name="available_bhagini" id="available_bhagini">
                        <input type="hidden" name="available_total" id="available_total">
                        <input type="hidden" name="branch_code" id="branch_code">

                        <input type="hidden" name="bandhu_morning" id="bandhu_morning" value="0">
                        <input type="hidden" name="bhagini_morning" id="bhagini_morning" value="0">
                        <input type="hidden" name="total_morning" id="total_morning" value="0">
                        <input type="hidden" name="bandhu_afternoon" id="bandhu_afternoon" value="0">
                        <input type="hidden" name="bhagini_afternoon" id="bhagini_afternoon" value="0">
                        <input type="hidden" name="total_afternoon" id="total_afternoon" value="0">
                        <input type="hidden" name="bandhu_evening" id="bandhu_evening" value="0">
                        <input type="hidden" name="bhagini_evening" id="bhagini_evening" value="0">
                        <input type="hidden" name="total_evening" id="total_evening" value="0">

                        <input type="hidden" name="morning_upasana_brothers" id="morning_upasana_brothers" value="0">
                        <input type="hidden" name="morning_upasana_sisters" id="morning_upasana_sisters" value="0">
                        <input type="hidden" name="morning_seva_brothers" id="morning_seva_brothers" value="0">
                        <input type="hidden" name="morning_seva_sisters" id="morning_seva_sisters" value="0">
                        <input type="hidden" name="morning_anugraha_brothers" id="morning_anugraha_brothers" value="0">
                        <input type="hidden" name="morning_anugraha_sisters" id="morning_anugraha_sisters" value="0">
                        <input type="hidden" name="morning_overlap_brothers" id="morning_overlap_brothers" value="0">
                        <input type="hidden" name="morning_overlap_sisters" id="morning_overlap_sisters" value="0">

                        <input type="hidden" name="afternoon_upasana_brothers" id="afternoon_upasana_brothers" value="0">
                        <input type="hidden" name="afternoon_upasana_sisters" id="afternoon_upasana_sisters" value="0">
                        <input type="hidden" name="afternoon_seva_brothers" id="afternoon_seva_brothers" value="0">
                        <input type="hidden" name="afternoon_seva_sisters" id="afternoon_seva_sisters" value="0">
                        <input type="hidden" name="afternoon_anugraha_brothers" id="afternoon_anugraha_brothers" value="0">
                        <input type="hidden" name="afternoon_anugraha_sisters" id="afternoon_anugraha_sisters" value="0">
                        <input type="hidden" name="afternoon_overlap_brothers" id="afternoon_overlap_brothers" value="0">
                        <input type="hidden" name="afternoon_overlap_sisters" id="afternoon_overlap_sisters" value="0">

                        <input type="hidden" name="evening_upasana_brothers" id="evening_upasana_brothers" value="0">
                        <input type="hidden" name="evening_upasana_sisters" id="evening_upasana_sisters" value="0">
                        <input type="hidden" name="evening_seva_brothers" id="evening_seva_brothers" value="0">
                        <input type="hidden" name="evening_seva_sisters" id="evening_seva_sisters" value="0">
                        <input type="hidden" name="evening_anugraha_brothers" id="evening_anugraha_brothers" value="0">
                        <input type="hidden" name="evening_anugraha_sisters" id="evening_anugraha_sisters" value="0">
                        <input type="hidden" name="evening_overlap_brothers" id="evening_overlap_brothers" value="0">
                        <input type="hidden" name="evening_overlap_sisters" id="evening_overlap_sisters" value="0">

                        <button id="btnSave" type="submit" name="save_BandhuBhaginiData" class="btn btn-success btn-sm">Save</button>
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

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer"></footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script>
    $(document).ready(function(){
        $('[id^=add_row_]').attr('disabled','disabled');
        $('[id^=delete_row_]').attr('disabled','disabled');
        $('#btnSave').attr('disabled','disabled');
        GetNewRefCodeOfOccasion($('#ddlOccasion')[0].selectedOptions[0].value);
    });
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
    var morning=1, afternoon=1, evening=1;
     $("#add_row_morning").click(function(){
      $('#addr'+morning+'_morning').html("<td>"+ (morning+1) +"</td><td><input name='name_"+morning+"_morning' id='name_"+morning+"_morning' type='text' placeholder='Name' class='form-control input-md'  /></td><td><input name='age_"+morning+"_morning' id='age_"+morning+"_morning' type='number' min='15' max='99' maxlength='2' placeholder='Age' class='form-control input-md'></td><td><select class='form-control' name='gender_"+morning+"_morning' id='gender_"+morning+"_morning' onchange='CalculateBandhuBhaginiCount_morning(this)'><option value='0'>Select</option><option>Male</option><option>Female</option></select><input type='hidden' id='selectedGender_"+morning+"_morning' name='selectedGender_"+morning+"_morning' /></td><td><select class='form-control' name='sevaUpasnaAnugraha_"+morning+"_morning' id='sevaUpasnaAnugraha_"+morning+"_morning'><option value='0'>Select</option><option>Seva</option><option>Upasana</option><option>Anugraha</option><option>Overlap</option></select></td>");

      $('#tblBandhuBhaginiDetails_morning').append('<tr id="addr'+(morning+1)+'_morning"></tr>');
      morning++; 
    });
    $("#add_row_afternoon").click(function(){
      $('#addr'+afternoon+'_afternoon').html("<td>"+ (afternoon+1) +"</td><td><input name='name_"+afternoon+"_afternoon' id='name_"+afternoon+"_afternoon' type='text' placeholder='Name' class='form-control input-md'  /></td><td><input name='age_"+afternoon+"_afternoon' id='age_"+afternoon+"_afternoon' type='number' min='15' max='99' maxlength='2' placeholder='Age' class='form-control input-md'></td><td><select class='form-control' name='gender_"+afternoon+"_afternoon' id='gender_"+afternoon+"_afternoon' onchange='CalculateBandhuBhaginiCount_afternoon(this)'><option value='0'>Select</option><option>Male</option><option>Female</option></select><input type='hidden' id='selectedGender_"+afternoon+"_afternoon' name='selectedGender_"+afternoon+"_afternoon' /></td><td><select class='form-control' name='sevaUpasnaAnugraha_"+afternoon+"_afternoon' id='sevaUpasnaAnugraha_"+afternoon+"_afternoon'><option value='0'>Select</option><option>Seva</option><option>Upasana</option><option>Anugraha</option><option>Overlap</option></select></td>");

      $('#tblBandhuBhaginiDetails_afternoon').append('<tr id="addr'+(afternoon+1)+'_afternoon"></tr>');
      afternoon++; 
    });
    $("#add_row_evening").click(function(){
      $('#addr'+evening+'_evening').html("<td>"+ (evening+1) +"</td><td><input name='name_"+evening+"_evening' id='name_"+evening+"_evening' type='text' placeholder='Name' class='form-control input-md'  /></td><td><input name='age_"+evening+"_evening' id='age_"+evening+"_evening' type='number' min='15' max='99' maxlength='2' placeholder='Age' class='form-control input-md'></td><td><select class='form-control' name='gender_"+evening+"_evening' id='gender_"+evening+"_evening' onchange='CalculateBandhuBhaginiCount_evening(this)'><option value='0'>Select</option><option>Male</option><option>Female</option></select><input type='hidden' id='selectedGender_"+evening+"_evening' name='selectedGender_"+evening+"_evening' /></td><td><select class='form-control' name='sevaUpasnaAnugraha_"+evening+"_evening' id='sevaUpasnaAnugraha_"+evening+"_evening'><option value='0'>Select</option><option>Seva</option><option>Upasana</option><option>Anugraha</option><option>Overlap</option></select></td>");

      $('#tblBandhuBhaginiDetails_evening').append('<tr id="addr'+(evening+1)+'_evening"></tr>');
      evening++; 
    });

    $("#delete_row_morning").click(function(){
        if (morning > 1) {
            if ($('#gender_' + (morning-1) + '_morning')[0].options.selectedIndex == "1") {
                var bandhu = $('#totalBandhu').val();
                $('#totalBandhu').val(parseInt(bandhu) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            } else if ($('#gender_' + (morning-1) + '_morning')[0].options.selectedIndex == "2") {
                var bhagini = $('#totalBhagini').val();
                $('#totalBhagini').val(parseInt(bhagini) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            }
            $("#addr"+(morning-1)+"_morning").html('');
            $('#addr' + morning + "_morning").remove();
            morning--;
        }
    });
    $("#delete_row_afternoon").click(function(){
        if (afternoon > 1) {
            if ($('#gender_' + (afternoon-1) + '_afternoon')[0].options.selectedIndex == "1") {
                var bandhu = $('#totalBandhu').val();
                $('#totalBandhu').val(parseInt(bandhu) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            } else if ($('#gender_' + (afternoon-1) + '_afternoon')[0].options.selectedIndex == "2") {
                var bhagini = $('#totalBhagini').val();
                $('#totalBhagini').val(parseInt(bhagini) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            }
            $("#addr"+(afternoon-1)+"_afternoon").html('');
            $('#addr' + afternoon + "_afternoon").remove();
            afternoon--;
        }
    });
    $("#delete_row_evening").click(function(){
        if (evening > 1) {
            if ($('#gender_' + (evening-1) + '_evening')[0].options.selectedIndex == "1") {
                var bandhu = $('#totalBandhu').val();
                $('#totalBandhu').val(parseInt(bandhu) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            } else if ($('#gender_' + (evening-1) + '_evening')[0].options.selectedIndex == "2") {
                var bhagini = $('#totalBhagini').val();
                $('#totalBhagini').val(parseInt(bhagini) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            }
            $("#addr"+(evening-1)+"_evening").html('');
            $('#addr' + evening + "_evening").remove();
            evening--;
        }
    });
    $('#btnSave').click(function(){
        if(ValidateInputData()){
            return true;
        }
        return false;
    });
    function ValidateInputData() {
        var returnFlag_morning = true;
        var returnFlag_afternoon = true;
        var returnFlag_evening = true;
        var parentReturnFlag = false;
        var addedRows = 0;
        var showErrorMessag = false;
        var errMsg = "Please make sure Name, Age are filled & Gender, Reason selected";
        var bandhuBhaginiData = "";
        var ageValidationErrorFlag = false;
        if ($('#branch_code').val() != null && $('#branch_code').val() != "") {
            $('#arrBandhuBhaginiData').val('');
            $('[id^=morning_]').val('0');
            $('[id^=afternoon_]').val('0');
            $('[id^=evening_]').val('0');
            $('#tblBandhuBhaginiDetails_morning tbody tr').each(function(i, row) {
                if ($(this)[0].cells.length > 0) {
                    var rowValidationCount = 0;
                    if ($('#name_' + $(this).index() + '_morning').val() != undefined && $('#name_' + $(this).index() + '_morning').val() == "") {
                        returnFlag_morning = false; rowValidationCount++;
                    }
                    if ($('#age_' + $(this).index() + '_morning').val() != undefined && $('#age_' + $(this).index() + '_morning').val() == "") {
                        returnFlag_morning = false; rowValidationCount++;
                    }
                    if ($('#gender_' + $(this).index() + '_morning')[0] != undefined && $('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == "0") {
                        returnFlag_morning = false; rowValidationCount++;
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0] != undefined && $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "0") {
                        returnFlag_morning = false; rowValidationCount++
                    }
                    // if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0] != undefined &&
                    //     $('#age_' + $(this).index() + '_morning').val() != undefined &&
                    //     $('#age_' + $(this).index() + '_morning').val() != "" &&
                    //     ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "4") &&
                    //     parseInt($('#age_' + $(this).index() + '_morning').val()) < 18) {
                    //         returnFlag_morning = false;
                    //         errMsg = "Age should be greater than 18 for Anugraha or Overlap";
                    // }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0] != undefined &&
                        $('#age_' + $(this).index() + '_morning').val() != undefined &&
                        $('#age_' + $(this).index() + '_morning').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "1" ||$('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_morning').val()) < 18) {
                                returnFlag_morning = false;
                                ageValidationErrorFlag = true;
                                errMsg = "Morning Tab: Age should be greater than 17 for Seva or Anugraha or Overlap";
                            }
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0] != undefined &&
                        $('#age_' + $(this).index() + '_morning').val() != undefined &&
                        $('#age_' + $(this).index() + '_morning').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_morning').val()) > 50) {
                                ageValidationErrorFlag = true;
                                returnFlag_morning = false;
                                errMsg = "Morning Tab: Age should be less than 51 for Seva or Overlap";
                            }
                    }
                    if ((rowValidationCount > 0 && rowValidationCount < 4) || ageValidationErrorFlag) {
                        showErrorMessag = true;
                    }
                    if (returnFlag_morning) {
                        addedRows++;
                        parentReturnFlag = true;
                        var startEndDateDays = ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex == "4") ? "3|2" : "1|1"
                        bandhuBhaginiData = bandhuBhaginiData == "" ? 
                            $('#name_' + $(this).index() + '_morning').val() + "|" + $('#age_' + $(this).index() + '_morning').val() + "|" + $('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex + "|1|" + startEndDateDays : 
                            bandhuBhaginiData + "$" + $('#name_' + $(this).index() + '_morning').val() + "|" + $('#age_' + $(this).index() + '_morning').val() + "|" + $('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex + "|1|" + startEndDateDays;
                        
                        switch ($('#sevaUpasnaAnugraha_' + $(this).index() + '_morning')[0].options.selectedIndex) {
                            case 1: // For Seva Selection
                                if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '1') {
                                    $('#morning_seva_brothers').val(parseInt($('#morning_seva_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '2') {
                                    $('#morning_seva_sisters').val(parseInt($('#morning_seva_sisters').val()) + 1);
                                }                                
                                break;
                            case 2: // For Upasana Selection
                                if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '1') {
                                    $('#morning_upasana_brothers').val(parseInt($('#morning_upasana_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '2') {
                                    $('#morning_upasana_sisters').val(parseInt($('#morning_upasana_sisters').val()) + 1);
                                }
                                break;
                            case 3: // For Anugraha Selection
                                if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '1') {
                                    $('#morning_anugraha_brothers').val(parseInt($('#morning_anugraha_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '2') {
                                    $('#morning_anugraha_sisters').val(parseInt($('#morning_anugraha_sisters').val()) + 1);
                                }
                                break;
                            case 4: // For Overlap Selection
                                if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '1') {
                                    $('#morning_overlap_brothers').val(parseInt($('#morning_overlap_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_morning')[0].options.selectedIndex == '2') {
                                    $('#morning_overlap_sisters').val(parseInt($('#morning_overlap_sisters').val()) + 1);
                                }
                                break;
                        }
                    }
                }
            });
            $('#tblBandhuBhaginiDetails_afternoon tbody tr').each(function(i, row) {
                if ($(this)[0].cells.length > 0) {
                    var rowValidationCount = 0;
                    if ($('#name_' + $(this).index() + '_afternoon').val() != undefined && $('#name_' + $(this).index() + '_afternoon').val() == "") {
                        returnFlag_afternoon = false; rowValidationCount++;
                    }
                    if ($('#age_' + $(this).index() + '_afternoon').val() != undefined && $('#age_' + $(this).index() + '_afternoon').val() == "") {
                        returnFlag_afternoon = false; rowValidationCount++;
                    }
                    if ($('#gender_' + $(this).index() + '_afternoon')[0] != undefined && $('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "0") {
                        returnFlag_afternoon = false; rowValidationCount++;
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0] != undefined && $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "0") {
                        returnFlag_afternoon = false; rowValidationCount++;
                    }
                    // if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0] != undefined &&
                    //     $('#age_' + $(this).index() + '_afternoon').val() != undefined &&
                    //     $('#age_' + $(this).index() + '_afternoon').val() != "" &&
                    //     ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "4") &&
                    //     parseInt($('#age_' + $(this).index() + '_afternoon').val()) < 18) {
                    //         returnFlag_afternoon = false;
                    //         errMsg = "Age should be greater than 18 for Anugraha or Overlap";
                    // }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0] != undefined &&
                        $('#age_' + $(this).index() + '_afternoon').val() != undefined &&
                        $('#age_' + $(this).index() + '_afternoon').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "1" ||$('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_afternoon').val()) < 18) {
                                ageValidationErrorFlag = true;
                                returnFlag_afternoon = false;
                                errMsg = "Afternoon Tab: Age should be greater than 17 for Seva or Anugraha or Overlap";
                            }
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0] != undefined &&
                        $('#age_' + $(this).index() + '_afternoon').val() != undefined &&
                        $('#age_' + $(this).index() + '_afternoon').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_afternoon').val()) > 50) {
                                returnFlag_afternoon = false;
                                errMsg = "Afternoon Tab: Age should be less than 51 for Seva or Overlap";
                            }
                    }
                    if ((rowValidationCount > 0 && rowValidationCount < 4) || ageValidationErrorFlag) {
                        showErrorMessag = true;
                    }
                    if (returnFlag_afternoon) {
                        addedRows++;
                        parentReturnFlag = true;
                        var startEndDateDays = ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex == "4") ? "3|2" : "1|1"
                        bandhuBhaginiData = bandhuBhaginiData == "" ? 
                            $('#name_' + $(this).index() + '_afternoon').val() + "|" + $('#age_' + $(this).index() + '_afternoon').val() + "|" + $('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex + "|2|" + startEndDateDays : 
                            bandhuBhaginiData + "$" + $('#name_' + $(this).index() + '_afternoon').val() + "|" + $('#age_' + $(this).index() + '_afternoon').val() + "|" + $('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex + "|2|" + startEndDateDays;
                        
                        switch ($('#sevaUpasnaAnugraha_' + $(this).index() + '_afternoon')[0].options.selectedIndex) {
                            case 1: // For Seva Selection
                                if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '1') {
                                    $('#afternoon_seva_brothers').val(parseInt($('#afternoon_seva_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '2') {
                                    $('#afternoon_seva_sisters').val(parseInt($('#afternoon_seva_sisters').val()) + 1);
                                }                                
                                break;
                            case 2: // For Upasana Selection
                                if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '1') {
                                    $('#afternoon_upasana_brothers').val(parseInt($('#afternoon_upasana_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '2') {
                                    $('#afternoon_upasana_sisters').val(parseInt($('#afternoon_upasana_sisters').val()) + 1);
                                }
                                break;
                            case 3: // For Anugraha Selection
                                if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '1') {
                                    $('#afternoon_anugraha_brothers').val(parseInt($('#afternoon_anugraha_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '2') {
                                    $('#afternoon_anugraha_sisters').val(parseInt($('#afternoon_anugraha_sisters').val()) + 1);
                                }
                                break;
                            case 4: // For Overlap Selection
                                if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '1') {
                                    $('#afternoon_overlap_brothers').val(parseInt($('#afternoon_overlap_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_afternoon')[0].options.selectedIndex == '2') {
                                    $('#afternoon_overlap_sisters').val(parseInt($('#afternoon_overlap_sisters').val()) + 1);
                                }
                                break;
                        }
                    }
                }
            });
            $('#tblBandhuBhaginiDetails_evening tbody tr').each(function(i, row) {
                if ($(this)[0].cells.length > 0) {
                    var rowValidationCount = 0;
                    if ($('#name_' + $(this).index() + '_evening').val() != undefined && $('#name_' + $(this).index() + '_evening').val() == "") {
                        returnFlag_evening = false; rowValidationCount++;
                    }
                    if ($('#age_' + $(this).index() + '_evening').val() != undefined && $('#age_' + $(this).index() + '_evening').val() == "") {
                        returnFlag_evening = false; rowValidationCount++;
                    }
                    if ($('#gender_' + $(this).index() + '_evening')[0] != undefined && $('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == "0") {
                        returnFlag_evening = false; rowValidationCount++;
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0] != undefined && $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "0") {
                        returnFlag_evening = false; rowValidationCount++;
                    }
                    // if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0] != undefined &&
                    //     $('#age_' + $(this).index() + '_evening').val() != undefined &&
                    //     $('#age_' + $(this).index() + '_evening').val() != "" &&
                    //     ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "4") &&
                    //     parseInt($('#age_' + $(this).index() + '_evening').val()) < 18) {
                    //         returnFlag_evening = false;
                    //         errMsg = "Age should be greater than 18 for Anugraha or Overlap";
                    // }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0] != undefined &&
                        $('#age_' + $(this).index() + '_evening').val() != undefined &&
                        $('#age_' + $(this).index() + '_evening').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "1" ||$('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_evening').val()) < 18) {
                                returnFlag_evening = false;
                                ageValidationErrorFlag = true;
                                errMsg = "Evening Tab: Age should be greater than 17 for Seva or Anugraha or Overlap";
                            }
                    }
                    if ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0] != undefined &&
                        $('#age_' + $(this).index() + '_evening').val() != undefined &&
                        $('#age_' + $(this).index() + '_evening').val() != "" &&
                        ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "4")) {
                            if (parseInt($('#age_' + $(this).index() + '_evening').val()) > 50) {
                                returnFlag_evening = false;
                                ageValidationErrorFlag = true;
                                errMsg = "Evening Tab: Age should be less than 51 for Seva or Overlap";
                            }
                    }
                    if ((rowValidationCount > 0 && rowValidationCount < 4) || ageValidationErrorFlag) {
                        showErrorMessag = true;
                    }
                    if (returnFlag_evening) {
                        addedRows++;
                        parentReturnFlag = true;
                        var startEndDateDays = ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "1" || $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex == "4") ? "3|2" : "1|1"
                        bandhuBhaginiData = bandhuBhaginiData == "" ? 
                            $('#name_' + $(this).index() + '_evening').val() + "|" + $('#age_' + $(this).index() + '_evening').val() + "|" + $('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex + "|3|" + startEndDateDays : 
                            bandhuBhaginiData + "$" + $('#name_' + $(this).index() + '_evening').val() + "|" + $('#age_' + $(this).index() + '_evening').val() + "|" + $('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex + "|3|" + startEndDateDays;
                        
                        switch ($('#sevaUpasnaAnugraha_' + $(this).index() + '_evening')[0].options.selectedIndex) {
                            case 1: // For Seva Selection
                                if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '1') {
                                    $('#evening_seva_brothers').val(parseInt($('#evening_seva_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '2') {
                                    $('#evening_seva_sisters').val(parseInt($('#evening_seva_sisters').val()) + 1);
                                }                                
                                break;
                            case 2: // For Upasana Selection
                                if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '1') {
                                    $('#evening_upasana_brothers').val(parseInt($('#evening_upasana_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '2') {
                                    $('#evening_upasana_sisters').val(parseInt($('#evening_upasana_sisters').val()) + 1);
                                }
                                break;
                            case 3: // For Anugraha Selection
                                if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '1') {
                                    $('#evening_anugraha_brothers').val(parseInt($('#evening_anugraha_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '2') {
                                    $('#evening_anugraha_sisters').val(parseInt($('#evening_anugraha_sisters').val()) + 1);
                                }
                                break;
                            case 4: // For Overlap Selection
                                if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '1') {
                                    $('#evening_overlap_brothers').val(parseInt($('#evening_overlap_brothers').val()) + 1);
                                } else if ($('#gender_' + $(this).index() + '_evening')[0].options.selectedIndex == '2') {
                                    $('#evening_overlap_sisters').val(parseInt($('#evening_overlap_sisters').val()) + 1);
                                }
                                break;
                        }
                    }
                }
            });
        } else {
            //parentReturnFlag = false;
            errMsg = "Please select Shakha properly";
        }
        if (addedRows < 1 || showErrorMessag) {
            $('#arrBandhuBhaginiData').val('');
            alert(errMsg);
            return false;
        } else {
            $('#arrBandhuBhaginiData').val(bandhuBhaginiData);
            return true;
        }
        // $('#arrBandhuBhaginiData').val(bandhuBhaginiData);
        // return parentReturnFlag;
    }
    function CalculateBandhuBhaginiCount_morning(e) {
        if(e.selectedIndex != 0) {
            var bandhu = 0; var bhagini = 0; var total = 0;
            $('#tblBandhuBhaginiDetails_morning tbody tr').each(function(i, row) {
                if ($('#' + row.id)[0].cells.length > 0) {
                    if ($('#gender_' + row.sectionRowIndex + '_morning')[0].options.selectedIndex != undefined && $('#gender_' + row.sectionRowIndex + '_morning')[0].options.selectedIndex != "0") {
                        if ($('#gender_' + row.sectionRowIndex + '_morning')[0].options.selectedIndex == "1") {
                            bandhu ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_morning').val('Male');
                        } else if ($('#gender_' + row.sectionRowIndex + '_morning')[0].options.selectedIndex == "2") {
                            bhagini ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_morning').val('Female');
                        }
                    }
                }
            });
            total = bandhu + bhagini;
            $('#bandhu_morning').val(bandhu);
            $('#bhagini_morning').val(bhagini);
            $('#total_morning').val(total);
            $('#totalBandhu').val(parseInt($('#bandhu_morning').val())+parseInt($('#bandhu_afternoon').val())+parseInt($('#bandhu_evening').val()));
            $('#totalBhagini').val(parseInt($('#bhagini_morning').val())+parseInt($('#bhagini_afternoon').val())+parseInt($('#bhagini_evening').val()));
            $('#totalBandhuBhagini').val(parseInt($('#total_morning').val())+parseInt($('#total_afternoon').val())+parseInt($('#total_evening').val()));
            if(e.selectedIndex == 1 && 
                (parseInt($('#totalBandhu').val()) > parseInt($('#available_bandhu').val()))) {
                alert('Bandhu count is greater than available Bandhu Count:('+$('#available_bandhu').val()+')');
            } else if (e.selectedIndex == 2 && 
                (parseInt($('#totalBhagini').val()) > parseInt($('#available_bhagini').val()))) {
                alert('Bhagini count is greater than available Bhagini Count:('+$('#available_bhagini').val()+')');
            }
        } else {
            var splitId = e.id.split('_');
            if ($('#selectedGender_' + splitId[1] + '_morning').val() == "Male") {
                var bandhu = $('#totalBandhu').val();
                $('#bandhu_morning').val(parseInt($('#bandhu_morning').val()) - 1);
                $('#totalBandhu').val(parseInt(bandhu) - 1);
            } else if ($('#selectedGender_' + splitId[1] + '_morning').val() == "Female") {
                var bhagini = $('#totalBhagini').val();
                $('#bhagini_morning').val(parseInt($('#bhagini_morning').val()) - 1);
                $('#totalBhagini').val(parseInt(bhagini) - 1);
            }
            $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
        }
    }
    function CalculateBandhuBhaginiCount_afternoon(e) {
        if(e.selectedIndex != 0) {
            var bandhu = 0; var bhagini = 0; var total = 0;
            $('#tblBandhuBhaginiDetails_afternoon tbody tr').each(function(i, row) {
                if ($('#' + row.id)[0].cells.length > 0) {
                    if ($('#gender_' + row.sectionRowIndex + '_afternoon')[0].options.selectedIndex != undefined && $('#gender_' + row.sectionRowIndex + '_afternoon')[0].options.selectedIndex != "0") {
                        if ($('#gender_' + row.sectionRowIndex + '_afternoon')[0].options.selectedIndex == "1") {
                            bandhu ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_afternoon').val('Male');
                        } else if ($('#gender_' + row.sectionRowIndex + '_afternoon')[0].options.selectedIndex == "2") {
                            bhagini ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_afternoon').val('Female');
                        }
                    }
                }
            });
            total = bandhu + bhagini;
            $('#bandhu_afternoon').val(bandhu);
            $('#bhagini_afternoon').val(bhagini);
            $('#total_afternoon').val(total);
            $('#totalBandhu').val(parseInt($('#bandhu_morning').val())+parseInt($('#bandhu_afternoon').val())+parseInt($('#bandhu_evening').val()));
            $('#totalBhagini').val(parseInt($('#bhagini_morning').val())+parseInt($('#bhagini_afternoon').val())+parseInt($('#bhagini_evening').val()));
            $('#totalBandhuBhagini').val(parseInt($('#total_morning').val())+parseInt($('#total_afternoon').val())+parseInt($('#total_evening').val()));
            if(e.selectedIndex == 1 && 
                (parseInt($('#totalBandhu').val()) > parseInt($('#available_bandhu').val()))) {
                alert('Bandhu count is greater than available Bandhu Count:('+$('#available_bandhu').val()+')');
            } else if (e.selectedIndex == 2 && 
                (parseInt($('#totalBhagini').val()) > parseInt($('#available_bhagini').val()))) {
                alert('Bhagini count is greater than available Bhagini Count:('+$('#available_bhagini').val()+')');
            }
        } else {
            var splitId = e.id.split('_');
            if ($('#selectedGender_' + splitId[1] + '_afternoon').val() == "Male") {
                var bandhu = $('#totalBandhu').val();
                $('#bandhu_afternoon').val(parseInt($('#bandhu_afternoon').val()) - 1);
                $('#totalBandhu').val(parseInt(bandhu) - 1);
            } else if ($('#selectedGender_' + splitId[1] + '_afternoon').val() == "Female") {
                var bhagini = $('#totalBhagini').val();
                $('#bhagini_afternoon').val(parseInt($('#bhagini_afternoon').val()) - 1);
                $('#totalBhagini').val(parseInt(bhagini) - 1);
            }
            $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
        }
    }
    function CalculateBandhuBhaginiCount_evening(e) {
        if(e.selectedIndex != 0) {
            var bandhu = 0; var bhagini = 0; var total = 0;
            $('#tblBandhuBhaginiDetails_evening tbody tr').each(function(i, row) {
                if ($('#' + row.id)[0].cells.length > 0) {
                    if ($('#gender_' + row.sectionRowIndex + '_evening')[0].options.selectedIndex != undefined && $('#gender_' + row.sectionRowIndex + '_evening')[0].options.selectedIndex != "0") {
                        if ($('#gender_' + row.sectionRowIndex + '_evening')[0].options.selectedIndex == "1") {
                            bandhu ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_evening').val('Male');
                        } else if ($('#gender_' + row.sectionRowIndex + '_evening')[0].options.selectedIndex == "2") {
                            bhagini ++;
                            $('#selectedGender_' + row.sectionRowIndex + '_evening').val('Female');
                        }
                    }
                }
            });
            total = bandhu + bhagini;
            $('#bandhu_evening').val(bandhu);
            $('#bhagini_evening').val(bhagini);
            $('#total_evening').val(total);
            $('#totalBandhu').val(parseInt($('#bandhu_morning').val())+parseInt($('#bandhu_afternoon').val())+parseInt($('#bandhu_evening').val()));
            $('#totalBhagini').val(parseInt($('#bhagini_morning').val())+parseInt($('#bhagini_afternoon').val())+parseInt($('#bhagini_evening').val()));
            $('#totalBandhuBhagini').val(parseInt($('#total_morning').val())+parseInt($('#total_afternoon').val())+parseInt($('#total_evening').val()));
            if(e.selectedIndex == 1 && 
                (parseInt($('#totalBandhu').val()) > parseInt($('#available_bandhu').val()))) {
                alert('Bandhu count is greater than available Bandhu Count:('+$('#available_bandhu').val()+')');
            } else if (e.selectedIndex == 2 && 
                (parseInt($('#totalBhagini').val()) > parseInt($('#available_bhagini').val()))) {
                alert('Bhagini count is greater than available Bhagini Count:('+$('#available_bhagini').val()+')');
            }
        } else {
            var splitId = e.id.split('_');
            if ($('#selectedGender_' + splitId[1] + '_evening').val() == "Male") {
                var bandhu = $('#totalBandhu').val();
                $('#bandhu_evening').val(parseInt($('#bandhu_evening').val()) - 1);
                $('#totalBandhu').val(parseInt(bandhu) - 1);
            } else if ($('#selectedGender_' + splitId[1] + '_evening').val() == "Female") {
                var bhagini = $('#totalBhagini').val();
                $('#bhagini_evening').val(parseInt($('#bhagini_evening').val()) - 1);
                $('#totalBhagini').val(parseInt(bhagini) - 1);
            }
            $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
        }
    }
    function GetQuotaByOccasion(e) {
        if (e.selectedOptions[0].value != "0") {
            var startDate = e.selectedOptions[0].attributes['startdate'].value;
            var endDate = e.selectedOptions[0].attributes['enddate'].value;
            // $('#periodOfStay').html('From ' + startDate + ' To ' + endDate );
            //$('#Duration').val(e.selectedOptions[0].attributes['duration'].value);
            $('#occasioncode').val(e.selectedOptions[0].attributes['occasioncode'].value);
            // $('#utsavStartDate').val(startDate);
            // $('#utsavEndDate').val(endDate);
            $('#utsav_type').val(e.selectedOptions[0].text);
            if ($('#txtBranch').val() != "" && $('#ddlBatches')[0].selectedOptions[0].value != "0") {
                GetQuotaByShakhaAndBranch(GetAndSetBranchCode($('#txtBranch').val()), e.selectedOptions[0].value, e.selectedOptions[0].text, $('#ddlBatches')[0].selectedOptions[0].value);
            } else {
                // alert('Please select Shakha');
            }
            GetNewRefCodeOfOccasion(e.selectedOptions[0].value);
        } else {
            $('[id^=add_row_]').attr('disabled','disabled');
            $('[id^=delete_row_]').attr('disabled','disabled');
            $('#btnSave').attr('disabled','disabled');
            $('#quota_total').val('0');
            $('#bandhu_available').val('0');
            $('#bhagini_available').val('0');
            $('#quota_available').val('0');
            $('#occasionRefNo').val('');
        }
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
        }
        if ($('#ddlOccasion')[0].selectedOptions[0].value != "0" && $('#ddlBatches')[0].selectedOptions[0].value != "0") {
            GetQuotaByShakhaAndBranch(GetAndSetBranchCode(e.value), $('#ddlOccasion')[0].selectedOptions[0].value,$('#ddlOccasion')[0].selectedOptions[0].text, $('#ddlBatches')[0].selectedOptions[0].value);
        } else {
            alert('Please select Occasion and Batch');
        }
    }
    function GetQuotaBySelectedBatch(e) {
        if (e.selectedOptions[0].value != "0" && $('#txtBranch').val() != "") {
            GetQuotaByShakhaAndBranch(GetAndSetBranchCode($('#txtBranch').val()),$('#ddlOccasion')[0].selectedOptions[0].value,$('#ddlOccasion')[0].selectedOptions[0].text,e.selectedOptions[0].value)
        }else {
            $('#quota_total').val(0);
            $('#quota_available').val(0);
            $('#bandhu_available').val(0);
            $('#bhagini_available').val(0);
            $('[id^=add_row_]').attr('disabled','disabled');
            $('[id^=delete_row_]').attr('disabled','disabled');
            $('#btnSave').attr('disabled','disabled');
            alert('Please select Occasion and Shakha');
        }
    }
    function GetQuotaByShakhaAndBranch(branchcode, occasionid, occasionName, selectedBatch) {
        var splitOccasion = occasionName.split('-');
        $.ajax({
            url: 'fetch_quota.php', // URL to PHP script that fetches dates
            type: 'POST',
            data: {
                branchcode: branchcode,
                occasionid: occasionid,
                occasionName:splitOccasion[0] + '%',
                selectedBatch:selectedBatch
            },
            success: function(response) {
                var res = JSON.parse(response);
                if(res.occasionquotaid != null) {
                    $('#quota_total').val(res.total);
                    $('#quota_available').val(res.availabletotal);
                    $('#added_bandhu').val(res.addedbandhu);
                    $('#added_bhagini').val(res.addedbhagini);
                    $('#added_total').val(res.addedtotal);
                    $('#available_bandhu').val(res.availablebandhu);
                    $('#bandhu_available').val(res.availablebandhu);
                    $('#available_bhagini').val(res.availablebhagini);
                    $('#bhagini_available').val(res.availablebhagini);
                    $('#available_total').val(res.availabletotal);
                    $('[id^=add_row_]').removeAttr('disabled');
                    $('[id^=delete_row_]').removeAttr('disabled');
                    $('#btnSave').removeAttr('disabled');
                } else {
                    $('#quota_total').val(0);
                    $('#quota_available').val(0);
                    $('#bandhu_available').val(0);
                    $('#bhagini_available').val(0);
                    $('[id^=add_row_]').attr('disabled','disabled');
                    $('[id^=delete_row_]').attr('disabled','disabled');
                    $('#btnSave').attr('disabled','disabled');
                    alert('Quota not available for selected Occasion and Shakha');
                }
            },
            error: function() {
                alert('Error fetching in Quota.');
            }
        });
    }
    function GetNewRefCodeOfOccasion(selectedOccasionId) {
        $.ajax({
            url: 'getnewrefnoofoccasion.php', // URL to PHP script that fetches dates
            type: 'POST',
            data: {occasionId: selectedOccasionId},
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
  </script>

  <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>