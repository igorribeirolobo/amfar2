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
 * Este é o arquivo de configuração da aplicação
 */
 ?>
<html>
	<head>
		<meta http-equiv="Content-Language" content="pt-br">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		<title>E-MAILING 1.2</title>
		<style>
			<!--
				*{
					font:9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;
					color:006699}
					
				a {color:#808080}
				a:hover{color:#006699}

				table{
					border:5px double #004f6d;
					padding:0px
					}
				
				table td{
					border-bottom:1px solid #60c659;
					border-right:1px solid #60c659;
					padding:2px}
					
				table .label{text-align:right}
					
				.inputText {
					border:1px solid #004f6d;					
					padding:0 4px;
					width:200px
					}
					
				.subTitle{
					font-weight:bolder;
					font-size:11pt;
					color:#fff;
					background:#004f6d;
					text-align:center;
					border:0
					}

				.erro{
					font-weight:bolder;
					font-size:10pt;
					color:#fff;
					background:#c40000;
					text-align:center;
					border:0
					}
					
				#btSend {cursor:pointer;}
				-->
		</style>
		<script language="javascript">
		
			function checkEmail(objForm) {
				var invalid;
				invalid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

				if (objForm.value == '')	{
					alert("Campo: '"+objForm.name.toUpperCase()+"' email não pode ficar me branco!");
					objForm.focus();
					return false;
					}
				if (invalid.test(objForm.value) == false) {
					objForm.style.color = "red";
					alert("Favor informar corretamente seu e-mail.");
					objForm.focus();
					return (false); }

				return true;
				}


			function isFill(obj) {
				if (obj.value=='') {
					alert("Campo: '"+obj.name.toUpperCase() + "' não pode ficar em branco!");
					obj.focus();
					return false;
					}
				return true;
				}
					
					
			function checkConfig() {
				var oForm=document.config;
				if (!isFill(oForm.empresa)) return false;
				if (!isFill(oForm.site)) return false;
				if (!isFill(oForm.homePage)) return false;
				if (!oForm.dbHost.disabled && !isFill(oForm.dbHost)) return false;
				if (!oForm.dbUser.disabled && !isFill(oForm.dbUser)) return false;
				if (!oForm.dbPwd.disabled && !isFill(oForm.dbPwd)) return false;
				if (!checkEmail(oForm.mailFrom)) return false;
				if (!checkEmail(oForm.retPath)) return false;
				if (!isFill(oForm.admUser)) return false;
				if (!isFill(oForm.admPwd)) return false;
				if (!isFill(oForm.admPwd2)) return false;
				if (oForm.admPwd.value != oForm.admPwd2.value) {
					alert("As duas senhas devem ser iguais!");
					oForm.admPwd.focus();
					return false;
					}
				return true;
				}
		</script>
	</head>
	<body>
	

<?php

############################################################
#                                                          #
#     Esse script foi criado e distribuído livremente por  #
# 		forum.imasters.com.br,  pode ser modificado de modo  #
# 		que seja respeitado o comentário acima.              #
#     Caso tenha gostado desse script me mande um e-mail!  #
#     Bugs e comentários para os emails abaixo:				  #
#		- lab.design@globo.com			     						  #
#      * Se alterar este script para melhor me avise!	     #
#                                                          #
############################################################

/* 	
	Configure as variaveis abaixo corretamente
	A base de dados já deve estar criada no servidor
	As pastas config, files e files/images devem estar liberadas para gravação.
	Se ainda não liberou, execute um chmod 0777 nelas antes de continuar.
*/

/* pre-definições */
$dataBase='mailing';
$_bgTop='images/topoEmail.png';
$_bgBottom='images/rodapeEmail.png';
$_warning="A #_coName preza muito a sua privacidade, caso não tenha interesse em receber nossos informativos, <a href=mailto:#_returnPath?subject=remover:#email>Clique aqui</a>";

$cfg="<?
	// idenficação da empresa
	\$host=;	// host ou ip do mysql\r\n
	\$userDB=;	// login do mysql\r\n
	\$pwdDB=;	// senha do mysql\r\n
	\$dataBase=;	// base de dados\r\n
	\$_vs=1.2;			// versao da aplicação
	\$_data='2007/05/17';	// data da ultima atualização
	";	

if ($_POST['btSend']) {
	
	$host=$_POST['dbHost'];
	$user=$_POST['dbUser'];
	$pwd=$_POST['dbPwd'];
	$dataBase=$_POST['dataBase'];
	
	$conn=mysql_connect($host, $user, $pwd) or die (mysql_error());
	mysql_select_db($dataBase);
	
	$sql= "CREATE TABLE `mailing` (
		`id` int(11) NOT NULL auto_increment,
		`nome` varchar(50) default NULL,
		`uf` char(2) default NULL,
		`email` varchar(100) default NULL,
		`categ` tinyint(2) NOT NULL default '0',
		`dataReg` datetime default '0000-00-00 00:00:00',
		`dataEnvio` datetime default '0000-00-00 00:00:00',
		`arquivo` varchar(100) default NULL,
		`status` tinyint(1) default '0',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `email` (`email`)
		) ENGINE=MyISAM";

	$exec = MYSQL_QUERY($sql, $conn);
	
	$sql= "CREATE TABLE `mailing_categ` (
		`id` tinyint(2) NOT NULL auto_increment,
		`categ` varchar(50) NOT NULL default '',
		`subCat` tinyint(2) NOT NULL default '0',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `categ` (`categ`)
		) ENGINE=MyISAM";
		
	$exec = MYSQL_QUERY($sql, $conn);

	$sql= "CREATE TABLE `mailing_setup` (
		`empresa` varchar(100) NOT NULL default '',
		`urlSite` varchar(100) NOT NULL default '',
		`homePage` varchar(30) NOT NULL default '',
		`mailFrom` varchar(100) NOT NULL default '',
		`returnPath` varchar(100) NOT NULL default '',
		`bgTop` varchar(50) NOT NULL default '',
		`bgBottom` varchar(50) NOT NULL default '',
		`warning` mediumtext NOT NULL
		) ENGINE=MyISAM";
		
	$exec = MYSQL_QUERY($sql, $conn);


	$sql= "CREATE TABLE `mailing_users` (
		`id` int(11) NOT NULL auto_increment,
		`username` varchar(50) default NULL,
		`password` varchar(20) default NULL,
		`status` tinyint(1) default '0',
		PRIMARY KEY  (`id`)		
		) ENGINE=MyISAM";

	$exec = MYSQL_QUERY($sql, $conn);
	
	
	$sql= "CREATE TABLE `mailing_returns` (
		`id` int(11) NOT NULL default '0',
		`dataRetorno` datetime NOT NULL default '0000-00-00 00:00:00',
		`campanha` varchar(255) default NULL,
		KEY `id` (`id`)
		) ENGINE=MyISAM";
		
	$exec = MYSQL_QUERY($sql, $conn);
	
	$query=mysql_query("select * from mailing_users where id=1", $conn);
	if (mysql_numrows($query) > 0)
		$exec =MYSQL_QUERY("update mailing_users set username=upper('{$_POST['admUser']}'),password=lower('{$_POST['admPwd']}'), status=0 where id=1", $conn);
	else		
		$exec =MYSQL_QUERY("insert into mailing_users values(1,upper('{$_POST['admUser']}'),lower('{$_POST['admPwd']}'),0)", $conn);
		
	$query=mysql_query("select * from mailing_setup", $conn);
	if (mysql_numrows($query) > 0)
		$exec =MYSQL_QUERY("update mailing_setup set empresa=upper('{$_POST['empresa']}'),urlSite=lower('{$_POST['site']}'), homePage=lower('{$_POST['homePage']}'), mailFrom='{$_POST['mailFrom']}', returnPath='{$_POST['retPath']}', bgTop='{$_bgTop}', bgBottom='{$_bgBottom}', warning='{$_POST['warning']}'");
	else		
		$exec =MYSQL_QUERY("insert into mailing_setup(empresa, urlSite, homePage, mailFrom, returnPath, bgTop, bgBottom, warning) values(upper('{$_POST['empresa']}'), lower('{$_POST['site']}'), lower('{$_POST['homePage']}'), lower('{$_POST['mailFrom']}'), lower('{$_POST['retPath']}'), '{$_bgTop}', '{$_bgBottom}', '{$_POST['warning']}')") or die("Erro " . mysql_error());



	$cfg=str_replace("host=", "host='$host'", $cfg);
	$cfg=str_replace("userDB=", "userDB='$user'", $cfg);
	$cfg=str_replace("pwdDB=", "pwdDB='$pwd'", $cfg);
	$cfg=str_replace("dataBase=", "dataBase='$dataBase'", $cfg);
	
	$cfg.="
	mysql_connect(\$host, \$userDB, \$pwdDB) or die (mysql_error());
	mysql_select_db($dataBase);

	\$query=mysql_query('select username, password from mailing_users where id=1');
	\$admUser=mysql_result(\$query, 0, 'username');
	\$admPwd=mysql_result(\$query, 0, 'password');

	\$query=mysql_query('select * from mailing_setup');
	\$_coName=mysql_result(\$query, 0, 'empresa');
	\$_coSite=mysql_result(\$query, 0, 'urlSite');
	\$_homePage=mysql_result(\$query, 0, 'homePage');
	\$_mailFrom=mysql_result(\$query, 0, 'mailFrom');
	\$_returnPath=mysql_result(\$query, 0, 'returnPath');
	\$_bgTop=mysql_result(\$query, 0, 'bgTop');
	\$_bgBottom=mysql_result(\$query, 0, 'bgBottom');
	\$_warning=mysql_result(\$query, 0, 'warning');
?>";
	
	$fn=@fopen("config/config.php", "w");
	fputs($fn, $cfg);
	@fclose($fn);	

	include 'config/config.php';
	
	if (!is_writable('images'))
		$msgErr="Por favor mude a permissão da pasta '$_coSite/e-mailing/images'<br>para 0777 no servidor e pressione F5 para atualizar.";

	elseif (!is_writable('files'))
		$msgErr="Por favor mude a permissão da pasta '$_coSite/e-mailing/files'<br>para 0777 no servidor e pressione F5 para atualizar.";

	elseif (!is_writable('files/images'))
		$msgErr="Por favor mude a permissão da pasta '$_coSite/e-mailing/files/images'<br>para 0777 no servidor e pressione F5 para atualizar.";

	?>
				
			<table border="0" cellpadding="2" cellspacing="2" align="center">
			  <tr>
				 <td align=center>
				 <img border="0" src="images/logoTopo.gif"></td>
				 <td class=subTitle colspan=3><span style='font-size:18pt; color:#fff'>e-Mailing 1.2</span><br>:: Configuração ::</td>
			  </tr>
			  <tr>
				 <td class=label>Empresa:</td>
				 <td colspan=3><input type="text" class=inputText id=empresa name="empresa" value="<?=$_coName?>" disabled/></td>
			  </tr>
			  <tr>
				 <td class=label>URL Principal:</td>
				 <td><input type="text" class=inputText id=site name="site" value="<?=$_coSite?>" disabled/></td>
				 <td class=label>Página Inicial:</td>
				 <td><input type="text" class=inputText id=homepage name="homepage" value="<?=$_homePage?>" disabled/></td>
			  </tr>
			  <tr>
				 <td colspan=4 class=subTitle>Configuração do mySQL</td>
			  </tr>
			  <tr>
				 <td class=label>Host ou IP:</td>
				 <td><input type="text" class=inputText id=dbHost name="dbHost" value="<?=$host?>" disabled/></td>
				 <td class=label>Usuário:</td>
				 <td ><input type="text" class=inputText style="width:100px" id=dbUser name="dbUser" value="<?=$userDB?>" disabled/></td>
			  
			  <tr>
				 <td class=label>Database:</td>
				 <td ><input type="text" class=inputText style="width:75px" id=dataBase name="dataBase" value="<?=$dataBase?>"  disabled/></td>
				 <td class=label>Senha:</td>
				 <td ><input type="password" class=inputText style="width:100px" id=dbPwd name="dbPwd" value="<?=$pwdDB?>"  disabled/></td>
			  </tr>
			  <tr>
				 <td colspan=4 class=subTitle>Configuração para Envio</td>
			  </tr>
			  <tr>
				 <td class=label>Mail-From:</td>
				 <td colspan=3><input type="text" class=inputText id=mailFrom name="mailFrom" value="<?=$_mailFrom?>"  disabled/></td>
				<tr>
				 <td class=label>Return-Path:</td>
				 <td colspan=3><input type="text" class=inputText id=retPath name="retPath" value="<?=$_returnPath?>"  disabled/></td>
			  </tr>
			  <tr>
				 <td class=label>Background-Top:</td>
				 <td colspan=3><input type="text" class=inputText id=bgTop name="bgTop" value="<?= $_bgTop ?>" disabled /></td>
				<tr>
			  <tr>
				 <td class=label>Background-Bottom:</td>
				 <td colspan=3><input type="text" class=inputText id=bgBottom name="bgBottom" value="<?= $_bgBottom ?>" disabled /></td>
				<tr>
			  <tr>
			  		<td class=label>Warning:
			  		<td colspan=3>
			  			<textarea id="warning" name="warning" cols="80" rows="4" disabled><?=$_warning?></textarea>
			  <tr>
				 <td colspan=4 class=subTitle>Administrador</td>
			  </tr>
			  <tr>
				 <td class=label>Nome:</td>
				 <td colspan=3><input type="text" class=inputText id=admUser name="admUser" value="<?=$admUser?>"  disabled/></td>
			  </tr>
			  <tr>
				 <td class=label>Senha:</td>
				 <td ><input type="password" class=inputText id=admPwd style="width:75px" name="admPwd" value="<?=$admPwd?>"  disabled/></td>
				 <td class=label>Confirme:</td>
				 <td ><input type="password" class=inputText style="width:75px" id=admPwd2 name="admPwd2" value="<?=$admPwd?>"  disabled/></td>
			  </tr><?
			 if ($msgErr) echo"
			 	<tr><td colspan=4 class=erro>$msgErr</td></tr>";
			 	
			 else echo"			 	
			  <tr>
				 <td colspan=4 align=center bgcolor='#60c659'>
				 	<b style='font-weight:bolder'>P A R A B É N S - Instalação Concluída com sucesso !</b> - <a href='index.php'>Entrar no mailing</a></td>";	?>
			  </tr>
			</table>
		<center>Desenvolvido por LAB Design &copy;2007</center>
	</body>
</html>

<? die();
	}

if (file_exists('config/config.php'))
	include 'config/config.php';

elseif (!is_writable('config'))
	$msgErr="Por favor mude a permissão da pasta 'config'<br>para 0777 no servidor antes de continuar.";
	
elseif (!is_writable('images'))
	$msgErr="Por favor mude a permissão da pasta 'images'<br>para 0777 no servidor antes de continuar.";


elseif (!is_writable('files'))
	$msgErr="Por favor mude a permissão da pasta 'files'<br>para 0777 no servidor antes de continuar.";

elseif (!is_writable('files/images'))
	$msgErr="Por favor mude a permissão da pasta 'files/images'<br>para 0777 no servidor antes de continuar.";

?>
			
		<form method="POST" style="margin:2px" name="config" action="#" onsubmit="return checkConfig()">
			<table border="0" cellpadding="2" cellspacing="2" align="center">
			  <tr>
				 <td align=center>
				 <img border="0" src="images/logoTopo.gif"></td>
				 <td class=subTitle colspan=3><span style='font-size:18pt; color:#fff'>e-Mailing 1.2</span><br>:: Configuração ::</td>
			  </tr>
			  <tr>
				 <td class=label>Empresa:</td>
				 <td colspan=3><input type="text" class=inputText id=empresa name="empresa" value="<?= isset($_coName) ? $_coName : 'Publicidade na Net' ?>" onclick="this.value=''"/></td>
			  </tr>
			  <tr>
				 <td class=label>URL Principal:</td>
				 <td><input type="text" class=inputText id=site name="site" value="<?= isset($_coSite) ? $_coSite : 'http://www.seusite.com.br' ?>" onclick="this.value=''"/></td>
				 <td class=label>Página Inicial</td>
				 <td><input type="text" class=inputText id=homePage style="width:100px" name="homePage" value="<?= isset($_homePage) ? $_homePage : 'index.php' ?>" onclick="this.value=''"/></td>

			  </tr>
			  <tr>
				 <td colspan=4 class=subTitle>Configuração do mySQL</td>
			  </tr>
			  <tr>
				 <td class=label>Host ou IP:</td>
				 <td><input type="text" class=inputText id=dbHost name="dbHost" value="<?= isset($host) ? $host : 'localhost' ?>" onclick="this.value=''"/></td>
				 <td class=label>Usuário:</td>
				 <td ><input type="text" class=inputText style="width:100px" id=dbUser name="dbUser" value="<?=$userDB?>"/></td>
			  
			  <tr>
				 <td class=label>Database:</td>
				 <td ><input type="text" class=inputText style="width:75px" id=dataBase name="dataBase" value="<?= isset($dataBase) ? $dataBase : 'mailing' ?>" onclick="this.value=''"/></td>
				 <td class=label>Senha:</td>
				 <td ><input type="password" class=inputText style="width:100px" id=dbPwd name="dbPwd" value="<?=$pwdDB?>"/></td>
			  </tr>
			  <tr>
				 <td colspan=4 class=subTitle>Configuração para Envio</td>
			  </tr>
			  <tr>
				 <td class=label>Mail-From:</td>
				 <td colspan=3><input type="text" class=inputText id=mailFrom name="mailFrom" value="<?= isset($_mailFrom) ? $_mailFrom : 'webmaster@teste.com.br' ?>" onclick="this.value=''"/></td>
				<tr>
				 <td class=label>Return-Path:</td>
				 <td colspan=3><input type="text" class=inputText id=retPath name="retPath" value="<?= isset($_returnPath) ? $_returnPath : 'webmaster@teste.com.br' ?>" onclick="this.value=''" /></td>
			  </tr>
			  <tr>
				 <td class=label>Background-Top:</td>
				 <td colspan=3><input type="text" class=inputText id=bgTop name="bgTop" value="<?= $_bgTop ?>" disabled /></td>
				<tr>
			  <tr>
				 <td class=label>Background-Bottom:</td>
				 <td colspan=3><input type="text" class=inputText id=bgBottom name="bgBottom" value="<?= $_bgBottom ?>" disabled /></td>
				<tr>
			  <tr>
			  		<td class=label>Warning:
			  		<td colspan=3>
			  			<textarea id="warning" name="warning" cols="80" rows="4"><?=$_warning?></textarea>
			  <tr>
				 <td colspan=4 class=subTitle>Administrador</td>
			  </tr>
			  <tr>
				 <td class=label>Nome:</td>
				 <td colspan=3><input type="text" class=inputText id=admUser name="admUser" value="<?=$admUser?>"/></td>
			  </tr>
			  <tr>
				 <td class=label>Senha:</td>
				 <td ><input type="password" class=inputText id=admPwd style="width:75px" name="admPwd" value="<?=$admPwd?>"/></td>
				 <td class=label>Confirme:</td>
				 <td ><input type="password" class=inputText style="width:75px" id=admPwd2 name="admPwd2" value="<?=$admPwd?>"/></td>
			  </tr><?
			 if ($msgErr) echo"
			 	<tr><td colspan=4 class=erro>$msgErr</td></tr>"; ?>
			  <tr>
				 <td colspan=4 align=center>
				 	<input type="submit" name="btSend" id="btSend" value="Salvar Configurações" 
				 		<? if ($msgErr) echo ' disabled'; ?>/></td>
			  </tr>
			</table>
		</form>		
		<center>Desenvolvido por LAB Design &copy;2007</center>
	</body>
</html>