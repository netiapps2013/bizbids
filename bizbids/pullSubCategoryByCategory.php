<?php
mysql_connect("localhost","anup","tripleseven7");
mysql_select_db("bizbids");

$parentid = $_REQUEST['categoryid'];

$sql = "select * from bbids_categories where parentid=".$parentid;
$res = mysql_query($sql);
$output = '<option value="">Select</option>';
while($data = mysql_fetch_array($res)){
	
$output .= 	'<option value="'.$data['id'].'">'.$data['category'].'</option>';
}

echo $output;
?>
