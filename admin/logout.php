<?php

	/***********************************************************************************************************************
	*														logout.php													   *
	************************************************************************************************************************
	*	This file contains code to allow the admin to logout of their account.											   *
	************************************************************************************************************************/
	
session_start();

include('../config.php');

// remove all session variables
session_unset();

// destroy the session
session_destroy(); 

	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>Logged Out</title>
				<link rel="stylesheet" href="../templates/main/style.css">
				</head>
				
					<body>';
						include('../templates/main/header.php');
						include('./includes/navigation.php');
	echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Admin Logout Form -->
										<div id="AdminLogoutFix" class="stylefix">
											<div id="AdminLogout" class="module">
												<div class="frameheader"><h2>Logged Out</h2></div>
													<ul>
														<li class="box">
															<br />
															You\'ve Been Successfully Signed Out!
															<br />
															<br />
															<br />
															<a href="./login.php">Click Here</a> to return to the login.
															<br />
														</li>
													</ul>
											</div>
										</div>
									</td>';
									//include('./includes/sidebar.right.php');
echo'							</tr>
							</table>
						</div>';
						include('../templates/main/footer.php');
echo'				</body>
				</html>';
?>