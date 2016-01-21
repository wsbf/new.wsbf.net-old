<?php
	
	require_once('connect.php');
	
	
	$GLOBALS['normalizeChars'] = array(
									   'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 
									   'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 
									   'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 
									   'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 
									   'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 
									   'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 
									   'ú'=>'u', 'û'=>'u', 'Ť'=>'T', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 
									   'ć'=>'c', 'Ŕ'=>'R', 'ŕ'=>'r', '$'=>'S', 'Ů'=>'U', 'ĺ'=>'l', 'ľ'=>'l', 'ř'=>'r', 'ť'=>'t', 'ň'=>'n',
									   'ď'=>'d', 'ů'=>'u', 'Ď'=>'D', 'Ĺ'=>'L', 'Ľ'=>'L', 'Ň'=>'N', 'Ř'=>'R'
									   );
	
	
	function replaceName($toClean) {
		$toClean     =     str_replace('&', 'and', $toClean);
		$toClean     =     str_replace('!', 'chk', $toClean);
		$toClean     =    trim(preg_replace('/[^\w\d_ -]/si', '', $toClean));//remove all illegal chars
		$toClean     =     str_replace('--', '', $toClean);
		
		return strtr($toClean, $GLOBALS['normalizeChars']);
	}
	
	//Import order: Artist->Label->Album->Tracks->Review
	
	//DSC: Add some shit to libaction
	function addAction($username, $description) {
		$username = mysql_real_escape_string($username);
		$description = mysql_real_escape_string($description);
		$qu = sprintf("INSERT INTO libaction (username, change_description) VALUES ('%s', '%s')", $username, $description);
		mysql_query($qu) or die("\nlibaction update failed: ".mysql_error());
		
	}
	
	
	// and now mostly reused from review_lib.php
	// mostly reused from check.php
	
	//DSC: See if the lable is already in liblabel, if it is, return the lableID, if it isn't return FALSE
	function labelCheck($name) {
		$name = mysql_real_escape_string($name);
		
		$query = sprintf("SELECT labelID FROM liblabel WHERE label='%s'", $name);
		//echo $query;
		$result = mysql_query($query);// or die("labelCheck failed: " . mysql_error());
		if(mysql_num_rows($result) == 0) 
			return FALSE;
		else if(mysql_num_rows($result) > 1)
			die("labelCheck failed: more than 1 entry!");
		else {
			$label = mysql_fetch_array($result);
			if ($label) 
				return $label[0];
			else 
				return FALSE;
		}
		
	}
	
	//DSC: Add a new label get that lable's labelID
	function insertLabel($name) {
		$name = mysql_real_escape_string($name);
		$query = sprintf("INSERT INTO `liblabel` (label) VALUES('%s')", $name);
		mysql_query($query);
		return mysql_insert_id();
	}
	
	
	//DSC: Check to see if an artist is in libartist, if if is return the artistID and if not, return FALSE
	function artistCheck($name) {
		$name = mysql_real_escape_string($name);
		//$query = "SELECT * FROM `libartist` WHERE `aPrettyArtistName` REGEXP '$name' LIMIT 1";
		$query = sprintf("SELECT artistID FROM libartist WHERE artist_name='%s'", $name);	
		$result = mysql_query($query); //or die("artistCheck failed : " . mysql_error());
		if(mysql_num_rows($result) > 1) die("artistCheck failed: more than 1 entry!");
		if(mysql_num_rows($result) == 0){ 
			return FALSE;
		}	
		$artist = mysql_fetch_array($result);
		if($artist){
			return $artist[0];
		}
		else{
			return FALSE;
		}	
	}
	
	//DSC: Insert an artist into libartist and return the new artistID
	function insertArtist($name) {
		$name = mysql_real_escape_string($name);
		$query = sprintf("INSERT INTO `libartist` (artist_name) VALUES('%s')", $name);
		mysql_query($query);
		return mysql_insert_id();
	}
	
	
	
	
	//DSC: Takes the name of the album and the artistID and sees if an album is already in libalbum, if it is return the albumID, if it isn't return FALSE 
	function albumCheck($name, $artistID) {
		if(strlen($name) < 1 || !is_numeric($artistID)) return FALSE;
		$name = mysql_real_escape_string($name);
	
		$query = sprintf("SELECT albumID FROM libalbum WHERE album_name LIKE '%s' AND artistID ='%d'", $name, $artistID);
		//echo $query."<br>";
		$result = mysql_query($query);
		if(mysql_num_rows($result) > 1) die("albumCheck failed: >1 entry!");
		if(mysql_num_rows($result) == 0) return FALSE;
		$album = mysql_fetch_array($result);
		if($album) return $album[0];
		else return FALSE;
	}
	
	
	//DSC: Creates a new readable album number. Ex: "J027"
	// David Bowman made this future proof on accident.  If you change it I'll kill you.
	// When it its to Z999, it will go to AA000
	function getNewAlbumCode() {
		//$query = "SELECT * FROM `libcd` ORDER BY `libcd`.`cAlbumNo` DESC LIMIT 1";
		$query = "SELECT album_code FROM libalbum WHERE libalbum.albumID <> libalbum.album_code ORDER BY libalbum.album_code DESC";
		$result = mysql_query($query) or die("getNewAlbumCode failed : " . mysql_error());
		$album = mysql_fetch_array($result);
		$latestAlbumNo = $album[0];
		
		$charlength = strlen($latestAlbumNo) - 3;
		$alpha = substr($latestAlbumNo,0,$charlength);
		
		$num = intval(substr($latestAlbumNo, -3));
		
		if ($num == 999) {
			$num = 0;
			$alpha++;
		} 
		else {
			$num++;
		}
		$num = str_pad($num, 3, "0", STR_PAD_LEFT);
		
		$newAlbumNo = $alpha . $num;
		
		return $newAlbumNo;
		
		}

	
	
	//DSC: Based on cleanName, but follows alphabetizing conventions.
	//	at the moment, merely remove "the" from beginnings of artist names
	//	newer features may be added later, so DO NOT rely on this (or cleanName)
	//	to stat files for automation/etc - there is a reason the WHOLE filename is 
	//	saved in libtrack!
	//		function added by ztm on 1 Nov 2010
	//  Added stuff that fixed problems zach didn't bother to fix, KKH 28 Sept 2011
	function directoryName($name) {
		$name = replaceName($name);
		$name = strtolower($name);
		
		if(strpos($name, 'the ') === 0){
			$name = str_replace(" ", "", $name);
			$name = substr($name, 3); //start with 4th character
		}
		else{
			$name = str_replace(" ", "", $name);
		}	
		
		return $name;
	}
	
	
	//DSC: Inserts album into database, and returns an ID
	function insertNewAlbum($album_name, $num_discs, $artistID, $labelID, $mediumID, $genre, $general_genreID) {
		$album_name = mysql_real_escape_string($album_name);
		$genre = mysql_real_escape_string($genre);
		$album_code = rand(0,999999999).'PENIS';
		
		$query = sprintf("INSERT INTO `libalbum` (album_name, num_discs, album_code, artistID, labelID, mediumID , genre, general_genreID, rotationID) 
		VALUES('%s', '%d', '%s', '%d', '%d', '%d', '%s', '%d', 0)", $album_name, $num_discs, $album_code, $artistID, $labelID, $mediumID, $genre, $general_genreID);
		mysql_query($query) or die("insertNewAlbum failed : ".mysql_error());
		
		$insertID = mysql_insert_id();
		$query = sprintf("UPDATE libalbum SET album_code='%s' WHERE albumID = '%d'", $insertID, $insertID);
		mysql_query($query) or die("reviewAlbum failed updating album_code: ".mysql_error());
		
		return $insertID;
	}
	
	//DSC: Inserts track into database, and returns an ID
	function insertNewTrack($track_name, $disc_num, $track_num, $artist_name, $albumID, $filename) {
		$track_name = mysql_real_escape_string($track_name);
		//Don't need to mysql_real_escape_string the artist name becasue the check already does it
		if(!($artistID = artistCheck($artist_name))){
			$artistID = insertArtist($artist_name);
		}
		$airID = 2;
		$query = sprintf("INSERT INTO `libtrack` (track_name, disc_num, track_num, artistID, airabilityID, albumID, file_name) 
		VALUES('%s','%d','%d','%d','%d','%d','%s')",$track_name, $disc_num, $track_num, $artistID, $airID, $albumID, $filename);
		mysql_query($query) or die("insertNewTrack failed : ".mysql_error());
		return mysql_insert_id();
	}
	
	function insertRecordTrack($track_name, $disc_num, $track_num, $artist_name, $albumID) {
		$track_name = mysql_real_escape_string($track_name);
		//Don't need to mysql_real_escape_string the artist name becasue the check already does it
		if(!($artistID = artistCheck($artist_name))){
			$artistID = insertArtist($artist_name);
		}
		$airID = 2;
		$query = sprintf("INSERT INTO `libtrack` (track_name, disc_num, track_num, artistID, airabilityID, albumID) 
						 VALUES('%s','%d','%d','%d','%d','%d')",$track_name, $disc_num, $track_num, $artistID, $airID, $albumID);
		mysql_query($query) or die("insertRecordTrack failed : ".mysql_error());
		return mysql_insert_id();
	}
	
	
	//DSC: Takes filename/path, returns boolean if is an MP3
	//Used by: import_*.php
	function isMP3($in) {
		if(strpos($in, ".mp3") !== FALSE)
			return TRUE;
		if(strpos($in, ".MP3") !== FALSE)
			return TRUE;
		return FALSE;
	}
	
	
	/** FOR USE BY review_backend.php (mode INSERT) **/
	
	function reviewAlbum($albumID, $album_name, $album_code, $artist_name, $label, $genre, $general_genreID, $review, $username, $reviewer, $rotation) {
		if(!is_numeric($albumID)) return FALSE;
		if(!is_numeric($general_genreID)) return FALSE;
		$album_name = mysql_real_escape_string($album_name);
		$genre = mysql_real_escape_string($genre);
		$review = mysql_real_escape_string($review);
		$username = mysql_real_escape_string($username);
		$reviewer = mysql_real_escape_string($reviewer);
		$rotation = mysql_real_escape_string($rotation);
		
		$artistID = artistCheck($artist_name);
		if(!($artistID)){
			$artistID = insertArtist($artist_name);
		}
		
		if(!($labelID = labelCheck($label))){
			$labelID = insertLabel($label);
		}
		
		
		if($album_code == $albumID){
			$album_code = getNewAlbumCode();
			$rotation = 7;
		}
		
		
		$query = sprintf("UPDATE libalbum SET album_name='%s', album_code='%s', artistID='%d', labelID='%d', genre='%s', general_genreID='%d', rotationID='%d' WHERE albumID = '%d'", $album_name, $album_code, $artistID, $labelID, $genre, $general_genreID, $rotation, $albumID);
		mysql_query($query) or die("reviewAlbum failed : ".mysql_error());
		
		$query = sprintf("REPLACE INTO libreview (albumID, review, username, reviewer) VALUES ('%d', '%s', '%s', '%s')", $albumID, $review, $username, $reviewer);
		mysql_query($query) or die("reviewAlbumSubmitReview failed : ".mysql_error());
		return TRUE;
	}
	
	function reviewTrack($track_name, $disc_num, $track_num, $artist_name, $airabilityID, $albumID) {
		if(!is_numeric($disc_num)) return FALSE;
		if(!is_numeric($track_num)) return FALSE;
		if(!is_numeric($airabilityID)) return FALSE;
		if(!is_numeric($albumID)) return FALSE;
		$track_name = mysql_real_escape_string($track_name);
		
		$artistID = artistCheck($artist_name);
		if(!($artistID)){
			$artistID = insertArtist($artist_name);
		}
		
		$query = sprintf("UPDATE libtrack SET track_name='%s', artistID='%d', airabilityID='%d' WHERE track_num='%d' AND disc_num='%d' AND albumID='%d'", $track_name, $artistID, $airabilityID, $track_num, $disc_num, $albumID);
		mysql_query($query) or die("reviewNewTrack failed : ".mysql_error());
		return TRUE;
		
	}
	
	
?>