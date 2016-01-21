      


<html>
   <head>
      <title>WSBF: DJ Album Review Index</title>
      <meta name="author" content="Edward Sullivan" />
	   <link rel="stylesheet" href="/jqui/css/smoothness/jquery-ui-1.8.16.custom.css">
	   <script type="text/javascript" src="/jqui/js/jquery-1.6.2.min.js"></script>
	   <script type="text/javascript" src="/jqui/js/jquery-ui-1.8.16.custom.min.js"></script>
	   <script type="text/javascript" src="/jqui/other_jq/timeentry/jquery.timeentry.min.js"> </script>
	   <script type="text/javascript" src="/jqui/development-bundle/ui/jquery.ui.widget.js"> </script>
	   <script type="text/javascript" src="/jqui/development-bundle/ui/jquery.ui.datepicker.js"> </script>
	   <script>
         $(function() {
		      $( "#datepicker1" ).datepicker();
		      $( "#datepicker2" ).datepicker();
	      });
	   </script>
      <link rel='stylesheet' type='text/css' href='style.css' />	
      <script type="text/javascript" src="/jqui/js/jquery-1.6.2.min.js"></script>
      <script type="text/javascript" src="/jqui/js/jquery-ui-1.8.16.custom.min.js"></script>
      <link rel="stylesheet" type="text/css" href="/jqui/css/smoothness/jquery-ui-1.8.16.custom.css" />

   </head>


   <body>
	<div id="container">

	   <h1><a href="http://new.wsbf.net">WSBF-FM Clemson</a></h1>
      <h2>Albums Reviewed By DJs</h2>
      <hr />

      <!-- Form to get start date and end date with Javascript. GETs back to itself -->
      <form name=frmTest action="reviewsByActiveDJs.php" method=GET>
         <table>

            <tr>
            <td>Start Date:</td>
            <td><input type="text" class="required ui-state-default ui-corner-all" id="datepicker1" name="start_date" /></td>
            </tr>

            <tr>
            <td>End Date:</td>
            <td><input type="text" class="required ui-state-default ui-corner-all" id="datepicker2" name="end_date" /></td>
            </tr>
       
            <tr>
            <td>Apply filter:</td>
		      <td><input type='submit' value='Submit' name='submit' class='ui-state-default ui-corner-all' /></td>
            </tr>

         </table>
      </form> 
      <hr />

      <?php

         /* Connecting to the database */
         require_once("connect.php");


         /* Default start date/time */
         $start_english = "1974-12-31 19:00:00";
         $start_english_default = "1974-12-31 19:00:00";

         /* Default end date/time */
         $now_unix = strtotime("now");
         $now_english = date("Y-m-d H:i:s", $now_unix);
         $end_english = $now_english;      
         $end_english_default = $now_english;

         /* Getting the start date from the Javascrip input calendar*/
         if(isset($_GET['start_date'])){
            $start_date = $_GET['start_date'];

            /* Getting the start time in English for MySQL */
            $start_Y = date('Y', strtotime($start_date)); // year
            $start_m = date('m', strtotime($start_date)); // month
            $start_d = date('d', strtotime($start_date)); // day
            // $start_H = date('H', strtotime($start_time)); // hour
            // $start_i = date('i', strtotime($start_time)); // minute
            // $start_s = date('s', strtotime($start_time)); // second
            $start_H = date('H', strtotime("00:00:00")); // hour
            $start_i = date('i', strtotime("00:00:00")); // minute
            $start_s = date('s', strtotime("00:00:00")); // second
            // Format for mktime is (hour, min, sec, month, day, year, day light savings)
            $start_unix = mktime($start_H, $start_i, $start_s, $start_m, $start_d, $start_Y);
            $start_english = date("Y-m-d H:i:s", $start_unix);
         }  
    
         /* Getting the end date from the Javascrip input calendar */
         if(isset($_GET['end_date'])){
            $end_date = $_GET['end_date'];

            /* Getting the end time in English for MySQL */
            $end_Y = date('Y', strtotime($end_date)); // year
            $end_m = date('m', strtotime($end_date)); // month
            $end_d = date('d', strtotime($end_date)); // day
            //$end_H = date('H', strtotime($end_time)); // hour
            //$end_i = date('i', strtotime($end_time)); // minute
            //$end_s = date('s', strtotime($end_time)); // second
            $end_H = date('H', strtotime("00:00:00")); // hour
            $end_i = date('i', strtotime("00:00:00")); // minute
            $end_s = date('s', strtotime("00:00:00")); // second
            // Format for mktime is (hour, min, sec, month, day, year, day light savings)
            $end_unix = mktime($end_H, $end_i, $end_s, $end_m, $end_d, $end_Y);
            $end_english = date("Y-m-d H:i:s", $end_unix);
         }   

         /* Setting date range (seeing if values from calendar do not match default values) */
         if ($start_english == "1969-12-31 00:00:00"){
            $start_english = $start_english_default;
         }
         if ($end_english == "1969-12-31 00:00:00"){
            $end_english = $end_english_default;
         }


         echo "<h4>Albums reviewed between $start_english and $end_english </h4><br>"; 


         /* For each active user, query to count all their CD reviews within the selected date range */
         $cd_count_query = sprintf("SELECT u.*, p.*,
                              (IFNULL( COUNT( p.`albumID` ) , 0 )) AS reviewCount 
                              FROM `users` u
                              LEFT JOIN `libreview` p
                              ON u.`username` = p.`username` 
                              AND p.`review_date` >= \"%s\" 
                              AND p.`review_date` <= \"%s\" 
                              WHERE `statusID` = 0
                              GROUP BY u.`username`
                              ORDER BY `last_name`, `first_name`, p.`username`",$start_english, $end_english);

         /* For each active user, query to list their reviewed album IDs for the selected date range */
         $cd_id_query = sprintf("SELECT u.*, p.*,
                              (IFNULL( COUNT( p.`albumID` ) , 0 )) AS reviewCount 
                              FROM `users` u
                              LEFT JOIN `libreview` p
                              ON u.`username` = p.`username` 
                              AND p.`review_date` >= \"%s\" 
                              AND p.`review_date` <= \"%s\" 
                              WHERE `statusID` = 0
                              GROUP BY p.`albumID`
                              ORDER BY `last_name`, `first_name`, p.`username`",$start_english, $end_english);



         /* Submitting the queries */
         $cd_count_query_result = mysql_query($cd_count_query,$link) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());
         $cd_id_query_result = mysql_query($cd_id_query,$link) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

         /* Getting the first row for the query of albumIDs (so we can match the first one's username) */
         $cd_id_query_row = mysql_fetch_assoc($cd_id_query_result);

         /* Displaying output in simple table*/
         echo "<table>";
   
         /* Table header row */
         echo "<tr><td><i>First</i></td><td><i>Last</i></td><td><i>#</i></td><td align='center'><i>Albums</i></td></tr>";
         /* Spacer row */
         echo "<tr><td>.</td><td></td><td></td><td></td></tr>";

         /* For each active user, printing the first and last name and the count of CDs reviews with given date range */
         while ($cd_count_query_row = mysql_fetch_assoc($cd_count_query_result)){
               echo "<tr><td>$cd_count_query_row[first_name]</td>
                     <td>$cd_count_query_row[last_name]</td>
                     <td>$cd_count_query_row[reviewCount]</td>
                     <td>";
                        /* For each active user, printing all the IDs (links) for the CDs that the user reviewed in the date range */
                        while (strtoupper($cd_id_query_row['username']) == strtoupper ($cd_count_query_row['username']) && $cd_id_query_row != FALSE ) {
                             echo "<a href=\"http://new.wsbf.net/wizbif/read_review.php?albumID=$cd_id_query_row[albumID]\">$cd_id_query_row[albumID] </a>";
                             $cd_id_query_row = mysql_fetch_assoc($cd_id_query_result);
                        }
               echo "<td>
                     </tr>";
         }

         echo "</table>";
 

      ?>

   </div>
   </body>

</html>



