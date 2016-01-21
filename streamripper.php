<?php
include_once('connect.php');

/**
 * This page is used by streamripper, which runs on "john" (130.127.17.39).
 * Streamripper is configured to pull this metadata with the script 
 * /home/compe/fetch_external_metadata.pl, which requests this 10 seconds. 
 * 
 * Streamripper uses the result from this to split and name the tracks.
 * The default behavior for streamripper is to name the track as <TITLE> - <ARTIST>.mp3.
 * We use the -D flag to set the name to only the title (-D %T). However, it appears that
 * streamripper uses the default configuration (name,artist).mp3 in the incomplete directory,
 * and then renames it as specified by the argument once the track is changed (i.e. new show).
 * 
 * [dcohen @ 2015-02-05] 
 */    

$showQuery   = "SELECT 
					s.showID, 
					GROUP_CONCAT(u.preferred_name ORDER BY u.preferred_name asc) dj_names 
				FROM `show` s
                INNER JOIN `show_hosts` sh on sh.showID = s.showID
                INNER JOIN `users` u on u.username = sh.username
                INNER JOIN `def_show_types` dst on dst.show_typeID = s.show_typeID
                
                GROUP BY s.showID
                ORDER BY s.showID desc
                LIMIT 1";
($showResult = mysql_query ($showQuery)) or die("MySQL error [". __FILE__ ."] near line " . __LINE__ . ": " .mysql_error());

$aCurrentShow = mysql_fetch_assoc($showResult);
$sShowID      = $aCurrentShow['showID'];
$sShowDJs     = $aCurrentShow['dj_names'];

echo 'title="' . $sShowID . '" artist="' . $sShowDJs . '"';

?>