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

	if(isset($_GET['sort'])){
		$sort = $_GET['sort'];
	}
	else {
		$sort = "last_name";
	}
	
	if(isset($_GET['dir'])){
		$asc = $_GET['dir'];
	}
	else{
		$asc = "ASC";
	}
	
	if(isset($_GET['status']) && ($_GET['status'] != "")){
		$stat = sprintf(" AND `users`.`statusID` = %d ", $_GET['status']);
		$stat_no = $_GET['status'];
	}
	else{
		$stat = "";
		$stat_no = "";
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
<h2>Admin Panel</h2>
<h3>Show Only: <a href='?&status='>All</a> <a href='?&status=0'>Active</a> <a href='?&status=1'>Semi-Active</a> <a href='?&status=2'>Inactive</a> <a href='?&status=3'>Suspended</a> <a href='?&status=4'>Alumni</a> <a href='?&status=5'>Interns</a> <a href='?&status=6'>Non-Members</a> <a href='?&status=7'>Banned</a></h3>
<p>
<?php
	//Query to get show subs
	$query = "SELECT `username`, `first_name`, `last_name`, `preferred_name`, `email_addr`, `cuid`, `def_teams`.`team`, `def_status`.`status` ". 
			"FROM `users`, `def_teams`, `def_status` ".
			"WHERE `users`.`teamID` = `def_teams`.`teamID` AND `users`.`statusID` = `def_status`.`statusID` $stat".
			"ORDER BY `$sort` $asc";
	
	//Submit Query
	$list = mysql_query($query, $link);
	//If query returns FALSE, no albums were returned.  Die with error
	if (!$list) {
		die ('No sub requests returned: ' . mysql_error());
	}
	
	//Formatting table
	echo "<style type=\"text/css\"> table.bottomBorder td, table.bottomBorder th { border-bottom:1px white; } </style>";
	echo "<table class = \"bottomBorder\">";
	echo "<tr><th style=\"text-align:center\">Username<a href='?sort=username&dir=ASC&status=$stat_no'>^</a><a href='?sort=username&dir=DESC&status=$stat_no'>v</a></th><th style=\"text-align:center\">First Name<a href='?sort=first_name&dir=ASC&status=$stat_no'>^</a><a href='?sort=first_name&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">Last Name<a href='?sort=last_name&dir=ASC&status=$stat_no'>^</a><a href='?sort=last_name&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">Preferred Name<a href='?sort=preferred_name&dir=ASC&status=$stat_no'>^</a><a href='?sort=preferred_name&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">Email Address<a href='?sort=email_addr&dir=ASC&status=$stat_no'>^</a><a href='?sort=email_addr&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">CUID<a href='?sort=cuid&dir=ASC&status=$stat_no'>^</a><a href='?sort=cuid&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">Team<a href='?sort=team&dir=ASC&status=$stat_no'>^</a><a href='?sort=team&dir=DESC&status=$stat_no'>v</a>
		</th><th style=\"text-align:center\">Status<a href='?sort=status&dir=ASC&status=$stat_no'>^</a><a href='?sort=status&dir=DESC&status=$stat_no'>v</a>
		</th></tr>";
	
	//Get row from SQL Query, populate tables with albums
	while ($row = mysql_fetch_array($list, MYSQL_NUM)){
		$username = $row[0];
		$first = $row[1];
		$last = $row[2];
		$pref = $row[3];
		$email = $row[4];
		$cuid = $row[5];
		$team = $row[6];
		$status = $row[7];
		
		if($team == "None"){
			$bg = "white";
			$text = "black";
		}
		else{
			$bg = $team;
			$text = "white";
		}
			
		echo "<tr style=\"text-align:center; background-color:$bg; color:$text;\">";
		
		echo "<td>$username</td><td>$first</td><td>$last</td><td>$pref</td><td><a style='text-align:center; background-color:$bg; color:$text; text-decoration:none !important;' href='mailto:$email'>$email</a></td><td>$cuid</td><td>$team</td><td>$status</td>";
		
		echo "</tr>";
	}
		
	echo "</table>";
?>	
</div>

</p>

</div>
</body>
</html>
