<?php
define('DEBUG', false);
require_once('../conn.php');
require_once('showtable.php');
/*
if(function_exists('drupal_add_css')) {
	drupal_add_css('wizbif/schedule.css');
}
else {
*/
?>
<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title>WSBF Schedule</title>
		<link rel="stylesheet" type="text/css" href="http://new.wsbf.net/wizbif/schedule/schedule.css" />
</head><body> -->


<?php
 // }
?>

<style>
	@import "http://new.wsbf.net/wizbif/schedule/schedule.css";
</style>

<?php 
/** **/
define('SMALLEST_INC', 30);
/** number of table rows, in this case based on half hours **/
define('NUM_TROWS', 48); 

/** create zeroed 2D array **/
$cells = NULL;
for($row = 0; $row < NUM_TROWS; $row++) 
	for($col = -1; $col < 7; $col++) 
			$cells[$row][$col] = 0;

/** this relies on a constant set in showtable.php **/
switch(SCHEDULE_MODE) {
	case 'SUMMER': {
		$cells[0][-1] = '1 - 4 a.m.';
		$cells[6][-1] = '4 - 7 a.m.';
		$cells[12][-1] = '7 - 10 a.m.';
		$cells[18][-1] = '10 a.m. - 1 p.m.';
		$cells[24][-1] = '1 - 4 p.m.';
		$cells[30][-1] = '4 - 7 p.m.';
		$cells[36][-1] = '7 - 10 p.m.';
		$cells[42][-1] = '10 p.m. - 1 a.m.';
	} break;
	case 'FREEFORM': {
		echo "WSBF is in freeform mode.";
		exit();
	} break;
	case 'SEMESTER': {
		$cells[0][-1] = '1 - 3:00 a.m.';
		$cells[4][-1] = '3:00 - 5 a.m.';
		$cells[8][-1] = '5 - 7:00 a.m.';
		$cells[12][-1] = '7 - 9 a.m.';
		$cells[16][-1] = '9 - 11 a.m.';
		$cells[20][-1] = '11 a.m. - 12:30 p.m.';
		$cells[23][-1] = '12:30 - 2 p.m.';
		$cells[26][-1] = '2 - 3:30 p.m.';
		$cells[29][-1] = '3:30 - 5 p.m.';
		$cells[32][-1] = '5 - 7 p.m.';
		$cells[36][-1] = '7 - 9 p.m.';
		$cells[40][-1] = '9 - 11 p.m.';
		$cells[44][-1] = '11 p.m. - 1 a.m.';
	} break;
	default: { echo "WTF?!?"; }
}
/** following are markings for leftmost columns (index -1) **/


/** create a 2D array of shows: by table row (start time), then table column (day of week) **/
$shows = array();
	/** sauce: http://en.wikipedia.org/wiki/Inner_join#Equi-join **/

//	$qu = "SELECT * FROM shows INNER JOIN show_dj USING ( show_id )";

	$qu = "SELECT users.username, users.preferred_name, schedule.scheduleID,
		schedule_hosts.schedule_alias, schedule.show_name,
		schedule.dayID, schedule.start_time, schedule.end_time, 
		schedule.genre, schedule.show_typeID, def_show_types.type		
		FROM `users`, `schedule`, `schedule_hosts`, `def_show_types`
		WHERE schedule.active = 1
		AND schedule_hosts.scheduleID = schedule.scheduleID
		AND def_show_types.show_typeID = schedule.show_typeID
		AND users.username = schedule_hosts.username";
		
	$rs = mysql_query($qu) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());


	while($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
		/** page is Mon to Sun; SQL is Sun to Sat **/
		$tcol = $row['dayID'] - 1;
		if($tcol == -1) $tcol = 6;
		
		// first row is 1am, so transition to row number 0
		// half hour multiplier, as well
		$timeArray = date_parse_from_format("H:i:s",$row['start_time']);
		$trow = (($timeArray['hour'] - 1) * 2) + ($timeArray['minute'] / SMALLEST_INC);

		/** move to a different row if not starting on the hour **/
		
		/** if the show hasn't been added to the array yet, add it. **/
		if(!isset($shows[$trow][$tcol]))
			$shows[$trow][$tcol] = $row;
		
		// add usernames and aliases into new element 'djs'
		
		// use schedule_alias if exists; preferred_name otherwise
		if(!array_key_exists('djs', $shows[$trow][$tcol]))
			$shows[$trow][$tcol]['djs'] = array();
		
		if(empty($row['schedule_alias']))
		 	$alias = $row['preferred_name'];
		else
			$alias = $row['schedule_alias'];
			
		$shows[$trow][$tcol]['djs'][] = array(
			'username'	=>	$row['username'],
			'alias'		=>	$alias );

		/*******/
		if(DEBUG){	
			foreach($shows[$trow][$tcol] as $k=>$v)	$$k = $v;
			$users = array();
			foreach($djs as $dj)
				$users[] = $dj['username'];
			echo "<!-- $scheduleID\t" . implode("\t", $users) . "\t$dayID\t$start_time-$end_time\t$trow\t$tcol -->\n";
		}
}


/** build left-hand header cells **/ 
for($i = 0; $i < NUM_TROWS; $i++) {
	if( $cells[$i][-1] !== 0 ) {
		$rowspan = 1;
		while( isset($cells[($i+$rowspan)][-1]) && $cells[($i+$rowspan)][-1] == 0) {
			++$rowspan;
		}
		$cells[$i][-1] = "<th class='side' rowspan='$rowspan'>".$cells[$i][-1]."</th>\n";
	} else {
		$cells[$i][-1] = '';
	}


}

/** build the actual cells **/
for($col = 0; $col < 7; $col++) {
	for($row = 0; $row < NUM_TROWS; $row++) {
		if(isset($shows[$row][$col]) && $cells[$row][$col] === 0) {
			$show = $shows[$row][$col];
			
			
			$djs = '';
			
			// shows with >1 DJ are put into an array
			// put all others into a one-element array

			// get names for each
			$aliases = array();
			foreach($show['djs'] as $dj) {
				$aliases[] = $dj['alias'];
			}

			$djs = implode('<br/>', $aliases);
			
			
			if(!empty($show['show_name']))
				//$djs = "<i><a href='#' onclick=\"openDialog('".$show['show_name']."','".$show['username']."','".$show['show_desc']."')\">".$show['show_name'].'</a></i><br/>'.$djs;
				$djs = '<i>'.$show['show_name'].'</i><br/>'.$djs;


			// old database used show_length; calculate length with strtotime
			// divide by 60 to get minutes
			$rowspan = ((strtotime($show['end_time']) - strtotime($show['start_time'])) / 60) / SMALLEST_INC;
			// take into account shows that go between days
			if($show['end_time'] < $show['start_time']) $rowspan += 48;
//			$rowspan =  / SMALLEST_INC;

			// 'c' is the prefix of the show types
			$class = "c" . $show['show_typeID'];
			$cells[$row][$col] = "\t<td class='$class' rowspan='$rowspan'>$djs</td>\n";
			
			$filler = $rowspan;
			while(--$filler > 0) {
				$cells[$row+$filler][$col] = -1;
			}
		}

		if(DEBUG){	
			echo "<!--$row $col \t";
			echo var_dump($cells[$row][$col]);
			echo "-->\n";
			
		}
	}
}

// echo var_dump($cells);

/** this pass has to be done AFTER building the cells above **/
/** add in all the filler cells and adjust their rowspans accordingly **/
for($col = -1; $col < 7; $col++) {
	for($row = 0; $row < NUM_TROWS; $row++) {
		//echo $row . "x" . $col . " | ";
		if($cells[$row][$col] === 0) {	
			$counter = 1;
			while( isset($cells[$row+$counter][$col]) && 
					$cells[$row+$counter][$col] === 0) {
				$cells[$row+$counter][$col] = -1;
				++$counter;
				if(DEBUG) echo "<!-- incrementing - $counter -->\n";
			}
			$cells[$row][$col] = "\t<td rowspan='$counter'>&nbsp;</td>\n";
		}	
	}
}

// wsbf.def_show_types
$def_show_types = array(
	0	=>	'Rotation',				// 0
	1	=>	'Specialty',			// 1	
	2	=>	'Jazz',					// 2
	3	=>	'Sports/Talk',			// 3
//	4	=>	'Rotating Specialty',	// 4 
//	5	=>	'Special-Programming',	// 5 [not used in schedule]
//	6	=>	'Live Sessions',		// 6
//	7	=>	'Free Form',			// 7 [not used in schedule]
//	8	=>	'Automation'			// 8 [not used in schedule]
);
	echo "<div style='margin: 15px auto; width: 100%'><h3>For more info about a show, check out the tabs for each day.</h3><br />Color key:";
	
	foreach($def_show_types as $show_typeID=>$type)

			echo "<div class='c" . $show_typeID . "' style='display: inline;'>$type</div> ";
?>

<table class='schedule'>
<tr class='side'>
	<th>&nbsp;</th>
	<th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th>
	<th>Saturday</th><th>Sunday</th>
</tr>
<?php

//print out everything. allow for filler rows.
for($i = 0; $i < NUM_TROWS; $i++) {
	echo "\n<tr>";
	for($j = -1; $j < 7; $j++) {
		if( isset($cells[$i][$j]) ) {
			if($cells[$i][$j] !== -1)
				echo $cells[$i][$j];
			//else echo "NULLITY ";
		}
	}
	echo "</tr>\n";
}

?></table>
