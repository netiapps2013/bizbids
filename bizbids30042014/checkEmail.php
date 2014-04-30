<?php 
include_once('externconfig.php');

$key = $_REQUEST['emailid'];
$sel="select * from bbids_user where email like '%".$key."%'";
$res = mysql_query($sel);
$noOfrows = mysql_num_rows($res);
if($noOfrows>0)
{
		echo 'exists';		
}else {
		echo 'donotexists';
}

?>
