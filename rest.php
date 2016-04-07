<?php
// Initial variables

ini_set('memory_limit', '564M');

// --- DATABASE CONNECT
include('connect.php');

$ip=$_GET['ip'];

$ipbits=explode('.',$ip);
$ipfirst=$ipbits[0] . '.' . $ipbits[1] . '.' . $ipbits[2] ;
$iplast=$ipbits[3];	


// ----------------------------- READ FROM DATABASE

//echo "$ipfirst-$iplast";


$result = mysqli_query($connection,"SELECT *  FROM ipgeo WHERE `ip1` LIKE '" . $ipfirst . "' AND `ip2` <= $iplast AND `ip3` >= $iplast LIMIT 0,10");

 
  
while($row = mysqli_fetch_array($result))
{
	
//	print_r($row);
	$data[]=$row;
}

echo json_encode($data);


?>