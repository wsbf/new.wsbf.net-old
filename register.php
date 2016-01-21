<?php
require_once('header.php');
require_once('hash_functions.php');
// username	password	first_name	last_name	preferred_name 	phone_number	email_addr	teamID	statusID	sms_recv
echo "<h1>WSBF-FM Clemson :: Register New User</h1>";
if($_POST){
	foreach($_POST as $k=>$v)
		$$k = mysql_real_escape_string($v);
	

$query = sprintf("SELECT username FROM `users` WHERE username = '%s'", $username);
$result = mysql_query($query) or die("Query failed : " . mysql_error());
$errormsg = '';

while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	if($username == $row['username'])
		$errormsg .= "Sorry, that username already exists. Please try another. <br />";
}
	if(!$username)
		$errormsg .= "Please pick a username. <br />";

	if(!$first_name || !$last_name)
		$errormsg .= "Please put your full name. <br />";



	//checks to see if you entered a password, and if they match.
	if (!$_POST['password0'])
		$errormsg .= "Please enter a password. <br />";
	elseif ($_POST['password0'] != $_POST['password1'])
		$errormsg .= "Passwords don't match! Try Again. <br />";
	//checks for proper email_addr
  if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email_addr)) 
		$errormsg .= "Please input a proper email address. <br />";	
	if($errormsg){
		echo "<div class='errormsg'>";
		echo "<font color='#ff0000'> $errormsg </font>";
		echo "</div>";

		echo "<form method='POST'><table><tr><td></td><td></td></tr>";
		echo "<tr><td>Desired username</td><td><input type='text' name='username' value='$username' /></td></tr>";
		echo "<tr><td>First Name</td><td><input type='text' name='first_name' value='$first_name' /></td></tr>";
		echo "<tr><td>Last Name</td><td><input type='text' name='last_name' value='$last_name' /></td></tr>";
		echo "<tr><td>Password</td><td><input type='password' name='password0' /></td></tr>";
		echo "<tr><td>Retype Password</td><td><input type='password' name='password1' /></td></tr>";
		echo "<tr><td>Email Address</td><td><input type='text' name='email_addr' value='$email_addr' /></td></tr>";
		echo "<tr><td>Phone</td><td><input type='text' name='phone_number' value='$phone_number' /></td></tr>";
		echo "<tr><td></td><td><input type='submit' value='Submit'>";
		echo "</table>";

	}
	else{
	$password = HashPassword($password0);
	$teamID = 0;
	$query = sprintf("INSERT INTO users (username, password, first_name, last_name, preferred_name, email_addr, phone_number, teamID) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %d)", $username, $password, $first_name, $last_name, $first_name . ' ' . $last_name, $email_addr, $phone_number, $teamID);
	$insert = mysql_query($query) or die(mysql_error());
	echo "Thanks for registering, $first_name! Now you can <a href='login.php'>log in.</a><br>If you are an intern, please email the <a href='mailto:computer@wsbf.net'>Computer Engineer</a> to get CD review permissions. ";
		
	}
	
}

else{
	$username = $first_name = $last_name = $email_addr = $phone_number = '';
		echo "<form method='POST'><table><tr><td></td><td></td></tr>";
		echo "<tr><td>Desired username</td><td><input type='text' name='username' value='$username' /></td></tr>";
		echo "<tr><td>First Name</td><td><input type='text' name='first_name' value='$first_name' /></td></tr>";
		echo "<tr><td>Last Name</td><td><input type='text' name='last_name' value='$last_name' /></td></tr>";
		echo "<tr><td>Password</td><td><input type='password' name='password0' /></td></tr>";
		echo "<tr><td>Retype Password</td><td><input type='password' name='password1' /></td></tr>";
		echo "<tr><td>Email Address</td><td><input type='text' name='email_addr' value='$email_addr' /></td></tr>";
		echo "<tr><td>Phone (optional)</td><td><input type='text' name='phone_number' value='$phone_number' /></td></tr>";
		echo "<tr><td></td><td><input type='submit' value='Submit'>";
		echo "</table>";

}

//require_once('footer.php');
?>
