<?
/** schedule_add.php - David Cohen - Nov. 2011
 *  Inserts array from POST to 
 * 
 */
require_once('conn.php');


/**
 * Takes the dayID, start_time, end_time formatted as ints (dayID is the same format
 * as the database; start_time and end_time should be formatted with PHP's strtotime())
 * returns an array of scheduleIDs that overlap with the given show time.
 */
function findConflicts(int $dayID, int $start_time, int $end_time){

	$st = date("H:i:s", strtotime('+'.$dayID.' days', $start_time));
	$et = date("H:i:s", strtotime('+'.$dayID.' days', $end_time));
	
$q = sprintf("SELECT scheduleID,
	ADDTIME( STR_TO_DATE(`dayID`, '%w'), start_time ) AS week_st,
	ADDTIME( STR_TO_DATE(`dayID`, '%w'), end_time ) AS week_et
	FROM `schedule`	WHERE
	('%s' < week_st AND '%s' < week_et AND '%s' > week_st AND '%s' < week_st)	OR 	('%s' < week_st AND '%s' > week_et AND '%s' > week_st AND '%s' < week_et)", $st, $st, $et, $et, $st, $st, $et, $et);
/*
	('%s' < start_time AND '%s' < end_time AND '%s' > start_time AND '%s' < start_time)	OR 	('%s' < start_time AND '%s' > end_time AND '%s' > start_time AND '%s' < end_time)", $st, $st, $et, $et, $st, $st, $et, $et);
*/
	mysql_query($q) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
}

function overwriteConflicts($dayID, $start_time, $end_time){
	$et = mysql_real_escape_string($end_time);
	$st = mysql_real_escape_string($start_time);
	
	$query = sprintf("UPDATE `schedule` 
		SET active = 0
		WHERE dayID = %d ", 
		$dayID);
		
/*		
	case one, proposed show end time overlaps an existing shows start time
		st < show.st && st < show.et && et > show.st && et < show.st
	case two, proposed show start time overlaps an existing shows end time
		st < show.st && st > show.et && et > show.st && et < show.et
	
	
	select * from whatever where show.startTime <= st and show.endTime > st
*/
	if($st < $et){	// all cases that don't spill over to next day
		$query .= sprintf("AND (start_time < '%s' AND end_time > '%s') 
		AND start_time < end_time", $et , $st);
	}

	else{ // cases that go to next day, i.e. 11-1 shows. 
		$query .= sprintf("AND (start_time > '%s' AND end_time < '%s')
			AND start_time > end_time", $et, $st);
	}



mysql_query($query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

}

/**
 * Takes an array of usernames and sets the statusID to $statusID
*/
function setUserStatusID($usernames, $statusID){
	// escape everything here!
	foreach($usernames as &$u) $u = mysql_real_escape_string($u);
	// put into form 'user1', 'user2', ..., 'user_n'
	$userlist = "'" . implode("','", $usernames) . "'";
	$q = sprintf("UPDATE `users` SET statusID = %d WHERE username IN(%s)", $statusID, $userlist);
	mysql_query($q) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
}


if($_POST){

	foreach($_POST as $k=>$v) $$k = $v;
//	scheduleID	show_name	dayID	start_time	end_time	show_typeID	description	genre	active

$start_time = strtotime($start_time);
$end_time = strtotime($end_time);

/* future - send an error / confirmation if there is a conflict
 * now - overwrite any show that exists by setting the old one to inactive. 
 */


/*
$entry_check = sprintf("SELECT scheduleID FROM `schedule` 
	WHERE dayID = %d
	AND active = 1
	AND (start_time >= '%s' AND end_time <= '%s')", $dayID, $start_time, $end_time);

// for shows that go to the next day (i.e. 11-1), end_time < start_time, which
// would otherwise screw up our results
if($end_time < $start_time)
	$entry_check .= " AND start_time > end_time";
	
$check = mysql_query($entry_check);
if(mysql_num_rows($check) > 0){
	$id = mysql_fetch_assoc($check);
*/



$start = date("H:i:s", $start_time);
$end = date("H:i:s", $end_time);

overwriteConflicts($dayID, $start, $end);

$sched_query = sprintf("INSERT INTO `schedule` (dayID, start_time, end_time, show_typeID, active, show_name) VALUES (%d, '%s', '%s', %d, 1, '%s')", $dayID, $start, $end, $show_type, mysql_real_escape_string($showName));
mysql_query($sched_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
//echo $sched_query . "\n";

$scheduleID = mysql_insert_id();

// remove trailing ", " from username string
$usernames = substr($usernames, 0, -2);
$hosts = explode(", ", $usernames);

// set hosts to active
setUserStatusID($hosts, 0);


// create array of insert statements of host usernames
$insert_host = array();
foreach($hosts as $host){
	$insert_host[] = sprintf("(%d, '%s')", $scheduleID, $host);
}
$insertStatement = implode(", ", $insert_host);

$host_query = sprintf("INSERT INTO `schedule_hosts` (scheduleID, username) VALUES %s", $insertStatement);
//echo $host_query . "<br />";
mysql_query($host_query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());




$days = array(
	0 => "Sun",
	1 => "Mon",
	2 => "Tues",
	3 => "Wed",
	4 => "Thurs",
	5 => "Fri",
	6 => "Sat" );
	
	$st = date("G:ia", strtotime($start_time));
	$et = date("G:ia", strtotime($end_time));
echo $_POST['names'] . ": " . $days[$dayID] . " $start-$end";
}
	
	
?>