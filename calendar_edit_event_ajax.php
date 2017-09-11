<?php
require 'calendar_database.php';
header("Content-Type: application/json"); 
ini_set("session.cookie_httponly", 1);
session_start();
// agent consistency  
$previous_ua = @$_SESSION['useragent'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];
if(isset($_SESSION['useragent']) && $previous_ua !== $current_ua){
  die("Session hijack detected");
}else{
  $_SESSION['useragent'] = $current_ua;
}
// if($_SESSION['token'] !== $_POST['token']){
// 	die("Request forgery detected");
// }
// else{
	if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
		// pass in variables
		$username = $_SESSION["username"];
		$event_id = (int)htmlentities($_POST["eventId"]);
		// query correct user
		$query = "select name from (events left join users on events.user_id=users.id) where events.id=?";
		$stmt = $mysqli->prepare($query);
		if(!$stmt){
	        echo json_encode(array(
	            "success" => false,
	            "message" => "Query failed"
	            ));
	        exit;
		}
		$stmt -> bind_param('i', $event_id);
		$stmt -> execute();
		$result = $stmt -> get_result();
		$row = $result -> fetch_assoc();
		$user_name = $row['name'];
		$stmt -> close();
		// check user credential
		if ($user_name != $username) {
			echo json_encode(array(
	            "success" => false,
	            "message" => "Authorization failed"
	            ));
	        exit;
		}
		// update event in database
		$title = htmlentities($_POST['title']);
		$date = htmlentities($_POST['date']);
		$time = htmlentities($_POST['time']);
		$category = htmlentities($_POST['category']);
		$query = "update events set title=?, date=?, time=?, category=? where id=?";
		$stmt = $mysqli->prepare($query);
		if(!$stmt){
		        echo json_encode(array(
		            "success" => false,
		            "message" => "Query failed"
		            ));
		        exit;
		}
		$stmt->bind_param('ssssi', $title, $date, $time, $category, $event_id);
		$stmt->execute();
		$stmt->close();
		// output json array
		echo json_encode(array(
	     "success" => true,
		));
		exit;
	}
// }
?>