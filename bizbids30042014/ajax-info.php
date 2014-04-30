<?php
mysql_connect("localhost","anup","tripleseven7");
mysql_select_db('bizbids');

$key = $_REQUEST['name_startsWith'];
$type = $_REQUEST['type'];
if($type=='category'){
$sel = "select * from bbids_categories where parentid!=111111 and category like '".$key."%'";
$res = mysql_query($sel);

$output = '<datalist id="browsers">';
//$abc = array();
while($parentdata = mysql_fetch_array($res)){
		$output .= '<option value="'.$parentdata['category'].'">'.$parentdata['category'].'</option>';
		//$abc  = array_push($abc, $parentdata['category']); 
		$abc[] = $parentdata['category'];
}

//$abc =array('satish','kubulu','roopali');
$output .= '</datalist>';
echo json_encode($abc);
	
}
//echo $output;

/*
 * 
 * <option value="Internet Explorer"> 
  
  <option value="&emsp;&emsp;Opera">
  <option value="&emsp;&emsp;Safari">

</datalist>
* if(strtolower($val)=="auto"){
		  $opt = "-<input type='checkbox' id='automobile' value='automobile' onclick='chekfun(this)'>automobile<br>";
		  $opt .= "&emsp;-<input type='checkbox' id='geared' value ='Geared' onclick='chekfun(this)'>Geared<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='re' value = 'RE' onclick='chekfun(this)'>RE<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='splendor' value= 'Splendor' onclick='chekfun(this)'>Splendor<br>";
		  $opt .= "&emsp;-<input type='checkbox' id='nongeared' value ='Non Geared' onclick='chekfun(this)'>Non Geared<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='activa' value = 'Activa' onclick='chekfun(this)'>Activa<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='ray' value= 'Ray' onclick='chekfun(this)'>Ray<br>";
		  echo $opt;
}elseif(strtolower($val)=="mobi"){
		$opt = "-<input type='checkbox' id='mobile' value='Mobile' onclick='chekfun(this)'>Mobile<br>";
		  $opt .= "&emsp;-<input type='checkbox' id='general' value ='General' onclick='chekfun(this)'>General Phone<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='nokia1100' value = 'nokia 1100' onclick='chekfun(this)'>Nokia 1100<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='samsungguru' value= 'Samsung guru' onclick='chekfun(this)'>Samsung Guru<br>";
		  $opt .= "&emsp;-<input type='checkbox' id='Smartphone' value ='Smart Phone' onclick='chekfun(this)'>Smart Phone<br>";
		  $opt .= "&emsp;&emsp;-<input type='checkbox' id='samsunggalaxy' value = 'Samsung Galaxy' onclick='chekfun(this)'>Samsung Galaxy<br>";
		echo  $opt .= "&emsp;&emsp;-<input type='checkbox' id='Nokialumia' value= 'Nokia Lumia' onclick='chekfun(this)'>Nokia Lumia<br>";
	
}*/

?>
