<?
	// idenfica��o da empresa
	$host='mysql01.amfar.com.br';	// host ou ip do mysql

	$userDB='amfar';	// login do mysql

	$pwdDB='amf10web20';	// senha do mysql

	$dataBase='amfar';	// base de dados

	$_vs=1.2;			// versao da aplica��o
	$_data='14/10/2010';	// data da ultima atualiza��o
	
	mysql_connect($host, $userDB, $pwdDB) or die (mysql_error());
	mysql_select_db($dataBase);
?>
