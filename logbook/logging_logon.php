<?php
//Accessed via AJAX call from logging software
//Logs in the given show
//Zach Musgrave, WSBF-FM Clemson, Dec 2009

//on success, returns lbshow prikey
//-1 on failboat

//GET parms: djname, scheduleID (aka showsid), showname (custom!)
// showname!? no it doesn't
//RETurns: show id (instance)

include("../connect.php");
$time = date("Y-m-d G:i:s"); //this is probably redundant, schema has default

// originally done by dj name; now done by username

if(isset($_GET['djname']) && isset($_GET['showsid'])){

	
	$usernames = urldecode($_GET['djname']);
	// should be usernames separated by commas or some bullshit like that.
	$scheduleID = urldecode($_GET['showsid']);

	// remove trailing ", "
	$usernames = substr($usernames, 0, strlen($usernames) - 2);

	/**
	*	WE DO NOT SUPPORT multi-djs with a SELF-TITLED show.
	*	wsbf doesn't really ever do this.
	**/
	$show_usernames = explode(", ", $usernames);	// put names (now USERNAMES) into array
	/********* NO scheduleID PASSED IN ***********/
	if($scheduleID == -1){		// no scheduleID passed = self-titled show

//		if(count($show_usernames) == 1){	// only 1 dj
			$dj_username = $show_usernames[0];
			// find entries in `schedule` with _only_ this dj and NO showname (it would have been given)
			$q00 = sprintf("SELECT * FROM `schedule`,`schedule_hosts`
			WHERE schedule_hosts.scheduleID = schedule.scheduleID
			AND schedule_hosts.username = '%s'
			AND schedule.show_name = ''", $dj_username);

			$r00 = mysql_query($q00) or die(mysql_error());

			if(mysql_num_rows($r00) == 1 && count($show_usernames == 1)){	// found it!
				$row = mysql_fetch_assoc($r00);
				// name variables as row elements
				foreach($row as $k=>$v) $$k = $v;

				// insert into shows
				$ifs = sprintf("INSERT INTO `show` (start_time, show_typeID, scheduleID) VALUES('%s', '%s', '%s')", $time, $show_typeID, $scheduleID);
				$insert_fucking_show = mysql_query($ifs) or die(mysql_error());


				$showID = mysql_insert_id($link);	// get showID of row just inserted

				// insert into show_hosts
				if(empty($schedule_alias))
					$schedule_alias = '';
					
				// first get user information

				$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES ('%s', '%s', '%s')", $showID, $username, $schedule_alias);
				$insert_fucking_show_hosts = mysql_query($ifsh) or die(mysql_error());

				// done!
			}
			
//		}
		else{	// >1 dj doing a show; just make scheduleID/showname NULL
			$ifs = sprintf("INSERT INTO `show` (start_time, show_typeID, scheduleID) VALUES ('%s', '0', NULL)", $time);
			$insert_fucking_show = mysql_query($ifs) or die(mysql_error());
			$showID = mysql_insert_id($link);	// get showID of row just inserted

			/** Since it's not in the schedule, we have to look up each of the hosts
			*	in the users table and get his/her preferred name, etc.
			*	FUTURE WORK: add custom aliases for shows and ability multiple djs with
			*	self-titled shows
			**/

			$fucking_dj_query_list = implode("' OR username='", $show_usernames);	// add usernames into query
			$get_fucking_dj_info = mysql_query("SELECT username, first_name, last_name, preferred_name FROM `users` WHERE username='$fucking_dj_query_list'") or die(mysql_error());

			while($fucking_row = mysql_fetch_assoc($get_fucking_dj_info)){
				foreach($fucking_row as $k=>$v) $$k=mysql_real_escape_string(htmlspecialchars($v));

				// if DJ doesn't use alias
				if($preferred_name == $first_name . " " . $last_name)
					$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES ('%s', '%s', NULL)", $showID, $username);


				else
					$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES ('%s', '%s', '%s')", $showID, $username, $preferred_name);
			$insert_fucking_show_host = mysql_query($ifsh);
			}

		}
	// only echo showID if it has properly created one
	if(isset($showID))
		echo $showID;
	else
		echo -1;

} 


else{
	/*************** scheduleID passed in *******************/
	$sql = sprintf("SELECT show_typeID, show_name FROM schedule WHERE scheduleID = '%s' LIMIT 1", $scheduleID);
	$r = mysql_query($sql, $link) or die(-1);
	$row01 = mysql_fetch_assoc($r);
	$show_typeID = $row01['show_typeID'];
	$show_name = mysql_real_escape_string(htmlspecialchars($row01['show_name']));

	// insert ìnto `show`
	$ifsn = sprintf("INSERT INTO `show` (start_time, show_typeID, scheduleID, show_name) VALUES ('%s', '%s', %d, '%s')", $time, $show_typeID, $scheduleID, $show_name);
	$insert_fucking_show_name = mysql_query($ifsn) or die(mysql_error());

	$showID = mysql_insert_id();
	
	
	// search for and insert each entry into `show_hosts`
	foreach($show_usernames as $user){
		
		
		$gfdi = sprintf("SELECT users.username, users.first_name, users.last_name, users.preferred_name, schedule_hosts.schedule_alias 
			FROM `users`
			LEFT JOIN `schedule_hosts` USING (username)
			WHERE (users.username='%s') AND schedule_hosts.scheduleID='%d' LIMIT 1", $user, $scheduleID);
		
		
		$get_fucking_dj_info = mysql_query($gfdi) or die(mysql_error());
		$djinfo = mysql_fetch_assoc($get_fucking_dj_info);

		foreach($djinfo as $k=>$v) $$k = mysql_real_escape_string(htmlspecialchars($v));
		
//		if($djinfo['schedule_alias'] != NULL && $djinfo['schedule_alias'] != ''){
		
		// check the schedule_alias
		if($schedule_alias != NULL && $schedule_alias != '')	// schedule alias exists, use
			$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES (%d, '%s', '%s')", 
				$showID, $user, $schedule_alias);
		
		else
			$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES (%d, '%s', NULL)", 
				$showID, $user);
		$insert_fucking_show_host = mysql_query($ifsh) or die(mysql_error());
		/* now all this shit should be done */
	}

echo $showID;
} //end
}


/***************************************************************************

// it seems that -1 is the default scheduleID sent
// i.e. it isn't on the schedule
if(!empty($_GET['showsid']) && $_GET['showsid'] != -1){
	$scheduleID = urldecode($_GET['showsid']);

	if(mysql_num_rows($r00) < 1){
		die("what the fuck?");
	}
	elseif(mysql_num_rows($r00) == 1){
		$row0 = mysql_fetch_array($r00);
		$scheduleID = $row0['scheduleID'];





	}


}
else{
	$scheduleID = NULL;
	$query = "INSERT INTO `show` (start_time, show_typeID) VALUES ('$time', 0)";
}

//	if($scheduleID == NULL) echo "CHICKEN";





	foreach($show_usernames as $dj){
		$query = "INSERT INTO show_hosts (showID, username) VALUES ('$showID', '$dj')";
		$reuslt = mysql_query($query) or die(-1);
	}
	echo $showID;
*/
?>
