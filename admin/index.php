<?php
	
	/***********************************************************************************************************************
	*													index.php													       *
	************************************************************************************************************************
	*	This file contains code to display the dashboard to the admin.													   *
	************************************************************************************************************************/

	session_start();
	include('../config.php');
		
		//Check if the user is logged in using session variables. Otherwise they are redirected to login.
		if(empty($_SESSION['SE_Admin_Username']) || empty($_SESSION['SE_Admin_ID']))
		{		
			$_SESSION['SE_Admin_Error'] = "You must sign in first.";
			header("location: login.php");
		}
		//Display Dashboard
		else
		{
	
	
	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>Admin Dashboard</title>
					<link rel="stylesheet" href="../templates/main/style.css">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
					<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
					<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
				</head>
				
					<body>';
						include('../templates/main/header.php');
						include('./includes/navigation.php');
	echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Registration Form -->
											<div id="Dashboard" class="module">
												<div class="frameheader"><h2>Health Care Provider Dashboard</h2></div>
													<ul>
														<li class="box">
															<h2>Welcome '.$_SESSION['SE_Admin_Name'].',</h2>
														</li>
													</ul>
											</div>
								</td>';
								include('./includes/sidebar.right.php');
echo'							</tr>
							</table>
						</div>';
						include('../templates/main/footer.php');
echo'				</body>
				</html>';
		}
?>