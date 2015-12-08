<?php

	/***********************************************************************************************************************
	*													getprovider.php													   *
	************************************************************************************************************************
	*	This include file contains code to get provider information based on a user entered accesscode. This file cannot be*
	*	accessed directly and instead is utilized using AJAX on the authorize.php?action=add page.                         *
	************************************************************************************************************************/

	session_start();
	
	include('../config.php');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
	{		
		$_SESSION['SE_User_Error'] = "You must sign in first.";
		header("location: ../login.php");
	}
	else
	{
		//Check if code is empty. If it is not empty get code from POST request.
		if (!empty($_REQUEST["code"]))
		{
			$accesscode = mysqli_real_escape_string($con, $_REQUEST["code"]);
		}
		// 
		else
		{
			$accesscode = "";
		}
		
		//Attempt to connect to mysql server. Otherwise die with an error.
		if (!$con) 
		{
		die('Could not connect: ' . mysqli_error($con));
		}
		
		# Query Admins Database where AccessCode is equal to 
		# GET from external page.
		
		$query = "SELECT * FROM admins WHERE AccessCode = '$accesscode'";
		$result = mysqli_query($con, $query);
		
		// Get number of rows in table.
		$count = mysqli_num_rows($result);
		
		//If a row exists for that access code display providers information.
		if ($count == 1)
		{
			//Initiate Index.
			$index = 0;
			
			//Table Headers.
			$titles_array = array("Full Name:", "Email:", "Hospital/Clinic Name:", "Hospital/Clinic ID:", "Location:", "Access Code:");
			//
			$data_array = array("Name", "Email", "HospitalName", "HospitalID", "Location", "AccessCode");
			
			
			echo '<h3>Providers Information:</h3>
					<br />
					<table class="myProvider">';
					while($row = mysqli_fetch_array($result))
					{
						foreach ($data_array as $val)
						{
							echo '<tr>';
							
							// Check for last row of table (Headers) for styling reasons.
							if (sizeof($data_array) == ($index + 1))
							{
								echo '<td class ="lastProviderHeader">'.$titles_array[$index].'</td>';
							}
							else
							{
								echo '<td class ="providerHeader">'.$titles_array[$index].'</td>';
							}
							// Check for last row of data for styling reasons.
							if (sizeof($data_array) == ($index + 1))
							{
								echo '<td class = "lastProviderData">'.$row[$val].'</td>';
								echo'</tr>';
							}
							else
							{
								echo '<td class = "providerData">'.$row[$val].'</td>';
								echo'</tr>';
							}
							
							$index++;
						}
					}
		}
		//Invalid Access Code Entered.
		else
		{
			echo 'Please enter a valid access code.';
		}
			  
	}

//Closes Mysql Connection.
mysqli_close($con);

?>