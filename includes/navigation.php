<?php

	/***********************************************************************************************************************
	*													navigation.php													   *
	************************************************************************************************************************
	*	This include file includes code to display navigation to the user based on session status.	                       *
	************************************************************************************************************************/


echo'	<div id="navigation">
		<ul>';
			
			//If logged in display these menu items
			if (!empty($_SESSION['SE_User_Username']))
			{
				echo '<li><a href="index.php">Home</a></li>|
					  <li><a href="account.php">My Account</a></li>|
					  <li><a href="device.php">My Devices</a></li>|
					  <li><a href="logout.php">Logout</a></li>';
			}
			//Not logged in
			else
			{
				echo '<li><a href="login.php">Login</a></li>|
					  <li><a href="register.php">Register</a></li>';
			}
echo'
		</ul>
	</div>';
?>