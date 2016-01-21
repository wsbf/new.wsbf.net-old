<?php
/** logging_getdjs.php
 *	prints out all the djs and interns as <option>s in separate <optgroup>s
*/

require_once('../connect.php');

// select all interns and djs
// statusID - active=0, interns=5
$res = mysql_query("SELECT username, preferred_name, statusID FROM users 
	WHERE statusID = 0 
	OR statusID = 5
	ORDER BY statusID, first_name, last_name ASC")
 	or die("MySQL error [". __FILE__ ."] near line" . __LINE__ . ": " .mysql_error());

// add to separate associative arrays -- i.e. two versions of $row
$djs = $interns = array();
while($row = mysql_fetch_assoc($res) ) {
	switch($row['statusID']){	// dj
		case 0:
			$djs[] = $row;
			break;
		case 5:
			$interns[] = $row;
			break;
		default:
			die("Shit, something went wrong: " . var_dump($row));
	}
}

	echo "<optgroup label='djs'>";
foreach($djs as $row){
	foreach($row as $k=>$v) $$k = $v;
	echo "<option value='$username'>$preferred_name</option>\n";
}
	echo "</optgroup><optgroup label='Interns/New DJs'>";
	foreach($interns as $row){
		foreach($row as $k=>$v) $$k = $v;
		echo "<option value='$username'>$preferred_name</option>\n";
	}

?>