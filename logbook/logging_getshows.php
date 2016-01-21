<?php
require_once('../connect.php');
//$qu = "SELECT show_id, show_name FROM shows WHERE show_name <> '' ORDER BY show_name ASC";
$qu = "SELECT scheduleID, show_name FROM schedule WHERE active=1 ORDER BY show_name ASC";
$showr = mysql_query($qu) or die(mysql_error());

while($row = mysql_fetch_array($showr, MYSQL_ASSOC)){
	if($row['show_name'])
		echo "<option value='".$row['scheduleID']."'>".stripslashes($row['show_name'])."</option>\n";
}


?>
