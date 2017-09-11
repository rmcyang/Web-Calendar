/*globals $:false */
// global variables
var months = [ "January", "February", "March", "April", "May", "June",
"July", "August", "September", "October", "November", "December" ];
//var daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var d = new Date();
var rightNow = [d.getDate(), d.getMonth(), d.getFullYear()];
var Month;
var currentMonth = new Month(rightNow[2], rightNow[1]);
var userIsLogged = false;
var selectedCategory = "all";

function showCurrMonth() {
	$(".curr_month").html(months[currentMonth.month] + " " + currentMonth.year);
}

function updateCalTable(month) {
	var weeks = month.getWeeks();
	var table = $("#cal_table")[0];
	// clean up table
	while (table.rows.length > 1) {
		table.deleteRow(1);
	}
	// append each week as a new row to table
	var rowCounter = 1;
  for (var w in weeks) {
	  if (weeks.hasOwnProperty(w)){
		var row = table.insertRow(rowCounter);
		var days = weeks[w].getDates();
		rowCounter += 1;
		// append day to each row
		for (var i = 0; i < 7; i ++) { 
			var cell = row.insertCell(i);
			var tempDay = days[i].getDate();
			if (w <= 0 && tempDay > 22) {
				cell.innerHTML = "";
			} else if (w > 3 && tempDay < 7) {
				cell.innerHTML = "";
			} else {
				// cell.innerHTML = tempDay;
				cell.appendChild(document.createTextNode(tempDay));
				// display events in calendar table
				if (userIsLogged) {
					var tempDate = currentMonth.year +"-"+ ("0"+(Number(currentMonth.month)+1)).slice(-2) +"-"+ ("0"+tempDay).slice(-2);
					displayDayEvent(null, cell, tempDate, tempDay);
				}
				// add background color to today
				if (rightNow[2] == currentMonth.year && rightNow[1] == currentMonth.month &&rightNow[0] == tempDay) {
					cell.style.background = "rgb(67,132,211)";
				}
	    	}
		}
		}
	}
}

function loginAjax(event){
	var username = document.getElementById("login_username").value;
	var password = document.getElementById("login_password").value;
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
	var xmlHttp = new XMLHttpRequest(); 
	xmlHttp.open("POST", "calendar_login_ajax.php", true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText); 
		if(jsonData.success){
			alert("You've been logged in!");
			$("#login_form").hide();
			$("#register_form").hide();
			$("#logout_form").show();
			$(".user_manage").show();
			$("#category_form").show();
			userIsLogged = true;
			updateCalTable(currentMonth);
		}else{
			alert("You were not logged in.  "+jsonData.message);
		}
	}, false);
	xmlHttp.send(dataString);
}

function registerAjax(event){
	var username = document.getElementById("register_username").value; 
	var password = document.getElementById("register_password").value; 
	var dataString = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
	var xmlHttp = new XMLHttpRequest(); 
	xmlHttp.open("POST", "calendar_register_ajax.php", true); 
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText);
		if(jsonData.success){
			alert("You've been registered!");
		}else{
			alert("You were not registered.  "+jsonData.message);
		}
	}, false);
	xmlHttp.send(dataString);
}

function logoutAjax(event){
	var xmlHttp = new XMLHttpRequest(); 
	xmlHttp.open("POST", "calendar_logout_ajax.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText); 
		if(jsonData.success){ 
			alert("You've been logged out!");
			$("#login_form").show();
			$("#register_form").show();
			$("#logout_form").hide();
			$(".user_manage").hide();
			$("#category_form").hide();
			userIsLogged = false;
			updateCalTable(currentMonth);
		}
	}, false); 
	xmlHttp.send(null);
}

function displayDayEvent(event, cell, date, day) {
	// date to pass into display event ajax script 
	var dataString = "date=" + encodeURIComponent(date);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", "calendar_display_event_ajax.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText);
		// if there exists event(s) for that day
		if (jsonData.length > 0) {
			for (var tempEvent in jsonData) { // append each even as child
			if(jsonData.hasOwnProperty(tempEvent)) {
				var tempDetails = jsonData[tempEvent];
				var tempPara = document.createElement("div", {"id": "event_"+tempDetails.id});
				tempPara.appendChild(document.createTextNode(tempDetails.time.split(":").slice(0,2).join(":")+" "));
				tempPara.appendChild(document.createTextNode(tempDetails.title+" "));
				tempPara.appendChild(document.createTextNode("[ID:"+tempDetails.id+"] "));
				// filter events by category
				if (selectedCategory == "all" || tempDetails.category == selectedCategory) {
					var tempEditBtn = document.createElement("BUTTON", {"id": "edit_btn_"+tempDetails.id});
					tempEditBtn.appendChild(document.createTextNode("edit"));
					tempPara.appendChild(tempEditBtn);
					var tempDeleteBtn = document.createElement("button", {"id": "delete_btn_"+tempDetails.id, "type": "button"});
					tempDeleteBtn.appendChild(document.createTextNode("delete"));
					tempPara.appendChild(tempDeleteBtn);
					cell.appendChild(tempPara);
				} 
			}
			}
		}
	}, false);
	xmlHttp.send(dataString);
}

function addEvent(event, title, date, time, category, token) {
	// date to pass into display event ajax script
	var dataString = "title="+encodeURIComponent(title)+"&date="+encodeURIComponent(date)+"&time="+encodeURIComponent(time)+"&category="+encodeURIComponent(category)+"&token="+encodeURIComponent(token);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", "calendar_add_event_ajax.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText);
		// // token debug
		// alert(token+" | "+jsonData.postToken+" | "+ jsonData.sessToken+" | "+jsonData.sessUsername);
		// check event handler status
		if(jsonData.success){
			alert("Your event has been added!");
			updateCalTable(currentMonth);
		}else{
			alert("Adding event failed.  "+jsonData.message);
		}
	}, false);
	xmlHttp.send(dataString);
}

function editEvent(event, eventId, title, date, time, category) {
	// date to pass into display event ajax script 
	var dataString = "eventId="+encodeURIComponent(eventId)+"&title="+encodeURIComponent(title)+"&date="+encodeURIComponent(date)+"&time="+encodeURIComponent(time)+"&category="+encodeURIComponent(category);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", "calendar_edit_event_ajax.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText);
		// check event handler status
		if(jsonData.success){
			alert("Your event has been edited!");
			updateCalTable(currentMonth);
		}else{
			alert("Editing event failed.  "+jsonData.message);
		}
	}, false);
	xmlHttp.send(dataString);
}

function deleteEvent(event, eventId, token) {
	// date to pass into display event ajax script 
	var dataString = "eventId="+encodeURIComponent(eventId)+"&token="+encodeURIComponent(token);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", "calendar_delete_event_ajax.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", function(event){
		var jsonData = JSON.parse(event.target.responseText);
		// check event handler status
		if(jsonData.success){
			alert("Your event has been deleted!");
			updateCalTable(currentMonth);
		}else{
			alert("Deleting event failed.  "+jsonData.message);
		}
	}, false);
	xmlHttp.send(dataString);
}


$(document).ready(function(){
	// toggle logout and event mangement panels
	$("#login_form").show();
	$("#register_form").show();
	$("#logout_form").hide();
	$(".user_manage").hide();
	$("#category_form").hide();

	// user login register and logout panel
	var eventLogin = document.getElementById("login_btn");
	if (eventLogin){
		eventLogin.addEventListener("click", loginAjax, false); 
	}
	var eventRegister = document.getElementById("register_btn");
	if (eventRegister){
		eventRegister.addEventListener("click", registerAjax, false); 
	}
	var eventLogout = document.getElementById("logout_btn");
	if (eventLogout){
		eventLogout.addEventListener("click", logoutAjax, false);
	}

	// current month and year and crate cal table
	showCurrMonth();
	updateCalTable(currentMonth);
	// update to next month
	$("#next_month_btn").click(function(event){
		currentMonth = currentMonth.nextMonth();
		showCurrMonth();
		updateCalTable(currentMonth);
	});
	// update to prev month
	$("#prev_month_btn").click(function(event){
		currentMonth = currentMonth.prevMonth();
		showCurrMonth();
		updateCalTable(currentMonth);
	});

	// add event
	$("#add_event_btn").click(function(event){
		// addEvent(event, $("#add_event_title").value(), $("#add_event_date").value(), $("#add_event_time").value(), $("#add_event_category").value());
		var title = document.getElementById("add_event_title").value;
		var date = document.getElementById("add_event_date").value;
		var time = document.getElementById("add_event_time").value;
		var category = document.getElementById("add_event_category").value;
		var token = document.getElementById("add_event_token").value;
		addEvent(event, title, date, time, category, token);
	});
	// edit event
	$("#edit_event_btn").click(function(event){
		var eventId = document.getElementById("edit_event_id").value;
		var title = document.getElementById("edit_event_title").value;
		var date = document.getElementById("edit_event_date").value;
		var time = document.getElementById("edit_event_time").value;
		var category = document.getElementById("edit_event_category").value;
		var token = document.getElementById("edit_event_token").value;
		editEvent(event, eventId, title, date, time, category, token);
	});
	// delete event
	$("#delete_event_btn").click(function(event){
		var eventId = document.getElementById("delete_event_id").value;
		var token = document.getElementById("delete_event_token").value;
		deleteEvent(event, eventId, token);
	});

	// category selection
	$("#filter_by_category").change(function() {
		selectedCategory = this.value;
		updateCalTable(currentMonth);
	});

	// event button clicked
	// $("button").click(function() {
	// 	alert("total buttons = "+$("button").length+" clicked button = "+this.id);
	// });
});