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

$dataEnvio = date('Y/m/d');

/*    Se vier o email dispara somente para ele como teste	*/
if ($_SESSION['eMail'])	{
	$_email = $_SESSION['eMail'];
	$query = mysql_query("SELECT id, nome, email FROM {$_SESSION['table']} WHERE email= '$_email'");
	$status=0;
	$id=0;

	if (mysql_numrows($query))	{
		$result	= mysql_fetch_object($query);
		$id		= $result->id;
		$nome		= $result->nome;
		$_email	= $result->email;
		$nome 	= $result->nome;

		$html= "<div id=msgOk>[$id] $nome [$_email]...";
		}
		
	else
		$html="<div id=msgErr>[$_email] ID não localizado! enviando sem atualizar tabela {$_SESSION['table']}...";
		
	$body = str_replace('#email', $_email, $_SESSION['eBody']);
	
	//	echo "email={$_SESSION['eMail']}";
	$shtml = $body;	
	$text = strip_tags($shtml);
	$mail = new htmlMimeMail();
	$mail->setReturnPath('amfemkt@amfar.com.br');
	$mail->setFrom("AMF - Associação Mineira de Farmacêuticos <amfemkt@amfar.com.br>");
	$mail->setSubject($_SESSION['subject']);
	$mail->setReplayTo('amfar@amfar.com.br');
	$mail->sethtml($shtml, $text, 'files/images/');
	$result	= $mail->send(array($_email));
	$msgMail = $result ? 'Success' : $mail->errors;
	if ($msgMail == 'Sucess') $status = 1;
	
	$html .= "($msgMail)</div>";

	if ($id > 0) 
		mysql_query("UPDATE {$_SESSION['table']} SET dataEnvio='$dataEnvio' WHERE id=$id");
		
	echo "$html";
	include 'home.html';
	}	// email de teste


elseif ($_SESSION['nextID'] == 0){
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
	}



else {	
	$hoje=date('Y-m-d');
	$sql=sprintf("SELECT id, nome, email, status, uf, dataEnvio
		FROM {$_SESSION['table']} WHERE (status >= 0) AND (id BETWEEN %d AND %d)",$_SESSION['nextID'],$_SESSION['lastID']);

	if ($_SESSION['categ'] > 0)
		$sql .= sprintf(" AND (categ=%d)", $_SESSION['categ']);

	if ($_SESSION['UF'] != '')
		$sql .= sprintf(" AND (UF='%s')", $_SESSION['UF']);	

	$sql .= sprintf(" ORDER BY id LIMIT %d", $_SESSION['limite']);

	$query= mysql_query($sql) or die("Erro $sql :" . mysql_error());

	echo "$sql (" . mysql_numrows($query) . ")<br>";

	if (mysql_numrows($query))	{	
		$html= "
		<table id='tableSend' width=100% bgcolor=#ffffff>
			<tr>
				<td class=tfield>ID</td>
				<td class=tfield>Nome</td>
				<td class=tfield>email to</td>
				<td class=tfield>uf</td>
				<td class=tfield>Categoria</td>
				<td class=tfield>ult. envio</td>
			</tr>";
		$total=mysql_num_rows($query);
		$counter=0;
		while ($result = mysql_fetch_object($query)) {
		
			$id	= $result->id;
			$nome	= substr($result->nome,0,20);
			$email= $result->email;
			$mBody = str_replace('#email', $email, $_SESSION['eBody']);
			$shtml = $mBody;		
			$ultEnvio=explode('-',substr($result->dataEnvio, 0, 10));
			$ultEnvio="{$ultEnvio[2]}/{$ultEnvio[1]}/{$ultEnvio[0]}";
			
			$text = strip_tags($shtml);	
			$mail = new htmlMimeMail();
			$mail->setReturnPath('amfemkt@amfar.com.br');
			$mail->setFrom("AMF - Associação Mineira de Farmacêuticos <amfemkt@amfar.com.br>");
			$mail->setReplayTo('amfar@amfar.com.br');
			$mail->setSubject($_SESSION['subject']);
			$mail->sethtml($shtml, $text, 'files/images/');
			$result = $mail->send(array($email));
			$msgMail = $result ? 'Success' : $mail->errors;
	
	
	//		$msgMail='Success';
	
			$color=($msgMail == 'Success') ? 'class="sended"' : 'class="notSended"';			
			if ($msgMail == 'Success') {
				$html .="
				<tr>
					<td $color align=right>$id&nbsp;</td>
					<td $color>&nbsp;$nome</td>
					<td $color>&nbsp;$email</td>
					<td $color>&nbsp;$result->uf</td>
					<td $color align=center>&nbsp;{$_SESSION['categ']} - {$_SESSION['categName']}</td>
					<td $color align=center>&nbsp;$ultEnvio</td>
				</tr>";
					
				mysql_query("UPDATE {$_SESSION['table']} SET dataEnvio=now(), status=1 WHERE id=$id") or die("Erro:" . mysql_error());
				$_SESSION['enviado']++;
				}
							
			$_SESSION['nextID']=$id+1;
			$counter++;
			unset($mail);
			}	// while
			
		$html .="
			<tr>
				<td align=center colspan=8 class=sended>
					<div id=msgOk>Total de e-mails enviados: $counter</div></td>
			</table>
			<meta http-equiv='refresh' content='30; URL=sender.php' />";
			
		}	//	if			
	else
		$html .="
			<div id=msgOk><font size=+2><b>Envio Encerrado !!!<br>
			<img src=images/sended.png><br>
			Total de e-mails enviados: {$_SESSION['enviado']} $totReg</b></div>";
		
	echo $html;
	}
?>
