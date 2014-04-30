<?php 
include_once('externconfig.php');

$sel = "select * from bbids_enquirysubcategoryrel where enquiryid=".$_REQUEST['enqid'];
$qry = mysql_query($sel);
echo $noOfrows = mysql_num_rows($qry);

?>
