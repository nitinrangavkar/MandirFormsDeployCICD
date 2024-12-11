<?php
include('../db.php');


if (!isset($_SESSION['IL_email'])) 
{
  $_SESSION['msg'] = "You must log in first";
  header('location: https://www.godjn.com');
}
if (isset($_GET['logout'])) 
{
  session_destroy();
  unset($_SESSION['IL_email']);
  header("location: https://www.godjn.com");
}


$name_dr_o = $_SESSION['IL_email'];

$date_time = date("d-m-Y") . " | " . date("h:i:sa");
$page_visit = "index";
$user_email_session = $_SESSION['IL_email'];
$role_session = $_SESSION['role'];
$vendor_session = $_SESSION['vendor'];

$ip_address = $_SERVER['REMOTE_ADDR'];


// initializing variables

$first_name ="";
$last_name ="";
$address = "";
$email ="";
$phone_no ="";

if(isset($_POST['reg_user']))
{
	$role = $_POST['role'];  // Storing Selected Value In Variable	
}


$password    = "";
$errors = array(); 




if(isset($_POST['update_user_info'])) 
{
  $phone_no = mysqli_real_escape_string($db, $_POST['phone_no']);
  $query_user_info = "SELECT * FROM users WHERE phone_no='$phone_no'";

  echo $query_user_info;
  $result_user_info = mysqli_query($db,$query_user_info);
  $row_user_info = mysqli_fetch_array($result_user_info);
}

if (isset($_POST['deactivate'])) 
{
  $phone_no = mysqli_real_escape_string($db, $_POST['phone_no']);
  $query = "UPDATE users SET first_name='$first_name', last_name='$last_name', address='$address', phone_no='$phone_no', phone_no='$phone_no', phone_no='$phone_no',role='$role', password='$password', status='$status' WHERE phone_no='$phone_no'";
	
  if(mysqli_query($db, $query))
  {
    echo '<script>alert("User deactivated successfully");</script>';
  }
}


if (isset($_POST['reg_user'])) 
{
  // receive all input values from the form
  $first_name = mysqli_real_escape_string($db, $_POST['first_name']);
  $last_name = mysqli_real_escape_string($db, $_POST['last_name']);
  $address = mysqli_real_escape_string($db, $_POST['address']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone_no = mysqli_real_escape_string($db, $_POST['phone_no']);
  $vendor = mysqli_real_escape_string($db, $_POST['vendor']);
  $role = mysqli_real_escape_string($db, $_POST['role']);
  $password = mysqli_real_escape_string($db, $_POST['password']);
  $status    = "1";
  $updated_date = date("d-m-Y");
  $updated_time = date("h:i:sa");
  $updated_date_time = $updated_date .' | '. $updated_time;

  $districts = mysqli_real_escape_string($db, $_POST['districts']);
  //$updated_by = $_SESSION('email');

  // first check the database to make sure
  $user_check_query = "SELECT * FROM users WHERE phone_no='$phone_no' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = @mysqli_fetch_assoc($result);
  
  
  if ($user) 
  { // if user exists
    if ($user['phone_no'] === $phone_no) 
    {

      if($password == "")
      {
	      $query = "UPDATE users SET first_name='$first_name', last_name='$last_name', address='$address', email='$email', phone_no='$phone_no', role='$role', status='$status', vendor='$vendor', districts='$districts' WHERE phone_no='$phone_no'";
      }
      else
      {
        $password = md5($password);//encrypt the password before saving in the database

        $query = "UPDATE users SET first_name='$first_name', last_name='$last_name', address='$address', email='$email', phone_no='$phone_no', role='$role', password='$password', status='$status', vendor='$vendor', districts='$districts' WHERE phone_no='$phone_no'";
      }

      if(mysqli_query($db, $query))
	    {
		    echo '<script>alert("User Updated Successfully with an existing email");</script>';
	    }
    }
  }
  else
  {
  	$password = md5($password);//encrypt the password before saving in the database

  	$query = "INSERT INTO users (first_name, last_name, address, email, phone_no, role, password, status, updated_date_time, vendor,districts) 
  			  VALUES('$first_name','$last_name','$address','$email','$phone_no','$role','$password','$status','$updated_date_time','$vendor','$districts')";
  	  if(mysqli_query($db, $query))
	    {
		    echo '<script>alert("User Created Successfully");</script>';
	    }
  }
}
?>
<?php
include('users_server.php');

?>
<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>User Management</title>
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

  <div class="pagetitle">
        <h1>Register New User</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">User Management</li>
          </ol>
        </nav>
  </div>
  <!--================================= End Page Title =========================================-->
  <!-- Super -->
    <?PHP  
    if($role_session == "Super Admin") 
    {
    ?>
      <section class="section dashboard">
          <div class="row">
            <!-- Left side columns -->
              <div class="col-lg-12">
                <div class="row">



                <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Register New User</h5>

                  <!-- Multi Columns Form -->
                  <form class="row g-3" name="#" method="post">
                    <div class="col-md-6">
                      <label for="inputName5" class="form-label">First Name</label>
                      <input type="text" name="first_name" class="form-control" id="inputName5" value="<?php echo $row_user_info['first_name']; ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="inputName5" class="form-label">Last Name</label>
                      <input type="text"  name="last_name" class="form-control" id="inputName5" value="<?php echo $row_user_info['last_name']; ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="inputEmail5" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="inputEmail5" value="<?php echo $row_user_info['email']; ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="inputPassword5" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="inputPassword5" >
                    </div>

                    <!-- Address -->
                    <!-- <div class="col-md-6">
                      <label for="inputState" class="form-label">Address</label>
                      <select id="inputState" name="address" class="form-select" required>
                        <option><?php echo $row_user_info['address']; ?></option>
                        <option>UP</option>
                        <option>MP</option>
                      </select>
                    </div> -->
                    <div class="col-md-6">
                      <label for="inputName5" class="form-label">State</label>
                      <input type="text" name="address" class="form-control" id="inputName5" value="<?php echo $row_user_info['address']; ?>" required>
                    </div>
                    <!-- Internal Team and Police will add by GODJN as of now -->
                    <div class="col-md-6">
                      <label for="inputState" class="form-label">Role</label>
                      <select id="inputState" name="role" class="form-select" required>
                        <option><?php echo $row_user_info['role']; ?></option>
                        <option>State Admin</option>
                        <option>Police</option>
                        <option>Vendor Admin</option>
                        <option>FOS</option>
                      </select>
                    </div>
                    <div class="col-md-12">
                      <label for="inputName5" class="form-label">Districts</label>
                      <input type="text" name="districts" class="form-control" id="inputName5" value="<?php echo $row_user_info['districts']; ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="inputAddress2" class="form-label">Phone Number</label>
                      <input type="text" name="phone_no" class="form-control" id="inputAddress2" value="<?php echo $row_user_info['phone_no']; ?>" required>
                    </div>
                    <!-- <div class="col-md-6">
                      <label for="inputState" class="form-label">Vendor</label>
                      <select id="inputState" name="vendor" class="form-select" required>
                        <option><?php echo $row_user_info['vendor']; ?></option>
                        <option>PCS</option>
                        <option>Moto Check</option>
                      </select>
                    </div> -->
                    <div class="col-md-6">
                      <label for="inputName5" class="form-label">Profille Name</label>
                      <input type="text" name="vendor" class="form-control" id="inputName5" value="<?php echo $row_user_info['vendor']; ?>" required>
                    </div>
                    
                    <!-- Close Internal Team and Police will add by GODJN as of now -->
                    <div class="text-center">
                      <button type="submit" name="reg_user" class="btn btn-primary">Submit</button>
                      <button type="reset" class="btn btn-secondary">Reset</button>
                    </div>
                  </form><!-- End Multi Columns Form -->

                </div>
              </div>

                </div>
              </div><!-- End 12 side columns -->
          </div>
        </section>



        <div class="row">
            <div class="col-lg-12">
          
                  <h5 class="card-title">MIS</h5>

                  <!-- Table with stripped rows -->
                  <!-- <table class="table datatable"> -->
                  <table>
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Role</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col">State</th>
                        <th scope="col">Districts</th>
                        <th scope="col">Profile Name</th>
                        <th scope="col">Status</th>
                        <th scope="col">Update</th>
                        <th scope="col">Deactivate</th>
                        <th scope="col">Updated Date</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php 
                        $query_all_records = "SELECT * FROM users";
                        $result_all_records = mysqli_query($db,$query_all_records);
                        $i = 0;
                        while($row_all_records = mysqli_fetch_array($result_all_records))
                        {
                          $i = $i + 1;
                    ?>
                      <tr>
                        <th scope="row"><?php echo $i;?></th>
                        <td><?php echo $row_all_records['role'];?></td>
                        <td><?php echo $row_all_records['first_name'];?></td>
                        <td><?php echo $row_all_records['last_name'];?></td>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </td>
                        <td><?php echo $row_all_records['email'];?></td>
                        <td><?php echo $row_all_records['phone_no'];?></td>
                        <td><?php echo $row_all_records['address'];?></td>
                        <td><?php echo $row_all_records['districts'];?></td>
                        <td><?php echo $row_all_records['vendor'];?></td>
                        <td><?php echo $row_all_records['status'];?></td>
                        <td>
                            <form name="#" method="post">
                                <input type="hidden" name="phone_no" value="<?php echo $row_all_records['phone_no'];?>">
                                <button type="submit" name="update_user_info" class="btn btn-primary btn-sm">Edit User</button><br>
                            </form>
                        </td>
                        <td>
                            <form name="#" method="post">
                              <input type="hidden" name="phone_no" value="<?php echo $row_all_records['phone_no'];?>">
                              <button type="submit" name="deactivate" class="btn btn-primary btn-sm">Deactivate</button><br>
                            </form>
                        </td>

                        <td><?php echo $row_all_records['updated_date_time'];?></td>
                      </tr>
                    <?php
                        }
                    ?>

                    </tbody>
                  </table>
                  <!-- End Table with stripped rows -->

                </div>
              </div>

            </div>
          </div>
      </section>
    <?PHP
    }
    ?>
    <!-- Close Super -->

</main><!-- End #main -->


  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>GODJN Solutions Pvt. Ltd.</span></strong>. All Rights Reserved
    </div>

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