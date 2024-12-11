<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include Composer autoload for Google Authenticator

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

include('db.php');
date_default_timezone_set('Asia/Kolkata');
$date_time = date('d-m-Y H:i');
session_start();

$usern = "GODJN";
$password = "GODJN";

$usern = $_SESSION['usern1'];
$password = $_SESSION['password'];


    $query_login = "SELECT * FROM users WHERE username='$usern' AND password='$password'";
    $results = mysqli_query($db, $query_login);
    $row = mysqli_fetch_array($results);

    if (mysqli_num_rows($results) == 1) {
        $googleAuthenticator = new GoogleAuthenticator();
        $user_secret = $googleAuthenticator->generateSecret();
        // Save the secret to the database
        $update_query = "UPDATE users SET google_auth_secret='$user_secret', auth_generated=1 WHERE username='$usern'";
        mysqli_query($db, $update_query);

        // Generate QR code
        $qrCodeUrl = GoogleQrUrl::generate($usern, $user_secret, 'Harimandir Login User Name');
        echo "<div class='card mb-3'>
                <div class='card-body'>
                <center><h3>Scan this QR code with your Google Authenticator app:</h3>";
        echo "<img src='" . $qrCodeUrl . "' alt='QR Code'>";
        echo "<p>Secret Key: $user_secret</p>";
        echo "<p>Use this key if you cannot scan the QR code.</p>
              <br> Once done, <a href='otp_login.php'>Click Here</a>
              </center></div></div>";
    } else {
        echo '<script>alert("Invalid username or password.");</script>';
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">


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
    <style>
        html {
            margin: auto;
            width: 80%;
            border: 5px solid #b8860b; /* Golden Rod Border */
            padding: 10px;
            border-radius: 15px;
        }
    </style>
</head>

<body>



  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>|| OM NAMAHA : SHIVAY ||</span></strong>
    </div>

  </footer><!-- End Footer -->


  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>