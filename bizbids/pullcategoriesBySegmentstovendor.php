<?php
include_once('externconfig.php');

$segid = $_REQUEST['id'];
$mode = $_REQUEST['mode'];
if($mode=='selectcat'){
	$eleid = "form_category_";
	$elename = "form[category][]";
	$sql = "select * from bbids_categories where segmentid=".$segid;
}else {
	$eleid = "form_subcategory_";
	$elename = "form[subcategory][]";
	$sql = "select * from bbids_categories where parentid=".$segid;
}
$res = mysql_query($sql);
$output = '';
$i=0;
while($data = mysql_fetch_array($res))
{
		$output .= '<div id="div'.$eleid.$data['id'].'" class="field-item">';

		$output .=	'<input type="checkbox" id="'.$eleid.$data['id'].'" name ="'.$elename.'"  ';
		if($mode=='selectcat'){
		$output .= 'onclick="secondlevel(this,\''.$data['category'].'\', \''.$data['price'].'\');"';
		}
		if($mode!='selectcat'){
			$output .= 'checked="checked" onclick="return false;"';
		}
		$output .= 'value="'.$data['id'].'">';
		$output .= '<label for="'.$eleid.$data['id'].'">'.$data['category'].'</label></div>';
		$i++;
}
echo $output;
?>
