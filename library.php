<?php
	if(!session_id()) session_start();
	require_once("connect.php");
	include ("position_check.php");
	
	
	//if(empty($_SESSION['username'])){
		
	//	die('You need to login first!<br><a href="login.php">Click here to login!</a>');
	//}
	//	if(empty($_GET)){
	//		$rot = 0;
	//	}
	
	//Check to see if username is set
	if(isset($_SESSION['username'])) {
		//If username is set, check for invalid/empty username
		if(empty($_SESSION['username'])) {
			//If invalid/empty username, die with error
			die('You specified an invalid username!<br><a herf="login.php">Click here to login!</a>');
		}
	}
	else {
		//If unset username, die with error
		die('You specified a NULL username!<br><a href="login.php">Click here to login!</a>');
	}
	//Check to see if $_GET is set
	if(!isset($_GET['rotation'])) {
		//If get is unset, default rotation to 0
		$rot = 0;
	}
	else {
		//If $_GET is set, check for empty/invalid rotation get-var
		if(empty($_GET['rotation'])) {
			//If $_GET[rotation] is empty, maybe valid, set to 0.
			$rot = 0;
		}
		else {
			//Rotation is non-zero non-null value, get rotation get-var
			$rot = $_GET['rotation'];
		}
	}	
	
	echo "<head><title>WSBF Library</title></head>";
	echo "<br><font size = 28>WSBF Library</font><br>You can review a CD, read a review, or <a href=\"logout.php\">logout!</a>";
	echo "<table border = \"1\" cellpadding=\"3\">";
	
	switch($rot) {
		case 0:  //To Be Reviewed
			echo "<th bgcolor = \"black\"><a href=\"library.php?rotation=0\"><font color = \"white\">To Be Reviewed</font></a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;
		case 1:  //New
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=1\"><font color = \"white\">New</font></a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;
		case 2:  //Heavy
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=2\"><font color = \"white\">Heavy</font></a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;
		case 3:  //Medium
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=3\"><font color = \"white\">Medium</font></a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 4:  //Light
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=4\"><font color = \"white\">Light</font></a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 5:  //Optional
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=5\"><font color = \"white\">Optional</font></a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		case 6:  //Jazz
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=6\"><font color = \"white\">Jazz</font></a></th></table><br><br>";
			break;
		case 7:  //To be reviewed
			echo "<th><a href=\"library.php?rotation=0\">To Be Reviewed</a></th><th bgcolor = \"black\"><a href=\"library.php?rotation=7\"><font color = \"white\">Recently Reviewed</font></a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";			
			break;
		default: //Default Review Mode: To Be Reviewed
			echo "<th bgcolor = \"black\"><a href=\"library.php?rotation=0\"><font color = \"white\">To Be Reviewed</font></a></th><th><a href=\"library.php?rotation=7\">Recently Reviewed</a></th><th><a href=\"library.php?rotation=1\">New</a></th><th><a href=\"library.php?rotation=2\">Heavy</a></th><th><a href=\"library.php?rotation=3\">Medium</a></th><th><a href=\"library.php?rotation=4\">Light</a></th><th><a href=\"library.php?rotation=5\">Optional</a></th><th><a href=\"library.php?rotation=6\">Jazz</a></th></table><br><br>";
			break;			
	}
	
	//Query to get albums by rotationID
        if ($rot != 0)
        {
	     $query = sprintf("SELECT `libalbum`.`albumID`, `album_code`, `artist_name`, `album_name`, `review_date`, `first_name`,`last_name` FROM `libalbum`, `libartist`, `libreview`,`users` WHERE `rotationID` = '%d' AND `libalbum`.`artistID` = `libartist`.`artistID` AND `libalbum`.`albumID` = `libreview`.`albumID` AND `users`.`username` = `libreview`.`username`", mysql_real_escape_string($rot));
        }
        else 
        {
	     $query = sprintf("SELECT `libalbum`.`albumID`, `album_code`, `artist_name`, `album_name` FROM `libalbum`, `libartist` WHERE `rotationID` = '%d' AND `libalbum`.`artistID` = `libartist`.`artistID`", mysql_real_escape_string($rot));
        }
	
	//Submit Query
	$list = mysql_query($query, $link);
	//If query returns FALSE, no albums were returned.  Die with error
	if (!$list) {
			die ('No albums returned: ' . mysql_error());
	}
	
	//Formatting table
	echo "<style type=\"text/css\"> table.bottomBorder td, table.bottomBorder th { border-bottom:1px dotted black; } </style>";
	echo "<table class = \"bottomBorder\">";
        if ($rot != 0){
	   echo "<tr><th style=\"text-align:center\">Album ID</th><th style=\"text-align:center\">Album Code</th>
                                             <th>Album</th><th>Artist</th><th>Reviewer</th><th>Date Reviewed</th>";
	}
        else{
             echo "<tr><th style=\"text-align:center\">Album ID</th><th style=\"text-align:center\">Album Code</th>
                                             <th>Album</th><th>Artist</th>";
        }
	//Check to see whether session is for music director.
	//True if so, false otherwise
	$md_ret_value = MD_check();
	//Get row from SQL Query, populate tables with albums
//	while ($row = mysql_fetch_array($list, MYSQL_NUM)){
//		$albumID = $row[0];
//		$album_code = $row[1];
//		$artist_name = $row[2];
//		$album_name = $row[3];
	while($row = mysql_fetch_assoc($list)) {
		$albumID = $row['albumID'];
		$album_code = $row['album_code'];
		$artist_name = $row['artist_name'];
		$album_name = $row['album_name'];

                if ($rot != 0){
		   $review_date = $row['review_date'];
		   $last_name = $row['last_name'];
		   $first_name = $row['first_name'];
                }


		
		if($album_code == $albumID){
			$a_code = "<a href=\"review.php?albumID=$albumID\">REVIEW THIS!</a>";
		}
		else{
			$a_code = "<a href=\"read_review.php?albumID=$albumID\">$album_code</a>";
			//If user is music director, show extra link to edit a review
			if($md_ret_value)
				$a_code .= "<br><a href=\"review.php?albumID=$albumID&edit=1\">Edit Review</a>";
		}
                if ($rot != 0){	
		    echo "<tr><td style=\"text-align:center\">$albumID</td><td style=\"text-align:center\">$a_code</td>
                          <td>$album_name</td><td>$artist_name</td><td>$first_name $last_name</td><td>$review_date</td></tr>";
                }
                else{
		    echo "<tr><td style=\"text-align:center\">$albumID</td><td style=\"text-align:center\">$a_code</td>
                          <td>$album_name</td><td>$artist_name</td></tr>";
                }

	}
	echo "</table>";	
?>
