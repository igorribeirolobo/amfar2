<script>
	$(document).ready(function(){
		$('#usrLogin').click(function(){
			if($('#senha').val()==''){
				alert('Digite sua senha!!!');
				$('#senha').focus();
				return false;
				}
			return true;
			})
		})
</script>

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

session_start();
include 'config/config.php';

//$_SESSION['usrAdm']='britto';
/*
echo "<pre>";
print_r($_POST);
echo"</pre>";
*/
//echo "<pre>";
//print_r($_SESSION);
//echo"</pre>";
//$_SESSION['usrAdm']=$admUser;
if ($_POST['usrLogin']) {
	if(!strtolower($_POST['senha'])=='britto') {
		echo " Senha n&atilde;o confere !!!";
		exit;
		}
	
	$admUser="webmaster";
	$admPwd="britto";
	$_SESSION['usrAdm']=$admUser;
	}
	
if ($_SESSION['usrAdm']) { ?>
	<table id='settings' border='0' cellpadding='2' width='100%'>
		<tr>
			<td colspan='4' align='center' height=80><i>
				<b><font size='2' color='#60c659'>
				Bem Vindo ao e-Mailing System<br><font size=+1><?=$_SESSION['usrAdm']?></font><br>Selecione uma das op&ccedil;&otilde;es abaixo:</font></b></i></td>
		</tr>
		<tr>
			<td id='lnkHome'>
				<a href='mgrCateg.php'>
				<img border='0' src='images/categ.png' alt='Gerenciador de Categorias'></a><br />
				Gerenciador<br>de Categorias
			</td>			
			<td id='lnkHome'>
				<a href='mgrFiles.php'>
				<img border='0' src='images/upload.png' alt='Gerenciador de Arquivos'></a><br />
				Gerenciador<br>de Arquivos
			</td>			
			<td id='lnkHome'>
				<a href='mgrList.php'>
				<img border='0' src='images/listing.png' alt='Gerenciador de E-mails'></a><br />
				Gerenciador<br>de E-mails
			</td>			
			<td id='lnkHome'>
				<a href='sndMail.php'>
				<img border='0' src='images/sender.png' alt='Enviar NewsLetter'></a><br />
				Gerenciador<br>de envio		
			<td>
		</tr>
	</table>

<?	}

else { ?>

	<form name='usrLog' id='usrLog' method='POST' action="">
		<table cellspacing='2' cellpadding=2 align='center' width='50%' bgcolor=#efefef>
			<tr>
				<td id='titulo'>:: Login de Usu&aacute;rio</td></tr>
			<tr>
				<td align='center'>
					<p style='background:#efefef'><b>Caro usu&aacute;rio, digite sua senha<br>para entrar no e-Mailing !!!</b></p>
				</td></tr>
			<tr>
				<td align='center' height=30><b>Senha:&nbsp;
					<input type='password' class='text' name='senha' id='senha' style='background:#efefef;text-align:center' size='20'/></td>
			</tr>
					
			<tr>
				<td align='center' height=30>
					<input type='submit' name='usrLogin' id='usrLogin' class='button' value='Entrar'/></td>
			</tr>
			<tr>
				<td><div id='msgErr'></div>
		</table>
	</form>
<?	} ?>
