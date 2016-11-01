<?php
require_once(dirname(__FILE__) . '/html2fpdf/fpdf.php');

class BoletoPDF extends FPDF {
	var $_idBoleto;
	var $_dba;
	var $_images;	
	var $_sacado;
	var $_boleto;
	var $_cur_x;

	//Page header
	function Header() {		
		$this->filename = basename($header->file);
		@unlink($this->filename);
		$this->SetFillColor(230,230,230);
		}



	//Colored table
	function BodyTable() {		
		$height = 4.5;
		$border = 0;
		$this->_cur_x = 11.5;
		
		$this->SetLineWidth(.3);		
		$this->SetFont('','B', 9);
		$this->Cell(0, $height, 'Gerado pelo boletosPDF.php - Instruções para Impressão', $border, 0, 'C');
		$this->Ln();
		$this->Ln();

		$this->SetFont('','', 9);
		$this->Cell(0, 3.8, '- Imprima em impressora jato de tinta (ink jet) ou laser em qualidade normal ou alta (Não use modo econômico).');
		$this->Ln();
		$this->Cell(0, 3.8, '- Utilize folha A4 (210 x 297 mm) ou Carta (216 x 279 mm) e margens mínimas à esquerda e à direita do formulário.');
		$this->Ln();
		$this->Cell(0, 3.8, '- Corte na linha indicada. Não rasure, risque, fure ou dobre a região onde se encontra o código de barras.');
		$this->Ln();
		$this->Cell(0, 3.8, '- Caso não apareça o código de barras no final, clique em F5 para atualizar esta tela.');
		$this->Ln();		
		$this->Cell(0, 3.8, '- Caso tenha problemas ao imprimir, copie a sequência numérica abaixo e pague no caixa eletrônico ou no internet banking:');
		$this->Ln();
	
		$this->Ln();
		$this->SetFont('','B', 9);

		$this->Cell(150, 3.8, sprintf('Linha Digitável: %s', $this->_boleto->linhaDigitavel), 0, 0, 'L');
		$this->Cell(0, 3.8, $this->_boleto->valor, 0, 0, 'R');

		$this->Ln();
		
		
		
		$this->Line(10.5,49,199,49);
		$this->SetFont('','', 6);
		$this->Cell(0, 10, 'Recibo do Sacado', 0, 0, 'R');
		$this->Ln();
	
		
		
		
		
		$this->SetFont('','', 8);
		$this->Cell(23);
		$this->Cell(0, 3.8, 'AMF - Associação Mineira de Farmacêuticos');
		$this->Ln();
		$this->Cell(23);		
		$this->Cell(0, 3.8, '17.431.743/0001-19 - Telefone: (31) 3291-6242');
		$this->Ln();
		$this->Cell(23);
		$this->Cell(0, 3.8, 'AENIDA DO CONTORNO, 9215 - SALAS 501/502');
		$this->Ln();
		$this->Cell(23);
		$this->Cell(0, 3.8, 'CEP 30110-130 - Belo Horizonte - MG');
		$this->Image('..'.$this->_boleto->logoEmpresa, 10, 53, 20);



		
		$this->SetFont('','B', 36);
		$this->Ln();
		$this->Ln();
		$this->Ln();
		
		$this->SetFont('','B', 16);
		$this->Cell(42, $height, '', 0, 0, 'L');
		$this->Cell(30, $height, '|'.$this->_boleto->codBancoDv.'|', 0, 0, 'L');
		$this->SetFont('','B', 12);
		$this->Cell(0, $height, $this->_boleto->linhaDigitavel, 0, 0, 'R');
		$this->Ln();
		$this->Image('..'.$this->_images.'/boletos/logocaixa.jpg', 10, 78, 42);
		




		
		
		$this->Line(12,89.2,199,89.2); // 1a. linha horizontal		
		$this->Line(12,89.5,12,117.5);
		$this->Line(12,96.5,199,96.5);
		$this->Line(12,103.5,199,103.5);
		$this->Line(12,110.5,199,110.5);
		$this->Line(12,117.5,199,117.5);

		$this->Line(92,89.5,92,96.5); // 2a. linha vertical
		$this->Line(132,89.5,132,96.5); // 3a. linha vertical
		$this->Line(143,89.5,143,110.5); // 4a. linha vertical
		$this->Line(160,89.5,160,96.5); // 5s. linha vertical

		$this->Line(70,96.5,70,110.5); // 2a. linha vertical
		$this->Line(106,96.5,106,103.5); // 3a. linha vertical
		
		$this->Line(40,103.5,40,110.5); // 3a. linha vertical
		$this->Line(70,103.5,70,110.5); // 3a. linha vertical		
		$this->Line(106,103.5,106,110.5); // 3a. linha vertical
		
		
		$this->SetFont('','', 6);
		$this->Cell( 2, 3.5,'');
		$this->Cell(81, 3.5, 'Cedente',0,0,'L');
		$this->Cell(40, 3.5, 'Agência/Código do Cedente',0,0,'L');
		$this->Cell(11, 3.5, 'Espécie',0,0,'L');
		$this->Cell(17, 3.5, 'Quantidade',0,0,'L');
		$this->Cell(0, 3.5, 'Nosso Número',0,0,'L');
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell( 2, 3.9);
		$this->Cell(81, 3.9, 'AMF - Associação Mineira de Farmacêuticos',0,0,'L');
		$this->Cell(40, 3.9, $this->_boleto->agenciaConta,0,0,'L');
		$this->Cell(12, 3.9);
		$this->Cell(17, 3.9, '001');
		$this->Cell( 0, 3.9, $this->_boleto->nossoNumeroDv, 0, 0, 'R');
		$this->Ln();





		$this->SetFont('','', 6);
		$this->Cell(2, 3.5);
		$this->Cell(58, 3.5, 'Número do documento',0,0,'L');
		$this->Cell(36, 3.5, 'CPF/CNPJ',0,0,'L');
		$this->Cell(37, 3.5, 'Vencimento',0,0,'L');
		$this->Cell(0, 3.5, 'Valor documento',0,0,'L');
		$this->Ln();
		
		$this->SetFont('','B', 8);
		$this->Cell(2, 3.8, '', 0, 0, 'L');
		$this->Cell(58, 3.8, $this->_boleto->numDoc,0,0,'L');
		$this->Cell(36, 3.8, $this->_sacado->cpf,0,0,'L');
		$this->Cell(37, 3.8, $this->_boleto->vcto,0,0,'L');
		$this->Cell(0, 3.8, $this->_boleto->valor, 0, 0, 'R');
		$this->Ln();

		$this->SetFont('','', 6);
		$this->Cell(2, 3.5, '', 0, 0, 'L');
		$this->Cell(28, 3.5, '(-) Desconto/Abatimentos',0,0,'L');
		$this->Cell(30, 3.5, '(-) Outras Deduções',0,0,'L');
		$this->Cell(36, 3.5, '(+) Mora / Multa',0,0,'L');
		$this->Cell(37, 3.5, '(+) Outros Acréscimos',0,0,'L');
		$this->Cell(0, 3.5, '(=) Valor Cobrado',0,0,'L');
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell(0, 3.5);
		$this->Ln();
		$this->SetFont('','', 6);
		$this->Cell(2, 3.5);
		$this->Cell(0, 3, 'Sacado',0,0,'L');
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell(2, 3.5, '', 0, 0, 'L');
		$this->Cell(0, 3.8, $this->_sacado->nome,0,0,'L');		
		$this->Ln();
		$this->SetFont('','', 6);
		$this->Cell(1, 3.5);
		$this->Cell(30, 3.5, 'Demonstrativo',0,0,'L');
		$this->Cell(0, 3.5, 'Autenticação Mecânica',0,0,'R');





		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->Ln();

		$this->Line(11.5,135,199,135);
		$this->SetFont('','', 6);
		$this->Cell(0, 10, 'Corte na linha', 0, 0, 'R');
		$this->Ln();
		$this->Cell(0, 18.5, '', 0, 0, 'R');
		$this->Ln();



		// ficha compensação


		$this->SetFont('','B', 16);
		$this->Cell(42, $height, '', 0, 0, 'L');
		$this->Cell(30, $height, '|'.$this->_boleto->codBancoDv.'|', 0, 0, 'L');
		$this->SetFont('','B', 12);
		$this->Cell(0, $height, $this->_boleto->linhaDigitavel, 0, 0, 'R');
		$this->Ln();
		$this->Image('..'.$this->_images.'/boletos/logocaixa.jpg', 10, 154, 42);
		

		
		$this->Line(12,165.2,199,165.2); // linhas horizontais
		$this->Line(12,165.5,12,243.5);
		$this->Line(12,172.5,199,172.5);
		$this->Line(12,179.5,199,179.5);
		$this->Line(12,186.5,199,186.5);
		$this->Line(12,193.5,199,193.5);		
		$this->Line(146,200.5,199,200.5);
		$this->Line(146,207.5,199,207.5);
		$this->Line(146,214.5,199,214.5);
		$this->Line(146,221.5,199,221.5);
		$this->Line(12,228.5,199,228.5);
		$this->Line(12,243.5,199,243.5);

		
		$this->Line(146,165.5,146,243.5); // 1a. linha vertical
		$this->Line(92,179.5,92,186.5); // 2a. linha vertical
		$this->Line(112,179.5,112,186.5); // 2a. linha vertical
		$this->Line(123,179.5,123,193.5); // 2a. linha vertical
		
		$this->Line(46,179.5,46,193.5); // 2a. linha vertical
		$this->Line(71,186.5,71,193.5); // 2a. linha vertical
		$this->Line(88,186.5,88,193.5); // 2a. linha vertical
		
		
		$h=3.3;
		$h2=3.8;
		$this->SetFont('','', 6);
		$this->Cell(  2, $h, '', 0, 0, 'L');
		$this->Cell(134, $h, 'Local de pagamento', 0, 0, 'L');
		$this->Cell(  0, $h, 'Vencimento', 0, 0, 'L');

		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell(  2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, 'Pagável em qualquer banco até o vencimento', 0, 0, 'L');
		$this->Cell(  0, $h2, $this->_boleto->vcto, 0, 0, 'R');
		$this->Ln();

		$this->SetFont('','', 6);
		$this->Cell(  2, $h, '', 0, 0, 'L');
		$this->Cell(134, $h, 'Cedente', 0, 0, 'L');
		$this->Cell(  0, $h, 'Agência/Código cedente', 0, 0, 'L');

		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell(  2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, 'AMF - Associação Mineira de Farmacêuticos', 0, 0, 'L');
		$this->Cell(  0, $h2, $this->_boleto->agenciaConta, 0, 0, 'R');
		$this->Ln();


		
		$this->SetFont('','', 6);
		$this->Cell(2, $h, '', 0, 0, 'L');
		$this->Cell(34, $h, 'Data do documento', 0, 0, 'L');
		$this->Cell(46, $h, 'Num. documento', 0, 0, 'L');
		$this->Cell(20, $h, 'Espécie doc.', 0, 0, 'L');
		$this->Cell(11, $h, 'Aceite', 0, 0, 'L');
		$this->Cell(23, $h, 'Data processamento', 0, 0, 'L');
		$this->Cell(0, $h, 'Nosso número', 0, 0, 'L');
		$this->Ln();
		

		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(34, $h2, date('d/m/Y'), 0, 0, 'L');
		$this->Cell(46, $h2, $this->_boleto->numDoc, 0, 0, 'L');
		$this->Cell(20, $h2, 'DS', 0, 0, 'L');
		$this->Cell(11, $h2, '', 0, 0, 'L');
		$this->Cell(23, $h2, date('d/m/Y'), 0, 0, 'L');
		$this->Cell( 0, $h2, $this->_boleto->nossoNumeroDv, 0, 0, 'R');
		$this->Ln();


		$this->SetFont('','', 6);
		$this->Cell( 2, $h, '', 0, 0, 'L');
		$this->Cell(34, $h, 'Uso do banco', 0, 0, 'L');
		$this->Cell(25, $h, 'Carteira', 0, 0, 'L');
		$this->Cell(17, $h, 'Espécie', 0, 0, 'L');
		$this->Cell(35, $h, 'Quantidade', 0, 0, 'L');
		$this->Cell(23, $h, 'Valor Documento', 0, 0, 'L');
		$this->Cell( 0, $h, '(=) Valor documento', 0, 0, 'L');
		$this->Ln();
		
		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(34, $h2, '', 0, 0, 'L');
		$this->Cell(25, $h2, $this->_boleto->carteira, 0, 0, 'L');
		$this->Cell(17, $h2, '', 0, 0, 'L');
		$this->Cell(35, $h2, '001', 0, 0, 'L');
		$this->Cell(23, $h2, $this->_boleto->valor, 0, 0, 'L');
		$this->Cell( 0, $h2, $this->_boleto->valor, 0, 0, 'R');
		$this->Ln();





		$h3 = 3.2;
		$this->SetFont('','', 6);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h3, 'Instruções (Texto de responsabilidade do cedente)', 0, 0, 'L');
		$this->Cell(0, $h3, '(-) Desconto / Abatimentos', 0, 0, 'L');
		$this->Ln();
		
		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, '- Após o vencimento, cobrar multa de 2% mais juros de 0,03% ao dia.', 0, 0, 'L');
		$this->Ln();

		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, "- Não receber após o dia {$this->_boleto->dataLimite}.", 0, 0, 'L');
		$this->Ln();

		
		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h3, '- Utilizar opção Título/Boleto para pgto. via internet ou caixa automático.', 0, 0, 'L');
		$this->SetFont('','', 6);
		$this->Cell(0, $h3, '(-) Outras Deduções', 0, 0, 'L');
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, '- Em caso de dúvidas entre em contato com secretaria@amfar.com.br', 0, 0, 'L');
		$this->Ln();
		
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h3, '  ou pelo telefone: 31 3291 6242', 0, 0, 'L');
		$this->SetFont('','', 6);
		$this->Cell(0, $h3, '(+) Mora / Multa', 0, 0, 'L');
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, 'Ref.: '. $this->_boleto->historico, 0, 0, 'L');
		
		$this->Ln();		
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h3, '', 0, 0, 'L');
		$this->SetFont('','', 6);
		$this->Cell(0, $h3, '(+) Outros Acréscimos', 0, 0, 'L');
		$this->Ln();
		$this->Cell(133, $h2, '', 0, 0, 'L');
		
		$this->Ln();
		$this->Cell( 2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h3, '', 0, 0, 'L');
		$this->SetFont('','', 6);
		$this->Cell(0, $h3, '(=) Valor Cobrado', 0, 0, 'L');
		$this->Ln();
		$this->Cell(134, $h2, '', 0, 0, 'L');
		$this->Ln();

		$this->SetFont('','', 6);
		$this->Cell(2, $h, '', 0, 0, 'L');
		$this->Cell(0, $h, 'Sacado', 0, 0, 'L');
		
		$this->Ln();
		$this->SetFont('','B', 8);
		$this->Cell(2, $h2, '', 0, 0, 'L');
		$this->Cell(0, $h2, $this->_sacado->nome . ' - ' . $this->_sacado->cpf, 0, 0, 'L');		
		$this->Ln();
		$this->Cell(2, $h2, '', 0, 0, 'L');
		$this->Cell(0, $h2, $this->_sacado->ender . ', ' . $this->_sacado->num . ' ' . $this->_sacado->compl . ' ' . $this->_sacado->bairro, 0, 0, 'L');		
		$this->Ln();
		$this->Cell(2, $h2, '', 0, 0, 'L');
		$this->Cell(134, $h2, $this->_sacado->cep . ', ' . $this->_sacado->cidade . ' / ' . $this->_sacado->uf, 0, 0, 'L');
		
		$this->SetFont('','', 6);
		$this->Cell(0, 4, 'Cód. baixa', 0, 0, 'L');
		
		$this->Ln();
		$this->Cell( 1, 3.5, '', 0, 0, 'L');
		$this->Cell(50, 3.5, 'Sacador/Avalista', 0, 0, 'L');
		$this->Cell(106.5, 3.5, 'Autenticação Mecânica - ', 0, 0, 'R');
		$this->SetFont('','B', 8);
		$this->Cell(  0, 3.5, 'Ficha de Compensação', 0, 0, 'R');
		$this->Ln();
		$this->fbarcode($this->_boleto->codBar);
		
		$this->Line(11.5,265,199,265);
		$this->SetFont('','', 6);
		$this->Cell(0, 10, 'Corte na linha', 0, 0, 'R');
		}






	function Footer() {
	   //
		}








	// funcoes boleto


	public function getSetting(){
		$fc = new FuncoesUteis();
		$this->_boleto = $fc->dbReader($this->_dba, "SELECT idCadastro, numDoc, historico, valor, vcto FROM financeiro WHERE idFinanceiro={$this->_idBoleto}");
		$this->_sacado = $fc->dbReader($this->_dba, "SELECT nome, cpf, ender, num, compl, bairro, cep, cidade, uf, email FROM cadastro WHERE idCadastro={$this->_boleto->idCadastro}");
		$this->_sacado->cpf = $fc->formatCNPJ($this->_sacado->cpf);
		//$fc->debug($this->_sacado);
		// completa os dados do boleto
		$this->_boleto->logoEmpresa= "$this->_images/boletos/logoEmpresa.jpg"; // logo que vai no topo do boleto
		$this->_boleto->codBanco = '104'; // banco
		$this->_boleto->codBancoDv = $this->geraCodigoBanco($this->_boleto->codBanco);
		$this->_boleto->moeda = '9';
				
		$this->_boleto->vcto = $fc->Ymd2dmY($this->_boleto->vcto);
		$v=explode('/', $this->_boleto->vcto); // yyyy/mm/dd
		$dataVctoInt = strtotime("{$v[1]}/{$v[0]}/{$v[2]}");
		$this->_boleto->dataLimite = date("d/m/Y", $dataVctoInt + (30 * 86400)); // vai mostrar o ultimo dia do mes atual		
		
		$this->_boleto->valor = number_format($this->_boleto->valor,2,',','.');
		
		$this->_boleto->codBarValor = sprintf('%010d', str_replace('.','',str_replace(',','', $this->_boleto->valor)));
		$this->_boleto->fatorVcto = $this->fator_vencimento($this->_boleto->vcto);
		
		$this->_boleto->agencia = '0083'; // agencia
		//$this->_boleto->agenciaDv = '0083';	// dv da agencia
		$this->_boleto->conta = "026522"; 	// Num da conta, 7 digitos sem dv
		$this->_boleto->contaDv = "8"; 	// dv da conta	
		$this->_boleto->carteira = "1";  // Código da Carteira
		$this->_boleto->quantidade = "001";
		$this->_boleto->especieDoc = "DM";
		$this->_boleto->aceite = "N";		
		$this->_boleto->nossoNumero = sprintf('90%016d', $this->_boleto->numDoc);
		//$this->_boleto->nossoNumeroDvPHP='900000000018264901';
		$this->_boleto->nossoNumeroDv = $this->_boleto->nossoNumero.'-'.$this->digitoVerificador_nossonumero($this->_boleto->nossoNumero);
		$this->_boleto->agenciaConta = $this->_boleto->agencia . ' / ' . $this->_boleto->conta . '-' . $this->_boleto->contaDv; 
		
		
		//$dv = digitoVerificador_barra("$codigobanco$nummoeda$fator_vencimento$valor$carteira$conta$nnum", 9, 0); echo "digitoVerificador_barra=$dv<br/>";
		$this->_boleto->codBarDv = $this->digitoVerificador_barra($this->_boleto->codBanco . $this->_boleto->moeda . $this->_boleto->fatorVcto . 
			$this->_boleto->codBarValor . $this->_boleto->carteira . $this->_boleto->conta . $this->_boleto->nossoNumero, 9, 0);

		//$linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$carteira$conta$nnum";
		$this->_boleto->codBar = $this->_boleto->codBanco . $this->_boleto->moeda . $this->_boleto->codBarDv . $this->_boleto->fatorVcto . 
			$this->_boleto->codBarValor . $this->_boleto->carteira . $this->_boleto->conta . $this->_boleto->nossoNumero;
		
		//$this->_boleto->linha = '&nbsp;10491563300000490001026522900000000018264901';
		
		$this->_boleto->linhaDigitavel = $this->monta_linha_digitavel($this->_boleto->codBar);


		//$fc->debug($this->_boleto);
		
		$this->file = sprintf("..%s/%06d.%06d.%s.pdf", 
			str_replace('images','boletos',$this->_images), 
			$this->_boleto->idCadastro, $this->_idBoleto, str_replace('-','',$fc->dmY2Ymd($this->_boleto->vcto)));

		//echo"this->file = $this->file";
		}
	


	// funcoes_cef

	//debug($dadosboleto);
	private function digitoVerificador_nossonumero($numero){
		$resto2 = $this->modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		if ($digito == 10 || $digito == 11){
			$dv = 0;
			}
		else {
			$dv = $digito;
			}
		return $dv;
		}



	private function digitoVerificador_barra($numero){
		$resto2 = $this->modulo_11($numero, 9, 1);
		if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
			$dv = 1;
			}
		else {
			$dv = 11 - $resto2;
			}
		return $dv;
		}



	// FUNÇÕES
	// Algumas foram retiradas do Projeto PhpBoleto e modificadas para atender as particularidades de cada banco
	
	private function formata_numero($numero,$loop,$insert,$tipo = "geral") {
		if ($tipo == "geral"){
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
				}
			}
		if ($tipo == "valor") {
			/*
			retira as virgulas
			formata o numero
			preenche com zeros
			*/
			$numero = str_replace(",","",$numero);
			while(strlen($numero)<$loop){
				$numero = $insert . $numero;
				}
			}
		if ($tipo = "convenio") {
			while(strlen($numero)<$loop){
				$numero = $numero . $insert;
				}
			}
		return $numero;
		}



	// $this->Image('../..'.$this->_images.'/boleto/p.gif', 12, $ybar, $hbar);
	function setbar($img, $narrow=true){
		$cur_y = 248;
		$height = 14;
		$width = ($narrow) ? 0.29 : 0.87;
		$this->Image("..$this->_images/boletos/$img.png", $this->_cur_x, $cur_y, $width, $height);
		$this->_cur_x += $width;	
		}


	
	
	public function fbarcode($valor){
//		die("valor=$valor");	
		$fino = 1;
		$largo = 3;
		$altura = 50;
		$cur_x = 12;
		
		$barcodes[0] = "00110" ;
		$barcodes[1] = "10001" ;
		$barcodes[2] = "01001" ;
		$barcodes[3] = "11000" ;
		$barcodes[4] = "00101" ;
		$barcodes[5] = "10100" ;
		$barcodes[6] = "01100" ;
		$barcodes[7] = "00011" ;
		$barcodes[8] = "10010" ;
		$barcodes[9] = "01010" ;
		for($f1=9;$f1>=0;$f1--){ 
			for($f2=9;$f2>=0;$f2--){  
				$f = ($f1 * 10) + $f2 ;
				$texto = "" ;
				for($i=1;$i<6;$i++){ 
					$texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
					}
				$barcodes[$f] = $texto;
				}
			}
			
		//Guarda inicial
		$this->setbar('p');
		$this->setbar('b');
		$this->setbar('p');
		$this->setbar('b');
		
		$texto = $valor ;
		if((strlen($texto) % 2) <> 0){
			$texto = "0" . $texto;
		}
	
		// Draw dos dados
		while (strlen($texto) > 0) {
			$i = round($this->esquerda($texto,2));
			$texto = $this->direita($texto,strlen($texto)-2);
			$f = $barcodes[$i];
			for($i=1;$i<11;$i+=2):
				if (substr($f,($i-1),1) == "0"){
					$this->setbar('p', true);
					}
				else{
					$this->setbar('p', false);
					}
				if (substr($f,$i,1) == "0") {
					$this->setbar('b', true);
					}
				else{
					$this->setbar('b', false);
					}
			endfor;
		}
		
		// Draw guarda final
		$this->setbar('p', false);
		$this->setbar('b', true);
		$this->setbar('p', true);
		} //Fim da função


	
	private function esquerda($entra,$comp){
		return substr($entra,0,$comp);
		}


	
	private function direita($entra,$comp){
		return substr($entra,strlen($entra)-$comp,$comp);
		}



	
	private function fator_vencimento($data) {
		$data = split("/",$data);
		$ano = $data[2];
		$mes = $data[1];
		$dia = $data[0];
		return(abs(($this->_dateToDays("1997","10","07")) - ($this->_dateToDays($ano, $mes, $dia))));
		}




	
	private function _dateToDays($year,$month,$day){
		$century = substr($year, 0, 2);
		$year = substr($year, 2, 2);
		if ($month > 2){
			$month -= 3;
			}
		else {
			$month += 9;
			if ($year){
				$year--;
				}
			else{
				$year = 99;
				$century --;
				}
			}
		return ( floor((  146097 * $century) /  4 ) +
		floor(( 1461 * $year)  /  4 ) +
		floor(( 153 * $month +  2) /  5 ) +
		$day +  1721119);
		}



	
	private function modulo_10($num){ 
		$numtotal10 = 0;
		$fator = 2;
		
		// Separacao dos numeros
		for ($i = strlen($num); $i > 0; $i--){
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo (falor 10)
			// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
			$temp = $numeros[$i] * $fator; 
			$temp0=0;
			foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v){
				$temp0+=$v;
				}
			$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
			// monta sequencia para soma dos digitos no (modulo 10)
			$numtotal10 += $parcial10[$i];
			if ($fator == 2){
				$fator = 1;
				}
			else {
				$fator = 2; // intercala fator de multiplicacao (modulo 10)
				}
			}
		
		// várias linhas removidas, vide função original
		// Calculo do modulo 10
		$resto = $numtotal10 % 10;
		$digito = 10 - $resto;
		if ($resto == 0){
			$digito = 0;
			}		
		return $digito;			
		}




	
	private function modulo_11($num, $base=9, $r=0){
		/**
		*   Autor:
		*           Pablo Costa <pablo@users.sourceforge.net>
		*
		*   Função:
		*    Calculo do Modulo 11 para geracao do digito verificador 
		*    de boletos bancarios conforme documentos obtidos 
		*    da Febraban - www.febraban.org.br 
		*
		*   Entrada:
		*     $num: string numérica para a qual se deseja calcularo digito verificador;
		*     $base: valor maximo de multiplicacao [2-$base]
		*     $r: quando especificado um devolve somente o resto
		*
		*   Saída:
		*     Retorna o Digito verificador.
		*
		*   Observações:
		*     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
		*     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
		*/                                        
		
		$soma = 0;
		$fator = 2;
		
		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--){
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2 
				$fator = 1;
				}
			$fator++;
			}
		
		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			if ($digito == 10) {
				$digito = 0;
				}
			return $digito;
			}
		elseif ($r == 1){
			$resto = $soma % 11;
			return $resto;
			}
		}



	
	private function monta_linha_digitavel($codigo){			
		// Posição 	Conteúdo
		// 1 a 3    Número do banco
		// 4        Código da Moeda - 9 para Real
		// 5        Digito verificador do Código de Barras
		// 6 a 9   Fator de Vencimento
		// 10 a 19 Valor (8 inteiros e 2 decimais)
		// 20 a 44 Campo Livre definido por cada banco (25 caracteres)
		
		// 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
		// do campo livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 0, 4);
		$p2 = substr($codigo, 19, 5);
		$p3 = $this->modulo_10("$p1$p2");
		$p4 = "$p1$p2$p3";
		$p5 = substr($p4, 0, 5);
		$p6 = substr($p4, 5);
		$campo1 = "$p5.$p6";
		
		// 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 24, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo2 = "$p4.$p5";
		
		// 3. Campo composto pelas posicoes 16 a 25 do campo livre
		// e livre e DV (modulo10) deste campo
		$p1 = substr($codigo, 34, 10);
		$p2 = $this->modulo_10($p1);
		$p3 = "$p1$p2";
		$p4 = substr($p3, 0, 5);
		$p5 = substr($p3, 5);
		$campo3 = "$p4.$p5";
		
		// 4. Campo - digito verificador do codigo de barras
		$campo4 = substr($codigo, 4, 1);
		
		// 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
		// indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
		// tratar de valor zerado, a representacao deve ser 000 (tres zeros).
		$p1 = substr($codigo, 5, 4);
		$p2 = substr($codigo, 9, 10);
		$campo5 = "$p1$p2";
		
		return "$campo1 $campo2 $campo3 $campo4 $campo5"; 
		}




	
	private function geraCodigoBanco($numero){
		$parte1 = substr($numero, 0, 3);
		$parte2 = $this->modulo_11($parte1);
		return $parte1 . "-" . $parte2;
		}


	}
?>
