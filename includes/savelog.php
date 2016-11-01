<?
############################################################
#                                                          #
# Título               : Online 1.0                        #
# Criado por           : Lauro Assis Lima de Brito         #
# Data da Criação      : 18/09/2006                        #
# E-mail	       		  : lab.design@globo.com			     #
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

$self 		= $_SERVER["PHP_SELF"];
$querystring= $_SERVER["QUERY_STRING"];
$referer		= $_SERVER["HTTP_REFERER"];
$remotaddr	= $_SERVER["REMOTE_ADDR"];
$sql "
	insert into sbrafh_log(data, referer, ip, user, link, secao, parceiro, querystring) 
						values (now(),'$referer','$remotaddr','{$_SESSION['userName']}','$link','$secao','$parceiro','$querystring')",$conn);
$query=mysql_query($sql, $conn);
?>