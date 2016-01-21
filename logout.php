<!DOCTYPE html>
<html lang="en">

<?php
	session_start();
	session_destroy();
?>

<head>
	<meta charset="utf-8">
	<title>Logout</title>
	<link rel="stylesheet" href="style.css" />
</head>

<body>
	<div id="container">
		<center><h1>WSBF-FM Clemson</h1></center>
		<p>Now you're logged out!<br>If you want to log back in, <a href="../login">click here!</a></p>
	</div>
</body>
