<?
include 'global.php';


$sql="select * from legislacao order by datapub desc";
$query=mssql_query($sql) or die("Erro");
?>
<script language="javascript">
	var lastId;

	function showThis(id) {	
		var div = document.getElementById(id);
		if (lastId) lastId.className='invisivel';		
		div.className='visivel';
		lastId=div;
		}
</script>

<div id="mainTop" style="background:url(images/mainLegislacao.png) no-repeat; height:24px"></div>
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="2">

<?
	$row=0;
	While ($result = mssql_fetch_object($query)) {
		echo"
		<tr><td style='padding:0px 4px; border-bottom:1px solid #808080'>
			<a style='text-decoration:none;border:0' href='javascript:void(null)' onclick=\"showThis('$row')\">
			$result->titulo</b></span></a></td>
			
		<tr><td id='$row' style='width:566px' class='invisivel'>
			<p style='margin:4px 4px; padding:0 10px; background:#f0f0f0; font-size:8pt' $result->chamada<br />$result->resumo<br />";
				
			if (isset($result->link)) {
				if (stristr($result->link, 'http'))
					echo"<a href='$result->link' target='_blank'>Ler matéria completa</a>";
				else
					echo"<a href='legislacao/$result->link' target='_blank'>Ler matéria completa</a>";
				}
			
		echo "</p></td></tr>";
			
		$row++;
		}
	echo"</table>";	
?>
