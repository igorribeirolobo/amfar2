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
	 * Script para gravar o retorno do mailing
 */
 
include_once "config/config.php";

// cria a conexao com a base de dados
$conn = mysql_connect($host,$userDB,$pwdDB) or die("Falha de Conexão - Por favor tente mais tarde !");
mysql_select_db($dataBase) or die("Falha na Base de Dados - Por favor, informe ao <a href=mailto:$webmaster>webmaster</a> !");

$query=mysql_query("select * from mailing where id='{$_GET['id']}'");
$rsQuery=mysql_fetch_array($query);
$sql="insert into mailing_returns values({$rsQuery['id']},  now(), '{$rsQuery['arquivo']}')";
mysql_query($sql) or die("Erro $sql" . mysql_error());

header("location:$_coSite/$_homePage");
?>