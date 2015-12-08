<?php
	
	/***********************************************************************************************************************
	*														authorize.php												   *
	************************************************************************************************************************
	*	This file contains code to display the users authorized accounts and to manage these authorized accounts. Actions  *
	*	include adding and deleting authorized users.		                                                               *
	************************************************************************************************************************/

	session_start();
	include('./config.php');
	
	//Check if the user is logged in using session variables. Otherwise they are redirected to login.
	if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
	{		
		$_SESSION['SE_User_Error'] = "You must sign in first.";
		header("location: login.php");
	}
	else
	{
		// Set ID from session.
		$id = $_SESSION['SE_User_ID'];
	
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
					<link rel="stylesheet" href="./templates/main/style.css">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
					<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
					<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
					<script>
						function showProvider(str) 
						{
							
							if (str.length < 10 || str.length > 10)
							{
								document.getElementById("addsubmit").disabled = true;
								document.getElementById("verifyDiv").style.visibility = "hidden";
								document.getElementById("showProvider").style.visibility = "visible";
								document.getElementById("showProvider").innerHTML = "Please enter a valid 10 digit access code.";
								return;
							}
							else
							{
								document.getElementById("showProvider").style.visibility = "visible";
								
								var xmlhttp = new XMLHttpRequest();
								xmlhttp.onreadystatechange = function() 
								{
									if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
									{
										document.getElementById("showProvider").innerHTML = xmlhttp.responseText;
									}
								}
								
								xmlhttp.open("GET", "./includes/getprovider.php?code=" + str, true);
								xmlhttp.send();	
								
								if (document.getElementById("showProvider").innerHTML != "Please enter a valid access code.")
								{
									document.getElementById("verifyDiv").style.visibility = "visible";
									document.getElementById("addsubmit").disabled = false;
								}
								
							}
							

						}
					</script>
					<script>
					function hideCB()
					{
						document.getElementById("showProvider").style.visibility = "hidden";
						document.getElementById("verifyDiv").style.visibility = "hidden";
					}
					</script>
				</head>
				
					<body onload="hideCB()">';
						include('./templates/main/header.php');
						include('./includes/navigation.php');
		echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Account Form -->';
											//Add Authorized User
											if ($_GET['action'] == 'add')
											{
												//Submit Add
												if ($_GET['task'] == 'submit')
												{
													//POST var
													$accesscode = mysqli_real_escape_string($con, $_POST['accesscode']);
													
													//Sql Query
													$sql = "SELECT * FROM authorization WHERE users_UserID = '$id' AND AccessCode = '$accesscode'";
												
													//Query DB using query.
													if ($result = mysqli_query($con, $sql))
													{
														//Get number of rows in DB.
														$count = mysqli_num_rows($result);
														
															//Check if provider already has access to account. Display error.
															if ($count != 0)
															{
																$_SESSION['SE_User_Error'] = "Provider already has access to your account.";
																header('Location: authorize.php?action=add');
																exit;
															}
															//Give Provider Access
															else
															{
																//Reset Error Messages.
																$_SESSION['SE_User_Error'] = "";
																
																//Sql Query
																$sql = "INSERT INTO authorization (AccessCode, users_UserID) VALUES ('$accesscode', '$id')";
																
																//Execute Query.
																if (mysqli_query($con, $sql))
																{
																	echo '<div id="AddAuth" class="module">
																				<div class="frameheader"><h2>Account Authorized</h2></div>
																				<ul>
																					<li class="box">
																						<p>Your account was authorized successfully.
																						<br />
																						<br />
																						<a href="authorize.php">Click Here</a> to go back to My Authorizations.</p>
																					</li>
																				</ul>
																		</div>';
																}
																//Display mysql error.
																else
																{
																	die('Error: ' . mysqli_error($con));
																}
															
															}
													}
													// Mysql error
													else
													{
														die('Error: ' . mysqli_error($con));
													}
												}
												//Show Add authorized users form.
												else
												{
													echo' 	<div id="EditAccount" class="module">
																<div class="frameheader"><h2>Add Authorized User</h2></div>
																<ul>
																	<li class="box">
																		<h2>Add Authorized User</h2>';
																			//Display Error if set.
																			if (!empty($_SESSION['SE_User_Error']))
																			{
																				echo '
																				<br />
																				<span class="error">'.$_SESSION['SE_User_Error'].'</span>
																				<br />';
																			}
																		
																	echo'	<p>This form allows you to authorize a health care provider/clinician 
																			to access your personal information. In order for you to authorize a provider,
																			you must have their unique access code. Please contact them before using this
																			form to obtain this access code. Before submitting this form, you will be asked
																			to confirm the authorization and will be shown the providers information if a
																			valid access code is provided.</p>
																			<form method="post" action="authorize.php?action=add&task=submit">
																				<table id="addAuth">
																					<tr>
																						<td>Access Code: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="accesscode" required onkeyup="showProvider(this.value)" autocomplete="off"/>
																					</tr>
																				</table>
																				<div id="showProvider"></div>
																				<br />
																				<br />
																				<div id="verifyDiv"><input type="checkbox" name="cbverify" value="Yes" required/>You verify that the above provider 
																				information is correct and you authorize the above view your records.</div>
																				<br />
																				<br />
																				<input type="submit" id="addsubmit" value="Authorize">
																			</form>
																	</li>
																</ul>
															</div>';
												}
											}
											/* Delete Authorization Access for particular Admin. */
											elseif ($_GET['action'] == 'delete')
											{
												//POST Vars
												$getaccesscode = mysqli_real_escape_string($con, $_POST['delete']);
												$userid = mysqli_real_escape_string($con, $_SESSION['SE_User_ID']);
													
													// SQL Query.
													$sql = "DELETE FROM authorization WHERE AccessCode = '$getaccesscode' AND users_UserID= '$userid'";
													
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
												
												//Title Array to print table headings.
												$titles_array = array("Full Name:", "Email:", "Hospital/Clinic Name:", "Hospital/Clinic ID:", "Location:", "Access Code:");
												//Database Columns array to print.
												$data_array = array("Name", "Email", "HospitalName", "HospitalID", "Location", "AccessCode");
												
												//Sql Query.
												$sql = "SELECT * FROM authorization WHERE users_UserID = '$id'";
												
												//Execute Mysql Query
												if($result = mysqli_query($con, $sql))
												{
														echo' 	<div id="AuthorizedUsers" class="module">
														<div class="frameheader"><h2>Authorized Users</h2></div>
															<ul>
																<li class="box">
																	<h2>Authorized Users:</h2>
																	<br />
																	<br />
																	<ul>';
																	
																	// Fetch each row as an array
																	while ($row = mysqli_fetch_array($result))
																	{
																		
																		//Active Code - Current code in database.
																		$activecode = $row['AccessCode'];
																		
																		echo '
																			<li class="box">
																				<div class="displayProvider">';
																		
																		//Second Sql Query.
																		$sql1 = "SELECT * FROM admins WHERE AccessCode = '$activecode'";
																		
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

																								// Check for last row of table (Data) for styling reasons.
																								if (sizeof($data_array) == ($index + 1))
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
																		
																		//Delete ID for deletion of admin access.
																		$deleteaccesscode = $activecode;
																		
																		
																echo'	</div>
																			<div class="providerButtons">
																				<form id="providerDelete" method="post" action="authorize.php?action=delete">
																					<input type="hidden" name="delete" value="'.$deleteaccesscode.'">
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
						include('./templates/main/footer.php');
echo'				</body>
				</html>';
	}
mysqli_close($con);
?>