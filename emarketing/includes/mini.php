<?
/*	
	*********************************
	responsavel por gerar miniaturas
	
	Se Width passado, despreza o height
	pois calcula automaticamente.
	
	se Height passado despreza o width
	pois calcula automaticamente
	para manter a proporção da imagem.
	
	ex.:
	width original da imagem = 250px
	height original da imagem =120px
	
	cálculo para o create e copy:
	enviado: width=200
		height = (int)200/250*120=96
	
	enviado: height=100
		width = (int)100/120*250=208
		
	**********************************
*/
function checkgd() { 
	$gd2=""; 
	ob_start(); 
	phpinfo(8);
	$phpinfo=ob_get_contents(); 
	ob_end_clean(); 
	$phpinfo=strip_tags($phpinfo); 
	$phpinfo=stristr($phpinfo,"gd version"); 
	$phpinfo=stristr($phpinfo,"version"); 
	preg_match('/\d/',$phpinfo,$gd); 
	if ($gd[0]=='2') {
		$gd2="yes";
		} 
	return $gd2; 
	}

function createthumb($name,$new_w,$new_h) {
	global $gd2;
	$system=explode(".",basename($name));
	if (preg_match("/jpg|jpeg/",$system[1]) || preg_match("/jpg|jpeg/",$system[2])) {
		$src_img=imagecreatefromjpeg($name); } 
		
	if (preg_match("/png/",$system[1]) || preg_match("/png/",$system[2])) {
		$src_img=imagecreatefrompng($name); } 
		
	if (preg_match("/gif/",$system[1]) || preg_match("/gif/",$system[2])) { 
		$src_img=imagecreatefromgif($name); }

	$old_x=imageSX($src_img); 
	$old_y=imageSY($src_img);
  
	if ($old_x > $old_y) { 
		$thumb_w=$new_w; 
		$thumb_h=$old_y*($new_h/$old_x);  } 
 
 	if ($old_x < $old_y) { 
 		$thumb_w=$old_x*($new_w/$old_y); 
 		$thumb_h=$new_h;  } 
 
	if ($old_x == $old_y) { 
		$thumb_w=$new_w; 
		$thumb_h=$new_h; } 
 
	if ($gd2=="") { 
		$dst_img=ImageCreate($thumb_w,$thumb_h); 
		imagecopyresized($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		}
	else { 
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h); 
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
		} 
 
	if (preg_match("/png/",$system[1]))
		imagepng($dst_img); 
	elseif (preg_match("/gif/",$system[1]))
		imageGif($dst_img); 
	else
		imagejpeg($dst_img);
 
	imagedestroy($dst_img); 
	imagedestroy($src_img); 
	}


$gd2=checkgd() or die("erro no GD");

createthumb($_GET["img"],$_GET["wh"],$_GET["wh"]);

/*
// error_reporting(E_ALL);
header("Content-type: image/jpeg");

$nova = imageCreateFromJPEG($_GET["img"]);
$oWidth = imageSX($nova);	// width original
$oHeight = imageSY($nova);	// height original
//$gifNova = imageCreateFromGIF($nova);

// testa se veio o width
if ($_GET["width"]) {
	$nWidth = $_GET["width"];	// width desejado	
	$nHeight= (int)$nWidth / $oWidth * $oHeight;	// w=width desejado / width original * height original
	}
	
elseif ($_GET["height"]) {
	$nHeight = $_GET["height"];	// width desejado	
	$nWidth= (int)$nHeight / $oHeight * $oWidth;	// h=height desejado / height original * width original
	}

$new = imageCreateTrueColor($nWidth, $nHeight);
$fim = imageCopyResized($new, $nova, 0, 0, 0, 0, $nWidth, $nHeight, $oWidth, $oHeight);

imageJPEG($new);
imageDestroy($new);
imageDestroy($nova);
*/
?>