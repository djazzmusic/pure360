<?php
// Initial variables

ini_set('memory_limit', '564M');

// --- DATABASE CONNECT
				$username="pure360";
				$password="Pure360";
				$database="pure360";
		//	$server="192.168.1.9";
				$server="192.168.5.2";
		//		$server="localhost";
				$site_is_online=0;
				$sql_port="3306";
				
					$connection = mysqli_connect($server,$username,$password,$database,$sql_port);
	$ip_rem=$_SERVER["REMOTE_ADDR"] . " " . $_SERVER['HTTP_USER_AGENT'] . " " . $_SERVER['REQUEST_URI'];
	
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



?>