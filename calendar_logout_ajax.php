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
session_destroy();
echo json_encode(array(
	"success" => true
));
exit;
?>