<?php
/** Written by David Cohen; Modified Nov 2011 for new db **/
/** 
 * Loads the tab based on get parameter
*/

require_once('../conn.php');
//require_once('review_lib.php');
require_once('../utils_ccl.php');
sanitizeInput();


$tabs = array("W"=>"Full Week",
					"1"=>"Monday", 
					"2"=>"Tuesday", 
					"3"=>"Wednesday", 
					"4"=>"Thursday", 
					"5"=>"Friday",
					"6"=>"Saturday",
					"0"=>"Sunday");


$tab = $_GET['tab'];
if(strlen($tabs[$tab]) < 1)
	die("Invalid tab code.");
else if($tab == "W"){
	//include("schedule_week.php");
	require_once('schedule_week.php');
}

else{ //this includes all the individual days
	echo "<h2>".$tabs[$tab]."</h2>\n";

	echo "<table id='$tab'><tr><td>Time</td><td>DJ</td><td>Show Name</td><td>Description</td></tr>";

	// AND libcd.cAlbumNo != '' 
	// $q = "SELECT * FROM shows WHERE day = $tab ORDER BY start_hour ASC";
	$q = sprintf("SELECT schedule.scheduleID, schedule.show_name, schedule.description,
		schedule.start_time, schedule.end_time, schedule.show_typeID,
		def_show_types.type,
		schedule_hosts.schedule_alias,
		users.username, users.preferred_name
		FROM `schedule`, `def_show_types`, `schedule_hosts`, `users`
		WHERE schedule.active = 1 
		AND schedule.dayID = '%d'
		AND def_show_types.show_typeID = schedule.show_typeID
		AND schedule_hosts.scheduleID = schedule.scheduleID
		AND schedule_hosts.username = users.username
		ORDER BY schedule.start_time, users.username ASC", $tab);

	$rs = mysql_query($q) or die(mysql_error());


	// copy all into another associative array
	$shows = array();
	while($row = mysql_fetch_assoc($rs)){
		// entry already exists - >1 dj
		foreach($row as $k=>$v) $$k = $v;

		// use schedule_alias if exists; preferred_name otherwise
		if(empty($schedule_alias))
		 	$alias = $preferred_name;
		else
			$alias = $schedule_alias;

		if(!array_key_exists($scheduleID, $shows))
			$shows[$scheduleID] = $row;

			// $shows[$scheduleID] is the element of the current show
			// make a 'djs' associative array for each show
			// each element of 'djs' is 
		
			$shows[$scheduleID]['djs'][] = array(
				'username' => $row['username'],
				'alias' => $alias
			);
	}
	
	/** Now that everything's in array $shows with all necessary info
	* already processed, build table
	*/
	
	foreach($shows as $show){
		$aliases = array();
		// easy variable naming
		foreach($show as $k=>$v)
			$$k = $v;

		foreach($djs as $dj)
			$aliases[] = $dj['alias'];
			
		$dj_names = htmlspecialchars(implode(", ", $aliases));
		$description = htmlspecialchars($description);
		$show_name = htmlspecialchars($show_name);
		
		echo "<tr class='c" . $show_typeID ."'>";


		$start = date_format(new DateTime($start_time), 'g:ia');
		$end = date_format(new DateTime($end_time), 'g:ia');
		echo "<td>$start - $end</td><td>$dj_names</td><td>$show_name</td>";
		//shows a button if the dj has written a description for the show
		if($description != '')
			echo "<td><button type='button' onclick=\"openDialog('$show_name','$dj_names','$description')\">About This Show</button></td></tr>";
		else 
			echo "<td></td></tr>";
	}
	echo "</table>";
}
?>