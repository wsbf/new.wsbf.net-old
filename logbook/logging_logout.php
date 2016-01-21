<?php
//Accessed via AJAX call from logging software
//Logs out the given id and sets relevant current time
//Zach Musgrave, WSBF-FM Clemson, Dec 2009

include("../connect.php");
$time = date("Y-m-d G:i:s"); 

if (isset($_GET['id'])) {


	// sign out
	$showID = $_GET['id'];
	$time = date("Y-m-d G:i:s");
//	$query = "UPDATE lbshow SET sEndTime='$time' WHERE sID='$showID'";
	$query = sprintf("UPDATE `show` SET end_time='%s' WHERE showID ='%s'", $time, $showID);
	mysql_query($query) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	echo $showID; // on success

	// check to see whether it's going to automation. if it is, login as automation
	if(isset($_GET['to_automation']) && $_GET['to_automation'] == true){
		$ifs = sprintf("INSERT INTO `show` (start_time, show_typeID, show_name, scheduleID) VALUES ('%s', 8, 'The Best of WSBF', NULL)", $time);
	$insert_fucking_show = mysql_query($ifs) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	}

	$showID = mysql_insert_id($link);	// get showID of row just inserted

			$ifsh = sprintf("INSERT INTO `show_hosts` (showID, username, show_alias) VALUES (%d, 'Automation', NULL)", $showID);
	$insert_fucking_show_host = mysql_query($ifsh) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
	
}
?>
