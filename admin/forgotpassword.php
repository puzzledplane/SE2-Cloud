<?php

	/***********************************************************************************************************************
	*												forgotpassword.php													   *
	************************************************************************************************************************
	*	This file contains code to retrieve a admins account by resetting their forgotten password.  Actions include  	   *
	*	verification of confirmation code and changing the password.		                                               *
	************************************************************************************************************************/

include('../config.php');
require_once '../includes/securimage/securimage.php';

//Set Default Timezone to New_York.
date_default_timezone_set("America/New_York"); 

//Captcha Options
$options = array();
$options['input_name'] = 'captcha'; // change name of input element for form post

//Function to strip special characters from inputs.
function strip_html($data)
{
	$data = htmlspecialchars($data);
	return $data;
}

//Valid Chars for randomly generated string.
$valid_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

/*********************************************************************************************
*                             get_random_string function                                     *
*                                   By: Chad Birch                                           *
* Source: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php  *
**********************************************************************************************/

function get_random_string($valid_chars, $length)
{
    // start with an empty random string
    $random_string = "";

    // count the number of chars in the valid chars string so we know how many choices we have
    $num_valid_chars = strlen($valid_chars);

    // repeat the steps until we've created a string of the right length
    for ($i = 0; $i < $length; $i++)
    {
        // pick a random number from 1 up to the number of valid chars
        $random_pick = mt_rand(1, $num_valid_chars);

        // take the random character out of the string of valid chars
        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
        $random_char = $valid_chars[$random_pick-1];

        // add the randomly-chosen char onto the end of our string so far
        $random_string .= $random_char;
    }

    // return our finished random string
    return $random_string;
}

//Set get request to blank if nothing found for action.
if(!isset($_GET['action']))
{
	$_GET['action'] = '';
}

//Attempt to connect to MySQL database. Error if it cannot connect.
if (mysqli_connect_errno())
{
	die('Could not connect: ' . mysqli_connect_error());
}

if($_GET['action'] == 'submit')
{
		//POST Vars
		$email = mysqli_real_escape_string($con, $_POST['email']);
		
		//Create captcha
		$securimage = new Securimage();
		
		//Check if captcha was entered correctly. Display error if it is not entered correctly.
		if ($securimage->check($_POST['captcha']) == false) 
		{
			header("location: ../wrongcaptcha.php");
			exit;
		}
		
		//SQL Query to get data for posted email.
		$query = "SELECT * FROM admins WHERE Email = '$email'"; 
		
		//Execute Query
		if ($result=mysqli_query($con, $query))
		{
		  // Return the number of rows in result set
		  $total = mysqli_num_rows($result);
		  // Free result set
		  mysqli_free_result($result);
		 }
		 //MySQL Error
		 else
		 {
			die('Error:' . mysqli_error($con));
		 }
		
		echo '
		<!DOCTYPE html>
			<html>
			<head>
			<meta charset="UTF-8">
			<title>Forgot Password</title>
			<link rel="stylesheet" href="../templates/main/style.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
			<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
			</head>
			
				<body>';
					include ('../templates/main/header.php');
					include('./includes/navigation.php');
echo'				<div id="content">
						<table id="mainframe">
							<tr>
								<td class="maincontent">';
										echo'				
											<div id="ForgotPassword" class="module">
												<div class="frameheader"><h2>Forgot Password</h2></div>
													<ul>
														<li class="box">';

															//Email if there is one row with posted email.
															if ($total == 1)
															{
																echo '<p>
																A password recovery link has been emailed to '.$email.'. Please allow a few minutes for it
																to be delivered.
																<br />
																<br />
																<a href="login.php">Click Here</a> to return to the login.
																</p>';
																
																//Insert result into recovery database.
																	
																$key = get_random_string($valid_chars, 12);
																
																//Get Current Date and Time
																$currentdate = date("m/d/Y h:iA");
																
																//Get Date and Time for 1 day in from current time.
																$expdate = date("m/d/Y h:iA", strtotime("+ 1 Day"));
																
																//Insert Sql Query
																$recoveryquery = "INSERT INTO recovery (Email, RequestID, ExpDate) 
																VALUES ('$email', '$key', '$expdate')";
																
																//Die if MySQL Error
																if (!mysqli_query($con, $recoveryquery))
																{
																	die('Error:' . mysqli_error($con));
																}
																
																// Email password url.
																
																$to = $email;
																$subject = 'Password Reset';

																$message = '
																<html>
																<head>
																<title>System Password Reset</title>
																</head>
																<body>
																<p>
																Someone requested a password change. If you did not intend to do this
																you can ignore this email.
																<br />
																<br />
																Please <a href="'.$dbsite_url.'/admin/forgotpassword.php?action=verify&email='.$email.'&key='.$key.'">click here</a> to reset your
																password.
																<br />
																<br />
																This link will expire in one day.
																<br />
																<br />
																- Webmaster
																</p>
																</body>
																</html>
																';

																// Always set content-type when sending HTML email
																$headers = 'MIME-Version: 1.0" . "\r\n';
																$headers .= 'Content-type:text/html;charset=UTF-8" . "\r\n';

																// More headers
																$headers .= 'From: <webmaster@se1dev.no-ip.org>' . '\r\n';
																
																//Send Email for password recovery.
																mail($to,$subject,$message,$headers);
																
															}
															//Error No Email found
															else
															{
																echo '	No Email found for '.$email.'. Please Try Again.
																		<br />
																		<br />
																		<a href="javascript:history.go(-1)">Click Here</a> to go back.';
															}
echo'													</li>
													</ul>
											</div>';
echo'							</td>';
								include('./includes/sidebar.right.php');	
echo'						</tr>
						</table>
					</div>';
					include ('../templates/main/footer.php');
echo'			</body>
			</html>';
}
//Verify Action
elseif ($_GET['action'] == 'verify')
{
	
	echo '
		<!DOCTYPE html>
			<html>
			<head>
			<meta charset="UTF-8">
			<title>Forgot Password</title>
			<link rel="stylesheet" href="../templates/main/style.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
			<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
			</head>
			
				<body>';
					include ('../templates/main/header.php');
					include('./includes/navigation.php');
echo'				<div id="content">
						<table id="mainframe">
							<tr>
								<td class="maincontent">';
										echo'				
											<div id="ForgotPassword" class="module">
												<div class="frameheader"><h2>Change Password</h2></div>
													<ul>
														<li class="box">';
															//Check if email and key are set in the url.
															if (isset($_GET['email']) && isset($_GET['key']))
															{
																//Get current date.
																$currentdate = date("m/d/Y h:iA");
																
																//POST vars
																$email = mysqli_real_escape_string($con, strip_html($_GET['email']));
																$key = mysqli_real_escape_string($con, strip_html($_GET['key']));
																
																//Verify key is correct.
																	
																$query = "SELECT * FROM recovery WHERE Email = '$email' AND RequestID = '$key'"; 
																
																//Execute Query
																if ($result = mysqli_query($con, $query))
																{
																  // Return the number of rows in result set
																  $total = mysqli_num_rows($result);
																  // Put result into array.
																  $row = mysqli_fetch_array($result);
																  
																 }
																 //Die with MySQL Error.
																 else
																 {
																	die('Error:' . mysqli_error($con));
																 }
																
																// Check if number of row is equal to 1 and the date is less than or equal to expiration date.
																// Display Change Password form.
																if ($total == 1 && $currentdate <= $row['ExpDate'])
																{
																	echo'
																	<form method="post" id="changepassword" action="forgotpassword.php?action=changepassword">
																		<div id="label">Password: <span class="error">*</span></div>
																		<input class="styled" type="password" name="password" required>
																		<div id="label">Confirm Password: <span class="error">*</span></div>
																		<input class="styled" type="password" name="confirm" required>
																		<input type="hidden" name="email" value="'.$email.'">
																		<input type="hidden" name="key" value="'.$key.'">
																		<br />
																		<br />
																		<input type="submit" value="Submit">
																	</form>';
																}
																// Invalid Request
																elseif ($total == 0)
																{
																	echo '<p> Invalid request. Please submit a new one.
																		  <br />
																		  <br />
																		  <a href="forgotpassword.php">Click Here</a> to go back to the forgot password request.
																		  </p>';
																}
																//Request is expired. Show Error and delete from database.
																elseif ($currentdate > $row['ExpDate'])
																{
																	
																	$query = "DELETE FROM recovery WHERE Email = '$email' AND RequestID = '$key'"; 
																	
																	if (!mysqli_query($con, $query))
																	{
																		die('Error:' . mysqli_error($con));
																	}
																	
																	echo '<p> This request has expired. Please submit a new one.
																		  <br />
																		  <br />
																		  <a href="forgotpassword.php">Click Here</a> to go back to the forgot password request.
																		  </p>';	  
																}
																//Invalid Request.
																else
																{
																	echo '<p> Invalid request. Please submit a new one.
																		  <br />
																		  <br />
																		  <a href="forgotpassword.php">Click Here</a> to go back to the forgot password request.
																		  </p>';
																}
																
																// Free result set.
																mysqli_free_result($result);
																
															}
															//Invalid email or key in url.
															else
															{
																echo '	<p>Please enter a valid email and key.
																		<br />
																		<br />
																		<a href="forgotpassword.php">Click Here</a> to go back to the forgot password request.
																		</p>';
															}
echo'													</li>
													</ul>
											</div>
								</td>';
								include('./includes/sidebar.right.php');	
echo'						</tr>
						</table>
					</div>';
					include ('../templates/main/footer.php');
echo'			</body>
			</html>';
}
//Change Password Action after verification.
elseif ($_GET['action'] == 'changepassword')
{

echo '
		<!DOCTYPE html>
			<html>
			<head>
			<meta charset="UTF-8">
			<title>Forgot Password</title>
			<link rel="stylesheet" href="../templates/main/style.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
			<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
			</head>
			
				<body>';
					include ('../templates/main/header.php');
					include('./includes/navigation.php');
echo'				<div id="content">
						<table id="mainframe">
							<tr>
								<td class="maincontent">';
										echo'				
											<div id="ForgotPassword" class="module">
												<div class="frameheader"><h2>Password Change</h2></div>
													<ul>
														<li class="box">';
															//Check if password and key are posted from form.
															if (isset($_POST['password']) && isset($_POST['key']))
															{
																
																// Salt Function -- Source: http://tinsology.net/2009/06/creating-a-secure-login-system-the-right-way/
																function createSalt()
																{
																	$string = md5(uniqid(rand(), true));
																	return substr($string, 0, 3);
																}
																
																//POST vars
																$pwd = mysqli_real_escape_string($con, strip_html($_POST['password']));
																$confirm = mysqli_real_escape_string($con, strip_html($_POST['confirm']));
																$email = mysqli_real_escape_string($con, strip_html($_POST['email']));
																$key = mysqli_real_escape_string($con, strip_html($_POST['key']));
																
																// Check if password and confirm password are the same.
																if ($pwd == $confirm)
																{
																	//Hash password.
																	$hash = hash('sha256', $pwd);
																	//Create Salt
																	$salt = createSalt();
																	//Hash Salt and Hash
																	$hash = hash('sha256', $salt . $hash);
																	
																	//Delete recovery request query
																	$query = "DELETE FROM recovery WHERE Email = '$email' AND RequestID = '$key'"; 
																	
																	//Error if query cannot execute. Die with error.
																	if (!mysqli_query($con, $query))
																	{
																		die('Error:' . mysqli_error($con));
																	}
																	
																	//Update password and salt query.
																	$update = "UPDATE admins SET Salt = '$salt', Password = '$hash' WHERE Email = '$email'";
																	
																	//Error if query cannot execute. Die with error.
																	if (!mysqli_query($con, $update))
																	{
																		die('Error:' . mysqli_error($con));
																	}
																	
																	echo' 	<p>Your password was successfully changed!
																			<br />
																			<br />
																			<a href="login.php">Click here</a> to go back to the login.
																			</p>';
																	
																}
																//Passwords do not match Error
																else
																{
																	echo'	<p>
																			Passwords do not match. Please try again.
																			<br />
																			<br />
																			<a href="javascript:history.go(-1)">Click Here</a> to go back.
																			</p>';
																}
																
															}
															//Error if page is access without POST from verification.
															else
															{
																echo '<p>
																		Error: You cannot access this page directly.
																		<br />
																		<br />
																		<a href="forgotpassword.php">Click Here</a> to go back to the forgot password request.
																	</p>';
																		
															}
echo'													</li>
													</ul>
										</div>';
echo'							</td>';
								include('./includes/sidebar.right.php');	
echo'						</tr>
						</table>
					</div>';
					include ('../templates/main/footer.php');
echo'			</body>
			</html>';
}							
//Display Email Form for password recovery.
else
{

echo '
		<!DOCTYPE html>
			<html>
			<head>
			<meta charset="UTF-8">
			<title>Forgot Password</title>
			<link rel="stylesheet" href="../templates/main/style.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
			<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
			</head>
			
				<body>';
					include ('../templates/main/header.php');
					include('./includes/navigation.php');
echo'				<div id="content">
						<table id="mainframe">
							<tr>
								<td class="maincontent">';
										echo'				
											<div id="ForgotPassword" class="module">
												<div class="frameheader"><h2>Forgot Password</h2></div>
													<ul>
														<li class="box">
															<br />
															<form method="post" id="forgotpassword" action="forgotpassword.php?action=submit">
																<div id="label">Email: <span class="error">*</span></div>
																<input class="styled" type="email" name="email" required data-parsley-trigger="change">
																<div id="label">Captcha: <span class="error">*</span></div>
																<br />';
																
																//Captcha error.
																if (!empty($_SESSION['ctform']['captcha_error'])) 
																{
																	$options['error_html'] = $_SESSION['ctform']['captcha_error'];
																}

																echo Securimage::getCaptchaHtml($options);
						
	echo '															<br />
																<br />
																<input type="submit" value="Submit">
															</form>
															<br />
														</li>
													</ul>
										</div>';
echo'							</td>';
								include('./includes/sidebar.right.php');	
echo'						</tr>
						</table>
					</div>';
					include ('../templates/main/footer.php');
echo'			</body>
			</html>';
}

mysqli_close($con);								
?>