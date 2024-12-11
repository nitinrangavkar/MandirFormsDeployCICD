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

$date_time = date("d-m-Y") . " | " . date("h:i:sa");
$page_visit = "index";
$user_email_session = $_SESSION['usern'];


// initializing variables
$unique_code = "";
$shakha = "";
$upasana_kendra = "";
$upasana_mahiti = "";
$pohochane_sadhan = "";
$vastu = "";
$location = "";
$seva_pramukh = "";
$patra_patta = "";
$upasana_karyakram = "";
$branch = "";
$dispatch_group = "";
$upasana_address = "";
$upasana_details = "";
$how_to_reach = "";
$latitude = "";
$longitude = "";
$is_validated = "";
$updated_at = "";
$effective_from = "";
$effective_to = "";
$is_active = "";



if(isset($_POST['update_user_info'])) {
    $unique_code = mysqli_real_escape_string($db, $_POST['unique_code']);
    $query_user_info = "SELECT * FROM mandir_branch WHERE unique_code='$unique_code'";
    $result_user_info = mysqli_query($db, $query_user_info);
    $row_user_info = mysqli_fetch_array($result_user_info);
}

if (isset($_POST['deactivate'])) {
    $unique_code = mysqli_real_escape_string($db, $_POST['unique_code']);
    $query = "DELETE FROM mandir_branch WHERE unique_code='$unique_code'";
    if(mysqli_query($db, $query)) {
        echo '<script>alert("Record Deleted successfully");</script>';
    }
}

if (isset($_POST['reg_user'])) {
    // receive all input values from the form
    $unique_code = $_POST['unique_code'];
    $shakha = $_POST['shakha'];
    $upasana_kendra = $_POST['upasana_kendra'];
    $upasana_mahiti = $_POST['upasana_mahiti'];
    $pohochane_sadhan = $_POST['pohochane_sadhan'];
    $vastu = $_POST['vastu'];
    $location = $_POST['location'];
    $seva_pramukh = $_POST['seva_pramukh'];
    $patra_patta = $_POST['patra_patta'];
    $upasana_karyakram = $_POST['upasana_karyakram'];
    $branch = $_POST['branch'];
    $dispatch_group = $_POST['dispatch_group'];
    $upasana_address = $_POST['upasana_address'];
    $upasana_details = $_POST['upasana_details'];
    $how_to_reach = $_POST['how_to_reach'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $is_validated = $_POST['is_validated'];
    $updated_at = $_POST['updated_at'];
    $effective_from = $_POST['effective_from'];
    $effective_to = $_POST['effective_to'];
    $is_active = $_POST['is_active'];

  // first check the database to make sure
  $user_check_query = "SELECT * FROM mandir_branch WHERE unique_code='$unique_code' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $row = @mysqli_fetch_assoc($result);
  
  
  if ($row) 
  { // if user exists
    if ($row['unique_code'] == $unique_code) 
    {
        $query = "UPDATE mandir_branch SET shakha='$shakha', upasana_kendra='$upasana_kendra',upasana_mahiti='$upasana_mahiti', pohochane_sadhan='$pohochane_sadhan', vastu='$vastu', location='$location', seva_pramukh='$seva_pramukh', patra_patta='$patra_patta', upasana_karyakram='$upasana_karyakram', branch='$branch', dispatch_group='$dispatch_group', upasana_address='$upasana_address', upasana_details='$upasana_details', how_to_reach='$how_to_reach', latitude='$latitude', longitude='$longitude', is_validated='$is_validated', updated_at='$updated_at', effective_from='$effective_from', effective_to='$effective_to', is_active='$is_active' WHERE unique_code='$unique_code'";
        if(mysqli_query($db, $query))
	    {
		    echo '<script>alert("Data Updated Successfully with an existing email");</script>';
	    }
    }
  }
  else
  {

      $query = "INSERT INTO mandir_branch (unique_code, shakha, upasana_kendra, upasana_mahiti, pohochane_sadhan, vastu, location, seva_pramukh, patra_patta, upasana_karyakram, branch, dispatch_group, upasana_address, upasana_details, how_to_reach, latitude, longitude, is_validated, updated_at, effective_from, effective_to, is_active) 
      VALUES ('$unique_code', '$shakha', '$upasana_kendra', '$upasana_mahiti', '$pohochane_sadhan', '$vastu', '$location', '$seva_pramukh', '$patra_patta', '$upasana_karyakram', '$branch', '$dispatch_group', '$upasana_address', '$upasana_details', '$how_to_reach', '$latitude', '$longitude', '$is_validated', '$updated_at', '$effective_from', '$effective_to', '$is_active')";

        if(mysqli_query($db, $query))
	    {
		    echo '<script>alert("Data Created Successfully");</script>';
	    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Shakha Management</title>
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



  
    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="dropdownassets/select2.min.js"></script>

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
            width: 100%;
            padding: 10px;
            border-radius: 15px;
        }
    </style>

</head>

<body>

  <!-- ======================== Header ======================== -->
  <?php include('header.php'); ?>
  <!------====================== End Header ==============------->

  <!--------------------- Sidebar --------------------------------->

  <!--------------------- End Sidebar ----------------------------->

  <!------------------------------------------ Main part Start --------------------------------------------------->

  <main unique_code="main" class="main">

  <div class="pagetitle">
        <h1>Register New Shakha</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         </ol>
        </nav>
  </div>

<!-- Form for user registration -->
<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Register New Data</h5>
                        <form class="row g-3" method="post" action="shakha_master.php" accept-charset="UTF-8">

                          <div class="col-md-4">
                              <label for="unique_code">Unique Code</label>
                              <input type="text" name="unique_code" unique_code="unique_code" class="form-control" placeholder="Unique Code" value="<?php echo $row_user_info['unique_code']; ?>">
                          </div>

                          <div class="col-md-4">
                              <label for="shakha">शाखा</label>
                              <input type="text" name="shakha" unique_code="shakha" class="form-control" placeholder="शाखा" value="<?php echo $row_user_info['shakha']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="upasana_kendra">उपासना केंद्र</label>
                              <input type="text" name="upasana_kendra" unique_code="upasana_kendra" class="form-control" placeholder="उपासना केंद्र" value="<?php echo $row_user_info['upasana_kendra']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="upasana_mahiti">उपासना माहिती</label>
                              <input type="text" name="upasana_mahiti" unique_code="upasana_mahiti" class="form-control" placeholder="उपासना माहिती" value="<?php echo $row_user_info['upasana_mahiti']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="pohochane_sadhan">पोहोचण्याचे साधन</label>
                              <input type="text" name="pohochane_sadhan" unique_code="pohochane_sadhan" class="form-control" placeholder="पोहोचण्याचे साधन" value="<?php echo $row_user_info['pohochane_sadhan']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="vastu">वास्तु</label>
                              <input type="text" name="vastu" unique_code="vastu" class="form-control" placeholder="वास्तु" value="<?php echo $row_user_info['vastu']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="location">Location</label>
                              <input type="text" name="location" unique_code="location" class="form-control" placeholder="Location" value="<?php echo $row_user_info['location']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="patra_patta">पत्र पत्ता</label>
                              <input type="text" name="patra_patta" unique_code="patra_patta" class="form-control" placeholder="पत्र पत्ता" value="<?php echo $row_user_info['patra_patta']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="seva_pramukh">सेवाप्रमुख</label>
                              <input type="text" name="seva_pramukh" unique_code="seva_pramukh" class="form-control" placeholder="सेवाप्रमुख" value="<?php echo $row_user_info['seva_pramukh']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="upasana_karyakram">Upasana Karyakram</label>
                              <input type="text" name="upasana_karyakram" unique_code="upasana_karyakram" class="form-control" placeholder="Upasana Karyakram" value="<?php echo $row_user_info['upasana_karyakram']; ?>">
                          </div>

                          <div class="col-md-4">
                              <label for="branch">Branch</label>
                              <input type="text" name="branch" unique_code="branch" class="form-control" placeholder="Branch" value="<?php echo $row_user_info['branch']; ?>">
                          </div>

                          <div class="col-md-4">
                              <label for="dispatch_group">Dispatch Group</label>
                              <input type="text" name="dispatch_group" unique_code="dispatch_group" class="form-control" placeholder="Dispatch Group" value="<?php echo $row_user_info['dispatch_group']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="upasana_address">Upasana Address</label>
                              <input type="text" name="upasana_address" unique_code="upasana_address" class="form-control" placeholder="Upasana Address" value="<?php echo $row_user_info['upasana_address']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="upasana_details">Upasana Details</label>
                              <input type="text" name="upasana_details" unique_code="upasana_details" class="form-control" placeholder="Upasana Details" value="<?php echo $row_user_info['upasana_details']; ?>">
                          </div>

                          <div class="col-md-12">
                              <label for="how_to_reach">How to Reach</label>
                              <input type="text" name="how_to_reach" unique_code="how_to_reach" class="form-control" placeholder="How to Reach" value="<?php echo $row_user_info['how_to_reach']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="latitude">Latitude</label>
                              <input type="text" name="latitude" unique_code="latitude" class="form-control" placeholder="Latitude" value="<?php echo $row_user_info['latitude']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="longitude">Longitude</label>
                              <input type="text" name="longitude" unique_code="longitude" class="form-control" placeholder="Longitude" value="<?php echo $row_user_info['longitude']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="is_validated">Is Validated</label>
                              <input type="text" name="is_validated" unique_code="is_validated" class="form-control" placeholder="Is Validated" value="<?php echo $row_user_info['is_validated']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="updated_at">Updated At</label>
                              <input type="text" name="updated_at" unique_code="updated_at" class="form-control" placeholder="Updated At" value="<?php echo $row_user_info['updated_at']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="effective_from">Effective From</label>
                              <input type="text" name="effective_from" unique_code="effective_from" class="form-control" placeholder="Effective From" value="<?php echo $row_user_info['effective_from']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="effective_to">Effective To</label>
                              <input type="text" name="effective_to" unique_code="effective_to" class="form-control" placeholder="Effective To" value="<?php echo $row_user_info['effective_to']; ?>">
                          </div>

                          <div class="col-md-3">
                              <label for="is_active">Is Active</label>
                              <input type="text" name="is_active" unique_code="is_active" class="form-control" placeholder="Is Active" value="<?php echo $row_user_info['is_active']; ?>">
                          </div>

                            <button type="submit" name="reg_user" class="btn btn-secondary">Add/Update Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function setMinEndDate() {
        var startDate = document.getElementById('Start_Date').value;
        var endDate = document.getElementById('End_Date');
        endDate.min = startDate;
    }
</script>
    <div class="row">
        <div class="col-lg-12">
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Unique Code</th>
                    <th scope="col">Shakha</th>
                    <th scope="col">Upasana Kendra</th>
                    <th scope="col">Edited By</th>
                    <th scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>

                <?php 
                    $query_all_records = "SELECT * FROM mandir_branch";
                    $result_all_records = mysqli_query($db,$query_all_records);
                    $i = 0;
                    while($row_all_records = mysqli_fetch_array($result_all_records))
                    {
                      $i = $i + 1;
                ?>
                  <tr>
                    <th scope="row"><?php echo $i;?></th>
                      <td><?php echo htmlspecialchars($row_all_records['unique_code']); ?></td>
                      <td><?php echo htmlspecialchars($row_all_records['shakha']); ?></td>
                      <td><?php echo htmlspecialchars($row_all_records['upasana_kendra']); ?></td>
                      <td>
                          <form name="#" method="post">
                              <input type="hidden" name="unique_code" value="<?php echo $row_all_records['unique_code'];?>">
                              <button type="submit" name="update_user_info" class="btn btn-primary btn-sm">Edit Shakha Data</button><br>
                          </form>
                      </td>
                      <td>
                          <form name="#" method="post">
                            <input type="hidden" name="unique_code" value="<?php echo $row_all_records['unique_code'];?>">
                            <button type="submit" name="deactivate" class="btn btn-primary btn-sm">Delete</button><br>
                          </form>
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
  <footer unique_code="footer" class="footer">


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
