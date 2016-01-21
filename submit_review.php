<?php
	if(!session_id()) session_start();
	include("connect.php");
	include("library_functions.php");
	require_once("position_check.php");

	
		$error = "";
		//gotta have an albumID
		if(isset($_POST['albumID'])) {
			$albumID = $_POST['albumID'];
			$album_code = $_POST['album_code'];
			$rotationID = $_POST['rotationID'];
			
			if(isset($_POST['edit'])){
				if($_POST['edit']==1){
					$description = "EDITED REVIEW OF albumID = $albumID";
					$edit = 1;
				}
				else{
					$description = "SUBMITTED REVIEW FOR albumID = $albumID";
					$edit = 0;
				}
			}
			else{
				$description = "SUBMITTED REVIEW FOR albumID = $albumID";
				$edit = 0;
			}
		}
		else{
			$error = "<br>How did you get here without an albumID!?<br>Either something went horribly wrong somewhere or you are a jerk.
				<br>You should probably email the <a href=\"mailto:computer@wsbf.net\">computer engineer</a>!";
			
			if(isset($_POST['review'])){
				$review = $_POST['review'];
				$error .= "<br>Oh, and since you wrote a review, you may want to copy and paste it:<br>$review<br>";
			}	
		}

		
		
		if($error == "") {

			$track_total = $_POST['track_total'];
			
			if(strlen($_POST['artist']) < 1) $error .= "<br>Enter an artist name.";
			if(strlen($_POST['album']) < 1) $error .= "<br>Enter an album name.";
			if(strlen($_POST['label']) < 1) $error .= "<br>Enter a label name, or put 'Self Released.'";
			if(strlen($_POST['genre']) < 1) $error .= "<br>Enter a genre, or multiple genres.";
			if(strlen($_POST['display']) < 1) $error .= "<br>Enter your name as the reviewer.";
			if(strlen($_POST['username']) < 1) $error .= "<br>You went out of your way to remove the username.";
			if($track_total == 0){
				$error .= "<br>How did you review this CD if it doesn't have any tracks?";
			}
			
			$max_track = 1;
			$max_disc = 1;
			$ctr_recc = 0;
			$ctr_dirty = 0;
			
			while(isset($_POST["track".$max_track."disc".$max_disc."air"])){
				
				while(isset($_POST["track".$max_track."disc".$max_disc."air"])){
					  $air = $_POST["track".$max_track."disc".$max_disc."air"];
					  if($air == 2){
							$ctr_dirty++;
					  }
					  if($air == 1){
							$ctr_recc++;	
					  }
					  $max_track++;
				}
						
				$max_track = 1;
				$max_disc++;
					  
			}
					  
			if($ctr_recc == 0 && !positionCheck("seniorstaff")){
				$error .= "<br>You haven't selected any recommended tracks. Please select the most outstanding tracks.";
			}
			
			if($ctr_dirty == $track_total && !positionCheck("seniorstaff")){	
				$error .= "<br>If the album only has dirty tracks, you shouldn't review it,
				<br>either that or you forgot to change them from No Air";	
			}	  
					  
			if(strlen($_POST['review']) < 1) $error .= "<br>Enter a review. Asshat.";
			
			
			if($error != "") {
				
				$msg = "Error(s):".$error."<br><a href=\"review.php?albumID=$albumID\">Go try again!</a>";
				if(isset($_POST['review'])){
					$review = $_POST['review'];
					$msg .= "<br>Oh and I know you'd be pissed when you hit back and your review wasn't there,
					<br>so if you typed one here's your review if you want to copy it before you go back:
						<br>$review";
				}
				
				die($msg);	 
					 
			}
			
			
			$artist_name = $_POST['artist'];
			$album_name = $_POST['album'];
			$label = $_POST['label'];
			$genre = $_POST['genre'];
			$review = $_POST['review'];
			$reviewer = $_POST['display'];
			$username = $_POST['username'];
			$general_genreID = $_POST['general_genreID'];
			
			
			if(!reviewAlbum($albumID, $album_name, $album_code, $artist_name, $label, $genre, $general_genreID, $review, $username, $reviewer, $rotationID)){
				echo "Uh oh, looks like something went wrong!  Try it again perhaps? <br> <a href=\"review.php?albumID=$albumID\">Click Here</a>
				<br>Oh and I know you'd be pissed when you hit back and your review wasn't there,
				<br>so here's your review if you want to copy it before you go back:<br>$review<br>";
			}
			else{
				echo "Successfully saved review of $album_name by $artist_name<br>";
				
			}	

			
			
			
			$max_track = 1;
			$max_disc = 1;
			
			while(isset($_POST["track".$max_track."disc".$max_disc])){
				
				while(isset($_POST["track".$max_track."disc".$max_disc])){
					$airabilityID = $_POST["track".$max_track."disc".$max_disc."air"];
					$track_name = $_POST["track".$max_track."disc".$max_disc];
					$artist_name = $_POST["track".$max_track."disc".$max_disc."art"];
					
					if(!(reviewTrack($track_name, $max_disc, $max_track, $artist_name, $airabilityID, $albumID))){
						echo "Something went wrong, I suppose.  I dunno, <a href=\"library.php?rotation=0\">go try again</a>?
						<br>Oh and you may want to copy and paste your review:<br>$review";
					}
					echo "Successfully saved disc # $max_disc track # $max_track. $track_name by $artist_name<br>";
					$max_track++;
				}
				
				$max_track = 1;
				$max_disc++;
				
			}
			
			
			$pref = $_SESSION['preferred_name'];	
			echo "Thanks for the review, $pref.  <br>
				Now you can <a href=\"logout.php\">log out</a> or "; 
			if($edit == 1){
				echo "<a href=\"library.php?rotation=0\">review some more</a> or <br>";
				echo "go back to <a href=\"rotation_control.php\">Rotation Control</a>!";
			}
			else{
				echo "<a href=\"library.php?rotation=0\">review another</a>!";
			}	
			
			$description = mysql_real_escape_string($description);
			addAction($username, $description);
			
		} 
	echo $error;
		

?>