<?php

	/***********************************************************************************************************************
	*												   dailystepcount.php												   *
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
	else
	{
		
		//Attempt to connect to mysql server. Otherwise die with an error.
		if (!$con) 
		{
		die('Could not connect: ' . mysqli_error($con));
		}
		
		
		$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$_SESSION['SE_User_ID']."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$dailysteps = 0;
				$lastdailyupdate = 0;
			}
			else
			{
				while ($rows = mysqli_fetch_array($result))
				{
					$dailysteps = $rows['Data'];
					$lastdailyupdate = $rows['Date'];
				}
			}
			
			mysqli_free_result($result);
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}		  
	}

?>