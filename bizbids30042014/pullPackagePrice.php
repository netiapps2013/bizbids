<?php
include_once('externconfig.php');
$catid = $_REQUEST['categoryid'];

$sql = "select price from bbids_categories where status=1 and id = ".$catid;
$res = mysql_query($sql);
while($data = mysql_fetch_assoc($res)){

echo $price = $data['price'];

}
?>
