<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Calendar</title>
	<style type="text/css">
	body{ font:12px/16px Verdana, sans-serif; background-image: url("marble.jpg");}
	h1{ font-size: 3em; font-weight: 700; padding: 0 0 20px 0; text-align:center; }
	.user_admin { text-align: right; padding: 40px 40px 0 0; }
	.user_manage { text-align: left; padding: 20px 20px 20px 40px; }
	.month_nav { text-align: center; }
	.cal_table { margin: 0 auto; padding: 20px;}
	table{ border: 1px solid black; table-layout: fixed; }
	th { font-weight: 700; height: 20px; }
	/*td { border: 1px solid black; overflow: hidden; width: 150px; height: 100px; vertical-align: top; text-align: left; }*/
	td { border: 1px solid black; width: 175px; height: 100px; vertical-align: top; text-align: left; overflow-y: auto; }
	thead { height: 50px; } 
	#category_form { padding: 20px; }
	</style>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script type="text/javascript" src="http://classes.engineering.wustl.edu/cse330/content/calendar.min.js"></script>
	<script type="text/javascript" src="calendar_ajax.js?v=2"></script>

</head>

<body>
	<!-- user login, register, logout panel -->
	<div class="user_admin">
    <?php
    require "calendar_database.php";
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
    // create token for user credential
    $_SESSION['token'] = substr(md5(rand()), 0, 10);
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
      // $_SESSION['token'] = substr(md5(rand()), 0, 10);
    } else {
    ?>
    <div id="login_form">
      <form name = "login">
      Already have an account?<br>
      username:<input type = "text" name = "username" id="login_username"/>
      password:<input type = "password" name = "password" id="login_password"/><br>
      <button type="button" id="login_btn">Login</button>
      </form>
    </div>
    <div id="register_form">
      <form name = "register">
      Register to add and share events now!<br>
      username:<input type = "text" name = "username" id="register_username"/> 
      password:<input type = "password" name = "password" id="register_password"/> <br>
      <button type="button" id="register_btn">Register</button>
      </form>
    </div>
    <div id="logout_form">
      <form name="input">
      <button type="button" id="logout_btn">Logout</button>
      </form>
    </div>
    <div id="category_form">
      <form>Filter by Category:<br>
      <select name="category" id="filter_by_category">
        <option value="all" selected="selected">all</option>
        <option value="home">home</option>
        <option value="school">school</option>
        <option value="work">work</option>
        <option value="other">other</option>
      </select>
      </form>
    </div>
    <?php  
    }
    ?>
  </div>
  <!-- calendar table -->
  <div class="month_nav">
    <h1 class="curr_month">1</h1>
    <button id="prev_month_btn">Prev Month</button>
    <button id="next_month_btn">Next Month</button>
  </div>
  <div class="cal_table">
    <table id="cal_table">
      <tr><th>Sunday</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th></tr>
    </table>
  </div>
  <!-- user even management panel -->
  <div class="user_manage">
    <h1>Event Manager</h1>
    <form name="add_event" id="add_event_form">
      --- Add Event ---<br>
      Content:<input type="text" name="event" id="add_event_title"/>
      Date:<input type="text" name="date" id="add_event_date" placeholder="YYYY-MM-DD"/>
      Time:<input type="text" name="time" id="add_event_time" placeholder="HH:MM"/>
      Category:
      <select name="category" id="add_event_category">
        <option value="home">home</option>
        <option value="school">school</option>
        <option value="work">work</option>
        <option value="other">other</option>
      </select>
      <input type="hidden" id="add_event_token" name="token" value="<?php echo $_SESSION['token'];?>"/>
      <button type="button" id="add_event_btn">Add</button>
    </form><br>
    <form name="edit_event" id="editevent_form">
      --- Edit Event ---<br>
      Event ID:<input type="text" name="event" id="edit_event_id"/>
      Content:<input type="text" name="event" id="edit_event_title"/>
      Date:<input type="text" name="date" id="edit_event_date" placeholder="YYYY-MM-DD"/>
      Time:<input type="text" name="time" id="edit_event_time" placeholder="HH:MM"/>
      Category:
      <select name="category" id="edit_event_category">
        <option value="home">home</option>
        <option value="school">school</option>
        <option value="work">work</option>
        <option value="other">other</option>
      </select>
      <input type="hidden" id="edit_event_token" name="token" value="<?php echo $_SESSION['token'];?>"/>
      <button type="button" id="edit_event_btn">Edit</button>
    </form><br>
    <form name="delete_event" id="delete_event_form">
      --- Delete Event ---<br>
      Event ID:<input type="text" name="event" id="delete_event_id"/>
      <input type="hidden" id="delete_event_token" name="token" value="<?php echo $_SESSION['token'];?>"/>
      <button type="button" id="delete_event_btn">Delete</button>
    </form>
	</div>
	
</body>
</html>
