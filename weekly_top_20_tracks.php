<?php
/****************************************************
 * File: weekly_top_20_tracks.php viewable at http://new.wsbf.net/wizbif/weekly_top_20_tracks
 * Author: Ed Sullivan ELSULLI@g.clemson.edu
 * Date: March 30, 2012
 * Purpose: Display the 20 songs played by DJs in the last week and other info about the songs
 * Note: 
 *
 *       I should propably re-write part of this so that
 *       fewer database queries are needed so that it will
 *       run faster (I'm new to mysql), but this is not a priority.
 ***************************************************/



   /* Book Keeping */
             /* Ensure that we don't already have a session */
             if(!session_id())
                session_start();

             $disabled = "disabled";

             //JYM - Moved the require_once up here.  We should keep all library includes
             //      in one spot.
             //include ('../position_check.php');
             require_once("connect.php");
   
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
   
             /* JYM
                In the future, we might want to consider forwarding the user to a generic
                HTTP 401 Error Page.  It keeps it nice and secure because they'll be
                moved from the page on a bad username, instead of being able to look at
                whatever document stuff is kept in the page on a bad user.
                But we'll worry about that later on */
      
             /* What does statusID == 7 mean?  As in, what does 7 refer to?
                I intuitively know, but we should provide a clear comment on what that
                means. Status 7 means that the user is permanently banned from WSBF for life */
             if($statusID == 7){ 
                //banned 4 life
                die ("Like we'd let $pref_name see our top 20 tracks?  Skedaddle.");
             }
   /* Book Keeping is done */

            
   
   /* Echo the header HTML information */
   echo "<html>";
   
             /* Head */  
             echo "<head>";
                       echo "<title>WSBF: Weekly Top-20</title>";
                       echo "<style>#top {text-align:right;}  #center {text-align:center;}</style>";
                       echo "<script type='text/javascript' src='http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js'></script>";
             echo "</head>";
   

             /* Body */
             echo "<body>";
                       echo "<h2> WSBF: Weekly Top-20 Rotation Tracks Played By DJs <h2>";

                       /* Offer user option to return to main menu or log out */
                       echo "<h5>If you want to, you can <a href=\"submit_login.php\">go back</a>, or you can <a href=\"logout.php\">log out</a>.<h5>";

                       /* Print table of top 20 songs and info about the songs */
                       print_song_list();

                       /* Echo info about the list of songs */
                       echo "<h5> Songs not included in rotation are not considered in this list.  <h5>";
                       echo "<h5> The \"Plays within last week\" count value includes only plays by DJs (does not count song plays by automation).<h5>";
               
             echo "</body>";
   echo "</html>";



   
   /* Print show table function
         Prints out a table containing information about each show the currently
         logged in user is a host of.  Only prints out active shows.  Utilizes
         the countChar function for character counting in the Show description.
   */

function print_song_list() 
{
      global $link;

      /* Find the current time in unix timestamp format */
      $right_now_unix = strtotime("now");

      /* Find the time of exactly one week ago in unix timestamp format */
      $one_week_ago_unix = strtotime ('-1 week' , $right_now_unix);

      /* Convert last week's time from the unix timestamp to English format YYYY-MM-DD HH:MM:SS (hours ranging from 0 to 23) */
      $one_week_ago_english = date("Y-m-d H:i:s", $one_week_ago_unix);

      /* Write query to find all songs (and their associated albums/tracks) from the logbook played within the last week (there can be repeats) */
      /* Limit it to shows that are not automation (show.show_typeID = 8 for automation) */
       $log_query = sprintf("SELECT logbook.logbookID, logbook.lb_album_code, logbook.lb_track_num, show.show_typeID 
                              FROM `logbook`, `show`
                              WHERE logbook.showID = show.showID and show.show_typeID != 8 and logbook.time_played >= '%s' and logbook.lb_album_code != '' and (logbook.lb_rotation = 'N' or logbook.lb_rotation = 'H' or logbook.lb_rotation = 'M' or logbook.lb_rotation = 'L')",
                                   $one_week_ago_english);


      /* Execute the query */
      $log_results = mysql_query($log_query, $link) or die ("A MySQL error has occurred.<br />Your Query: " . $log_query . "<br /> Error: (" . mysql_errno() . ") " . mysql_error());
      //$log_results = mysql_query($log_query, $link);


      /* Set the initial count of distinct tracks equal to 0 */
      $num_distinct_tracks = 0;

      $distinct_track_array[0] = array("album"=>0, "track"=>0, "plays"=>0);

      /* While loop will traverse all the songs played within the last week and create an array entry for each distinct track and count
         the number of occurences/plays for each particular track */
      while($log = mysql_fetch_assoc($log_results))
     {
         /* Initially assume that the track is distinct (has not yet been found in the query) */
         $is_track_distinct = 1;

         /* Search the growing list of distinct logbook tracks to determine if the current track is already on the list or not */
         for ($i = 0; $i <  $num_distinct_tracks and $is_track_distinct == 1; $i++)
         {
              /* If the track is already on the list of distinct tracks, just incremenet its count, but do not add it to the list again */
              if($distinct_track_array[$i]["album"] == $log["lb_album_code"] and $distinct_track_array[$i]["track"] == $log["lb_track_num"])
              {
                   $is_track_distinct = 0; /* Indicates that track is aleady on list of distinct tracks */
                   $distinct_track_array[$i]["plays"]++;
              }
         }

         /* If the track is new/distinct (not already on the growing list of distinct tracks, then add it to the list of distinct tracks */
         if ($is_track_distinct == 1)
         {
             /* Add the current track to the growing 2-D array of distinct tracks */
 
             $distinct_track_array[$num_distinct_tracks]["album"] = $log["lb_album_code"];
             $distinct_track_array[$num_distinct_tracks]["track"] = $log["lb_track_num"];

             /* Since this is the first occurence of the song in the the logbook query, mark the count as 1*/
             $distinct_track_array[$num_distinct_tracks]["plays"] = 1;

             /* Increment count of distinct tracks*/
             $num_distinct_tracks++;
         }
    }



    /* Sorting the 2-D array of distinct tracks based on number of plays in descending order */
    usort($distinct_track_array, 'cmpplays');



      echo "<table id=\"top_20_song_table\" border='1'>";
      echo "<tr> <td><b>Rank</b></td> <td><b>Song</b></td> <td><b>Artist</b></td> <td><b>Album</b></td> <td><b>Album code</b></td> <td><b>Rotation</b></td> <td><b>Plays within last week</b></td> </tr>";
      for ($k=0;$k < 30;$k++)
      {

           /* Getting info for the specific song */
           $song_query = sprintf("SELECT lb_track_name, lb_artist, lb_album, lb_rotation 
                                 FROM logbook
                                 WHERE lb_album_code = '%s' and lb_track_num = %d", 
                                          $distinct_track_array[$k]["album"], 
                                          $distinct_track_array[$k]["track"]
                               );

           /* Execute the query */
           $song_results = mysql_query($song_query, $link);
         
           $rank = $k + 1;
           $song = mysql_fetch_assoc($song_results);
           $song_album_code = $distinct_track_array[$k]["album"];
           $song_plays = $distinct_track_array[$k]["plays"];

           /* Username row */
           echo "<tr>";
               echo "<td> $rank                               </td>"; //Rank
               echo "<td> $song[lb_track_name]                </td>"; //Song name
               echo "<td> $song[lb_artist]                    </td>"; //Artist
               echo "<td> $song[lb_album]                     </td>"; //Album
               echo "<td> $song_album_code                    </td>"; //Album code 
               echo "<td> $song[lb_rotation]                  </td>"; //Rotation
               echo "<td> $song_plays                         </td>"; //Plays
         echo "</tr>";
      }

      echo "</table>\n";

}
 


/* Comparison function: for sorting (descending) a 2D array of tracks based on number of song plays */ 
function cmpplays($a, $b)
{
    if ($a["plays"] == $b["plays"]) 
    {
          return 0;
    }

    return ($a["plays"] > $b["plays"]) ? -1 : 1;
}


?>

