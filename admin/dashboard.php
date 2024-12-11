<?php
include('../db.php');

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
$page_visit = "dashboard";

$query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
mysqli_query($db,$query_log);

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
  <!-- Close Input type marathi -->

  <!-- Include DataTables CSS -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

  <!-- Include DataTables JavaScript -->
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

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
        .NoDataFound {
          text-align: center;
          font-weight: bold;
          color: #f00;
        }
        .ReportHeading {
          text-align: center;
          font-weight: bold;
          color: #012970;
        }
        .tablePrasadReport {
          border: 1px solid #000 !important;
        }
        .tablePrasadReport tbody, tr, th, td, thead, tr {
          border: 1px solid #000 !important;
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

<!-- Form for user registration -->
<section class="section dashboard">
	<div class="row">
    <div class="col-lg-12" style="border: 1px solid #D3D3D3;">
      <div class="ReportHeading">Prasad Report</div>
      <div style="float:left; height:30px; padding-top:8px;">Select Date - &nbsp;</div>
        <div id="datepicker1"  class="input-group date" data-date-format="dd-MM-yyyy" style="width:200px; float:left;">
        <input class="form-control" type="text" id="selectedDate" readonly />
        <span class="input-group-addon">
            <i class="glyphicon glyphicon-calendar"></i>
        </span>
        </div>
        <div style="float:right;">
        <form method="post" action="daily_prasad_report.php">
            <input type="hidden" name="selectedDate" id="selectedDateDailyPrasadReport">
            <input type="hidden" name="isExport" id="isExport" value="true">
              <button type="submit" id="btnExportDailyPrasadReport" class="btn btn-primary">Export to PDF</button>
          </form>
        </div>
        <br>
        <div id="dailyPrasadReport" style="padding-top:5px;">
        </div>
    </div>
    </div>
    <br>
    <div class="row" style="border: 1px solid #D3D3D3;">
        <div class="col-lg-12" >
          <div class="ReportHeading">Seva Type Count of Occasion</div>
          <div class="col-md-5">
              <label for="SelectDate">Select Date</label>
              <div id="datepicker2"  class="input-group date" data-date-format="dd-MM-yyyy" style="width:200px;">
                <input class="form-control" type="text" id="selectedDate2" readonly />
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </span>
              </div>
          </div>
          <div class="col-md-5">
              <label for="SelectOccasion">Select Occasion</label>
              <?php
              $query_utsav = "SELECT DISTINCT id, occasion, Start_Date,
                              DATE_FORMAT(Start_Date, '%d-%M-%Y') AS FormattedStartDate,
                              End_Date,
                              DATE_FORMAT(End_Date, '%d-%M-%Y') AS FormattedEndDate,
                              (End_Date - Start_Date)+1 AS Duration,
                              batch, occasion_key, occasion_code,
                              CONCAT_WS(' B - ',
                              IF(LENGTH(occasion),occasion,NULL),
                              IF(LENGTH(batch),batch,NULL)) AS CombineName
                              FROM occasions";
              $result_utsav = mysqli_query($db, $query_utsav);
              $row_utsav = mysqli_fetch_array($result_utsav);
              ?>
                <select class="form-control" id="ddlOccasionSevaType" name="occasionId" onchange="CallSevaTypeOfOccasionReport(this)">
                  <option value="0">Select</option>
                <?php
                mysqli_data_seek($result_utsav, 0 );
                while ($row_occasion = mysqli_fetch_array($result_utsav)) {
                    echo '<option value="'.$row_occasion['id'].'" startdate="' . $row_occasion['FormattedStartDate'] . '" enddate="' . $row_occasion['FormattedEndDate'] . '" duration="' . $row_occasion['Duration'] . '" occasioncode="' . $row_occasion['occasion_code'] . '">' . htmlspecialchars($row_occasion['CombineName']) . '</option>';
                }
                ?>
                </select>
          </div>
          <div class="col-md-2" style="padding-top:30px;">
            <form method="post" action="sevatype_occasion_report.php">
                <input type="hidden" name="selectedDate" id="selectedDateSevaOccasionReport">
                <input type="hidden" name="selectedOccasionId" id="selectedOccasionIdSevaOccasionReport">
                <input type="hidden" name="selectedOccasionCode" id="selectedOccasionCodeSevaOccasionReport">
                <input type="hidden" name="isExport" id="isExport" value="true">
                <input type="hidden" name="selectedOccasionNameSevaOccasionReport" id="selectedOccasionNameSevaOccasionReport" value="true">
                  <button type="submit" id="btnExportDailyPrasadReport" onclick="SetValuesSevaOccasionReport()" class="btn btn-primary">Export to PDF</button>
              </form>
            </div>
        </div>
        <div id="sevaTypeOfOccasionReport" class="col-lg-12">
        </div>
    </div>
    <div class="row" style="border: 1px solid #D3D3D3;">
        <div class="col-lg-12" >
          <div class="ReportHeading"> Occasion Date range report</div>
          <!-- <div class="col-md-6">
              <label for="SelectDate">Select Date</label>
              <div id="datepicker2"  class="input-group date" data-date-format="dd-MM-yyyy" style="width:200px;">
                <input class="form-control" type="text" id="selectedDate2" readonly />
                <span class="input-group-addon">
                    <i class="glyphicon glyphicon-calendar"></i>
                </span>
              </div>
          </div> -->
          <div class="col-md-6">
              <label for="SelectOccasion">Select Occasion</label>
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
                              FROM occasions";
              $result_utsav = mysqli_query($db, $query_utsav);
              $row_utsav = mysqli_fetch_array($result_utsav);
              ?>
                <select class="form-control" id="ddlOccasion" name="occasionId" onchange="CallOccasionDateRangeReport(this, false)">
                  <option value="0">Select</option>
                <?php
                mysqli_data_seek($result_utsav, 0 );
                while ($row_occasion = mysqli_fetch_array($result_utsav)) {
                    echo '<option value="'.$row_occasion['id'].'" startdate="' . $row_occasion['FormattedStartDate'] . '" enddate="' . $row_occasion['FormattedEndDate'] . '" duration="' . $row_occasion['Duration'] . '" occasioncode="' . $row_occasion['occasion_code'] . '">' . htmlspecialchars($row_occasion['CombineName']) . '</option>';
                }
                ?>
                </select>
          </div>
          <div class="col-md-6 mr-0 mt-5">
            <form method="post" action="occassion_date_range_report.php">
              <input type="hidden" name="selectedOccassionName" id="selectedOccassionName">
              <input type="hidden" name="selectedOccassionId" id="selectedOccassionId">
              <input type="hidden" name="selectedOccassionCode" id="selectedOccassionCode">
              <input type="hidden" name="isExport" id="isExport" value="true">
              <button type="submit" id="btnExportDailyPrasadReport" onclick="SetValuesOccasionDateRange()" class="btn btn-primary">Export to PDF</button>
            </form>
          </div>
        </div>
        <div id="occassion_date_range_report" class="col-lg-12">
        </div>
    </div>
    <br>
    <div class="row" style="border: 1px solid #D3D3D3;">
      <div class="col-lg-12" style="border: 1px solid #D3D3D3;">
        <div class="ReportHeading">Occasion Desk report</div>
        <div style="float:left; height:30px; padding-top:8px;">Select Date - &nbsp;</div>
          <div id="datepicker3"  class="input-group date" data-date-format="dd-MM-yyyy" style="width:200px; float:left;">
          <input class="form-control" type="text" id="selectedDate3" readonly />
          <span class="input-group-addon">
              <i class="glyphicon glyphicon-calendar"></i>
          </span>
          </div>
          <div style="float:right;">
          <form method="post" action="occassion_desk_report.php">
              <input type="hidden" name="selectedDate3" id="selectedDeskReportDate">
              <input type="hidden" name="isExport" id="isExport" value="true">
                <button type="submit" id="btnDeskReport" class="btn btn-primary">Export to PDF</button>
            </form>
          </div>
          <br>
      </div>
        <div id="occassion_desk_report" class="col-lg-12">
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-lg-12" style="border: 1px solid #D3D3D3;">
        <div class="ReportHeading">उपस्थिती रिपोर्ट</div>
          <div>
            <select name="utsavache_naav" class="form-control" id="ddlUtsav" onchange="CallDataPage(this)" style="width:50%;">
              <option value="0">कृपया निवडा</option>
                <?php 
                    $query_CombineCategory = "SELECT id, Category, utsavache_naav, Category_Name,
                                              CONCAT_WS(' - ',
                                              IF(LENGTH(`Category`),`Category`,NULL),
                                              IF(LENGTH(`utsavache_naav`),`utsavache_naav`,NULL),
                                              IF(LENGTH(`Category_Name`),`Category_Name`,NULL),
                                              IF(LENGTH(`Gat_Kramank`),`Gat_Kramank`,NULL)) AS CombineName FROM `sevautsav`
                                              ORDER BY start_date ASC";
                    $result_CombineCategory = mysqli_query($db, $query_CombineCategory);
                    while ($row_CombineCategory = mysqli_fetch_assoc($result_CombineCategory)) {
                        $id = $row_CombineCategory['id'];
                    ?>
                    <?php
                      echo '<option category="' . htmlspecialchars($row_CombineCategory['Category']) . '" categoryname="' . htmlspecialchars($row_CombineCategory['Category_Name']) . '" utsavname="' . htmlspecialchars($row_CombineCategory['utsavache_naav']) . '" value="' . htmlspecialchars($row_CombineCategory['CombineName']) . '"' . $selected . '>' 
                      . htmlspecialchars($row_CombineCategory['CombineName']) 
                      . '</option>';
                    }
                ?>
            </select>
          </div>
          <div>
            <div id="upasthiti_report"></div>
          </div>
        </div>
    </div> -->
</section>
    
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">


  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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

  <script>
    $(document).ready(function () {
      var nowDate = new Date(); 
      var date = nowDate.getDate()+'-'+nowDate.toLocaleString('default', { month: 'long' })+'-'+nowDate.getFullYear();
      CallDailyPrasadReportGeneration(date, false);
      CallOccasionDeskReport(date, false);
    });
    $(function () {
      $("#datepicker1").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        clearBtn: true
      }).datepicker('update', new Date()).change(dateChanged);
      $("#datepicker2").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        clearBtn: true
      }).datepicker('update', new Date()).change(dateChanged2);
      $("#datepicker3").datepicker({ 
        format: 'dd-MM-yyyy',
        autoclose: true, 
        todayHighlight: true,
        clearBtn: true
      }).datepicker('update', new Date()).change(dateChanged3);
    });
    function dateChanged(ev) {
      $(this).datepicker('hide');
      CallDailyPrasadReportGeneration($('#selectedDate').val(), false);
    }
    function dateChanged2(ev) {
      $(this).datepicker('hide');
      GenerateSevaTypeOfOccasionReport($('#selectedDate2').val(), $('#ddlOccasionSevaType')[0].selectedOptions[0].value, $('#ddlOccasionSevaType')[0].selectedOptions[0].attributes['occasioncode'].value);
    }
    function dateChanged3(ev) {
      $(this).datepicker('hide');
      CallOccasionDeskReport($('#selectedDate3').val(), false);
    }
    function CallDailyPrasadReportGeneration(selectedDate, isExport) {
      $('#selectedDateDailyPrasadReport').val(selectedDate);
      $.post('daily_prasad_report.php', { selectedDate: selectedDate, isExport: isExport }, function(data) {
        $('#dailyPrasadReport').html(data);
      });
    }
    function CallDataPage(e) {
      var category = e.selectedOptions[0].getAttribute("category");
      var categoryname = e.selectedOptions[0].getAttribute("categoryname");
      var utsavname = e.selectedOptions[0].getAttribute("utsavname");
      $.post('upasthiti_report.php', { category: category, categoryname: categoryname, utsavname: utsavname }, function(data) {
        $('#upasthiti_report').html(data);
      });
    }
    function CallSevaTypeOfOccasionReport(e) {
      if (e.selectedOptions[0].value != "0") {
        GenerateSevaTypeOfOccasionReport($('#selectedDate2').val(), e.selectedOptions[0].value, e.selectedOptions[0].attributes['occasioncode'].value, false, e.selectedOptions[0].text);
      } else {
        alert('Please Select Occasion');
      }
    }
    function submitDeskReport() {
      const selectedOccasion = $('#ddlOccasion4').val();
      const selectedOccasionCode = $('#ddlOccasion4 option:selected').attr('occasioncode');

        const startDate = $('#selectedDate3').val();
        const endDate = $('#selectedDate4').val();

        if ((startDate && endDate) || selectedOccasion !== "0") {
            GenerateCustomReport(selectedOccasion, selectedOccasionCode, startDate, endDate);
        } else {
            alert('Please fill start date and end date or occassion before generating the report!');
        }
    }
    function GenerateCustomReport(selectedOccasion, selectedOccasionCode, startDate, endDate) {
        $.post('occassion_desk_report.php', {
            selectedOccasion,
            selectedOccasionCode,
            startDate,
            endDate,
        }, function(data) {
            console.log(data);
            $('#occassion_desk_report').html(data);

            // Initialize DataTable after the table is rendered
            if ($.fn.DataTable.isDataTable('#occassionTable')) {
                $('#occassionTable').DataTable().destroy(); // Destroy existing instance if any
            }

            // Initialize DataTable after the table content is loaded
            $('#occassionTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "lengthChange": false,
                "pageLength": 10 // Set the number of records per page
            });
        });
    }
    function SetValuesOccasionDateRange() {
      $('#selectedOccassionName').val($('#ddlOccasion')[0].selectedOptions[0].text);
      $('#selectedOccassionId').val($('#ddlOccasion')[0].selectedOptions[0].value);
      $('#selectedOccassionCode').val($('#ddlOccasion')[0].selectedOptions[0].getAttribute('occasioncode'));
    }
    function CallOccasionDateRangeReport(e, isExport) {
      $.post('occassion_date_range_report.php', { selectedOccassionName: e.selectedOptions[0].text, selectedOccassionId: e.selectedOptions[0].value, selectedOccassionCode: e.selectedOptions[0].getAttribute('occasioncode'), isExport: isExport }, function(data) {
        $('#occassion_date_range_report').html(data);
      });
    }
    function CallOccasionDeskReport(inputDate, isExport) {
      $('#selectedDeskReportDate').val(inputDate);
      $.post('occassion_desk_report.php', { selectedDate3: inputDate, isExport: isExport }, function(data) {
        $('#occassion_desk_report').html(data);
      });
    }
    function GenerateSevaTypeOfOccasionReport(selectedDate, selectedOccasion, selectedOccasionCode, isExport, selectedOccasionNameSevaOccasionReport) {
      if (selectedOccasion != "0") {
        $('#selectedDateSevaOccasionReport').val(selectedDate);
        $('#selectedOccasionIdSevaOccasionReport').val(selectedOccasion);
        $('#selectedOccasionCodeSevaOccasionReport').val(selectedOccasionCode);
        $('#selectedOccasionNameSevaOccasionReport').val(selectedOccasionNameSevaOccasionReport);
        $.post('sevatype_occasion_report.php', { selectedDate: selectedDate, selectedOccasionId: selectedOccasion, selectedOccasionCode: selectedOccasionCode, isExport: isExport, selectedOccasionNameSevaOccasionReport: selectedOccasionNameSevaOccasionReport }, function(data) {
          $('#sevaTypeOfOccasionReport').html(data);
        });
      } else {
          alert('Please Select Occasion');
      }
    }
    function GenerateOccasionDateRangeReport(selectedOccassionName, isExport) {
      if (selectedOccassionName) {
        $.post('occassion_date_range_report.php', { selectedOccassionName: selectedOccassionName, isExport }, function(data) {
          $('#occassion_date_range_report').html(data);
        });
      } else {
          alert('Please Select Occasion');
      }
    }
  </script>


    <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>

</body>
</html>
