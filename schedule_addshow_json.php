<?php


require_once('conn.php');

/** David Cohen - Fall 2011
 *	Searches for a user in the database
 *	This script is called in schedule_addshow.js for the autocomplete.
**/
if($_GET){
	$term = "%" . mysql_real_escape_string($_GET['term']) . "%";
	$final_result = array();
	$q = sprintf("SELECT username, first_name, last_name, preferred_name FROM `users` WHERE (first_name LIKE '%s' OR last_name LIKE '%s' OR preferred_name LIKE '%s')", $term, $term, $term);
		$res = mysql_query($q) or die("Mysql Error near Line " . __LINE__ . ": " . mysql_error());

		while($row = mysql_fetch_assoc($res)){
			foreach($row as $k=>$v) $$k = htmlentities($v);
			if($preferred_name == $first_name . " " . $last_name)
				$name = $preferred_name;
			else
				$name = $first_name . " " . $last_name . " [" . $preferred_name . "]";
			$final_result[] = array(
				'id' => $username,
				'label' => $name,
				'value' => $username
			);
		}

	echo json_encode($final_result);
}
?>