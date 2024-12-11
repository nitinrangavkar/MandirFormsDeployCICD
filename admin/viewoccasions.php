<?php
include("../db.php");

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
$page_visit = "viewoccasion";

$query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
mysqli_query($db,$query_log);

// Handle search/filtering
if (isset($_POST['string_search'])) {
    $valueToSearch = $_POST['valueToSearch'];
    // Search in specific columns
    $query_all_record = "SELECT *, form_submissions.id AS formid,
                          form_submissions.start_date AS FormStartDate,
                          form_submissions.end_date AS FormEndDate,
                          CASE 
                            WHEN occasions.occasion_code = 'GEN' THEN 
                            (CONCAT(occasions.occasion_code,'-',
                            DATE_FORMAT(occasions.start_date,'%b-%Y'),'-',form_submissions.id))
                          ELSE
                            (CONCAT(occasions.occasion_code,'-',form_submissions.id)) 
                          END
                            AS formcode
                          FROM `form_submissions`
                          INNER JOIN occasions ON form_submissions.occasion_id=occasions.id
                          WHERE CONCAT(form_submissions.id, branch, form_submissions.start_date, form_submissions.end_date, occasions.occasion) LIKE '%" . mysqli_real_escape_string($db, $valueToSearch) . "%' ORDER BY form_submissions.id DESC";
} else {
    $query_all_record = "SELECT *, form_submissions.id AS formid,
                          form_submissions.start_date AS FormStartDate,
                          form_submissions.end_date AS FormEndDate,
                          CASE 
                            WHEN occasions.occasion_code = 'GEN' THEN 
                            (CONCAT(occasions.occasion_code,'-',
                            DATE_FORMAT(occasions.start_date,'%b-%Y'),'-',form_submissions.id))
                          ELSE
                            (CONCAT(occasions.occasion_code,'-',form_submissions.id)) 
                          END
                            AS formcode
                          FROM `form_submissions`
                          INNER JOIN occasions ON form_submissions.occasion_id=occasions.id
                          ORDER BY form_submissions.id DESC";
}
$result_all_record = mysqli_query($db, $query_all_record);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>View Occasions</title>
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

    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f5f5f5;
            color: #333;
            margin-top: 40px;
            font-size:13px;
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

  <!------------------------------------------ Main part Start --------------------------------------------------->

  <main id="main" class="main">
    <div class="row">
        <div class="col-lg-12">
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Batch</th>
                    <th>Occasion</th>
                    <th>Shakha</th>
                    <th>Letter Dated</th>
                    <th>Form Date</th>
                    <th>Bandhu</th>
                    <th>Bhagini</th>
                    <th>Total</th>
                    <th>Period Of Stay</th>
                    <!-- <th>Duration</th> -->
                    <th>Edit</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                    // Display data rows
                    while ($row_all_record = mysqli_fetch_assoc($result_all_record)) {
                    ?>
                      <tr>
                        <td><?php echo $row_all_record['refno']; ?></td>
                        <td><?php echo $row_all_record['batch']; ?></td>
                        <td><?php echo $row_all_record['occasion']; ?></td>
                        <?php if ($row_all_record['branch'] === "Belagavi Shriharimandir"): ?>
                          <td><?php echo $row_all_record['branch']." - ".$row_all_record['location']; ?></td>
                        <?php else: ?>
                          <td><?php echo $row_all_record['branch']; ?></td>
                        <?php endif; ?>  
                        <td><?php $convertedLetterDate = date("d-M-Y", strtotime($row_all_record['letter_dated']));
                              echo $convertedLetterDate;?></td>
                        <td><?php $convertedFormDate = date("d-M-Y", strtotime($row_all_record['form_dated']));
                              echo $convertedFormDate;?></td>
                        <td><?php echo $row_all_record['brothers']; ?></td>
                        <td><?php echo $row_all_record['sisters']; ?></td>
                        <td><?php echo $row_all_record['total_people']; ?></td>
                        <td>From <?php $convertedStartDate = date("d-M-Y", strtotime($row_all_record['FormStartDate']));
                              echo $convertedStartDate;?><br>To <?php $convertedEndDate = date("d-M-Y", strtotime($row_all_record['FormEndDate']));
                              echo $convertedEndDate;?></td>
                        <!-- <td><?php echo $row_all_record['duration']; ?></td> -->
                        <td>
                          <?php 
                          if (str_contains(htmlspecialchars($row_all_record['utsav_type']), "Punyatithi")) {
                          ?>
                            <a href="editpunyatithi.php?id=<?php echo htmlspecialchars($row_all_record['formid']); ?>" class="btn btn-primary btn-sm">Edit</a>
                          <?php 
                          } else {
                          ?>
                            <a href="editoccasionform.php?id=<?php echo htmlspecialchars($row_all_record['formid']); ?>" class="btn btn-primary btn-sm">Edit</a>
                          <?php 
                          }
                          ?>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>

                </tbody>
              </table>
              <!-- End Table with stripped rows -->

        </div>
    </div>
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

</body>
</html>
