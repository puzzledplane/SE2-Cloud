<?php

	/***********************************************************************************************************************
	*													register.php													   *
	************************************************************************************************************************
	*	This file contains code to allow the user to register an account.  Actions include submitting a registration 	   *
	*	request.  																										   *
	************************************************************************************************************************/

session_start();

require_once ('./includes/securimage/securimage.php');
require ('./includes/utilities/utilities.php');
include("./config.php");
$options = array();
$options['input_name'] = 'captcha';

//Attempt to connect to mysql server. Otherwise die with an error.
if (mysqli_connect_errno())
{
	die('Could not connect: ' . mysqli_connect_error());
};

################################
##      Register Action       ##
################################

if(!isset($_GET['action'])) 
{	
	$_GET['action'] = "";
}

//If already logged in redirect to index page.
if (!empty($_SESSION['SE_User_Username']))
{
	header("location: index.php");
}

//Register action. Submit Registration.
if($_GET['action'] == 'register')
{
	//POST Variables
	$username = mysqli_real_escape_string($con, $_POST['username']);
	$pwd = mysqli_real_escape_string($con, $_POST['password']);
	$confirm = mysqli_real_escape_string($con, $_POST['confirm']);
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
	
	//SQL Query
	$sql="SELECT * FROM users WHERE Username = '$username' OR Email = '$email'";
	$result = mysqli_query($con, $sql);
	
	//Count Amount of rows.
	$count=mysqli_num_rows($result);		
	mysqli_free_result($result);
	
	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>User Registration</title>
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
											<!-- Begin Error Form -->
											<div id="Error" class="module">
												<div class="frameheader"><h2>Registration</h2></div>
													<ul>
														<li class="box">';
	//Display Error if Password and confirm password do not match.
	if ($pwd != $confirm)
	{
		echo'		<p>
					Password and Confirm do not match. Please try again.
					<br />
					<br />
					<a href="javascript:history.go(-1)">Click Here</a> to go back.
				</p>';

	}
	//Display Error if email or username have already been registered.
	elseif ($count >= 1)
	{
		echo'	<p>
					Username or Email has already been used. Please try again.
					<br />
					<br />
					<a href="javascript:history.go(-1)">Click Here</a> to go back.
				</p>';
	}
	//Continue to add user registration.
	else
	{
		//Create captcha
		$securimage = new Securimage();
		
		//Check if captcha was entered correctly. Display error if it is not entered correctly.
		if ($securimage->check($_POST['captcha']) == false) 
		{
			header('location: wrongcaptcha.php');
			exit;
		}
		
		//Hash password.
		$hash = hash('sha256', $pwd);
		//Create Salt
		$salt = createSalt();
		//Hash Salt and Hash
		$hash = hash('sha256', $salt . $hash);
		
		//Generate API_Key
		$api_key = createAPIKey ($valid_chars, 15);
		
		//Registration Query.
		$query = "INSERT INTO users (Username, Password, Salt, Name, Email, Gender, DOB, Height, Weight, Location, StepGoal, API_Key) 
		VALUES ('$username', '$hash', '$salt', '$name', '$email', '$gender', '$dob', '$height', '$weight', '$location', '$stepgoal', '$api_key')";
		
		//Execute Query
		if (mysqli_query($con, $query))
		{
			echo'	<p>
					Account was successfully created.
					<br />
					<br />
					<a href="login.php">Click Here</a> to go login.
				</p>';
		}
		//Mysql Error.
		else
		{
			die('Error: ' . mysqli_error($con));
		}
	}
	
echo'													</li>
													</ul>
											</div>
								</td>';
								include('./includes/sidebar.right.php');
echo'							</tr>
							</table>
						</div>';
						include('./templates/main/footer.php');
echo'				</body>
				</html>';

}
//Display Registration Form.
else
{
	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>User Registration</title>
					<link rel="stylesheet" href="./templates/main/style.css">
					<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
					<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
					<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
					<!-- Date picker script -- Source: https://jqueryui.com/datepicker/#dropdown-month-year -->
					<script>
						$(document).ready(function() {
							$( "#dob" ).datepicker({
							changeMonth: true,
						changeYear: true
						});
						});
					</script>
				</head>
				
					<body>';
						include('./templates/main/header.php');
						include('./includes/navigation.php');
	echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Registration Form -->
											<div id="Registration" class="module">
												<div class="frameheader"><h2>User Registration</h2></div>
													<ul>
														<li class="box">
														<p>Please fill out this form to create an account. 
														All fields marked with a <span class="required">*</span> are required.</p>
														<br />
														<h3>Personal Information:</h3>
														<br />
														<form method="post" action="register.php?action=register" data-parsley-validate>
																<table id="register">
																	<tr>
																		<td>Username: <span class="required">*</span></td>
																		<td><input class="styled" type="text" name="username" required></td>
																	</tr>
																	<tr>
																		<td>Password: <span class="required">*</span></td>
																		<td><input class="styled" type="password" name="password" required></td>
																	</tr>
																	<tr>
																		<td>Confirm Password: <span class="required">*</span>
																		<td><input class="styled" type="password" name="confirm" required></td>
																	</tr>
																	<tr>
																		<td>Full Name: <span class="required">*</span></td>
																		<td><input class="styled" type="text" name="name" required></td>
																	</tr>
																	<tr>
																		<td>Email: <span class="required">*</span></td>
																		<td><input class="styled" type="email" name="email" required></td>
																	</tr>
																	<tr>
																		<td>Gender: <span class="required">*</span></td>
																		<td>
																			<select class="styled" name="gender" required>
																				<option value="Male">Male</option>
																				<option value="Female">Female</option>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td>Date of Birth 
																		<br />
																		(MM/DD/YYYY): <span class="required">*</span></td>
																		<td><input class="styled" type="text" name="dob" required pattern="^(0[1-9]|1[012])[/](0[1-9]|[12][0-9]|3[01])[/](19|20)\d\d"></td>
																	</tr>
																	<tr>
																		<td>Height: <span class="required">*</span></td>
																		<td><input class="height" type="text" name="heightft" required> feet <input class="height" type="text" name="heightinches" required> inches</td>
																	</tr>
																	<tr>
																		<td>Weight: <span class="required">*</span></td>
																		<td><input class="height" type="text" name="weight" required> lbs</td>
																	</tr>
																	<tr>
																		<td>Location (City, State): <span class="required">*</span></td>
																		<td><input class="styled" type="text" name="location" required></td>
																	</tr>
																	<tr>
																		<td>Daily Step Goal: <span class="required">*</span></td>
																		<td><input class="styled" type="text" name="stepgoal" required></td>
																	</tr>';
																	
																	//Display Captcha Error.
																	if (!empty($_SESSION['ctform']['captcha_error'])) 
																	{
																		$options['error_html'] = $_SESSION['ctform']['captcha_error'];
																	}
																	
													echo'			<tr>
																		<td>Captcha: <span class="required">*</span></td>
																		<td>';echo Securimage::getCaptchaHtml($options); echo'</td>
																	</tr>';
																	

													echo'		</table>
																<br />
																<br />
																<input type="submit" value="Submit">
														</form>
														</li>
													</ul>
											</div>
								</td>';
								include('./includes/sidebar.right.php');
echo'							</tr>
							</table>
						</div>';
						include('./templates/main/footer.php');
echo'				</body>
				</html>';
}

mysqli_close($con);
?>