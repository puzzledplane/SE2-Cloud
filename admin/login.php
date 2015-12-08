<?php

	/***********************************************************************************************************************
	*													login.php														   *
	************************************************************************************************************************
	*	This file contains code to allow the admin to login to their account.  Actions include submitting a login request. *
	************************************************************************************************************************/

session_start();

include("../config.php");

//Attempt to connect to MySQL DB. Error if it cannot connect
if (mysqli_connect_errno())
{
	die('Could not connect: ' . mysqli_connect_error());
};

################################
##      Login Action          ##
################################

//Set Action to nothing if not action is taken from url.
if(!isset($_GET['action'])) 
{	
	$_GET['action'] = "";
}

//If Logged in redirect user to index.
if (!empty($_SESSION['SE_Admin_Username']))
{
	header("location: index.php");
}

//Login request posted. Attempt to login.		
if($_GET['action'] == 'login')
{
	
	//POST Vars
	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	
	//SQL Query - Get information for username.
	$sql="SELECT * FROM admins WHERE Username='$username'";
	//Execute Query
	$result=mysqli_query($con, $sql);
	
	//Get the amount of rows from DB query.
	$count=mysqli_num_rows($result);
	
	//Check if number of rows is equal to 1.
	if($count == 1)
	{
		//Fetch each row from DB.
	  while ($row = mysqli_fetch_array($result))
	  {
		//Hash password
		$hash = hash('sha256', $row['Salt'] . hash('sha256', $password));
		
		//Error - Incorrect Password
		if($hash != $row['Password'])
		{
			$_SESSION['SE_Admin_Error'] = "You have entered an incorrect password. Please try again";
			header('Location: login.php');
			die();
		}
		//Register Sessions - Password Correct
		else
		{
			$_SESSION['SE_Admin_ID'] = $row['ID'];
			$_SESSION['SE_Admin_Username'] = $row['Username'];
			$_SESSION['SE_Admin_Name'] = $row['Name'];
			$_SESSION['SE_Admin_AccessCode'] = $row['AccessCode'];
			$_SESSION['SE_Admin_Error'] = "";
		}
	  }
	  
	  //Redirect to index.
	  header("location:index.php");
	  
	} 
	//Error - Incorrect Username Entered
	else 
	{
		$_SESSION['SE_User_Error'] = "You have entered an incorrect username. Please try again";
		header('Location: login.php');
		die();
	}
}
//Display Login Form
else
{
	
	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>Admin Login</title>
				<link rel="stylesheet" href="../templates/main/style.css">
				</head>
				
					<body>';
						include('../templates/main/header.php');
						include('./includes/navigation.php');
	echo'				<div id="content">
							<table id="mainframe">
								<tr>
									<td class="maincontent">
											<!-- Begin Login Form -->
											<div id="Login" class="module">
												<div class="frameheader"><h2>Health Care Provider Login</h2></div>
													<ul>
														<li class="box">
																<p>This login is for health care providers/clinicians ONLY. If you are a user, please login <a href="../login.php">here</a></p>';
																//Display Error Message if set.
																if (isset($_SESSION['SE_Admin_Error'])) 
																{
																	echo '
																	<span class="error">'.$_SESSION['SE_Admin_Error'].'</span>
																		<br />
																		<br />	';
																}
												echo'	
														<form method="post" action="login.php?action=login">
																<div id="label">Username: 
																	<input class="styled" type="text" id="username" name="username" required>
																</div>		
																<div id="label">Password: 
																	<input class="styled" type="password" id="password" name="password" required>
																</div>
																<br />
																<span><a href="forgotpassword.php">Forgot Password</a></span>
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
						include('../templates/main/footer.php');
echo'				</body>
				</html>';
}
		
?>