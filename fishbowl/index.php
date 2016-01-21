<?php
require_once('../header.php');
require_once('../conn.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>WSBF: Fishbowl</title>
	<link rel="stylesheet" href="../style.css" />
	<link rel="stylesheet" href="/jqui/css/smoothness/jquery-ui-1.8.16.custom.css">
	<script type="text/javascript" src="/jqui/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/jqui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" language="javascript" src="fishbowl.js"></script>
</head>
<body>
<div id="container">
<center><h1>WSBF-FM Clemson</h1></center>
	<h2>WSBF Fishbowl Signup</h2>
	<p><div id="successMessage" style="display:none" class="success"></div></p>
<?php
	include("fishbowl_app.php");
?>

</div>
</body>
</html>
