<?php
/*
 * e-Mailing - Sistema automatizado para criação e envio de emails por lote
 * Copyright (C) 2007 Lauro A. L. Brito
	 *
	 * == BEGIN LICENSE ==
	 *
	 * Licensed under the terms of any of the following licenses at your
	 * choice:
	 *
	 *  - GNU General Public License Version 2 or later (the "GPL")
	 *    http://www.gnu.org/licenses/gpl.html
	 *
	 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
	 *    http://www.gnu.org/licenses/lgpl.html
	 *
	 *  - Mozilla Public License Version 1.1 or later (the "MPL")
	 *    http://www.mozilla.org/MPL/MPL-1.1.html
	 *
	 * == END LICENSE ==
	 *
	 * - Seleção de arquivos htm(l) ou image que será o corpo do email
	 * - Configuração de range de ID's para envio,
	 * - Seleção de uma categoria específica para envio ou para todos com exceção de bloqueados
	 * - Envio para somente um email para teste independente de estar na tabela ou não.
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');


session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}

include 'config/config.php';

mysql_connect($host,$userDB, $pwdDB);
mysql_select_db($dataBase);


$_SESSION['table'] = ($_POST['cmd']=='teste')?"mailing_test":"mailing";

// pesquisa o primeiro id valido
$query = mysql_query("SELECT MIN(id) AS minId FROM {$_SESSION['table']} WHERE status >= 0");
$firstId = @mysql_result($query, 0, 'minId');

// pesquisa o ultimo id valido
$query = mysql_query("SELECT MAX(id) AS maxId FROM {$_SESSION['table']} WHERE status >= 0");
$lastId = @mysql_result($query, 0, 'maxId');


// total de emails válidos
$query = mysql_query("SELECT COUNT(*) AS total FROM {$_SESSION['table']} WHERE status >= 0");
$total = @mysql_result($query, 0, 'total');

// pesquisa o ultimo id valido
$hoje=date('Y-m-d');
$sql="SELECT MAX(id) AS maxid FROM {$_SESSION['table']} WHERE left(dataEnvio, 10) ='$hoje'";
$query = mysql_query($sql);
$maxid = @mysql_result($query, 0, 'maxid') + 1;
$nextId = ($maxid <= $lastId) ? $maxid : 1;

$toSend=false;

if($_POST['cmd']=='teste'){;
	$_SESSION['nextID'] = $firstId;	// iniciar a partir deste ID	
	$_SESSION['lastID'] = $lastId;	// ultimo ID válido
	}


if ($_POST['cmd']=='send') {
	$_SESSION['table'] = ($_POST['test'])?"mailing_test":"mailing";

	$_SESSION['eBody'] = null;
	$_arquivo = $_POST['arquivo'];
	if (!empty($_arquivo)) {		
		$fh=file("$_arquivo");	
		foreach($fh as $line){
			$_SESSION['eBody'] .= $line;
			}
		$_SESSION['arquivo'] = $_arquivo;
		$_SESSION['eMail'] = isset($_POST['email']) ? $_POST['email'] : null;	// somente um para teste	
		$_SESSION['enviado']=0;
		$_SESSION['nextID'] = $_POST['nextId'];	// iniciar a partir deste ID	
		$_SESSION['lastID'] = $_POST['lastId'];	// ultimo ID válido	
		if ($_SESSION['lastID']==0) {
			$query = mysql_query("SELECT MAX(id) AS maxId FROM {$_SESSION['table']} WHERE status >= 0");
			$_SESSION['lastID'] = mysql_result($query, 0, 'maxId');
			}		
		$_SESSION['limite']= isset($_POST['amount']) ? $_POST['amount'] : 30;	
		$_SESSION['categ']= isset($_POST['cbCateg']) ? $_POST['cbCateg'] : 0;
	
		if ($_SESSION['categ'] > 0) {
			$query=mysql_query("SELECT categ FROM mailing_categ WHERE id={$_SESSION['categ']}");
			$_SESSION['categName']=mysql_result($query, 0, 'categ');
			}
		else
			$_SESSION['categName']='TODOS';
	
		$_SESSION['UF']= isset($_POST['cbUF']) ? $_POST['cbUF'] : '';	
		$toSend=($_SESSION['nextID'] <= $_SESSION['lastID']) ? true : false;		
		$_SESSION['subject']= trim($_POST['subject']);
		
		//echo"<pre>";
		////print_r($_SESSION);
		//echo"</pre>";
		//die();
		}
	}	// fim do post
	
?>

<html>
	<head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<script language="javascript" type="text/javascript" src="js/jquery-1.3.2.js"></script>
		<script language="javascript" type="text/javascript" src="js/prototypes.js"></script>
		<title>E-Mailing|Manager Send</title>
		<link rel="shortcut icon" type="image/ico" href="/favicon.ico" />	
		<script language="javascript">
			$(document).ready(function(){
				var originColor='#fff';
				var alertColor='#E9F8E7'
				$('.text').each(function(){$(this).css('border','solid 1px #369').css('padding','1px');})
				$('.text').focus(function(){$(this).css('background',alertColor);})
				$('.text').blur(function(){$(this).css('background',originColor);})
				$('.text').keyup(function(){if (this.id=='nome')$(this).val($(this).val().upper());})				
				$('#mailText').focus();
				
				$('.sel').click(function(){
					id = this.id.split('.');
					$('#subject').val(id[0]);
					})
				$('#test').click(function(){
					$('#cmd').val($(this).is(':checked')?'teste':'');
					$('#setIt').submit();
					})
				$('#sendThis').click(function(){
					if($('#subject').val()==''){
						alert("Selecione um arquivo para envio!");
						$('#subject').focus();
						return false;
						}
					var nextId = parseInt($('#nextId').val());
					var lastId = parseInt($('#lastId').val());		
											
					if(nextId ==0 || isNaN(nextId)) {
						alert("Digite um ID válido para início");
						$('#nextId').focus();
						return false;
						}
					if(lastId ==0 || isNaN(lastId)) {
						alert("Digite um ID válido para final");
						$('#lastId').focus();
						return false;
						}
					if(nextId > lastId) {
						alert("Id inicial não pode ser superior ao Id final!");
						$('#nextId').focus();
						return false;
						}
					if (!confirm("Antes de clicar em OK verifique:?\n\nOs ID's inicial e final estão corretos ?\nO(s) arquivo(s) selecionado(s) está(ão) correto(s) ?\nA categoria selecionada está correta?\n\nTecle [OK] para iniciar o envio."))
						return false;
					$('#cmd').val('send');
					$('#setIt').submit();
					})
				})
		</script>
	</head>

	<body>
		<div id="tudo">
		<center>
		<? include 'includes/topo.php';?>
  
			<table border="0" cellpadding="2" cellspacing="2" width="100%">
				<tr>
					<td class="tfield">DATE:</td>
					<td class="tdata">Data de Envio: <?=date('d/m/Y')?></td>      
					<td class="tfield">ID Inicial:</td>
					<td class="tdata"><b><?= substr("000000000$firstId",-79)?></b></td>		
					<td class="tfield">ID Final:</td>
					<td class="tdata"><b><?=substr("000000000$lastId",-7)?></b></td>
					<td class="tfield">Total:</td>
					<td class="tdata"><b><?=substr("000000000$total",-7)?></b></td>
					<td class="tfield">Table:</td>
					<td class="tdata" width="150"><b><?=strtoupper($_SESSION['table'])?></b></td>
				</tr>
			</table>
		
			<form name="setIt" id="setIt" action="" method="POST">
				<input type="hidden" name="currId" value="0"/>
				<input type="hidden" name="cmd" id="cmd" value=""/>
				<table border="0" cellpadding="1" cellspacing="1" width="100%">		
					<tr>
						<td valign="top" width="100%">
							<small>Este arquivo será o corpo do e-mail Só é permitido um arquivo para envio.</small><br>
							<b>Selecione o arquivo HTML p/envio:</b><br><?	
							$dh = dir ('files/');
							while ($entry = $dh->read()){
								$temp = explode('.',$entry);
								$fname = $temp[0];
								$ext = $temp[1];
								if (stristr($ext,"htm")){
									$checked = ($_SESSION['arquivo']=="files/$entry")?'checked':'';
									echo "<input $checked type='radio' name='arquivo' id='$fname' value='files/$entry' class='sel'/>$fname<br>";
									}
								}	?>
						</td>
						<td valign="top">
							<style>
								#settings td{text-align:left;border:solid 1px silver;;background:silver;padding:2px 0 2px 0}
								#settings .label{text-align:right;padding:4px;border:0}
								#settings .text{width:200px;text-align:left;font-size:12px}
								#settings .button{cursor:pointer}
								#settings .button:hover{color:red;font-weight:bolder}
							</style>
							<table id="settings" cellpadding="0" cellspacing="2" border="0">
								<tr>
									<td class="label">Inicio:</td> 
									<td>
										<input type="text" id="nextId" name="nextId" style="text-align:center" size=11
											value="<?= isset($_SESSION['nextID']) ? $_SESSION['nextID'] : $nextId ?>"/>
									</td>
									<td class="label">Final:</td>
									<td>
										<input type="text" id="lastId" name="lastId" style="text-align:center" size=11
											value="<?= isset($_SESSION['lastID']) ? $_SESSION['lastID'] :  $lastId ?>"/>
									</td>
									<td class="label">Lote:</td>
									<td><input type="text" id="amount" name="amount" value="<?=$_SESSION['limite']?>"/></td>
								</tr>
								<tr>
									<td class="label" nowrap>E-mail-teste:</td>
									<td colspan="5"><input type="text" class="text" id="mailText" name="email" style="text-align:left" value=""/></td>
								</tr>
								<tr>
									<td class="label" valign="top">Assunto:</td> 
									<td colspan="5"><textarea type="text" class="text" rows="2" id="subject" name="subject"><?=$_SESSION['subject']?></textarea></td>
								</tr>	
								<tr>
									<td class="label">Categoria:</td>
									<td colspan="5"> 
										<select name="cbCateg" id="cbCateg" class="text">
											<option selected value="0">TODAS</option>
										<?
										$query=mysql_query("SELECT * FROM mailing_categ ORDER BY categ");
										while ($rsQuery=mysql_fetch_object($query)){
											$checked = ($_SESSION['categ']==$rsQuery->id)?'selected':'';										
											echo "<option $checked value='$rsQuery->id'/>$rsQuery->id: $rsQuery->categ</option>";
											}	?>
										</select>
									</td>
								</tr>	
								<tr>
									<td class="label">Estado:</td>
									<td colspan="5">
										<select name="cbUF" id="cbUF" class="text">
											<option value="">Selecione</option>
											<option value="0">Todos</option>
										<?
										$query=mysql_query("SELECT * FROM estados ORDER BY estado");
										while ($rsQuery=mysql_fetch_object($query)){
											$checked = ($_SESSION['UF']==$rsQuery->sigla)?'selected':'';	
											echo "<option $checked value='$rsQuery->sigla'/>$rsQuery->sigla: $rsQuery->estado</option>";
											}	?>
										</select>
									</td>					
								</tr>
								<tr>
									<td style="text-align:center;background:silver" colspan="6">
										<input type="checkbox" name="test" id="test" value="On" <?=($_SESSION['table']=='mailing_test')?'checked':''?>/>Enviar Tabela Teste
										<input style="margin-left:30px" type="button" class='button' name="sendThis" id="sendThis" value="Enviar"/>
									</td>					
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form><?
			if (!$toSend) 
				include 'home.html';
			else
				echo "
					<iframe marginwidth='0' marginheight='0' name='working' 
						src='sender.php' frameborder='0' width='100%' scrolling='auto' height='382'>
					</iframe>";	?>
			
			<?
				include 'includes/rodape.html';
				//echo "<pre>";
				//print_r($_SESSION);
				//echo "</pre>";
			?>
			<center>
		</div>
	</body>
</html>
