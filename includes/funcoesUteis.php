<?
$meses=array('','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

function debug($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
	}
	
	
function insertMailing($nome, $uf, $email){
	$nome=trim($nome);
	$uf=trim($uf);
	$email=trim($email);
	$host='mysql01.amfar.com.br';	// host ou ip do mysql
	$userDB='amfar';	// login do mysql
	$pwdDB='amf10web20';	// senha do mysql
	$dataBase='amfar';	// base de dados
	mysql_connect($host, $userDB, $pwdDB);
	mysql_select_db($dataBase);
	@mysql_query("INSERT INTO mailing SET nome='$nome', uf='$uf', email='$email', dataReg=NOW()");	
	}

function formatCPF($strNumber)	{
	$strNumber = ereg_replace("[' '-./ \t]",'',$strNumber);
	if (strlen($strNumber)==11)	{	// o numero e CPF
		$temp = substr($strNumber,0,3) . '.';
		$temp .= substr($strNumber,3,3) . '.';
		$temp .= substr($strNumber,6,3) . '-';
		$temp .= substr($strNumber,-2);
		}
	
	else	{
	
		$temp = substr($strNumber,0,2) . '.';
		$temp .= substr($strNumber,2,3) . '.';
		$temp .= substr($strNumber,5,3) . '/';
		$temp .= substr($strNumber,8,4) . '-';
		$temp .= substr($strNumber,-2);
		}
		
	return($temp);
}	// end function

function chConvert($string) {
	$uSpecialChars = array('‡','Æ','£');
	$lSpecialChars = array('ç','ã','ú');
	$nString = str_replace($uSpecialChars, $lSpecialChars, $string);
	return $nString;
	}


function ufWords($string) {

	$uSpecialChars = array('Á','Ã','À','É','Ê','Í','Ç','Ó','Ô','Õ','Ú',' De ',' A ', ' E ', 'R$');
	$lSpecialChars = array('á','ã','à','é','ê','í','ç','ó','ô','õ','ú',' de ',' a ', ' e ', 'R$');

	$nString = ucwords(mb_strtolower($string));
	$nString = str_replace($uSpecialChars, $lSpecialChars, $nString);
	return $nString;
	}

function ufString($string) {	
	$uSpecialChars = array('Á','Ã','À','É','Ê','Í','Ç','Ó','Ô','Õ','Ú',' De ',' A ', ' E ', 'R$');
	$lSpecialChars = array('á','ã','à','é','ê','í','ç','ó','ô','õ','ú',' de ',' a ', ' e ', 'R$');

	$nString = strtolower($string);	
	$nString = str_replace($uSpecialChars, $lSpecialChars, $nString);
	$nString = ucFirst($nString);
	return $nString;
	}

function formatVal($val) {
	$temp=number_format($val, 2,',', '.');
	return("R$ $temp");
	}

function formatDec($val) {
	$temp=number_format($val, 2,',', '.');
	return($temp);
	}

function formatPeso($peso) {
	$temp=number_format($peso, 3,',', '.');
	return("$temp");
	}
	
	
/*
	****************************************
	funcao para detectar possiveis ataques
	de sql injection atraves de formularios
	
	Todo formulário deve ser direcionado
	pára esta função antes de qualquer
	acesso à base de dados.
	
	esta função deve ser colocada num
	arquivo de include que contenha os
	dados para acesso ao mySQL.
	
	Obs.: Não se deve usar como values
	para qualquer campo, palavras reservadas
	do sql tipo:
		<input type="sumbmit" name="insert">
	ou
		<input type="sumbmit" name="update">
	a verificação é feita através do stristr
	que verifica toda a string e portanto
	um valor bt_insert retorna verdadeiro
	da função.
	
	versão do código 1.00 - 27/01/2006
	criado por Lauro A L Brito
	****************************************
*/

function value_ok($var)	{
	require 'global.php';
	$conn2 = mysql_connect($host,$userDB, $pwdDB) or die("Erro de conexao");
	mysql_select_db($dataBase) or die("Erro de seleção $dataBase");

	$query=mysql_query("SELECT * FROM sql_injection",$conn2);
	while ($res=mysql_fetch_assoc($query))	{
	
		$badchars=$res['seq'];	
		if (stristr($var, $badchars))	{			
			$remotaddr = $_SERVER["REMOTE_ADDR"];
			$self = $_SERVER["PHP_SELF"];
			$querystring = $_SERVER["QUERY_STRING"];
			$useragent = $_SERVER["HTTP_USER_AGENT"];
			$remotport = $_SERVER["REMOTE_PORT"];
			$referer = $_SERVER["HTTP_REFERER"];
			
			$badchars=addslashes($badchars);
			$badchars=quotemeta($badchars);
			$badchars=htmlspecialchars($badchars);
			
			$sql="insert into sql_injection_log values(now(),'$remotaddr','$self','$querystring','$useragent','$referer','$badchars')";
			mysql_query($sql) or die("Erro 70 - SQL_INJECTION: $sql" . mysql_error());

	    	return false;
	    	}	    	
		}	// while

	return true;		
	}
	

function checkForm()	{	
	foreach ($_POST as $field_name => $value)	{	
		if (!value_ok($value))
			return false;			
		} // end foreach
		
	//retorna verdadeiro significando que os dados estão ok;
	return true;	
	}	// checkForm()


/* 

*************** checagem de form *************** */

function checkEmail($email) {
	$email = trim($email);
	if ((ereg("^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$", $email)) && (!empty($email)))
		return true;
	return false;
	} 	// function

function checkCEP($chk_cep) {
	$var = trim($chk_cep);
	if(ereg("^([0-9]{5})([ ,-]{1})([0-9]{3})$", $var)) return true;
	elseif(ereg("^([0-9]{5})([0-9]{3})$", $var)) return true;
	return false;
	}

function checkFone($tel) {
	$var = trim($tel);
	if(ereg("^(\(0?[0-9]{2}\))?([])?([0-9]?[0-9]{3})([-]{1})([0-9]{4})$", $var)) return true;
	return false;
	}

function checkNumeros($num) {
	$var = trim($num);
	if(ereg("^[0-9]*$", $var)) return true;
	return false;
	}

function checkTexto($texto) {
	$var = trim($texto);
	if(ereg("^([A-Za-zÀ-ú,.;'\}\{\(\)])*$", $var)) return true;
	return false;
	}

function checkData($data) {
	if (!ereg ("([0-9]{2})/([0-9]{2})/([0-9]{4})", $data))
		return false;
	else {
		$d=parseInt(substr($data,0,2));
		$m=parseInt(substr($data,3,2));
		$y=parseInt(substr($data,6,4));
		$maxDay=31;

		if ($m==4 || $m==6 || $m==9 || $m==11)
			$maxDay=30;
		elseif ($m==2) {
			$maxDay=28;
			if (($y/4)==intval($y/4))
				$maxDay=29;
			}

		if ($m > 0 && $m < 13 && $d > 0 && $d <= $maxDay) return true;
		return false;
		}
	}


/*
    valorPorExtenso - ? :)
    Copyright (C) 2000 andre camargo

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

    Andr&eacute;) Ribeiro Camargo (acamargo@atlas.ucpel.tche.br)
    Rua Silveira Martins, 592/102
    Canguçu-RS-Brasil
    CEP 96.600-000
*/

// funcao............: valorPorExtenso
// ---------------------------------------------------------------------------
// desenvolvido por..: andré camargo
// versoes...........: 0.1 19:00 14/02/2000
//                     1.0 12:06 16/02/2000
// descricao.........: esta função recebe um valor numérico e retorna uma 
//                     string contendo o valor de entrada por extenso
// parametros entrada: $valor (formato que a função number_format entenda :)
// parametros saída..: string com $valor por extenso

function valorPorExtenso($valor=0) {
	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
"quatrilhões");

	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
"quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
"sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
"dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
"sete", "oito", "nove");

	$z=0;

	$valor = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);
	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
	
		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		if ($valor == "000")$z++; elseif ($z > 0) $z--;
		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 
		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? " e " : ", ") : " ") . $r;
	}

	return($rt ? $rt : "zero");
}



function exParcelas($i=1) {
	$p[1] = "Uma Parcela";
	$p[2] = "Duas Parcelas";
	$p[3] = "Tres Parcelas";
	$p[4] = "Quatro Parcelas";
	$p[5] = "Cinco Parcelas";
	$p[6] = "Seis Parcelas";
	$p[7] = "Sete Parcelas";
	$p[8] = "Oito Parcelas";
	$p[9] = "Nove Parcelas";
	$p[10] = "Dez Parcelas";
	$p[11] = "Onze Parcelas";
	$p[12] = "Doze Parcelas";
	$p[13] = "Treze Parcelas";
	$p[14] = "Quatorze Parcelas";
	$p[15] = "Quinze Parcelas";
	$p[16] = "Dezesseis Parcelas";
	$p[17] = "Dezessete Parcelas";
	$p[18] = "Dezoito Parcelas";
	$p[19] = "Dezenove Parcelas";
	$p[20] = "Vinte Parcelas";
	return($p[(int)$i]);
}



// modulo 11 para validação de CPF e CNPJ
function checkCNPJF($source,$f) {	
/* $f=formato de saída
	0 = sem formatação, retira '.','/','-' volta so digitos
	2 = com formatação: 99.999.999/9999-99 ou 999.999.999-99
*/
	$s=ereg_replace("[' '-./ \t]",'',$source);
	$len=strlen($s)-2;
	if ($len != 9 && $len != 12) 
		return false;
	
	$num= substr($s,0,$len);							// pega so a parte do numero sem o dv
	$dv = substr($s,-2);									// pega somente o dv
	
	$d1 = 0;													// verifica o primeiro dv
	for ($i = 0; $i < $len; $i++) {
		if ($len==11) 
			$d1 += $num[11 - $i] * (2 + ($i % 8));	// expressão para cnpj
		else
			$d1 += $num[$i] * (10 - $i);				//	expressão para cpf
		}
		
	if ($d1==0)
		return false;
		
		
	$d1 = 11 - ($d1 % 11);
	if ($d1 > 9) $d1 = 0;	
	if ($dv[0] != $d1)
		return false;
				
	$d1 *= 2;												// verifica o segundo dv
	for ($i = 0; $i < $len; $i++) {
		if ($len==11)
			$d1 += $num[11 - $i] * (2 + (($i + 1) % 8));		// expressão para cnpj
		else
			$d1 += $num[$i] * (11 - $i);							//	expressão para cpf
		}
		
	$d1 = 11 - ($d1 % 11);
	if ($d1 > 9) $d1 = 0;
	if ($dv[1] != $d1)
		return false;
	
	if ($f==0) return($s);	// retorna o numero limpo sem '.', '-', '/'
	
	// retorna cpf formatado
	$formato=($len==9) ? '###.###.###-##' : '##.###.###/####-##';
	$indice=-1;
	for ($x=0; $x < strlen($formato); $x++) {
		if ($formato[$x]=='#')
			$formato[$x] =$s[++$indice];
		}
	return($formato);
	}	//



$formasPgto = array();
$formasPgto[0]='';
$formasPgto[1]='VISA';
$formasPgto[2]='AMEX';
$formasPgto[3]='MASTERCARD';
$formasPgto[4]='DINERS';
$formasPgto[5]='';
$formasPgto[6]='';
$formasPgto[7]='VISAELECTRON';
$formasPgto[8]='BOLETO';
$formasPgto[9]='DEPÓSITO';
$formasPgto[10]='FINBRADESCO';

$tipoPgto = array();
$tipoPgto[0]='';
$tipoPgto[1]='Cartão de Crédito VISA';
$tipoPgto[4]='Cartão de Crédito AMERICAN EXPRESS';
$tipoPgto[2]='Cartão de Crédito MASTERCARD';
$tipoPgto[4]='Cartão de Crédito DINERS CLUB';
$tipoPgto[5]='';
$tipoPgto[6]='';
$tipoPgto[7]='Cartão de Débito VISAELECTRON';
$tipoPgto[8]='BOLETO BANCÁRIO';
$tipoPgto[9]='DEPÓSITO EM C/C';
$tipoPgto[10]='FINANCIAMENTO BANCÁRIO';
?>
