<?php

/** File name: view_show_profiles.php 
 *  Author: Edward Sullivan (ELSULLI@g.clemson.edu, 240 383 0498)
 *  Date: 30 August, 2012
 *  Purpose: Allow people to see the names of all the active named shows.
 *           Displays the weekly time of the show, the names of the hosts,
 *           and a description of the show.
 */





echo "<html>";
   echo "<head>";

      echo " <meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />
         <meta name=\"author\" content=\"Edward Sullivan\" />
         <meta name=\"keywords\" content=\"WSBF, wsbf, Clemson, radio, music, stream, online, mp3, profile, DJ, show, view\" />
         <meta name=\"description\" content=\"WSBF FM Clemson is an alternative local radio station broadcasting from Clemson University. Page to view show info.\" />
         <meta name=\"robots\" content=\"all\" />
         <title>WSBF FM CLEMSON: View Show Info</title>
         <h3>View WSBF Show Profiles</h3>";
      
   echo "</head>";

   echo "<body>";

      require_once("../connect.php"); // To connect to the database
      global $link;

       // Navigation links at top
       echo "<h5>If you want to, you can go back to the <a href=\"..\submit_login.php\">main menu</a>, or you can <a href=\"..\logout.php\">log out</a>.</h5>";

      // Defining MySQL database query to get the names and IDs of all the shows
      // that are active and are named.
      $shows_query = sprintf("SELECT scheduleID, show_name
                              FROM `schedule`
                              WHERE active='1' and show_name !=''");

      // Submitting the query 
      $shows_query_results = mysql_query($shows_query, $link) or die ("A MySQL error has occurred.<br />Your Query: " . $shows_query . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());


      // Storing the show names and IDs in arrays and counting the number of active shows with names
      for($shows_count=0; $shows_query_row = mysql_fetch_assoc($shows_query_results); $shows_count++){
         $array_scheduleID[$shows_count] = $shows_query_row["scheduleID"];
         $array_show_name[$shows_count] = $shows_query_row["show_name"];
      }  

      // Form that POSTs to itself so the user stay on the same page after submitting the form
      echo "<form name=frmTest action=\"view_show_profiles.php\" method=POST>";
      
         // Combo box. When the user changes which option is selected, the form is submitted. This way,
         // no ugly submit button is needed
         echo "<h4>Select a show: </h4>";
     
         echo "<select name=\"comboName\" size=\"1\" onChange=\"frmTest.submit();\">";

            // Starting option
            echo "<option value=\"-1\"> Choose One </option> ";

            // One combo box option for each show, the value of the menu option is the same as the index
            // of the show in the arrays for the show names and show IDs
            for($k=0; $k<$shows_count;$k++)
            {
               echo "<option value='$k'>$array_show_name[$k]</option>";
            }

         echo "</select>";
      echo "</form>";

      // If the user has selected any show at least once, this section of code will post
      // the details of the most recent show selection
      if(isset($_POST['comboName'])){

         // Getting the choice selected in the previous combo box
         $show_to_display_ID = $_POST['comboName'];

         // Don't display any show details of the user selected the "Choose one" option
         // from the combo box menu
         if ($show_to_display_ID != -1){
         
            // Dispay name of the selected show
            echo "<br><br><br><h2>$array_show_name[$show_to_display_ID]</h2>";

            // Defining a new MySQL database query to find the day of the week of the show, the hour of the show, the show's 
            // description, the names and corresponding aliases all the show's hosts
            $single_show_query = sprintf("SELECT def_days.day, schedule.start_time, schedule.end_time, 
                                          schedule.description, schedule_hosts.schedule_alias,users.first_name, users.last_name, users.username
                         FROM `def_days`, `schedule`,`schedule_hosts`,`users`
                         WHERE def_days.dayID=schedule.dayID and schedule.scheduleID='$array_scheduleID[$show_to_display_ID]' and 
                               schedule_hosts.scheduleID = schedule.scheduleID and users.username=schedule_hosts.username");

           // Submitting the query
           $single_show_query_results = mysql_query($single_show_query, $link) or die ("A MySQL error has occurred.<br />Your Query: " . $single_show_query . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());

           // Displaying the show's day and hour, as well as the names and alliases of all the hosts
           for($host_count=0; $host_query_row = mysql_fetch_assoc($single_show_query_results); $host_count++){
              if ($host_count ==0){  // First show host
                 echo "<pre>$host_query_row[day]:      $host_query_row[start_time] - $host_query_row[end_time]</pre>"; // Using <pre> to keep whitespace formatting
                 echo "<br><b>Show hosts:</b> <br>$host_query_row[first_name] $host_query_row[last_name]";
                 if ($host_query_row['schedule_alias'] != NULL)
                    echo " ($host_query_row[schedule_alias])";
                 displayProfilePic($host_query_row['username']); 
              }
              else { // All remaining show hosts
                 echo "<br> and $host_query_row[first_name] $host_query_row[last_name]";
                 if ($host_query_row['schedule_alias'] != NULL)
                    echo " ($host_query_row[schedule_alias])"; 
                 displayProfilePic($host_query_row['username']);             
              }
          }

          // Dispaying the description of the show
          echo "<br><br><b>Show description:</b><br>";
          mysql_data_seek ($single_show_query_results,0); // Restting the row pointer for the query reseult
          $host_query_row = mysql_fetch_assoc($single_show_query_results);            
          echo "<table width=\"400\"><tr><td>$host_query_row[description]</td></tr></table>";              
       }   
    }

   function displayProfilePic($showHostUsername){

      $pictFolder = "user_profile_pictures/".$showHostUsername;
      if (file_exists($pictFolder)) {  
         $pictFolder = "user_profile_pictures/".$showHostUsername."/";
         $isFolderEmpty = (bool) (count(scandir($pictFolder)) == 2);
         if (!$isFolderEmpty) {
            $stre = "user_profile_pictures/".$showHostUsername."/*.*";

            foreach(glob($stre) as $filename){
               $existingPictName = $filename;
            }

            echo "<br><img src=\"$existingPictName\" alt=\"Current user profile picture.\" />";
         }   
      }  
   }  

  echo "</body>";
echo "</html>";







?>
