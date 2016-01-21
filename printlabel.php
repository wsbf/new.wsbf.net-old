<?php
	require("connect.php");
	require('fpdf.php');
	/**
	 $albums = array();
	 $albums[] = "H189";
	 $albums[] = "H193";
	 $albums[] = "A193";
	 $albums[] = "B193";
	 print_r($albums);
	 **/
	$albums[0] = $_GET['a1'];
	$albums[1] = $_GET['a2'];
	$albums[2] = $_GET['a3'];
	$albums[3] = $_GET['a4'];
	
	$topx = 0.50; //coordinates of the top left of the first box
	$topy = 1.2; //1.0; //0.70
	
	class PDF extends FPDF {
		function LabelLayout($x,$y,$albumNo,$title,$artist,$genre,$review,$reviewer,$reccs,$noairs,$silence,$general_genre) {
			$width = 3.3;
			$height = 4.8;
			$linespace = 0.152;
			$ladywidth = 0.55;
			$ladyoffset = 0.002;
			$alnowidth = 0.45;   //width for the album number
			$aralgenwidth = 0.49;  //width of the area with artist, album, and genre 
			$noairwidth = 0.83;
			$rotspacer = 4.3;  //space in the boxes with N, H, M, and L
			
			
			$this->SetXY($x,$y);
			$this->SetFont('','',9);
			$this->Rect($x-0.1, $y-0.18, $width+0.2, $height+0.2);
			
			//$this->SetLeftMargin($x);
			//$this->SetRightMargin($x+$width);
			$this->SetTopMargin($y);
			$this->Image('wsbflady.png',$x,$y,$ladywidth-$ladyoffset);
			
			//general genre at the top
			$this->SetX($x+$ladywidth);
			$this->Cell($width-$ladywidth-$alnowidth-($rotspacer*$linespace), $linespace, $general_genre, 1,0,'C');
			
			//rotation
			$this->SetX($x+($width-$alnowidth)-($rotspacer*$linespace));
			$this->Cell($linespace,$linespace,"N","LTRB",0,'C');
			$this->Cell($linespace,$linespace,"H","LTRB",0,'C');
			$this->Cell($linespace,$linespace,"M","LTRB",0,'C');
			$this->Cell($linespace,$linespace,"L","LTRB",0,'C');
			
			//album code
			$this->SetFont('','B',16);
			$this->SetX($x+($width-$alnowidth)+0.08);
			$this->Cell($alnowidth,$linespace,$albumNo,0,1,'R');
			
			//artist
			$this->SetX($x+$ladywidth);
			$this->SetFont('','B',9);
			$this->Cell($aralgenwidth,$linespace,"Artist: ");
			$this->SetFont('','');
			$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$artist,0,'L');
			
			//album
			$this->SetX($x+$ladywidth);
			$this->SetFont('','B');
			$this->Cell($aralgenwidth,$linespace,"Album: ");
			$this->SetFont('','');
			$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$title);
			
			//genre
			$this->SetX($x+$ladywidth);
			$this->SetFont('','B');
			$this->Cell($aralgenwidth,$linespace,"Genre: ");
			$this->SetFont('','');
			$this->MultiCell($width-$ladywidth-$aralgenwidth,$linespace,$genre);
			
			//review
			$this->SetX($x);
			$this->SetFont('','B',9);
			$this->Cell($width,$linespace,"Property of WSBF-FM Clemson - 88.1",'',1,'C');
			
			$this->SetFont('','',9);
			$this->SetX($x);
			$review = convert_smart_quotes($review);
			$this->MultiCell($width,$linespace,$review,"LTR",'L');
			
			//reviewer
			$this->SetX($x);
			$this->SetFont('','B','');
			$this->Cell(0.8,$linespace,"Reviewed by:", "L",'L');
			$this->SetFont('','',9);
			$this->Cell($width-0.8,$linespace,$reviewer,"RB",1,'L');
			$this->SetFont('','',9);
			
			//reccomended
			if(sizeof($reccs) > 0) {
				
				$this->SetFont('','',9);
				$reccList = "";
				$this->SetFont('','B','');
				$this->SetX($x);
				$this->Cell($width,$linespace,"Recommended:", "TR",'L',2);		
				foreach ($reccs as $recc) {
					$reccList .= $recc . ", ";
				}
				$this->SetX($x);
				$reccList = substr_replace($reccList,"",-2);
				$this->SetFont('','',9);			
				$this->MultiCell($width,$linespace,"             " . $reccList,'LR','L');
			} else {
				$this->SetX($x);
				$this->SetFont('','B');
				$this->Cell($width,$linespace,"Album Apparently Has No Recommended Tracks?",'T',1,'C');
				$this->SetFont('','',9);
			}
			
			//no-air
			if(sizeof($noairs) > 0) {
				
				$this->SetFont('','B',9);
				$noairList = "";
				$this->SetX($x);
				$this->Cell($width,$linespace,"No-Air:","TR",'L',2);
				foreach ($noairs as $noair) {
					$noairList .= $noair . ", ";
				}
				$this->SetX($x);
				$this->SetFont('','',9);
				$noairList=substr_replace($noairList,"",-2);
				$this->MultiCell($width,$linespace,"        " . $noairList,'LRTB','L');
			} else {
				$this->SetX($x);
				$this->SetFont('','B');
				$this->Cell($width,$linespace,"Album Is FCC Clean",'T',1,'C');
				$this->SetFont('','',9);
			}
			
			//silence after track
			if(sizeof($silence) > 0) {
				
				$this->SetFont('','B',9);
				$silenceList = "";
				$this->SetX($x);
				$this->Cell($width,$linespace,"Note:","TR",'L',2);
				foreach ($silence as $silences) {
					$silenceList .= $silences . ", ";
				}
				$this->SetX($x);
				$this->SetFont('','',9);
				$silenceList=substr_replace($silenceList,"",-2);
				$silenceList .= " has silence after track.";
				$this->MultiCell($width,$linespace,"      " . $silenceList,'LRTB','L');
			} 
			
		}
	}
	
	function getArtist($aID) {
		$query = "SELECT artist_name FROM libartist WHERE artistID = $aID LIMIT 1";
		$artist = "NOT IN DATABASE";
		$result = mysql_query($query);
		while($entry = mysql_fetch_array($result)) {
			$artist = $entry[0];
		}
		return $artist;
	}
	
	
	function getGeneralGenre($general_genreID){
		if(!is_numeric($general_genreID)) die('general_genreID must be numeric');
		
		$query = sprintf("SELECT genre FROM def_general_genres WHERE general_genreID='%d'", $general_genreID);
		$result = mysql_query($query) or die("getGeneralGenre failed : ".mysql_error());
		$genre = mysql_fetch_array($result);
		return $genre[0];
		
	}
	
	function getRecc($cID) {
		$query = "SELECT * FROM libtrack WHERE albumID = '$cID'";
		$tracks = array();
		$result = mysql_query($query);
		while($entry = mysql_fetch_array($result)) {
			if ($entry['disc_num'] == 1) {
				if ($entry['airabilityID'] == 1) {
					$tracks[] = $entry['track_num'] . "." . $entry['track_name'];
				}
			}
			else{
				if ($entry['airabilityID'] == 1) {
					$tracks[] = "D".$entry['disc_num'].".T".$entry['track_num'] . "." . $entry['track_name'];
				}
			}	
		}
		return $tracks;
	}
	
	function getNoAir($cID) {
		$query = "SELECT * FROM libtrack WHERE albumID = '$cID'";
		$tracks = array();
		$result = mysql_query($query);
		while($entry = mysql_fetch_array($result)) {
			if ($entry['disc_num'] == 1) {
				if ($entry['airabilityID'] == 2) {
					$tracks[] = $entry['track_num'];
				}
			}
			else{
				if ($entry['airabilityID'] == 2) {
					$tracks[] = "D".$entry['disc_num'].".T".$entry['track_num'];
				}
			}
		}
		return $tracks;
	}
	
	function getSilenceAfter($cID) {
		$query = "SELECT * FROM libtrack WHERE albumID = '$cID'";
		$tracks = array();
		$result = mysql_query($query);
		while($entry = mysql_fetch_array($result)) {
			if ($entry['disc_num'] == 1) {
				if ($entry['airabilityID'] == 3) {
					$tracks[] = $entry['track_num'];
				}
			}
			else{
				if ($entry['airabilityID'] == 3) {
					$tracks[] = "D".$entry['disc_num'].".T".$entry['track_num'];
				}
			}
		}
		return $tracks;
	}
	
	$pdf=new PDF('P', 'in', 'A4');
	$pdf->AddPage();
	$pdf->AddFont('Basicmanual','','svbasicmanual.php');
	$pdf->AddFont('Basicmanual','B','svbasicmanual-bold.php');
	$pdf->SetFont('Basicmanual','',12);
	$pdf->SetAutoPageBreak(false);
	
	function convert_smart_quotes($string) 
	{ 
		$search = array(chr(0xe2) . chr(0x80) . chr(0x98),
						chr(0xe2) . chr(0x80) . chr(0x99),
						chr(0xe2) . chr(0x80) . chr(0x9c),
						chr(0xe2) . chr(0x80) . chr(0x9d),
						chr(0xe2) . chr(0x80) . chr(0x93),
						chr(0xe2) . chr(0x80) . chr(0x94));
		
		$replace = array("'",
						 "'",
						 '"',
						 '"',
						 "-",
						 "-");
		
		return str_replace($search, $replace, $string); 
	}
	
	$list = "";
	$concat = "";
	foreach ($albums as $album) {
		$list .= $concat . "album_code = '" . $album . "'";
		$concat = " or ";
	}
	
	$query = "SELECT * FROM libalbum WHERE " . $list;
	$result = mysql_query($query);
	
	$x = $topx;
	$y = $topy;
	
	$section = 1;
	while($review = mysql_fetch_array($result)) {
		$albumNo = $review['album_code'];
		$albumID = $review['albumID'];
		$title = $review['album_name'];
		$general_genreID = $review['general_genreID'];
		
		$review_query = "SELECT review, reviewer FROM libreview WHERE albumID = $albumID";
		$rev = mysql_query($review_query);
		$review_array = mysql_fetch_array($rev);
		$reviewtext = $review_array['review'];
		
		$reviewtext = str_replace(array("\r", "\r\n", "\n"), ' ', $reviewtext);
		$reviewtext = str_replace(array("      ", "     ", "    ", "   ", "  "), ' ', $reviewtext);
		
		$reviewer = $review_array['reviewer'];
		
		$reccs = getRecc($albumID);
		$noairs = getNoAir($albumID);
		$silence = getSilenceAfter($albumID);
		$artist = getArtist($review['artistID']);
		$genre = $review['genre'];
		$general_genre = getGeneralGenre($general_genreID);
		
		$pdf->LabelLayout($x,$y,$albumNo,$title,$artist,$genre,$reviewtext,$reviewer,$reccs,$noairs,$silence,$general_genre);
		
		
		if ($section == 1) {
			$x = 4.0 + $topx; //110,11
			$y = $topy;
		} else if ($section == 2) {
			$x = $topx; //0,152
			$y = 5 + $topy;
		} else if ($section == 3) {
			$x = 4.0 + $topx; //110,152
			$y = 5 + $topy;
		} 
		
		$section++;
	}
	$pdf->Output("CDlabel.pdf","D");
?>
