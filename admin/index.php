<?php
include('../db.php');

$name_dr_o = $_SESSION['usern'];

if (!isset($_SESSION['usern'])) {
  $_SESSION['msg'] = "तुम्हाला प्रथम लॉग इन करणे आवश्यक आहे";
  header('location: https://www.godjn.com');
}
if (isset($_GET['logout'])) {
  session_destroy();
  unset($_SESSION['usern']);
  header("location: https://www.godjn.com");
}

// Fetching filtered data from the database
$branch_filter = $_POST['branch_filter'] ?? '';
$seva_type_filter = $_POST['seva_type_filter'] ?? '';
$utsav_type_filter = $_POST['utsav_type_filter'] ?? '';

$query = "SELECT `id`, `branch`, `brothers`, `sisters`, `total_people`, `start_date`, `end_date`, `duration`, `arrival_time`, `arrival_hour`, `breakfast_brothers`, `breakfast_sisters`, `lunch_brothers`, `lunch_sisters`, `dinner_brothers`, `dinner_sisters`, `seva_type`, `utsav_type`, `anugraha_type`, `meeting_type`, `seva_brothers`, `seva_sisters`, `utsav_brothers`, `utsav_sisters`, `anugraha_brothers`, `anugraha_sisters`, `meeting_brothers`, `meeting_sisters` FROM `form_submissions` WHERE 1";

if ($branch_filter) {
    $query .= " AND `branch` = '$branch_filter'";
}
if ($seva_type_filter) {
    $query .= " AND `seva_type` = '$seva_type_filter'";
}
if ($utsav_type_filter) {
    $query .= " AND `utsav_type` = '$utsav_type_filter'";
}

$result = mysqli_query($db, $query);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="mr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>सक्रिय प्रकरणे</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com/css2?family=Lohit+Marathi&display=swap" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Lohit Marathi', sans-serif;
    }
    .inline-form {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      align-items: center;
    }
    .inline-form input, .inline-form select {
      margin: 5px 0;
    }
    .chart-container {
      width: 100%;
      height: 400px;
    }
  </style>


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
    </style>


</head>

<body class="sidebar-toggled">

  <!-- Header -->
  <?php include('header.php'); ?>
  <!-- End Header -->

  <!-- Sidebar -->
  <?php include('sidebar.php'); ?>
  <!-- End Sidebar -->

  <main id="main" class="main">
    <section class="section dashboard">
      <section class="section">
        <div class="row">
          <div class="col-lg-12">
          </div>
        </div>
      </section>
    </section>
  </main>

  <!-- Footer -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span> Harimandir</span></strong>. सर्व हक्क राखीव.
    </div>
  </footer>
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.min.js"></script>
  <script src="../assets/js/main.js"></script>

 </body> </html>