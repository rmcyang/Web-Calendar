<?php
header("Content-Type: application/json");
require 'calendar_database.php';
$user = htmlentities($_POST['username']);
$pwd = htmlentities($_POST['password']);
// check input arguments
if (isset($user)){
    if (empty($user)){
    	echo json_encode(array(
			"success" => false,
			"message" => "Empty username!"
		));
     }
    elseif (!preg_match('/^[\w_\-]+$/', $user)) {
    	echo json_encode(array(
			"success" => false,
			"message" => "No special character!"
		));
	} else {
		// make database query
		$query = "SELECT COUNT(*), id FROM users WHERE name=?";
		$stmt = $mysqli -> prepare($query);
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
		    exit;
		}
		$stmt -> bind_param('s', $user);
		$stmt -> execute();
		$stmt -> bind_result($cnt, $id);
		$stmt -> fetch();
		$stmt -> close();
		// check if there exits registered username
		if ($cnt == 1) {
			echo json_encode(array(
				"success" => false,
				"message" => "Username is already registered!"
			));
		} else if($cnt == 0){
			$pwd_hash = crypt($pwd);
			$query = "insert into users (name, crypted_password) values (?, ?)";
			$stmt = $mysqli->prepare($query);
			if(!$stmt){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$stmt -> bind_param('ss', $user, $pwd_hash);
		 	$stmt -> execute();
		 	$stmt -> close();
			echo json_encode(array(
				"success" => true
			));
		}
	}
}
?>