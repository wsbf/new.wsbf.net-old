<?php
$time = microtime(true);
//require_once("conn.php");
include("import_config.php");
require_once("../utils_ccl.php");
require_once("../library_functions.php");
require_once("../position_check.php");
require_once("getid3/getid3.php");

if(!session_id()) session_start();
if(!(MD_check())){
	die ('You aren\'t allowed to be here!<br>');
}
	   

sanitizeInput();

echo "<title>WSBF Import Music</title>";
	
/** current directory **/
if(isset($_GET['dir']))
	$dirCurrent = urldecode($_GET['dir']);
else $dirCurrent = BASE_DIR;

if(chdir($dirCurrent) === FALSE)
	die("Error: Could not change to ".$dirCurrent."\n");
$dirCurrent = getcwd(); //gets a real path, without the ..

//security - don't leave base directory
if(strpos($dirCurrent, BASE_DIR) === FALSE)
	header("Location: ".$_SERVER['HTTP_REFERER']);

$uri = genUriStruct($_SERVER['REQUEST_URI'], "http://new.wsbf.net");
echo "<h3>PRELIMINARY IMPORT SYSTEM</h3>\n\n\n";
echo "<p>Or import a <a href='import_record.php'>record</a></p>\n";

echo "<p>Current working directory: $dirCurrent</p>\n";

$dirA = array();
$fileA = array();


/** scandir() and then iterating is faster than this
	.99 vs 1.37 secs on BASE_DIR, 24jun10
$dirA = glob('*', GLOB_ONLYDIR);
**/
$tempA = scandir($dirCurrent);
foreach($tempA as $temp) {
	if(is_dir($temp) ) // && $temp[0] != '.'
		$dirA[] = $temp;
	else if(isMP3($temp))
		$fileA[] = $temp;
}


echo "<div id='directories'>";
foreach ($dirA as $dir) {
	if($dir === ".") continue;
	$moduri = useUriStruct(updateUriStruct($uri, "dir", urlencode($dirCurrent."/".$dir)));
	if($dir == "..") $dir = "Parent Directory";
	echo"| <div style='display: inline-block; white-space:nowrap;'><a href='$moduri'>$dir</a></div> |";
}
echo "</div><div id='contents'>";

$artistA = array();
$cartA = array();
//echo "<pre>";
//print_r($fileA, FALSE);

foreach($fileA as $file) {
	
	/** Assume filename: ARTIST - ALBUM - ... **/
	/** TODO: Some special case for others that don't conform to this **/
	/** Make non-conformists go away: if number of matches is 1, ignore **/
	$pieces = explode(" - ", $file);
	$check = count($pieces);
//If it's a cart, it will be a variable called $cart, not an array. And it's called $cart, not $artist.
	if($check == 1){
		$cart = $file;
		if(!in_array($cart, $cartA)) {
		$cartA[] = $cart;
		}
	}
	else{
	
	$artist = $pieces[0];
	//$album = $pieces[1];

	if(!in_array($artist, $artistA)) {
		//$tmp[$artist] = 
		$artistA[] = $artist;
	}
	}
}

echo "<ul>\n";


//sends carts to import_cart.php =dac
$uriCart = genUriStruct("/import/import_cart.php", SCRIPT_PREFIX);
echo "<h3>Carts/Single Files:</h3>";
foreach($cartA as $cart) {
	//echo $artist . " | ";
	
	$uriCart = updateUriStruct($uriCart, 'path', urlencode($dirCurrent));
	$uriCart = updateUriStruct($uriCart, 'cart', urlencode($cart));
	
	echo "<li><a href='".useUriStruct($uriCart)."'.>$cart</a></li>\n";
}

$uriArtist = genUriStruct("/import/import_artist.php", SCRIPT_PREFIX);
//end dac

echo "<h3>Artists:</h3>";
foreach($artistA as $artist) {
	//echo $artist . " | ";
	
	$uriArtist = updateUriStruct($uriArtist, 'path', urlencode($dirCurrent));
	$uriArtist = updateUriStruct($uriArtist, 'artist', urlencode($artist));
	
	echo "<li><a href='".useUriStruct($uriArtist)."'.>$artist</a></li>\n";
}
echo "</ul>\n";

//echo "</pre>";

echo "</div>";
$netTime = microtime(true)-$time;
echo "<p>Time needed to execute: ".round($netTime,5)." seconds\n</p>";

//print_r($arr);




/** ID3 is too EXPENSIVE to handle here. instead, let's rely on filenames, and validate later. **/

?>
