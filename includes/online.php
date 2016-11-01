<?
############################################################
#                                                          #
# Título               : Online 1.3                        #
# Criado por           : Anderson Silva Pinto              #
# Data da Criação      : 09/04/2002                        #
# Última Atualização   : 28/04/2002			   #
# E-mail	       : anderson@brmasters.net  	   #
# Home Page            : http://www.brmasters.net 	   #
# ICQ   	       : 158657690			   #
#                                                          #
############################################################

############################################################
#                                                          #
#      Esse script foi criado e distribuído livremente por #
# www.brmasters.net,  pode ser modificado de modo 	   #
# que seja respeitado o comentário acima.                  #
#      Caso tenha gostado desse script me mande um e-mail! #
#      Bugs e comentários - suporte@brmasters.net 	   #
#      * Se alterar este script para melhor me avise!	   #
#                                                          #
############################################################

$self = $_SERVER["PHP_SELF"];
$querystring = $_SERVER["QUERY_STRING"];
$useragent = $_SERVER["HTTP_USER_AGENT"];
$remotport = $_SERVER["REMOTE_PORT"];
$referer = $_SERVER["HTTP_REFERER"];
$remotaddr = $_SERVER["REMOTE_ADDR"];

$conn = mysql_connect($host, $userDB, $pwdDB) or die (mysql_error());
mysql_select_db($dataBase);
$timestamp=time();
$timeout=time()-300;
$query= @mysql_query("INSERT INTO online VALUES ('$timestamp','$remotaddr')",$conn);
$query=mysql_query("INSERT INTO log VALUES (now(),'$referer','$remotaddr','$remotport','$useragent','$self','$querystring')",$conn);
$query=mysql_db_query($dataBase, "DELETE FROM online WHERE timestamp<$timeout");

$query=mysql_query("SELECT DISTINCT ip FROM online",$conn);
$usuarios=mysql_numrows($query);

if ($usuarios ==0)
	$usuarios = 1;
	
echo "<font face=Arial,Verdana,Tahoma size=2>• Usuário(s) Online: $usuarios •</font>";

@mysql_free_result($query);
mysql_close();
?>