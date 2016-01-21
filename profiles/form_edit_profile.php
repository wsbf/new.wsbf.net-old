<?php
/** File name: form_edit_profile.php 
 *  Author: Edward Sullivan (ELSULLI@g.clemson.edu, 240 383 0498)
 *  Date: 30 August, 2012
 *  Purpose: Form to allow DJs to edit their personal information
 *           (email address, preferred name, phone number, status, etc.)
 *           as well as info for each of their active shows (show name, alias
 *           for the show hosts, show description, etc.). This form
 *           posts to submit_edit_profile.php.  
 *
 */



/* Start Book Keeping */

   /* Ensure that we don't already have a session */
   if(!session_id())
   session_start();

   $disabled = "disabled"; // Not sure what this does
   require_once("../connect.php"); // Connecting to the database
   
   /* Check to make sure user is logged in */
   if(empty($_SESSION['username'])){  
      die ('You need to login first!<br><a href="/login">' +
           'Click here to login!</a>');
   }

   /* If user is logged in, save username, pref_name, statusID */
   else{  
      $username = $_SESSION['username'];
      $pref_name = $_SESSION['preferred_name'];
      $statusID = $_SESSION['statusID'];
   }
   
   /* Blocking people banned for life */
   if($statusID == 7){ 
      die ("Like we'd let $pref_name edit profile information?  Skedaddle.");
   }

/* Book Keeping is done */



   /* Defining mysql query to get current info about the user (name, preferred name, phone number, email address */
   $DJ_profile_query = sprintf("SELECT first_name, last_name, preferred_name,
                           phone_number, email_addr,
                           sms_recv, username, profile_paragraph
                           FROM users
                           WHERE username = '%s'", $username);

   /* Submitting the query */
   $DJ_profile = mysql_query($DJ_profile_query, $link);

   /* Checking to make sure that the mysql database query went through correctly */
   if(!$DJ_profile)
     die ('Error when querying for info on username ' . $username . 
          'information: ' . mysql_error());
            
   /* Getting a associative row of results from the query (there should be just
      one row here in this case */
   $user_info = mysql_fetch_assoc($DJ_profile);

   /* Checking that we have successfully gotten data from the database query */
   if(!$user_info)
      die('Error when getting assoc array from user query');

   
   /* Echo the header HTML information */
   echo "<html>";
      /* Head */
      echo "<head>";
         echo "<big><big><b>WSBF: Edit DJ and Show Profile Info</b></big></big>";
         echo "<title>WSBF: Edit Your Profile Information</title>";
         echo "<style>#top {text-align:right;}  #center {text-align:center;}</style>";

         /* Javascript for the character counter (for the show text areas) */
         echo "<script type='text/javascript' src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js'></script>";    
         echo "<script type='text/javascript' src='charCount.js'></script>";
      echo "</head>";
   
   
      /* Body */
      echo "<body>";

         /* Message at top giving user links to go back to main menu or to logout */
         echo "<br><h5>If you want to, you can go back to the <a href=\"..\submit_login.php\">main menu</a>, or you can <a href=\"..\logout.php\">log out</a>.</h5>";

         /* Form to collect all information. Posts to the file submit_edit_profile.php (in same folder) */ 
         echo "<form name='myForm' action='submit_edit_profile.php' method='POST' enctype='multipart/form-data' onsubmit='return validateForm();'>";
               
            // Posting the user's username, hidden 
            echo "<input type='hidden' name='username' value='$username'/>";

            // Presenting the form for the user to see/edit personal info
            echo "<b>Edit DJ Profile Info</b><br><br>";
            print_user_table($user_info);

            // Presenting the form for the user to see/edit info for each of
            // his/her active shows.
            echo "<br><br><b>Edit Info For Active Shows</b>";
            print_show_table($user_info);
			
			
			 /* Display the show description text box (max 2500 characters) with character counter */
			 /*
           echo " <small><i><code>Show description</code></i></small><br>
           <textarea name=\"txtarea_desc\" id=\"txtarea_desc\"  value=\"$show[description]\"  cols=\"40\" rows=\"5\"
           onkeyup=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\" onkeydown=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\" onmouseout=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\">$show[description]</textarea><br>
           ";

           echo "
           <small><small><code><span id='charcount_desc$i'>0</span> characters entered and <span id='remaining_desc$i'>2500</span> characters remaining.</code></small></small><br>";
         */
	

			echo "
			<small><i><code>Show Genre</code></i></small><br>
			
			<select name=\"genre\" id=\"genre\">
			<option value=\"Rock\">Rock</option>
			<option value=\"Loud Rock/Metal\">Loud Rock/Metal</option>
			<option value=\"Hip-Hop/Rap\">Hip-Hop/Rap</option>
			<option value=\"Indie\">Indie</option>
			<option value=\"Electronic\">Electronic</option>
			<option value=\"Folk/Americana/Bluegrass\">Folk/Americana/Bluegrass</option>
			<option value=\"Punk\">Punk</option>
			<option value=\"Pop\">Pop</option>
			<option value=\"Jazz/Blues/Soul\">Jazz/Blues/Soul</option>
			<option value=\"World\">World</option>
			<option value=\"R&B/Reggae\">R&B/Reggae</option>
			<option value=\"Dance\">Dance</option>
			</select>
			</form>
			<br>
			
			";
			//single query for schedule ID
			$show_query = sprintf("SELECT schedule.scheduleID, schedule_hosts.schedule_alias, schedule.description, schedule.show_name, schedule.description, schedule.genre, schedule.start_time, schedule.end_time, def_days.day
                                   FROM schedule_hosts, schedule, def_days
                                    WHERE schedule_hosts.username = '%s' and schedule.scheduleID = schedule_hosts.scheduleID and schedule.active = 1 and def_days.dayID = schedule.dayID", $username);
			  /* Execute the query */
			  $show_results = mysql_query($show_query, $link);
			  
			  /* Check that query worked */
			  if ($show_results == 0)
				   echo "<br><br>Yes it failed<br><br>";


			  /* Div for future purposes of traversing entries if we need to */
			  echo "<div id=\"shows_table\">";

			  /* Iterate over each show */
			  $i = 0;
			  $_show = mysql_fetch_assoc($show_results);
			
			echo "<input type='hidden' name='scheduleID' value='$_show[scheduleID]'/>";

            echo "<br><br><br>";

            // Button to submit the form, save the data, and go to the next page (sumit_edit_profile.php)
            echo "<div><input id=\"submit\" class='edit_profile' type='submit' name='submit_profile' value='Submit Info' /></div></form>";

      echo "</body>";
   echo "</html>";



   /* Function: print_user_table
      Outputs a table with info from the "users" table based on the username
      that is currently logged in, populated from a query done in the main
      body of the php script.
   */
function print_user_table($user_info) {

    $disabled = "disabled";
    echo "<table id=\"user_info_table\">\n";
      
         /* Username row */
       echo "<tr><td><div id=\"top\"><tt>Username:</tt></div></td><td> <INPUT TYPE = \"Text\" " . 
            "$disabled SIZE ='40' VALUE =\"$user_info[username]\" NAME = 'username' ></td></tr>\n";

         /* First name row */
       echo "<tr><td><div id=\"top\"><tt>First name:</tt></div></td><td> <INPUT TYPE = \"Text\" " . 
            "$disabled SIZE ='40' VALUE =\"$user_info[first_name]\" NAME = 'first_name' ></td></tr>\n";

         /* Last name row */
       echo "<tr><td><div id=\"top\"><tt>Last name:</tt></div></td><td> <INPUT TYPE = \"Text\" " . 
            "$disabled SIZE ='40' VALUE =\"$user_info[last_name]\" NAME = 'last_name' ></td></tr>\n";

         /* Preferred name row */
       echo "<tr><td><div id=\"top\"><tt>Preferred name:</tt></div></td><td> <INPUT TYPE = \"Text\" " . 
            "SIZE ='40' VALUE =\"$user_info[preferred_name]\" NAME = 'preferred_name' ></td></tr>\n";

         /* Email Address row */
       echo "<tr><td><div id=\"top\"><tt>Email address:</tt></div></td><td> <INPUT TYPE = \"Text\" " .
            "SIZE ='40' VALUE =\"$user_info[email_addr]\" NAME = 'email_addr' ></td></tr>\n";

         /* Phone number row */
       echo "<tr><td><div id=\"top\"><tt>Phone number:</tt></div></td><td> <INPUT TYPE = \"Text\" " .
            "SIZE ='40' VALUE =\"$user_info[phone_number]\" NAME = 'phone_number' ></td></tr>\n";


         /* Combo box to decide whether or not to recieve texts */ 
       echo "<tr><td><div id=\"top\"><tt>Receive texts:</tt></div></td><td>";
       echo "<select name=\"sms_recv\">";
       $sms_yes = 1;
       $sms_no = 0;
 
            /* Option to recieve cell phone texts from wsbf */
          echo "\t<option";
          if($user_info[sms_recv] == 1){
             echo " selected=\"true\"";  
          }
          echo " value=\"$sms_yes\">Yes</option>\r";

            /* Option to NOT recieve cell phone texts from wsbf */       
          echo "\t<option";
          if($user_info[sms_recv] == 0){
             echo " selected=\"true\""; 
          }
          echo " value=\"$sms_no\">No</option>\r";

       echo "</select>\n";
       echo "</td></tr>\n";




          /* Combo box to allow user to set his/her current status (active DJ at WSBF, inactive but still at
             Clemson and participating in wsbf social events, alumni and no longer living in Clemson, etc.) 
             Interns, people who are suspended, banned people, etc. are not allow to edit this */
       $statusID = $_SESSION['statusID'];
       if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4)
       {
          echo "<tr><td><div id=\"top\"><tt>Status at WSBF:</tt></div></td><td><select name=\"statusID\">";

          /* Option: user is actively involved with wsbf */
          echo "\t<option";
          if($statusID == 0){
             echo " selected=\"true\"";  
          }
          echo " value=\"0\">Active at WSBF</option>\r";

          /* Option: user semi-active at wsbf */
          echo "\t<option";
          if($statusID == 1){
             echo " selected=\"true\""; 
          }
          echo " value=\"1\">Semi-active at WSBF</option>\r";

          /* Option: user is not actively involved with wsbf, but still lives in Clemson and participates in events*/
          echo "\t<option";
          if($statusID == 1){
             echo " selected=\"true\""; 
          }
          echo " value=\"2\">Inactive at WSBF</option>\r";

          /* Option: user is a wsbf/Clemson alumna or alulmnus */
          echo "\t<option";
          if($statusID == 1){
             echo " selected=\"true\""; 
          }
          echo " value=\"4\">Alumnus/Alumna</option>\r";
  
          echo "</select>\n";
          echo "</td></tr>\n";
       }

       /* Otherwise: we set the user's status for them. They are not allowed to change their status (interns, banned people, etc.) */
       else{
          echo "<input type='hidden' name='statusID' value='$statusID'/>";
       }


       /* Display the profile paragraph */
      $i = 0;
       echo " <tr>
           <td><div id=\"top\"><tt>About you</tt></div></td><td>
           <textarea name=\"profile_paragraph\" id=\"profile_paragraph\"  value=\"$user_info[profile_paragraph]\"  cols=\"40\" rows=\"5\"
           onkeyup=\"CheckFieldLength(profile_paragraph, 'charcount_desc$i', 'remaining_desc$i', 2500);\" onkeydown=\"CheckFieldLength(profile_paragraph, 'charcount_desc$i', 'remaining_desc$i', 2500);\" onmouseout=\"CheckFieldLength(profile_paragraph, 'charcount_desc$i', 'remaining_desc$i', 2500);\">$user_info[profile_paragraph]</textarea><br></td>
           ";

           echo "
           <tr><td></td><td><small><small><code><span id='charcount_desc$i'>0</span> characters entered and <span id='remaining_desc$i'>2500</span> characters remaining.</code></small></small><br></td></tr>
           </form>";




       if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4){
          echo "<tr><td><tt>Profile picture:</tt></td><td><input type=\"file\" name=\"fileField\"></td></tr>";
          echo "<tr><td><tt>(Max size = 1 MB)</tt></td><td></td>";
       }


   echo "</table>\n";


   if ($statusID == 0 or $statusID == 1 or $statusID == 2 or $statusID == 4){
      displayProfilePicture();
   }



}
?>


<html>
<script type="text/javascript">   
// Javascript to check the telephone number and email address for correct format
function validateForm()
{
    var x=document.forms["myForm"]["email_addr"].value;
    // Regular expression defining an email address (not perfect)
    var filter_email = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{0,4}$/;
    if (!filter_email.test(x)){
       alert("Not a valid e-mail address");
       return false;
    }


    var y=document.forms["myForm"]["phone_number"].value;
    var filter_phone =/^\d{10}$/ //regular expression defining a 10 digit number
    if (!filter_phone.test(y)){
       alert("Not a valid phone number. Please enter 10 digits with no formatting or spaces.");
       return false;
    }
}
</script>


<?php
/* Print show table function
      Prints out a table containing information about each show the currently
      logged in user is a host of.  Only prints out active shows.  Utilizes
      the countChar function for character counting in the Show description.
*/
function print_show_table() {

   /* Define 'active' */
   $active = 1;
   global $username;
   global $link;



      /* Define mysql query to get info about the user's active shows */
      $show_query = sprintf("SELECT schedule.scheduleID, schedule_hosts.schedule_alias, schedule.description, schedule.show_name, schedule.description, schedule.genre, schedule.start_time, schedule.end_time, def_days.day
                                   FROM schedule_hosts, schedule, def_days
                                    WHERE schedule_hosts.username = '%s' and schedule.scheduleID = schedule_hosts.scheduleID and schedule.active = 1 and def_days.dayID = schedule.dayID", $username);

      /* Execute the query */
      $show_results = mysql_query($show_query, $link);
      
      /* Check that query worked */
      if ($show_results == 0)
           echo "<br><br>Yes it failed<br><br>";


      /* Div for future purposes of traversing entries if we need to */
      echo "<div id=\"shows_table\">";

      /* Iterate over each show */
      $i = 0;
      while($show = mysql_fetch_assoc($show_results)) {
      $i++;

            /* Print weekly show time for each of user's active shows*/
           echo "<br><br>";
           echo " <i> <em> <pre>Show #"  .  $i   . "      " .  $show["day"] . " @   ". $show["start_time"] . " - " . $show["end_time"] .  " </pre> </em> </i>";

            /* Post the show ID */
           echo "<input type='hidden' name='scheduleID$i' value='$show[scheduleID]'/>";


            /* Display the show name text box (max of 255 characters) with a character counter */
			/*
           echo "
                 <small><i><code>Show name</code></i></small><br>
                 <input type=\"text\" name=\"txtbox_showname$i\" id=\"txtbox_showname$i\"  value=\"$show[show_name]\"   size=\"50\" maxlength=\"255\"
                 onkeyup=\"CheckFieldLength(txtbox_showname$i, 'charcount_showname$i', 'remaining_showname$i', 255);\" onkeydown=\"CheckFieldLength(txtbox_showname$i, 'charcount_showname$i', 'remaining_showname$i', 255);\" onmouseout=\"CheckFieldLength(txtbox_showname$i, 'charcount_showname$i', 'remaining_showname$i', 255);\"><br>
           ";

           echo "
           <small><small><code><span id='charcount_showname$i'>0</span> characters entered and <span id='remaining_showname$i'>255</span> characters remaining.</code></small></small><br>
           <br>";
			*/
			
			 echo "
                 <small><i><code>Show name</code></i></small><br>
                 <input type=\"text\" name=\"txtbox_showname\" id=\"txtbox_showname\"  value=\"$show[show_name]\"   size=\"50\" maxlength=\"255\"
                 onkeyup=\"CheckFieldLength(txtbox_showname, 'charcount_showname', 'remaining_showname', 255);\" onkeydown=\"CheckFieldLength(txtbox_showname$i, 'charcount_showname', 'remaining_showname', 255);\" onmouseout=\"CheckFieldLength(txtbox_showname, 'charcount_showname', 'remaining_showname', 255);\"><br>
           ";

           echo "
           <small><small><code><span id='charcount_showname'>0</span> characters entered and <span id='remaining_showname'>255</span> characters remaining.</code></small></small><br>
           <br>";



           /* Display the DJ alias text box (max 45 characters) with character counter */
          
		  
		  
		  /*
		  echo "<small><i><code>DJ Alias for the Show</code></i></small><br>
          <input type=\"text\" name=\"txtbox_alias$i\" id=\"txtbox_alias$i\"  value=\"$show[schedule_alias]\" size=\"50\" maxlength=\"50\"
          onkeyup=\"CheckFieldLength(txtbox_alias$i, 'charcount_alias$i', 'remaining_alias$i', 45);\" onkeydown=\"CheckFieldLength(txtbox_alias$i, 'charcount_alias$i', 'remaining_alias$i', 45);\" onmouseout=\"CheckFieldLength(txtbox_alias$i, 'charcount_alias$i', 'remaining_alias$i', 45);\"><br>
          ";
		  
		   //<textarea name=\"profile_paragraph\" id=\"profile_paragraph\"  value=\"$user_info[profile_paragraph]\"  cols=\"40\" rows=\"5\"

          echo "<small><small><code><span id='charcount_alias$i'>0</span> characters entered and <span id='remaining_alias$i'>45</span> characters remaining.</code></small></small><br>
        <br>";
		 */
		 
		  echo "<small><i><code>DJ Alias for the Show</code></i></small><br>
          <input type=\"text\" name=\"txtbox_alias\" id=\"txtbox_alias\"  value=\"$show[schedule_alias]\" size=\"50\" maxlength=\"50\"
          onkeyup=\"CheckFieldLength(txtbox_alias, 'charcount_alias', 'remaining_alias', 45);\" onkeydown=\"CheckFieldLength(txtbox_alias, 'charcount_alias', 'remaining_alias', 45);\" onmouseout=\"CheckFieldLength(txtbox_alias, 'charcount_alias', 'remaining_alias', 45);\"><br>
          ";
		  
		  

          echo "<small><small><code><span id='charcount_alias'>0</span> characters entered and <span id='remaining_alias'>45</span> characters remaining.</code></small></small><br>
        <br>";
		 
		 
		 
		 
		 /* Display the show description text box (max 2500 characters) with character counter */
		 /*
           echo " <small><i><code>Show description</code></i></small><br>
           <textarea name=\"txtarea_desc$i\" id=\"txtarea_desc$i\"  value=\"$show[description]\"  cols=\"40\" rows=\"5\"
           onkeyup=\"CheckFieldLength(txtarea_desc$i, 'charcount_desc$i', 'remaining_desc$i', 2500);\" onkeydown=\"CheckFieldLength(txtarea_desc$i, 'charcount_desc$i', 'remaining_desc$i', 2500);\" onmouseout=\"CheckFieldLength(txtarea_desc$i, 'charcount_desc$i', 'remaining_desc$i', 2500);\">$show[description]</textarea><br>
           ";

           echo "
           <small><small><code><span id='charcount_desc$i'>0</span> characters entered and <span id='remaining_desc$i'>2500</span> characters remaining.</code></small></small><br>
           </form>";
*/

echo " <small><i><code>Show description</code></i></small><br>
           <textarea name=\"txtarea_desc\" id=\"txtarea_desc\"  value=\"$show[description]\"  cols=\"40\" rows=\"5\"
           onkeyup=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\" onkeydown=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\" onmouseout=\"CheckFieldLength(txtarea_desc, 'charcount_desc', 'remaining_desc', 2500);\">$show[description]</textarea><br>
           ";

           echo "
           <small><small><code><span id='charcount_desc$i'>0</span> characters entered and <span id='remaining_desc$i'>2500</span> characters remaining.</code></small></small><br>";
		
           
   }

    /* Post the counted number of active show that the user hosts */
   echo "<input type='hidden' name='show_count' value='$i'/>";

}
      
function displayProfilePicture(){
   $username = $_SESSION['username'];   
   $pictFolder = "user_profile_pictures/".$username."/";
   if (file_exists($pictFolder)) {
      $isFolderEmpty = (bool) (count(scandir($pictFolder)) == 2);
      if ($isFolderEmpty) {
         echo "<br> No current profile picture<br>";
      }
      else{
         $stre = "user_profile_pictures/".$username."/*.*";

         foreach(glob($stre) as $filename){
            $existingPictName = $filename;
         }

         echo "<br> <tt>Current profile picture:</tt><br>";
         echo "<img src=\"$existingPictName\" alt=\"Current user profile picture.\" />";

         echo "<br><input type=\"checkbox\" name=\"checkBoxDeletePic\" value=\"deletePic\" /><tt>Delete current profile picture</tt><br />";
      }   
   }
   else{
      echo "<br> No current profile picture<br>";
   }
}


?>









