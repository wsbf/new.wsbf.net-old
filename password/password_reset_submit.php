<html>
<head>
<title> WSBF: Reset Password </title>
<meta name="author" content="Edward Sullivan" />
</head>
<body>
<?

   require_once("../hash_functions.php");
   require_once("../connect.php"); // Connecting to the database


   if(isset($_POST['username'])){ // Basic check to make sure that the posted info from password_reset_form.php is present
      $username = $_POST['username']; 
		$new_password_hashed = hashPassword($_POST['new_password']);

      $q = sprintf("UPDATE users SET password='%s' WHERE username='%s'", $new_password_hashed, $username);
      mysql_query($q) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

      $q2 = sprintf("DELETE FROM password_reset WHERE username='%s'",$username);            
      mysql_query($q2) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

      echo "Thank you for updating your password."; 
   }
   else{
      echo "Password not updated."; 
   }

?>

</body>
</html>
