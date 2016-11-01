<?
	// idenficaчуo da empresa
	$host='localhost';	// host ou ip do mysql

	$userDB='root';	// login do mysql

	$pwdDB='britto';	// senha do mysql

	$dataBase='mailing';	// base de dados

	$_vs=1.2;			// versao da aplicaчуo
	$_data='2007/05/17';	// data da ultima atualizaчуo
	
	mysql_connect($host, $userDB, $pwdDB) or die (mysql_error());
	mysql_select_db(mailing);

	$query=mysql_query('select username, password from mailing_users where id=1');
	$admUser=mysql_result($query, 0, 'username');
	$admPwd=mysql_result($query, 0, 'password');

	$query=mysql_query('select * from mailing_setup');
	$_coName=mysql_result($query, 0, 'empresa');
	$_coSite=mysql_result($query, 0, 'urlSite');
	$_homePage=mysql_result($query, 0, 'homePage');
	$_mailFrom=mysql_result($query, 0, 'mailFrom');
	$_returnPath=mysql_result($query, 0, 'returnPath');
	$_bgTop=mysql_result($query, 0, 'bgTop');
	$_bgBottom=mysql_result($query, 0, 'bgBottom');
	$_warning=mysql_result($query, 0, 'warning');
?>