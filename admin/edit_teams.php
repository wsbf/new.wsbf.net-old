<?php
if(!session_id())
	session_start();
require_once('../header.php');
require_once('../conn.php');
include ('../position_check.php');
if(empty($_SESSION['username'])){  //gotta be logged in
	die ('You need to login first!<br><a href="/login">Click here to login!</a>');
}
if(!(positionCheck("seniorstaff"))){
	die('Go away!');
		
}

	if(isset($_GET['team'])){
		$team_view = $_GET['team'];
		//Query to get show subs
		if(($team_view == '0') || ($team_view == '1') || ($team_view == '2') || ($team_view == '3') || ($team_view == '4')){
		$query = sprintf("SELECT `first_name`, `last_name`, `preferred_name`, `def_teams`.`team`, `def_status`.`status`, `username`". 
		"FROM `users`, `def_teams`, `def_status`".
		"WHERE `users`.`teamID` = %d AND `users`.`teamID` = `def_teams`.`teamID` AND `users`.`statusID` = `def_status`.`statusID` ORDER BY `status` ASC", $team_view);
		}
		else{
			//Query to get show subs
			$query = "SELECT `first_name`, `last_name`, `preferred_name`, `def_teams`.`team`, `def_status`.`status`, `username`". 
			"FROM `users`, `def_teams`, `def_status`".
			"WHERE `users`.`teamID` = `def_teams`.`teamID` AND `users`.`statusID` = `def_status`.`statusID` ORDER BY `team`, `status` ASC";
		}
	}
	else{
		//Query to get show subs
		$query = "SELECT `first_name`, `last_name`, `preferred_name`, `def_teams`.`team`, `def_status`.`status`, `username`". 
		"FROM `users`, `def_teams`, `def_status`".
		"WHERE `users`.`teamID` = `def_teams`.`teamID` AND `users`.`statusID` = `def_status`.`statusID` ORDER BY `team`, `status` ASC";
	}
	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WSBF: Admin Panel</title>
</head>
<body>
<div id="container">
	<center><h1>WSBF-FM Clemson</h1></center>
<h2>Edit Teams</h2>
<h3>Show Only: <a href='?&team='>All</a> <a href='?&team=0'>No Team</a> <a href='?&team=1'>Blue</a> <a href='?&team=2'>Red</a> <a href='?&team=3'>Green</a> <a href='?&team=4'>Purple</a></h3>
<p>
<?php

	
	$i = 0;
	//Submit Query
	$list = mysql_query($query, $link);
	//If query returns FALSE, no albums were returned.  Die with error
	if (!$list) {
		die ('No sub requests returned: ' . mysql_error());
	}
	
	//Formatting table
	echo "<form action='edit_team_submit.php' method='POST'>";
	echo "<input type='submit' value='Submit Changes' />";
	echo "<style type=\"text/css\"> table.bottomBorder td, table.bottomBorder th { border-bottom:1px white; } </style>";
	echo "<table class = \"bottomBorder\">";
	echo "<tr><th style=\"text-align:center\">No.</th><th style=\"text-align:center\">First Name</th>
		<th style=\"text-align:center\">Last Name</th>
		<th style=\"text-align:center\">Preferred Name</th>
		<th style=\"text-align:center\">Team</th>
		<th style=\"text-align:center\">Status</th>";
	
	//Get row from SQL Query, populate tables with albums
	while ($row = mysql_fetch_array($list, MYSQL_NUM)){
		
		$first = $row[0];
		$last = $row[1];
		$pref = $row[2];
		$team = $row[3];
		$status = $row[4];
		$username = $row[5];
		$i++;
		//get the teams
		$team_query = "SELECT teamID, team FROM def_teams ORDER BY teamID DESC";
		$team_list = mysql_query($team_query, $link);
		if (!$team_list) {
			die ('This is an error message: ' . mysql_error());
		}
		$team_combo = "<select name='team_".$username."'>";
		while ($team_get = mysql_fetch_array($team_list, MYSQL_NUM)){
			
			$teamID = $team_get[0];
			$team_name = $team_get[1];
			
			$team_combo .= "\t<option";
			
			if($team_name == $team){
				
				$team_combo .= " selected=\"true\"";  //selects the airabilityID for each, default is No Air
				
			}	
			$team_combo .= " value=\"$teamID\">$team_name</option>\r";
			
		}
		
		$team_combo .= "</select>\n";
		
		//get the status
		$status_query = "SELECT statusID, status FROM def_status ORDER BY statusID ASC";
		$status_list = mysql_query($status_query, $link);
		if (!$status_list) {
			die ('This is an error message: ' . mysql_error());
		}
		$status_combo = "<select name='status_".$username."'>";
		while ($status_get = mysql_fetch_array($status_list, MYSQL_NUM)){
			
			$statusID = $status_get[0];
			$status_name = $status_get[1];
			
			$status_combo .= "\t<option";
			
			if($status_name == $status){
				
				$status_combo .= " selected=\"true\"";  //selects the airabilityID for each, default is No Air
				
			}	
			$status_combo .= " value=\"$statusID\">$status_name</option>\r";
			
		}
		
		$status_combo .= "</select>\n";
		

		
		if($team == "None"){
			$bg = "white";
			$text = "black";
		}
		else{
			$bg = $team;
			$text = "white";
		}
			

		echo "<tr style=\"text-align:center; background-color:$bg; color:$text;\">";
		
		echo "<td>$i.</td><td>$first</td><td>$last</td><td>$pref</td><td>$team_combo</td><td>$status_combo</td>";
		
		
		echo "</tr>";
	}
		
	echo "</table></form>";
	
?>	
</div>

</p>

</div>
</body>
</html>
