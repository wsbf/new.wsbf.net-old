<?php
/** phonebook.php
 * This page displays the phonebook of the full staff.
 * (It should only be available to members of fullstaff).
 * Re-written by David Cohen 
 */ 
require_once("conn.php");

/** this function is the whole page. it should NOT be called outside this page. **/
function phoneBookList($query) {
	$teamNames = array(
		'Purple'=>'Purple Pirates',
		'Red'=>'Red Jaguars',
		'Green'=>'Green Monkeys',
		'Blue'=>'Blue Barracudas',
		'None'=>'---'
	);
	$teamColors = array(
		'Purple'=>'#601860',
		'Red'=>'#BF3030',
		'Green'=>'#308030',
		'Blue'=>'#5566FF',
		'None'=>'#E0E0E0'
	);
?>
	<table class="chart">
	<tr>
	<th class="show"><p class="show">Name</p></th>
	<th class="show">Team Affiliation</th>
	<th class="show"><p class="show">E-mail</p></th>
	</tr>
	
<?php 
		//$query = "SELECT * FROM `djs` WHERE `still_here`=1 ORDER BY `sort_by`,`name` ASC";
		$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	    while ($dj = mysql_fetch_array($result)) {
			$team = $dj['team'];
			if(empty($dj['team'])) $team = 'None';
			$name = $dj['first_name'] . " " . $dj['last_name'];
			$alias = $dj['preferred_name'];

//			$profile = genProfileURL($dj['drupal']);

			echo "<tr style=' background-color:".$teamColors[$team]."'>";
			echo "<td>"; //<a href='$profile'>".$name."</a>";
			if ($alias != "") 
				echo "<br><i>$alias</i>";
//			if(strlen($dj['position']) != 0)
//				echo "<br><b>".$dj['position']."</b>";
			echo "</td>";
			echo "<td>".$teamNames[$team]."</td> <td>".$dj['email_addr']."</td> </tr>";
	    }
	    echo '</table>';
}

echo "<h2 style='margin: 20px 10px'>Active DJs</h2>";
phoneBookList(
"SELECT u.username, u.preferred_name, u.email_addr,
	u.first_name, u.last_name, t.team
	FROM `users` AS u
	INNER JOIN `def_teams` AS t ON t.teamID=u.teamID
	WHERE u.statusID=0
	ORDER BY u.last_name, u.first_name ASC"
);

//echo "<h2 style='margin: 20px 10px'>Inactive DJs Still Here</h2>";
// phoneBookList("SELECT * FROM `djs` WHERE `active`=0 AND `still_here`=1 ORDER BY `sort_by`,`name` ASC");
?>
