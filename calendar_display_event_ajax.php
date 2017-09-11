<?php
header("Content-Type: application/json");
ini_set("session.cookie_httponly", 1);
require 'calendar_database.php';
// agent consistency  
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];
if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
  die("Session hijack detected");
}else{
  $_SESSION['useragent'] = $current_ua;
}
session_start();
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
	// perform query
	$query = "SELECT events.id, title, date, time, category, name FROM (events LEFT JOIN users ON events.user_id=users.id) WHERE users.name=? AND events.date=?";
	$stmt = $mysqli->prepare($query);
	if(!$stmt){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}
	$stmt -> bind_param('ss', trim($_SESSION['username']), htmlentities($_POST['date']));
	$stmt -> execute();
	$result = $stmt -> get_result();
	// output result in array of arrays for json data
	$output_array = array();
	while ($row = $result -> fetch_assoc()) {
		array_push($output_array, array(
			"id" => htmlspecialchars($row['id']),
			"title" => htmlspecialchars($row['title']),
			"date" => htmlspecialchars($row['date']),
			"time" => htmlspecialchars($row['time']),
			"category" => htmlspecialchars($row['category'])
		));
	}
	echo json_encode($output_array);

	exit;
}
?>