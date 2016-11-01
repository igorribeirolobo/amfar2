<div style="margin-top:0; background:url(images/admCursos.png) no-repeat; height:24px"></div>
<?
include "../global.php";

$_msg=null;
//debug($_GET);
$idCurso=isset($_GET['idCurso']) ? $_GET['idCurso'] : $_POST['idCurso'];

if ($_POST['btSend']) {
	// pega os dados do formulario
//	debug($_POST);
	$titulo = str_replace("'",'"',trim($_POST['titulo']));
	$status = $_POST['status'];
	if ($_POST['inicio'] != '') {
		$dataInicio = explode('/',$_POST['inicio']);
		$dataInicio="{$dataInicio[1]}/{$dataInicio[0]}/{$dataInicio[2]}";			
		}
	else $dataInicio='00/00/0000';
		
	if ($_POST['final'] != '') {
		$dataFinal = explode('/',$_POST['final']);
		$dataFinal="{$dataFinal[1]}/{$dataFinal[0]}/{$dataFinal[2]}";			
		}
	else $dataFinal='00/00/0000';

	if ($_POST['dataMax'] != '') {
		$dataMax = explode('/',$_POST['dataMax']);
		$dataMax="{$dataMax[1]}/{$dataMax[0]}/{$dataMax[2]}";			
		}
	else $dataMax='00/00/0000';

	$descricao = str_replace("'",'"',trim($_POST['descricao']));
	$site = str_replace("'",'"',trim($_POST['url']));
	$valor = str_replace('.','',$_POST['valor']);
	$valor = str_replace(',','.',$valor);
	$cargaHoraria = (int)$_POST['cargaHoraria'];
	$parcelas = (int)$_POST['parcelas'];
	$boleto = ($_POST['boleto']=='on') ? 1 : 0;
	

	if (!$idCurso) {
		$sql = "INSERT INTO cursos(idParceiro, inicio, final, dataMax, titulo, descricao, url, valor, parcelas, cargaHoraria, status, boleto) values(
			0, '$dataInicio', '$dataFinal', '$dataMax', '$titulo', '$descricao', '$site', $valor, $parcelas, $cargaHoraria, $status, $boleto)
			select @@Identity as LastID";
		$query=@mssql_query($sql); //echo $sql;
		$idCurso=mssql_result($query, 0, 'LastID');
		}
	
	else {
		$sql = "update cursos set 
			inicio='$dataInicio',
			final='$dataFinal',
			dataMax='$dataMax',
			titulo='$titulo',
			descricao='$descricao',
			url=lower('$site'),
			valor=$valor,
			parcelas=$parcelas,
			cargaHoraria=$cargaHoraria,
			status=$status,
			boleto=$boleto
			where idCurso=$idCurso";
				
	//	echo $sql;
		$query = mssql_query($sql);
		}

	if (mssql_rows_affected($conn)==0)
		$_msg = "Erro";	
	else {
		$_msg = "Registro gravado com sucesso !";		
		}	
	}	// Post






$rs->valor = 0.0;
$rs->parcelas = 0;
$rs->cargaHoraria = 0;

if ($idCurso)	{
	$query = mssql_query("select *, 
		convert(char(10), inicio, 103) as inicio,
		convert(char(10), final, 103) as final, 
		convert(char(10), dataMax, 103) as dataMax from 
		cursos where idCurso=$idCurso");
	$rs = mssql_fetch_object($query);
	}
?>

<html>
	<head>
		<title>AMF/Agenda de cursos</title>
		<script>
			wysiwygHeight = 530;
			wysiwygWidth = 550;
		</script>
		<script language="JavaScript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
		<script language="javascript" type="text/javascript" src="../js/jquery.maskMoney.0.2.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/jquery.maskedinput-1.2.2.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/jquery.autotab.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>
		<script language="JavaScript" type="text/javascript" src="editor/wysiwyg.js"></script>
		<script language="JavaScript" type="text/javascript">
			$(document).ready(function() {
				var originColor='#fff';
				var alertColor='#FADEC5'
				$myForm = $('#myForm');
				$status = $('#status');
				$dataInicio = $('#inicio');
				$dataFinal = $('#final');
				$titulo = $('#titulo');
				$descricao = $('#wysiwygdescricao');				
				$valor = $('#valor');
				$valor.maskMoney({symbol:'',decimal:",",thousands:"."});
				$parcelas = $('#parcelas');
				$boleto = $('#boleto');
				$cargaHoraria = $('#cargaHoraria');
				$dataMax = $('#dataMax');				
				$btSend = $('#btSend');
				$btNew = $('#btNew');
				
				$('.data').mask('99/99/9999',{placeholder:'_'});			
				// desabilita tecla Enter
            $("input").keydown(function(event) {
					if (event.keyCode==13||event.wich==13)
						return false;
					});

				function clearfocus() {
					$('.text').each(function() {
						$(this).css('background',originColor);
						})				
					$('.numeric').each(function() {
						$(this).css('background',originColor);
						})
					$('.data').each(function() {
						$(this).css('background',originColor);
						})
					}
	
				$('.numeric').keypress(function(event) {
					if (event.charCode && (event.charCode < 48 || event.charCode > 57)) {
						alert('somente numeros por favor!')
						event.preventDefault();
						}
					});
				
				$('.text').focus(function() {
					clearfocus();
					$(this).css('background',alertColor);
					})			
				$('.data').focus(function() {
					clearfocus();
					$(this).css('background',alertColor);
					})
				$('.numeric').focus(function() {
					clearfocus();
					$(this).css('background',alertColor);
					})	
				
				$btNew.click(function() {document.location.href="cursos.php";})
					
				function checkForm() {
					if ($dataInicio.val()==''||!$dataInicio.val().validDate()) {
						alert('Digite corretamente a data inicial');
						$dataInicio.focus();
						return false;
						}
					else if ($dataFinal.val()==''||!$dataFinal.val().validDate()) {
						alert('Digite corretamente a data final ou\nrepita a data inicial caso seja único dia');
						$dataFinal.focus();
						return false;
						}
					else if ($titulo.val()=='') {
						alert('Digite o título do curso!');
						$titulo.focus();
						return false;
						}
					else if (editorIsEmpty('descricao')) {
						alert('Digite a descrição do curso');
						$descricao.focus();
						return false;
						}
					else if ($valor.val()=='') {
						alert('Digite o valor do curso ou deixe \'0,00\'.');
						$valor.focus();
						return false;
						}
					else if ($parcelas.val()=='') {
						alert('Digite a quantidade de parcelas ou deixe \'0\'.');
						$parcelas.focus();
						return false;
						}
					else if ($cargaHoraria.val()=='') {
						alert('Digite a carga horária ou deixe \'0\'.');
						$cargaHoraria.focus();
						return false;
						}
					else if ($dataMax.val()==''||!$dataMax.val().validDate()) {
						alert('Digite corretamente da data máxima para inscrição\nou repita a data inicial caso seja único dia');
						$dataMax.focus();
						return false;
						}
					return true;
					}
					
				resizeTo(670,635);
				$dataInicio.focus();
				
				if ($('#msg').val()!='') {
					alert($('#msg').val());
					if ($('#msg').val()!='Erro')
						opener.location.href='index.php?p=8';				
					}			
				})
		</script>
		
	</head>
	<style>
		*{font:9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;color:#000}
		.label {text-align:right;padding:0 4px}
		.descricao {text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px;margin:0}
		#status{font-size:9pt;width:220px}
		.data{text-align:center;padding:0;width:80px}
		#titulo{width:494px}
		#valor{text-align:right;width:70px}
		#parcelas{width:30px;text-align:center}
		#cargaHoraria{text-align:center;width:35px;padding:0}
		.buttons{cursor:pointer}
		form{margin:0;width:100%}
	</style>
	<body style="margin:0;overflow:hidden;background:#ccc">
		<form name="myForm" id="myForm" action="" method="POST" onsubmit="return checkForm()">	
			<input type="hidden" name="idCurso" value="<?=$idCurso?>"/>
			<input type="hidden" id="msg" value="<?=$_msg?>"/>
			<table border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td class="label">Classe:</td>
					<td>
						<div style="float:left">
							<select class="text" name="status" id="status" size="1">
								<option value="0" <?=($rs->status==0)?'selected':''?>/>AMF - Cursos de Atualização</option>
								<option value="1" <?=($rs->status==1)?'selected':''?>/>AMF - Cursos de Especialização</option>
								<option value="2" <?=($rs->status==2)?'selected':''?>/>Cursos de Atualização (Terceiros)</option>
								<option value="3" <?=($rs->status==3)?'selected':''?>/>Cursos de Especialização (Terceiros)</option>
							</select>
						</div>
						<div style="float:right">
							<span class="label">Início:</span> 
							<input type="text" class="data" name="inicio" id="inicio" maxlength="10" value="<?=($rs->inicio!='01/01/1900')?$rs->inicio:''?>"/>
							<span class="label">Término:</span>
							<input type="text" class="data" name="final" id="final" maxlength="10" value="<?=($rs->final!='01/01/1900')?$rs->final:''?>"/>
						</div>
					</td>
				</tr>
				<tr>
					<td class="label">Título:</td>
					<td><input type="text" name="titulo" id="titulo" maxlength="100" value="<?=$rs->titulo?>"/></td>
				</tr>
				<tr>
					<td class="descricao" colspan="2">Descrição:</td>
				</tr>
				<tr>
					<td colspan="2" style="background:#fff">
			  			<textarea class="text" id='descricao' maxlength="255" name="descricao" cols="50" rows="20"/><?=$rs->descricao?></textarea>
						<script language="javascript1.2">generate_wysiwyg('descricao');</script>
					</td>
				</tr>
				<tr>
					<td class="label">Valor R$:</td>
					<td>
						<div style="float:left">
							<input type="text" class="numeric" name="valor" id="valor" maxlength="20" value="<?=number_format($rs->valor,2,',','.') ?>"/>
							Parc.: <input type="text" class="numeric" name="parcelas" id="parcelas" maxlength="2" value="<?=$rs->parcelas?>"> X
							&nbsp;&nbsp;<input type="checkBox" name="boleto" id="boleto" <?=($rs->boleto==1)?'checked':''?> /> Boleto
						</div> 
						<div style="float:right">
							<span class="label">Carga Hor.:</span>
							<input type="text" class="numeric" name="cargaHoraria" id="cargaHoraria" maxlength="6" value="<?=$rs->cargaHoraria?>"/> hs
							<span class="label">Data máx.:</span>
							<input type="text" class="data" name="dataMax" id="dataMax" maxlength="10" value="<?=$rs->dataMax?>"/>
						</div>
					</td>
				</tr>
				<tr>
					<td height="25" align="center" colspan=2>
						<input type="submit" class="buttons" name="btSend" id="btSend" value="Enviar"/>
						<input type="button" class="buttons" style="margin-left:30px" name="btNew" id="btNew" value="Novo Registro"/>
					</td>
				</tr>
			</table>
			</form>
		</body>
</html>
