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
	 * Login do administrador
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');

header("Content-type: text/html; charset=iso-8859-1");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include 'config/config.php';

if ($_POST['senha']) {
	
	$conn=mysql_connect($host, $userDB, $pwdDB) or die (mysql_error());
	mysql_select_db($dataBase);
	
	$query=mysql_query("select * from mailing_users where password='{$_POST['senha']}'");
	if(mysql_numrows($query)==0) {
		echo " Senha n&atilde;o confere !!!";
		exit;
		}
	
	$admUser=mysql_result($query, 0, 'username');
	$admPwd=mysql_result($query, 0, 'password');
	$_SESSION['usrAdm']=$admUser;
	}
	
if ($_SESSION['usrAdm']) {
	$html="
	<table border='0' cellpadding='2' width='100%' />
		<tr>
			<td colspan='11' align='center' height=80><i>
				<b><font size='2' color='#60c659'>
				Bem Vindo ao e-Mailing System<br><font size=+1>{$_SESSION['usrAdm']}</font><br>Selecione uma das op&ccedil;&otilde;es abaixo:</font></b></i></td>
		</tr>
	 <tr>
		<td align='center' width='20'>
		<td id='lnkHome'>
			<a href='mgrCateg.php'>
				<img border='0' src='images/categ.png' alt='Gerenciador de Categorias'></a>
					Gerenciador<br>de Categorias

		<td align='center' width='20'>
		<td id='lnkHome'>
			<a href='mgrFiles.php'>
				<img border='0' src='images/upload.png' alt='Gerenciador de Arquivos'></a>
					Gerenciador<br>de Arquivos

		<td align='center' width='20'>
		<td id='lnkHome'>
			<a href='mgrList.php'>
				<img border='0' src='images/listing.png' alt='Gerenciador de E-mails'></a>
					Gerenciador<br>de E-mails

		<td align='center' width='20'>
		<td id='lnkHome'>
			<a href='newsletter.php'>
				<img border='0' src='images/editor.png' alt='Criar NewsLetter'></a>
					Editor para criação

		<td align='center' width='20'>
		<td id='lnkHome'>
			<a href='sndMail.php'>
				<img border='0' src='images/sender.png' alt='Enviar NewsLetter'></a>
					Gerenciador<br>de envio
		<td>
	 </tr>
	</table>";
	
	echo $html;
	exit;
	}

$html="
	<form action='#' method='POST' id='usrLog' name='usrLog' enctype='application/x-www-form-urlencoded' />
		<table cellspacing='2' cellpadding=2 align='center' width='50%' bgcolor=#efefef>
			<tr>
				<td id='titulo'>:: Login de Usu&aacute;rio</td></tr>
			<tr>
				<td align='center'>
					<p style='background:#efefef'><b>Caro usu&aacute;rio, digite sua senha<br>para entrar no e-Mailing !!!</b></p>
				</td></tr>
			<tr>
				<td align='center' height=30><b>Senha:&nbsp;
					<input type='password' class='tInput' id='senha' style='background:#efefef;text-align:center' size='20' name='senha' onclick=this.value=''></td></tr>
					
			<tr>
				<td align='center' height=30>
					<input type='button' name='usrLogin' class='but' value='Entrar' onclick='sendLogin()'></td></tr>
			<tr>
				<td><div id='msgErr'></div>
		</table>
	</form>";
	
echo $html;
?>