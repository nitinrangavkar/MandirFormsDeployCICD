<?php

//$db=mysqli_connect('localhost', 'root', '', 'bhsyujms_harimandir');

$db=mysqli_connect('localhost', 'u847347078_UAT_Admin', 'G>2@Yr?j>!i', 'u847347078_UAT');

session_start();
date_default_timezone_set("Asia/Kolkata");
$db->set_charset("utf8mb4");
if(!$db){
die('Could not Connect My Sql Check:' .mysqli_error());
}
?> 