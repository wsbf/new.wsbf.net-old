<?php
$time = microtime(true);
require_once("../connect.php");
require_once("../utils_ccl.php");
require_once("../library_functions.php");
require_once("../position_check.php");
require_once("getid3/getid3.php");
require_once("import_config.php");

if(!session_id()) session_start();
if(!(MD_check())){
	die ('You aren\'t allowed to be here!<br>Go <a href='.$_SERVER['HTTP_REFERER'].'>back.</a><br>');
}

$id3 = new getID3;	
//sanitizeInput();
	
echo "<title>WSBF Import Music</title>";

$dirCurrent = urldecode($_GET['path']); //security is above...
$artistGet = urldecode($_GET['artist']);

if(chdir($dirCurrent) === FALSE)
	echo("Error: Could not change to ".$dirCurrent."\n");
$dirCurrent = getcwd();
if(strpos($dirCurrent, BASE_DIR) === FALSE)
	header("Location: ".$_SERVER['HTTP_REFERER']);


echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import a CD</h3>\n";
echo "<p>This page imports <b>one album at a time.</b> Or go <a href='".$_SERVER['HTTP_REFERER']."'>back</a>...</p>\n";
echo "<div id='contents'>";


$pattern = "$artistGet*";
$files = glob($pattern);
//echo "<pre>$pattern\n".print_r($files,TRUE)."</pre>";

//this is an array that we will use to store the id3 info in,
//after we get it from the files	
$trackA = array();

$index = 1;
foreach($files as $file) {
	$arr = $id3->analyze($file);
	//put the id3 stuff in an array called $arr
	getid3_lib::CopyTagsToComments($arr);
	//print_r($arr['comments']); //die();
	
	//set the filename in the track array
	$trackA[$index]['filename'] = $dirCurrent."/".$file;
	
	//get the disc number and save it in the track array
	if(isset($arr['comments']['part_of_a_set'][0])) {
		$discNum = $arr['comments']['part_of_a_set'][0];
		//It's normally stored as "1/2" or "2/2"
		if(strpos($discNum, "/") !== FALSE){
			$pcs = explode("/", $discNum);
			$discNum = $pcs[0];
		}
	}
	else{
		//If it isn't in the id3 tags, it could be in the filename
		$pieces = explode(' - Disc ', $file);
		if(isset($pieces[1])){
			$discNum = str_ireplace(".mp3", "", $pieces[1]);
		}
		else{ 
			//otherwise, there's only one disc
			$discNum = 1;
		}
	}	
	$trackA[$index]['disc_number'] = $discNum;
	
	//get the track number, same logic as disc number
	if(isset($arr['comments']['track_number'][0])) {
		$track = $arr['comments']['track_number'][0];
	}
	else{
		$track = $index;
	}
	if(strpos($track, "/") !== FALSE){
		$pcs = explode("/", $track);
		$track = $pcs[0];
	}
	$trackA[$index]['track_number'] = $track;
	
	if(isset($arr['comments']['album'][0])) 
		$trackA[$index]['album'] = $arr['comments']['album'][0];
	if(isset($arr['comments']['artist'][0])) 
		$trackA[$index]['artist'] = $arr['comments']['artist'][0];
	if(isset($arr['comments']['title'][0])) 
		$trackA[$index]['title'] = $arr['comments']['title'][0];
	if(isset($arr['comments']['genre'][0])) 
		$trackA[$index]['genre'] = $arr['comments']['genre'][0];
	else $trackA[$index]['genre'] = "";
	
	$index++;
}

/** TODO: resolve any conflicting tags (artist, album, genre, year) **/

/** TODO: WHAT HAPPENS IF YOU HAVE A " IN A FIELD? **/


$firstT = $trackA[1];
//echo "<pre>".print_r($firstT, TRUE)."</pre>";

//We'll just use the info from the first track
if(!(isset($firstT['artist']))){
	$firstT['artist'] = "";
	echo "<br>ID3 tags not properly set, no artist name, please type this in manually.<br>";
}
if(!(isset($firstT['album']))){
	$firstT['album'] = "";
	echo "<br>ID3 tags not properly set, no album name, please type this in manually.<br>";
}


echo "<form method='POST' action='/wizbif/import/import_submit.php'>";
echo "<table><tr><th></th><th></th>";
echo "<tr><td>Album Artist:</td><td><input type='text' name='artist' size='50' value=\"".htmlspecialchars ($firstT['artist'])."\" /></td></tr>";
echo "<tr><td>Album Name:</td><td><input type='text' name='album' size='50' value=\"".htmlspecialchars ($firstT['album'])."\" /></td></tr>";
echo "<tr><td>Label:</td><td><input type='text' name='label' size='50' value=\"\" /></td></tr>";

echo "<tr><td>Genre:</td><td><input type='text' name='genre' size='50' value=\"".htmlspecialchars ($firstT['genre'])."\" /></td></tr>";
	//get the general genre combobox
	$genres_query = "SELECT general_genreID, genre FROM def_general_genres ORDER BY general_genreID ASC";
	$genres = mysql_query($genres_query, $link);
	if (!$genres) {
		die ('This is an error message: ' . mysql_error());
	}
	
	echo "<tr><td><div id=\"top\">General Genre:</div></td><td><select name=\"general_genreID\">";
	while ($genre_get = mysql_fetch_array($genres, MYSQL_NUM)){
		
		$genreID = $genre_get[0];
		$genre = $genre_get[1];
		
		echo "<option value=\"$genreID\">$genre</option>";
		
	}
	echo "</select></td></tr>\n";
	echo "<tr><td>Medium:</td><td><input type='radio' name='medium' value='2' checked> CD <input type='radio' name='medium' value='3'> Digital</td></tr>";
echo "</table>";
echo "<input type='hidden' name='redirect' value=\"".urlencode($_SERVER['HTTP_REFERER'])."\" />";

//echo "<pre>".print_r($trackA, TRUE)."</pre>";



echo "<table>\n";
echo "<tr><th>Disc #</th><th>Track #</th><th>Track Name</th><th>Track Artist</th></tr>";

	$counter = 1;
	$number_of_discs = 0;
foreach($trackA as $trNum => $track) {
	echo "<tr>
			<td><input type='text' size='4' name='".$counter."_discnum' value='".$track['disc_number']."' /></td>
			<td><input type='text' size='4' name='".$counter."_trnum' value='".$track['track_number']."' /></td>
			<td><input type='text' size='75' name='".$counter."_trname' value=\"".htmlspecialchars ($track['title'])."\" />
			</td>";
	echo "<td><input type='text' size='50' name='".$counter."_trartist' value=\"".htmlspecialchars ($track['artist'])."\" /></td></tr>";
	
	echo "<tr><td colspan='4' style='font-style:italic; font-size:.75em;'>".$track['filename'].
	"<input type='hidden' name='".$counter."_trfile' value=\"".urlencode($track['filename'])."\" />".
	"</td></tr>";
	if($track['disc_number'] > $number_of_discs){
		$number_of_discs = $track['disc_number'];
	}
	
	$counter++;	
}
echo "</table><p>";
echo "<br>Based on the tracks, this album seems to have $number_of_discs discs;<br>if this is incorrect, edit the disc numbers on the tracks and type the number of discs here:<input type='text' name='number_of_discs' size='4' value=\"$number_of_discs\" /><br>";	
echo "<input type='submit' name='submit' value='Submit' /></form>";



$netTime = microtime(true)-$time;
echo "<p>Current working directory: $dirCurrent</p>\n";
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";
echo "\n</div>\n";
?>
