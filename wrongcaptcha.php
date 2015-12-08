<?php

	/***********************************************************************************************************************
	*													wrongcaptcha.php												   *
	************************************************************************************************************************
	*	This file contains code to display an error to the user pertaining to entering a captcha incorrectly.   		   *
	************************************************************************************************************************/

include('config.php');

echo '
		<!DOCTYPE html>
			<html>
			<head>
			<meta charset="UTF-8">
			<title>Incorrect Captcha</title>
			<link rel="stylesheet" href="./templates/main/style.css">
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
			<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css" />
			<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
			</head>
			
				<body>';
					include ('./templates/main/header.php');
					include('./includes/navigation.php');
echo'				<div id="content">
						<table id="mainframe">
							<tr>
								<td class="maincontent">				
									<div id="WrongCaptcha" class="module">
										<div class="frameheader"><h2>Incorrect Code</h2></div>
											<ul>
												<li class="box">
													The security code entered was incorrect.
													<br />
													<br />
													Please go <a href=\'javascript:history.go(-1)\'>back</a> and try again.
													<br />
												</li>
											</ul>
										</div>
								</td>';
								include('./includes/sidebar.right.php');
echo'						</tr>
						</table>
					</div>';
					include ('./templates/main/footer.php');
echo'		</body>
		</html>';							
?>