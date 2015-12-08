<?php

	/***********************************************************************************************************************
	*												   getGoals.php												   *
	************************************************************************************************************************
	*	This include file contains functions to get goals from a particular user.					                       *
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
	
	function getStepGoal($users_UserID)
	{
		global $con, $host, $user, $password, $db;
		
		if (mysqli_connect_errno())
		{
			die('Could not connect: ' . mysqli_connect_error());
		};
		
		
		$query = "SELECT StepGoal FROM users WHERE ID = '".$users_UserID."' LIMIT 1";
		
		if($result = mysqli_query($con, $query))
		{

			while ($rows = mysqli_fetch_array($result))
			{
				$dailystepgoal = $rows['StepGoal'];
			}
			
			mysqli_free_result($result);
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}

		return $dailystepgoal;
			
	}
	
	

?>