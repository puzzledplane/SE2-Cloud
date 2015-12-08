<?php

	/***********************************************************************************************************************
	*												   sidebar.right.php												   *
	************************************************************************************************************************
	*	This include file includes code to display sidebar navigation to the user based on session status.	               *
	************************************************************************************************************************/
	
	//Sidebar if not logged in
	if (empty($_SESSION['SE_Admin_Username']))
	{
		echo '	<td class="sidebar" id="right">
				<!-- nav Information.-->
						<div id="nav" class="module">
							<div class="frameheader"><h2>Navigation</h2></div>
							<ul>
								<li class="box">
									<ul>
										<li class="box">
											<h3>Account</h3>
											<br />
											<a href="login.php">Login</a>
											<br />
											<a href="register.php">Register</a>
											<br />
											<a href="forgotpassword.php">Forgot Password</a>
										</li>
									</ul>
								</li>
							</ul>
						</div>
			</td>';
	}
	//Sidebar if logged in
	else
	{
		echo '	<td class="sidebar" id="right">
				<!-- nav Information.-->
						<div id="nav" class="module">
							<div class="frameheader"><h2>Navigation</h2></div>
							<ul>
								<li class="box">
									<ul>
										<li class="box">
											<h3>My Account</h3>
											<br />
											<a href="account.php">View My Account</a>
											<br />
											<a href="account.php?action=edit">Edit My Information</a>
											<br />
											<a href="account.php?action=changepassword">Change Password</a>
										</li>
										<li class="box">
											<h3>Authorized Users</h3>
											<br />
											<a href="authorize.php">My Authorizations</a>
										</li>
									</ul>
								</li>
							</ul>
						</div>
			</td>';
	}

?>