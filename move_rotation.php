<?php

	session_start();
	require_once("connect.php");
	include ("position_check.php");
	
	
	if(empty($_SESSION['username'])){
		
		die('You need to login first!<br><a href="login.php">Click here to login!</a>');
	}
	
	if(!(MD_check())){
		die('<br>You have to be a music director to move rotation!<br>');
	}	
	
	if(isset($_POST['rotation'])) {
		$rot = $_POST['rotation'];
	}
	else{
		die('<br>You seem to have gotten here from somewhere you shouldn\'t have. Move along now.<br>');
	}
	
	$query = sprintf("SELECT albumID FROM libalbum WHERE rotationID = '%d'", mysql_real_escape_string($rot));
	
	$list = mysql_query($query, $link);
	if (!$list) {
		die ('No albums returned: ' . mysql_error());
	}
	
	while($row = mysql_fetch_array($list)){
		$albumID = $row[0];
		
		if(isset($_POST["album".$albumID])) {
			if(!($rot == $_POST["album".$albumID])){
				
				$new_rot = $_POST["album".$albumID];
				
				$query = sprintf("UPDATE libalbum SET rotationID='%d' WHERE albumID='%d'", mysql_real_escape_string($new_rot), mysql_real_escape_string($albumID));
				mysql_query($query) or die("reviewAlbum failed : ".mysql_error());
			}	
			
		}

	}
	
	
	echo "<meta http-equiv=\"refresh\" content=\"0; url=rotation_control.php?rotation=$rot\"/>";


?>