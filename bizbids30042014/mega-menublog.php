<?php
mysql_connect('localhost','anup','tripleseven7');
mysql_select_db('bizbids_blog');


$sql= "select term.term_id,term.name from wp_terms term,wp_term_taxonomy tax,wp_term_relationships rel,wp_posts post where term.term_id=tax.term_id and rel.object_id=post.ID and tax.taxonomy='category' and post.post_status='publish' group by term.term_id";
$qry = mysql_query($sql);
$noOfrows = mysql_num_rows($qry);
$conbyfour = $noOfrows/4;
$conarr = array(0,$conbyfour,$conbyfour*2,$conbyfour*3);
while($data = mysql_fetch_array($qry))
{
		$resultid[] = $data['term_id'];
		$resultval[] = $data['name'];
}

for($i = 0;$i<count($conarr);$i++){
	$output = '<div class="column"><ul>';
	if($i==count($conarr)-1)
		$max = $noOfrows;
	else
		$max = $conarr[$i+1];
		
			for($j=$conarr[$i]+1;$j<=$max;$j++){
				$output .=	"<li><a href=\"http://".$_SERVER[HTTP_HOST]."/~anup/resource_centre/?cat=".$resultid[$j]."\">".$resultval[$j]."</a></li>";
				
			}
	echo $output .= '</ul></div>';
}
	//$output .=	"<li><a href=\"http://192.168.1.10/~anup/bizbids_resource_centre/?cat=".$data['term_id']."\">".$data['name']."</a></li>";

//$output = '<div class="column"><ul>';

?>
