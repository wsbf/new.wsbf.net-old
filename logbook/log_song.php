<?php

//Accessed via AJAX call from logging software
//
//Adds typed-in 'optional' songs to DB
//Zach Musgrave, WSBF-FM Clemson, Jan 2010

// fixt by david cohen for the new fucking database

/** TO DO **/ 
	// AT PRESENT: cBin codes are IGNORED. incoming GET rotation is ALWAYS used.
	//auto-currently playing is turned off
	//use recc & noair codes for something

include("../connect.php");

//$genre = mysql_real_escape_string($_GET['genre']);
	$genre = "";
$track = mysql_real_escape_string($_GET['track']);
$album = mysql_real_escape_string($_GET['album']);
$label = mysql_real_escape_string($_GET['label']);
$artist = mysql_real_escape_string($_GET['artist']);
$showid = mysql_real_escape_string($_GET['showid']);


$albumno = mysql_real_escape_string($_GET['albumno']);
$trackno = mysql_real_escape_string($_GET['trackno']);

if(!empty($albumno)){
	$q = "SELECT def_rotations.rotation_bin FROM `libalbum`, `def_rotations` WHERE libalbum.rotationID = def_rotations.rotationID AND libalbum.album_code = '$albumno'";
	$r = mysql_query($q) or die(mysql_error());
	$row = mysql_fetch_assoc($r);
	// rotation is the first character of whatever this says.
	$rotation = substr($row['rotation_bin'], 0, 1);
}
else $rotation = "O";

//calculate pNumInShow. initialization only here.
//$numinshow = 0;

//field names for lbplaylist
//	pID 	p_sID		pDTS		pNumInShow	pAlbumNo	pTrackNo	
//	pGenre	pRotation	pArtistName	pSongTitle	pAlbumTitle	pRecordLabel	pCurrentlyPlaying

	
	//try to get data based on albumno
	//if there is NOT 1 record, then treat as optional.

/*
$rsc = mysql_query("SELECT * FROM libalbum WHERE album_code = '$albumno'");
	if(mysql_num_rows($rsc) == 1) {
		$row = mysql_fetch_array($rsc, MYSQL_ASSOC);
		foreach($row as $k=>$v) $$k = $v;
		
		$genre = mysql_real_escape_string($row['genre']);
		$album = mysql_real_escape_string($row['album_name']);
		
		$rotation = $row['cBin'];
		if($row['cBin'] == "")
			$rotation = "O";
		
		
		//try based on artist
		$rsc2 = mysql_query("SELECT * FROM libartist WHERE artistID='$artistID'");
		if(mysql_num_rows($rsc2) == 1) {
			$row2 = mysql_fetch_array($rsc2, MYSQL_ASSOC);
			$artist = mysql_real_escape_string($row2['aPrettyArtistName']);
		}
		
		//now try based on trackno too
		$rsc3 = mysql_query("SELECT * FROM libtrack WHERE albumID='$albumID' AND tTrackNo='$trackno'");
		if(mysql_num_rows($rsc3) == 1) {
			$row3 = mysql_fetch_array($rsc3, MYSQL_ASSOC);
			$track = mysql_real_escape_string($row3['tTrackName']);
		}
		
		//finally, try based on label
		$rsc4 = mysql_query("SELECT * FROM liblabel WHERE labelID='$labelID'");
		if(mysql_num_rows($rsc4) == 1) {
			$row4 = mysql_fetch_array($rsc4, MYSQL_ASSOC);
			$label = mysql_real_escape_string($row4['lPrettyLabelName']);
		}
		
		
		
	}
	
	//field names for lbplaylist
	//do NOT write pID (auto_inc) or pDTS (curr timestamp)
*/

$query = "INSERT INTO `logbook` (showID, lb_album_code, lb_rotation, lb_track_num, lb_track_name, lb_artist, lb_album, lb_label) VALUES ('$showid', '$albumno', '$rotation', '$trackno', '$track', '$artist', '$album', '$label')";
//echo $query;
	mysql_query($query) or die("Query failed : " . mysql_error());
	echo "success";

	
	

?>