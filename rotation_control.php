<?php
	
	require_once("connect.php");
	include ("position_check.php");
	if(!session_id()) session_start();
	
	if(empty($_SESSION['username'])){
		die('You need to login first!<br><a href="login.php">Click here to login!</a>');
	}
	
	if(empty($_GET)){
		$rot = 7;
	}
	else {
		$rot = $_GET["rotation"];
	}
	
	if(!(MD_check())){
		die('<br>You have to be a music director to print labels and move rotation!<br>');
	}	
	
echo "
<script type='text/javascript'>

var albumids = new Array();
function append(app) {
	
	document.getElementById('output').innerHTML += ' ' + app + ' ';
	albumids.push(app);
	
	if(albumids.length == 4) {
		var get = 'printlabel.php?';
		
		for(i=1; i<=4; i++)
			get += 'a' + i + '=' + albumids[i-1] + '&';
		window.location.href = get;
		clearA();
	}
}
function clearA() {
	albumids.length = 0;
	document.getElementById('output').innerHTML = \"&nbsp;\";
}
</script>";
	



	
	echo "<br><font size = 28>WSBF Rotation Control</font><br>Click 4 CDs to print labels, move rotation, or <a href=\"logout.php\">logout!</a> OR MOVE SHIT IN <a href=\"bulk_move.php\">BULK</a>";
	echo "<br><a href='#' onclick='clearA()'>Clear clicked albums</a><br>
	<span id='output'>&nbsp;</span>
	<table border='1'>";
	echo "<table border = \"1\" cellpadding=\"3\">";
	
	
	switch($rot) {
		case 7:  //Recently
			echo "<th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=7\"><font color = \"white\">Recently Reviewed</font></a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 1:  //New
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=1\"><font color = \"white\">New</font></a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;
		case 2:  //Heavy
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=2\"><font color = \"white\">Heavy</font></a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;
		case 3:  //Medium
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=3\"><font color = \"white\">Medium</font></a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 4:  //Light
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=4\"><font color = \"white\">Light</font></a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 5:  //Optional
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=5\"><font color = \"white\">Optional</font></a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 6:  //Jazz
			echo "<th><a href=\"rotation_control.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=6\"><font color = \"white\">Jazz</font></a></th></table><br><br>";
			break;
		default: //Default Review Mode: Recently
			echo "<th bgcolor = \"black\"><a href=\"rotation_control.php?rotation=7\"><font color = \"white\">Recently Reviewed</font></a></th><th><a href=\"rotation_control.php?rotation=1\">New</a></th><th><a href=\"rotation_control.php?rotation=2\">Heavy</a></th><th><a href=\"rotation_control.php?rotation=3\">Medium</a></th><th><a href=\"rotation_control.php?rotation=4\">Light</a></th><th><a href=\"rotation_control.php?rotation=5\">Optional</a></th><th><a href=\"rotation_control.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;			
	}
	
	//get the rotations
	$rot_query = "SELECT rotationID, rotation_bin FROM def_rotations";
	$rotations = mysql_query($rot_query, $link);
	if (!$rotations) {
		die ('This is an error message: ' . mysql_error());
	}
	
	//make the combo boxes
	$rotation = "";
	while ($rot_get = mysql_fetch_array($rotations, MYSQL_NUM)){
		
		$rotID = $rot_get[0];
		$bin = $rot_get[1];
		
		$rotation .= "\t<option";
		
		if($rotID == $rot){
			
			$rotation .= " selected=\"true\"";  //selects the airabilityID for each, default is No Air
			
		}	
		$rotation .= " value=\"$rotID\">$bin</option>\r";
		
	}
	
	$rotation .= "</select>\n";
	
	
	
	$query = sprintf("SELECT `libalbum`.`albumID`, `album_code`, `artist_name`, `album_name`, `libreview`.`review_date` FROM `libalbum`, `libartist`, `libreview` WHERE `rotationID` = '%d' AND `libalbum`.`artistID` = `libartist`.`artistID` AND `libalbum`.`albumID` = `libreview`.`albumID` ORDER BY review_date DESC", mysql_real_escape_string($rot));
	
	$list = mysql_query($query, $link);
	if (!$list) {
		die ('No albums returned: ' . mysql_error());
	}
	
	echo "<form action='move_rotation.php' method='POST'>";
	
	echo "<input type='hidden' name='rotation' value='$rot'/>";
	
	echo "<style type=\"text/css\"> table.bottomBorder td, table.bottomBorder th { border-bottom:1px dotted black; } </style>";
	
	echo "<table class = \"bottomBorder\">";
	
	echo "<tr><th style=\"text-align:center\">Album ID</th><th style=\"text-align:center\">Album Code</th><th style=\"text-align:center\">Edit</th><th>Review Date</th><th>Album</th><th>Artist</th><th>Rotation<br><input class='review' type='submit' value='Submit Rotation Changes' /></th></tr>";
	

	
	while($row = mysql_fetch_array($list)){
		  $albumID = $row[0];
		  $album_code = $row[1];
		  $artist_name = $row[2];
		  $album_name = $row[3];
		  $review_date = substr($row[4],0,10);

		  $rotation_box = "<select name='album".$albumID."'>\r" . $rotation;
			
		  echo "<tr><td style=\"text-align:center\">$albumID</td><td style=\"text-align:center\"><a href='#' onclick=\"append('$album_code')\">$album_code</a></td><td><a href=\"review.php?albumID=$albumID&edit=1\">Edit Review</a></td><td>$review_date</td><td>$album_name</td><td>$artist_name</td><td>$rotation_box</td></tr>";
		
	}
	echo "</table></form>";	
	
	?>