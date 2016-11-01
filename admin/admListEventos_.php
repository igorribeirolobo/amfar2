<?
if (!$_SESSION['admUser'])	{	?>
	<script>
		history.back();
	</script><?
	}
	
require '../includes/navbar.php';

$mult_pag =new Mult_Pag(30, 10);

$sql="select id, status, convert(char(10), data, $dataFilter) as fData, titulo from eventos order by data desc";

if ($_GET['del']) mssql_query("delete from eventos where id={$_GET['del']}");

$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);

?>
<script language="JavaScript">
	function remover(id) {
		if (!confirm("Tem certeza que deseja remover este registro da base de dados?")) return false;
			document.location.href="index.php?act=lstEvt&del="+id;
			}
</script>
<div style="background:url(images/admListEventos.png) no-repeat; height:24px"></div>

<table border="0" cellspacing="2" cellpadding="2" width="100%">
	 <tr>
		  <td colspan="6" align="center">
		  	Ordem decrescente por data. Use a barra de navegação abaixo para outras páginas ou clique no ID para Visualizar/Alterar o registro.</td>
	 </tr>
	 <tr align=center bgcolor="c0c0c0">
		  <td class=tTable>ID
		  <td class=tTable>TIPO
		  <td class=tTable>DATA
		  <td class=tTable>TÍTULO				  
		  <td class=tTable>Del
	 </tr>
<?
if ($res_pag==0)	{
	echo"<tr>
		<td colspan='3' align='center'>
			<font color='orange'><b>Não existem notícias na base de dados !</b></font></td>
		</tr>
		<tr><td style='background:#efefef; text-align:center; padding:2px' colspan='4'>
			<a href='javascript:void(null)' onclick=\"eventos('eventos.php')\">
				<img src='images/seta.png' border=0' align='absMiddle' style='margin-right:10px'><b>ADICIONAR NOVO EVENTO</a>
	</table>";
	exit;
	}			

$counter=0;
While ($counter < $mult_pag->numreg)  {
	$result = mssql_fetch_object($query);
?>		<tr>

		<td style="text-align:center; border-bottom:1px solid #e0e0e0; border-right:1px solid #e0e0e0">
			<a href="javascript:void(null)" onclick="eventos('eventos.php?id=<?=$result->id?>')"><b>
			<?= $result->id ?></td>

		<td style="text-align:center; border-bottom:1px solid #e0e0e0; border-right:1px solid #e0e0e0">
			<?= ($result->status==0) ? 'AMF' : 'DVS' ?></td>
			
		<td style="text-align:center; padding-left:4px; border-bottom:1px solid #e0e0e0; border-right:1px solid #e0e0e0">
			<?= $result->fData ?></td>
		<td style="text-align:left; border-bottom:1px solid #e0e0e0; border-right:1px solid #e0e0e0">
			<?= $result->titulo ?></font></td>

		<td style="text-align:center; border-bottom:1px solid #e0e0e0; border-right:1px solid #e0e0e0">						
			<img style="cursor:pointer" src="images/closeLayer.gif" border="0" onclick="remover(<?= $result->id ?>)" alt="Remover esta notícia da base de dados"></a></td>
	</tr><?
	$counter ++;
	}	// while
		
echo"
<tr><td style='background:#efefef; text-align:center; padding:2px' colspan='5'>
	<a href='javascript:void(null)' onclick=\"eventos('eventos.php')\">
		<img src='images/seta.png' border=0' align='absMiddle' style='margin-right:10px'><b>ADICIONAR NOVO EVENTO</a>
	<tr><td align=center colspan='5'><div id='bar'>";

		$todos_links = $mult_pag->Construir_Links("todos", "sim");
	echo"</div></td></tr></table>";	
?>
