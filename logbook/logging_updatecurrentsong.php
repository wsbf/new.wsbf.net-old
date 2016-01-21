<?php

//Accessed via AJAX call from logging software
//Sets currently playing song using primary key in lbplaylist
//Zach Musgrave, WSBF-FM Clemson, Oct 2009

require_once("../conn.php");
require_once("logging_functions.php");

if(!isset($_GET['pid']))
	die();
if(!get_magic_quotes_gpc())
	$_GET['pid'] = addslashes($_GET['pid']);

$logbookID = $_GET['pid'];

setNowPlaying($logbookID);

// mysql_close();
?>