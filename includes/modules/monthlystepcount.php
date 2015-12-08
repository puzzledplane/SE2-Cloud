<?php

	/***********************************************************************************************************************
	*												   monthlystepcount.php												   *
	************************************************************************************************************************
	*	This include file contains code to get step count data on a monthly basis. This file cannot be					   *
	*	accessed directly and instead is utilized using includes by the index.php page.                         		   *
	************************************************************************************************************************/
	
	date_default_timezone_set('America/New_York');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
	{		
		$_SESSION['SE_User_Error'] = "You must sign in first.";
		header("location: ./login.php");
	}
	else
	{
		
		//Initialize Weekly Steps
		$monthlysteps = 0;
		
		//Attempt to connect to mysql server. Otherwise die with an error.
		if (!$con) 
		{
		die('Could not connect: ' . mysqli_error($con));
		}
		
		
		$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$_SESSION['SE_User_ID']."' AND MONTH(Date) = MONTH(NOW()) AND YEAR(Date) = YEAR(NOW()) ORDER BY Date ASC";
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$monthlysteps = 0;
				$lastmonthlyupdate = 0;
			}
			else
			{
				while ($rows = mysqli_fetch_array($result))
				{
					$monthlysteps += $rows['Data'];
					$lastmonthlyupdate = $rows['Date'];
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

//Closes Mysql Connection.
//mysqli_close($con);

?>