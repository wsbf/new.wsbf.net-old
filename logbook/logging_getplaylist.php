<?php

//Outputs a complete XHTML page (for inclusion via iframe)

//All songs DJ has played (so far) in their show
//Zach Musgrave, WSBF-FM Clemson, Oct 2009 (Revised from XML output Aug 2010)
// fix't for new database by David A. Cohen, II, 8/15/2011. 
/** header("Content-type: application/xml"); **/
//require_once('logging_header.php');
require_once('../conn.php');
require_once('../utils_ccl.php');

$day = date("j");
$day = str_pad($day, 2, "0", STR_PAD_LEFT);
if(isset($_GET['sid']))
	$showID = mysql_real_escape_string($_GET['sid']);
else{
$mx = "SELECT MAX(showID) FROM `show`";
$r = mysql_query($mx) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
$row = mysql_fetch_array($r);
$showID = $row[0];
	
}
// $sID is vestigial from the old fucking database.

$currentTime = strtotime('now');
if($showID != '' && $showID != -1) { //
	$q = "SELECT * FROM `logbook`, `show` WHERE show.showID = '$showID' AND logbook.showID='$showID' AND deleted = 0 ORDER BY logbookID ASC";
$rsc = mysql_query($q) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());


}

else {
	// hopefully this should NEVER HAPPEN BECAUSE AUTOMATION WILL BE LOGGED
	
	//$q = "SELECT * FROM lbshow WHERE sEndTime=0 ORDER BY sID DESC LIMIT 1";
	/** this change was made for automation mode to show no playlist **/
	
	
	die("<table id='log'></table>");
}
?>
<!--<div id="users-contain" class="ui-widget">-->
	<table id='log'>
		<tr id='top' class='ui-widget-header'>
			<th class='small'>Now Playing</th>
			<th class='small'>Album #</th>
			<th class='small'>Track #</th>
			<th>Rotation</th>
			<th class='std'>Track Name</th>
			<th class='std'>Artist</th>
			<th class='std'>Album Name</th>
			<th class='std'>Record Label</th>
		</tr>
	<?php
// $npq = "SELECT `logbookID` FROM `now_playing`";	
$npq = "SELECT logbookID FROM `logbook` WHERE played = 1 ORDER BY time_played DESC LIMIT 1";
 $npr = mysql_query($npq) or die(mysql_error());
 $npres = mysql_fetch_array($npr);
 $latest_played = $npres[0];



while($record = mysql_fetch_assoc($rsc)) {

		foreach($record as $k=>$v) $$k = htmlspecialchars($v);
		
		
	// it appears that zach tried to write this in xml and then changed his mind.
		/**{
		echo "<entry>\n";
		echo "<id>" . $pID . "</id>\n";
		echo "<albnum>" . $albnum . "</albnum>\n";
		echo "<trknum>" . $trknum . "</trknum>\n";
		echo "<numinshow>" . $num . "</numinshow>\n";
		echo "<nowplaying>" . $nowP . "</nowplaying>\n";
		echo "<rotation>" . $rot . "</rotation>\n";
		echo "<track>" . htmlspecialchars($track) . "</track>\n";
		echo "<album>" . htmlspecialchars($album) . "</album>\n";
		echo "<artist>" . htmlspecialchars($artist) . "</artist>\n";
		echo "<label>" . htmlspecialchars($label) . "</label>\n";
		echo "</entry>";
		}**/
		?>
		<tr>
			<td><img 
				<?php if($logbookID != $latest_played) echo "class='gray' alt='not playing' "; else echo "alt='playing!' " ?> 
				src='next32.png' onclick='nowPlaying(<?php echo $logbookID; ?>)' /></td>
			<td><?php echo $lb_album_code; ?></td>
			<td><?php echo $lb_track_num; ?></td>
			<td><?php echo $lb_rotation; ?></td>
			<td><?php echo $lb_track_name; ?></td>
			<td><?php echo $lb_artist; ?></td>
			<td><?php echo $lb_album; ?></td>
			<td><?php echo $lb_label; ?></td>
		</tr>
	
	
		<?php
	}
	/** echo "</playlist>"; **/
	?>
	
	</table>

<!--</div>
</body></html>-->