<?php

	/***********************************************************************************************************************
	*												 getGraphData.php												       *
	************************************************************************************************************************
	*	This include file contains functions to get graph data from the sensors table. This page should not be			   *
	*	accessed directly and instead is utilized using includes by the index.php page.                         		   *
	************************************************************************************************************************/
	
	date_default_timezone_set('America/New_York');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
	{		
		$_SESSION['SE_User_Error'] = "You must sign in first.";
		header("location: ./login.php");
		die("Forbidden");
	}
	
	function getGraphData($users_UserID, $type, $start_date, $end_date)
	{
		// Global variables provided by config.php for DB connection.
		global $con, $host, $user, $password, $db;
		
		if (mysqli_connect_errno())
		{
			die('Could not connect: ' . mysqli_connect_error());
		};
		
		//Initialize index variable.
		$index = 0;
		
		$start_date = new DateTime($start_date);
		$end_date = new DateTime($end_date);
		
		while ($start_date <= $end_date)
		{
		
			$query = "SELECT * FROM sensors WHERE users_UserID = '".$users_UserID."' AND Type = '".$type."' AND Date >= '".$start_date->format("Y-m-d").' 00:00:00'."' AND Date <= '".$start_date->format("Y-m-d").' 23:59:59'."'";
			
			if($result = mysqli_query($con, $query))
			{
				
				$rowcount=mysqli_num_rows($result);
			
				if ($rowcount == 0)
				{
					// No Data for this date -- So fill with 0.
					$graphdata[$index] = 0;
				}
				else
				{
					while ($rows = mysqli_fetch_array($result))
					{
						// Insert Data into array.
						$graphdata[$index] = $rows['Data'];	
						
					}
				}
				
				mysqli_free_result($result);
			}
			//Die with MySQL Error.
			else
			{
				die('Error:' . mysqli_error($con));
			}
			
			//Increment Start Date
			$start_date->modify('+1 day');
			
			//Increment index variable.
			$index++;
			
		}
		
		return $graphdata;
	}
?>