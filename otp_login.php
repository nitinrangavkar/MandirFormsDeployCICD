<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include Composer autoload for Google Authenticator

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

include('db.php');
date_default_timezone_set('Asia/Kolkata');
$date_time = date('d-m-Y H:i');
session_start();

if (isset($_POST['login_user'])) {
    //$usern = mysqli_real_escape_string($db, $_POST['usern']);
    //$password = mysqli_real_escape_string($db, $_POST['password']);


    $usern = "GODJN";
    $password = "GODJN";

    $usern = $_SESSION['usern1'];
    $password = $_SESSION['password'];

    $otp_code = mysqli_real_escape_string($db, $_POST['otp_code']);


    $query_login = "SELECT * FROM users WHERE username='$usern' AND password='$password'";
    $results = mysqli_query($db, $query_login);
    $row = mysqli_fetch_array($results);

    if (mysqli_num_rows($results) == 1) {
        $googleAuthenticator = new GoogleAuthenticator();
        $user_secret = $row['google_auth_secret'];

        if (!$user_secret) {
            echo '<script>alert("2FA is not set up for this account. Please set it up first.");</script>';
            exit;
        }

        // Validate OTP
        if ($googleAuthenticator->checkCode($user_secret, $otp_code)) {
            $_SESSION['usern'] = $row['username'];
            $_SESSION['success'] = "Logged in successfully!";
            header("Location: admin/dashboard.php");
        } else {

            echo '<script>alert("Invalid OTP. Please try again.");</script>';
        }
    } else {
        echo '<script>alert("Wrong username or password combination.");</script>';
    }
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

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="#" class="logo d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block">|| ॐ नमः शिवाय ||</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your Username & password to login</p>
                    <?php if (isset($error_msg)) { ?>
                        <p style="color: red;"><?php echo $error_msg; ?></p>
                    <?php } ?>
                  </div>

                  <!-- <form name="login.php" method="post" class="row g-3 needs-validation"> -->
                  <form method="post">

                    <div class="col-12">
                      <label for="opt_code" class="form-label">OTP Code</label>
                      <input type="text" name="otp_code" class="form-control" id="otp_code" required>
                      <div class="invalid-feedback">Please enter your otp code!</div>
                    </div>

                    <br>

                    <div class="col-12">
                      <button class="btn btn-secondary w-100" name="login_user" type="submit">Verify OTP</button>
                    </div>

                  </form>
                </div>


              <div class="credits">
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->


  <footer>
    <center>
      <font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000000"> © 1981-<script type="text/javascript">
      </script>2024,परमार्थ निकेतन ट्रस्ट, बेळगांव. सर्व हक्क राखीव.</font>
    </center>
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