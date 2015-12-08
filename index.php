<?php

	/***********************************************************************************************************************
	*													index.php													       *
	************************************************************************************************************************
	*	This file contains code to display the dashboard to the user.													   *
	************************************************************************************************************************/


	session_start();
	include('config.php');
	include_once('/includes/modules/getGoals.php');
	include_once('/includes/modules/getSensorData.php');
	include_once('/includes/modules/getGraphData.php');
	
	date_default_timezone_set('America/New_York');
	
		//Check if the user is logged in using session variables. Otherwise they are redirected to login.
		if(empty($_SESSION['SE_User_Username']) || empty($_SESSION['SE_User_ID']))
		{		
			$_SESSION['SE_User_Error'] = "You must sign in first.";
			header("location: login.php");
		}
		//Display Index -- Dashboard.
		else
		{
			//Daily Step Sensor Data
			$dailystepdata = getSensorData ($_SESSION['SE_User_ID'], "Steps", "Daily");
			$dailysteps = $dailystepdata[0];
			$lastdailyupdate = $dailystepdata[1]; 
			
			//Weekly Step Sensor Data
			$weeklystepdata = getSensorData ($_SESSION['SE_User_ID'], "Steps", "Weekly");
			$weeklysteps = $weeklystepdata[0];
			$lastweeklyupdate = $weeklystepdata[1]; 
			
			//Monthly Step Sensor Data
			$monthlystepdata = getSensorData ($_SESSION['SE_User_ID'], "Steps", "Monthly");
			$monthlysteps = $monthlystepdata[0];
			$lastmonthlyupdate = $monthlystepdata[1];
			
			//Conversions for steps to miles.
			$daily_step_miles = $dailysteps / 2500;
			$weekly_step_miles = $weeklysteps / 2500;
			$monthly_step_miles = $monthlysteps / 2500;
			
			//Get weekly and monthly average steps
			$weekly_avg_steps = floor($weeklysteps / 7);
			$monthly_avg_steps = floor($monthlysteps / date('t'));
			
			//Get Daily, Weekly and Monthly Step Goals
			$daily_step_goal = getStepGoal($_SESSION['SE_User_ID']);
			$weekly_step_goal = $daily_step_goal * 7;
			$monthly_step_goal = $daily_step_goal * date('t');
			
			//Get Percentages for progressbar
			$daily_step_percent = floor(($dailysteps / $daily_step_goal) * 100);
			$weekly_step_percent = floor(($weeklysteps / $weekly_step_goal) * 100);
			$monthly_step_percent = floor(($monthlysteps / $monthly_step_goal) * 100);
			
			//Daily HB Sensor Data
			$dailyHBdata = getSensorData ($_SESSION['SE_User_ID'], "HB", "Daily");
			$dailyHB = $dailyHBdata[0];
			$lastdailyupdateHB = $dailyHBdata[1];
			
			//Weekly HB Sensor Data
			$weeklyHBdata = getSensorData ($_SESSION['SE_User_ID'], "HB", "Weekly");
			$weeklyHB = $weeklyHBdata[0];
			$lastweeklyupdateHB = $weeklyHBdata[1];
			$weeklyHBsize = $weeklyHBdata[2];
			
			//Weekly HB Sensor Data
			$monthlyHBdata = getSensorData ($_SESSION['SE_User_ID'], "HB", "Monthly");
			$monthlyHB = $monthlyHBdata[0];
			$lastmonthlyupdateHB = $monthlyHBdata[1];
			$monthlyHBsize = $monthlyHBdata[2];
			
			
			//Get weekly and monthly average heartbeat
			if ($weeklyHBsize == 0)
			{
				$weekly_avg_HB = 0;
			}
			else
			{
				$weekly_avg_HB = floor($weeklyHB / $weeklyHBsize);
			}
			
			if ($monthlyHBsize == 0)
			{
				$monthly_avg_HB = 0;
			}
			else
			{
				$monthly_avg_HB =floor($monthlyHB / $monthlyHBsize);
			}
	
	echo '
			<!DOCTYPE html>
				<html>
				<head>
				<meta charset="UTF-8">
				<title>User Dashboard</title>
					<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
					<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
					<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
					<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
					<link rel="stylesheet" href="./templates/main/style.css">
					<script>
						$(function() {
							$( ".tabs" ).tabs({
								beforeLoad: function( event, ui ) {
									ui.jqXHR.fail(function() {
									ui.panel.html(
									"Couldn\'t load this tab.");
									});
								}
							});
						});
					</script>
					<script>
						$(function() {
							var dailysteps = '.$dailysteps.'
							var weeklysteps = '.$weeklysteps.'
							var monthlysteps = '.$monthlysteps.'
							var dailystepgoal = '.$daily_step_goal.'
							var weeklystepgoal = '.$weekly_step_goal.'
							var monthlystepgoal = '.$monthly_step_goal.'
							$( "#dailystepspbar" ).progressbar({
								max: dailystepgoal,
								value: dailysteps
							});
							$( "#weeklystepspbar" ).progressbar({
								max: weeklystepgoal,
								value: weeklysteps
							});
							$( "#monthlystepspbar" ).progressbar({
								max: monthlystepgoal,
								value: monthlysteps
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
											<div id="Dashboard" class="module">
												<div class="frameheader"><h2>User Dashboard</h2></div>
													<ul>
														<li class="box">
															<h2>Welcome '.$_SESSION['SE_User_Name'].',</h2>
														</li>
													</ul>
											</div>
											<div id="StepCounts" class="module">
											<div class="frameheader"><h2>Step Count</h2></div>
													<ul>
														<li class="box">
															<div class="tabs">
															  <ul>
																<li><a href="#dailystep">Daily</a></li>
																<li><a href="#weeklystep">Weekly</a></li>
																<li><a href="#monthlystep">Monthly</a></li>
															  </ul>
															  <div id="dailystep">
																<div id ="dailystepspbar"><div class="progress-label">';
																if ($daily_step_percent < 55) 
																{
																	echo '<span class="wbg">'.$daily_step_percent.'%</span>';
																}
																else
																{
																	echo '<span class="rbg">'.$daily_step_percent.'%</span>';
																}
							echo '
																</div>
																</div>
																<span class="leftbar">0</span><span class="rightbar">'.$daily_step_goal.'</span>
																<div class = "goalheader">
																<table class="stepsgoal">
																	<tr>
																		<td class="title">
																			Progress:
																		</td>
																		<td>
																			'.$dailysteps.'/'.$daily_step_goal.' Steps
																		</td>
																	</tr>
																	<tr>
																		<td class="title">
																			Distance:
																		</td>
																		<td>
																			'.$daily_step_miles.' Miles
																		</td>
																	</tr>
																</table>
																</div>
																<span class="left"><a href="account.php?action=goals">Set Goal</a></span>';
																if ($lastdailyupdate == 0)
																{
																	//Nothing
																}
																else
																{
																	echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastdailyupdate)).'</span>';
																}
															
										echo'
															</div>
															<div id="weeklystep">
															  <div id ="weeklystepspbar"><div class="progress-label">';
															  if ($weekly_step_percent < 55) 
																{
																	echo '<span class="wbg">'.$weekly_step_percent.'%</span>';
																}
																else
																{
																	echo '<span class="rbg">'.$weekly_step_percent.'%</span>';
																}
							echo '
															  </div></div>
															  <span class="leftbar">0</span><span class="rightbar">'.$weekly_step_goal.'</span>
																<div class = "goalheader">
																<table class="stepsgoal">
																	<tr>
																		<td class="title">
																			Progress:
																		</td>
																		<td>
																			'.$weeklysteps.'/'.$weekly_step_goal.' Steps
																		</td>
																	</tr>
																	<tr>
																		<td class="title">
																			Distance:
																		</td>
																		<td>
																			'.$weekly_step_miles.' Miles
																		</td>
																	</tr>
																	<tr>
																		<td class="title">
																			Average:
																		</td>
																		<td>
																			'.$weekly_avg_steps.' Steps
																		</td>
																	</tr>
																</table>
																</div>';
																
																// Get the first and last days of the week.
																
																$date1 = new DateTime();
																$date2 = new DateTime();
																$date3 = new DateTime();
																$weekrange;
																
																$week_num = date("W");
																$cur_year = date("Y");
																
																$date1->setISODate($cur_year, $week_num);
																$date1->modify('-1 day');
																
																$date2->setISODate($cur_year, $week_num, 7);
																$date2->modify('-1 day');
																
																$date3 = clone $date1;
																
																$weekrange[0] = $date1->format("m/d/Y");
																
																for ($i = 1; $i <= 6; $i++)
																{
																	$date3->modify('+1 day');
																	$weekrange[$i] = $date3->format("m/d/Y");
																}
																
																$weekdata = getGraphData($_SESSION['SE_User_ID'], 'Steps', $weekrange[0], $weekrange[6]);
																
																echo '
																
																<div class="graphcontainer">
																	<canvas id="weeklyscgraph" width="600" height="400"></canvas>
																</div>
																
																<script>
		
																	var ctx = document.getElementById("weeklyscgraph").getContext("2d");
																	
																	var data = {
																		labels : ["'.$weekrange[0].'","'.$weekrange[1].'","'.$weekrange[2].'","'.$weekrange[3].'","'.$weekrange[4].'","'.$weekrange[5].'","'.$weekrange[6].'"],
																		datasets : [
																			{
																				label: "Weekly Step Counts",
																				fillColor : "#B20000",
																				strokeColor : "#000000",
																				data : ['.$weekdata[0].','.$weekdata[1].','.$weekdata[2].','.$weekdata[3].','.$weekdata[4].','.$weekdata[5].','.$weekdata[6].']
																			}
																		]
																	}
																	
																	var options = {
																			barStrokeWidth : 1,
																			scaleFontColor: "#000000"
																	}
																	
																	var barChart = new Chart(ctx).Bar(data, options);
																	
																</script>
																
																<span class="left"> '.$date1->format("m/d/Y") . " - " . $date2->format("m/d/Y").' </span>';
																
																if ($lastweeklyupdate == 0)
																{
																	//Nothing
																}
																else
																{
																	echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastweeklyupdate)).'</span>';
																}
										echo'				</div>
															<div id="monthlystep">
															  <div id ="monthlystepspbar"><div class="progress-label">';
															  if ($monthly_step_percent < 55) 
																{
																	echo '<span class="wbg">'.$monthly_step_percent.'%</span>';
																}
																else
																{
																	echo '<span class="rbg">'.$monthly_step_percent.'%</span>';
																}
							echo '
																</div></div>
															  <span class="leftbar">0</span><span class="rightbar">'.$monthly_step_goal.'</span>
																<div class = "goalheader">
																<table class="stepsgoal">
																	<tr>
																		<td class="title">
																			Progress:
																		</td>
																		<td>
																			'.$monthlysteps.'/'.$monthly_step_goal.' Steps
																		</td>
																	</tr>
																	<tr>
																		<td class="title">
																			Distance:
																		</td>
																		<td>
																			'.$monthly_step_miles.' Miles
																		</td>
																	</tr>
																	<tr>
																		<td class="title">
																			Average:
																		</td>
																		<td>
																			'.$monthly_avg_steps.' Steps
																		</td>
																	</tr>
																</table>
																</div>';
																
																//First day of month
																
																$firstday = new DateTime(date('m/1/Y', strtotime(date("m/d/Y"))));
																
																//Last day of month 
																
																$lastday = new DateTime(date('m/t/Y', strtotime(date("m/d/Y"))));
																
																$daycount = new DateTime();
																$daycount = clone $firstday;
																
																$monthindex = 0;
																
																while ($daycount <= $lastday)
																{
																	$montharray[$monthindex] = $daycount->format("m/d/Y");
																	
																	//Increment day
																	$daycount->modify('+1 day');
																	
																	//Increment index;
																	$monthindex++;
																}
																
																$monthdata = getGraphData($_SESSION['SE_User_ID'], 'Steps', $firstday->format("m/d/Y"), $lastday->format("m/d/Y"));
																
																
								echo'							<div class="graphcontainer">
																	<canvas id="monthlyscgraph" width="675" height="400"></canvas>
																</div>
																
																<script>
		
																	var ctx = document.getElementById("monthlyscgraph").getContext("2d");
																	
																	var data = {
																		labels : ['; 	for ($i = 0; $i <= sizeof($montharray) - 1; $i++) 
																						{ 
																							if ($i == sizeof($montharray))
																							{
																								echo '"'. $montharray[$i] .'"';
																							}
																							else
																							{
																								
																								echo '"'. $montharray[$i] .'",';
																							
																							} 
																						}; 
								echo											'],
																		datasets : [
																			{
																				label: "Monthly Step Count",
																				strokeColor: "#000000",
																				pointColor: "#B20000",
																				data : [';	for ($i = 0; $i <= sizeof($monthdata) - 1; $i++) 
																							{ 
																							if ($i == sizeof($monthdata))
																							{
																								echo $monthdata[$i];
																							}
																							else
																							{
																								
																								echo $monthdata[$i] . ",";
																							
																							} 
																							}; 
								echo												  ']
																			}
																		]
																	}
																	
																	var options = {
																			datasetFill : false,
																			scaleFontColor: "#000000"
																	}
																	
																	var barChart = new Chart(ctx).Line(data, options);
																	
																</script>
																
																<span class="left"> '.$firstday->format("m/d/Y").'  -  '. $lastday->format("m/d/Y").' </span>';
																
																if ($lastmonthlyupdate == 0)
																{
																	//Nothing
																}
																else
																{
																	echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastmonthlyupdate)).'</span>';
																}
										echo'
														</div>
														</li>
													</ul>
												</div>
												<div id="HeartBeat" class="module">
													<div class="frameheader"><h2>Heart Beat</h2></div>
													<ul>
														<li class="box">
														<div class="tabs">
															  <ul>
																<li><a href="#dailyhb">Daily</a></li>
																<li><a href="#weeklyhb">Weekly</a></li>
																<li><a href="#monthlyhb">Monthly</a></li>
															  </ul>
																<div id="dailyhb">
																	<div class="sensordatadisp">'.$dailyHB.' BPM</div>';
																	if ($lastdailyupdateHB == 0)
																	{
																		//Nothing
																	}
																	else
																	{
																		echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastdailyupdateHB)).'</span>';
																	}
echo'															</div>
																  <div id="weeklyhb">
																	<div class = "goalheader">
																	  <table class="HBgoal">
																			<tr>
																				<td class="title">
																					Average:
																				</td>
																				<td class="data">
																					'.$weekly_avg_HB.' BPM
																				</td>
																			</tr>
																		</table>
																	</div>';
																	
																	$weekdata = getGraphData($_SESSION['SE_User_ID'], 'HB', $weekrange[0], $weekrange[6]);
																	
													echo'			<div class="graphcontainer">
																		<canvas id="weeklyHBgraph" width="600" height="400"></canvas>
																	</div>
																	
																	<script>
			
																		var ctx = document.getElementById("weeklyHBgraph").getContext("2d");
																		
																		var data = {
																			labels : ["'.$weekrange[0].'","'.$weekrange[1].'","'.$weekrange[2].'","'.$weekrange[3].'","'.$weekrange[4].'","'.$weekrange[5].'","'.$weekrange[6].'"],
																			datasets : [
																				{
																					label: "Weekly Heartbeat Monitor",
																					fillColor : "#B20000",
																					strokeColor : "#000000",
																					data : ['.$weekdata[0].','.$weekdata[1].','.$weekdata[2].','.$weekdata[3].','.$weekdata[4].','.$weekdata[5].','.$weekdata[6].']
																				}
																			]
																		}
																		
																		var options = {
																				barStrokeWidth : 1,
																				scaleFontColor: "#000000"
																		}
																		
																		var barChart = new Chart(ctx).Bar(data, options);
																		
																	</script>
																	
																	<span class="left"> '.$date1->format("m/d/Y") . " - " . $date2->format("m/d/Y").' </span>
																	';
																	if ($lastweeklyupdateHB == 0)
																	{
																		//Nothing
																	}
																	else
																	{
																		echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastweeklyupdateHB)).'</span>';
																	}
																	
											echo'				</div>
															  <div id="monthlyhb">
																<div class = "goalheader">
																	  <table class="HBgoal">
																			<tr>
																				<td class="title">
																					Average:
																				</td>
																				<td class="data">
																					'.$monthly_avg_HB.' BPM
																				</td>
																			</tr>
																		</table>
																</div>';
																
																$monthdata = getGraphData($_SESSION['SE_User_ID'], 'HB', $firstday->format("m/d/Y"), $lastday->format("m/d/Y"));
																
																
								echo'							<div class="graphcontainer">
																	<canvas id="monthlyHBgraph" width="675" height="400"></canvas>
																</div>
																
																<script>
		
																	var ctx = document.getElementById("monthlyHBgraph").getContext("2d");
																	
																	var data = {
																		labels : ['; 	for ($i = 0; $i <= sizeof($montharray) - 1; $i++) 
																						{ 
																							if ($i == sizeof($montharray))
																							{
																								echo '"'. $montharray[$i] .'"';
																							}
																							else
																							{
																								
																								echo '"'. $montharray[$i] .'",';
																							
																							} 
																						}; 
								echo											'],
																		datasets : [
																			{
																				label: "Monthly Heartbeat Monitor",
																				strokeColor: "#000000",
																				pointColor: "#B20000",
																				data : [';	for ($i = 0; $i <= sizeof($monthdata) - 1; $i++) 
																							{ 
																							if ($i == sizeof($monthdata))
																							{
																								echo $monthdata[$i];
																							}
																							else
																							{
																								
																								echo $monthdata[$i] . ",";
																							
																							} 
																							}; 
								echo												  ']
																			}
																		]
																	}
																	
																	var options = {
																			datasetFill : false,
																			scaleFontColor: "#000000"
																	}
																	
																	var barChart = new Chart(ctx).Line(data, options);
																	
																</script>
																
																<span class="left"> '.$firstday->format("m/d/Y").'  -  '. $lastday->format("m/d/Y").' </span>';
																
																if ($lastmonthlyupdateHB == 0)
																{
																	//Nothing
																}
																else
																{
																	echo'<span class="lastupdated">Last Updated: '.date("m/d/Y h:i a", strtotime($lastmonthlyupdateHB)).'</span>';
																}
							echo'							</div>
														</div>
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
?>