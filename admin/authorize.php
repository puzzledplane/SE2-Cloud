<?php

	/***********************************************************************************************************************
	*														authorize.php												   *
	************************************************************************************************************************
	*	This file contains code to display the admins authorized accounts and to manage these authorized accounts. Actions *
	*	include deleting authorized users.		                                                               			   *
	************************************************************************************************************************/
	
	session_start();
	include('../config.php');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_Admin_Username']) || empty($_SESSION['SE_Admin_ID']))
	{		
		$_SESSION['SE_Admin_Error'] = "You must sign in first.";
		header("location: login.php");
	}
	else
	{
	
		if(!isset($_GET['action'])) 
		{	
			$_GET['action'] = "";
		}
		
		if(!isset($_GET['task'])) 
		{	
			$_GET['task'] = "";
		}
		
		//Attempt to connect to mysql server. Otherwise die with an error.
		if (mysqli_connect_errno())
		{
			die('Could not connect: ' . mysqli_connect_error());
		};
		
		echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>Authorize Account</title>
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
											<!-- Begin Account Form -->';
											/* Delete Authorization Access for particular Admin. */
											if ($_GET['action'] == 'delete')
											{
												//POST Vars
												$ID = mysqli_real_escape_string($con, $_POST['delete']);
												$myaccesscode = mysqli_real_escape_string($con, $_SESSION['SE_Admin_AccessCode']);
													
													// SQL Query.
													$sql = "DELETE FROM authorization WHERE AccessCode = '$myaccesscode' AND ID = '$ID'";
													
													//Execute Query.
													if (mysqli_query($con, $sql))
													{
															echo '<div id="DeleteAuthorization" class="module">
																	<div class="frameheader"><h2>Authorized User Deleted</h2></div>
																	<ul>
																		<li class="box">
																			<p>Authorized user was deleted successfully.
																			<br />
																			<br />
																			<a href="authorize.php">Click Here</a> to go back to Authorized Users.</p>
																		</li>
																	</ul>
															</div>';
													}
													//Display Mysql Error.
													else
													{
														die('Error deleting record: ' . mysqli_error($con));
													}
											}
											// View Authorized Accounts
											else
											{
												$index = 0;
												
												//Title Array
												$titles_array = array("Username:", "Full Name:", "Email:", "Gender:", "Date of Birth:", "Height:", "Weight:", "Location:");
												//Database Column Names
												$data_array = array("Username", "Name", "Email", "Gender", "DOB", "Height", "Weight", "Location");
												
												$myaccesscode = $_SESSION['SE_Admin_AccessCode'];
												
												//Sql Query.
												$sql = "SELECT * FROM authorization WHERE AccessCode = '$myaccesscode'";
												
												//Execute Mysql Query
												if($result = mysqli_query($con, $sql))
												{
														echo' 	<div id="AuthorizedUsers" class="module">
														<div class="frameheader"><h2>User Authorizations</h2></div>
															<ul>
																<li class="box">
																	<h2>User Authorizations</h2>
																	<p>These users have authorized me to view their information.</p>
																	<br />
																	<ul>';
																	
																	// Fetch each row as an array
																	while ($row = mysqli_fetch_array($result))
																	{
																		
																		//Active ID- Current userid in database.
																		$activeid = $row['users_UserID'];
																		//Current authorization id
																		$accessid = $row['ID'];
																		
																		echo '
																			<li class="box">
																				<div class="displayProvider">';
																		
																		//Second Sql Query.
																		$sql1 = "SELECT * FROM users WHERE ID = '$activeid'";
																		
																		//Execute Second 
																		if($result2 = mysqli_query($con, $sql1))
																		{
																			//Display Provider information.
																			while ($row2 = mysqli_fetch_array($result2))
																			{
																					echo'<table class="myProvider">';
																					
																					foreach ($data_array as $val)
																					{
																						echo '	<tr>';
																						
																								// Check for last row of table (Headers) for styling reasons.
																								if (sizeof($data_array) == ($index + 1))
																								{
																									echo '<td class ="lastProviderHeader">'.$titles_array[$index].'</td>';
																								}
																								else
																								{
																									echo '<td class ="providerHeader">'.$titles_array[$index].'</td>';
																								}
																								
																								// Also Check for DOB field to add age using php date_diff.
																								if ($val == 'DOB')
																								{
																									echo '<td class ="accountData">'.$row2[$val].'</td> 
																											</tr>
																											<tr>
																												<td class ="accountHeader">Age:</td> 
																												<td class ="accountData">'.date_diff(date_create($row2[$val]), date_create('today'))->y.'</td>
																											</tr>';
																								}
																								// Also Check for Weight field to add lbs at end.
																								elseif ($val == 'Weight')
																								{
																									echo '<td class = "accountData">'.$row2[$val].' lbs</td>';
																									echo'</tr>';
																								}
																								// Check for last row of table (Data) for styling reasons.
																								elseif (sizeof($data_array) == ($index + 1))
																								{
																									echo '<td class = "lastProviderData">'.$row2[$val].'</td>';
																									echo'</tr>';
																								}
																								else
																								{
																									echo '<td class = "providerData">'.$row2[$val].'</td>';
																									echo'</tr>';
																								}
																								
																						
																						//Resets index to account for each row.
																						if (($index + 2) > sizeof($titles_array))
																						{
																							$index = 0;
																						}
																						else
																						{
																						$index++;
																						}
																						
																					}
																					
																					echo '</table>';
																				
																			}
																			
																		}
																		else
																		{
																			die('Error:' . mysqli_error($con));
																		}
																		
																		//Delete ID for deletion of access.
																		$deleteid = $accessid;
																		
																		
																echo'	</div>
																			<div class="providerButtons">
																				<form id="providerDelete" method="post" action="authorize.php?action=delete">
																					<input type="hidden" name="delete" value="'.$deleteid.'">
																					<input type="submit" value="Delete" />
																				</form>
																			</div>
																			</li>';
																			
																	}
																	
														echo '		</ul>
																</li>
															</ul>
														</div>';
												}
												// Mysql Error.
												else
												{
													die('Error:' . mysqli_error($con));
												}
											}
		echo'						</td>';
								include('./includes/sidebar.right.php');
		echo'					</tr>
							</table>
						</div>';
						include('../templates/main/footer.php');
echo'				</body>
				</html>';
	}
mysqli_close($con);
?>