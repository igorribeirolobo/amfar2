<?
session_start();
if (!$_SESSION['admUser']) echo"<script>history.back();</script>";
require '../global.php';


$sql="SELECT idEvento, status, CONVERT(CHAR(10), data, 103) AS fData, titulo FROM eventos ORDER BY data DESC";

if ($_GET['del']) mssql_query("DELETE FROM eventos WHERE idEvento={$_GET['del']}");

$query=mssql_query($sql); //echo $sql;
$rows=mssql_num_rows($query);

?>
<script language="JavaScript">
	function remover(id) {
		if (!confirm("Tem certeza que deseja remover este registro da base de dados?")) return false;
			document.location.href="index.php?p=7&del="+id;
			}
</script>

<p align="center" style="margin:0">Ordem decrescente por data. Use a barra de navegação abaixo para outras páginas ou clique no ID para Visualizar/Alterar o registro.</p>
<table id="list" border="0" cellspacing="1" cellpadding="1">
	<thead>
		 <tr>
			  <th class="header">ID</th>
			  <th class="header">Tipo</th>
			  <th class="header">Data</th>
			  <th class="header">Título</th>				  
			  <th class="header">Excluir</th>
		 </tr>
	 </thead>
	 <tbody><?
		if ($rows==0)
			echo"<tr><td colspan='5' align='center'><b style='color:#f00'>Nenhum registro localizado!</b></td></tr>";
		else 
			While ($rs = mssql_fetch_object($query)) {	?>
				<tr class="trLink" style="height:18px;text-align:center">			
					<td><a href="#" onclick="eventos('eventos.php?id=<?=$rs->idEvento?>')"><?=sprintf('%06d',$rs->idEvento)?></a></td>			
					<td><?=($rs->status==0)?'AMF':'DVS'?></td>						
					<td><?=$rs->fData?></td>
					<td style="text-align:left"><?=$rs->titulo?></td>			
					<td><img style="cursor:pointer" src="images/excluir.gif" border="0" onclick="remover(<?= $rs->idEvento ?>)" alt="Remover este registro"></a></td>
				</tr><?
				$counter ++;
				}	?>
	</tbody>
</table>
