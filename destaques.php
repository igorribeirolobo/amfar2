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

$html="<table width='100%' cellPadding=2 cellSpacing=2>
	<tr>
		<td align='center' colspan='2'>
			<a class=dst href=\"javascript:abrir('noticias.php?idNoticia=67')\">
				<img src='http://www.amfar.com.br/noticias/esjus.jpg' border='0'/><br />
				ESJUS - Escola Superior de Justiça<br /> - Mestrado em Bioética -</a>
			<hr size='1'>
		</td>
	</tr>";
//$html='';

$query=mssql_query("SELECT idCurso, titulo FROM cursos WHERE ativo = 1");
While ($result=mssql_fetch_object($query))  {
	$foto='noticias/amf.jpg';	
	$html .="<tr>
		<td style='border-bottom:1px solid #808080'>
			<a href=\"javascript:abrir('insCursos.php?idCurso==$result->idCurso')\">
			<img src='mini.php?img=$foto&wh=50' border=0 style='margin-right:6px'></a>
		<td style='border-bottom:1px solid #808080'>
			<a class=dst href=\"javascript:abrir('insCursos.php?idCurso=$result->idCurso')\">$result->titulo</a>
		</td>
		</tr>";
	}	// while	
	$html .="</table>";
	
	echo $html;
?>
