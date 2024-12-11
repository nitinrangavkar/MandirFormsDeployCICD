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
$Category = "";
$utsavache_naav = "";
$Category_Name = "";
$Start_Date = "";
$End_Date = "";
$Gat_Kramank = "";
$Partial_group_yes_no = "";
$Edited_By = ""; // New field
$Edited_Date = "";



if(isset($_POST['update_user_info'])) {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $query_user_info = "SELECT * FROM sevautsav WHERE id='$id' ORDER BY Start_Date ASC";
    $result_user_info = mysqli_query($db, $query_user_info);
    $row_user_info = mysqli_fetch_array($result_user_info);
    echo '<script type="text/javascript">',
     'toggleCategoryNameRequired();',
     '</script>';
}

if (isset($_POST['deactivate'])) {
    $id = mysqli_real_escape_string($db, $_POST['id']);
    $phone_no = mysqli_real_escape_string($db, $_POST['phone_no']);
    $query = "DELETE FROM sevautsav WHERE id='$id'";
    if(mysqli_query($db, $query)) {
        echo '<script>alert("Record Deleted successfully");</script>';
    }
}

if (isset($_POST['reg_user'])) {
    // receive all input values from the form
	$id = mysqli_real_escape_string($db, $_POST['idToUpdate']);
    $Category = mysqli_real_escape_string($db, $_POST['Category']);
    $utsavache_naav = mysqli_real_escape_string($db, $_POST['utsavache_naav']);
    $Category_Name = mysqli_real_escape_string($db, $_POST['Category_Name']);
    $Start_Date = mysqli_real_escape_string($db, $_POST['Start_Date']);
    $End_Date = mysqli_real_escape_string($db, $_POST['End_Date']);
    $Gat_Kramank = mysqli_real_escape_string($db, $_POST['Gat_Kramank']);
    $Partial_group_yes_no = mysqli_real_escape_string($db, $_POST['Partial_group_yes_no']);
    $Edited_By = $name_dr_o; // New field
    $Edited_Date =  $date_time;

  // first check the database to make sure
  // $user_check_query = "SELECT * FROM sevautsav WHERE Category='$Category' AND Category_Name='$Category_Name' AND Gat_Kramank='$Gat_Kramank' AND Start_Date='$Start_Date' AND End_Date='$End_Date' AND Gat_Kramank='$Gat_Kramank' AND Partial_group_yes_no='$Partial_group_yes_no' LIMIT 1";
  $user_check_query = "SELECT * FROM `sevautsav` WHERE `id` = '$id'";
  $result = mysqli_query($db, $user_check_query);
  $row = @mysqli_fetch_assoc($result);
  
  if(!empty($row['id']))
  { // if user exists
    //if ($row['id'] == $id AND $row['Category'] == $Category AND $row['Gat_Kramank'] == $Gat_Kramank AND $row['Start_Date'] == $Start_Date AND $row['End_Date'] == $End_Date AND $row['Gat_Kramank'] == $Gat_Kramank AND $row['Partial_group_yes_no'] == $Partial_group_yes_no) 
	///== If Data id, CategoryName, StartDate, EndDate is not changed then update the same record else insert new == //
    if ($row['id'] == $id AND $row['Category_Name'] == $Category_Name AND $row['Start_Date'] == $Start_Date AND $row['End_Date'] == $End_Date)
	{
        $query = "UPDATE sevautsav SET Category='$Category', utsavache_naav='$utsavache_naav',Category_Name='$Category_Name', Start_Date='$Start_Date', End_Date='$End_Date', Gat_Kramank='$Gat_Kramank', Partial_group_yes_no='$Partial_group_yes_no', Edited_By='$Edited_By', Edited_Date='$Edited_Date' WHERE id='$id'";
        if(mysqli_query($db, $query))
	    {
		    echo '<script>alert("माहिती यशस्वीरीत्या अद्ययावत झाली");</script>';
	    }
    }
	else
	{
		$query = "INSERT INTO `sevautsav`(`Category`, `utsavache_naav`, `Category_Name`, `Start_Date`, `End_Date`, `Gat_Kramank`, `Partial_group_yes_no`,`Edited_By`,`Edited_Date`) VALUES ('$Category', '$utsavache_naav', '$Category_Name', '$Start_Date', '$End_Date', '$Gat_Kramank', '$Partial_group_yes_no','$Edited_By','$Edited_Date')";
        if(mysqli_query($db, $query))
        {
            echo '<script>alert("माहिती यशस्वीरीत्या जमा झाली");</script>';
        }
	}
  }
  else
  {
  	$query = "INSERT INTO `sevautsav`(`Category`, `utsavache_naav`, `Category_Name`, `Start_Date`, `End_Date`, `Gat_Kramank`, `Partial_group_yes_no`,`Edited_By`,`Edited_Date`) VALUES ('$Category', '$utsavache_naav', '$Category_Name', '$Start_Date', '$End_Date', '$Gat_Kramank', '$Partial_group_yes_no','$Edited_By','$Edited_Date')";
    if(mysqli_query($db, $query))
    {
        echo '<script>alert("माहिती यशस्वीरीत्या जमा झाली");</script>';
    }
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
                                <label for="Category">प्रकार</label>
                                <select name="Category" id="Category" class="form-control" onchange="toggleCategoryNameRequired()">
                                    <option value="0">कृपया निवडा</option>
                                    <option value="सेवा" <?php if($row_user_info['Category']=="सेवा") echo 'selected="selected"'; ?>>सेवा</option>
                                    <option value="उत्सव" <?php if($row_user_info['Category']=="उत्सव") echo 'selected="selected"'; ?>>उत्सव</option>
                                    <option value="अनुग्रह" <?php if($row_user_info['Category']=="अनुग्रह") echo 'selected="selected"'; ?>>अनुग्रह</option>
                                    <option value="मीटिंग" <?php if($row_user_info['Category']=="मीटिंग") echo 'selected="selected"'; ?>>मीटिंग</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="Category_Name">उत्सव / अनुग्रह</label>
                                <select name="utsavache_naav" class="form-control" id="ddlUtsav">
                                <option value="0">कृपया निवडा</option>
                                  <?php 
                                      $query_utsav = "SELECT DISTINCT Category_Name FROM sevautsav WHERE Category='उत्सव'";
                                      $result_utsav = mysqli_query($db, $query_utsav);
                                      while ($row_utsav = mysqli_fetch_assoc($result_utsav)) {
                                          $id = $row_utsav['id'];
                                      ?>
                                      <?php
                                        $selected = "";
                                        if ($row_utsav['Category_Name'] == $row_user_info['utsavache_naav']) {
                                            $selected = " selected='selected'";
                                        }
                                        echo '<option value="' . htmlspecialchars($row_utsav['Category_Name']) . '"' . $selected . '>' 
                                        . htmlspecialchars($row_utsav['Category_Name']) 
                                        . '</option>';
                                      }
                                  ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="Category_Name">सेवा/उत्सव/अनुग्रह/मीटिंगचे नाव</label>
                                <input type="text" name="Category_Name" class="form-control" placeholder="नाव" id="Category_Name" value="<?php echo htmlspecialchars($row_user_info['Category_Name'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="Start_Date">दिनांक पासून</label>
                                <input type="date" name="Start_Date" id="Start_Date" class="form-control" placeholder="Start Date" value="<?php echo $row_user_info['Start_Date']; ?>" required min="<?php echo date("Y-m-d"); ?>" onchange="setMinEndDate()">
                            </div>
                            <div class="col-md-3">
                                <label for="End_Date">दिनांक पर्यंत</label>
                                <input type="date" name="End_Date" id="End_Date" class="form-control" placeholder="End Date" value="<?php echo $row_user_info['End_Date']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="Gat_Kramank">गट क्रमांक</label>
                                <input type="text" name="Gat_Kramank" id="Gat_Kramank" class="form-control" placeholder="Gat Kramank" value="<?php echo $row_user_info['Gat_Kramank']; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="Partial_group_yes_no">Partial Batch Yes or No</label>
                                <select name="Partial_group_yes_no" id="Partial_group_yes_no" class="form-control" value="<?php echo $row_user_info['Partial_group_yes_no']; ?>">
                                    <option>No</option>
                                    <option>Yes</option>
                                </select>
                            </div>
                            <input type="hidden" name="idToUpdate" value="<?php echo $row_user_info['id'];?>">
                            <input type="hidden" name="upasanecheNaw" id="hidUpasanecheNaw" value="<?php echo $row_user_info['utsavache_naav'];?>">
                            <button type="submit" name="reg_user" class="btn btn-secondary">जमा / अद्ययावत करा</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- anugraha meeting not required -->
<script>
function toggleCategoryNameRequired() {
    var category = document.getElementById("Category").value;
    var categoryNameInput = document.getElementById("Category_Name");

    if (category === "अनुग्रह") {
        categoryNameInput.removeAttribute("required");

    } else {
        categoryNameInput.setAttribute("required", "required");
    }

    if (category == "उत्सव" || category == "मीटिंग"){
		document.getElementById("ddlUtsav").disabled=true;
		document.getElementById("ddlUtsav").options.selectedIndex = 0;
	}
	else{
		document.getElementById("ddlUtsav").disabled=false;
		document.getElementById("ddlUtsav").options.selectedIndex = 0;
	}
}
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
                    <th scope="col">प्रकार</th>
                    <th scope="col">उत्सव / अनुग्रह</th>
                    <th scope="col">सेवा/उत्सव/अनुग्रह/मीटिंगचे नाव</th>
                    <th scope="col">दिनांक पासून</th>
                    <th scope="col">दिनांक पर्यंत</th>
                    <th scope="col">गट क्रमांक</th>
                    <th scope="col">Partial Group Yes No</th>
                    <th scope="col">अद्ययावत करा</th>
                    <!--<th scope="col">काढून टाका</th>-->
                  </tr>
                </thead>
                <tbody>

                <?php 
                    $query_all_records = "SELECT * FROM sevautsav ORDER BY Edited_Date DESC";
                    $result_all_records = mysqli_query($db,$query_all_records);
                    $i = 0;
                    while($row_all_records = mysqli_fetch_array($result_all_records))
                    {
                      $i = $i + 1;
                ?>
                  <tr>
                    <th scope="row"><?php echo $i;?></th>
                    <td><?php echo $row_all_records['Category'];?></td>
                    <td><?php echo $row_all_records['utsavache_naav'];?></td>
                    <td><?php echo $row_all_records['Category_Name'];?></td>
                    <td><?php $convertedStartDate = date("d-M-Y", strtotime($row_all_records['Start_Date']));
						echo $convertedStartDate;?>
					</td>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </td>
                    <td><?php $convertedEndDate = date("d-M-Y", strtotime($row_all_records['End_Date']));
						echo $convertedEndDate;?>
					</td>
                    <td><?php echo $row_all_records['Gat_Kramank'];?></td>
                    <td><?php echo $row_all_records['Partial_group_yes_no'];?></td>

                    <td>
                        <form name="#" method="post">
                            <input type="hidden" name="id" value="<?php echo $row_all_records['id'];?>">
                            <button type="submit" name="update_user_info" class="btn btn-primary btn-sm">अद्ययावत करा</button><br>
                        </form>
                    </td>
                    <!--<td>
                        <form name="#" method="post">
                          <input type="hidden" name="id" value="<?php echo $row_all_records['id'];?>">
                          <button type="submit" name="deactivate" class="btn btn-danger btn-sm">काढून टाका</button><br>
                        </form>
                    </td>-->

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
