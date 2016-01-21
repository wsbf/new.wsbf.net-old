<?php
$debug = false;

//Accessed via AJAX call from logging software
//Outputs the current 'shows' primary key, or -1 if we're on automation
//Zach Musgrave, WSBF-FM Clemson, Dec 2009
// edited by David Alexander Cohen, II on 8/14/2011 in order to make it work with the new db

// this is NOT XML because it kills chrome after awhile

require_once("../conn.php");
require_once("../utils_ccl.php");
// fuck you, bac tics.

// show number of listeners in the page


// get most recent show [instance] from show table
$q = sprintf("SELECT show.showID, show.start_time, show.end_time, show.show_name, show.scheduleID, def_show_types.type FROM `show`, `def_show_types` WHERE show.show_typeID = def_show_types.show_typeID ORDER BY show.showID DESC LIMIT 1");
$rsc = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($rsc, MYSQL_ASSOC);

sanitize($row); 	/// CHECK
foreach($row as $k=>$v) $$k = $v;


//$ttime = date("Y-m-d <b\\r> h:i:s A", strtotime($start_time);
$start_time = date("F j, Y, g:i a", strtotime($start_time));

if($row['end_time'] == NULL) {
	// currently playing show
	// show_alias is a dj's alias for a particular show.

	$query = sprintf("SELECT show_hosts.showID, show_hosts.show_alias,
show_hosts.username, users.first_name, users.last_name FROM `show`, `show_hosts`, `users` WHERE show.showID = '%s' AND show_hosts.showID = show.showID AND users.username = show_hosts.username", $showID);


	$zing = mysql_query($query) or die(mysql_error());

	// make array of djs in show, then list separated by commas with
	$dj_usernames = array();
	while($r = mysql_fetch_assoc($zing)){
		if(!empty($r['show_alias']))
			$dj_usernames[] = stripslashes($r['show_alias']);
		elseif(!empty($r['preferred_name'])){	// probs redundant

		}
		else $dj_usernames[] = stripslashes($r['first_name'] . " " . $r['last_name']);
	}

	$djs = implode(', ', $dj_usernames);


	echo $showID . "|" . $start_time . "|" . $djs . "|";
	echo $type . "|" . stripslashes($show_name) . "|" . -1;



}

// should never happen with new system
else {
	echo -1;
}


mysql_close();

?>
