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

$formId = "0";

if(isset($_GET['id'])){
    $formId =$_GET['id']; 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove') {
    try {
        $rowId = intval($_POST['rowId']);
        $formSubmissionId = intval($_POST['formSubmissionId']);
        $genderId = $_POST['genderId'];

        // Mark record as deleted
        $deleteBandhuBhaginiQuery = "UPDATE utsavbandhubhagini SET isDeleted=true WHERE id='$rowId'";
        if (mysqli_query($db, $deleteBandhuBhaginiQuery)) {
            // Update the gender-based counters
            if ($genderId == "1") {
                $updateBandhuBhaginiCount = "UPDATE form_submissions
                                            SET brothers = (brothers - 1),
                                                total_people = (total_people - 1)
                                            WHERE id='$formSubmissionId'";
            } else {
                $updateBandhuBhaginiCount = "UPDATE form_submissions
                                            SET sisters = (sisters - 1),
                                                total_people = (total_people - 1)
                                            WHERE id='$formSubmissionId'";
            }

            if (mysqli_query($db, $updateBandhuBhaginiCount)) {
                // Success response to AJAX
                echo json_encode(['status' => 'success']);
                exit();
            } else {
                // Error while updating form submission
                echo json_encode(['status' => 'error', 'message' => 'Error updating form submission']);
                exit();
            }
        } else {
            // Error while deleting the record
            echo json_encode(['status' => 'error', 'message' => 'Error deleting record']);
            exit();
        }
    } catch (Exception $e) {
        // Handle exceptions
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}


// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $id = $_POST['utsavbandhubhagini_id'];
        $formSubmissionId = $_POST['formSubmissionId'];
        $name = $_POST['name'];
        $age = $_POST['age'];
        
        $query = "UPDATE utsavbandhubhagini SET name='$name', age='$age' WHERE id='$id'";

        if (mysqli_query($db, $query)) {
            echo '<script>alert("Record updated successfully");</script>';
            echo '<script>window.location.replace("editpunyatithi.php?id=' . $formSubmissionId . '");</script>';
            exit();

        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        $error_message = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Punyatithi Utsav</title>
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

  <!-- <link href=
"https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" 
          rel="stylesheet" type="text/css" /> -->
    <link rel="stylesheet" href=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

  <!-- Bootstrap JS and Dependencies -->
  <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
        .datepicker {
          z-index: none !important;
          margin: 40px 0 0 90px;
        }
        .table-bordered>thead>tr>th {
            border: 1px solid #000 !important;
        }
        .table-bordered>tbody>tr>td {
            border: 1px solid #000 !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
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
            <form method="POST" action="editpunyatithi.php" accept-charset="UTF-8">
            <?php 
                $query_form_records = "SELECT *, form_submissions.id AS refno 
                                        FROM form_submissions
                                        INNER JOIN occasions ON form_submissions.occasion_id=occasions.id
                                        WHERE form_submissions.id='$formId'";
                $result_form_records = mysqli_query($db,$query_form_records);
                $row_form_record = mysqli_fetch_array($result_form_records)
            ?>
    <div>
    <div class="w-100">
        <div class="text-right" style="font-family:none;">Ref No : <?php echo htmlspecialchars($row_form_record['occasion_code']); ?>_<?php echo htmlspecialchars($row_form_record['refno']); ?></div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="Category">Occasion</label>
            <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['utsav_type']); ?></b></div>
        </div>
        <div class="col-md-8">
            <label for="Category">Shakha</label>
            <div class="form-control" readonly><b><?php echo htmlspecialchars($row_form_record['branch']); ?></b></div>
        </div>
        <div class="col-md-3">
            <label for="Category">Quota</label>
            <input type="text" class="form-control" id="quota_total" name="quota_total" value="0" readonly />
        </div>
        <div class="col-md-3">
            <label for="Category">Available Bandhu</label>
            <input type="text" class="form-control" id="bandhu_available" name="bandhu_available" value="0" readonly />
        </div>
        <div class="col-md-3">
            <label for="Category">Available Bhagini</label>
            <input type="text" class="form-control" id="bhagini_available" name="bhagini_available" value="0" readonly />
        </div>
        <div class="col-md-3">
            <label for="Category">Available Total</label>
            <input type="text" class="form-control" id="quota_available" name="quota_available" value="0" readonly />
        </div>
        <div class="col-md-4">
            <label for="Category">Letter Dated</label>
            <div class="form-control" readonly>
                <?php 
                    $convertedLetterDate = date("d-F-Y", strtotime($row_form_record['letter_dated']));
                    echo $convertedLetterDate; 
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <label for="Category">Date</label>
            <div class="form-control" readonly>
                <?php 
                    $convertedFormDate = date("d-F-Y", strtotime($row_form_record['form_dated']));
                    echo $convertedFormDate; 
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <label for="Category">Time Of Arrival</label>
            <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['time_of_arrival']); ?></div>
        </div>
        <div class="col-md-4">
            <label for="Category">No Of Bandhu</label>
            <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['brothers']); ?></div>
        </div>
        <div class="col-md-4">
            <label for="Category">No Of Bhagini</label>
            <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['sisters']); ?></div>
        </div>
        <div class="col-md-4">
            <label for="Category">Total</label>
            <div class="form-control" readonly><?php echo htmlspecialchars($row_form_record['total_people']); ?></div>
        </div>
        <div class="col-md-8">
            <label for="Category">Period of Stay</label>
            <div class="form-control" readonly>
                From <?php 
                    $convertedStartDate = date("d-F-Y", strtotime($row_form_record['start_date']));
                    echo $convertedStartDate; 
                ?> 
                To <?php 
                    $convertedEndDate = date("d-F-Y", strtotime($row_form_record['end_date']));
                    echo $convertedEndDate; 
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <label for="Category">Duration</label>
            <input class="form-control" type="number" name="Duration" readonly value="<?php echo $row_form_record['duration'];?>">
        </div>
    </div>
</div>


                    <div class="col-md-12" style="margin-top:10px;">
                    <?php 
                        $query_bandhuBhagini = "SELECT utsavbandhubhagini.*, gendermaster.Gender, prakarmaster.type
                                                FROM utsavbandhubhagini
                                                INNER JOIN gendermaster ON gendermaster.id = utsavbandhubhagini.gender
                                                INNER JOIN prakarmaster ON prakarmaster.id = utsavbandhubhagini.seva
                                                WHERE form_submission_id='$formId'
                                                AND isDeleted=false
                                                ORDER BY utsavbandhubhagini.name ASC";
                        $result_bandhuBhagini = mysqli_query($db,$query_bandhuBhagini);
                    ?>
                        <div>
                        <table class="table table-bordered table-hover" id="tblBandhuBhaginiDetails">
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>
                                    <th class="text-center">
                                        Name
                                    </th>
                                    <th class="text-center">
                                        Age
                                    </th>
                                    <th class="text-center">
                                        Gender
                                    </th>
                                    <th class="text-center">
                                        Reason
                                    </th>
                                    <th class="text-center">Start Date</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center"> Actions </th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                $index = 0;
                                while($row_bandhuBhagini = mysqli_fetch_array($result_bandhuBhagini))
                                {
                                    $index++;
                            ?>
                                <tr id="row-<?php echo $row_bandhuBhagini['id'];?>" 
                                    style="background-color: <?php echo $row_bandhuBhagini['gender'] == '1' ? '#c3c5f7' : '#ffccff'; ?>;">
                                    <td><?php echo $index ?></td>
                                    <td><?php echo $row_bandhuBhagini['name'];?></td>
                                    <td><?php echo $row_bandhuBhagini['age'];?></td>
                                    <td><?php echo $row_bandhuBhagini['Gender'];?></td>
                                    <td><?php echo $row_bandhuBhagini['type'];?></td>
                                    <td><?php $convertedStartDate = date("d-M-Y", strtotime($row_bandhuBhagini['start_date']));
                                        echo $convertedStartDate;?>
                                    </td>
                                    <td><?php $convertedEndDate = date("d-M-Y", strtotime($row_bandhuBhagini['end_date']));
                                        echo $convertedEndDate;?>
                                    </td>
                                    <td>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal(<?= htmlspecialchars($row_bandhuBhagini['id']) ?>, <?= htmlspecialchars($row_bandhuBhagini['form_submission_id']) ?>, '<?= htmlspecialchars($row_bandhuBhagini['name']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['age']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['Gender']) ?>', '<?= htmlspecialchars($row_bandhuBhagini['type']) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['start_date']))) ?>', '<?= htmlspecialchars(date("d-M-Y", strtotime($row_bandhuBhagini['end_date']))) ?>')">Update</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="RemoveRecord('<?php echo $row_bandhuBhagini['id'];?>', '<?php echo $row_bandhuBhagini['form_submission_id'];?>', '<?php echo $row_bandhuBhagini['gender'];?>');">Remove</button>
                                    </td>
                                </tr> 
                            <?php } ?>   
                            <tbody>
                        </table>
                        <input type="hidden" name="rowId" id="rowId">
                        <input type="hidden" name="formSubmissionId" id="formSubmissionId">                
                        <input type="hidden" name="genderId" id="genderId">
                        <input type="hidden" name="batch" id="batch" value="<?php echo $row_form_record['batch']; ?>">
                        <input type="hidden" name="branchcode" id="branchcode" value="<?php echo $row_form_record['branch_code']; ?>">
                        <input type="hidden" name="occasionid" id="occasionid" value="<?php echo $row_form_record['occasion_id']; ?>">
                        </div>
                    </div>                    
                </div>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div id="UpdateModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                    <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="UpdateForm" action="" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="utsavbandhubhagini_id" id="utsavbandhubhagini_id">
                        <input type="hidden" name="formSubmissionId" id="formSubmissionIdModal">

                        <div class="form-group">
                            <label for="modalName">Name:</label>
                            <input type="text" name="name" id="modalName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="modalAge">Age:</label>
                            <input type="text" name="age" id="modalAge" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="modalGender">Gender:</label>
                            <input type="text" name="gender" id="modalGender" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalType">Type:</label>
                            <input type="text" name="type" id="modalType" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalStartDate">Start Date:</label>
                            <input type="text" name="start_date" id="modalStartDate" class="form-control" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="modalEndDate">End Date:</label>
                            <input type="text" name="end_date" id="modalEndDate" class="form-control" required readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                            <input type="submit" value="Update" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer"></footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script>
    function RemoveRecord(rowId, formSubmissionId, genderId) {
        if (confirm("Are you sure you want to delete this entry?")) {
            $.ajax({
                url: 'editpunyatithi.php',
                type: 'POST',
                data: {
                    rowId: rowId,
                    formSubmissionId: formSubmissionId,
                    genderId: genderId,
                    action: 'remove'
                },
                success: function(response) {
                    var data = JSON.parse(response); // Parse JSON response from PHP

                    if (data.status === 'success') {
                        alert("Record deleted successfully");
                        window.location.replace(window.location.href); // Redirect without re-submitting the form
                    } else {
                        alert("Error: " + data.message); // Display error message if deletion failed
                    }
                },
                error: function() {
                    alert("An unexpected error occurred.");
                }
            });
        }
    }





    function openModal(id, formSubmissionId, name, age, gender, type, start_date, end_date) {
        document.getElementById('utsavbandhubhagini_id').value = id;
        document.getElementById('formSubmissionIdModal').value = formSubmissionId;
        document.getElementById('modalName').value = name;
        document.getElementById('modalAge').value = age;
        document.getElementById('modalGender').value = gender;
        document.getElementById('modalType').value = type;

        document.getElementById('modalStartDate').value = start_date;
        document.getElementById('modalEndDate').value = end_date;

        // Show the modal using Bootstrap's modal method
        $('#UpdateModal').modal('show');
    }

    function closeModal() {
        // Close the modal using Bootstrap's modal method
        $('#UpdateModal').modal('hide');
    }
</script>

<script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
</body>
</html>