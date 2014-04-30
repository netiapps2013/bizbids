<?php

mysql_connect('localhost','anup', 'tripleseven7');
mysql_select_db('bizbids');

$count = count($_POST['categories']);
echo $uid = $_POST['uid'];

$maxorderSql = "SELECT MAX(id) AS maxid FROM bbids_order";
$maxorderRes = mysql_query($maxorderSql);
while($data = mysql_fetch_assoc($maxorderRes))
{
	$maxid = $data['maxid'];
}

$nowmaxid = $maxid + 1;

$ordernumber = "BB".$nowmaxid;

$date = date('Y-m-d h:i:s');

$amount = $_POST['GrandTotal'];

$payoption = "Credit Card";

$transactionid = "3478665409834621";

$bankname = "SBT";

$transactionstatus = "successfull";

$author = $uid;

$insertOrderSql = "INSERT INTO bbids_order(ordernumber, created, updated, amount, payoption, transactionid, bankname, transactionstatus, author, status) Values ('".$ordernumber."', '".$date."', '".$date."', '".$amount."', '".$payoption."', '".$transactionid."', '".$bankname."', '".$transactionstatus."', '".$author."', 1)";
mysql_query($inserOrderSql);

$orderid = mysql_insert_id();



for($i=0; $i<$count; $i++){
	$catid = $_POST['categories'][$i];
	$plan = $_POST['category'.$catid];

	if($plan == 1)
		$leadpack = 25;
	if($plan == 2)
		$leadpack = 50;
	if($plan == 3)
		$leadpack = 100;
	if($plan == 4)
		$leadpack = 250;

	$getSubcategoriesSql = "SELECT * FROM bbids_categories WHERE parentid = $catid";
	$subcategoriesRes = mysql_query($getSubcategoriesSql);
	while($data = mysql_fetch_assoc($subcategoriesRes)){
		$subcatid = $data['id'];
		$updateQuery = "UPDATE bbids_vendorsubcategoriesrel set status = 1 WHERE vendorid = $uid AND subcategoryid = $subcatid";
		mysql_query($updateQuery);

	}

	$insertLeadpackSql = "Insert into bbids_vendorcategoriesrel(vendorid, categoryid, leadpack, status) VALUES ($uid, $catid, $leadpack, 1)";
	mysql_query($insertLeadpackSql);

	$updateSql = "UPDATE bbids_vendorcategoriesrel SET leadpack = $leadpack, status = 1 WHERE vendorid = $uid AND categoryid = $catid";
	mysql_query($updateSql);

	$insertProductSql = "INSERT INTO bbids_orderproductsrel (ordernumber, categoryid, leadpack) VALUES ($orderid, $catid, $leadpack)";
	mysql_query($insertProductsSql);

}

header('Location: http://192.168.1.10/~anup/bizbids/web/app_dev.php/vendor/home');

//http_redirect('192.168.1.10/~anup/bizbids/web/app_dev.php/home');
?>

