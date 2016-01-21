<html>
<head>
	<script type="javascript">
	function validateForm() {
	with (document.review) {
	var alertMsg = "Pick a rating.";
	radioOption = -1;
	for (counter=0; counter<rating.length; counter++) {
	if (rating[counter].checked) radioOption = counter;
	}
	if (radioOption == -1) alertMsg = "Pick a Rating!";
	if (alertMsg != "Pick a rating.") {
	alert(alertMsg);
	return false;
	} else {
	return true;
	} } }
	</script>
	<title>WSBF Fishbowl Review</title>
</head>
<body>
<?php
//David Cohen
//July-August 2010
require_once('../conn.php');
require_once('../header.php');
require_once('../position_check.php');
require_once('fishbowl_config.php');

if(empty($_SESSION['username'])){
	die("Not authorized: please <a href='/login'>Log in</a>");
}

else if(positionCheck("seniorstaff")){

if (empty($_POST['submit'])){
$query="SELECT id FROM fishbowl ORDER BY id ASC";
$sql = mysql_query($query) or die(mysql_error());
	while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
$ids[] = $q['id']; //all primary keys go into array
	}
if(empty($ids)) die("No entries yet.");
//Randomize array ids, serialize and use $_SESSION to handle refresh
// shuffle($ids); 
$_SESSION['keys'] = serialize($ids); 
?>
<h2>This is the fishbowl review page. Click "Start" to begin reviewing applications.</h2><br /><br />
<form method='post'>
	&nbsp;&nbsp;&nbsp;&nbsp;<input name='submit' type='submit' value='Start!' />
</form>
<?php	
}
else{
	$ids = unserialize($_SESSION['keys']);

//Posting the previous rating into the mysql database	
	if(!empty($_POST['rating'])){
		$rating = $_POST['rating'];
		$just_rated = $_SESSION['just_rated'];

//		echo "Record number $just_rated was just rated $rating stars.<br />";	
		$query="SELECT * FROM fishbowl WHERE id='$just_rated'";
		$sql = mysql_query($query) or die(mysql_error()); 
			while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
				$average = $q['average'];
	//Note: Weight is the number of reviewers who have reviewed this entry. 
				$weight = $q['weight'];
			}
//		echo "<p>Previous average was $average and previous weight was $weight. </p>";
		$average = ($average*$weight + $rating) / ($weight + 1);
		$weight++;
		mysql_query("UPDATE fishbowl SET average = $average, weight = $weight WHERE id = $just_rated");
//		echo "<p>New average is $average and new weight is $weight. </p>";
	}
	
//Confirming that all entries have been successfully reviewed.
		if(empty($ids)){
			echo "<h2>You're Done!</h2>";
			echo "<table>
				<tr>
					<td>id</td>
					<td>User</td>
					<td>Average</td>
				</tr>";
			$query="SELECT * FROM fishbowl ORDER BY average DESC";
			$sql = mysql_query($query) or die(mysql_error()); 
				while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
					$id = $q['id'];
					$username = $q['username'];
					$average = $q['average'];
					$weight = $q['weight'];
					//Get names of all DJs
					$djquery = sprintf("SELECT preferred_name FROM users WHERE username LIKE '%s'", $username);
					$qdj = mysql_query($djquery) or die(mysql_error());
					while($dj = mysql_fetch_array($qdj, MYSQL_ASSOC)) {
						$name = $dj['preferred_name'];
					}
				echo"<tr>
				<td>$id</td>
				<td>$name</td>
				<td>$average</td>
				</tr>";
				}
		}
	else{
	
		
//The Current Rating page		
	$current = array_pop($ids);
// 	echo "You are currently rating record number $current.";
	$query="SELECT users.username, fishbowl.*, users.preferred_name  FROM `fishbowl`, `users` WHERE fishbowl.username = users.username AND id=$current";
	$sql = mysql_query($query) or die(mysql_error()); 
		while($q = mysql_fetch_array($sql, MYSQL_ASSOC)){
		foreach($q as $k=>$v) $$k = stripslashes($v);

		/**** find number of reviews */
		
		$q = sprintf("SELECT libreview.albumID
			FROM `libreview`
			WHERE libreview.username LIKE '%s'
			AND libreview.review_date >= '%s' 
			AND libreview.review_date <= '%s'",
			$username,
			date("Y-m-d H:i:s", strtotime(SEMESTER_BEGIN)),
			date("Y-m-d H:i:s", strtotime(DEADLINE)));
		$res = mysql_query($q) or die(mysql_error());
		$num_reviews = mysql_num_rows($res);


		echo "<h2>$preferred_name</h2>";
			if($specialty =="1") $specialtyDisp = "Yes";
				else $specialtyDisp = "No";
				echo $id . "<p>Number of semesters at the station: <br /><i>$semesters</i></p>
				<p>Number of missed shows: <br /><i>$missedShows</i></p>
				<p>Did you help set up for live shows or events?<br /><i>$liveShows</i></p>
				<p>Applying for a specialty show?<br /><i>$specialtyDisp</i></p>
				<p>Is there anything else that you've done for the station?<br /><i>$other</i></p>
				<p><b>Number of CD Reviews since " . SEMESTER_BEGIN .": $num_reviews";
		}



	$_SESSION['just_rated'] = $current;
?>
<form name='review'method='post' onSubmit='return validateForm()'>
	<p>
	<input type='radio' name='rating' value='1' />&#9733;<br />
	<input type='radio' name='rating' value='2' />&#9733;&#9733;<br />
	<input type='radio' name='rating' value='3' />&#9733;&#9733;&#9733;<br />
	<input type='radio' name='rating' value='4' />&#9733;&#9733;&#9733;&#9733;<br />
	<input type='radio' name='rating' value='5' />&#9733;&#9733;&#9733;&#9733;&#9733;<br />
	</p>
	<input name='submit' type='submit' value='Submit Rating' />
	</form>
<?php
//Re-serializing the keys that now have one less variable.
$_SESSION['keys'] = serialize($ids);	
}

}}
?></body>
</html>