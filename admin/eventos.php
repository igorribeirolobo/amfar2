<div style="margin-top:0; background:url(images/admEventos.png) no-repeat; height:24px"></div>
<?
include "../global.php";
//include_once '../includes/funcoesUteis.php';

$idEvento=isset($_GET['id']) ? $_GET['id'] : $_POST['idEvento'];
if (!$idEvento) $idEvento=0;
$novo = false;
$msg=null;
if ($_POST['cmd']=='save') {
	$_POST = str_replace("'","´",$_POST);
	$_status = $_POST['status'];
	$_data =  $_POST['data'];
//	$_data = implode("-", array_reverse(explode("/", $_POST['data'])));
//	$_data = explode('/',$_POST['data']);
//	$_data = "$data[1]/$data[0]/$data[2]"; // mm/dd/yyyy
	$_tipo = $_POST['tipo'];	
	$_titulo = trim($_POST['titulo']);
	$_email = trim($_POST['email']);
	$_site = trim($_POST['site']);
	$_descricao = str_replace("'","´",trim($_POST['descricao']));	

	if ($idEvento==0) {
		$query=mssql_query("INSERT INTO eventos(data) VALUES('$_data')
			SELECT @@IDENTITY AS LastID");
		$idEvento=mssql_result($query,0,0);
		$novo = true;
		}

	if ($idEvento > 0) {
		$sql = "UPDATE eventos SET 
			status=$_status,
			data='$_data',
			tipo='$_tipo',
			titulo='$_titulo',
			email=lower('$_email'),
			site=lower('$_site'),
			descricao='$_descricao'
		WHERE idEvento=$idEvento";
		echo $sql;
		die();
	//	mssql_query($sql) or die("Erro: $sql");

		if (mssql_rows_affected($conn)==0) {
			if ($novo)		
				$msg = "Erro inserindo novo registro\nid=$idEvento\n\n$sql";
			else
				$msg = "Erro atualizando registro\nid=$idEvento\n\n$sql";
			}	
		else {
			if ($novo)
				$msg = "Novo registro inserido com sucesso!";
			else
				$msg = "Registro alterado com sucesso!";
			}
		}
	else
		$msg = "Erro inserindo novo registro\nid=$idEvento\n\n$sql";
	}	// Post








if ($idEvento)	{
	$query = mssql_query("SELECT *, CONVERT(CHAR(10), data, 103) AS fData FROM eventos WHERE idEvento=$idEvento");
	$rs = mssql_fetch_object($query);
	}
?>

<html>
	<head>
		<title>AMF/Agenda de Eventos</title>

		<script language="JavaScript" type="text/javascript" src="editor/wysiwyg.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>	
		<script language="JavaScript" type="text/javascript" src="../js/jquery.maskedinput-1.2.2.min.js"></script>
		<script>
			wysiwygHeight = 370;
			wysiwygWidth = 580;
			$(document).ready(function() {
				var originColor='#fff';
				var alertColor='#fadec5'
				$('#data').mask('99/99/9999',{placeholder:'_'});
				
				$('.text').focus(function() {
					$('.text').each(function() {						
						$(this).css('background',originColor);
						})
					$(this).css('background',alertColor);
					})
					
				$('#btSend').click(function() {
					if (!$('#data').val().validDate()) {
						alert('Digite corretamente o campo \'Data\'');
						$('#data').focus();
						return false;
						}
					else if ($('#tipo').val()=='') {
						alert('Selecione o tipo de \'Evento\'');
						$('#tipo').focus();
						return false;
						}
					else if (!$('#email').val().validEmail()) {
						alert('Digite corretamente o campo \'E-mail\'');
						$('#email').focus();
						return false;
						}
					else if ($('#descricao').val()=='') {
						alert('Digite a descrição do \'Evento\'');
						$('#descricao').focus();
						return false;
						}
					$('#cmd').val('save');
					$('#myForm').submit();
					})
					
				if ($('#msg').val()!='') {
					alert($('#msg').val());
					}
				})
		</script>

	</head>
	<style>
		*{font:9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;color:#000}
		body {overflow:hidden;background:#fff}
		#table td {border:solid 1px silver}
		.text {background:#fff;border:solid 1px #369;padding:0;padding-left:4px;font-size:9pt}
		#btSend, #btNew{cursor:pointer}
		.label {text-align:right;padding:0 4px}		
		#data{text-align:center;width:80px}
		select{font-size:9pt;border:solid 1px #369;background:#fff}
		select option{padding-left:10px}
		form {margin:0}
		#titulo{width:502px}
		textarea {width:502px;background:#fff;border:solid 1px #369}
		th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px;font-size:10pt}
	</style>
	<body style="margin:0" onload="resizeTo(600,660)">
		<form name="myForm" id="myForm" method="POST" action="">	
			<input type="hidden" name="idEvento" id="idEvento" value="<?=$idEvento?>"/>
			<input type="hidden" name="cmd" id="cmd" value=""/>
			<input type="hidden" id="msg" name="msg" value="<?=$msg?>"/>
			<table id="table" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td class="label">Classe:</td>
					<td>
						<div style="float:left">
							<select name="status" id="status" size="1">
								<option value="0" <?=($rs->status==0)?'selected':'' ?>/>Eventos AMF</option>
								<option value="1" <?=($rs->status==1)?'selected':'' ?>/>Eventos Diversos</option>
							</select>
						</div>
						<div style="float:right">
							<span class="label">Data:</span>
							<input type="text" class="text" name="data" id="data" maxlength="10" value="<?=$rs->fData?>"/>
							
							<span class="label" style="margin-left:60px">Tipo:</span> 
						 	<select class="text" name="tipo" id="tipo" size="1">
							 	<option value="">Selecione</option>
								<option value="PALESTRA" <?=($rs->tipo=='PALESTRA')?'selected':''?>/>PALESTRA</option>
								<option value="SIMPÓSIO" <?=($rs->tipo=='SIMPÓSIO')?'selected':''?>/>SIMPÓSIO</option>
								<option value="CONGRESSO" <?=($rs->tipo=='CONGRESSO')?'selected':''?>/>CONGRESSO</option>
								<option value="CURSO" <?=($rs->tipo=='CURSO')?'selected':''?>/>CURSO</option>
								<option value="OUTROS" <?=($rs->tipo=='OUTROS')?'selected':''?>/>OUTROS</option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td class="label">Título:</td>
					<td><input type="text" class="text" name="titulo" id="titulo" maxlength="100" value="<?=$rs->titulo?>"/></td>
				</tr>
				<tr>
					<td class="label">E-mail:</td>
					<td>
						<div style="float:left">
							<input type="text" class="text" name="email" id="email" style="width:200px" maxlength="100" value="<?=$rs->email?>"/>
						</div>
						<div style="float:right">
							<span class="label">Link:</span>
							<input type="text" class="text" name="site" id="site" style="width:250px" maxlength="100" value="<?=$rs->site?>"/>
						</div>
					</td>
				</tr>
				<tr>
					<th colspan="2">Descrição do Evento</th>
				</tr>
			</table>			
			
			<textarea id='descricao' maxlength="255" name="descricao" cols=50 rows=3 /><?= $rs->descricao ?></textarea>
			<script language="javascript1.2">generate_wysiwyg('descricao');</script>
			
			<div style="text-align:center;padding:4px">
				<input type="submit" name="btSend" id="btSend" value="Enviar"/>
				<input style="margin-left:30px;cursor:pointer" type="button" name="btNew" id="btNew" value="Limpar"/>
			</div>
		</form>
	</body>
</html>
