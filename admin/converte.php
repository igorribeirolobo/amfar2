

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<?
	header("Content-type: text/html; charset=iso-8859-1");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

$str="<br>ã á é í ó ú ç ä<br/>";
echo "<br/>$str";

echo "<br/>utf_8";
echo utf8_encode($str);

echo "<br/>urlEncode";
echo urlencode($str);

echo "<br/>unicode_encode";
//echo unicode_encode($str);


echo "resultado: <br>Æ   ‚ ¡ ¢ £ ‡ „";

?>
