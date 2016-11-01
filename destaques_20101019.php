<style>
	.dst
		{
		font:9pt Arial,  helvetica, sans-serif, tahoma, verdana;
		color: #ffffff;
		}
	
	.dst a {
		text-decoration:none;
		color: #ffffff;	
		}
		
	.dst a:hover {
		color: #ff9900;
		text-decoration:underline;
		font-weight: bold;
		} 
	
</style>
<?
include 'global.php';

//include 'includes/funcoesUteis.php';


$query=mssql_query("
SELECT     TOP (20) idNoticia, titulo, subTitulo, foto
FROM         noticias
WHERE     (status = 1)
ORDER BY RAND() DESC");

$html="<table width='98%' cellPadding=2 cellSpacing=2>
<tr><td colspan='2'><img src='images/titDestaques.png' border='0'/></tr></tr>";

While ($result=mssql_fetch_object($query))  {
	$foto=($result->foto)?$result->foto:'noticias/amf.jpg';	
	$html .="<tr>
		<td colspan='2' align='center'>
			<a href=\"javascript:abrir('noticias.php?idNoticia=$result->idNoticia')\">
			<img src='mini.php?img=$urlSite/$foto&wh=190' border=0 style='margin-right:6px'></a><br />
			<a class=dst href=\"javascript:abrir('noticias.php?idNoticia=$result->idNoticia')\">
				<span style='color:#000'>$result->titulo</span></a></td></tr>";
	}	// while	
	$html .="</table>";
	
	echo $html;
?>
