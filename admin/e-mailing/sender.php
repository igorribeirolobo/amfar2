<html>

	<head>
		<title>E-Mailing|Manager Send</title>
		<link rel="shortcut icon" type="image/ico" href="/favicon.ico" />		
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
		<script language="javascript" type="text/javascript" src="ajax.js"></script>
	</head>
	
	<body>

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
	 * - Script responsável pelo envio em lote.
	 * - Caso receba o campo e-mail, envia somente um para o email informado
	 	  neste caso os ID's são desconsiderados
	 * - Campo email vazio, será considerado o ID inicial e o ID final,
	 	  ambos colocados automaticamente pelo sistema mas podem ser alterados.
	 *	- O script envia o primeiro lote de no máximo 20 emails e aguarda 8 segs
	 	  para enviar o próximo lote e assim sucessivamente até chegar no último ID.
 */
 
if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');

session_start();
if (!$_SESSION['usrAdm']) {
	die();
	}

include 'config/config.php';
$conn = mysql_connect($host, $userDB,$pwdDB) or die (mysql_error());
mysql_select_db($dataBase) or die( "database não pode ser selecionada");


//error_reporting(E_ALL);
include("mail/htmlMimeMail.php");


// parte text/html
$ds = date("w");
$mes = date("n");
$dia = substr(date("0d"), -2);
$ano = date("Y");

$semana = array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado');
$nomeMes = array('','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
$dataext =	"{$semana[$ds]}, $dia {$nomeMes[$mes]} $ano";

$dataEnvio = date('Y/m/d');

// parte text/html

$body = "
	<html>
		<head>
			<title>$_coName: Mailing</title>
			<style>
				#topo {
					background:url('$_coSite/e-mailing/$_bgTop') no-repeat;
					height:100px;
					padding:44px 0 0 125px;
					font: bolder 18pt Arial, Helvetica, sans-serif, Verdana;
					color:#ffffff;
					text-align:center;
					}

				.titulo {
					padding:44px 0 0 125px;
					font: bolder 14pt Arial, Helvetica, sans-serif, Verdana;
					color:#ffffff;
					text-align:center;
					}

				.data {
					padding:0 4px;
					margin:0;
					font: bolder 10pt Arial, Helvetica, sans-serif, Verdana;
					text-align:right;
					background:#f0f0f0;
					}

				#warning {
					padding:0 4px;
					margin:0;
					font: normal 9pt Arial, Helvetica, sans-serif, Verdana;
					text-align:center;
					}

				#corpo {
					border-left:1px solid #004d6f;
					border-right:1px solid #004d6f;
					text-align:center;
					background:#f0f0f0;
					}

				#rodape {
					background:#a0a0a0;
					text-align:center;
					font: bolder 10pt Arial, Helvetica, sans-serif, Verdana;
					color:#000;
					height:20px;
					background:url('$_coSite/e-mailing/$_bgBottom') no-repeat;
					}

				#rodape a {
					font: bolder 9pt arial, verdana;
					color:#efefef; font-size:9pt;
					text-decoration:none
					}

				#rodape a:hover {
					color:#fff;
					text-decoration:none;
					border-bottom:1px solid #c40000
					}
			</style>
		</head>

		<body topmargin='4' leftmargin='4'/>
			<table border='0' cellpadding='0' cellspacing='0' width='565'/>
				<tr>
					<td id='topo' class='titulo'>$tit</td></tr>
				<tr><td id='corpo'>
					<p class='data'>:: Newsletter - $dataext ::</p></tr>
				<tr>
					<td id='corpo'>{$_SESSION['eBody']}</td>
				</tr>
			</table>
		</body>
	</html>\r\n";
	

/*    Se vier o email dispara somente para ele como teste	*/
if ($_SESSION['eMail'])	{

	$query = mysql_query("select id, nome, email from mailing where email= '{$_SESSION['eMail']}'");
	$status=0;
	$id=0;

	if (mysql_numrows($query))	{
		$result	= mysql_fetch_array($query);
		$id		= $result['id'];
		$nome		= $result['nome'];
		$email	= $result['email'];
		$nome 	= $result['nome'];

		$html= "<div id=msgOk>[$id] $nome [$email]...";
		}
		
	else
		$html="<div id=msgErr>[$email] ID não localizado! enviando sem atualizar tabela mailing...";
		
	if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102') $email='lauro@localhost';
	
	$body = str_replace('?id=',"?id=$id", $body);
	$body = str_replace('#email', $email, $body);
	
	$shtml = $body;	
	$text = strip_tags($shtml);
	$mail = new htmlMimeMail();
	$mail->setReturnPath($_returnPath);
	$mail->setFrom("$_coName <$_mailFrom>");
	$mail->setSubject("$_coName: Newsletter {$nomeMes[$mes]}/$ano");
	$mail->sethtml($shtml, $text, null);
	$result	= $mail->send(array($email));
	$msgMail = $result ? 'Success' : $mail->errors;
	if ($msgMail == 'Sucess') $status = 1;
	
	$html .= "($msgMail)</div>";

	if ($id > 0) 
		mysql_query("update mailing set dataEnvio='$dataEnvio', arquivo='$fname' where id=$id");
		
	echo "$html";
	include 'home.html';
	exit;
	}	// email de teste




// **** loop para envio.

if ($_SESSION['nextID'] == 0) {
	$msg=" 
		<meta http-equiv='refresh' content='0;URL=sender.php' />
		
		Total de emails enviados: </b>{$_SESSION['enviado']}</b><br>
		Não há mais e-mails para envio - STOPPED !!!";
		
	unset($_SESSION['enviado']);
	unset($_SESSION['categ']);
	unset($_SESSION['limite']);
	unset($_SESSION['nextID']);
	unset($_SESSION['lastID']);
	unset($_SESSION['eBody']);
	
	echo $msg;
	include 'home.html';
	exit;
	}
	
	
$hoje=date('Y-m-d');

if ($_SESSION['categ'] == 0)
	$sql="select id, nome, email, status from mailing where (status >= 0) and (id >= {$_SESSION['nextID']} and id <= {$_SESSION['lastID']}) order by id limit {$_SESSION['limite']}";
else
	$sql="select id, nome, email, status from mailing where (status >= 0 and categ={$_SESSION['categ']}) and (id >= {$_SESSION['nextID']} and id <= {$_SESSION['lastID']}) order by id limit {$_SESSION['limite']}";
	
$query= mysql_query($sql) or die("Erro $sql :" . mysql_error());

echo $sql;

if (mysql_numrows($query))	{

	$html= "
	<table id='tableSend' width=100% bgcolor=#ffffff>
		<tr>
			<td class=tfield>ID</td>
			<td class=tfield>Nome</td>
			<td class=tfield>email to</td>
			<td class=tfield>Categoria</td>
			<td class=tfield>Status</td>
			<td class=tfield>STMP</td>
		</tr>";
	$total=mysql_num_rows($query);
	$counter=0;
	while ($result = mysql_fetch_array($query)) {
	
		$id	= $result['id'];
		$nome	= substr($result['nome'],0,20);
		$email= $result['email'];

		$mBody = str_replace('?id=',"?id=$id", $body);
		$mBody = str_replace('#email', $email, $mBody);
		$shtml = $mBody;
		
		$text = strip_tags($shtml);	
		$mail = new htmlMimeMail();
		$mail->setReturnPath($_returnPath);
		$mail->setFrom("$_coName <$_mailFrom>");
		$mail->setSubject("$_coName: Informativo {$nomeMes[$mes]}/$ano");
		$mail->sethtml($shtml, $text, null);
		
		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')			
			$result = $mail->send(array('lauro@localhost'));
		else
			$result = $mail->send(array($email));
			
		$msgMail = $result ? 'Success' : $mail->errors;

		if ($msgMail == 'Success')	{
			$color='class="sended"';
			$_SESSION['enviado']++;
			mysql_query("update mailing set dataEnvio=now(), arquivo='$fname', status=status+1 where id=$id") or die("Erro:" . mysql_error());
			}					
		else	{		
			$color='class="notSended"';					
			}
			
		$_SESSION['nextID']=$id+1;
		if ($msgMail == 'Success')
			$html .="
				<tr>
				<td $color align=right>$id&nbsp;</td>
				<td $color>&nbsp;$nome</td>
				<td $color>&nbsp;$email</td>
				<td $color align=center>&nbsp;{$_SESSION['categ']} - {$_SESSION['categName']}</td>
				<td $color align=center>{$result['status']}
				<td $color align=center>&nbsp;$msgMail</td>";
				
						
		$counter++;
		unset($mail);
		}	// while
		
	$_SESSION['nextID']++;
		
	$html .="
		<tr>
			<td align=center colspan=6 class=sended>
				<div id=msgOk>Total de e-mails enviados: $counter</div></td>
		</table>
		<meta http-equiv='refresh' content='10; URL=sender.php' />";
		
	}	//	if			
else
	$html .="
		<div id=msgOk><font size=+2><b>Envio Encerrado !!!<br>
		<img src=images/sended.png><br>
		Total de e-mails enviados: {$_SESSION['enviado']}</b></div>";
	
echo $html;
?>
</body>
</html>