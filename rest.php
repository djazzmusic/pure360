<?php
// Initial variables
$ip=$_GET['ip'];
$iplong=ip2long($ip); // change the ip to a number.
if (is_numeric($iplong)) // this would make sure that no XSS attacks or any dirty input will pass and out api is a bit more safe.
	{
		// --- DATABASE CONNECT
		$no_out_put=1; // dont show any output from connect.php
		include('connect.php');
		// ----------------------------- READ FROM DATABASE
		/*
		Since all the ip are in a numeric format, finding the subnet that the ip belongs is a simple maths problem.
		*/
		$result = mysqli_query($connection,"SELECT *  FROM ipgeo USE INDEX (a,b) WHERE `a` <= $iplong AND `b` >= $iplong LIMIT 0,1");

		while($row = mysqli_fetch_array($result))
			{
				//	print_r($row);
				$data[]=$row;
			}


		if(isset($data))
			{
				// create a json output for our API.
				echo json_encode($data);
			}

		else
			{
				// In case that we didn't find a record on the database.
				$error['error']='No record has been found';
				$error['status']='0';
				echo json_encode($error);
			}

	}

else
	{
		echo "Wrong ip entered";
	}

?>