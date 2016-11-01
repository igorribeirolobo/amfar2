<?php

// -- O caminho do banco de dados 200.234.200.109

if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102') {

	$urlSite ="http://10.1.1.110/amfar";
	$host = "LAURO";
	// cria a conexao com o sqlser 2005
	$conn=mssql_connect("LAURO", "amfar", "eofilho172298");
	mssql_select_db("amfar");
	}

else {
	$urlSite ="http://www.amfar.com.br";
	$host = "200.234.197.42";
	// cria a conexao com o sqlser 2005
	$conn=mssql_connect("200.234.197.42", "amfar_1", "eofilho172298");
	mssql_select_db("amfar_1");
	}
   


$webmaster = "webmaster@amfar.com.br";
$eContato = "contato@amfar.com.br";
$eCadastro = "cadastro@amfar.com.br";
$eMatricula = "cursos@amfar.com.br";
$eBoletos = "financeiro@amfar.com.br";
$eCurriculo = "curriculos@amfar.com.br";
$eEmpresas = "empresas@amfar.com.br";
$pwdAdmin ="admAmfar";	

$dataBase = "amfar";
$userDB = "amfar";
$pwdDB = "eofilho172298";
	
$urlEnq="$urlSite/enquete";


$dataFilter=103;
$offsetBoleto = 4;
$taxaBoleto = 0;
$admFin=3;
$taxaAssociado=100;
$multaBoleto='2%';
$jurosBoleto='0,33% ao dia';
$contaCursos=41102001;
$contaSocios=41101001;

$arrayFormaPgto =array('','DINHEIRO','CHEQUE','BOLETO','CARTÃO');

$arrayBancos=array();
$arrayBancos[1]='BRASIL';
$arrayBancos[104]='CEF';
$arrayBancos[141]='ITAÚ';
$arrayBancos[237]='BRADESCO';
$arrayBancos[409]='UNIBANCO';
?>
