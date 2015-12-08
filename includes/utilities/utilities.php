<?php

	// Salt Function -- Source: http://tinsology.net/2009/06/creating-a-secure-login-system-the-right-way/
	function createSalt()
	{
		$string = md5(uniqid(rand(), true));
		return substr($string, 0, 3);
	}
	
	//Valid Chars for access code generation.
		$valid_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		global $called;
		$called = 1;
		
		/*********************************************************************************************
		*                             createAPIKey function                                      *
		*                      By: Chad Birch - Modified By: Derek Campaniello                       *
		* Source: http://stackoverflow.com/questions/853813/how-to-create-a-random-string-using-php  *
		**********************************************************************************************/

		function createAPIKey($valid_chars, $length)
		{
			// Global variables provided by config.php for DB connection.
			global $con, $host, $user, $password, $db, $called;
			
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
			
			//MySQL Query to check if access code exists.
			$query = "SELECT API_Key FROM users WHERE API_Key = '$random_string'";
			//Execute Query.
			$result = mysqli_query($con, $query);
			
			//Get number of rows from DB query.
			$count=mysqli_num_rows($result);
			//Free Result Set.
			mysqli_free_result($result);
			
			//Check if code already exists.
			if ($count != 0)
			{
				$called++;
				createAPIKey($valid_chars, $length);
			}
			//Error if we are out of unique access codes.
			elseif($called >= 5)
			{
				die('Error Code 100: An Error Occurred. Please Contact an Administrator.');
			}
			// return our finished random string
			else
			{
				return $random_string;
			}
		}
?>