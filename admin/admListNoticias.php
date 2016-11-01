<?
if (!$_SESSION['admUser'])	{	?>
	<script>
		history.back();
	</script><?
	}
if ($_GET['del']) {
	$sql = "DELETE FROM noticias WHERE idNoticia={$_GET['del']}";
	mssql_query($sql);
	//echo $sql;
	}

$sql="SELECT idNoticia, status, CONVERT(CHAR(10), data, 103) as fData, titulo FROM noticias ORDER BY data DESC";
$query=mssql_query($sql) or die("erro $sql");
$rows=mssql_num_rows($query);

?>
<script language="JavaScript">
	$(document).ready(function() {
		$('.links').click(function() {	
			var newsWin= window.open('noticias.php?id='+this.id,'winNot','left=0, top=0, width=820,height=400,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
			newsWin.focus();
			})

		$('.newReg').click(function() {	
			var newsWin= window.open('noticias.php?id=0','winNot','left=0, top=0, width=780,height=400,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
			newsWin.focus();
			})	

		$('.del').click(function() {
			if (!confirm("Tem certeza que deseja remover este registro da base de dados?"))
				return false;
			var t = this.id.split(':');
			document.location.href="index.php?p=6&del="+t[1];
			})
		})
</script>

<p class="htable">
	Ordem decrescente por data. Use a barra de navegação abaixo para outras páginas ou clique no ID para Visualizar/Alterar o registro.
	<input type="button" class="newReg" value="Novo Registro"/>
</p>
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
	 <tbody class="scroll"><?		
		While ($rs = mssql_fetch_object($query)) { ?>
			<tr class="trLink" align="center" style="height:18px">	
				<td><a href="#" class="links" id="<?=$rs->idNoticia?>"><?=$rs->idNoticia?></td>
				<td><?=($rs->status==0)?'NOT':'DST'?></td>			
				<td><?=$rs->fData?></td>
				<td align="left"><?=$rs->titulo?></td>
				<td><a href="#" class="del" id="del:<?=$rs->idNoticia?>"><img src="images/excluir.gif" border="0"
					alt="Remover esta notícia da base de dados" title="Remover esta notícia da base de dados"></a>
				</td>
			</tr><?
			}	?>
	 </tbody>
</table>
