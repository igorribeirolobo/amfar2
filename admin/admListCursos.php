<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';

if (!$_SESSION['admUser']) {echo"<script>history.back();</script>";die();}

$_msg = null;
	
if ($_POST['cmd']=='delete') {
	$idCurso = $_POST['idCurso'];
	$sql = "DELETE FROM cursos WHERE idCurso=$idCurso"; //echo $sql;			
	mssql_query($sql);
	$_msg = "Registro excluído com sucesso!";
	}

if ($_POST['cmd']=='update') {
	$idCurso = $_POST['idCurso'];
	$status = $_POST['status'];
	$sql = "UPDATE cursos set ativo=$status WHERE idCurso=$idCurso"; //echo $sql;			
	mssql_query($sql);
	$_msg = "Registro alterado com sucesso!";
	}

$sql="SELECT idCurso, status,
	CONVERT(CHAR(10), inicio, 103) AS fInicio,
	CONVERT(CHAR(10), final, 103) AS fFinal,
	titulo, ativo
		FROM cursos
			ORDER BY idCurso DESC";
$query = mssql_query($sql);
$rows=mssql_num_rows($query);
?>


<script language="JavaScript">
	$(document).ready(function() {
		$('.delete').click(function() {
			var temp = this.id.split(':');
			if (!confirm('Tem certeza que deseja excluir este registro?')) {
				$(this).attr('checked',false);
				return false;
				}
			$('#idCurso').val(temp[1]);
			$('#cmd').val('delete');
			$('#myForm').submit();			
			})
	
		$('.update').click(function() {
			var temp = this.id.split(':');
			if (!confirm('Tem certeza que deseja alterar este registro?')) {
				$(this).attr('checked',false);
				return false;
				}

			$('#status').val(($(this).is(':checked'))?'1':'0');			
			$('#idCurso').val(temp[1]);
			$('#cmd').val('update');
			$('#myForm').submit();			
			})
		
		$('#print').css('display','none');
		if($('#msg').val()!='')
			alert($('#msg').val());
		})
</script>

<p class="htable">
	Ordem decrescente por data. Use a barra de navegação abaixo para outras páginas ou clique no ID para Visualizar/Alterar o registro.
	<input type="button" class="newReg" value="Novo Registro" onclick="eventos('cursos.php?id=0')"/>
</p>
<form name="myForm" id="myForm" method="POST" action="">
	<input type="hidden" name="idCurso" id="idCurso" value=""/>
	<input type="hidden" name="status" id="status" value=""/>
	<input type="hidden" name="cmd" id="cmd" value=""/>
	<input type="hidden" name="msg" id="msg" value="<?=$_msg?>"/>
	<table id="list" border="0" cellspacing="2" cellpadding="2">
		<thead>
			<tr align=center>
				<th class="header">ID</th>
				<th class="header">Tipo</th>				
				<th class="header">Título</th>
				<th class="header">Início</th>
				<th class="header">Final</th>				  
				<th class="header">Ativo</th>
				<th>Excluir</th>
			</tr>
		</thead>
		<tbody class="scroll">
	<?
	if ($rows==0)
		echo"<tr><td colspan='6' align='center'><b style='color:red'>Nenhum registro localizado!</b></td></tr>";
	else 
		While ($rs = mssql_fetch_object($query)) {
			if(!$rs->inicio) $rs->inicio="00/00/0000";
			if(!$rs->final) $rs->final="00/00/0000";
				?>
			<tr class="trLink" align="center" style="height:18px">	
				<td><a href="javascript:void(null)" onclick="eventos('cursos.php?idCurso=<?=$rs->idCurso?>')">
					<b><?=sprintf('%06d',$rs->idCurso)?></td>	
				<td align="left"><?if ($rs->status==0) echo 'AMF - Atualização';
						elseif ($rs->status==1) echo 'AMF - Especialização';
						elseif ($rs->status==2) echo 'DVS - Atualização'; 
						elseif ($rs->status==3) echo 'DVS - Especialização'; ?>
				</td>				
				<td align="left"><?=$rs->titulo?></td>
				<td><?=$rs->fInicio?></td>
				<td><?=$rs->fFinal?></td>	
				<td><input <?=($rs->ativo==1)?"value='1' checked":"value='0'"?> class="update" type="checkbox" name="update:<?=$rs->idCurso?>" id="update:<?=$rs->idCurso?>"/></td>
				<td><a href="#" class="delete" id="delete:<?=$rs->idCurso?>"><img src="images/excluir.gif" border="0"
					alt="Remover esta notícia da base de dados" title="Remover esta notícia da base de dados"></a>
				</td>
		</tr><?
		}	?>
		</tbody>
	</table>
</form>
