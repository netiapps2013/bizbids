<?php 
include_once('externconfig.php');
$sql = "select category from bbids_categories where parentid=0";
$qry = mysql_query($sql);
$noOfrows = mysql_num_rows($qry);
$conbyfour = $noOfrows/4;
$conarr = array(0,$conbyfour,$conbyfour*2,$conbyfour*3);
while($data = mysql_fetch_array($qry))
{
		
		$resultval[] = $data['category'];
}

for($i = 0;$i<count($conarr);$i++){
	$output = '<div class="column"><ul>';
	if($i==count($conarr)-1)
		$max = $noOfrows;
	else
		$max = $conarr[$i+1];
		
			for($j=$conarr[$i]+1;$j<=$max;$j++){
				$output .=	"<li><a href=\"http://".$_SERVER[HTTP_HOST]."/~anup/bizbids/web/app_dev.php/search?category=".trim($resultval[$j])."\">".$resultval[$j]."</a></li>";
				
			}
	echo $output .= '</ul></div>';
}


?>
