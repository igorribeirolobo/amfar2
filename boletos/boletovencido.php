<?

// DADOS DO BOLETO PARA O SEU CLIENTE
include '../global.php';
include '../includes/funcoesUteis.php';





$id = $_GET['id'];	// id do financeiro
if (!$id) die("Erro: Não foi passado o id do boleto para impressão");



//debug($_GET);
//$rsSacado = null;
$query=mssql_query("SELECT 
	c.idCadastro, c.nome, c.cpf, c.ender, c.num, c.bairro, c.cep, c.cidade, c.uf, c.email,
	f.numDoc, f.historico, 
	convert(char(10), f.vcto, 103) as vcto, 
	convert(char(10), f.pgto, 103) as pgto,
	f.valor, f.idSacado 
	FROM cadastro c, financeiro f
	WHERE (f.idFinanceiro=$id AND c.idCadastro=f.idCadastro)");

$rsBoleto=mssql_fetch_object($query);
//debug($rsBoleto);
if($rsBoleto->idSacado > 0){
	$rsSacado = mssql_fetch_object(mssql_query("SELECT * FROM cadastro WHERE idCadastro=$rsBoleto->idSacado"));
	$rsBoleto->aluno = $rsBoleto->nome;
	$rsBoleto->nome = $rsSacado->nome;
	$rsBoleto->ender = $rsSacado->ender;
	$rsBoleto->num = $rsSacado->num;
	$rsBoleto->bairro = $rsSacado->bairro;
	$rsBoleto->cep = $rsSacado->cep;
	$rsBoleto->cidade = $rsSacado->cidade;
	$rsBoleto->uf = $rsSacado->uf;
	$rsBoleto->cpf = $rsSacado->cpf;
	$rsBoleto->historico .= "<br />Aluno(a): $rsBoleto->aluno";	
	}


//debug($rsBoleto);

$sacado= $rsBoleto->nome;
$ender1= "$rsBoleto->ender, $rsBoleto->num $rsBoleto->bairro";
$ender2= "$rsBoleto->cep - $rsBoleto->cidade/$rsBoleto->uf";
$cpf	 = formatCPF($rsBoleto->cpf);
$email = $rsBoleto->email;



$v=explode('/', $rsBoleto->vcto);
$dataLimite = date("Ymt", strtotime(sprintf('%s%s%s', $v[2], $v[1], $v[0]))); // vai mostrar o ultimo dia do mes atual
$dataLimite = date("t/m/Y", strtotime(sprintf('%s/%s/%s', $v[2], $v[1], $v[0]))); // vai mostrar o ultimo dia do mes atual



//$taxa_boleto= 2.9;
$taxa_boleto= 0;
$dadosboleto["identificacao"]	= "ASSOCIAÇÃO MINEIRA DE FARMACÊUTICOS - AMF";
$dadosboleto["endereco"] 		= "AVENIDA DO CONTORNO, 9215 - SALAS 501/502";
$dadosboleto["cidade_uf"]		= "CEP 30110-130  - Belo Horizonte/MG";
$dadosboleto["cpf_cnpj"] 		= "17.431.743/0001-19";
$dadosboleto["telefone"]		= "(31) 3291-6242";
$dadosboleto["cedente"]			= "ASSOCIAÇÃO MINEIRA DE FARMACÊUTICOS - AMF";



// DADOS DA SUA CONTA - CEF

$dadosboleto["agencia"] = "0083"; // Num da agencia, sem digito
$dadosboleto["conta"] = "026522"; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = "8"; 	// Digito do Num da conta

//	$dadosboleto["conta_cedente"] = "87000000414"; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente"] = "026522"; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = "8"; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = "1";  // Código da Carteira
$dadosboleto["quantidade"] = "01";
$dadosboleto["aceite"] = "N";
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "DM";
$dadosboleto["inicio_nosso_numero"]="90";  // Inicio do Nosso numero - Pode ser 80 ou 81 ou 82 (Confirmar com gerente qual usar)
$funcoesBanco="funcoes_cef.php"; 
$layoutBanco="layout_cef.php";





// logo que vai no topo do boleto
$dadosboleto["logoEmpresa"]= "imagens/logoEmpresa.jpg";





// INFORMACOES PARA O CLIENTE

//echo"rsBoleto->numDoc=$rsBoleto->numDoc";

$parcela = (int)substr($rsBoleto->numDoc, -2);

//if($parcela > 1){
	$dadosboleto["instrucoes"] .= "-&nbsp;&nbsp;Após o vencimento, cobrar multa de 2% mais juros de 0,03% ao dia.";	
//	}

//else{
//	$dataLimite = '28/03/2012';
//	$dadosboleto["instrucoes"] = "-&nbsp;&nbsp;Sr. Caixa, não receber após $dataLimite.";
//	}



//

$dadosboleto["instrucoes1"] = "-&nbsp;&nbsp;Utilizar opção Título/Boleto para pagto. via internet ou caixa automático.";

$dadosboleto["instrucoes2"] = "-&nbsp;Em caso de dúvidas entre em contato com $eContato";

$dadosboleto["instrucoes3"] = "&nbsp;&nbsp;ou pelo telefone: 31 3291-6242<br/>Ref.: $rsBoleto->historico";

$dadosboleto["instrucoes4"] = "";



$dadosboleto["demonstrativo"] = "";

$dadosboleto["demonstrativo1"] = "Cobrado taxa de R$ " . str_replace(".", ",",sprintf("%.2f",$taxa_boleto)) . ' referente ao custo do boleto';

$dadosboleto["demonstrativo2"] = "";

$dadosboleto["demonstrativo3"] = "";













$emissao=date("Y-m-d");

$data_venc = $rsBoleto->vcto;  // Prazo de X dias OU informe data: "13/04/2006";	

$valor_cobrado = $rsBoleto->valor; // Valor - REGRA: Tanto faz símbolo da casa decimal com "." ou ","

$valor_cobrado = str_replace(",", ".",$valor_cobrado);

$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$nosso_numero = $rsBoleto->numDoc;



$dadosboleto["nosso_numero"] = $nosso_numero;  // Nosso numero sem o DV - REGRA: Máximo de 11 caracteres!

$dadosboleto["numero_documento"] = $dadosboleto["nosso_numero"];	// Num do pedido ou do documento = Nosso numero

$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA

$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto

$dadosboleto["data_processamento"] = date("d/m/Y");; // Data de processamento do boleto (opcional)

$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula







// DADOS DO SEU CLIENTE

$dadosboleto["sacado"]	 = "$sacado - $cpf";

$dadosboleto["endereco1"]= "$ender1";

$dadosboleto["endereco2"]= "$ender2";



// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE

$dadosboleto["valor_unitario"] = $valor_boleto;

$dadosboleto["uso_banco"] = "";



$dadosboleto["demonstrativo"]=$dadosboleto["instrucoes4"] = isset($rsBoleto->referente) ? "<br><br>Referente: $rsBoleto->referente" : '';



// NÃO ALTERAR!

//echo "funcoesBanco=$funcoesBanco";

include("include/$funcoesBanco"); 

include("include/$layoutBanco");

?>

