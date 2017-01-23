<?php
	require_once("connect.php");

	$show_picked = $_POST["show_picked"];

	$result = mysql_query("SELECT * FROM `show`");
	$shows = array();
	while($row = mysql_fetch_array($result)){
		if ($row['show_name'] != ''){
			//echo($row['show_name'] . "<br>");
			array_push($shows, $row['show_name']);
		}

	}
	$shows = array_unique($shows);
	sort($shows);


?>



<html>
<head>
	<title>Historical WSBF Show Data!</title>
</head>
<body>
	<h1 align="center"> Dive into WSBF History! </h1>
	<div id="box" align="center">
		<p>Select a show below!</p>

		<form method=post action="<?php echo($PHP_SELF);?>">
		<select name=show_picked>
			<?php
				foreach($shows as $i){
					echo("<option value=\"$i\">" . $i .  "</option>" . "\n");
				}

			?>

		<select>

		<input type="submit" name="submit" value="submit">

		</form>

		<p><?php echo($show_picked); ?></p>

		<?php
			$songs = array();
			$artists = array();
			$ids = array();
			$result = mysql_query("SELECT * FROM `show` WHERE `show_name`=\"" . $show_picked . "\"" );
			while($row = mysql_fetch_array($result)){
				array_push($ids, $row['showID']);

			}

			$psa = mysql_query("SELECT `issuer`, `title` FROM `libcart`");

			foreach($ids as $i){
				$result = mysql_query("SELECT * FROM `logbook` WHERE `showID`=\"" . $i . "\"");
				while($row = mysql_fetch_array($result)){
					$s = mysql_query("SELECT * FROM `libcart` WHERE `title`=\"" . $row['lb_track_name'] . "\"");

					$a = mysql_query("SELECT * FROM `libcart` WHERE `issuer`=\"" . $row['lb_artist'] . "\"");


					if(!mysql_fetch_array($s)){
						array_push($songs, $row['lb_track_name']);
					}
					if(!mysql_fetch_array($a)){
						array_push($artists, $row['lb_artist']);
					}
				}

			}

			if(isset($_POST['show_picked'])){
				echo("<br>This show has been on-air " . count($ids) . " times! Woah! <br>");
				echo("These guys have played " . count($songs) . " songs by " . count(array_unique($artists)) . " different artists! <br>" );



				$c = array_count_values($songs);
				$val = array_search(max($c), $c);

				echo("Their favorite song is " . $val);

				$c = array_count_values($artists);
				$val = array_search(max($c), $c);

				echo(" and their favorite artist is " . $val . "<br>");
				$max = 0;
				foreach($ids as $i){
					$result = mysql_query("SELECT * FROM `show` WHERE `showID`=\"" . $i . "\"");
					$row = mysql_fetch_array($result);

					if($row['max_listeners'] > $max){
						$max = $row['max_listeners'];
						$date = $row['start_time'];
					}
				}

				echo("Their craziest show was on " . $date . " 'cause they had "  . $max . " listeners! No way dude!<br>" );
			}
		?>



	</div>





</body>
</html>
