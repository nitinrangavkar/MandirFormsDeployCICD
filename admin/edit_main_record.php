<?php
include("../db.php");

// Handle search/filtering
if (isset($_POST['string_search'])) {
    $valueToSearch = $_POST['valueToSearch'];
    // Search in specific columns
    $query_all_record = "SELECT * FROM `form_submissions` WHERE CONCAT(id, branch, start_date, end_date) LIKE '%" . mysqli_real_escape_string($db, $valueToSearch) . "%'";
} else {
    $query_all_record = "SELECT `id`,
                `branch`,
                `brothers`,
                `sisters`,
                `total_people`,
                `start_date`,
                `end_date`,
                `duration`,
                `arrival_time`,
                `arrival_hour`,
                `breakfast_brothers`,
                `breakfast_sisters`,
                `lunch_brothers`,
                `lunch_sisters`,
                `dinner_brothers`,
                `dinner_sisters`,
                `seva_type`,
                `utsav_type`,
                `anugraha_type`,
                `meeting_type`,
                `seva_brothers`,
                `seva_sisters`,
                `utsav_brothers`,
                `utsav_sisters`,
                `anugraha_brothers`,
                `anugraha_sisters`,
                `meeting_brothers`,
                `meeting_sisters` FROM form_submissions";
}
$result_all_record = mysqli_query($db, $query_all_record);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>सेवा / उत्सव - बंधू / भगिनी संख्या</title>
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
            line-height: 1.8;
            margin-top: 40px;
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
                    <th>शाखा</th>
					<th>बंधू</th>
					<th>भगिनी</th>
					<th>एकूण</th>
					<th>दिनांक पासून</th>
					<th>दिनांक पर्यंत</th>
					<th>कालावधी</th>
					<th>आगमन वेळ</th>
					<th>आगमन वेळ</th>
					<th>नाष्टा बंधू</th>
					<th>नाष्टा भगिनी</th>
					<th>दुपारचा प्रसाद बंधू</th>
					<th>दुपारचा प्रसाद भगिनी</th>
					<th>रात्रीचा प्रसाद बंधू</th>
					<th>रात्रीचा प्रसाद भगिनी</th>
					<th>सेवा प्रकार</th>
					<th>उत्सव प्रकार</th>
					<th>अनुग्रह प्रकार</th>
					<th>बैठक प्रकार</th>
					<th>सेवा बंधू</th>
					<th>सेवा भगिनी</th>
					<th>उत्सव बंधू</th>
					<th>उत्सव भगिनी</th>
					<th>अनुग्रह बंधू</th>
					<th>अनुग्रह भगिनी</th>
					<th>बैठक बंधू</th>
					<th>बैठक भगिनी</th>
          <th>बदली करा</th>
                  </tr>
                </thead>
                <tbody>

                <?php
                    // Display data rows
                    while ($row_all_record = mysqli_fetch_assoc($result_all_record)) {
                    ?>
                      <tr>
                        <td><?php echo $row_all_record['branch']; ?></td>
                        <td><?php echo $row_all_record['brothers']; ?></td>
                        <td><?php echo $row_all_record['sisters']; ?></td>
                        <td><?php echo $row_all_record['total_people']; ?></td>
                        <td><?php echo $row_all_record['start_date']; ?></td>
                        <td><?php echo $row_all_record['end_date']; ?></td>
                        <td><?php echo $row_all_record['duration']; ?></td>
                        <td><?php echo $row_all_record['arrival_time']; ?></td>
                        <td><?php echo $row_all_record['arrival_hour']; ?></td>
                        <td><?php echo $row_all_record['breakfast_brothers']; ?></td>
                        <td><?php echo $row_all_record['breakfast_sisters']; ?></td>
                        <td><?php echo $row_all_record['lunch_brothers']; ?></td>
                        <td><?php echo $row_all_record['lunch_sisters']; ?></td>
                        <td><?php echo $row_all_record['dinner_brothers']; ?></td>
                        <td><?php echo $row_all_record['dinner_sisters']; ?></td>
                        <td><?php echo $row_all_record['seva_type']; ?></td>
                        <td><?php echo $row_all_record['utsav_type']; ?></td>
                        <td><?php echo $row_all_record['anugraha_type']; ?></td>
                        <td><?php echo $row_all_record['meeting_type']; ?></td>
                        <td><?php echo $row_all_record['seva_brothers']; ?></td>
                        <td><?php echo $row_all_record['seva_sisters']; ?></td>
                        <td><?php echo $row_all_record['utsav_brothers']; ?></td>
                        <td><?php echo $row_all_record['utsav_sisters']; ?></td>
                        <td><?php echo $row_all_record['anugraha_brothers']; ?></td>
                        <td><?php echo $row_all_record['anugraha_sisters']; ?></td>
                        <td><?php echo $row_all_record['meeting_brothers']; ?></td>
                        <td><?php echo $row_all_record['meeting_sisters']; ?></td>
                        <!-- <td><a href="editsankhya.php?id=<?php echo htmlspecialchars($row_all_record['id']); ?>" class="btn btn-primary">बदली करा</a></td> -->
                        <td>
                          <?php 
                          if (str_contains(htmlspecialchars($row_all_record['utsav_type']), "Punyatithi")) {
                          ?>
                            <a href="editpunyatithi.php?id=<?php echo htmlspecialchars($row_all_record['id']); ?>" class="btn btn-primary">बदली करा</a>
                          <?php 
                          } else {
                          ?>
                            <a href="editsankhya.php?id=<?php echo htmlspecialchars($row_all_record['id']); ?>" class="btn btn-primary">बदली करा</a>
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
