<?php

$output = shell_exec("netstat -n"
	. " | grep '130.127.17.4:8000'"
	. " | grep ESTABLISHED"
	. " | grep -v '130.127.17.6'");

$lines = explode("\n", $output);

array_pop($lines);

$addresses = array_map(function($line) {
	$row = explode(" ", preg_replace("/\s+/", " ", $line));

	return explode(":", $row[4])[0];
}, $lines);

$count = count(array_unique($addresses));

echo $count;
?>
