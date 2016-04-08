<?php
// --- DATABASE CONNECT
$username="pure360";
$password="Pure360";
$database="pure360";
$server="192.168.5.2";
$site_is_online=0;
$sql_port="3306";


$connection = mysqli_connect($server,$username,$password,$database,$sql_port);

if(!isset($no_out_put))
	{
		echo 'Connecting to database ... ';
		// Check connection

		if(mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

		else
			{
				echo "DONE \n";
				$connected_to=$server;
			}

	}

?>