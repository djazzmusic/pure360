<?php
// ============================= INIT VARIABLES

$setup['fileToSaveDatabase']='GeoIPCountryCSV.zip';
$setup['fileInsideTheZip']='GeoIPCountryWhois.csv';
ini_set('memory_limit', '564M');
$allRecordsFromDb=0;


// ============================= CONNECT TO THE DATABASE

include('connect.php');

// check if we have data on the database

$result = mysqli_query($connection,"SELECT COUNT(*)  FROM ipgeo");
 
while($row = mysqli_fetch_array($result))
{
		$allRecordsFromDb=$row[0];
}

if($allRecordsFromDb>0)
{
	echo "The data is already on the database \n";
}
else
{
	echo "No data has been found on the database \n";

// ============================= DOWNLOAD THE DATABASE

echo "Deleting all records from the database before adding\n";
$result = mysqli_query($connection,"TRUNCATE ipgeo");

echo "Downloading, and unziping the file ... \n";

$appErrors=downloadAndUnzipTheFile($setup['fileToSaveDatabase'],$setup['fileInsideTheZip']);


// ============================= RAD DATA AND INSERT TO THE DATABASE

// ------------ Read infromation from the CVS file
$file = fopen($setup['fileInsideTheZip'],"r");

while(! feof($file))
  {
  $data[]=fgetcsv($file);
  }

//print_r($data);


// -------------- Prepare 1 query to add the information

/*
Please NOTE, the data might contain characters like ' that would result to an error to the database.

*/
$countRecords=0; // this will count how many lines of records did we found on the CVS and we will use it to verify that we added all the data to the database.
foreach ($data as $entry)
{

	if($countRecords>0) // This will help add a commma to the query
		{
			$q.=',';	
		}
		
	$q.= "('',INET_ATON('" . trim($entry[0]) . "'),INET_ATON('" . trim($entry[1]) . "'),'" . $entry[2] . "','" . $entry[3] . "','" . $entry[4] . "','" . addslashes($entry[5]) . "')";	
	
	$countRecords++;
	
}

// -------------- Insert the information to the database


// echo $q; // Uncomment if you want to see the query before insertation.

echo "Please wait, inserting data to database ... ";

$query = "INSERT INTO ipgeo VALUES " . $q . "";
			
	if (!mysqli_query($connection,$query))
  {
  echo("Error inserting data to database: " . mysqli_error($connection) . "\n");
  }
  else
  {
	  echo "DONE\n";
	  
  }
		
			
//		mysqli_query($connection,$query);
		
		
		
// ============================= VERIFY THAT DATA GOT ADDED TO THE DATABASE


echo "VERIFICATION Stage ... ";
$result = mysqli_query($connection,"SELECT COUNT(*)  FROM ipgeo");

  
  
  
while($row = mysqli_fetch_array($result))
{
		$allRecordsFromDb=$row[0];
}

if($allRecordsFromDb==$countRecords)
{
	echo "PASSED\n";
}
else
{
		echo "FAILED, please fix the problem\n";
}

} // END the if there is data on the database

//  =============================  FUNCTIONS



function ipFirstBit($ip)
{
	$a=explode('.',$ip);
	$b=$a[0] . '.' . $a[1] . '.' . $a[2] ;
	return $b;
}

function ipLastBit($ip)
{
	$a=explode('.',$ip);
	$b=$a[3];
	return $b;
}




function downloadAndUnzipTheFile($fileToSaveDatabase,$fileInsideTheZip)
{

if (file_exists($fileInsideTheZip))
	{
		echo "the file is already downloaded and unziped\n";
	}

else
	{
		if (file_exists($fileToSaveDatabase))
			{
				echo "The file is already downloaded\n";
			}

		else
			{
				echo "Please wait, im downloading file ... ";
				downloadOnlineDatabase($fileToSaveDatabase);
				if (file_exists($fileToSaveDatabase))
					{
						echo "DONE \n";
						// UNZIPING THE FILE
							echo "Unziping the file ... ";
	
						unZipFile($fileToSaveDatabase);
							
						// Double check that the unziped file is really there
						if (file_exists($fileInsideTheZip))
							{
								echo "DONE \n";
							}
						
						else
							{
								echo " ERROR: the unziped file couldn't be found \n";
							}
	
					}

				else
					{
						echo "\n ERROR: The file failed to download \n";
					}

			}

	}
return($pageErrors);
}



function downloadOnlineDatabase($fileToSave)
	{
		file_put_contents($fileToSave, file_get_contents("http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip"));
	}


function unZipFile($file)
	{
		// ---------------- PLEASE NOTE ------------
		/* if the ZipArchive is not installed you can switch to a more traditional method like
		system('unzip file.zip'); 
		*/
		
		// get the absolute path to $file
		$path = pathinfo(realpath($file), PATHINFO_DIRNAME);
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === TRUE)
			{
				// extract it to the path we determined above
				$zip->extractTo($path);
				$zip->close();
				echo "$file extracted to $path ";
			}

		else
			{
				echo "I couldn't open $file ";
			}

	}

?>