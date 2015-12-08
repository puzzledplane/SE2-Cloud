<?php

#########################################
##      Database Configuration         ##
#########################################

####################################
##      Database Information      ##
##   Edit to your own information ##
####################################

$dbhost = "localhost"; ## Database's IP or Hostname	
$dbuser = "root"; ## Username of Database
$dbpassword = ""; ## Password of Database
$dbname = ""; ## Database's name.
$dbsite_url = ""; //Leave out trailing backslash.

####################################
##   End of Database Information  ##
####################################

####################################
##       Database Connection      ##
####################################

$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

####################################
##   End of Database Connection   ##
####################################

?>