<?
$dirr = opendir("galeria");			
$fotos = array();			
while($a = readdir($dirr)) {
	$b = explode(".", $a);
	if($b[1] == "jpg"){
		array_push($fotos, $a);
		}
	$len=count($fotos);
	}
?>

<style>
	.galeria{font: 8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;color:#00923F;}
	.galeria caption{text-align:center;color:#006;font-size:11pt;background:#DCEAF8}
	.galeria td a{text-align:center; padding:4px 0;border-bottom:solid 4px #fff}
	.galeria td a:hover{text-align:center; padding:4px 0;border-bottom:solid 4px #f00}
	.legenda{text-align:center;font: 8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;color:#00923F;}
</style>

<link href="light/lightbox.css" type="text/css" media="screen" rel="stylesheet" />
<script src="light/prototype.js" type="text/javascript"></script>
<script src="light/scriptaculous.js?load=effects" type="text/javascript"></script>
<script src="light/lightbox.js" type="text/javascript"></script>

<div id="mainTop" style="background:url(images/mainGallery.png) no-repeat; height:24px; margin:4px 0"></div>	

<table class="galeria" cellpadding=2 cellspacing=2 width="100%" align="center"/>
	<caption>19-20/11/2010: Curso de Gerenciamento</caption>
<?
	$dirr = opendir("galeria/20101119");			
	$fotos = array();			
	while($a = readdir($dirr)) {
	$b = explode(".", $a);
	if(strtolower($b[1]) == "jpg"){
		array_push($fotos, $a);
		}
	$len=count($fotos);
	}
	$col=0;
	for($i=0; $i < $len; $i++) {
		if ($col==0 || $col > 8)  {
			echo "<tr>";
			$col=0;
			}
			
		echo "<td>
			<a href='galeria/20101119/{$fotos[$i]}' rel='lightbox[roadtrip]' style='margin:0; padding:0'>
			<img id='$a' src='mini.php?img=galeria/20101119/{$fotos[$i]}&wh=50' border='0' /></a></td>";
		$col++;
		}	?>
</table>

<table class="galeria" cellpadding=2 cellspacing=2 width="100%" align="center"/>
	<caption> 24-25/09/2010: III Fórum Internacional sobre Erros de Medicação</caption>
<?
	$dirr = opendir("galeria/20100924");			
	$fotos = array();			
	while($a = readdir($dirr)) {
	$b = explode(".", $a);
	if(strtolower($b[1]) == "jpg"){
		array_push($fotos, $a);
		}
	$len=count($fotos);
	}
	$col=0;
	for($i=0; $i < $len; $i++) {
		if ($col==0 || $col > 8)  {
			echo "<tr>";
			$col=0;
			}
			
		echo "<td>
			<a href='galeria/20100924/{$fotos[$i]}' rel='lightbox[roadtrip]' style='margin:0; padding:0'>
			<img id='$a' src='mini.php?img=galeria/20100924/{$fotos[$i]}&wh=50' border='0' /></a></td>";
		$col++;
		}	?>
</table>
