<?php
/**
 * David Cohen - 11/27/11 - Some functions to make logging a little easier
 */


/**
 * updateNowPlaying - sets the currently playing song in the database, affecting
 * `logbook` immediately (time-played is offset +20s to account for delay,
 * `now_playing` (after 20 seconds)
 * and sends to the RDS (immediately; we don't worry about 20s delay here.)
 * @param int $logbookID the logbookID of the song to be set as now playing
 */
function setNowPlaying($logbookID){
	// set time to 20 seconds in advance (because of delay)
	$time = date('Y-m-d H:i:s', strtotime('+20 seconds'));
	$q = sprintf("UPDATE `logbook` SET played=1, time_played='%s' WHERE logbookID = %d", $time, $logbookID);
	mysql_query($q) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

	// get the name/artist of the current list
	$query = sprintf("SELECT lb_track_name, lb_artist FROM `logbook` WHERE logbookID = %d LIMIT 1", $logbookID);
	$result = mysql_query($query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	$row = mysql_fetch_array($result, MYSQL_ASSOC);

	// sanitize
	$artist = mysql_real_escape_string($row['lb_artist']);
	$song = mysql_real_escape_string($row['lb_track_name']);

	// send RDS info here; the delay is already set with that.
	sendRDS($row['lb_artist'], $row['lb_track_name']);
	
	
	// wait 20 seconds to change now playing on for the stream, because it's
	// post-delay
//s		sleep(20);
		$query = sprintf("UPDATE `now_playing` SET logbookID = '%d', lb_artist_name = '%s', lb_track_name = '%s'", $logbookID, $artist, $song);

		$result = mysql_query($query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

	
}

/**
 * sendRDS - sends RDS info to windows server to be read by
 * kevin's VB RDS sender. To be replaced with his PHP one someday.
 * @param string $artist the artist name, as it would appear in `logbook`
 * @param string $track the name of the track
 * @author david cohen 11/27/11
 */
function sendRDS($artist, $track){
	$artist = urlencode($artist);
	$track = urlencode($track);
	$fp = fopen( "http://130.127.17.2/wizbif/logbook/write_rds_files.php?artist=$artist&song=$track", 'r' );
	$content = '';

	while ($l = fread($fp, 1024)) $content .= $l;
	fclose($fp);
	/**** some stuff that used to be in updatecurrentsong ****/
	
	//echo $content;

	/*
	$segment_id= 881;
	// You have to attach to the shared memory segment first
	$shm = shm_attach($segment_id,PHP_INT_SIZE,0600);
	$date = time();
	shm_put_var($shm,1,$date);
	include("../rds_sender.php");
	rdssend($song,$artist,$date);
	*/
	/*
	//write artist and songname to textfiles so they can be accessed by Kevin's RBDS program
	$fa = fopen('current_artist.txt', 'w');
	fwrite($fa, $artist);
	fclose($fa);

	$fs = fopen('current_song.txt', 'w');
	fwrite($fs, $song);
	fclose($fs);

	include("../rds_sender_test.php");

	rdssend($song,$artist);
	*/
	
	
}

?>