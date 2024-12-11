<?php
include('../db.php');
$db->set_charset("utf8mb4");


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


// Function to fetch available dates based on type selection
function fetch_available_dates($type) {
    global $db;
    $query_availabledates = "SELECT start_date, end_date FROM sevautsav WHERE Category_Name = '$type'";
    $result_availabledates = mysqli_query($db, $query_availabledates);
    $dates = [];
    if ($row = mysqli_fetch_assoc($result_availabledates)) {
        $start_date = new DateTime($row['start_date']);
        $end_date = new DateTime($row['end_date']);
        $start_date->modify('-5 days');
        $end_date->modify('+5 days');
        $dates['start_date'] = $start_date->format('Y-m-d');
        $dates['end_date'] = $end_date->format('Y-m-d');
    }
    return $dates;
}

$name_dr_o = $_SESSION['usern'];
$branch = "";
$brothers = 0;
$sisters = 0;
$total_people = 0;
$start_date = "";
$end_date = "";
$duration = 0;
$arrival_time = "";
$arrival_hour = 0;

$breakfast_brothers = 0;
$breakfast_sisters = 0;
$lunch_brothers = 0;
$lunch_sisters = 0;
$dinner_brothers = 0;
$dinner_sisters = 0;

$seva_type = "";
$utsav_type = "";
$anugraha_type = "";
$meeting_type = "";

$seva_brothers = 0;
$seva_sisters = 0;
$utsav_brothers = 0;
$utsav_sisters = 0;
$anugraha_brothers = 0;
$anugraha_sisters = 0;
$meeting_brothers = 0;
$meeting_sisters = 0;


if(isset($_POST['clear_all']))
{
        // Session create if error occured and not submitted query then place values in input field //
        unset($_SESSION['branch']);
        unset($_SESSION['brothers']);
        unset($_SESSION['sisters']);
        unset($_SESSION['total_people']);
        unset($_SESSION['start_date']);
        unset($_SESSION['end_date']);
        unset($_SESSION['arrival_time']);
        unset($_SESSION['arrival_hour']);
    
        unset($_SESSION['breakfast_brothers']);
        unset($_SESSION['breakfast_sisters']);
        unset($_SESSION['lunch_brothers']);
        unset($_SESSION['lunch_sisters']);
        unset($_SESSION['dinner_brothers']);
        unset($_SESSION['dinner_sisters']);
    
        unset($_SESSION['seva_type']);
        unset($_SESSION['utsav_type']);
        unset($_SESSION['anugraha_type']);
        unset($_SESSION['meeting_type']);
    
        unset($_SESSION['seva_brothers']);
        unset($_SESSION['seva_sisters']);
        unset($_SESSION['utsav_brothers']);
        unset($_SESSION['utsav_sisters']);
        unset($_SESSION['anugraha_brothers']);
        unset($_SESSION['anugraha_sisters']);
        unset($_SESSION['meeting_brothers']);
        unset($_SESSION['meeting_sisters']);

}
// Handle form submission
if(isset($_POST['reg_user']))
{
    // Retrieve form data
    $branch = $_POST['branch'];
    $brothers = $_POST['brothers'];
    $sisters = $_POST['sisters'];
    $total_people = $_POST['total_people'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $arrival_time = $_POST['arrival_time'];
    $arrival_hour = $_POST['arrival_hour'];

    $breakfast_brothers = $_POST['breakfast_brothers'];
    $breakfast_sisters = $_POST['breakfast_sisters'];
    $lunch_brothers = $_POST['lunch_brothers'];
    $lunch_sisters = $_POST['lunch_sisters'];
    $dinner_brothers = $_POST['dinner_brothers'];
    $dinner_sisters = $_POST['dinner_sisters'];

    if(($breakfast_brothers < 0) && ($breakfast_sisters < 0) && ($lunch_brothers < 0) && ($lunch_sisters < 0) && ($dinner_brothers < 0) && ($dinner_sisters < 0))
    {
        echo '<script>alert("There should be atelast 1 bandhu or bhagini");</script>';
    }
    $seva_type = $_POST['seva_type'];
    $utsav_type = $_POST['utsav_type'];
    $anugraha_type = $_POST['anugraha_type'];
    $meeting_type = $_POST['meeting_type'];

    $seva_brothers = $_POST['seva_brothers'];
    $seva_sisters = $_POST['seva_sisters'];
    $utsav_brothers = $_POST['utsav_brothers'];
    $utsav_sisters = $_POST['utsav_sisters'];
    $anugraha_brothers = $_POST['anugraha_brothers'];
    $anugraha_sisters = $_POST['anugraha_sisters'];
    $meeting_brothers = $_POST['meeting_brothers'];
    $meeting_sisters = $_POST['meeting_sisters'];

    // Session create if error occured and not submitted query then place values in input field //
    $_SESSION['branch'] = $branch;
    $_SESSION['brothers'] = $brothers;
    $_SESSION['sisters'] = $sisters;
    $_SESSION['total_people'] = $total_people;
    $_SESSION['start_date'] = $start_date;
    $_SESSION['end_date'] = $end_date;
    $_SESSION['arrival_time'] = $arrival_time;
    $_SESSION['arrival_hour'] = $arrival_hour;

    $_SESSION['breakfast_brothers'] = $breakfast_brothers;
    $_SESSION['breakfast_sisters'] = $breakfast_sisters;
    $_SESSION['lunch_brothers'] = $lunch_brothers;
    $_SESSION['lunch_sisters'] = $lunch_sisters;
    $_SESSION['dinner_brothers'] = $dinner_brothers;
    $_SESSION['dinner_sisters'] = $dinner_sisters;

    $_SESSION['seva_type'] = $seva_type;
    $_SESSION['utsav_type'] = $utsav_type;
    $_SESSION['anugraha_type'] = $anugraha_type;
    $_SESSION['meeting_type'] = $meeting_type;

    $_SESSION['seva_brothers'] = $seva_brothers;
    $_SESSION['seva_sisters'] = $seva_sisters;
    $_SESSION['utsav_brothers'] = $utsav_brothers;
    $_SESSION['utsav_sisters'] = $utsav_sisters;
    $_SESSION['anugraha_brothers'] = $anugraha_brothers;
    $_SESSION['anugraha_sisters'] = $anugraha_sisters;
    $_SESSION['meeting_brothers'] = $meeting_brothers;
    $_SESSION['meeting_sisters'] = $meeting_sisters;


    // Set default values for empty fields
    $seva_brothers = $seva_brothers ?: 0;
    $seva_sisters = $seva_sisters ?: 0;
    $utsav_brothers = $utsav_brothers ?: 0;
    $utsav_sisters = $utsav_sisters ?: 0;
    $anugraha_brothers = $anugraha_brothers ?: 0;
    $anugraha_sisters = $anugraha_sisters ?: 0;
    $meeting_brothers = $meeting_brothers ?: 0;
    $meeting_sisters = $meeting_sisters ?: 0;

    // Calculate total seva brothers and sisters
    $query_bro1 = "SELECT id FROM form_submissions ORDER BY id DESC LIMIT 1";
    $result_bro1 = mysqli_query($db, $query_bro1);
    $last_id_bro = mysqli_fetch_assoc($result_bro1)['id'] + 1;


    $result_brothers = mysqli_query($db, "SELECT SUM(seva_brothers) as sbro FROM seva_selected WHERE form_submission_id='$last_id_bro'");
    $row_brothers= mysqli_fetch_array($result_brothers);
    $seva_brothers= $row_brothers['sbro'];

    $result_sisters = mysqli_query($db, "SELECT SUM(seva_sisters) as ssis FROM seva_selected WHERE form_submission_id='$last_id_bro'");
    $row_sisters= mysqli_fetch_array($result_sisters);
    $seva_sisters= $row_sisters['ssis']; 


    $total_seva_bandhu = $seva_brothers + $utsav_brothers + $anugraha_brothers + $meeting_brothers;

    $total_seva_bhagini = $seva_sisters + $utsav_sisters + $anugraha_sisters + $meeting_sisters;

    
    // Validate total brothers and sisters
    if ($total_seva_bandhu != $brothers) {
        echo '<script>alert("Kindly check bandhu count once");</script>';
    } elseif ($total_seva_bhagini != $sisters) {
        echo '<script>alert("Kindly check bhagini count once");</script>';
    } else {

        // Calculate duration
        $start_date_obj = new DateTime($start_date);
        $end_date_obj = new DateTime($end_date);
        $duration = $end_date_obj->diff($start_date_obj)->days + 1; // Adding 1 to include both start and end date

        // utsav bandhu count
        $result_bandhu = mysqli_query($db, 'SELECT SUM(brothers) AS value_sum_bandhu FROM form_submissions WHERE utsav_type="'.$utsav_type.'"'); 
        $row_bandhu = mysqli_fetch_assoc($result_bandhu); 
        $sum_bandhu = $row_bandhu['brothers'];

        // utsav bhagini count
        $result_bhagini = mysqli_query($db, 'SELECT SUM(sisters) AS value_sum_bhagini FROM form_submissions WHERE utsav_type="'.$utsav_type.'"'); 
        $row_bhagini = mysqli_fetch_assoc($result_bhagini); 
        $sum_bhagini = $row_bhagini['sisters'];
        

        // utsav bandhu count
        $result_quota_bandhu = mysqli_query($db, 'SELECT SUM(bandhu_count) AS value_quota_bandhu FROM quota_utsav_seva WHERE utsavorseva_name="'.$utsav_type.'"');
        $row_quota_bandhu = mysqli_fetch_assoc($result_quota_bandhu); 
        $sum_quota_bandhu = $row_quota_bandhu['bandhu_count'];


        // utsav bhagini count
        $result_quota_bhagini = mysqli_query($db, 'SELECT SUM(bhagini_count) AS value_quota_bhagini FROM quota_utsav_seva WHERE utsavorseva_name="'.$utsav_type.'"'); 
        $row_quota_bandhu = mysqli_fetch_assoc($result_quota_bhagini); 
        $sum_quota_bhagini = $row_quota_bandhu['bhagini_count'];

        $existing_bandhu = $sum_bandhu + $sum_quota_bandhu;
        $existing_baghini = $sum_bhagini + $sum_quota_bhagini;

        $remaining_bandhu = 200 - $existing_bandhu;
        $remaining_baghini = 200 - $existing_baghini;

        if($remaining_bandhu >= $total_seva_bandhu)
        {
            if($remaining_baghini >= $total_seva_bhagini)
            {
                            // Insert data into the database
                            $sql = "INSERT INTO form_submissions (branch, brothers, sisters, total_people, start_date, end_date, duration, arrival_time, arrival_hour, 
                            breakfast_brothers, breakfast_sisters, lunch_brothers, lunch_sisters, dinner_brothers, dinner_sisters, 
                            seva_type, utsav_type, anugraha_type, meeting_type, 
                            seva_brothers, seva_sisters, utsav_brothers, utsav_sisters, anugraha_brothers, anugraha_sisters, meeting_brothers, meeting_sisters) 
                            VALUES ('$branch', '$brothers', '$sisters', '$total_people', '$start_date', '$end_date', '$duration', '$arrival_time', '$arrival_hour',
                                    '$breakfast_brothers', '$breakfast_sisters', '$lunch_brothers', '$lunch_sisters', '$dinner_brothers', '$dinner_sisters',
                                    '$seva_type', '$utsav_type', '$anugraha_type', '$meeting_type', 
                                    '$seva_brothers', '$seva_sisters', '$utsav_brothers', '$utsav_sisters', '$anugraha_brothers', '$anugraha_sisters', '$meeting_brothers', '$meeting_sisters')";


                            if (mysqli_query($db, $sql)) {
                                        // Session create if error occured and not submitted query then place values in input field //
                                unset($_SESSION['branch']);
                                unset($_SESSION['brothers']);
                                unset($_SESSION['sisters']);
                                unset($_SESSION['total_people']);
                                unset($_SESSION['start_date']);
                                unset($_SESSION['end_date']);
                                unset($_SESSION['arrival_time']);
                                unset($_SESSION['arrival_hour']);
                            
                                unset($_SESSION['breakfast_brothers']);
                                unset($_SESSION['breakfast_sisters']);
                                unset($_SESSION['lunch_brothers']);
                                unset($_SESSION['lunch_sisters']);
                                unset($_SESSION['dinner_brothers']);
                                unset($_SESSION['dinner_sisters']);
                            
                                unset($_SESSION['seva_type']);
                                unset($_SESSION['utsav_type']);
                                unset($_SESSION['anugraha_type']);
                                unset($_SESSION['meeting_type']);
                            
                                unset($_SESSION['seva_brothers']);
                                unset($_SESSION['seva_sisters']);
                                unset($_SESSION['utsav_brothers']);
                                unset($_SESSION['utsav_sisters']);
                                unset($_SESSION['anugraha_brothers']);
                                unset($_SESSION['anugraha_sisters']);
                                unset($_SESSION['meeting_brothers']);
                                unset($_SESSION['meeting_sisters']);
                                
                                echo '<script>alert("Form data submitted successfully!");</script>';
                            } else {
                                echo "Error: " . $sql . "<br>" . mysqli_error($db);
                            }
            }
            else
            {
                echo '<script>alert("Bhagini count is more than remaining Bhagini");</script>';
            }
                                
        }//close bandhu count exist
        else
        {
            echo '<script>alert("Bandhu count is more than remaining Bhagini");</script>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html {
            margin: auto;
            width: 60%;
            border: 5px solid #b8860b; /* Golden Rod Border */
            padding: 10px;
            border-radius: 15px;
			margin-top:60px;
        }
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
		.tableStyle{
			border: 1px solid #808080;
			width: 100%;			
		}
		.tableStyle th {
			font-weight: bold;
			background-color: #C0C0C0;
			border: 1px solid;
			padding-left:5px;
		}
		.tableStyle tr td {
			border: 1px solid;
			padding-left:5px;
		}
		.dropdownStyle{
			width: 80%
		}
		.form-control{
			display: inline !important;
			width: 50% !important;
		}
    </style>

    <!-- close for body border -->
        <script>
        function calculateTotals() {
            // Get values from input fields
            let breakfastBrothers = parseInt(document.querySelector('input[name="breakfast_brothers"]').value) || 0;
            let lunchBrothers = parseInt(document.querySelector('input[name="lunch_brothers"]').value) || 0;
            let dinnerBrothers = parseInt(document.querySelector('input[name="dinner_brothers"]').value) || 0;
            let breakfastSisters = parseInt(document.querySelector('input[name="breakfast_sisters"]').value) || 0;
            let lunchSisters = parseInt(document.querySelector('input[name="lunch_sisters"]').value) || 0;
            let dinnerSisters = parseInt(document.querySelector('input[name="dinner_sisters"]').value) || 0;

            // Calculate brothers and sisters totals
            let brothersTotal = breakfastBrothers + lunchBrothers + dinnerBrothers;
            let sistersTotal = breakfastSisters + lunchSisters + dinnerSisters;

            // Update the brothers, sisters, and total_people fields
            document.querySelector('input[name="brothers"]').value = brothersTotal;
            document.querySelector('input[name="sisters"]').value = sistersTotal;
            document.querySelector('input[name="total_people"]').value = brothersTotal + sistersTotal;
        }

        // Add event listeners to the meal inputs to trigger recalculation on change
        document.addEventListener('DOMContentLoaded', (event) => {
            const mealInputs = document.querySelectorAll('input[name^="breakfast_"], input[name^="lunch_"], input[name^="dinner_"]');
            mealInputs.forEach(input => {
                input.addEventListener('input', calculateTotals);
            });
        });
    </script>


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


  <!-- Close Input type marathi -->



  
    <!-- Include Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (required for Select2) -->
    <script src="dropdownassets/jquery-3.6.0.min.js"></script>

    <!-- Include Select2 JS -->
    <!-- <script src="dropdownassets/select2.min.js"></script> -->

  <script>

    // Call fetchUpdatedSevaData() when the page loads
    window.onload = function() {
        fetchUpdatedSevaData();
    };
  </script>

</head>

<body class="sidebar-toggled">

  <!-- Header -->
  <?php include('header.php'); ?>
  <!-- End Header -->



  <div class="content">
		<div style="overflow: hidden;">
			<div class="center" style="width: 30%; float: left;">|| ॐ नमः शिवाय ||</div>
			<div class="center" style="width: 40%; float: left;">|| श्री गुरुदेव प्रसन्न ||</div>
			<div class="center" style="width: 30%; float: right;">|| ॐ नमः शिवाय ||</div>
		</div>		
		<div style="text-align: center; margin-top: 10px;"><h5>परमपुज्य आईंच्या चरणी शिरसाष्टांग दंडवत</h1></div>
		<div id="dataDisplay">
            <!-- Updated seva data will be injected here -->
        </div>
		<br>
		<div>
			<form method="POST" action="addnewrecord.php" accept-charset="UTF-8">
            <!-- Other form fields -->

            <table border="1" id="seva" class="tableStyle">
                <tr>
                    <th>सेवा/अनुग्रह/उपासना</th>
                    <th>उत्सव</th>
                    <th>बंधू</th>
                    <th>भगिनी</th>
                </tr>



                <tr>
                    <td>उत्सव</td>
                    <td>
                        <select class="dropdownStyle" name="utsav_type" id="utsav_type">
                            <option><?php echo $_SESSION['utsav_type'];?></option>
                            <?php 
                                $query_utsav = "SELECT DISTINCT Category_Name FROM sevautsav WHERE Category='उत्सव'";
                                $result_utsav = mysqli_query($db, $query_utsav);
                                while ($row_utsav = mysqli_fetch_assoc($result_utsav)) {
                                    echo '<option value="' . htmlspecialchars($row_utsav['Category_Name']) . '">' . htmlspecialchars($row_utsav['Category_Name']) . '</option>';
                                }
                            ?>
                        </select>
                    </td>
                    <td><input type="number" name="utsav_brothers" min="0" max="100" value="<?php echo $_SESSION['utsav_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="utsav_sisters" min="0" max="100" value="<?php echo $_SESSION['utsav_sisters'];?>" placeholder="0"></td>
                </tr>
                <tr>
                    <td>सेवा</td>
                    <td>
                        <div class="inline-form">
                            <select class="dropdownStyle" name="seva_type" id="seva_type">
                                <option><?php echo $_SESSION['seva_type'];?></option>
                                <?php 
                                    $query_seva = "SELECT DISTINCT CONCAT(Category_Name, ' ', Gat_Kramank) AS Category_Gat FROM sevautsav WHERE Category = 'सेवा'";
                                    $result_seva = mysqli_query($db, $query_seva);
                                    while ($row_seva = mysqli_fetch_assoc($result_seva)) {
                                        echo '<option value="' . htmlspecialchars($row_seva['Category_Gat']) . '">' . htmlspecialchars($row_seva['Category_Gat']) . '</option>';
                                    }
                                ?>
                            </select>
                            <button type="button" onclick="addSeva()">+</button>
                        </div>
                    </td>
                    <td><input type="number" name="seva_brothers" id="seva_brothers" min="0" max="100" value="<?php echo $_SESSION['seva_brothers']?>" placeholder="0"></td>
                    <td><input type="number" name="seva_sisters" id="seva_sisters" min="0" max="100" value="<?php echo $_SESSION['seva_sisters']?>" placeholder="0"></td>
                </tr>
                <tr>
                        <div id="dataDisplay">
                            <!-- Updated seva data will be injected here -->
                        </div>
                </tr>

                <!-- add seva script -->
                <!-- add seva script -->
                <script>
                    function addSeva() {
                        let sevaType = document.getElementById("seva_type").value;
                        let sevaBrothers = document.getElementById("seva_brothers").value || 0;
                        let sevaSisters = document.getElementById("seva_sisters").value || 0;

                            if (!sevaType) {
                                alert("Please select a Seva type.");
                                return;
                            }

                            $.ajax({
                                    url: 'insert_seva.php',
                                    method: 'POST',
                                    data: {
                                        seva_type: sevaType,
                                        seva_brothers: sevaBrothers,
                                        seva_sisters: sevaSisters
                                    },
                                    success: function(response) {
                                        alert(response);

                                        // After successful addition, fetch the updated data
                                        fetchUpdatedSevaData();
                                        document.getElementById("seva_brothers").value = "";
                                        document.getElementById("seva_sisters").value = "";
                                    },
                                    error: function() {
                                        alert("There was an error processing your request.");
                                    }
                                });
                            }

                            function fetchUpdatedSevaData() {
                                $.ajax({
                                    url: 'fetch_seva_data.php', // PHP script to fetch the updated Seva data
                                    method: 'GET',
                                    success: function(data) {
                                        $('#dataDisplay').html(data); // Inject the updated data into the page
                                    },
                                    error: function() {
                                        alert("There was an error fetching the updated data.");
                                    }
                                });
                            }
                </script>
                <tr>
                    <td>अनुग्रह</td>
                    <td>
                        <select class="dropdownStyle" name="anugraha_type" id="anugraha_type">
                            <option><?php echo $_SESSION['anugraha_type'];?></option>
                            <?php 
                                $query_anugraha = "SELECT DISTINCT Category_Name FROM sevautsav WHERE Category='अनुग्रह'" . $utsav_filter;
                                $result_anugraha = mysqli_query($db, $query_anugraha);
                                while ($row_anugraha = mysqli_fetch_assoc($result_anugraha)) {
                                    echo '<option value="' . htmlspecialchars($row_anugraha['Category_Name']) . '">' . htmlspecialchars($row_anugraha['Category_Name']) . '</option>';
                                }
                            ?>
                        </select>
                    </td>
                    <td><input type="number" name="anugraha_brothers" min="0" max="100"  value="<?php echo $_SESSION['anugraha_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="anugraha_sisters" min="0" max="100"  value="<?php echo $_SESSION['anugraha_sisters'];?>" placeholder="0"></td>
                </tr>
                <tr>
                    <td>मीटिंग</td>
                    <td>
                        <select class="dropdownStyle" name="meeting_type" id="meeting_type">
                            <option><?php echo $_SESSION['meeting_type'];?></option>
                            <?php 
                                $query_meeting = "SELECT DISTINCT Category_Name FROM sevautsav WHERE Category='मीटिंग'" . $utsav_filter;
                                $result_meeting = mysqli_query($db, $query_meeting);
                                while ($row_meeting = mysqli_fetch_assoc($result_meeting)) {
                                    echo '<option value="' . htmlspecialchars($row_meeting['Category_Name']) . '">' . htmlspecialchars($row_meeting['Category_Name']) . '</option>';
                                }
                            ?>
                        </select>
                    </td>
                    <td><input type="number" name="meeting_brothers" min="0" max="100"  value="<?php echo $_SESSION['meeting_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="meeting_sisters" min="0" max="100"  value="<?php echo $_SESSION['meeting_sisters'];?>" placeholder="0"></td>
                </tr>
            </table>

            <br>

            <table border="1" id="Prasad" class="tableStyle">
                <tr>
                    <th>प्रसादाची वेळ</th>
                    <th>बंधू</th>
                    <th>भगिनी</th>
                </tr>
                <tr>
                    <td>सकाळच्या नाष्टा</td>
                    <td><input type="number" name="breakfast_brothers" min="0" max="100"  value="<?php echo $_SESSION['breakfast_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="breakfast_sisters" min="0" max="100"  value="<?php echo $_SESSION['breakfast_sisters'];?>" placeholder="0"></td>
                </tr>
                <tr>
                    <td>दुपारचा प्रसाद</td>
                    <td><input type="number" name="lunch_brothers" min="0" max="100"  value="<?php echo $_SESSION['lunch_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="lunch_sisters" min="0" max="100"  value="<?php echo $_SESSION['lunch_sisters'];?>" placeholder="0"></td>
                </tr>
                <tr>
                    <td>रात्रीचा प्रसाद</td>
                    <td><input type="number" name="dinner_brothers" min="0" max="100"  value="<?php echo $_SESSION['dinner_brothers'];?>" placeholder="0"></td>
                    <td><input type="number" name="dinner_sisters" min="0" max="100"  value="<?php echo $_SESSION['dinner_sisters'];?>" placeholder="0"></td>
                </tr>
            </table>
            <br>
                <p>
                    आमच्या <b>
                            <select name="branch" id="shakhaSelect" class="form-control" required>
                                <option><?php echo $_SESSION['branch'];?></option>
                                <?php 
                                    $query_shakha = "SELECT * FROM mandir_branch";
                                    $result_shakha = mysqli_query($db, $query_shakha);
                                    while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                                        $unique_code = $row_shakha['unique_code'];
                                    ?>
                                    <option value="<?php echo $unique_code; ?>">
                                        <?php echo htmlspecialchars($row_shakha['shakha']); ?>
                                    </option>
                                <?php
                                    }
                                ?>
                            </select>
                                <script>
                                    $(document).ready(function() {
                                        $('#shakhaSelect').select2({
                                            placeholder: "Select an option",
                                            allowClear: true
                                        });
                                    });
                                </script>

                
                </b> 
                    
                    
                    शाखेतून 
                    <input type="number" name="brothers" min="0" max="100" readonly value="<?php echo $_SESSION['brothers'];?>" placeholder="0"> बंधू व 
                    <input type="number" name="sisters" min="0" max="100" readonly value="<?php echo $_SESSION['sisters'];?>" placeholder="0"> भगिनी एकूण 
                    <input type="number" name="total_people" min="0" max="100" readonly value="<?php echo $_SESSION['total_people'];?>" placeholder="0"> मंडळी
                    दिनांक: <input type="date" name="start_date" id="start_date" value="<?php echo $_SESSION['start_date'];?>" required> ते दिनांक 
                    <input type="date" name="end_date" id="end_date" value="<?php echo $_SESSION['end_date'];?>" required>
                    पर्यंत - <input type="number" name="duration" min="0" max="100" readonly value="<?php echo $_SESSION['duration'];?>"> दिवसांकरिता श्रीहरिमंदिरात येऊ इच्छितात, तरी कृपया परवानगी मिळावी.
                </p>

            <center><button type="submit" name="reg_user" class="btn btn-success">Add Data</button></center>
        </form>
        <form action="#" method="post">
            <center><button type="submit" name="clear_all" class="btn btn-secondary">Clear All</button></center>
        </form>
    </div>
<!-- duration end date - start date -->
<script>
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationInput = document.querySelector('input[name="duration"]');

    function calculateDuration() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && endDate >= startDate) {
            const timeDifference = endDate - startDate;
            const daysDifference = timeDifference / (1000 * 3600 * 24);
            durationInput.value = daysDifference + 1; // Add 1 to include the start date
        } else {
            durationInput.value = 0; // Reset if dates are invalid or end_date is before start_date
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
</script>
<!-- close duration end date - start date -->
    <script>
        $(document).ready(function() {
            function updateDateFields(dates) {
                $('#start_date').attr('min', dates.start_date);
                $('#start_date').attr('max', dates.end_date);
                $('#end_date').attr('min', dates.start_date);
                $('#end_date').attr('max', dates.end_date);
                $('#start_date').val('');
                $('#end_date').val('');
            }
            $('select[name="seva_type"], select[name="utsav_type"], select[name="anugraha_type"], select[name="meeting_type"]').change(function() {
                var type = $(this).val();
                if (type) {
                    $.ajax({
                        url: 'fetch_dates.php', // URL to PHP script that fetches dates
                        type: 'POST',
                        data: {type: type},
                        success: function(response) {
                            var dates = JSON.parse(response);
                            updateDateFields(dates);
                        },
                        error: function() {
                            alert('Error fetching dates.');
                        }
                    });
                }
            });
        });
    </script>

<script>
    // Fetch and update dependent dropdowns based on utsav_type
    $('#utsav_type').change(function() {
        var utsav_type = $(this).val();
        // Update seva_type
        $.post('fetch_seva.php', { utsav_type: utsav_type }, function(data) {
            $('#seva_type').html(data);
        });

        // Update anugraha_type
        $.post('fetch_anugraha.php', { utsav_type: utsav_type }, function(data) {
            $('#anugraha_type').html(data);
        });

        // Update meeting_type
        $.post('fetch_meeting.php', { utsav_type: utsav_type }, function(data) {
            $('#meeting_type').html(data);
        });
    });
</script>
</body>
</html>
