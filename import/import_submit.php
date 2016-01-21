<?php
$delete = TRUE; 
	
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

	define('BASE_ZAUTO', "/var/www/wizbif/ZAutoLib");
	define('BASE_DIR', "/home/compe/RIPPED_MUSIC");
	define('SCRIPT_PREFIX', "http://stream.wsbf.net/wizbif/");
define('BIN_CODE', 0); //To Be Reviewed
define('LABEL_DUMMY', 1899);

//sanitizeInput();



//dac
echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import Submission/Confirmation</h3>\n";
echo "<p>Go <a href='".urldecode($_POST['redirect'])."'>back</a>...</p>\n";
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
	if($lID) echo "Label already exists: $lID for $label<br>";
	else {
		$lID = insertLabel($label);
		echo "New label ID: $lID for $label<br>";
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
if($cID) echo "CD already exists: $cID<br>";
else {
	$cID = insertNewAlbum($album, $number_of_discs, $aID, $lID, $medium, $genre, $general_genreID);
	echo "New CD ID: $cID<br>";
}


if($delete)
	echo "Delete mode is turned on.<br>";
else
	echo "Delete mode is turned off - old files will be preserved.<br>";
echo "Moving files and updating track table...<br>";

$ctr = 1; /** $ctr is an iterator, not the ACTUAL track number, which is a POSTed *_trNum **/
while(isset($_POST[$ctr.'_trnum'])) {
	$trNum = $_POST[$ctr.'_trnum'];
	$discNum = $_POST[$ctr.'_discnum'];
	$trName = urldecode(trim($_POST[$ctr.'_trname']));
	$trArtist = urldecode(trim($_POST[$ctr.'_trartist']));
	$trFile = urldecode($_POST[$ctr.'_trfile']);
	
	$pcs = explode("/", $trFile);
	$filename = $pcs[count($pcs)-1];
	
	
	/** file moving **/
		//this change made by ztm 1nov10. see review_lib.php documentation
		//$dirMake = cleanName($artist);
		$dirMake = directoryName($artist);
	$newPath = BASE_ZAUTO."/";
	//echo "<br>$newPath<br>";
	//echo "<br>$newPath"."$dirMake[0]<br>";
	if(!file_exists( $newPath.$dirMake[0] ))
		mkdir( $newPath.$dirMake[0] );
	$newPath .= $dirMake[0] . "/";
	//echo "<br>$newPath<br>";
	//echo "<br>$newPath"."$dirMake[1]<br>";
	if(!file_exists( $newPath.$dirMake[1] ))
		mkdir( $newPath.$dirMake[1] );
	$newPath .= $dirMake[1] . "/";
	//echo "<br>$newPath<br>";
	$newPath .= $filename;
	//echo "<br>$newPath<br>";
	
	/** FOR WRITING TO DATABASE ONLY **/
	$dirDB = $dirMake[0].$dirMake[1].$filename;
	//echo "<br>$dirDB<br>";
	//echo "<br>$trFile<br>";
	//echo "<br>$newPath<br>";
	
	if(!copy($trFile, $newPath))
		echo("<br>Could not copy: $trFile to $newPath<br>");
	//echo "Moved from: $trFile <br>Moved to: $newPath<br>";
	//echo "Copying to ZAutomate... ";
	
	if($delete) {
		if(!unlink($trFile)){
			die("Could not delete: $trFile");
			echo "Deleted: $trFile<br>";
			echo "Success!<br>";
		}
	}
	else {
		$rs = fopen("import_todelete.txt", 'a');
		fwrite($rs, $trFile."\n");
		fclose($rs);
	}

	
	/** libtrack modifying **/
	$qu = "SELECT * FROM libtrack WHERE albumID='$cID' AND track_num='$trNum' AND disc_num ='$discNum'";
	$rs = mysql_query($qu);
	if(mysql_num_rows($rs) == 1) {
		if(!($artistID = artistCheck($trArtist))){
			$artistID = insertArtist($trArtist);
		}
		$qu2 = "UPDATE libtrack SET track_name='$trName', artistID='$artistID', file_name='".urlencode($dirDB)."' WHERE albumID='$cID' AND track_num='$trNum' AND disc_num ='$discNum'"; //urlencode($newPath)
		mysql_query($qu2);
		//if(mysql_affected_rows() != 1)
		//	die("dead on qu2: ".mysql_error());
		echo "Track updated, already exists as album: $cID disc: $discNum track: $trNum <br>";
		$description = "UPDATED $ctr tracks for existing album: $cID";
	}
	elseif(mysql_num_rows($rs) == 0) {
		insertNewTrack($trName, $discNum, $trNum, $trArtist, $cID, urlencode($dirDB)); //urlencode($newPath)
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
