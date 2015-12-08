<?php

	/**********************************************************************
	* 							API v1
	*		Originally By: Ravi Tamada  Adapted by: Derek Campaniello
	*	URL: http://www.androidhive.info/2014/01/how-to-create-rest-api-for-android-app-using-php-slim-and-mysql-day-12-2/
	*
	************************************************************************/

	require ('../.././libs/Slim/Slim.php');
	include ('../../config.php');
	require ('../../includes/utilities/utilities.php');
	
	if (mysqli_connect_errno())
	{
		die('Could not connect: ' . mysqli_connect_error());
	};
	
	\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim();

	// User id from db - Global Variable
	$user_id = NULL;
	
	function authenticate(\Slim\Route $route) 
	{
		// Getting request headers
		$headers = apache_request_headers();
		$response = array();
		$app = \Slim\Slim::getInstance();

		// Verifying Authorization Header
		if (isset($headers['Authorization'])) 
		{

			// get the api key
			$api_key = $headers['Authorization'];
			
			// validating api key
			if (!ValidateAPIKey($api_key)) 
			{
				// api key is not present in users table
				$response["error"] = true;
				$response["message"] = "Access Denied. Invalid API key";
				echoRespnse(401, $response);
				$app->stop();
			} 
			else 
			{
				global $user_id;
				// get user primary key id
				$user_id = getUserId($api_key);
			}
		} 
		else 
		{
			// api key is missing in header
			$response["error"] = true;
			$response["message"] = "API key is missing.";
			echoRespnse(400, $response);
			$app->stop();
		}
	}
	
	/****************************************************************
	*						NO AUTH needed							*
	*****************************************************************/

	/**************************************************************
	*	Registration
	*	url - /register
	*	method - POST
	*	params - username, password, confirm, name, email, gender
	*			 dob, heightft, heightinches, weight, location,
	*			 stepgoal
	**************************************************************/	
	
	$app->post('/register', function() use ($app) 
	{
		global $con, $host, $user, $password, $db, $valid_chars;
		
		// check for required params
        verifyRequiredParams(array('username', 'password', 'confirm', 'name', 'email', 'gender',
		'dob', 'heightft', 'heightinches', 'weight', 'location', 'stepgoal'));
		
		$response = array();
		
		//POST Variables
		$username = mysqli_real_escape_string($con, $app->request->post('username'));
		$pwd = mysqli_real_escape_string($con, $app->request->post('password'));
		$confirm = mysqli_real_escape_string($con, $app->request->post('confirm'));
		$name = mysqli_real_escape_string($con, $app->request->post('name'));
		$email = mysqli_real_escape_string($con, $app->request->post('email'));
		$gender = mysqli_real_escape_string($con, $app->request->post('gender'));
		$dob = mysqli_real_escape_string($con, $app->request->post('dob'));
		$heightft = mysqli_real_escape_string($con, $app->request->post('heightft'));
		$heightinches = mysqli_real_escape_string($con, $app->request->post('heightinches'));
		$height = $heightft . " ft " . $heightinches . " in ";
		$weight = mysqli_real_escape_string($con, $app->request->post('weight'));
		$location = mysqli_real_escape_string($con, $app->request->post('location'));
		$stepgoal = mysqli_real_escape_string($con, $app->request->post('stepgoal'));
		
		// validating email address
        validateEmail($email);
		
		//SQL Query
		$sql="SELECT * FROM users WHERE Username = '$username' OR Email = '$email'";
		$result = mysqli_query($con, $sql);
	
		//Count Amount of rows.
		$count=mysqli_num_rows($result);		
		mysqli_free_result($result);
		
		//Display Error if Password and confirm password do not match.
		if ($pwd != $confirm)
		{
			$response["error"] = true;
			$response["message"] = "Password and Confirm Password do not match.";
		}
		//Display Error if email or username have already been registered.
		elseif ($count >= 1)
		{
			$response["error"] = true;
            $response["message"] = "Sorry, this username or email already exists.";
		}
		else
		{
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
				$response["error"] = false;
                $response["message"] = "You are successfully registered";	
			}
			//Mysql Error.
			else
			{
				die('Error: ' . mysqli_error($con));
			}
		}
		
			// echo json response
            echoRespnse(201, $response);
	
	});
	
	/**************************************************************
	*	Login
	*	url - /register
	*	method - POST
	*	params - username, password
	**************************************************************/	
	
	$app->post('/login', function() use ($app) {
		
			global $con, $host, $user, $password, $db, $valid_chars;
		
            // check for required params
            verifyRequiredParams(array('username', 'password'));
			
			// POST Vars
			$username = mysqli_real_escape_string($con, $app->request->post('username'));
			$pwd = mysqli_real_escape_string($con, $app->request->post('password'));
			
			$response = array();
			
			// SQL Query
			$sql="SELECT * FROM users WHERE Username='$username'";
			$result=mysqli_query($con, $sql);
			
			//Get Number of rows in DB.
			$count=mysqli_num_rows($result);
			
			//
			if($count == 1)
			{
			  while ($row = mysqli_fetch_array($result))
			  {
				$hash = hash('sha256', $row['Salt'] . hash('sha256', $pwd));
				
				//Incorrect Password entered. Display Error and redirect to login page.
				if($hash != $row['Password'])
				{
					$response["error"] = true;
					$response["message"] = "You entered an incorrect password. Please try again.";
				}
				// If Password is correct. Defines Session variables.
				else
				{
					$response["error"] = false;
					$response['id'] = $row['ID'];
                    $response['username'] = $row['Username'];
                    $response['email'] = $row['Email'];
                    $response['api_key'] = $row['API_Key'];
				}
			  }
			}
			//Display Error if incorrect username is entered.
			else 
			{
				$response["error"] = true;
				$response["message"] = "You entered an incorrect username. Please try again.";
			}
			
			echoRespnse(200, $response);

	});
	
	/****************************************************************
	*							AUTH needed							*
	*****************************************************************/
	
	/**************************************************************
	*	Get step count
	*	url - /steps/:frequency
	*	method - GET
	*	params: None
	**************************************************************/
	
	$app->get('/steps/:frequency', 'authenticate', function($frequency) 
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		if ($frequency == "daily")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
		}
		else if ($frequency == "weekly")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND YEARWEEK(Date) = YEARWEEK(NOW()) ORDER BY Date ASC";
		}
		else if ($frequency == "monthly")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND MONTH(Date) = MONTH(NOW()) AND YEAR(Date) = YEAR(NOW()) ORDER BY Date ASC";
		}
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$response["error"] = true;
				$response["message"] = "No results found.";
			}
			else if ($rowcount == 1)
			{
				$response["error"] = false;
				
				while ($rows = mysqli_fetch_array($result))
				{
					$response["data"] = $rows['Data'];
					$response["date"] = date("m/d/Y h:i a", strtotime($rows['Date']));
				}
			}
			else
			{
				
				$response["error"] = false;
				$response["steps"] = array();
				
				while ($rows = mysqli_fetch_array($result))
				{
					$temp_array = array();
					$temp_array["id"] = $rows['ID'];
					$temp_array["data"] = $rows['Data'];
					$temp_array["date"] = date("m/d/Y h:i a", strtotime($rows['Date']));
					array_push($response["steps"], $temp_array);
				}
			}
			
			mysqli_free_result($result);
			
			
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}	
		
		echoRespnse(200, $response);
	});
	
	/**************************************************************
	*	Insert step count
	*	url - /steps
	*	method - POST
	*	params: data
	**************************************************************/
	
	$app->post('/steps', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		// check for required params
        verifyRequiredParams(array('data'));
		
		$data = mysqli_real_escape_string($con, $app->request->post('data'));
		
		$query = "SELECT * FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$query2 = "INSERT INTO sensors (users_UserID, Type, Data) VALUES ('$user_id', 'Steps', '$data')";
				
				//Execute Query
				if (mysqli_query($con, $query2))
				{
					$response["error"] = false;
					$response["message"] = "Successfully Inserted.";	
				}
				else
				{
					$response["error"] = true;
					$response["message"] = "An error occured try again.";
				}
				
			}
			else
			{
				$query2 = "UPDATE sensors SET Data = '".$data."', Date =  NOW() WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
				//Execute Query
				if (mysqli_query($con, $query2))
				{
					$response["error"] = false;
					$response["message"] = "Entry already existed. Record was updated instead.";	
				}
				else
				{
					$response["error"] = true;
					$response["message"] = "An error occured try again.";
				}
				
			}
			
			mysqli_free_result($result);
			
		}
		else
		{
			die('Error:' . mysqli_error($con));
		}

		// echo json response
        echoRespnse(201, $response);
		
	});
	
	/**************************************************************
	*	Update step count
	*	url - /steps
	*	method - PUT
	*	params: data
	**************************************************************/
	
	$app->put('/steps', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		// check for required params
        verifyRequiredParams(array('data'));
		
		$data = mysqli_real_escape_string($con, $app->request->post('data'));
		
		$query = "UPDATE sensors SET Data = '".$data."', Date =  NOW() WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
		//Execute Query
		if (mysqli_query($con, $query))
		{
			$response["error"] = false;
			$response["message"] = "Record was updated successfully.";	
		}
		else
		{
			$response["error"] = true;
			$response["message"] = "An error occured try again.";
		}
		
		echoRespnse(200, $response);
		
	});
	
	/**************************************************************
	*	Delete step count
	*	url - /steps
	*	method - DELETE
	*	params: None
	**************************************************************/
	
	$app->delete('/steps', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		$query = "DELETE FROM sensors WHERE Type = 'Steps' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
		//Execute Query
		if (mysqli_query($con, $query))
		{
			$response["error"] = false;
			$response["message"] = "Record was deleted successfully.";	
		}
		else
		{
			$response["error"] = true;
			$response["message"] = "An error occured try again.";
		}
		
		echoRespnse(200, $response);
		
	});
	
	/**************************************************************
	*	Get heart beat
	*	url - /hb/:frequency
	*	method - GET
	*	params: None
	**************************************************************/
	
	$app->get('/hb/:frequency', 'authenticate', function($frequency) 
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		if ($frequency == "daily")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
		}
		else if ($frequency == "weekly")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND YEARWEEK(Date) = YEARWEEK(NOW()) ORDER BY Date ASC";
		}
		else if ($frequency == "monthly")
		{
			$query = "SELECT * FROM sensors WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND MONTH(Date) = MONTH(NOW()) AND YEAR(Date) = YEAR(NOW()) ORDER BY Date ASC";
		}
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$response["error"] = true;
				$response["message"] = "No results found.";
			}
			else if ($rowcount == 1)
			{
				$response["error"] = false;
				
				while ($rows = mysqli_fetch_array($result))
				{
					$response["data"] = $rows['Data'];
					$response["date"] = date("m/d/Y h:i a", strtotime($rows['Date']));
				}
			}
			else
			{
				
				$response["error"] = false;
				$response["steps"] = array();
				
				while ($rows = mysqli_fetch_array($result))
				{
					$temp_array = array();
					$temp_array["id"] = $rows['ID'];
					$temp_array["data"] = $rows['Data'];
					$temp_array["date"] = date("m/d/Y h:i a", strtotime($rows['Date']));
					array_push($response["steps"], $temp_array);
				}
			}
			
			mysqli_free_result($result);
			
			
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}	
		
		echoRespnse(200, $response);
	});
	
	/**************************************************************
	*	Insert heart beat
	*	url - /hb
	*	method - POST
	*	params: data
	**************************************************************/
	
	$app->post('/hb', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		// check for required params
        verifyRequiredParams(array('data'));
		
		$data = mysqli_real_escape_string($con, $app->request->post('data'));
		
		$query = "SELECT * FROM sensors WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."' LIMIT 1";
		
		if($result = mysqli_query($con, $query))
		{
			$rowcount=mysqli_num_rows($result);
			
			if ($rowcount == 0)
			{
				$query2 = "INSERT INTO sensors (users_UserID, Type, Data) VALUES ('$user_id', 'HB', '$data')";
				
				//Execute Query
				if (mysqli_query($con, $query2))
				{
					$response["error"] = false;
					$response["message"] = "Successfully Inserted.";	
				}
				else
				{
					$response["error"] = true;
					$response["message"] = "An error occured try again.";
				}
				
			}
			else
			{
				$query2 = "UPDATE sensors SET Data = '".$data."', Date =  NOW() WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
				//Execute Query
				if (mysqli_query($con, $query2))
				{
					$response["error"] = false;
					$response["message"] = "Entry already existed. Record was updated instead.";	
				}
				else
				{
					$response["error"] = true;
					$response["message"] = "An error occured try again.";
				}
				
			}
			
			mysqli_free_result($result);
			
		}
		else
		{
			die('Error:' . mysqli_error($con));
		}

		// echo json response
        echoRespnse(201, $response);
		
	});
	
	/**************************************************************
	*	Update heart beat
	*	url - /hb
	*	method - PUT
	*	params: data
	**************************************************************/
	
	$app->put('/hb', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		// check for required params
        verifyRequiredParams(array('data'));
		
		$data = mysqli_real_escape_string($con, $app->request->post('data'));
		
		$query = "UPDATE sensors SET Data = '".$data."', Date =  NOW() WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
		//Execute Query
		if (mysqli_query($con, $query))
		{
			$response["error"] = false;
			$response["message"] = "Record was updated successfully.";	
		}
		else
		{
			$response["error"] = true;
			$response["message"] = "An error occured try again.";
		}
		
		echoRespnse(200, $response);
		
	});
	
	/**************************************************************
	*	Delete heart beat
	*	url - /hb
	*	method - DELETE
	*	params: None
	**************************************************************/
	
	$app->delete('/hb', 'authenticate', function() use ($app)
	{
		global $con, $host, $user, $password, $db, $valid_chars, $user_id;
        $response = array();
		
		$query = "DELETE FROM sensors WHERE Type = 'HB' AND users_UserID = '".$user_id."' AND Date >= '".date('Y-m-d').' 00:00:00'."' AND Date < '".date('Y-m-d').' 23:59:59'."'";
				
		//Execute Query
		if (mysqli_query($con, $query))
		{
			$response["error"] = false;
			$response["message"] = "Record was deleted successfully.";	
		}
		else
		{
			$response["error"] = true;
			$response["message"] = "An error occured try again.";
		}
		
		echoRespnse(200, $response);
		
	});
	
	/**
	* Verifying required params posted or not
	*/
	function verifyRequiredParams($required_fields) 
	{
		$error = false;
		$error_fields = "";
		$request_params = array();
		$request_params = $_REQUEST;
		
		// Handling PUT request params
		if ($_SERVER['REQUEST_METHOD'] == 'PUT') 
		{
			$app = \Slim\Slim::getInstance();
			parse_str($app->request()->getBody(), $request_params);
		}
		
		foreach ($required_fields as $field) 
		{
			if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) 
			{
				$error = true;
				$error_fields .= $field . ', ';
			}
		
		}

		if ($error) 
		{
			
			// Required field(s) are missing or empty
			// echo error json and stop the app
			$response = array();
			$app = \Slim\Slim::getInstance();
			$response["error"] = true;
			$response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
			echoRespnse(400, $response);
			$app->stop();
		}
    }

	/**
	* Validating email address
	*/
	function validateEmail($email) 
	{
		$app = \Slim\Slim::getInstance();
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$response["error"] = true;
			$response["message"] = 'Email address is not valid';
			echoRespnse(400, $response);
			$app->stop();
		}
	}
	
	// Echo JSON to client
	function echoRespnse($status_code, $response) 
	{
		$app = \Slim\Slim::getInstance();
		// Http response code
		$app->status($status_code);

		// setting response content type to json
		$app->contentType('application/json');

		echo json_encode($response);
	}
	
	function validateAPIKey ($api_key)
	{
		// Global variables provided by config.php for DB connection.
		global $con, $host, $user, $password, $db;
		
		if (mysqli_connect_errno())
		{
			die('Could not connect: ' . mysqli_connect_error());
		};
		
		$query = "SELECT ID from users WHERE API_Key = '$api_key'";
		
		if($result = mysqli_query($con, $query))
		{
			$num_rows = mysqli_num_rows($result);
			
			mysqli_free_result($result);
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}
		
		return $num_rows > 0;
	}
	
	function getUserId ($api_key)
	{
		// Global variables provided by config.php for DB connection.
		global $con, $host, $user, $password, $db;
		
		if (mysqli_connect_errno())
		{
			die('Could not connect: ' . mysqli_connect_error());
		};
		
		$query = "SELECT ID from users WHERE API_Key = '$api_key'";
		
		if($result = mysqli_query($con, $query))
		{
			while ($rows = mysqli_fetch_array($result))
			{
				// Insert Data into array.
				$user_id = $rows['ID'];			
			}
			
			mysqli_free_result($result);
		}
		//Die with MySQL Error.
		else
		{
			die('Error:' . mysqli_error($con));
		}
		
		return $user_id;
	}

$app->run();
	
?>