<?php
	
	/***********************************************************************************************************************
	*														device.php													   *
	************************************************************************************************************************
	*	This file contains code to display the users authorized devices and to manage the users devices.  Actions include  *
	*	registering devices, editing device information and deleting a device.                                             *
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
				<title>My Devices</title>
					<link rel="stylesheet" href="./templates/main/style.css">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
					<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
					<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
				</head>
				
					<body>';
						include('./templates/main/header.php');
						include('./includes/navigation.php');
		echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Device Form -->';
											//Add Device
											if ($_GET['action'] == 'add')
											{
												//Submit Changes from Add
												if ($_GET['task'] == 'submit')
												{
													//POST Vars
													$type = mysqli_real_escape_string($con, $_POST['type']);
													$make = mysqli_real_escape_string($con, $_POST['make']);
													$model = mysqli_real_escape_string($con, $_POST['model']);
													$serial = mysqli_real_escape_string($con, $_POST['serial']);
													$name = mysqli_real_escape_string($con, $_POST['name']);
													$userid = mysqli_real_escape_string($con, $_POST['userid']);
													
													//SQL Insert Query
													$query = "INSERT INTO devices (Type, Make, Model, Serial, Name, users_UserID) VALUES ('$type', '$make', '$model', '$serial', '$name', '$userid')";
													
													//Execute Query
													if(mysqli_query($con, $query))
													{
														
														echo '<div id="AddDevice" class="module">
																	<div class="frameheader"><h2>Device Added</h2></div>
																	<ul>
																		<li class="box">
																			<p>Device was added successfully.
																			<br />
																			<br />
																			<a href="device.php">Click Here</a> to go back to My Devices.</p>
																		</li>
																	</ul>
															</div>';
													}
													//Mysql Error.
													else
													{
														die('Error: ' . mysqli_error($con));
													}
												}
												//Display Add Device Form.
												else
												{
													
													echo' 	<div id="AddDevice" class="module">
																<div class="frameheader"><h2>Add Device</h2></div>
																<ul>
																	<li class="box">
																		<h2>Device Information:</h2>
																			<br />
																			<form method="post" action="device.php?action=add&task=submit">
																				<table id="addDevice">
																					<tr>
																						<td>Device Type: <span class="required">*</span></td>
																						<td>
																							<select class="styled" name="type" required>
																							<option value="Smart Phone">Smart Phone</option>
																							<option value="Tablet">Tablet</option>
																							<option value="Smart Watch">Smart Watch</option>
																							</select>
																						</td>
																					</tr>
																					<tr>
																						<td>Make: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="make" required></td>
																					</tr>
																					<tr>
																						<td>Model Number: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="model" required></td>
																					</tr>
																					<tr>
																						<td>Serial Number: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="serial" required></td>
																					</tr>
																					<tr>
																						<td>Device Name: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="name" required></td>
																					</tr>
																				</table>
																				<input type="hidden" name="userid" value="'.$id.'">
																				<br />
																				<br />
																				<input type="submit" value="Submit">
																		</form>
																	</li>
																</ul>
															</div>';
												}
											}
											//Edit Device Action
											elseif ($_GET['action'] == 'edit')
											{	
												//Submit Edit Form
												if ($_GET['task'] == 'submit')
												{
													//POST Vars
													$deviceid = mysqli_real_escape_string($con, $_POST['devid']);
													$type = mysqli_real_escape_string($con, $_POST['type']);
													$make = mysqli_real_escape_string($con, $_POST['make']);
													$model = mysqli_real_escape_string($con, $_POST['model']);
													$serial = mysqli_real_escape_string($con, $_POST['serial']);
													$name = mysqli_real_escape_string($con, $_POST['name']);
													
													//SQL Query
													$sql="SELECT * FROM devices WHERE ID='$deviceid'";
													$result=mysqli_query($con, $sql);
													
													//Fetch results in array.
													while ($row = mysqli_fetch_array($result))
													{
														//Update query
														$query = "UPDATE devices SET Type = '$type', Make = '$make', Model = '$model',
																  Serial = '$serial', Name = '$name' WHERE ID = '$deviceid'";
															
															//Execute Query
															if(mysqli_query($con, $query))
																{	
																	echo '<div id="EditDevice" class="module">
																				<div class="frameheader"><h2>Device Edited</h2></div>
																				<ul>
																					<li class="box">
																						<p>Your Device was edited successfully.
																						<br />
																						<br />
																						<a href="device.php">Click Here</a> to go back to My Devices.</p>
																					</li>
																				</ul>
																		</div>';
																}
																//Mysql Error
																else
																{
																	die('Error: ' . mysqli_error($con));
																}
													}
												}
												else
												{
													//POST Var
													$getid = mysqli_real_escape_string($con, $_POST['id']);
													
													// Sql Query to select device data.
													$sql = "SELECT * FROM devices WHERE ID = '$getid'";
													$result=mysqli_query($con, $sql);
													
													//Fetch data in array.
													while ($row = mysqli_fetch_array($result))
													{
														echo'<div id="EditDevice" class="module">
																<div class="frameheader"><h2>Edit Device</h2></div>
																<ul>
																	<li class="box">
																		<h2>Edit Device Information:</h2>
																			<br />
																			<form method="post" action="device.php?action=edit&task=submit">
																				<table id="addDevice">
																					<tr>
																						<td>Device Type: <span class="required">*</span></td>
																						<td>
																							<select class="styled" name="type" required>';
																							
																							//Selection if Type is Smart Phone.
																							if ($row['Type'] == 'Smart Phone')
																							{
																								echo '<option value="Smart Phone" selected>Smart Phone</option>';
																							}
																							else
																							{
																								echo '<option value="Smart Phone">Smart Phone</option>';
																							}
																							//Selection if Type is Tablet.
																							if ($row['Type'] == 'Tablet')
																							{
																								echo '<option value="Tablet" selected>Tablet</option>';
																							}
																							else
																							{
																								echo '<option value="Tablet">Tablet</option>';
																							}
																							//Selection if Type is Smart Watch.
																							if ($row['Type'] == 'Smart Watch')
																							{
																								echo '<option value="Smart Watch" selected>Smart Watch</option>';
																							}
																							else
																							{
																								echo '<option value="Smart Watch">Smart Watch</option>';
																							}
																							
																					echo'	</select>
																						</td>
																					</tr>
																					<tr>
																						<td>Make: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="make" value="'.$row['Make'].'" required></td>
																					</tr>
																					<tr>
																						<td>Model Number: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="model" value="'.$row['Model'].'" required></td>
																					</tr>
																					<tr>
																						<td>Serial Number: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="serial" value="'.$row['Serial'].'" required></td>
																					</tr>
																					<tr>
																						<td>Device Name: <span class="required">*</span></td>
																						<td><input class="styled" type="text" name="name" value="'.$row['Name'].'" required></td>
																					</tr>
																				</table>
																				<input type="hidden" name="devid" value="'.$getid.'">
																				<br />
																				<br />
																				<input type="submit" value="Submit">
																		</form>
																	</li>
																</ul>
															</div>';
															
													}
												}
											}
											//Delete Device Function.
											elseif ($_GET['action'] == 'delete')
											{	
												//POST vars
												$getid = mysqli_real_escape_string($con, $_POST['id']);
													
													//Delete Query
													$sql = "DELETE FROM devices WHERE ID= '$getid'";
													
													//Execute Query
													if (mysqli_query($con, $sql))
													{
															echo '<div id="DeleteDevice" class="module">
																	<div class="frameheader"><h2>Device Deleted</h2></div>
																	<ul>
																		<li class="box">
																			<p>Device was deleted successfully.
																			<br />
																			<br />
																			<a href="device.php">Click Here</a> to go back to My Devices.</p>
																		</li>
																	</ul>
															</div>';
													}
													//Mysql Error
													else
													{
														die('Error deleting record: ' . mysqli_error($con));
													}
												
											}
											//Display Registered Devices.
											else
											{
												$index = 0;
												
												//Title Array -- Table Headers
												$titles_array = array("Name:", "Type:", "Make:", "Model Number:", "Serial Number:");
												//Data array -- DB Columns
												$data_array = array("Name", "Type", "Make", "Model", "Serial");
												
												//SQL Array.
												$sql = "SELECT * FROM devices WHERE users_UserID = '$id'";
												
												//Execute Mysql query.
												if($result = mysqli_query($con, $sql))
												{
													echo' 	<div id="Devices" class="module">
														<div class="frameheader"><h2>My Devices</h2></div>
															<ul>
																<li class="box">
																	<h2>My Registered Devices:</h2>
																	<br />
																	<br />
																	<ul>';
																		while ($row = mysqli_fetch_array($result))
																		{
																			echo '
																			<li class="box">
																				<div class="displayDevice">';
																					//Smartphone
																					if ($row['Type'] == 'Smart Phone')
																					{
																						echo '  <!-- Smartphone icon -- courtesy of http://icons8.com -->
																								<img src="./images/icons/smartphone.png" alt="Smartphone" class="deviceIcon"/>';
																					}
																					//Tablet
																					elseif($row['Type'] == 'Tablet')
																					{
																						echo '	<!-- Tablet icon -- courtesy of http://icons8.com -->
																								<img src="./images/icons/tablet.png" alt="Tablet" class="deviceIcon"/>';
																					}
																					//Smartwatch
																					else
																					{
																						echo '<img src="./images/icons/smartwatch.png" alt="Smartwatch" class="deviceIcon"/>';
																					}
																					echo'
																						<table class="myDevices">';
																				
																					foreach ($data_array as $val)
																					{
																						echo '	<tr>';
																						
																								// Check for last row of table (Headers) for styling reasons.
																								if (sizeof($data_array) == ($index + 1))
																								{
																									echo '<td class ="lastDeviceHeader">'.$titles_array[$index].'</td>';
																								}
																								else
																								{
																									echo '<td class ="deviceHeader">'.$titles_array[$index].'</td>';
																								}

																								// Check for last row of table (Data) for styling reasons.
																								if (sizeof($data_array) == ($index + 1))
																								{
																									echo '<td class = "lastDeviceData">'.$row[$val].'</td>';
																									echo'</tr>';
																								}
																								else
																								{
																									echo '<td class = "deviceData">'.$row[$val].'</td>';
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
																						
																						// Device ID to be passed for post in edit and delete.
																						$editid = $row['ID'];
																						
																					}
																			
																	echo'			</table>
																					<div class="deviceButtons">
																						<form id="deviceEdit" method="post" action="device.php?action=edit">
																							<input type="hidden" name="id" value="'.$editid.'">
																							<input type="submit" value="Edit" />
																						</form>
																						<form id="deviceDelete" method="post" action="device.php?action=delete">
																							<input type="hidden" name="id" value="'.$editid.'">
																							<input type="submit" value="Delete" />
																						</form>
																					</div>
																				</div>
																			</li>';
																			
																		}
													echo'			</ul>
																</li>
															</ul>
														</div>';
												}
												//Mysql Error.
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