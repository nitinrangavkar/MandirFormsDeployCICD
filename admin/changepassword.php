<?php
include('../db.php');
$db->set_charset("utf8mb4");

$password = "GeeksforGeeks@123";





if (isset($_POST['change_password'])) 
{
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
    $username = $_POST['username'];
    $newPassword = $_POST['newPassword'];

    $query_checkusername = "SELECT * FROM users WHERE username='$username'";
    $results = mysqli_query($db, $query_checkusername);
    $row= mysqli_fetch_array($results);

    if(mysqli_num_rows($results) == 1) 
    {
        if (preg_match($pattern, $newPassword)) {
            $newPassword = md5($newPassword);
            $query_updatepassword = "UPDATE users SET password='$newPassword' WHERE username='$username'";
            if(mysqli_query($db, $query_updatepassword)) {
                session_write_close();
                $_SESSION['usern'] = "";
                echo '<script>alert("Password changed successfully");</script>';
                header('Refresh: 3; URL=../login.php');
            } else {
                echo '<script>alert("Error in password change, please contact Admin");</script>';
            }
        } else {
            echo '<script>alert("Password must be 8 character long, including atleast 1 Integer, 1 Special Character, 1 Capital letter character");</script>';
        }
    }
    else
    {
        echo '<script>alert("Please enter valid Username");</script>';
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Change Password</title>
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

            <div class="card mb-3">

                <div class="card-body">

                <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Change your Password</h5>
                    <?php if (isset($error_msg)) { ?>
                        <p style="color: red;"><?php echo $error_msg; ?></p>
                    <?php } ?>
                </div>

                <form name="changepassword.php" method="post" class="row g-3 needs-validation">

                    <div class="col-12">
                    <label for="userid" class="form-label">Username</label>
                    <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Please enter your Username</div>
                    </div>
                    </div>

                    <div class="col-12">
                    <label for="yourPassword" class="form-label">New Password</label>
                    <input type="password" name="newPassword" class="form-control" id="yourPasswordNew" required>
                    <div class="invalid-feedback">Please enter New password!</div>
                    </div>

                <!--  
                    <div class="col-12">
                        <label for="captcha" class="form-label">Enter the CAPTCHA code:</label>
                        <img src="captcha.php" alt="CAPTCHA"><br>
                        <input type="text" id="captcha" class="form-control" name="captcha" required><br>
                    </div> -->

                    <div class="col-12">
                    <button class="btn btn-secondary w-100" name="change_password" type="submit">Change Password</button>
                    </div>

                </form>

                </div>
        </div>
    </div>
</main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer"></footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  

  <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>