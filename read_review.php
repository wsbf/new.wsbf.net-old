<?php
	
	if(!session_id()) session_start();
	include ("position_check.php");
	if(empty($_GET)){
		
		die('WTF are you doing here?  Pick an album from the <a href="library.php">list!</a>');
		
	}
	
	else {
		
		$albumID = $_GET["albumID"];
		
	}
	
	if(empty($_SESSION['username'])){
		
		die ('You need to login first!<br><a href="login.php">Click here to login!</a>');
	}


	require_once("connect.php");
	
	$album_query = sprintf("SELECT album_name, num_discs, artistID, labelID, genre, general_genreID, album_code FROM libalbum WHERE albumID = '%d'", $albumID);
	
	$album = mysql_query($album_query, $link);
	if (!$album) {
		die ('This is an error message: ' . mysql_error());
	}
	
	$row = mysql_fetch_array($album, MYSQL_NUM);
	
	$album_name = $row[0];
	$num_discs = $row[1];
	$artistID = $row[2];
	$labelID = $row[3];
	$genre = $row[4];
	$general_genreID = $row[5];
	$album_code = $row[6];
	
	$md = MD_check();
	
	
	echo "<head><title>WSBF Read CD Reviews</title>";
	
	echo "<style>table.bottomBorder td, table.bottomBorder th { border-bottom:1px dotted black; } #top {text-align:right; font-weight:bold;} #topcenter {font-weight:bold; text-align:center;} #bold{font-weight:bold;} #italic {font-style:italic;} #center {text-align:center;} #centerred {color:red; text-align:center;} #centergreen {color:green; text-align:center;} #centeryellow {color:yellow; text-align:center;} #centerblack {color:black; text-align:center;} #red {color:red;} #green {color:green;} #yellow {color:yellow;} #black {color:black;}</style>";
	
	echo "</head>";
	
	echo "When you're done, you can <a href=\"library.php\">go back</a>, or you can <a href=\"logout.php\">log out</a>.";
	
	
	
	$artist_query = sprintf("SELECT artist_name FROM libartist WHERE artistID = '%d'", $artistID);
	$artist = mysql_query($artist_query, $link);
	if (!$artist) {
		die ('This is an error message: ' . mysql_error());
	}
	$artist_get = mysql_fetch_array($artist, MYSQL_NUM);
	$artist = $artist_get[0];
	
	$review = "";
	$review_query = sprintf("SELECT review, username, reviewer, review_date FROM libreview WHERE albumID = '%d'", $albumID);
	$rev = mysql_query($review_query, $link);
	if (!$rev) {
		die ('This is an error message: ' . mysql_error());
	}
	
	if ($review_get = mysql_fetch_array($rev, MYSQL_NUM)){
		$review = $review_get[0];
		$username = $review_get[1];
		$pref_name = $review_get[2];
		$review_date = $review_get[3];
		
	}	
	
	$label_query = sprintf("SELECT label FROM liblabel WHERE labelID = '%d'", $labelID);
	$label = mysql_query($label_query, $link);
	if (!$label) {
		die ('This is an error message: ' . mysql_error());
	}
	$label_get = mysql_fetch_array($label, MYSQL_NUM);
	$label = $label_get[0];
	
	if($label == "foobar records"){
	
		$label = "";
		
	}	
	
	echo "<table class=\"bottomBorder\">\n";
	echo "<tr><td><div id=\"top\">Album Code: </div></td><td>$album_code</td></tr>\n";
	echo "<tr><td><div id=\"top\">Artist: </div></td><td>$artist</td></tr>\n";
	echo "<tr><td><div id=\"top\">Album: </div></td><td><div id=\"italic\">$album_name</div></td></tr>\n";
	echo "<tr><td><div id=\"top\">Label: </div></td><td>$label</td></tr>\n";
	echo "<tr><td><div id=\"top\">Genre: </div></td><td>$genre</td></tr>\n";
	
	$genres_query = sprintf("SELECT genre FROM def_general_genres WHERE general_genreID = '%d'", $general_genreID);
	$genres = mysql_query($genres_query, $link);
	if (!$genres) {
		die ('This is an error message: ' . mysql_error());
	}
	
	echo "<tr><td><div id=\"top\">General Genre: </div></td><td>";
	$genre_get = mysql_fetch_array($genres, MYSQL_NUM);
	$genre = $genre_get[0];
		
		echo "$genre";
		
	echo "</td></tr>\n";
	if($md){
		echo "<tr><td><div id=\"top\">Username:</div></td><td>$username</td></tr>\n";
	}	
	echo "<tr><td><div id=\"top\">Reviewer:</div></td><td>$pref_name</td></tr>\n";
	echo "</table>\n";
	
	echo "<br><div id=\"bold\">Review:</div>$review<br><br>";
	
	$track_query = sprintf("SELECT track_name, disc_num, track_num, artist_name, airability, libtrack.airabilityID FROM libtrack, libartist, def_airability WHERE albumID = '%d' AND libtrack.airabilityID=def_airability.airabilityID AND libtrack.artistID=libartist.artistID", $albumID);
	$track = mysql_query($track_query, $link);
	if (!$track) {
		die ('This is an error message: ' . mysql_error());
	}
	
	echo "<table class=\"bottomBorder\" cellpadding='3'>\n";
	echo "<tr><th><div id=\"center\">Disc No.</div></th><th><div id=\"center\">Track No.</div></th><th><div id=\"center\">Track Name</div></th><th><div id=\"center\">Track Artist</div></th><th><div id=\"center\">Track Status</div></tr>\n";

	while($row = mysql_fetch_array($track, MYSQL_NUM)){
	
		$track_name = $row[0];
		$disc_num = $row[1];
		$track_num = $row[2];
		$artist_name = $row[3];
		$airability = $row[4];
		$airabilityID = $row[5];

		if($airabilityID == 1){
			$divID = "green";
		}
		elseif($airabilityID == 2){
			$divID = "red";
		}
		elseif($airabilityID == 3){
			$divID = "yellow";
		}
		else{
			$divID = "black";
		}
		
		echo "<tr><td><div id=\"center$divID\">$disc_num</div></td><td><div id=\"center$divID\">$track_num</div></td><td><div id=\"$divID\">$track_name</div></td><td><div id=\"$divID\">$artist_name</div></td><td><div id=\"center$divID\">$airability</div></td></tr>\n";
		
	}
	
	echo "</table>\n<br>\n";
	
	$url = urlencode($artist);
	
	$similar_artist_query = "SELECT artist_name FROM libartist WHERE";
	
	$jsonurl = "http://developer.echonest.com/api/v4/artist/similar?api_key=4VQZKSD99EUX9ON55&name=$url&format=json&results=100&start=0";
	$json = file_get_contents($jsonurl);
	$json_output = json_decode($json, true);
	$i=0;
	
	while(isset($json_output['response']['artists'][$i]['name'])){
		
		$similar = mysql_real_escape_string($json_output['response']['artists']["$i"]['name']);
		
		$similar_artist_query .= " artist_name LIKE '$similar' OR";
		
		$i++;
	}
	
	if($similar_artist_query == "SELECT artist_name FROM libartist WHERE"){
		echo "<br>No Artists Similar to $artist found.<br>";
	}
	else{
		$similar_artist_query = substr($similar_artist_query, 0, -3);
		
		$artists = mysql_query($similar_artist_query, $link);
		
		if (!$artists) {
			die ('This is an error message: ' . mysql_error());
		}
		
		echo "<br><b>Similar Artists to $artist:</b><br>";
		
		while($row = mysql_fetch_array($artists, MYSQL_NUM)){
			
			$similar_artist = $row[0];
			
			
			echo "$similar_artist<br>";	
			
			
		}
	}

?>