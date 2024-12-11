<?php
include('../db.php');
$db->set_charset("utf8mb4");


if (!isset($_SESSION['usern'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: https://www.godjn.com');
}
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['usern']);
    header("location: https://www.godjn.com");
}

$name_dr_o = $_SESSION['usern'];

$formatedUpdateSevaQuery = "";
$formatedUpdateQuery = "UPDATE form_submissions SET ";
$finalUpdateQuery = "";
$formId = "0";
$isSeva = false;

if(isset($_GET['id'])){
    $formId =$_GET['id']; 
}

if(isset($_POST['update_sankhya']))
{
    $arrSevaUpdate = $_POST['arrSevaUpdate'];
    if($arrSevaUpdate != "") {
        $isSeva = true;
        $splitByPipe = explode('|', $arrSevaUpdate);
        $sevaTotalBandhu = 0;
        $sevaTotalBhagini = 0;
        foreach($splitByPipe as &$seva){
            $splitByColon = explode(':', $seva);
            $sevaTotalBandhu = $sevaTotalBandhu + (int)$splitByColon[2];
            $sevaTotalBhagini = $sevaTotalBhagini + (int)$splitByColon[3];
            $formatedUpdateSevaQuery = $formatedUpdateSevaQuery == '' ? "UPDATE seva_selected SET seva_brothers=" . $splitByColon[2] . ", seva_sisters=" . $splitByColon[3] . " WHERE id=" . $splitByColon[0] : $formatedUpdateSevaQuery . "; " . "UPDATE seva_selected SET seva_brothers=" . $splitByColon[2] . ", seva_sisters=" . $splitByColon[3] . " WHERE id=" . $splitByColon[0];
        }
    }

    $rowId = $_POST['idSevaUtsav'];
    $utsav_brothers = $_POST['utsav_brothers'];
    $utsav_sisters = $_POST['utsav_sisters'];
    $anugraha_brothers = $_POST['anugraha_brothers'];
    $anugraha_sisters = $_POST['anugraha_sisters'];
    $meeting_brothers = $_POST['meeting_brothers'];
    $meeting_sisters = $_POST['meeting_sisters'];
    $breakfast_brothers = $_POST['breakfast_brothers'];
    $breakfast_sisters = $_POST['breakfast_sisters'];
    $lunch_brothers = $_POST['lunch_brothers'];
    $lunch_sisters = $_POST['lunch_sisters'];
    $dinner_brothers = $_POST['dinner_brothers'];
    $dinner_sisters = $_POST['dinner_sisters'];

    $seva_brothers_deleted = ((int)$sevaTotalBandhu <= (int)$_POST['seva_total_brothers_previous']) ? ((int)$_POST['seva_total_brothers_previous'] - (int)$sevaTotalBandhu) : $sevaTotalBandhu;
    $seva_sisters_deleted = ((int)$sevaTotalBhagini <= (int)$_POST['seva_total_sisters_previous']) ? ((int)$_POST['seva_total_sisters_previous'] - (int)$sevaTotalBhagini) : $sevaTotalBhagini;
    $utsav_brothers_deleted = ((int)$utsav_brothers <= (int)$_POST['utsav_brothers_previous']) ? ((int)$_POST['utsav_brothers_previous'] - (int)$utsav_brothers) : $utsav_brothers;
    $utsav_sisters_deleted =  ((int)$utsav_sisters <= (int)$_POST['utsav_sisters_previous']) ? ((int)$_POST['utsav_sisters_previous'] - (int)$utsav_sisters) : $utsav_sisters;
    $anugraha_brothers_deleted = ((int)$anugraha_brothers <= (int)$_POST['anugraha_brothers_previous']) ? ((int)$_POST['anugraha_brothers_previous'] - (int)$anugraha_brothers) : $anugraha_brothers;
    $anugraha_sisters_deleted = ((int)$anugraha_sisters <= (int)$_POST['anugraha_sisters_previous']) ? ((int)$_POST['anugraha_sisters_previous'] - (int)$anugraha_sisters) : $anugraha_sisters;
    $meeting_brothers_deleted = ((int)$meeting_brothers <= (int)$_POST['meeting_brothers_previous']) ? ((int)$_POST['meeting_brothers_previous'] - (int)$meeting_brothers) : $meeting_brothers;
    $meeting_sisters_deleted = ((int)$meeting_sisters <= (int)$_POST['meeting_sisters_previous']) ? ((int)$_POST['meeting_sisters_previous'] - (int)$meeting_sisters) : $meeting_sisters;

    if($isSeva) {
        $formatedUpdateQuery = $formatedUpdateQuery . "seva_brothers=" . $sevaTotalBandhu . ", seva_sisters=" . $sevaTotalBhagini . ", seva_brothers_deleted=" . $seva_brothers_deleted . ", seva_sisters_deleted=" . $seva_sisters_deleted . ",";
    }
    if ($utsav_brothers != null && $utsav_sisters != null) {
        $formatedUpdateQuery = $formatedUpdateQuery . "utsav_brothers=" . $utsav_brothers . ", utsav_sisters=" . $utsav_sisters . ", utsav_brothers_deleted=" . $utsav_brothers_deleted . ", utsav_sisters_deleted=" . $utsav_sisters_deleted . ",";
    }
    if ($anugraha_brothers != null && $anugraha_sisters != null) {
        $formatedUpdateQuery = $formatedUpdateQuery . "anugraha_brothers=" . $anugraha_brothers . ", anugraha_sisters=" . $anugraha_sisters . ", anugraha_brothers_deleted=" . $anugraha_brothers_deleted . ", anugraha_sisters_deleted=" . $anugraha_sisters_deleted . ",";
    }
    if ($meeting_brothers != null && $meeting_sisters != null) {
        $formatedUpdateQuery = $formatedUpdateQuery . "meeting_brothers=" . $meeting_brothers . ", meeting_sisters=" . $meeting_sisters . ", meeting_brothers_deleted=" . $meeting_brothers_deleted . ", meeting_sisters_deleted=" . $meeting_sisters_deleted . ",";
    }

    $totalBandhu = ((int)$breakfast_brothers + (int)$lunch_brothers + (int)$dinner_brothers);
    $totalBhagini = ((int)$breakfast_sisters + (int)$lunch_sisters + (int)$dinner_sisters);
    $totalBandhuBhagini = (int)$totalBandhu + (int)$totalBhagini;

    $formatedUpdateQuery = $formatedUpdateQuery . "brothers=" . $totalBandhu . ", sisters=" . $totalBhagini . ", total_people=" . $totalBandhuBhagini . ", breakfast_brothers=" .$breakfast_brothers . ", breakfast_sisters=" . $breakfast_sisters . ", lunch_brothers=" . $lunch_brothers . ", lunch_sisters=" . $lunch_sisters . ", dinner_brothers=" . $dinner_brothers . ", dinner_sisters=" . $dinner_sisters . " WHERE id=" . $rowId;

    $finalUpdateQuery = $formatedUpdateSevaQuery == "" ? $formatedUpdateQuery : $formatedUpdateSevaQuery . "; " . $formatedUpdateQuery;

    try {
        $result = mysqli_multi_query($db, $finalUpdateQuery);
        if($result) {
            echo '<script>alert("माहिती यशस्वीरीत्या अद्ययावत झाली"); window.location.href="edit_main_record.php";</script>';
        } else {
            echo '<script>alert("तांत्रिक अडचणीमुळे माहिती अद्ययावत होत नाही");</script>';
        }
    } catch (Exception $e) {
        echo '<script>alert("तांत्रिक अडचणीमुळे माहिती अद्ययावत होत नाही");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>सेवा / उत्सव / मीटिंग / शिकवणी</title>
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
        .tableStyle{
			border: 1px solid #808080;
			width: 100%;			
		}
		.tableStyle th {
			font-weight: bold;
			background-color: #C0C0C0;
			border: 1px solid;
			padding-left:5px;
		}
		.tableStyle tr td {
			border: 1px solid;
			padding-left:5px;
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

  <!------------------------------------------ Main part Start --------------------------------------------------->

  <main id="main" class="main">
    <div class="row">
        <div class="col-lg-12">
            <div class="content">
                <div style="overflow: hidden;">
                    <div class="center" style="width: 30%; float: left; font-size:20px;">|| ॐ नमः शिवाय ||</div>
                    <div class="center" style="width: 40%; float: left; font-size:20px;">|| श्री गुरुदेव प्रसन्न ||</div>
                    <div class="center" style="width: 30%; float: right; font-size:20px;">|| ॐ नमः शिवाय ||</div>
                </div>		
		        <div style="text-align: left; margin-top: 10px;"><h5>परमपूज्य आईंच्या चरणी शिरसाष्टांग दंडवत</h1></div>
		        <div>
                <form method="POST" action="editsankhya.php" accept-charset="UTF-8">
                    <div>
                        <p>
                        <?php 
                            $query_form_records = "SELECT * FROM form_submissions WHERE id='$formId'";
                            $result_form_records = mysqli_query($db,$query_form_records);
                            $row_form_record = mysqli_fetch_array($result_form_records)
                        ?>
                            आमच्या <b><?php echo $row_form_record['branch'];?></b> शाखेतून, 
                            <?php echo $row_form_record['brothers'];?> बंधू व 
                            <?php echo $row_form_record['sisters'];?> भगिनी एकूण 
                            <?php echo $row_form_record['total_people'];?> मंडळी 
                            दिनांक: <?php $convertedStartDate = date("d-M-Y", strtotime($row_form_record['start_date']));
                            echo $convertedStartDate;?> ते दिनांक <?php $convertedEndDate = date("d-M-Y", strtotime($row_form_record['end_date']));
                            echo $convertedEndDate;?> 
                            पर्यंत - <?php echo $row_form_record['duration'];?> दिवसांकरिता श्रीहरिमंदिरात येऊ इच्छितात, तरी कृपया परवानगी मिळावी.
                        </p>
                </div>
                <div>
                    तपशील खालीलप्रमाणे,
                </div>
                <div>
                <?php 
                    $query_seva_records = "SELECT * FROM seva_selected WHERE form_submission_id='$formId'";
                    $result_seva_records = mysqli_query($db,$query_seva_records);
                    if (mysqli_num_rows($result_seva_records) > 0)
                    {
                ?>
                    <input type="hidden" name="seva_total_brothers_previous" id="seva_total_brothers_previous" value="<?php echo $row_form_record['seva_brothers'];?>">
                    <input type="hidden" name="seva_total_sisters_previous" id="seva_total_sisters_previous" value="<?php echo $row_form_record['seva_sisters'];?>">
                    <table id="tblSeva" border="1" class="tableStyle">
                        <tr>
                            <th>सेवा</th>
                            <th>बंधू</th>
                            <th>भगिनी</th>
                        </tr>
                <?php 
                    while($row_seva_record = mysqli_fetch_array($result_seva_records))
                    {
                ?>
                    <tr>
                        <td style="display:none;"><?php echo $row_seva_record['id'];?></td>
                        <td><?php echo $row_seva_record['seva_type'];?></td>
                        <td>
                            <input id="bandhu" type="number" name="seva_brothers" min="0" max="100" value="<?php echo $row_seva_record['seva_brothers'];?>" placeholder="0">
                            <input type="hidden" name="seva_brothers_previous" id="seva_brothers_previous" value="<?php echo $row_seva_record['seva_brothers'];?>">
                        </td>
                        <td>
                            <input id="bhagini" type="number" name="seva_sisters" min="0" max="100" value="<?php echo $row_seva_record['seva_sisters'];?>" placeholder="0">
                            <input type="hidden" name="seva_sisters_previous" id="seva_sisters_previous" value="<?php echo $row_seva_record['seva_sisters'];?>">
                        </td>
                    </tr>
                <?php
                    }
                    }
                ?>
                    </table>
                    <input type="hidden" name="arrSevaUpdate" id="arrSevaUpdate">
                </div>
                <br>
                <div>
                <?php if ($row_form_record["utsav_type"] != "" ||
                            $row_form_record["anugraha_type"] != "" ||
                            $row_form_record["meeting_type"] != "") {
                ?>
                    <table border="1" id="seva" class="tableStyle">
                        <tr>
                            <th>उत्सव/अनुग्रह/मीटिंग/शिकवणी</th>
                            <th>नाव</th>
                            <th>बंधू</th>
                            <th>भगिनी</th>
                        </tr>
                        <?php if ($row_form_record["utsav_type"] != "") { ?>
                        <tr>
                            <td>उत्सव</td>
                            <td><?php echo $row_form_record["utsav_type"];?></td>
                            <td>
                                <input id="utsav_brothers" type="number" name="utsav_brothers" min="0" max="100" value="<?php echo $row_form_record['utsav_brothers'];?>" placeholder="0">
                                <input type="hidden" name="utsav_brothers_previous" id="utsav_brothers_previous" value="<?php echo $row_form_record['utsav_brothers'];?>">
                            </td>
                            <td>
                                <input id="utsav_sisters" type="number" name="utsav_sisters" min="0" max="100" value="<?php echo $row_form_record['utsav_sisters'];?>" placeholder="0">
                                <input type="hidden" name="utsav_sisters_previous" id="utsav_sisters_previous" value="<?php echo $row_form_record['utsav_sisters'];?>">
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($row_form_record["anugraha_type"] != "") { ?>
                        <tr>
                            <td>अनुग्रह</td>
                            <td><?php echo $row_form_record["anugraha_type"];?></td>
                            <td>
                                <input id="anugraha_brothers" type="number" name="anugraha_brothers" min="0" max="100"  value="<?php echo $row_form_record['anugraha_brothers'];?>" placeholder="0">
                                <input type="hidden" name="anugraha_brothers_previous" id="anugraha_brothers_previous" value="<?php echo $row_form_record['anugraha_brothers'];?>">
                            </td>
                            <td>
                                <input id="anugraha_sisters" type="number" name="anugraha_sisters" min="0" max="100"  value="<?php echo $row_form_record['anugraha_sisters'];?>" placeholder="0">
                                <input type="hidden" name="anugraha_sisters_previous" id="anugraha_sisters_previous" value="<?php echo $row_form_record['anugraha_sisters'];?>">
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($row_form_record["meeting_type"] != "") { ?>
                        <tr>
                            <td>मीटिंग / शिकवणी</td>
                            <td><?php echo $row_form_record["meeting_type"];?></td>
                            <td>
                                <input id="meeting_brothers" type="number" name="meeting_brothers" min="0" max="100"  value="<?php echo $row_form_record['meeting_brothers'];?>" placeholder="0">
                                <input type="hidden" name="meeting_brothers_previous" id="meeting_brothers_previous" value="<?php echo $row_form_record['meeting_brothers'];?>">
                            </td>
                            <td>
                                <input id="meeting_sisters" type="number" name="meeting_sisters" min="0" max="100"  value="<?php echo $row_form_record['meeting_sisters'];?>" placeholder="0">
                                <input type="hidden" name="meeting_sisters_previous" id="meeting_sisters_previous" value="<?php echo $row_form_record['meeting_sisters'];?>">
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                <?php
                }
                ?>
                </div>
                <br>
                <div>
                    <table border="1" id="Prasad" class="tableStyle">
                        <tr>
                            <th>प्रसादाची वेळ</th>
                            <th>बंधू</th>
                            <th>भगिनी</th>
                        </tr>
                        <tr>
                            <td>सकाळचा नाष्टा</td>
                            <td><input id="breakfast_brothers" type="number" name="breakfast_brothers" min="0" max="100"  value="<?php echo $row_form_record['breakfast_brothers'];?>" placeholder="0"></td>
                            <td><input id="breakfast_sisters" type="number" name="breakfast_sisters" min="0" max="100"  value="<?php echo $row_form_record['breakfast_sisters'];?>" placeholder="0"></td>
                        </tr>
                        <tr>
                            <td>दुपारचा प्रसाद</td>
                            <td><input id="lunch_brothers" type="number" name="lunch_brothers" min="0" max="100"  value="<?php echo $row_form_record['lunch_brothers'];?>" placeholder="0"></td>
                            <td><input id="lunch_sisters" type="number" name="lunch_sisters" min="0" max="100"  value="<?php echo $row_form_record['lunch_sisters'];?>" placeholder="0"></td>
                        </tr>
                        <tr>
                            <td>रात्रीचा प्रसाद</td>
                            <td><input id="dinner_brothers" type="number" name="dinner_brothers" min="0" max="100"  value="<?php echo $row_form_record['dinner_brothers'];?>" placeholder="0"></td>
                            <td><input id="dinner_sisters" type="number" name="dinner_sisters" min="0" max="100"  value="<?php echo $row_form_record['dinner_sisters'];?>" placeholder="0"></td>
                        </tr>
                    </table>
                </div>
                <br>
                <div>
                <center>
                    <input type="hidden" name="idSevaUtsav" id="idSevaUtsav" value="<?php echo $row_form_record['id'];?>">
                    <button id="btnUpdateSankhya" type="submit" name="update_sankhya" class="btn btn-success">अद्ययावत करा</button>
                </center>
                </div>
                </form>
            </div>
        </div>
    </div>
  </main>

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer"></footer><!-- End Footer -->

    <script>
        $('#btnUpdateSankhya').click(function(){
            if(ValidateInputs()) {
                FormatSevaUpdate();
                return true;
            }
            return false;
        });
        function ValidateInputs() {
            var returnVal = true;
            var errMsg = "";
            $("#tblSeva tr").each(function(i, row){
                if(i>0){
                    var sevaName = "";
                    $(row).find('td').each(function(){
                        var rowSevaBandhu = 0, rowSevaBandhuPrev = 0, rowSevaBhagini = 0, rowSevaBhaginiPrev = 0;
                        if ($(this).index() == 1) {
                            sevaName = $(this)[0].innerText;
                        }
                        $(this).find('input').each(function() {
                            if ($(this)[0].id == "bandhu") {
                                rowSevaBandhu = parseInt($(this).val());
                            } else if ($(this)[0].id == "seva_brothers_previous") {
                                rowSevaBandhuPrev = parseInt($(this).val());
                            } else if ($(this)[0].id == "bhagini") {
                                rowSevaBhagini = parseInt($(this).val());
                            } else if ($(this)[0].id == "seva_sisters_previous") {
                                rowSevaBhaginiPrev = parseInt($(this).val());
                            }
                        });
                        if (rowSevaBandhu > rowSevaBandhuPrev) {
                            errMsg = errMsg + sevaName + " सेवेची बंधू संख्या " + rowSevaBandhuPrev + " पेक्षा मोठी असू शकत नाही, ";
                        }
                        if (rowSevaBhagini > rowSevaBhaginiPrev) {
                            errMsg = errMsg + sevaName + " सेवेची भगिनी संख्या " + rowSevaBhaginiPrev + " पेक्षा मोठी असू शकत नाही, ";
                        }
                    });
                }
            });
            if ($('#utsav_brothers').val() != undefined &&
                $('#utsav_brothers_previous').val() != undefined &&
                parseInt($('#utsav_brothers').val()) > parseInt($('#utsav_brothers_previous').val())) {
                    errMsg = errMsg + "उत्सवाची बंधू संख्या " + parseInt($('#utsav_brothers_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            if ($('#utsav_sisters').val() != undefined &&
                $('#utsav_sisters_previous').val() != undefined &&
                parseInt($('#utsav_sisters').val()) > parseInt($('#utsav_sisters_previous').val())) {
                    errMsg = errMsg + "उत्सवाची भगिनी संख्या " + parseInt($('#utsav_sisters_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            if ($('#anugraha_brothers').val() != undefined &&
                $('#anugraha_brothers_previous').val() != undefined &&
                parseInt($('#anugraha_brothers').val()) > parseInt($('#anugraha_brothers_previous').val())) {
                    errMsg = errMsg + "अनुग्रहाची बंधू संख्या " + parseInt($('#anugraha_brothers_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            if ($('#anugraha_sisters').val() != undefined &&
                $('#anugraha_sisters_previous').val() != undefined &&
                parseInt($('#anugraha_sisters').val()) > parseInt($('#anugraha_sisters_previous').val())) {
                    errMsg = errMsg + "अनुग्रहाची भगिनी संख्या " + parseInt($('#anugraha_sisters_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            if ($('#meeting_brothers').val() != undefined &&
                $('#meeting_brothers_previous').val() != undefined &&
                parseInt($('#meeting_brothers').val()) > parseInt($('#meeting_brothers_previous').val())) {
                    errMsg = errMsg + "मीटिंग / शिकवणी बंधू संख्या " + parseInt($('#meeting_brothers_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            if ($('#meeting_sisters').val() != undefined &&
                $('#meeting_sisters_previous').val() != undefined &&
                parseInt($('#meeting_sisters').val()) > parseInt($('#meeting_sisters_previous').val())) {
                    errMsg = errMsg + "मीटिंग / शिकवणी भगिनी संख्या " + parseInt($('#meeting_sisters_previous').val()) + " पेक्षा मोठी असू शकत नाही, ";
            }
            
            if (errMsg != "") {
                alert(errMsg);
                return false;
            }

            var prasadTotalBandhu = parseInt($('#breakfast_brothers').val()) + parseInt($('#lunch_brothers').val()) + parseInt($('#dinner_brothers').val());
            var prasadTotalBhagini = parseInt($('#breakfast_sisters').val()) + parseInt($('#lunch_sisters').val()) + parseInt($('#dinner_sisters').val());

            var totalBandhu = ($('#utsav_brothers').val() != undefined ? parseInt($('#utsav_brothers').val()) : 0) + 
                            ($('#anugraha_brothers').val() != undefined ? parseInt($('#anugraha_brothers').val()) : 0) + 
                            ($('#meeting_brothers').val() != undefined ? parseInt($('#meeting_brothers').val()) : 0);
            var totalBhagini = ($('#utsav_sisters').val() != undefined ? parseInt($('#utsav_sisters').val()) : 0) + 
                            ($('#anugraha_sisters').val() != undefined ? parseInt($('#anugraha_sisters').val()) : 0) + 
                            ($('#meeting_sisters').val() != undefined ? parseInt($('#meeting_sisters').val()) : 0);

            var totalSevaBandhu = 0;
            var totalSevaBhagini = 0;

            $("#tblSeva tr").each(function(i, row){
                if(i>0){
                    $(row).find('td').each(function(){
                        $(this).find('input').each(function() {
                            if ($(this)[0].id == "bandhu") {
                                totalSevaBandhu = parseInt(totalSevaBandhu) + parseInt($(this).val());
                            } else if ($(this)[0].id == "bhagini") {
                                totalSevaBhagini = parseInt(totalSevaBhagini) + parseInt($(this).val());
                            }
                        });
                    });
                }
            });

            if ((parseInt(prasadTotalBandhu) != (parseInt(totalBandhu) + parseInt(totalSevaBandhu))) ||
                (parseInt(prasadTotalBhagini) != (parseInt(totalBhagini) + (parseInt(totalSevaBhagini))))) {
                    alert("कृपया प्रसादाची बंधू किंवा भगिनी संख्या तपासा");
                    return false;
                }
            return true;    
        }
        function FormatSevaUpdate() {
            $("#tblSeva tr").each(function(i, row){
                if(i>0){
                    var particularSeva;
                    $(row).find('td').each(function(){
                        if($(this)[0].childNodes.length > 1) {
                            $(this).find('input[type=number]').each(function() {
                                particularSeva = particularSeva == undefined ? $(this).val() : particularSeva + ':' + $(this).val();
                            });
                        } else {
                            particularSeva = particularSeva == undefined ? $(this).text() : particularSeva + ':' + $(this).text();
                        }
                    });
                    if($('#arrSevaUpdate').val() == "") {
                        $('#arrSevaUpdate').val(particularSeva);
                    } else {
                        $('#arrSevaUpdate').val($('#arrSevaUpdate').val() + '|' + particularSeva);       
                    }            
                }
            });
        }
    </script>

    </body>
</html>