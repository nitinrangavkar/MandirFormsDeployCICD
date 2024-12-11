<?php


// Session assigned
  if (!isset($_SESSION['usern'])) 
  {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: ../login.php');
  }
  if (isset($_GET['logout'])) 
  {
  	session_destroy();
  	unset($_SESSION['superuser']);
  	header("location: ../login.php");
  }
// Session assigned completed

header('Location: login.php');
exit;

?>