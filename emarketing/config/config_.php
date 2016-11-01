<?
	// idenficação da empresa
	$host='mysql.sbrafh.org.br';	// host ou ip do mysql

	$userDB='sbrafh';	// login do mysql

	$pwdDB='sbf20net05';	// senha do mysql

	$dataBase='sbrafh';	// base de dados

	$_vs=1.2;			// versao da aplicação
	$_data='2007/05/17';	// data da ultima atualização
	
	mysql_connect($host, $userDB, $pwdDB) or die (mysql_error());
	mysql_select_db($dataBase);

	$query=mysql_query('select username, password from mailing_users where id=1') or die("Erro " . mysql_error());
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
