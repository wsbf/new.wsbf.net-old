<?php
/* schedule_addshow.php
 * Adds a new show to the schedule. 
 * Uses JQuery UI for nice usability and eye candy and shit.
 * David Cohen - 9/16/2011
 *
 * time entry - http://keith-wood.name/timeEntry.html
*/
if(!session_id())
	session_start();
require_once('header.php');
require_once('conn.php');
include ("position_check.php");
if(empty($_SESSION['username'])){  //gotta be logged in
	die ('You need to login first!<br><a href="/login">Click here to login!</a>');
}
elseif(empty($_SESSION['positionID'])){
	die ('You must be on senior staff to use this.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WSBF: Add Show</title>
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="/jqui/css/smoothness/jquery-ui-1.8.16.custom.css">
	<script type="text/javascript" src="/jqui/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/jqui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="/jqui/other_jq/timeentry/jquery.timeentry.min.js"> </script>
		<script type="text/javascript" src="/jqui/other_jq/validation/jquery.validate.min.js"></script>

	<style>
	@import "/jqui/other_jq/timeentry/jquery.timeentry.css";
	.ui-button { margin-left: -1px; }
	.ui-button-icon-only .ui-button-text { padding: 0; } 
/*	.ui-autocomplete-input { margin: 0; padding: 0.48em 0 0.47em 0.45em; }*/
	.ui-autocomplete {
		max-height: 600px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		margin:0;
		padding: 0.3em 0 0.3em 0.45em;
	}
	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */
	* html .ui-autocomplete {
		height: 200px;
	}
	.ui-autocomplete-loading { background: white url('http://stream.wsbf.net/jqui/css/smoothness/images/ui-anim_basic_16x16.gif') right center no-repeat; }
	/* for validator */
	label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
	</style>

	<script type="text/javascript" language="javascript" src="schedule_addshow.js"></script>
</head>
<body>
<div id="container">
	<center><h1>WSBF-FM Clemson</h1></center>
<h2>Add New Show to the Schedule</h2>
<p><div id="successMessage" class="success"></div></p>
<p>
<form name="newshow" id="addShowForm" method="POST" action="schedule_add.php">
<table>
	<tr><td></td><td></td></tr>
<tr>

	<td>DJ Names:</td>
	<td><div class="ui-widget">
	<input id="names" name="names" />
	<!-- usernames is filled by javascript when names is filled -->
	<input type="hidden" id="usernames" name="usernames" />
</div>
</td><td>
	
	
		</td>
	</tr>
	
	<tr>
		<td>Day</td>
		<td>
		<div id="dayRadio">
		<?php
			$days = array("S", "M", "T", "W", "Th", "F", "S");
			$dayID = 0;
			foreach($days as $day){
				$radioID = "radio" . ($dayID);
					echo "<input type='radio' id='$radioID' name='dayID' value='$dayID' /><label for='radio$dayID'>$day</label>";

				$dayID++;
			}
	
		?>
		</div>
	</td>
</tr>
<tr>
	<td>Start Time:</td>
	<td><input type="text" class="timeRange required ui-state-default ui-corner-all" id="timeFrom" name="start_time" size="6" /><i>Tip: Use tab and the arrow keys.</i></td>
</tr>
<tr>
	<td>End Time:</td>
	<td><input type="text" class="timeRange required ui-state-default ui-corner-all" id="timeTo" name="end_time" size="6" /></td>
</tr>

<tr>
	<td>Show Type:</td>
	<td>
		<select name="show_type" class='ui-state-default ui-corner-all'>
			<option name="show_type" value="0" />Rotation</option>
			<option name="show_type" value="1" />Specialty</option>
			<option name="show_type" value="2" />Jazz</option>
			<option name="show_type" value="3" />Talk / Sports</option>
			<option name="show_type" value="4" />Rotating Specialty</option>
			<option name="show_type" value="6" />Live Sessions</option>
		</select>
	</td>		
</tr>

<tr>
	<td>Show Name <i>(optional)</i></td>
	<td><input type="text" class="ui-state-default ui-corner-all" id="showName" name="showName" /></td>
</tr>

<tr>
	<td></td>
	<td>
		<input type='submit' value='Submit' name='submit' class='ui-state-default ui-corner-all' />
	</td>
</tr>
</table>
</form>
</div>




</p>

</div>
</body>
</html>
