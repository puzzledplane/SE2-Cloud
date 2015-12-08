<?php

	/***********************************************************************************************************************
	*													navigation.php													   *
	************************************************************************************************************************
	*	This include file includes code to display navigation to the user based on session status.	                       *
	************************************************************************************************************************/

echo'	<div id="navigation">
		<ul>';
			
			//Navigation if logged in
			if (!empty($_SESSION['SE_Admin_Username']))
			{
				echo '<li><a href="index.php">Home</a></li>|
					  <li><a href="account.php">My Account</a></li>|
					  <li><a href="logout.php">Logout</a></li>';
			}
			//Navigation if not logged in
			else
			{
				echo '<li><a href="login.php">Login</a></li>|
					  <li><a href="register.php">Register</a></li>';
			}
echo'
		</ul>
	</div>';
?>