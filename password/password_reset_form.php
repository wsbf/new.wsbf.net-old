<html>
<head>
<title> WSBF: Reset Password </title>
<meta name="author" content="Edward Sullivan" />
</head>
<body>
<?php

   require_once("../connect.php"); // Connecting to the database


   if(isset($_GET['transaction_id'])){

      $transaction_id = $_GET['transaction_id'];

      /* Find the current time in unix timestamp format */
      $right_now_unix = strtotime("now");

      /* Convert the unix current time to  English format
       YYYY-MM-DD HH:MM:SS (hours ranging from 0 to 23) */
      $right_now_english = date("Y-m-d H:i:s", $right_now_unix);

      // query
      $q = sprintf("SELECT username FROM `password_reset` WHERE transaction_id = '%s' and expiration_date >= '%s' LIMIT 1", $transaction_id, $right_now_english);

      // query result
      $qr = mysql_query($q,$link) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

      if (mysql_num_rows($qr) > 0){
         $row = mysql_fetch_assoc($qr);
         echo '<form name=password_reset action="password_reset_submit.php" method=POST>';
         echo '<row><div>New password: </div> <div><INPUT TYPE ="Text" SIZE ="40" NAME ="new_password" ></div></row>';
         echo "<input type=\"hidden\" name=\"username\" value=\"$row[username]\"/>";
         echo '<input id="submit" type="submit" name="submit_profile" value="Submit" /></form>';
      }
      else
         echo "Please email the computer engineer (computer@wsbf.net) if you need to have your password reset."; 
   }
   else
      echo "Please email the computer engineer (computer@wsbf.net) if you need to have your password reset."; 

?>

</body>
</html>
