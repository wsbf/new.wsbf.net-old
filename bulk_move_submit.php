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
	
	if(!(isset($_POST["list"]))){
		die ("You didn't type any albums");
	}
	else {
		$list = $_POST["list"];
	}
	
	if(!(isset($_POST["new_bin"]))){
		die ("You didn't select the desired bin, failed to move albums<br> $list");
	}
	else {
		$new_bin = $_POST["new_bin"];
		if($new_bin == 999){
			die ("You didn't select the desired bin, failed to move albums<br> $list");
		}	
	}
	
	switch($new_bin){
		case 0:
			$bin_name = "To Be Reviewed";
			break;
		case 1:
			$bin_name = "New";
			break;	
		case 2:
			$bin_name = "Heavy";
			break;	
		case 3:
			$bin_name = "Medium";
			break;	
		case 4:
			$bin_name = "Light";
			break;
		case 5:
			$bin_name = "Optional";
			break;
		case 6:
			$bin_name = "Jazz";
			break;
		case 7:
			$bin_name = "To Be Reviewed";
			break;
		default:
			die("What the Fuck did you do, Peter?");
			break;
	}		
	
	$album = explode(" ", $list);
	
	foreach($album as $albums){
		
		$album_code = preg_replace("/[^a-zA-Z0-9\s]/", "", $albums);
		
		$query = sprintf("UPDATE libalbum SET rotationID='%d' WHERE album_code LIKE '%s'", mysql_real_escape_string($new_bin), mysql_real_escape_string($album_code));
		if(mysql_query($query)){
		   echo "Successfully moved $album_code to $bin_name<br>";
		}
		else {
			echo "<br>Failed to move $album_code to $bin_name:" . mysql_error() . "<br>";
		}
	}	

	echo "<br><a href=\"bulk_move.php\">Bulk Move More!</a> or go to <a href=\"rotation_control.php\">Rotation Control!</a>";
?>