<?php
// ============================= INIT VARIABLES
ini_set('memory_limit', '564M'); // This gives more than enough of the nessesary memory to run the import.
$setup['fileToSaveDatabase']='GeoIPCountryCSV.zip'; // Location to save the zipped file
$setup['fileInsideTheZip']='GeoIPCountryWhois.csv'; // Location to export the cvss
$allRecordsFromDb=0; // init this counter


// ============================= CONNECT TO THE DATABASE
include('connect.php'); // connects to mysql server
// check if we have data on the database
$result = mysqli_query($connection,"SELECT COUNT(*)  FROM ipgeo");


while($row = mysqli_fetch_array($result))
	{
		$allRecordsFromDb=$row[0]; // stores the number of records found on the database.
	}


if($allRecordsFromDb>0)
	{
		echo "The data is already on the database \n";
	}

else
	{
		echo "No data has been found on the database \n";
		// ============================= DOWNLOAD THE DATABASE
		echo "Deleting all records from the database before adding data from the cvs\n";
		$result = mysqli_query($connection,"TRUNCATE ipgeo"); // clears the ipgeo table
		echo "Downloading, and unziping the file ... \n";
		$appErrors=downloadAndUnzipTheFile($setup['fileToSaveDatabase'],$setup['fileInsideTheZip']); // checks if the file has been downloaded and if not downloads and unzip the file
		// ============================= RAD DATA AND INSERT TO THE DATABASE
		// ------------ Read infromation from the CVS file
		$file = fopen($setup['fileInsideTheZip'],"r"); // read the information of the file

		while(! feof($file))
			{
				$data[]=fgetcsv($file); // parse data from cvs to a data array
			}

		//print_r($data);
		// -------------- Prepare 1 query to add the information
		/*
		Please NOTE, the data might contain characters like ' that would result to errors on the query so we use addslashes.
		*/
		$countRecords=0; // this will count how many lines of records did we found on the CVS and we will use it to verify that we added all the data to the database.
		foreach ($data as $entry)
			{

				if($countRecords>0) // This will help add a commma to the query
					{
						$q.=',';
					}

				$q.= "('','" . $entry[0] . "','" . $entry[1] . "','" . $entry[2] . "','" . $entry[3] . "','" . $entry[4] . "','" . addslashes($entry[5]) . "')";
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

	}

// END the if there is data on the database
//  =============================  FUNCTIONS

function downloadAndUnzipTheFile($fileToSaveDatabase,$fileInsideTheZip)
	{
		/*
		$fileToSaveDatabase is the zip file
		$fileInsideTheZip is the cvs file
		*/
		if (file_exists($fileInsideTheZip)) // check if theres a cvs file
			{
				echo "The file has already been downloaded and unziped\n";
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

	}


function downloadOnlineDatabase($fileToSave)
	{
		// Download the zip file and save it with $fileToSave file name.
		file_put_contents($fileToSave, file_get_contents("http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip"));
	}


function unZipFile($file)
	{
		// ---------------- PLEASE NOTE ------------
		/* if the ZipArchive is not installed you can switch to a more traditional method like
		system('unzip file.zip');
		*/
		// get the absolute path to $file
		// dear pure360, here i make some comments to explain a simple oop example
		$path = pathinfo(realpath($file), PATHINFO_DIRNAME);
		$zip = new ZipArchive; // construct new object
		$res = $zip->open($file); // with mothod open, open that file and 'load' its data to object $zip
		if ($res === TRUE)
			{
				// extract it to the path we determined above
				$zip->extractTo($path); // object is using the method extractTo to extruct the file to that path.
				$zip->close();
				echo "$file extracted to $path ";
			}

		else
			{
				echo "I couldn't open $file ";
			}

	}

?>