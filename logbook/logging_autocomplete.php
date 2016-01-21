<?php

//Accessed via AJAX call from logging software
//Delivers XML-formatted data
//Auto-completion of manual input fields!
//Zach Musgrave, WSBF-FM Clemson, Jan 2010

//Modified 22 Aug 2010, relies on cBin in libcd now.

header("Content-type: application/xml");
include("../conn.php");


//include("drs.php");

$albno = null;
$trkno = null;
	$label = "";
	$artist = "";
	$album = "";
	$track = "";
	$clean = "";
	$recc = "";
$cdid = "";

if(!isset($_GET['albno']))
	die();
else {
	$albno = mysql_real_escape_string($_GET['albno']);

// fuck you, zach, for not commenting your god damn code
	
//	$rsc1 = mysql_query("SELECT libcd.cID, liblabel.lPrettyLabelName, libartist.aPrettyArtistName, libcd.cAlbumName, libcd.cBin 
//	FROM libcd, liblabel, libartist WHERE libcd.c_lID=liblabel.lID AND libcd.c_aID=libartist.aID AND 
//	libcd.cAlbumNo='$albno' ORDER BY libcd.cID DESC LIMIT 1") or die(mysql_error());
	
	$rsc1 = mysql_query("SELECT libalbum.albumID, libalbum.album_name, liblabel.label, libartist.artist_name, def_rotations.rotation_bin 
		FROM `libalbum`, `liblabel`, `libartist`, `def_rotations`
		WHERE def_rotations.rotationID = libalbum.rotationID
		AND libalbum.artistID = libartist.artistID
		AND libalbum.labelID = liblabel.labelID
		AND libalbum.album_code = '$albno'
		ORDER BY libalbum.albumID DESC LIMIT 1") or die(mysql_error());
	
	
	$show_record = mysql_fetch_array($rsc1);
	
	$cdid = $show_record['albumID'];
	$label = htmlspecialchars($show_record['label']);
	$artist = htmlspecialchars($show_record['artist_name']);
	$album = htmlspecialchars($show_record['album_name']);
	
	$bin = htmlspecialchars($show_record['rotation_bin']);
	if($bin == "")
		$bin = "O";
	
}

if(isset($_GET['trkno'])) {
	$trkno = mysql_real_escape_string($_GET['trkno']);
	
//	$rsc2 = mysql_query("SELECT tTrackName, tClean, tRecc FROM libtrack WHERE t_cID='$cdid' 
//		AND tTrackNo='$trkno' ORDER BY tID DESC LIMIT 1");

	$rsc2 = mysql_query("SELECT track_name, airabilityID FROM `libtrack` WHERE albumID = '$cdid' AND track_num = '$trkno' ORDER BY track_num ASC LIMIT 1") or die(mysql_error());
	$show_record = mysql_fetch_array($rsc2);
	$track = htmlspecialchars($show_record['track_name']);

$clean = $recc = 0;
	if($show_record['airabilityID'] != 2)
		$clean = 1;
	if($show_record['airabilityID'] == 1)
		$recc = 1;
	
//	$clean = $show_record['tClean'];
//	$recc = $show_record['tRecc'];
	
	
}


echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
echo "<autoinfo>\n";

	//echo "<id>" . $pID . "</id>\n";

	echo "<track>" . htmlspecialchars($track) . "</track>\n";
	echo "<album>" . htmlspecialchars($album) . "</album>\n";
	echo "<artist>" . htmlspecialchars($artist) . "</artist>\n";
	echo "<label>" . htmlspecialchars($label) . "</label>\n";
	echo "<bin>" . htmlspecialchars($bin) . "</bin>\n";
	echo "<clean>" . $clean . "</clean>\n";
	echo "<recc>" . $recc . "</recc>\n";

echo "</autoinfo>";
?>
