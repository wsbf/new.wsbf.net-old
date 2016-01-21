<?php
	$time = microtime(true);
	require_once("../connect.php");
	require_once("../utils_ccl.php");
	require_once("../library_functions.php");
	require_once("../position_check.php");
	
	session_start();
	if(!(MD_check())){
		die ('You aren\'t allowed to be here!<br>Go <a href='.$_SERVER['HTTP_REFERER'].'>back.</a><br>');
	}
	
	$user = $_SESSION['username'];
	
	define('SCRIPT_PREFIX', "http://stream.wsbf.net/wizbif/");
	define('BIN_CODE', 0); //To Be Reviewed
	define('LABEL_DUMMY', 1899);

//dac
echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import Submission/Confirmation</h3>\n";
echo "<p>Import another <a href='import_record.php'>record</a>...</p>\n";
echo "<p>Go back to <a href='import_main.php'>main</a>...</p>\n";
echo "<div id='contents'>";


	$artist = trim($_POST['artist']);
	$album = trim($_POST['album']);
	$label = trim($_POST['label']);
	$genre = trim($_POST['genre']);
	$general_genreID = $_POST['general_genreID'];
	$medium = $_POST['medium'];
	$number_of_discs = sprintf("%d", $_POST['number_of_discs']);

if($artist == "" || $album == "")
	die("Please fill in artist and album!");


if($label != ""){
	$lID = labelCheck($label);
	if($lID) echo "Label already exists: $lID<br>";
	else {
		$lID = insertLabel($label);
		echo "New label ID: $lID<br>";
	}	
}
else $lID = LABEL_DUMMY;

$aID = artistCheck($artist);
if($aID) echo "Artist already exists: $aID<br>";
else {
	$aID = insertArtist($artist);
	echo "New artist ID: $aID for $artist<br>";
}

$cID = albumCheck($album, $aID);
if($cID) echo "Album already exists: $cID<br>";
else {
	$cID = insertNewAlbum($album, $number_of_discs, $aID, $lID, $medium, $genre, $general_genreID);
	echo "New Album ID: $cID<br>";
}

echo "Updating track table...<br>";

$ctr = 1; /** $ctr is an iterator, not the ACTUAL track number, which is a POSTed *_trNum **/
while(isset($_POST[$ctr.'_trnum'])) {
	$trNum = $_POST[$ctr.'_trnum'];
	$discNum = $_POST[$ctr.'_discnum'];
	$trName = urldecode(trim($_POST[$ctr.'_trname']));
	$trArtist = urldecode(trim($_POST[$ctr.'_trart']));
	
	/** libtrack modifying **/
	$qu = "SELECT * FROM libtrack WHERE albumID='$cID' AND track_num='$trNum' AND disc_num ='$discNum'";
	$rs = mysql_query($qu);
	if(mysql_num_rows($rs) == 1) {
		if(!($artistID = artistCheck($trArtist))){
			$artistID = insertArtist($trArtist);
		}
		$qu2 = "UPDATE libtrack SET track_name='$trName', artistID='$artistID' WHERE albumID='$cID' AND track_num='$trNum' AND disc_num ='$discNum'";
		mysql_query($qu2);
		//if(mysql_affected_rows() != 1)
		//	die("dead on qu2: ".mysql_error());
		echo "Track updated, already exists as album: $cID disc: $discNum track: $trNum <br>";
		$description = "UPDATED $ctr tracks for existing album: $cID";
	}
	elseif(mysql_num_rows($rs) == 0) {
		insertRecordTrack($trName, $discNum, $trNum, $trArtist, $cID); //urlencode($newPath)
		echo "New track: $trName by $trArtist<br>";
		$description = "INSERTED $ctr tracks for new album: $cID";
	}
	else { die("libtrack rows not one or zero!"); }
	
	//echo "<br>";
	$ctr++;
}

addAction($user, $description);


echo "Complete: All operations finished!<br>";
//echo "<pre>".print_r($_POST, TRUE)."</pre>";

echo "</div>";
$netTime = microtime(true)-$time;
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";


?>