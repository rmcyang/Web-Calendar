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
		$title = htmlentities($_POST['title']);
		$date = htmlentities($_POST['date']);
		$time = htmlentities($_POST['time']);
		$category = htmlentities($_POST['category']);
		// // token debug
		// echo json_encode(array(
		// 	"postToken" => $_POST['token'], 
		// 	"sessToken" => $_SESSION['token'],
		// 	"sessUsername" => $_SESSION['username'])); 
		// exit;
		// get user id
		$query = "select id from users where name=?";
		$stmt = $mysqli->prepare($query);
		if(!$stmt){
			echo json_encode(array(
	            "success" => false,
	            "message" => "Query failed"
	        ));
			exit;
		}
		$stmt -> bind_param('s', $username);
		$stmt -> execute();
		$result = $stmt -> get_result();
		$row = $result -> fetch_assoc();
		$user_id = $row['id'];
		$stmt -> close();
		// insert event into database
		if($title!=""||$date!==""||$time!=""){
		$query = "insert into events (title, date, time, category, user_id) values (?, ?, ?, ?, ?)";
		$stmt = $mysqli->prepare($query);
		if(!$stmt){
	        echo json_encode(array(
	            "success" => false,
	            "message" => "Query failed"
	            ));
	        exit;
		}
		$stmt->bind_param('ssssi', $title, $date, $time, $category, $user_id);
		$stmt->execute();
		$stmt->close();
		// output json array
		echo json_encode(array(
	    	"success" => true
		));
		exit;
		}
	}
// }
?>