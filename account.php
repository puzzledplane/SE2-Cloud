<?php
	
	/***********************************************************************************************************************
	*														account.php													   *
	************************************************************************************************************************
	*	This file contains code to display the users account information and to manage the users account. Actions include  *
	*	Editing account information and changing a password.                                                               *
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
		
		// Salt Function -- Source: http://tinsology.net/2009/06/creating-a-secure-login-system-the-right-way/
		function createSalt()
		{
			$string = md5(uniqid(rand(), true));
			return substr($string, 0, 3);
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
				<title>My Account</title>
					<link rel="stylesheet" href="./templates/main/style.css">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
					<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
					<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
				</head>
				
					<body>';
						//Include header and navigation files.
						include('./templates/main/header.php');
						include('./includes/navigation.php');
		echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Account Form -->';
											//Edit Account Information
											if ($_GET['action'] == 'edit')
											{
												//Submit Changes from Edit
												if ($_GET['task'] == 'submit')
												{
													//Post Variables
													$current = mysqli_real_escape_string($con, $_POST['password']);
													$name = mysqli_real_escape_string($con, $_POST['name']);
													$email = mysqli_real_escape_string($con, $_POST['email']);
													$gender = mysqli_real_escape_string($con, $_POST['gender']);
													$dob = mysqli_real_escape_string($con, $_POST['dob']);
													$heightft = mysqli_real_escape_string($con, $_POST['heightft']);
													$heightinches = mysqli_real_escape_string($con, $_POST['heightinches']);
													$height = $heightft . " ft " . $heightinches . " in ";
													$weight = mysqli_real_escape_string($con, $_POST['weight']);
													$location = mysqli_real_escape_string($con, $_POST['location']);
													$stepgoal = mysqli_real_escape_string($con, $_POST['stepgoal']);
													
													//SQL query to select all users data for logged in id.
													$sql="SELECT * FROM users WHERE ID='$id'";
													$result=mysqli_query($con, $sql);
													
													while ($row = mysqli_fetch_array($result))
													{
														$currenthash = hash('sha256', $row['Salt'] . hash('sha256', $current));
														
														// Check current hash against password value in db.
														if ($currenthash == $row['Password'])
														{
															$query = "UPDATE users SET Name = '$name', Email='$email', 
															Gender = '$gender', DOB = '$dob', Height = '$height', Weight = '$weight', Location = '$location', StepGoal = '$stepgoal' WHERE ID = '$id'";
															
																//Query DB using query.
																if(mysqli_query($con, $query))
																{
																	$_SESSION['SE_User_Error'] = "";
																	$_SESSION['SE_User_Name'] = $name;
																	
																	echo '<div id="EditAccount" class="module">
																				<div class="frameheader"><h2>Account Edited</h2></div>
																				<ul>
																					<li class="box">
																						<p>Your Account was edited successfully.
																						<br />
																						<br />
																						<a href="account.php">Click Here</a> to go back to My Account.</p>
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
														//current hash does not match does not match password. Redirect.
														else
														{
															$_SESSION['SE_User_Error'] = "Incorrect Current Password. Please try again";
															header('Location: account.php?action=edit');
															exit;
														}
													}
												}
												//Display Edit Account form.
												else
												{
													
													echo' 	<div id="EditAccount" class="module">
																<div class="frameheader"><h2>Edit Account</h2></div>
																<ul>
																	<li class="box">
																		<h2>Edit My Account</h2>
																			<br />';
																			//Display error if one is set.
																			if(!empty($_SESSION['SE_User_Error']))
																			{
																				echo '<span class="error">'.$_SESSION['SE_User_Error'].'</span>
																				  <br />
																				  <br />';
																			}
																			
																			//DB query
																			$query = "SELECT * FROM users WHERE ID = '$id'";
																			
																			//Query DB using query.
																			if ($result = mysqli_query($con, $query))
																			{
																				//Fetch Data and parse into array.
																				$row = mysqli_fetch_array($result);
																				//Free result
																				mysqli_free_result($result);
																			}
																			//Display Error
																			else
																			{
																				die('Error:' . mysqli_error($con));
																			}
													
																	echo'	<form method="post" action="account.php?action=edit&task=submit">
																				<table id="editAccount">
																					<tr>
																						<td>Username: <span class="required">*</span></td>
																						<td>'.$row['Username'].'</td>
																					</tr>
																					<tr>
																						<td>Current Password: <span class="required">*</span></td>
																						<td><input class="styled" type="password" name="password" required></td>
																					</tr>
																					<tr>
																						<td>Full Name: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="name" value="'.$row['Name'].'" required></td>
																					</tr>
																					<tr>
																						<td>Email: <span class="required">*</span></td>
																						<td><input class="styled" type="email" name="email" value="'.$row['Email'].'" required></td>
																					</tr>
																					<tr>
																						<td>Gender: <span class="required">*</span></td>
																						<td>
																							<select class="styled" name="gender" required>';
																								
																								// Get Selected value from DB
																								if ($row['Gender'] == 'Male')
																								{
																									echo '<option value="Male" selected>Male</option>';
																								}
																								else
																								{
																									echo '<option value="Male">Male</option>';
																								}
																								
																								if ($row['Gender'] == 'Female')
																								{
																									echo '<option value="Female" selected>Female</option>';
																								}
																								else
																								{
																									echo '<option value="Female">Female</option>';
																								}
																				echo'		</select>
																						</td>
																					</tr>
																					<tr>
																						<td>Date of Birth 
																						<br />
																						(MM/DD/YYYY): <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="dob" value="'.$row['DOB'].'" required pattern="^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d"></td>
																					</tr>';
																					
																					//Get value in feet from height value using explode function.
																					$heightfeet = explode(' ft ', $row['Height']);
																					
																			echo'	<tr>
																						<td>Height: <span class="required">*</span></td>
																						<td><input class="height" type="text" name="heightft" value="'.$heightfeet[0].'" required> feet <input class="height" type="text" name="heightinches" value="" required> inches</td>
																					</tr>
																					<tr>
																						<td>Weight: <span class="required">*</span></td>
																						<td><input class="height" type="text" name="weight" value="'.$row['Weight'].'" required> lbs</td>
																					</tr>
																					<tr>
																						<td>Location (City, State): <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="location" value="'.$row['Location'].'" required></td>
																					</tr>
																					<tr>
																						<td>Daily Step Goal: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="stepgoal" value="'.$row['StepGoal'].'" required></td>
																					</tr>
																				</table>
																				<br />
																				<br />
																				<input type="submit" value="Submit">
																		</form>
																	</li>
																</ul>
															</div>';
												}
											}
											//Change Password Action
											elseif ($_GET['action'] == 'changepassword')
											{
												//Form is submitted.
												if ($_GET['task'] == 'submit')
												{
													//DB Query
													$sql="SELECT * FROM users WHERE ID='$id'";
													$result=mysqli_query($con, $sql);
													
													//POST Vars
													$current = mysqli_real_escape_string($con, $_POST['current']);
													$password = mysqli_real_escape_string($con, $_POST['password']);
													$confirm = mysqli_real_escape_string($con, $_POST['confirm']);
													
													while ($row = mysqli_fetch_array($result))
													{
														$currenthash = hash('sha256', $row['Salt'] . hash('sha256', $current));
														
														//Hash value and check current password.
														if ($currenthash == $row['Password'])
														{
															//Check password and confirm password match.
															if ($password == $confirm)
															{
																//Hash password using sha256.
																$hash = hash('sha256', $password);
																//Generate Salt Value.
																$salt = createSalt();
																//Hash Salt and Hash values.
																$hash = hash('sha256', $salt . $hash);
																
																// Update password and salt in DB
																$query = "UPDATE users SET Password='$hash', Salt='$salt' WHERE ID = '$id'";
																
																//Success.
																if(mysqli_query($con, $query))
																{
																	$_SESSION['SE_User_Error'] = "";
																	
																	echo '<div id="EditAccount" class="module">
																				<div class="frameheader"><h2>Password Changed</h2></div>
																				<ul>
																					<li class="box">
																						<p>Your Password was changed successfully.
																						<br />
																						<br />
																						<a href="account.php">Click Here</a> to go back to My Account.</p>
																					</li>
																				</ul>
																		</div>';
																}
																// Mysql Error
																else
																{
																	die('Error: ' . mysqli_error($con));
																}
															}
															// Confirm password and password fields do not match.
															else
															{
																$_SESSION['SE_User_Error'] = "New Password and Confirmation do not match. Please try again";
																header('Location: account.php?action=changepassword');
																exit;
															}
															
														}
														//Incorrect Current Password.
														else
														{
															$_SESSION['SE_User_Error'] = "Incorrect Current Password. Please try again";
															header('Location: account.php?action=changepassword');
															exit;
														}
													}
												}
												// Get Change Password Form
												else
												{
													echo '<div id="EditAccount" class="module">
																<div class="frameheader"><h2>Change Password</h2></div>
																<ul>
																	<li class="box">
																		<h2>Change Password</h2>
																		<br />';
																		// Display Error if session is set.
																		if(!empty($_SESSION['SE_User_Error']))
																		{
																			echo '<span class="error">'.$_SESSION['SE_User_Error'].'</span>
																				  <br />
																				  <br />';
																		}
																	echo'<form method="post" action="account.php?action=changepassword&task=submit">
																				<table id="changePassword">
																					<tr>
																						<td>Current Password: <span class="required">*</span></td>
																						<td><input class="styled" type="password" name="current" required></td>
																					</tr>
																					<tr>
																						<td>New Password: <span class="required">*</span></td>
																						<td><input class="styled" type="password" name="password" required></td>
																					</tr>
																					<tr>
																						<td>Confirm New Password: <span class="required">*</span></td>
																						<td><input class="styled" type="password" name="confirm" required></td>
																					</tr>
																				</table>
																				<br />
																				<br />
																				<input type="submit" value="Submit">
																		</form>
																	</li>
																</ul>
															</div>';
												}
											}
											elseif ($_GET['action'] == 'goals')
											{
												//Form is submitted.
												if ($_GET['task'] == 'submit')
												{
													
													//POST Vars
													$stepgoal = mysqli_real_escape_string($con, $_POST['stepgoal']);
																
														// Update goals in DB
														$query = "UPDATE users SET StepGoal = '$stepgoal' WHERE ID = '$id'";
														
														//Success.
														if(mysqli_query($con, $query))
														{
															$_SESSION['SE_User_Error'] = "";
															
															echo '<div id="GoalsEdited" class="module">
																		<div class="frameheader"><h2>Goals Updated</h2></div>
																		<ul>
																			<li class="box">
																				<p>Your goals were updated successfully.
																				<br />
																				<br />
																				<a href="account.php">Click Here</a> to go back to My Account.</p>
																			</li>
																		</ul>
																</div>';
														}
														// Mysql Error
														else
														{
															die('Error: ' . mysqli_error($con));
														}
												}
												// Get Set Goals Form
												else
												{
													
													//DB query
													$query = "SELECT StepGoal FROM users WHERE ID = '$id'";
													
													//Query DB using query.
													if ($result = mysqli_query($con, $query))
													{
														//Fetch Data and parse into array.
														$row = mysqli_fetch_array($result);
														//Free result
														mysqli_free_result($result);
													}
													//Display Error
													else
													{
														die('Error:' . mysqli_error($con));
													}
													
													echo '<div id="EditGoals" class="module">
																<div class="frameheader"><h2>Set Goals</h2></div>
																<ul>
																	<li class="box">
																		<h2>Set Goals</h2>
																		<br />';
																	echo'<form method="post" action="account.php?action=goals&task=submit">
																				<table id="ChangeGoals">
																					<tr>
																						<td>Daily Step Goal: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="stepgoal" value="'.$row['StepGoal'].'" required></td>
																					</tr>
																				</table>
																				<br />
																				<br />
																				<input type="submit" value="Submit">
																		</form>
																	</li>
																</ul>
															</div>';
												}
											}
											else
											{
												$index = 0;
												
												//Title Array
												$titles_array = array("Username:", "Full Name:", "Email:", "Gender:", "Date of Birth:", "Height:", "Weight:", "Location:", "Daily Step Goal:");
												//Database Column Names
												$data_array = array("Username", "Name", "Email", "Gender", "DOB", "Height", "Weight", "Location", "StepGoal");
												
												//DB Query
												$sql="SELECT * FROM users WHERE ID = '$id'";
												
												//Execute Query.
												if($result = mysqli_query($con, $sql))
												{
													echo' 	<div id="Account" class="module">
														<div class="frameheader"><h2>My Account</h2></div>
															<ul>
																<li class="box">
																	<h2>My Account Information:</h2>
																	<table id="myAccount">';
																		// Loop to fetch each row of data
																		while($row = mysqli_fetch_array($result))
																		{
																			//Display Data in $data_array using foreach.
																			foreach ($data_array as $val)
																			{
																				echo '	<tr>';
																				
																						// Check for last row of table (Headers) for styling reasons.
																						if (sizeof($data_array) == ($index + 1))
																						{
																							echo '<td class ="lastAccountHeader">'.$titles_array[$index].'</td>';
																						}
																						else
																						{
																							echo '<td class ="accountHeader">'.$titles_array[$index].'</td>';
																						}
																						
																						
																						// Also Check for DOB field to add age using php date_diff.
																						if ($val == 'DOB')
																						{
																							echo '<td class ="accountData">'.$row[$val].'</td> 
																									</tr>
																									<tr>
																										<td class ="accountHeader">Age:</td> 
																										<td class ="accountData">'.date_diff(date_create($row[$val]), date_create('today'))->y.'</td>
																									</tr>';
																						}
																						// Also Check for Weight field to add lbs at end.
																						elseif ($val == 'Weight')
																						{
																							echo '<td class = "accountData">'.$row[$val].' lbs</td>';
																							echo'</tr>';
																						}
																						// Check for last row of table (Data) for styling reasons.
																						elseif (sizeof($data_array) == ($index + 1))
																						{
																							echo '<td class = "lastAccountData">'.$row[$val].'</td>';
																							echo'</tr>';
																						}
																						else
																						{
																							echo '<td class = "accountData">'.$row[$val].'</td>';
																							echo'</tr>';
																						}
																							
																				$index++;
																			}
																		}
													echo'			</table>
																	<div class="buttonRow">
																		<a href="authorize.php?action=add"><button class="styled">Authorize User</button></a> <a href="device.php?action=add"><button class="styled">Register Device</button></a><a href="account.php?action=edit"><button class="styled">Edit Account</button></a> <a href="account.php?action=changepassword"><button class="styled">Change Password</button></a>
																	</div>
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