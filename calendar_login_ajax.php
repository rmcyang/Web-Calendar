<?php
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
// Check if intput arguments are valid
require 'calendar_database.php';
$stmt = $mysqli->prepare("SELECT COUNT(*), id, crypted_password FROM users WHERE name=?");
if(!$stmt){
	printf("Query Prep Failed: %s\n", $mysqli->error);
	exit;
}
$stmt->bind_param('s', $user);
$user = htmlentities($_POST['username']);
$stmt->execute();
$stmt->bind_result($cnt, $id, $pwd_hash);
$stmt->fetch();
$pwd_guess = htmlentities($_POST['password']);
// check if username and password are correct
if( $cnt == 1 && crypt($pwd_guess, $pwd_hash)==$pwd_hash) {
	$_SESSION['username'] = $user;
	$_SESSION['token'] = substr(md5(rand()), 0, 10);
	echo json_encode(array(
		"success" => true,
		"username" => $user
	));
	exit;
}else{
	echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}
$stmt -> close();
?>