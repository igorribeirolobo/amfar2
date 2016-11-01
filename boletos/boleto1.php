<?
// DADOS DO BOLETO PARA O SEU CLIENTE
include '../global.php';
include '../includes/funcoesUteis.php';


$id = $_GET['id'];	// id do financeiro
if (!$id) die("Erro: N�o foi passado o id do boleto para impress�o");

//debug($_GET);
$query=mssql_query("select 
	c.idCadastro, c.nome, c.cpf, c.ender, c.num, c.bairro, c.cep, c.cidade, c.uf, c.email,
	f.numDoc, f.historico, 
	convert(char(10), f.vcto, 103) as vcto, 
	convert(char(10), f.pgto, 103) as pgto,
	f.valor 
	from cadastro c, financeiro f
	where (f.idFinanceiro=$id and c.idCadastro=f.idCadastro)");


$rsBoleto=mssql_fetch_object($query);
//debug($rsBoleto);
$sacado= $rsBoleto->nome;
$ender1= "$rsBoleto->ender, $rsBoleto->num $rsBoleto->bairro";
$ender2= "$rsBoleto->cep - $rsBoleto->cidade/$rsBoleto->uf";
$cpf	 = $rsBoleto->cpf;
$email = $rsBoleto->email;

$v=explode('/', $rsBoleto->vcto);
$dataLimite=date("Y.m.d", mktime(0,0,0,$v[1],$v[0]+30,$v[2]));

//debug($rsBoleto);
//echo "pgto em:$rsBoleto->pgto";

if ($rsBoleto->pgto != '01/01/1900')
	die("Este boleto j� est� pago e n�o pode mais ser impresso!<br>
		Entre em contato com nosso <a href='mailto:$eContato'>Setor de Cobran�as</a>");


if (date('Y.m.d') > $dataLimite) {
	//debug($rsBoleto);
	die("Este boleto j� passou da data de validade e n�o poder ser impresso!<br>
		Entre em contato com nosso <a href='mailto:$eContato'>Setor de Cobran�as</a> para solicitar novo vencimento");
	}	

$dataLimite=date("d/m/Y", mktime(0,0,0,$v[1],$v[0]+30,$v[2]));

$convBoleto='CEF';
//echo "convBoleto=$convBoleto"; 
/*	*** variaveis para uso com boletos
	*** DADOS DO CEDENTE	***			*/

/*
	echo "convBoleto=$convBoleto";

	$dadosboleto["agencia"] = "0083"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "026522"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "8"; 	// Digito do Num da conta
	$dadosboleto["conta_cedente"] = "87000000414"; // ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente_dv"] = "3"; // Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] = "SR";  // C�digo da Carteira
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";
	$dadosboleto["inicio_nosso_numero"] = "80";  // Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)
	$funcoesBanco="funcoes_cef.php"; 
	$layoutBanco="layout_cef.php";
*/


//$taxa_boleto= 2.9;
$taxa_boleto= 0;
$dadosboleto["identificacao"]	= "ASSOCIA��O MINEIRA DE FARMAC�UTICOS - AMF";
$dadosboleto["endereco"] 		= "AVENIDA DO CONTORNO, 9215 - SALAS 501/502";
$dadosboleto["cidade_uf"]		= "CEP 30110-130  - Belo Horizonte/MG";
$dadosboleto["cpf_cnpj"] 		= "17.431.743/0001-19";
$dadosboleto["telefone"]		= "(31) 3291-6242";
$dadosboleto["cedente"]			= "ASSOCIA��O MINEIRA DE FARMAC�UTICOS - AMF";



if ($convBoleto=='BRADESCO') {
	// DADOS DA SUA CONTA  - Bradesco
	$dadosboleto["agencia"] = "1172"; // Num da agencia, sem digito
	$dadosboleto["agencia_dv"] = "0"; // Digito do Num da agencia
	$dadosboleto["conta"] = "0403005"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta
	$dadosboleto["conta_cedente"] = "0403005"; // ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente_dv"] = "2"; // Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] = "06";  // C�digo da Carteira
	$dadosboleto["quantidade"] = "001";
	$dadosboleto["especie_doc"] = "DS";
	$dadosboleto["aceite"] = "";	
	$funcoesBanco="funcoes_bradesco.php"; 
	$layoutBanco="layout_bradesco.php";
	}
	
	

elseif ($convBoleto=='BANCO DO BRASIL') {
	// DADOS DA SUA CONTA - BANCO DO BRASIL

	$dadosboleto["agencia"] = "0813"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "37714"; 	// Num da conta, sem digito

	// DADOS PERSONALIZADOS - BANCO DO BRASIL
	$dadosboleto["convenio"] = "7777777";  // Num do conv�nio - REGRA: 6 ou 7 d�gitos
	$dadosboleto["contrato"] = "999999"; // Num do seu contrato
	$dadosboleto["carteira"] = "18";  // C�digo da Carteira 18 - 17 ou 11
	$dadosboleto["variacao_carteira"] = "-019";  // Varia��o da Carteira, com tra�o (opcional)
	$dadosboleto["formatacao_convenio"] = "7"; // REGRA: Informe 7 se for Conv�nio com 7 d�gitos ou 6 se for Conv�nio com 6 d�gitos
	$dadosboleto["formatacao_nosso_numero"] = "1"; // REGRA: Se for Conv�nio com 6 d�gitos, informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos
	$dadosboleto["quantidade"] = "001";
	$dadosboleto["aceite"] = "N";
	$dadosboleto["especie_doc"] = "DM";

	/*
	#################################################
	DESENVOLVIDO PARA CARTEIRA 18
	- Carteira 18 com Convenio de 7 digitos
	  Nosso n�mero: pode ser at� 10 d�gitos
	- Carteira 18 com Convenio de 6 digitos
	  Nosso n�mero:
	  de 1 a 99999 para op��o de at� 5 d�gitos
	  de 1 a 99999999999999999 para op��o de at� 17 d�gitos
	#################################################
	*/
	$funcoesBanco="funcoes_bb.php"; 
	$layoutBanco="layout_bb.php";
	}
	
	
	
elseif ($convBoleto=='ITAU') {
// DADOS DA SUA CONTA - BANCO ITAU
	$dadosboleto["agencia"] = "1565"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "13877";	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "4"; 	// Digito do Num da conta
	$dadosboleto["carteira"] = "175";  // C�digo da Carteira
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "";
	$dadosboleto["especie_doc"] = "";
	$funcoesBanco="funcoes_itau.php"; 
	$layoutBanco="layout_itau.php";
	}
	
	
	
elseif ($convBoleto=='UNIBANCO') {
	// DADOS DA SUA CONTA - UNIBANCO
	$dadosboleto["agencia"] = "1017"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "100618"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "9"; 	// Digito do Num da conta
	$dadosboleto["codigo_cliente"] = "2031671"; // Codigo do Cliente
	$dadosboleto["carteira"] = "20";  // C�digo da Carteira
	$dadosboleto["aceite"] = "";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "DM";
	$funcoesBanco="funcoes_unibanco.php"; 
	$layoutBanco="layout_unibanco.php";
	}
	
	
	
elseif ($convBoleto=='HSBC') {
	$dadosboleto["codigo_cedente"] = "1122334"; // C�digo do Cedente (Somente 7 digitos)
	$dadosboleto["carteira"] = "CNR";  // C�digo da Carteira
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";
	$funcoesBanco="funcoes_hsbc.php"; 
	$layoutBanco="layout_hsbc.php";
	}
	
	
	
elseif ($convBoleto=='CEF') {
// DADOS DA SUA CONTA - CEF
	$dadosboleto["agencia"] = "0083"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "026522"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "8"; 	// Digito do Num da conta
//	$dadosboleto["conta_cedente"] = "87000000414"; // ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente"] = "026522"; // ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente_dv"] = "8"; // Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] = "1";  // C�digo da Carteira
	$dadosboleto["quantidade"] = "01";
	$dadosboleto["aceite"] = "N";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "DM";
	$dadosboleto["inicio_nosso_numero"]="90";  // Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)
	$funcoesBanco="funcoes_cef.php"; 
	$layoutBanco="layout_cef.php";
	}




elseif ($convBoleto=='REAL') {
// DADOS DA SUA CONTA - REAL
	$dadosboleto["agencia"] = "1234"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "0012345"; 	// Num da conta, sem digitote do Cliente
	$dadosboleto["carteira"] = "75";  // C�digo da Carteira
	
	$funcoesBanco="funcoes_real.php"; 
	$layoutBanco="layout_real.php";
	}
	


elseif ($convBoleto=='BANESPA') {
// DADOS DA SUA CONTA - BANESPA
	$dadosboleto["codigo_cliente"] = "0707077"; // C�digo do Cliente (PSK) (Somente 7 digitos)
	$dadosboleto["ponto_venda"] = "1333"; // Ponto de Venda = Agencia
	$dadosboleto["carteira"] = "102";  // Cobran�a Simples - SEM Registro
	$dadosboleto["carteira_descricao"] = "COBRAN�A SIMPLES - CSR";  // Descri��o da Carteira
	
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";
	$dadosboleto["inicio_nosso_numero"] = "80";  // Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)

	$funcoesBanco="funcoes_santander_banespa.php"; 
	$layoutBanco="layout_santander_banespa.php";
	}



elseif ($convBoleto=='SUDAMERIS') {
// DADOS DA SUA CONTA - SUDAMERIS
	$dadosboleto["agencia"] = "0501";		// N�mero da agencia, sem digito
	$dadosboleto["conta"] = "6703255";	// N�mero da conta, sem digito
	$dadosboleto["carteira"] = "57";		// Deve possuir conv�nio - Carteira 57 (Sem Registro) ou 20 (Com Registro)

	
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "";

// Esp�cie do Titulo
/*
	DM	Duplicata Mercantil
	DMI	Duplicata Mercantil p/ Indica��o
	DS	Duplicata de Servi�o
	DSI	Duplicata de Servi�o p/ Indica��o
	DR	Duplicata Rural
	LC	Letra de C�mbio
	NCC Nota de Cr�dito Comercial
	NCE Nota de Cr�dito a Exporta��o
	NCI Nota de Cr�dito Industrial
	NCR Nota de Cr�dito Rural
	NP	Nota Promiss�ria
	NPR	Nota Promiss�ria Rural
	TM	Triplicata Mercantil
	TS	Triplicata de Servi�o
	NS	Nota de Seguro
	RC	Recibo
	FAT	Fatura
	ND	Nota de D�bito
	AP	Ap�lice de Seguro
	ME	Mensalidade Escolar
	PC	Parcela de Cons�rcio
*/
	$dadosboleto["especie_doc"] = "DM";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["inicio_nosso_numero"] = "80";  // Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)

	$funcoesBanco="funcoes_sudameris.php"; 
	$layoutBanco="layout_sudameris.php";
	}



elseif ($convBoleto=='NOSSA CAIXA') {
	// DADOS DA SUA CONTA - NOSSA CAIXA
	/*$dadosboleto["agencia"] = "0033"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "0001131"; 	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "1"; 	// Digito do Num da conta*/

	// DADOS PERSONALIZADOS - CEF
	$dadosboleto["agencia"] = "0033"; // Num da agencia, sem digito
	$dadosboleto["conta_cedente"] = "001131"; // ContaCedente do Cliente, sem digito (Somente N�meros)
	$dadosboleto["conta_cedente_dv"] = "1"; // Digito da ContaCedente do Cliente
	$dadosboleto["carteira"] = "5";  // C�digo da Carteira -> 5-Cobran�a Direta ou 1-Cobran�a Simples
	$dadosboleto["modalidade_conta"] = "04";  // modalidade da conta 02 posi��es
	
	$dadosboleto["quantidade"] = "";
	$dadosboleto["aceite"] = "n";
	$dadosboleto["uso_banco"] = "";
	$dadosboleto["especie_doc"] = "DM";
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";
	$dadosboleto["inicio_nosso_numero"] = "99";  // Inicio do Nosso numero -> 99 - Cobran�a Direta(Carteira 5) ou 0 - Cobran�a Simples(Carteira 1)

	$funcoesBanco="funcoes_nossacaixa.php"; 
	$layoutBanco="layout_nossacaixa.php";
	}

	

// logo que vai no topo do boleto
$dadosboleto["logoEmpresa"]= "imagens/logoEmpresa.jpg";


// INFORMACOES PARA O CLIENTE

$dadosboleto["instrucoes"] = "- Sr. Caixa, n�o receber ap�s $dataLimite<br>- Cobrar multa de 2% mais juros de 1% ao m�s.";
$dadosboleto["instrucoes1"] = "- &nbsp;Utilizar op��o T�tulo/Boleto para pagto. via internet ou caixa autom�tico.";
$dadosboleto["instrucoes2"] = "- Em caso de d�vidas entre em contato com $eContato";
$dadosboleto["instrucoes3"] = "&nbsp;&nbsp;ou pelo telefone: 31 3291-6242<br/>Ref.: $rsBoleto->historico)";
$dadosboleto["instrucoes4"] = "";

$dadosboleto["demonstrativo"] = "";
$dadosboleto["demonstrativo1"] = "Cobrado taxa de R$ " . str_replace(".", ",",sprintf("%.2f",$taxa_boleto)) . ' referente ao custo do boleto';
$dadosboleto["demonstrativo2"] = "";
$dadosboleto["demonstrativo3"] = "";






$emissao=date("Y-m-d");
$data_venc = $rsBoleto->vcto;  // Prazo de X dias OU informe data: "13/04/2006";	
$valor_cobrado = $rsBoleto->valor; // Valor - REGRA: Tanto faz s�mbolo da casa decimal com "." ou ","
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
$nosso_numero = $rsBoleto->numDoc;

$dadosboleto["nosso_numero"] = $nosso_numero;  // Nosso numero sem o DV - REGRA: M�ximo de 11 caracteres!
$dadosboleto["numero_documento"] = $dadosboleto["nosso_numero"];	// Num do pedido ou do documento = Nosso numero
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y");; // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula



// DADOS DO SEU CLIENTE
$dadosboleto["sacado"]	 = "$sacado - $cpf";
$dadosboleto["endereco1"]= "$ender1";
$dadosboleto["endereco2"]= "$ender2";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["valor_unitario"] = $valor_boleto;
$dadosboleto["uso_banco"] = "";

$dadosboleto["demonstrativo"]=$dadosboleto["instrucoes4"] = isset($rsBoleto->referente) ? "<br><br>Referente: $rsBoleto->referente" : '';

// N�O ALTERAR!
//echo "funcoesBanco=$funcoesBanco";
include("include/$funcoesBanco"); 
include("include/$layoutBanco");

// prepara e-mail para o Cedente informando que o boleto foi impresso
include("../mail/htmlMimeMail.php");
 
// prepara o corpo da mensagem e chama a funcao sendmail()

$eBody = "
	<table border=0 cellspacing=2 cellpadding=2 width=100%> 		<tr>
 			<td class=boxborder align=right>Sacado:
 			<td class=boxborder colspan=3><b>$sacado
 			<td class=boxborder align=right>CPF:
 			<td class=boxborder><b>$cpf 			
 		<tr>
 			<td class=boxborder align=right>Emiss�o:
			<td class=boxborder><b>{$dadosboleto["data_processamento"]}
 			<td class=boxborder align=right>N� Docto.:
 			<td class=boxborder><b>{$dadosboleto["numero_documento"]}
 			<td class=boxborder align=right>N/N�mero:
 			<td class=boxborder><b>{$dadosboleto["nosso_numero"]}</b>
 			
 		<tr>
 			<td class=boxborder align=right>Vencto.:
 			<td class=boxborder><b>{$dadosboleto["data_vencimento"]}
 			<td class=boxborder align=right>Valor:
 			<td class=boxborder><b>R$ {$dadosboleto["valor_boleto"]}
			<td class=boxborder align=right>Referente.:
 			<td class=boxborder><b>$rsBoleto->historico

 		<tr>
 			<td valign=top colspan=6 class=boxborder>
 				Foi impresso o boleto conforme os dados mencionados acima. 
 				Favor conferir pelo extrato banc�rio o pagamento conforme <b>N� Documento</b> ou <b>Nosso N�mero</b>  
 				e proceder a baixa do mesmo atrav�s do sistema administrativo.</td></tr>
 	</table>";
 
 
// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
$mailsend = sendMail($eBoleto, 'AMF/Boletos', $webmaster, 'Boleto on Line', $eBody, 'Boleto on Line');
//$mailsend = sendMail('lab.design@globo.com', 'AMF/Boletos', $webmaster, 'Boleto on Line', $eBody, 'Boleto on Line');
echo"<div style='text-align:center; width:680px; margin-top:30px'><input type='button' value='Imprimir o Boleto' onclick='window.print()'></p>";
?>
