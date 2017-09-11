<?php
// connect to the database named "calendar"
 
$mysqli = new mysqli('localhost', 'root', 'Richard19920422*', 'calendar');
 
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>