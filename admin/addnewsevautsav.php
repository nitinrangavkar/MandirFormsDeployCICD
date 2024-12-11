<?php
include('../db.php');
$db->set_charset("utf8mb4");


// Session and form handling (unchanged)
if (!isset($_SESSION['usern'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: ../login.php');
    exit;
  }
  
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['usern']);
    header("location: ../login.php");
    exit;
  }
  
  
  $ip = $_SERVER['REMOTE_ADDR'];
  
  $date_time = date('d-m-Y H:i');
  $usern = $_SESSION['usern'];
  $page_visit = "CreateOccasion";
  
  $query_log = "INSERT INTO login_logs(ip_address, username, page, date_time)VALUE('$ip', '$usern', '$page_visit', '$date_time')";
  mysqli_query($db,$query_log);

$name_dr_o = $_SESSION['usern'];

$date_time = date('Y-m-d H:i:s');
$page_visit = "index";
$user_email_session = $_SESSION['usern'];


// initializing variables
$Category = "";
$utsavache_naav = "";
$Category_Name = "";
$start_date = "";
$end_date = "";
$batch = "";
$Partial_group_yes_no = "";
$Edited_By = ""; // New field
$Edited_Date = "";

if (isset($_POST['update_user_info'])) {
    try {
        $id = mysqli_real_escape_string($db, $_POST['id']);
        $query_user_info = "SELECT * FROM occasions WHERE id='$id' ORDER BY start_date ASC";
        $result_user_info = mysqli_query($db, $query_user_info);
        if ($result_user_info && mysqli_num_rows($result_user_info) > 0) {
            $row_user_info = mysqli_fetch_assoc($result_user_info);
        } else {
            throw new Exception("Error: No occasion found");
            $row_user_info = []; // Handle cases where no data is found
        }
    } catch (Exception $e) {
        echo $e->getMessage(), "\n";
    }
} else {
    $row_user_info = []; // Default empty array to avoid errors if the form is not pre-filled
}


if (isset($_POST['deactivate'])) {
    try {
        $id = mysqli_real_escape_string($db, $_POST['id']);
        $query = "DELETE FROM occasions WHERE id='$id'";
        $deleteOccasionQuotaQuery = "DELETE FROM occasion_quota WHERE occasion_id='$id'";
        if(mysqli_query($db, $deleteOccasionQuotaQuery) && mysqli_query($db, $query)) {
            echo '<script>alert("Record deleted sucessfully");</script>';
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
    } catch (Exception $e) {
        echo $e->getMessage(), "\n";
    }

}

function removeYearFromOccasionName ($occasion) {
    $position = $occasion ? strpos($occasion, " -") : 0;
    return $position ? substr($occasion, 0, $position) : $occasion;
}

if (isset($_POST['reg_user'])) {

    try {
        // receive all input values from the form
        $id = mysqli_real_escape_string($db, $_POST['idToUpdate']);
        $occasion = mysqli_real_escape_string($db, $_POST['occasion']);

        $start_date = mysqli_real_escape_string($db, $_POST['start_date']);
        $end_date = mysqli_real_escape_string($db, $_POST['end_date']);
        $batch = mysqli_real_escape_string($db, $_POST['batch']);
        $loggedin_user = $name_dr_o; // New field
        $edited_date =  $date_time;
        
        $occasion_code = "";

        $occasionWithoutYear = removeYearFromOccasionName($occasion);

        $query_code = "SELECT occasion_code FROM occasion_master WHERE occasion_name = '$occasionWithoutYear' AND is_active = 1";
        $result_code = mysqli_query($db, $query_code);
        
        if ($result_code && mysqli_num_rows($result_code) > 0) {
            $row_code = mysqli_fetch_assoc($result_code);
            $occasion_code = $row_code['occasion_code']; // Get the occasion_code
        } else {
            // Handle the case where the occasion_code is not found
            throw new Exception("Error: " . "Occasion code not found");
            $occasion_code = "null"; // Or set to a default value or throw an error
        }

        $dateTime = new DateTime($start_date);
        $year = $dateTime->format("Y");
        $monthName = $dateTime->format('M');
        $occasion_name = "";
        $formattedBatch = "";

        // if batch is 1 then formattedBatch would be 01 
        if ($batch) {
            $formattedBatch = str_pad($batch, 2, '0', STR_PAD_LEFT);
        } else {
            $formattedBatch = "00";
        }

        if ($occasion_code == "BS" || $occasion_code == "LS" || $occasion_code == "MTG") {
            $occasion_name = "{$occasion} - {$monthName} - {$year}";
            $occasion_key = "{$occasion_code}"."{$monthName}"."$year"."$formattedBatch";
        } else {
            $occasion_name = "{$occasion} - {$year}";
            $occasion_key = "{$occasion_code}"."$year"."$formattedBatch";
        }
    
        $user_check_query = "SELECT * FROM `occasions` WHERE `id` = '$id'";
        $result = mysqli_query($db, $user_check_query);
        $row = mysqli_fetch_assoc($result);
        
        if (!empty($row['id'])) { // If the occasion exists
            $query = "UPDATE occasions 
                    SET occasion = '$occasion_name',
                        occasion_key = '$occasion_key',
                        start_date = '$start_date', 
                        end_date = '$end_date', 
                        batch = '$batch', 
                        edited_by = '$loggedin_user', 
                        edited_date = '$edited_date' ,
                        occasion_code = '$occasion_code'
                    WHERE id = '$id'";
            
            if (mysqli_query($db, $query)) {
                echo '<script>alert("Record updated sucessfully");</script>';
            } else {
                throw new Exception("Error: " . mysqli_error($db));
            }
        } else { // If the occasion does not exist
            $query = "INSERT INTO occasions 
                    (occasion, occasion_key, start_date, end_date, batch, edited_by, edited_date, occasion_code, created_by, created_date) 
                    VALUES ('$occasion_name', '$occasion_key', '$start_date', '$end_date', '$batch', '$loggedin_user', '$edited_date', '$occasion_code', '$loggedin_user', '$date_time')";

            if (mysqli_query($db, $query)) {
                echo '<script>alert("Record added sucessfully");</script>';
            } else {
                throw new Exception("Error: " . mysqli_error($db));
            }
        }
    } catch (Exception $e) {
        echo $e->getMessage(), "\n";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Seva / Utsav / मीटिंग / शिकवणी</title>
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

  <!--<div class="pagetitle">
        <h1>Register New Data</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
         </ol>
        </nav>
  </div>-->

<!-- Form for user registration -->
<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <!--<h5 class="card-title">Register New Data</h5>-->
                        <form class="row g-3" method="post" action="addnewsevautsav.php" accept-charset="UTF-8">
                            <div class="col-md-4">
                                <label for="occasion">Occasion</label>
                                <select name="occasion" class="form-control" id="ddlUtsav" required onchange="handleOccasionChange(this)">
                                    <option value="">Please Select</option>
                                    <?php
                                    $query = "SELECT DISTINCT id, occasion_name FROM occasion_master WHERE is_active = 1";
                                    $result_utsav = mysqli_query($db, $query);
                                    if ($result_utsav) {
                                        while ($row = $result_utsav->fetch_assoc()) {
                                            $occasionWithoutYear = removeYearFromOccasionName($row_user_info['occasion']);
                                            $selected = (isset($row_user_info['occasion']) && trim($occasionWithoutYear) === trim($row['occasion_name'])) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['occasion_name']) . '" ' . $selected . '>' . htmlspecialchars($row['occasion_name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" placeholder="dd-mm-yyyy"
                                    value="<?php echo isset($row_user_info['start_date']) ? date('Y-m-d', strtotime($row_user_info['start_date'])) : ''; ?>"
                                    required min="<?php echo date('Y-m-d'); ?>" onchange="setMinEndDate()">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" placeholder="End Date"
                                    value="<?php echo isset($row_user_info['end_date']) ? date('Y-m-d', strtotime($row_user_info['end_date'])) : ''; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="batch">Batch</label>
                                <input type="text" name="batch" id="batch" class="form-control" placeholder="Batch"
                                    value="<?php echo $row_user_info['batch'] ?? ''; ?>">
                            </div>
                            <input type="hidden" name="idToUpdate" value="<?php echo $row_user_info['id'] ?? ''; ?>">
                            <input type="hidden" name="upasanecheNaw" id="hidUpasanecheNaw" value="<?php echo $row_user_info['occasion'] ?? ''; ?>">
                            <button type="submit" name="reg_user" class="btn btn-secondary">Add/Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- anugraha meeting not required -->
<script>

function SelectUtsavAnugraha() {
    ddlUtsavAnugraha = document.getElementById('ddlUtsav');
    if(document.getElementById('hidUpasanecheNaw').value != "") {
        setSelectedValue(ddlUtsavAnugraha, document.getElementById('hidUpasanecheNaw').value);
    }
}
function setSelectedValue(selectObj, valueToSet) {
    for (var i = 0; i < selectObj.options.length; i++) {
        if (selectObj.options[i].text== valueToSet) {
            selectObj.options[i].selected = true;
            return;
        }
    }
}

// Ensure the function runs on page load in case the form is pre-populated
window.onload = function() {
    toggleCategoryNameRequired();
    SelectUtsavAnugraha();
};
</script> 
<!-- close anugraha meeting not required -->
<script>
    function setMinEndDate() {
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date');
        endDate.min = startDate;
    }
    function handleOccasionChange(selectElement) {
        const batchField = document.getElementById("batch");
        if (selectElement.value === "Punyatithi") {
            batchField.disabled = true; // Disable the batch field
            batchField.value = "";     // Optionally clear the field
        } else {
            batchField.disabled = false; // Enable the batch field
        }
    }
</script>

    <div class="row">
        <div class="col-lg-12">
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Occasion</th>
                    <th scope="col">Start Date</th>
                    <th scope="col">End Date</th>
                    <th scope="col">Batch</th>
                    <th scope="col">Update</th>
                    <!-- <th scope="col">Delete</th> -->
                  </tr>
                </thead>
                <tbody>

                <?php 
                    $query_all_records = "SELECT * FROM occasions ORDER BY start_date ASC";
                    $result_all_records = mysqli_query($db,$query_all_records);
                    $i = 0;
                    while($row_all_records = mysqli_fetch_array($result_all_records))
                    {
                      $i = $i + 1;
                ?>
                  <tr>
                    <th scope="row"><?php echo $i;?></th>
                    <td><?php echo $row_all_records['occasion'];?></td>
                    <td><?php $convertedStartDate = date("d-M-Y", strtotime($row_all_records['start_date']));
						echo $convertedStartDate;?>
					</td>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </td>
                    <td><?php $convertedEndDate = date("d-M-Y", strtotime($row_all_records['end_date']));
						echo $convertedEndDate;?>
					</td>
                    <td><?php echo $row_all_records['batch'];?></td>

                    <td>
                        <form name="#" method="post">
                            <input type="hidden" name="id" value="<?php echo $row_all_records['id'];?>">
                            <button type="submit" name="update_user_info" class="btn btn-primary btn-sm">Update</button><br>
                        </form>
                    </td>
                    <!-- <td>
                        <form name="#" method="post">
                          <input type="hidden" name="id" value="<?php echo $row_all_records['id'];?>">
                          <input type="hidden" name="categoryName" value="<?php echo $row_all_records['Category_Name'];?>">
                          <button type="submit" name="deactivate" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button><br>
                        </form>
                    </td> -->

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
