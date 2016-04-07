<?php
// Initial variables

ini_set('memory_limit', '564M');

// --- DATABASE CONNECT
$no_out_put=1;
include('connect.php');

$ip=$_GET['ip'];

$iplong=ip2long($ip);	


// ----------------------------- READ FROM DATABASE

//echo "$ipfirst-$iplast";


$result = mysqli_query($connection,"SELECT *  FROM ipgeo WHERE `a` <= $iplong AND `b` >= $iplong LIMIT 0,10");

 
  
while($row = mysqli_fetch_array($result))
{
	
//	print_r($row);
	$data[]=$row;
}

echo json_encode($data);


?>