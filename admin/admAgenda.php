<?
session_start();
if (!$_SESSION['admUser']) echo"<script>history.back();</script>";
require '../global.php';

$_idAgenda = ($_POST['idAgenda'])?$_POST['idAgenda']:0;

if ($_POST['cmd']=='get') {
	$sql="SELECT *, CONVERT(CHAR(10), data, 103) AS data FROM agenda WHERE idAgenda=$_idAgenda";
	$qy1 = mssql_query($sql);
	$rs = mssql_fetch_object($qy1);
	echo utf8_encode("$rs->data:$rs->titulo:$rs->descricao");
	exit;
	}

if ($_POST['cmd']=='del') {
	mssql_query("DELETE FROM agenda WHERE idAgenda=$_idAgenda");	
	}

if ($_POST['cmd']=='save') {
	$_POST = str_replace("'","´",$_POST);
	$_data = explode('/',$_POST['data']);
	$_data = "$_data[2]/$_data[1]/$_data[0] 00:00";
	$_titulo = trim($_POST['titulo']);
	$_descricao = trim($_POST['descricao']);

	if ($_idAgenda==0) {		
		// grava so o id na tabela
		@mssql_query("INSERT INTO agenda(data, titulo, descricao, dataReg) VALUES('$_data', '$_titulo', '$_descricao', getDate())");
		if (mssql_rows_affected($conn) > 0)
			$msg = "Novo registro inserido com sucesso!";
		else
			$msg = "Não é permitido registro em duplicidade!";
		}		
	else {
		mssql_query("UPDATE agenda SET data='$_data', titulo='$_titulo', descricao='$_descricao' WHERE idAgenda=$_idAgenda");
		$msg = "Registro atualizado com sucesso!";
		}
	}


	
$sql="SELECT idAgenda, titulo, descricao, CONVERT(CHAR(10), data, 103) AS fData FROM agenda ORDER BY data DESC";
$query=mssql_query($sql) or die("erro $sql");
$rows=mssql_num_rows($query);
?>

<script language="JavaScript" type="text/javascript" src="../js/jquery.maskedinput-1.2.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>

<script language="JavaScript">
	$(document).ready(function() {
		var $dvLayer = $('#dvLayer');
		var $myForm = $('#myForm');
		var $cmd = $('#cmd');
		var $msg = $('#msg');
		var $id = $('#idAgenda');
		var $data = $('#data');
		var $titulo = $('#titulo');
		var $descricao = $('#descricao');
		var $btSend = $('#btSend');
		
		$data.mask('99/99/9999',{placeholder:'_'});
		
		$('.text').focus(function() {
			$('.text').each(function() {
				$(this).css('background',originColor);
				})
			$(this).css('background',alertColor);
			})
		
		$('.edit').click(function() {
			var t = this.id.split(':');			
			$id.val(t[1]);
			$cmd.val('get');
			$.ajax({			
				type: 'POST',
				url: 'admAgenda.php',
				data: $myForm.serialize(),
					success: function(msg){
						var ret = msg.split(':');
						$data.val(ret[0]);
						$titulo.val(ret[1].trim());
						$descricao.val(ret[2].trim()); 
						$dvLayer.toggle('slow');
						}
				});
			})
		
		$('.newReg').click(function() {
			$id.val('0');
			$('.text').each(function() {
				$(this).val('');
				})
			$dvLayer.css('display','block');
			})	
		
		
		$('.del').click(function() {
			if(!confirm('Tem certeza que deseja excluir este registro?\n\nUma vez excluído não tem como recuperá-lo.'))
				return false;
			var t = this.id.split(':');
			$cmd.val('del');
			$id.val(t[1]);
			$myForm.submit();
			})
			
		$btSend.click(function() {
			if ($data.val()=='') {
				alert('Digite a data da agenda.');
				$data.focus();
				return false;
				}
			else if (!$data.val().validDate()) {
				alert('A data da agenda é inválida!\n\nDigite corretamente a data da agenda.');
				$data.val('');
				$data.focus();
				return false;
				}
			else if ($titulo.val()=='') {
				alert('Digite o título da agenda.');
				$titulo.focus();
				return false;
				}
			else if ($descricao.val()=='') {
				alert('Digite a descrição da agenda.');
				$descricao.focus();
				return false;
				}
			$cmd.val('save');
			$myForm.submit();
			})
		
		if ($msg.val()!='') {
			alert($msg.val());
			}	

		if ($.browser.msie)
			$dvLayer.css('left',(screen.width-470-220)/2+'px');
		else
			$dvLayer.css('left',(screen.width-470)/2+'px');
			
		$dvLayer.css('height','200px');

		$('#icClose').click(function() {
			$dvLayer.toggle('slow');
			})
		})
</script>

<style>
	#topo {width:100%;text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	#formAgenda {width:100%;background:#fff}
	#formAgenda td{padding:2px 4px;border:1px solid silver;}
	#formAgenda th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	.newReg{margin-left:30px;cursor:pointer}
	.label{text-align:center;background:#369;color:#fff;font-size:9pt}	
	#formAgenda .label{text-align:right;background:#369;color:#fff;font-size:9pt}
	#formAgenda #data{text-align:center;width:80px;border:solid 1px #369}
	#formAgenda #titulo{width:400px;border:solid 1px #369}
	#formAgenda #descricao{width:400px;height:60px;border:solid 1px #369}	
	#formAgenda #btSend, #btCancel{cursor:pointer}
	#formAgenda th p{text-align:center;font-weight:bolder}
	#formAgenda th img{text-align:right;}
</style>

<table id="topo" border="0" cellspacing="1" cellpadding="1">
	<tr>
		<th>
			Ordem decrescente por data. Use a barra de scroll se necessário. Clique no ID para Visualizar/Alterar o registro.
			<input type="button" class="newReg" value="Incluir Novo Registro"/></th>
	</tr>
</table>

<table id="list" border="0" cellspacing="1" cellpadding="1">
	<thead>
		<tr>
			<th class="header">ID</th>		  
			<th class="header">Data</th>
			<th class="header">Título</th>				  
			<th class="header">Excluir</th>
		</tr>
	</thead>
	<tbody><?
		While ($rs = mssql_fetch_object($query)) {	?>
			<tr class="trLink">
				<td align="center">
					<a href="#" id="edit:<?=$rs->idAgenda?>" class="edit"
						alt="Alterar este registro" title="Alterar este registro">
					<?=sprintf('%06d',$rs->idAgenda)?></a></td>			
				<td align="center"><?= $rs->fData ?></td>
				<td><?=$rs->titulo?></font></td>
				<td align="center">
					<a href="#" id="del:<?=$rs->idAgenda?>" class="del">						
					<img style="cursor:pointer" src="images/excluir.gif" border="0"
						alt="Remover este Registro" title="Remover este Registro"/></a>
				</td>
			</tr><?
			}	?>
	 </tbody>
</table>



<div id="dvLayer">
	<fieldset>
		<form name="myForm" id="myForm" method="POST" action="" style="width:100%">
			<input type="hidden" name="idAgenda" id="idAgenda" value=""/>
			<input type="hidden" name="cmd" id="cmd" value=""/>
			<input type="hidden" id="msg" value="<?=$msg?>"/>
			<table id="formAgenda" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<th colspan="2">
						<p style="text-align:center">
							<img id="icClose" src="images/closeLayer.gif" border="0"
								style="cursor:pointer" alt="Fechar" title="Fechar" align="right"/>
							<b>Agenda da Diretoria</b>
						</p>
					</th>
				</tr>
				<tr>
					<td class="label">Data</td>
					<td><input class="text" name="data" id="data" maxlength="10" value=""> <span>Formato: DD/MM/YYYY</span></td>
				</tr>
				<tr>
					<td class="label">Título</td>
					<td><input class="text" name="titulo" id="titulo" maxlength="100" value=""></td>
				</tr>
				<tr>			
					<td class="label">Descrição</td>
					<td><textarea class="text" name="descricao" id="descricao" maxlength="2048"></textarea></td>
				</tr>
				<tr>
					<th colspan="2"><input type="button" id="btSend" name="btSend" value="Enviar"/></th>
				</tr>
			</table>
		</form>
	</fieldset>
</div>
