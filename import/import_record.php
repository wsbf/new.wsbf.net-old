<?php
require_once("../connect.php");
require_once("../library_functions.php");
require_once("../position_check.php");
	
session_start();
if(!(MD_check())){
	die ('You aren\'t allowed to be here!<br>Go <a href='.$_SERVER['HTTP_REFERER'].'>back.</a><br>');
}

echo "<h1>PRELIMINARY IMPORT SYSTEM</h1>\n";
echo "<h3>Import a Record</h3>\n";
echo "<p>This page imports <b>one record at a time.</b> Or go <a href='import_main.php'>back</a>...</p>\n";
echo "<div id='contents'>";
echo "<title>WSBF Import Music</title>";

echo "<form name='record' method='POST' action='record_submit.php'>";
echo "<table id='albumData' ><tr><th></th><th></th>";
echo "<tr><td>Album Artist: </td><td><input id='artist' type='text' name='artist' value=\"\" onkeyup=\"copy_data(this)\"/></td></tr>";
echo "<tr><td>Album Name:</td><td><input type='text' name='album' value=\"\" /></td></tr>";
echo "<tr><td>Number of Discs:</td><td><input type='text' size ='4' name='number_of_discs' value=\"\" /></td></tr>";
echo "<tr><td>Label:</td><td><input type='text' name='label' value=\"\" /></td></tr>";

echo "<tr><td>Genre:</td><td><input type='text' name='genre' value=\"\" /></td></tr>";
	$genres_query = "SELECT general_genreID, genre FROM def_general_genres ORDER BY general_genreID ASC";
	$genres = mysql_query($genres_query, $link);
	if (!$genres) {
		die ('This is an error message: ' . mysql_error());
	}
	
	echo "<tr><td><div id=\"top\">General Genre:</div></td><td><select name=\"general_genreID\">";
	while ($genre_get = mysql_fetch_array($genres, MYSQL_NUM)){
		
		$genreID = $genre_get[0];
		$genre = $genre_get[1];
		
		echo "<option value=\"$genreID\">$genre</option>";
		
	}
	echo "</select></td></tr>\n";
	echo "<tr><td>Medium:</td><td><input type='radio' name='medium' value='0' checked> Vinyl <input type='radio' name='medium' value='1'> Cassette</td></tr>";
echo "</table>";

?>
<script language="javascript">
        function addRow(tableID) {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);
			var artist = document.getElementById('artist').value;
			
			var disc = row.insertCell(0);
            var discNo = document.createElement("input");
            discNo.name = (rowCount) + '_discnum';
			discNo.type = 'text';
			discNo.size ='4';
			discNo.value = 1;
            disc.appendChild(discNo);
			
            var track = row.insertCell(1);
            var trackNo = document.createElement("input");
            trackNo.name = (rowCount) + '_trnum';
			trackNo.type = 'text';
			trackNo.size ='4';
			trackNo.value = rowCount;
            track.appendChild(trackNo);
			
            var name = row.insertCell(2);
            var trackName = document.createElement("input");
            trackName.type = 'text';
			trackName.size = '75';
			trackName.name = (rowCount) + '_trname';
            name.appendChild(trackName);
			
			var art = row.insertCell(3);
            var trackArtist = document.createElement("input");
            trackArtist.type = 'text';
			trackArtist.size = '75';
			trackArtist.name = (rowCount) + '_trart';
			trackArtist.value = artist;
            art.appendChild(trackArtist);
			
			if (document.record.rowDelete.disabled == true){
				document.record.rowDelete.disabled = false;
			}
 
        }

		function artistGet() {
			var first_artist = document.getElementById('first_track').value
			if (first_artist == "") {
				var artist = document.getElementById('artist').value;
				first_artist.value = artist;
			}
			
		}

 
        function deleteRow(tableID) {
            try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
				if (rowCount > 2){
					table.deleteRow(rowCount-1);
					if ((rowCount - 1) == 2) {
						document.record.rowDelete.disabled = true;
					}
				}
					
            }
			catch(e) {
                alert(e);
            }
        }

		function copy_data(val){
			var a = document.getElementById(val.id).value;
			document.getElementById('first_track').value=a;
		}
    </script>

	
	<input type="button" value="Add Row" onclick="addRow('songData')" />
 
    <input type="button" name="rowDelete" value="Delete Row" disabled="disabled" onclick="deleteRow('songData')" />
 
	<table id='songData'>
	<tr><th>Disc #</th><th>Track #</th><th>Song Title</th><th>Track Artist</th></tr>
        <tr>
			<td> <input type='text' size='4' value="1" name="1_discnum" /> </td>
            <td> <input type='text' size='4' value="1" name="1_trnum" /> </td>
            <td> <input type='text' size='75' name="1_trname" /> </td>
			<td> <input type='text' id='first_track' size='75' name="1_trart"/> </td>
        </tr>
    </table>
<?php
echo "<p><input type='submit' name='submit' value='Submit' /></form>";
?>