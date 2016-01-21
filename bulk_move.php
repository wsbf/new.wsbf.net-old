<?php

	require_once("connect.php");
	include ("position_check.php");
	
	if(!session_id()) session_start();
	if(!(MD_check())){
		die ('You aren\'t allowed to be here!<br>');
	}

	
	
	if(empty($_SESSION['username'])){
		
		die('You need to login first!<br><a href="login.php">Click here to login!</a>');
	}
		
	echo "<head><title>WSBF Bulk Move Rotation</title></head>";
	
	echo "<font size = 28>WSBF Bulk Move Rotation</font><br>Type in the CDs you want to move and select where you want to move 'em, or <a href=\"logout.php\">logout!</a>";
	
	echo "<form action='bulk_move_submit.php' method='POST'>";
	
	echo "<textarea name='list' cols=\"65\" rows=\"12\"></textarea>";
	
	$rot_query = "SELECT rotationID, rotation_bin FROM def_rotations";
	$rotations = mysql_query($rot_query, $link);
	if (!$rotations) {
		die ('This is an error message: ' . mysql_error());
	}
	
	
	echo "<br><select name='new_bin'>";
	echo "<option value='999'>SELECT NEW BIN</option>";
	while ($rot_get = mysql_fetch_array($rotations, MYSQL_NUM)){
	
		$rotID = $rot_get[0];
		$bin = $rot_get[1];
		
		echo "<option value='$rotID'>$bin</option>";		
		
	}
	echo "</select>";

	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type='submit' value='Move Rotation'/> </form>";

?>