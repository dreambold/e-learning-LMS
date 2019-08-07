<?php
isset($_REQUEST['fileaway']) or die(); 
$sImagePath = base64_decode($_GET["fileaway"]);
if(isset($_REQUEST["width"]) && isset($_REQUEST["height"])){ $iThumbnailWidth = (int)$_GET['width']; $iThumbnailHeight = (int)$_GET['height']; $sType = 'exact'; }
elseif(isset($_REQUEST["maxw"]) && isset($_REQUEST["maxh"])){ $iMaxWidth = (int)$_GET["maxw"]; $iMaxHeight = (int)$_GET["maxh"]; $sType = 'scale'; } else die();
$img = NULL; $sExtension = strtolower(end(explode('.', $sImagePath)));
if($sExtension == 'jpg' || $sExtension == 'jpeg'){ $img = @imagecreatefromjpeg($sImagePath) or die("Cannot create new JPEG image"); }
elseif($sExtension == 'png'){ $img = @imagecreatefrompng($sImagePath) or die("Cannot create new PNG image"); }
elseif($sExtension == 'gif'){ $img = @imagecreatefromgif($sImagePath) or die("Cannot create new GIF image"); }
if($img){ 
	$iOrigWidth = imagesx($img); $iOrigHeight = imagesy($img);
	if($sType == 'scale'){
		$fScale = min($iMaxWidth/$iOrigWidth,$iMaxHeight/$iOrigHeight);
		if($fScale < 1){
			$iNewWidth = floor($fScale*$iOrigWidth);
			$iNewHeight = floor($fScale*$iOrigHeight);
			$tmpimg = imagecreatetruecolor($iNewWidth,$iNewHeight);
			imagecopyresampled($tmpimg, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOrigWidth, $iOrigHeight);
			imagedestroy($img); $img = $tmpimg;
		}
	}elseif($sType == "exact"){
		$fScale = max($iThumbnailWidth/$iOrigWidth,$iThumbnailHeight/$iOrigHeight);
		if($fScale < 1){
			$yAxis = 0; $xAxis = 0;
			$iNewWidth = floor($fScale*$iOrigWidth);
			$iNewHeight = floor($fScale*$iOrigHeight);
			$tmpimg = imagecreatetruecolor($iNewWidth,$iNewHeight);
			$tmp2img = imagecreatetruecolor($iThumbnailWidth,$iThumbnailHeight);
			imagecopyresampled($tmpimg, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iOrigWidth, $iOrigHeight);
			if($iNewWidth == $iThumbnailWidth){ $yAxis = ($iNewHeight/2)-($iThumbnailHeight/2); $xAxis = 0; }
			elseif($iNewHeight == $iThumbnailHeight){ $yAxis = 0; $xAxis = ($iNewWidth/2)-($iThumbnailWidth/2); }
			imagecopyresampled($tmp2img, $tmpimg, 0, 0, $xAxis, $yAxis, $iThumbnailWidth, $iThumbnailHeight, $iThumbnailWidth, $iThumbnailHeight);
			imagedestroy($img); imagedestroy($tmpimg); $img = $tmp2img;
		}     
	}
	header("Content-type: image/jpeg");
	imagejpeg($img);
}