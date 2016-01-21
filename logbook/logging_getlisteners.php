<?php
require_once('../conn.php');

// simply get number of listeners and output it

$listeners = getNumConnections("http://130.127.17.4:8000/status.xsl");
if($listeners)
	echo $listeners;
else
	echo "-1";


?>