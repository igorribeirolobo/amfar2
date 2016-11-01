<?
session_start();
if (!$_SESSION['admUid']) die("Acesso Indevido");

include '../global.php';
include '../includes/funcoesUteis.php';

$id=isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$urlImages="noticias";

//debug($_POST);

if ($_POST['cmd']=='save') {
//	$_POST = str_replace("'","´",$_POST);

	$data = explode('/',$_POST['data']);
	$data="{$data[1]}/{$data[0]}/{$data[2]}";

	$titulo = trim($_POST['titulo']);
	$subTitulo = trim($_POST['subTitulo']);	
	$materia = trim($materia);
	$fotografo = trim($_POST['fotografo']);
	$legenda = trim($_POST['legenda']);
	$fonte = trim($_POST['fonte']);
	$credito = trim($_POST['credito']);
	$link = trim($_POST['link']);
	$status = $_POST['status'];
		
	if (!$id > 0) {	
		$query2=mssql_query("INSERT INTO noticias(data) VALUES('$data')
			SELECT @@IDENTITY AS LastID");
		$id=mssql_result($query2,0,'LastID');
		}
	
	$sql = "UPDATE noticias SET 
		status=$status, 
		data='$data',
		titulo='$titulo', 
		subTitulo='$subTitulo', 
		materia='$materia', 
		fotografo='$fotografo', 
		legenda='$legenda',
		fonte='$fonte', 
		creditos='$credito', 
		link=lower('$link') 
			WHERE idNoticia=$id";
	echo $sql;
	$query3 = mssql_query($sql) or die("Erro atualizando");

	if (mssql_rows_affected($conn)==0) $message="Erro";
	
	elseif (isset($_FILES['userFile']['name'])) {
	//	debug($_FILES);	  
		$arquivo=$_FILES['userFile']['name'];
		$tipo=$_FILES['userFile']['type'];
		if ($tipo != "image/gif" && $tipo != "image/pjpeg" && $tipo != "image/x-png")
			$message="Arquivo inválido! Somente: *.jpg ou *.jpeg, *.gif ou *.png";
			
		elseif(!move_uploaded_file($_FILES['userFile']['tmp_name'], "../$urlImages/$arquivo"))	
			$message="Não foi possível fazer upload - $urlDestino/$arquivo!";
		
		else			
			$query4 = mssql_query("UPDATE noticias SET foto='$urlImages/$arquivo' WHERE	idNoticia=$id");
		}
	echo"<script>opener.location.href='index.php?p=6';</script>";
	}	// post
	
	
	
if ($id > 0) {
	$query = mssql_query("SELECT *, CONVERT(CHAR(10), data, 103) AS fData FROM noticias WHERE idNoticia=$id");
	$rs = mssql_fetch_object($query);
	}
?>
<head>
	<title>AMF / Notícias</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<script>
		wysiwygHeight = 320;
		wysiwygWidth = 428;
	</script> 	
	<script language="JavaScript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
	<script language="JavaScript" type="text/javascript" src="../js/jquery.maskedinput-1.2.2.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>
	<script language="JavaScript" type="text/javascript" src="wysiwyg.js"></script> 
</head>

<script language="JavaScript">
	$(document).ready(function() {	
		var $data	= $('#data');
		var $titulo	= $('#titulo');
		var $subTitulo	= $('#subTitulo');
		var $materia= $('#materia');
	//	var $editor= $('#wysiwygmateria');
		var $fonte	= $('#fonte');
		var $credito= $('#credito');
		var $link	= $('#link');
		var $enviar	= $('#btSend');
		var myForm	= $('#myForm');
		var $enviar	= $('#btSend');
		var $btNew	= $('#btNew');
		var $cmd	= $('#cmd');
		
		var originColor='#fff';
		var alertColor='#FADEC5'
		$data.mask('99/99/9999',{placeholder:'_'});
		
		$('.text').focus(function() {
			$('.text').each(function() {						
				$(this).css('background',originColor);
				})
			$(this).css('background',alertColor);
			})

		$btNew.click(function() {document.location.href="noticias.php?id=0";})
		
		$enviar.click(function() {
			if ($data.val()=='') {
				alert('Digite a data por favor!');
				$data.focus();
				return false;
				}
			else if ($titulo.val().trim()=='') {
				alert('Digite o  título por favor!');
				$titulo.focus();
				return false;
				}

		//	var mat = document.getElementById("wysiwygmateria").contentWindow.document.body.innerHTML;  
		//	$materia.val(editorGetHtml('materia'));
			if ($materia.val().trim()=='') {
				alert('Digite a descrição!');
				$editor.focus();
				return false;
				}

			$enviar.attr('disabled',true);
			$cmd.val('save');
			return true;		
			})
		
		$data.focus();
		resizeTo(850,615);		
		})


	function enviar() {
		f = document.myForm;
		if (f.data.value=='') {
			alert('Digite a data por favor!');
			f.data.focus();
			return false;
			}
		else if (f.titulo.value.trim()=='') {
			alert('Digite o  título por favor!');
			f.titulo.focus();
			return false;
			}

	//	var mat = document.getElementById("wysiwygmateria").contentWindow.document.body.innerHTML;  
	//	$materia.val(editorGetHtml('materia'));
		if (f.materia.value.trim()=='') {
			alert('Digite a descrição!');
			f.materia.focus();
			return false;
			}

	//	$enviar.attr('disabled',true);
		f.materia.cmd.value='save';
		return true;		
		}		
	
</script>
<style>
	*{font:9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;color:#000}
	.label {text-align:right;padding:0 4px}
	.descricao {text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px;margin:0}
	#status{font-size:9pt;width:170px}
	#data{text-align:center;padding:0;width:80px}
	#titulo, #subTitulo, #fonte, #credito, #link{width:430px}
	#legenda{width:210px;height:60px;font-size:8pt}
	.buttons{cursor:pointer}
	form{margin:0;width:100%}
	.text{background:#fff;border:solid 1px #369}
</style>
<body bgcolor=#f0f0f0>
	<form method="POST" name="myForm" id="myForm" action="" ENCTYPE="multipart/form-data" onsubmit="return enviar()">
		<input type="hidden" name="id" value="<?=$id?>"/>
		<input type="hidden" name="urlFoto" value="<?=$rs->foto?>"/>
		<input type="hidden" name="upLoadId" value="<?=$upLoadId?>"/>
		<input type="hidden" name="MAX_FILE_SIZE" value="800000"/>
		<input type="hidden" name="cmd" id="cmd" value=""/>
		<div style="float:left;width:500px">
			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>			
					<td class="label">Data:</td>
					<td><input class="text" name="data" id="data" maxlength="10" value="<?=$rs->fData?>">
						Formato: DD/MM/YYYY <span class="label">Status: 
						<select class name="status" id="status" size="1">				 		
							<option <?=($rs->status==0)?'selected':''?> value="0">Página de Notícias</option>
							<option <?=($rs->status==1)?'selected':''?> value="1" >Divulgar em Destaques</option>
						</select>
					 </td>
				</tr>
				<tr>
					<td class="label">T&iacute;tulo:</td>
					<td><input class="text" name="titulo" id="titulo" maxlength="100" value="<?=$rs->titulo?>"/></td>	
				</tr>	
				<tr>
					<td class="label">Sub-t&iacute;tulo:</td>
					<td><input class="text" name="subTitulo" id="subTitulo" style="width:430px" maxlength="255" value="<?=$rs->subTitulo?>"/></td>
				</tr>	
				<tr>
					<td class="label">Met&eacute;ria:</td>
					<td style="background:#fff"><textarea id="materia" name="materia" style="width:430px; font-size:9pt"><?=$rs->materia?></textarea>
						<script language="javascript1.2">generate_wysiwyg('materia');</script>
					</td>
				</tr>
				<tr>
					<td class="label">Fonte:</td>
					<td><input class="text" name="fonte" id="fonte" style="width:430px" maxlength="100" value="<?=$rs->fonte?>"/></td>
				</tr>
				<tr>
					<td class="label">Cr&eacute;ditos:</td>
					<td><input class="text" name="credito" id="credito" style="width:430px" maxlength="100" value="<?=$rs->creditos?>"/></td>
				<tr>
					<td class="label">Link:</td>
					<td><input class="text" name="link" id="link" style="width:430px" maxlength="100" value="<?=$rs->link?>"/></td>
				</tr>
			</table>
		</div>

		<div style="float:right">
			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td align="center" colspan="2"><br /><b>INFORMAÇÕES EXTRAS</b></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class="label">Fotógrafo:</td>
					<td><input class="text" name="fotografo" id="fotografo" style="width:210px" maxlength="100" value="<?=$rs->fotografo?>"></td>
				</tr>	
				<tr>
					<td class="label">url Foto:</td>
					<td><input type=file name="userFile" id="userFile" value="<?=$foto?>"></td>
				</tr>	
				<tr><td>&nbsp;</td></tr>
				<tr>	
					<td class="label">Foto:</td>
					<td style="border:1px solid #eeeee4; width:200px; height:auto" align="center">
						<img src="../<?=$rs->foto?>" width="200" height="180"/></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td class="label">Legenda:</td>
					<td align="center"><textarea class="text" name="legenda" id="legenda"><?=$rs->legenda?></textarea></td>
				</tr>
				<tr>
					<td height="35" align="center"></td>
					<td align="center"><br />
						<input type="submit" class="buttons" id="btSend" name="btSend" value="Enviar"/>
						<input type="button" class="buttons" style="margin-left:30px" name="btNew" id="btNew" value="Novo Registro"/>
					</td>
				</tr>
			</table>
		</div>
	</form>
</body>
