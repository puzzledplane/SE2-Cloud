<?php

	/***********************************************************************************************************************
	*												   getSensorData.php												   *
	************************************************************************************************************************
	*	This include file contains code to get step count data on a daily basis. This file cannot be					   *
	*	accessed directly and instead is utilized using includes by the index.php page.                         		   *
	************************************************************************************************************************/
	
	date_default_timezone_set('America/New_York');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
	{		
		$_SESSION['SE_User_Error'] = "You must sign in first.";
		header("location: ../../login.php");
	}
	
	function getSensorData ($users_UserID, $type, $frequency)
	{
		
		$sensordata = 0;
		
		// Global variables provided by config.php for DB connection.
		global $con, $host, $user, $password, $db;
		
		//Attempt to connect to mysql server. Otherwise die with an error.
		if (!$con) 
		{
		die('Could not connect: ' . mysqli_error($con));
		}
				if ($frequency == "Daily")
				{
					$query = "SELECT * FROM sensors WHERE Type = '".$type."' AND users_UserID = '".$users_UserID."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
				}
				else if ($frequency == "Weekly")
				{
					$query = "SELECT * FROM sensors WHERE Type = '".$type."' AND users_UserID = '".$users_UserID."' AND YEARWEEK(Date) = YEARWEEK(NOW()) ORDER BY Date ASC";
				}
				else if ($frequency == "Monthly")
				{
					$query = "SELECT * FROM sensors WHERE Type = '".$type."' AND users_UserID = '".$users_UserID."' AND MONTH(Date) = MONTH(NOW()) AND YEAR(Date) = YEAR(NOW()) ORDER BY Date ASC";
				}
			
			
				if($result = mysqli_query($con, $query))
				{
					$rowcount=mysqli_num_rows($result);
					
					if ($rowcount == 0)
					{
						$sensordata = 0;
						$lastupdate = 0;
						$size = 0;
					}
					else
					{
						while ($rows = mysqli_fetch_array($result))
						{
							$sensordata += $rows['Data'];
							$lastupdate = $rows['Date'];
						}
						
						$size = $rowcount;
					}
					
					mysqli_free_result($result);
					
					//Array for final result.
					$finalsensordata = array($sensordata, $lastupdate, $size);
					
					return $finalsensordata;
					
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}		  
	}

?>