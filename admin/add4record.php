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

$occasion_name = "";

$formatInsertQueryParent = "Insert into form_submissions (sevautsav_id, occasion_id, branch_code, branch, brothers, sisters, total_people, start_date, end_date, duration, letter_dated, form_dated, time_of_arrival, utsav_type) values"; 
$formatInsertQueryChild = "Insert into utsavbandhubhagini (form_submission_id, name, age, gender, seva, isDeleted, start_date, end_date) values";

if(isset($_POST['save_BandhuBhaginiData']))
{
    $occasionId = $_POST['occasionId'];
    $shakha = $_POST['shakha'];
    $branch_code = $_POST['branch_code'];
    $brothers = $_POST['brothers'];
    $sisters = $_POST['sisters'];
    $total_people = $_POST['total_people'];
    $start_date = $_POST['utsavStartDate'];
    $end_date = $_POST['utsavEndDate'];
    $duration = $_POST['Duration'];
    $letter_dated = $_POST['letter_dated'];
    $form_dated = $_POST['form_dated'];
    $time_of_arrival = $_POST['time_of_arrival'];
    $utsavType = $_POST['utsav_type'];
    $arrBandhuBhaginiData = $_POST['arrBandhuBhaginiData'];

    if ($arrBandhuBhaginiData != "" && $arrBandhuBhaginiData != null) {
        $formatInsertQueryParent = $formatInsertQueryParent . "(" . $occasionId . "," . $occasionId . ",'" . $branch_code . "','" . $shakha . "'," . $brothers . "," . $sisters . "," . $total_people . ",'" . $start_date . "','" . $end_date . "'," . $duration . ",STR_TO_DATE('" . $letter_dated . "', '%d-%M-%Y'),STR_TO_DATE('" . $form_dated . "', '%d-%M-%Y'),'" . $time_of_arrival . "','" . $utsavType . "'); ";

        if(mysqli_query($db, $formatInsertQueryParent)) {
            $lastId = mysqli_insert_id($db);
            if ($arrBandhuBhaginiData != "" && $arrBandhuBhaginiData != null) {
                $bandhuBhaginiValues = "";
                $splitByDollar = explode('$', $arrBandhuBhaginiData);
                foreach($splitByDollar as &$data) {
                    $splitByPipe = explode('|', $data);
                    $formStartDate = "DATE_SUB('" . $start_date . "', INTERVAL " . $splitByPipe[4] . " DAY)";
                    $formEndDate = "DATE_ADD('" . $end_date . "', INTERVAL " . $splitByPipe[4] . " DAY)";
                    $bandhuBhaginiValues = $bandhuBhaginiValues == "" ? "(" . $lastId . ",'" . $splitByPipe[0] . "'," . $splitByPipe[1] . "," . $splitByPipe[2] . "," . $splitByPipe[3] . ",false," . $formStartDate . "," . $formEndDate . ")" : $bandhuBhaginiValues . ",(" . $lastId . ",'" . $splitByPipe[0] . "'," . $splitByPipe[1] . "," . $splitByPipe[2] . "," . $splitByPipe[3] . ",false," . $formStartDate . "," . $formEndDate .")";
                }
                $formatInsertQueryChild = $formatInsertQueryChild . $bandhuBhaginiValues . ";";
            }
            if(mysqli_query($db, $formatInsertQueryChild)) {
                echo '<script>alert("Data Saved Successfully");</script>';
            } else {
                echo '<script>alert("ERROR in Data Save");</script>';
            }
        } else {
            echo '<script>alert("ERROR in Data Save");</script>';
        }
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
                                (End_Date - Start_Date)+1 AS Duration,
                                batch, occasion_key, occasion_code,
                                CONCAT_WS(' B - ',
                                IF(LENGTH(`occasion`),`occasion`,NULL),
                                IF(LENGTH(`batch`),`batch`,NULL)) AS CombineName
                                FROM occasions  
                                WHERE occasion LIKE ('%Punyatithi%') 
                                AND DATE_SUB(Start_Date, INTERVAL 5 MONTH) <= CURRENT_DATE()
                                AND End_Date > CURRENT_DATE()";
                $result_utsav = mysqli_query($db, $query_utsav);
                $row_utsav = mysqli_fetch_array($result_utsav);
                if(mysqli_num_rows($result_utsav) > 0)
                {
            ?>
                <div>
                    <div style="width:100%;">
                        <?php
                        $query_refno = "SELECT MAX(id)+1 AS refno FROM form_submissions";
                        $result_refno = mysqli_query($db, $query_refno);
                        $row_refno = mysqli_fetch_array($result_refno);
                        ?>
                        <div style="text-align:right; font-family:none;">Ref No : <?php echo htmlspecialchars($row_utsav['occasion_code']); ?>_<?php echo htmlspecialchars($row_refno['refno']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                         <select class="form-control" id="ddlOccasion" name="occasionId" onchange="GetQuotaByOccasion(this)">
                            <?php
                            mysqli_data_seek($result_utsav, 0 );
                            while ($row_occasion = mysqli_fetch_array($result_utsav)) {
                                echo '<option value="'.$row_occasion['id'].'" startdate="' . $row_occasion['FormattedStartDate'] . '" enddate="' . $row_occasion['FormattedEndDate'] . '" duration="' . $row_occasion['Duration'] . '">' . htmlspecialchars($row_occasion['CombineName']) . '</option>';
                            }
                            ?>
                         </select>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Shakha</label>
                        <input id="txtBranch" type="text" name="shakha" class="form-control" list="shakha" required
                            value="<?php htmlspecialchars($_POST['branch']); ?>" branchcode="<?php htmlspecialchars($_POST['unique_code']); ?>" />
                            <datalist id="shakha">
                                <?php 
                                    $query_shakha = "SELECT * FROM mandir_branch";
                                    $result_shakha = mysqli_query($db, $query_shakha);
                                    while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                                        $shakha = $row_shakha['shakha'];
                                    ?>
                                    <option value="<?php echo $shakha; ?>" branchcode="<?php echo htmlspecialchars($row_shakha['unique_code']); ?>">
                                        <?php echo htmlspecialchars($row_shakha['shakha']); ?>
                                    </option>
                                <?php
                                    }
                                ?>
                            </datalist>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Occasion</label>
                        <input id="txtBranch" type="text" name="occasion" class="form-control" list="occasion" required
                            value="<?php htmlspecialchars($_POST['branch']); ?>" branchcode="<?php htmlspecialchars($_POST['occasion_key']); ?>" />
                            <datalist id="occasion">
                                <?php 
                                    $query_occasion = "SELECT * FROM occasions";
                                    $result_occasion = mysqli_query($db, $query_occasion);
                                    while ($row_occasion = mysqli_fetch_assoc($result_occasion)) {
                                        $occasion = $row_occasion['occasion'];
                                    ?>
                                    <option value="<?php echo $occasion; ?>" branchcode="<?php echo htmlspecialchars($row_occasion['occasion_key']); ?>">
                                        <?php echo htmlspecialchars($row_occasion['occasion']); ?>
                                    </option>
                                <?php
                                    }
                                ?>
                            </datalist>
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
                        <input class="form-control calendarBackground" type="text" name="letter_dated" id="datepicker" required>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Date</label>
                        <input class="form-control" name="form_dated" value="<?php echo date('d-F-Y'); ?>" readonly></input>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Time Of Arrival</label>
                        <select class="form-control" name="time_of_arrival" required>
                            <option value="">Select</option>
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Evening">Evening</option>
                        </select>
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

                    <div class="col-md-8">
                        <label for="Category">Period of Stay</label>
                        <div id="periodOfStay" class="form-control" readonly>From <?php $convertedStartDate = date("d-F-Y", strtotime($row_utsav['Start_Date']));
                            echo $convertedStartDate; ?> To <?php $convertedEndDate = date("d-F-Y", strtotime($row_utsav['End_Date']));
                            echo $convertedEndDate; ?></div>
                    </div>
                    <div class="col-md-4">
                        <label for="Category">Duration</label>
                        <input class="form-control" type="number" id="Duration" name="Duration" min="0" max="100" readonly value="<?php echo $row_utsav['Duration'];?>">
                    </div>

                    <div class="col-md-12" style="margin-top:10px;">
                        <div>
                            <table class="table table-bordered table-hover" id="tblBandhuBhaginiDetails">
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
                                <tr id='addr0'>
                                    <td style="width:40%">
                                    Upasana
                                    </td>
                                    <td style="width:30%">
                                        <input type="number" name="utsav_brothers" min="0" max="100" value="<?php echo $_SESSION['utsav_brothers'];?>" placeholder="0">
                                    </td>
                                    <td style="width:30%">
                                        <input type="number" name="utsav_sisters" min="0" max="100" value="<?php echo $_SESSION['utsav_sisters'];?>" placeholder="0">
                                    </td>
                                    
                                </tr>
                                <tr id='addr1'></tr>
                            </tbody>
                        </table>
                        </div>
                        <div>
                            <a id="add_row" class="btn btn-primary pull-left btn-sm">Add Row</a>
                            <a id='delete_row' class="pull-right btn btn-danger btn-sm">Delete Row</a>
                        </div>
                        <center>
                        <!-- <input type="hidden" name="occasionId" id="occasionId" value="<?php echo $row_utsav['id'];?>"> -->
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
    $(function () {
      $("#datepicker").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        endDate: "today"
      });
    });
    var i=1;
     $("#add_row").click(function(){
      $('#addr'+i).html("<td>"+ (i+1) +"</td><td><input name='name_"+i+"' id='name_"+i+"' type='text' placeholder='Name' class='form-control input-md'  /></td><td><input name='age_"+i+"' id='age_"+i+"' type='number' min='15' max='99' maxlength='2' placeholder='Age' class='form-control input-md'></td><td><select class='form-control' name='gender_"+i+"' id='gender_"+i+"' onchange='CalculateBandhuBhaginiCount(this)'><option value='0'>Select</option><option>Male</option><option>Female</option></select><input type='hidden' id='selectedGender_"+i+"' name='selectedGender_"+i+"' /></td><td><select class='form-control' name='sevaUpasnaAnugraha_"+i+"' id='sevaUpasnaAnugraha_"+i+"'><option value='0'>Select</option><option>Seva</option><option>Upasana</option><option>Anugraha</option><option>Overlap</option></select></td>");

      $('#tblBandhuBhaginiDetails').append('<tr id="addr'+(i+1)+'"></tr>');
      i++; 
    });
    $("#delete_row").click(function(){
        if (i > 1) {
            if ($('#gender_' + (i-1))[0].options.selectedIndex == "1") {
                var bandhu = $('#totalBandhu').val();
                $('#totalBandhu').val(parseInt(bandhu) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            } else if ($('#gender_' + (i-1))[0].options.selectedIndex == "2") {
                var bhagini = $('#totalBhagini').val();
                $('#totalBhagini').val(parseInt(bhagini) - 1);
                $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
            }
            $("#addr"+(i-1)).html('');
            $('#addr' + i).remove();
            i--;
        }
    });
    $('#btnSave').click(function(){
        if(ValidateInputData()){
            return true;
        }
        return false;
    });
    function ValidateInputData() {
        var returnFlag = true;
        var errMsg = "Please make sure Name, Age are filled & Gender, Reason selected";
        var bandhuBhaginiData = "";
        $('#arrBandhuBhaginiData').val('');
        $('#tblBandhuBhaginiDetails tbody tr').each(function(i, row) {
            if ($(this)[0].cells.length > 0) {
                if ($('#name_' + $(this).index()).val() != undefined && $('#name_' + $(this).index()).val() == "") {
                    returnFlag = false;
                }
                if ($('#age_' + $(this).index()).val() != undefined && $('#age_' + $(this).index()).val() == "") {
                    returnFlag = false;
                }
                if ($('#gender_' + $(this).index())[0] != undefined && $('#gender_' + $(this).index())[0].options.selectedIndex == "0") {
                    returnFlag = false;
                }
                if ($('#sevaUpasnaAnugraha_' + $(this).index())[0] != undefined && $('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex == "0") {
                    returnFlag = false;
                }
                if ($('#sevaUpasnaAnugraha_' + $(this).index())[0] != undefined &&
                    $('#age_' + $(this).index()).val() != undefined &&
                    $('#age_' + $(this).index()).val() != "" &&
                    ($('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex == "3" || $('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex == "4") &&
                    parseInt($('#age_' + $(this).index()).val()) < 18) {
                        returnFlag = false;
                        errMsg = "Age should be greater than 18 for Anugraha or Overlap";
                    }
                if (returnFlag) {
                    var startEndDateDays = ($('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex != "1" || $('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex != "4") ? "1|1" : "2|2"
                    bandhuBhaginiData = bandhuBhaginiData == "" ? 
                        $('#name_' + $(this).index()).val() + "|" + $('#age_' + $(this).index()).val() + "|" + $('#gender_' + $(this).index())[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex + "|" + startEndDateDays : 
                        bandhuBhaginiData + "$" + $('#name_' + $(this).index()).val() + "|" + $('#age_' + $(this).index()).val() + "|" + $('#gender_' + $(this).index())[0].options.selectedIndex + "|" + $('#sevaUpasnaAnugraha_' + $(this).index())[0].options.selectedIndex + "|" + startEndDateDays;
                }
            }
        });
        if (!returnFlag) {
            $('#arrBandhuBhaginiData').val('');
            alert(errMsg);
        }
        $('#arrBandhuBhaginiData').val(bandhuBhaginiData);
        return returnFlag;
    }
    function CalculateBandhuBhaginiCount(e) {
        if(e.selectedIndex != 0) {
            var bandhu = 0; var bhagini = 0; var total = 0;
            $('#tblBandhuBhaginiDetails tbody tr').each(function(i, row) {
                // if (i > 0 && $('#' + row.id)[0].cells.length > 0) {
                //     if ($('#gender_' + ($(this).index()-1))[0].options.selectedIndex != undefined && $('#gender_' + ($(this).index()-1))[0].options.selectedIndex != "0") {
                //         if ($('#gender_' + ($(this).index()-1))[0].options.selectedIndex == "1") {
                //             bandhu ++;
                //             $('#selectedGender_' + ($(this).index()-1)).val('Male');
                //         } else if ($('#gender_' + ($(this).index()-1))[0].options.selectedIndex == "2") {
                //             bhagini ++;
                //             $('#selectedGender_' + ($(this).index()-1)).val('Female');
                //         }
                //     }
                // }
                if ($('#' + row.id)[0].cells.length > 0) {
                    if ($('#gender_' + row.sectionRowIndex)[0].options.selectedIndex != undefined && $('#gender_' + row.sectionRowIndex)[0].options.selectedIndex != "0") {
                        if ($('#gender_' + row.sectionRowIndex)[0].options.selectedIndex == "1") {
                            bandhu ++;
                            $('#selectedGender_' + row.sectionRowIndex).val('Male');
                        } else if ($('#gender_' + row.sectionRowIndex)[0].options.selectedIndex == "2") {
                            bhagini ++;
                            $('#selectedGender_' + row.sectionRowIndex).val('Female');
                        }
                    }
                }
            });
            total = bandhu + bhagini;
            $('#totalBandhu').val(bandhu);
            $('#totalBhagini').val(bhagini);
            $('#totalBandhuBhagini').val(total);
            if(e.selectedIndex == 1 && 
                (parseInt(bandhu) > parseInt($('#available_bandhu').val()))) {
                alert('Bandhu count is greater than available Bandhu Count:('+$('#available_bandhu').val()+')');
            } else if (e.selectedIndex == 2 && 
                (parseInt(bhagini) > parseInt($('#available_bhagini').val()))) {
                alert('Bhagini count is greater than available Bhagini Count:('+$('#available_bhagini').val()+')');
            }
        } else {
            var splitId = e.id.split('_');
            if ($('#selectedGender_' + splitId[1]).val() == "Male") {
                var bandhu = $('#totalBandhu').val();
                $('#totalBandhu').val(parseInt(bandhu) - 1);
            } else if ($('#selectedGender_' + splitId[1]).val() == "Female") {
                var bhagini = $('#totalBhagini').val();
                $('#totalBhagini').val(parseInt(bhagini) - 1);
            }
            $('#totalBandhuBhagini').val(parseInt($('#totalBandhuBhagini').val()) - 1);
        }
    }
    function GetQuotaByOccasion(e) {
        var startDate = e.selectedOptions[0].attributes['startdate'].value;
        var endDate = e.selectedOptions[0].attributes['enddate'].value;
        $('#periodOfStay').html('From ' + startDate + ' To ' + endDate );
        $('#Duration').val(e.selectedOptions[0].attributes['duration'].value);
        if ($('#txtBranch').val() != "") {
            GetQuotaByShakhaAndBranch(GetAndSetBranchCode($('#txtBranch').val()), e.selectedOptions[0].value);
        } else {
            alert('Please select Shakha');
        }
    }
    function GetQuotaByShakha(e) {
        GetQuotaByShakhaAndBranch(GetAndSetBranchCode(e.value), $('#ddlOccasion')[0].selectedOptions[0].value);
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
                    $('#quota_available').val(res.AvailableTotal);
                    $('#added_bandhu').val(res.AddedBandhu);
                    $('#added_bhagini').val(res.AddedBhagini);
                    $('#added_total').val(res.AddedTotal);
                    $('#available_bandhu').val(res.AvailableBandhu);
                    $('#bandhu_available').val(res.AvailableBandhu);
                    $('#available_bhagini').val(res.AvailableBhagini);
                    $('#bhagini_available').val(res.AvailableBhagini);
                    $('#available_total').val(res.AvailableTotal);
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