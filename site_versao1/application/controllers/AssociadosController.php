<?php
class AssociadosController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/associados", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		//$this->fc->debug($this->view->user);
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		$this->view->taxaAnuidade = 120;
		/*
		Zend_Loader::loadClass('Financeiro');
		$financeiro = new Financeiro();
		$query = $this->db->fetchAll("SELECT *, 
			CONVERT(CHAR(10), dataLct, 103) AS emissao,
			CONVERT(CHAR(10), vcto, 103) AS dataVcto,
			CONVERT(CHAR(10), pgto, 103) AS dataPgto,
			upper(historico) AS historico
			FROM financero_20120331
				WHERE status = 4 AND (CONVERT(CHAR(10), dataReg, 102) < '2012.01.01')");
		foreach($query as $rs):
			$_data = array(
				'idFinanceiro' => $rs->idFinanceiro,
				'idCadastro' => $rs->idCadastro,
				'dataLct' => $this->fc->dmY2msSql($rs->emissao),
				'numDoc' =>  $rs->numDoc,
				'tipoDoc' => $rs->tipoDoc,
				'conta' => $rs->conta,
				'historico' => $rs->historico,
				'valor' => $rs->valor, 
				'vcto' => $this->fc->dmY2msSql($rs->dataVcto),
				'pgto' => $this->fc->dmY2msSql($rs->dataPgto),
				'total' => $rs->total,
				'formaPgto' => $rs->formaPgto,
				'boleto' => $rs->boleto,
				'status' => $rs->status,
				'idCentroCusto' => $rs->idCentroCusto,
				'dataReg' => $this->fc->dmY2msSql($rs->emissao),
				'observacoes' => $rs->observacoes,
				);
			
			$idFinanceiro = (int)$this->fc->dbReader($this->db, sprintf("SELECT idFinanceiro FROM financeiro WHERE 
				(numDoc='%s') AND (idCadastro=%d) AND (valor=%s)", $_data['numDoc'], $rs->idCadastr, $rs->valor))->idFinanceiro;
			
			$this->fc->debug($rs); $this->fc->debug($_data); //die();
			if(!$idFinanceiro)
				$financeiro->insert($_data);		
		endforeach;
		*/
		}






	function gerarboletoAction(){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/associados/login/');
			}
			
		try{
			Zend_Loader::loadClass('Financeiro');
			$financeiro = new Financeiro();
			
			//$taxa = ($this->view->user->formacao == 'EST. GRADUAÇÃO: FARMÁCIA') ? $this->view->taxaAnuidade/2 : $this->view->taxaAnuidade;
			$offSet = 4;
			if ($this->view->user->uf=='BA') $_cCusto=2;
			elseif ($this->view->user->uf=='MG') $_cCusto=3;
			elseif ($this->view->user->uf=='SP') $_cCusto=4;
			elseif ($this->view->user->uf=='RJ') $_cCusto=5;
			elseif ($this->view->user->uf=='PR') $_cCusto=6;
			elseif ($this->view->user->uf=='RN') $_cCusto=7;
			else $_cCusto=1;				
			$_data = array(
				'idCadastro' => $this->view->user->idCadastro,
				'numDoc' =>  sprintf('%05d%d%d',$this->view->user->idCadastro, date('y'), date('y')+1),
				'tipoDoc' => 2,
				'conta' => 41101001,
				'historico' => 'TAXA DE ASSOCIADO - RENOVAÇÃO',
				'valor' => $this->view->user->valor, 
				'vcto' => date("m.d.Y", time() + ($offSet * 86400)),
				'total' => $this->view->user->valor,
				'formaPgto' => 2,
				'boleto' => sprintf('%05d%d%d',$this->view->user->idCadastro, date('y'), date('y')+1),
				'status' => 2,
				'idCentroCusto' => $_cCusto,
				);
			
			$query = $this->fc->dbReader($this->db, sprintf("SELECT idFinanceiro, pgto			
				FROM financeiro
					WHERE (numDoc='%s')", $_data['numDoc']));
			//$this->fc->debug($_data);
			if((int)$query->idFinanceiro == 0)
				$query->idFinanceiro = $financeiro->insert($_data);
			elseif(trim($query->pgto)=='')
				$financeiro->update($_data, "idFinanceiro=$query->idFinanceiro");
			else
				throw new Exception(sprintf("Já existe um pagamento efetuado em %s\nreferente à sua inscrição!\n\n", $this->fc->Ymd2dmY($query->pgto)));
		
			//$this->hstAction("Boleto gerado com sucesso.\n\nClique no link ao lado para impressão.\n\n");
			$this->view->boleto = "<a href='http://www.sbrafh.org.br/boletos/boleto.php?id=$query->idFinanceiro' target='_boleto'>
				http://www.sbrafh.org.br/boletos/boleto.php?id=$query->idFinanceiro</a>";
			//exit;
			}
		catch(Exception $ex){
			$this->view->message = $ex->getMessage();
			}
		$this->view->where = "Renovar Inscricao";
		$this->view->page = "associados/renovacao.phtml";
		$this->render('index');	
		}











	// resolvido o problema
	function hstAction($message=null){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/associados/login/');
			}
		//$this->fc->debug($this->view->user);
		$datas = $this->fc->dbReader($this->db, "SELECT
			CONVERT(CHAR(10), atualizado, 103) AS atualizado,
			CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso,
			CONVERT(CHAR(10), validade, 103) AS dataval,
			CONVERT(CHAR(10), validade, 102) AS validade,
			CONVERT(CHAR(10), dataReg, 103) AS datareg,
			status 
				FROM cadastro WHERE idCadastro=".$this->view->user->idCadastro);
			
						
		$this->view->datareg = $datas->datareg;
		$this->view->validade = $datas->validade;
		$this->view->ultAcesso = $datas->ultAcesso;
		$this->view->atualizado = $datas->atualizado;
		$this->view->dataval = $datas->dataval;
		$this->view->datalim = date('Y.m.d', mktime(0,0,0,date('m'),date('d')+7,date('Y')));

		$sql = "SELECT idFinanceiro, valor, 
			CONVERT(CHAR(10),vcto,103) AS datavcto, CONVERT(CHAR(10),pgto,103) AS datapgto, historico
			FROM financeiro
				WHERE (idCadastro={$this->view->user->idCadastro}) ORDER BY vcto DESC";
		$this->view->pgtos = $this->fc->dbReader($this->db, $sql, true);
		$this->view->message = $message;
		$this->view->where = "Histórico de Pagamentos";
		$this->view->page = "associados/historico.phtml";
		$this->render('index');	
		}













	function saveCadastro(){		
		Zend_Loader::loadClass('Cadastro');
		$cadastro = new Cadastro();
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$f = new Zend_Filter_StripTags();						
		$crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);		   
		$_confirm = strtoupper($crypt->Encrypt($f->filter(strtoupper(trim($_POST['confirme'])))));			
		$_cpf = ereg_replace("([^0-9])",'', $_POST['cpf']);
		
		
		$hfone1 = $_POST['hfone1'];
		if($_POST['hramal1'])
			$hfone1 .= '|' . $_POST['hramal1'];

		$hfone2 = $_POST['hfone2'];
		if($_POST['hramal2'])
			$hfone2 .= '|' . $_POST['hramal2'];

		$hfax = $_POST['hfax'];
		if($_POST['hramal3'])
			$hfax .= '|' . $_POST['hramal3'];
		
		
		
		
		$_dados = array(
			'nome' => trim($f->filter(strtoupper($_POST['nome']))),
			'rg' => trim($f->filter($_POST['rg'])),						
			'ender' => trim($f->filter(strtoupper($_POST['ender']))),
			'num' => (int)$_POST['num'],
			'compl' => trim($f->filter(strtoupper($_POST['compl']))),
			'bairro' => trim($f->filter(strtoupper($_POST['bairro']))),
			'cidade' => trim($f->filter(strtoupper($_POST['cidade']))),
			'uf' => trim($f->filter($_POST['uf'])),
			'cep' => ereg_replace("([^0-9])",'',$_POST['cep']),
			'pais' => trim($f->filter(strtoupper($_POST['pais']))),
			'email' => strtolower(trim($f->filter($_POST['email']))),
			'fone' => $_POST['fone'],
			'fone2' => $_POST['fone2'],
			'dataNcto' => $this->fc->dmY2msSql($_POST['dataNcto']),
			'dataAniv' => substr($_POST['dataNcto'], 0, 5),
			'sexo' => (int)$_POST['sexo'],
			'crf' => trim($f->filter($_POST['crf'])),
			'naturalidade' => trim($f->filter(strtoupper($_POST['naturalidade']))),
			'formacao' => trim($f->filter($_POST['formacao'])),			
			'areaAtuacao' => @join('|', $_POST['atuacao']),
			'instituicao' => trim($f->filter(strtoupper($_POST['instituicao']))),
			'hfone1' => $hfone1,
			'hfone2' => $hfone2,
			'hfax' => $hfax
			);
		
		if(!$_dados['nome'])throw new Exception("O campo 'Nome' é obrigatório!\n\n");
		elseif(!$_dados['ender'])throw new Exception("O campo 'Ender' é obrigatório!\n\n");
		elseif(!$_dados['num'])throw new Exception("O campo 'Número' é obrigatório!\n\n");
		elseif(!$_dados['bairro'])throw new Exception("O campo 'Bairro' é obrigatório!\n\n");
		elseif(!$_dados['cep'])throw new Exception("O campo 'CEP' é obrigatório!\n\n");
		elseif(!$_dados['cidade'])throw new Exception("O campo 'Cidade' é obrigatório!\n\n");
		elseif(!$_dados['uf'])throw new Exception("O campo 'Estado' é obrigatório!\n\n");
		elseif(!$_dados['pais'])throw new Exception("O campo 'Pais' é obrigatório!\n\n");
		elseif(!$_dados['email'])throw new Exception("O campo 'Email' é obrigatório!\n\n");
		elseif(!$_dados['fone'])throw new Exception("O campo 'Fone' é obrigatório!\n\n");
		elseif(!$_dados['fone2'])throw new Exception("O campo 'Celular' é obrigatório!\n\n");
		elseif(!$_dados['dataNcto'])throw new Exception("O campo 'Data de Nascimento' é obrigatório!\n\n");
		elseif(!$_dados['formacao'])throw new Exception("O campo 'Formação' é obrigatório!\n\n");	
		
		//$this->fc->debug($_dados);
		//die(sprintf('idCadastro = %d', $this->view->user->idCadastro));
		$_dados['valor'] = ($_dados['formacao'] == 'EST. GRADUAÇÃO: FARMÁCIA') ? $this->view->taxaAnuidade/2 : $this->view->taxaAnuidade;

		try{
			$novo = false;
			
			if(!$this->view->user->idCadastro){
				$found = (int)$this->fc->dbReader($this->db, "SELECT idCadastro FROM cadastro WHERE cpf='$_cpf'")->idCadastro;
				if($found > 0)
					throw new Exception("O cpf '$_cpf' ja existe em nossa base de dados.!\n\nPor favor efetue seu login para alterar seu cadastro.\n\n");	
				}
			
			if($this->view->user->idCadastro){
				$this->id = $this->view->user->idCadastro;
				$_dados['atualizado'] = date('m.d.Y H:i:s');
				
				$cadastro->update($_dados, sprintf("idCadastro = %d", $this->view->user->idCadastro));
				$this->view->message = "Cadastro atualizado com sucesso!\n\n";
				$query = $this->fc->dbReader($this->db, "SELECT 
					CONVERT(CHAR(10),dataReg, 103) AS datareg,
					CONVERT(CHAR(10),validade, 103) AS dataval
						FROM cadastro
							WHERE idCadastro=" . $this->id);
				$datareg = $query->datareg;
				$dataval = $query->dataval;
				
				$sql = "SELECT * FROM financeiro WHERE (idCadastro={$this->view->user->idCadastro}) AND (pgto IS NULL) ORDER BY idFinanceiro DESC";
				$financeiro = $this->fc->dbReader($this->db, $sql, true);
				foreach($financeiro as $rs):
					if($rs->valor != $_dados['valor']){
						$sql = "UPDATE financeiro SET valor={$_dados['valor']}, total={$_dados['valor']} WHERE (idFinanceiro=$rs->idFinanceiro) AND (pgto IS NULL)";
						//echo "<br />$sql<br />";
						$this->db->query($sql);						
						}
				endforeach;
				$_senha = $this->fc->dbReader($this->db, "SELECT pwd FROM cadastro WHERE idCadastro={$this->view->user->idCadastro}")->pwd;
				//$this->fc->debug($this->view->user); die();
				$this->fc->authentique($_POST['email'], $crypt->Decrypt($_senha));
				$this->view->user = Zend_Auth::getInstance()->getIdentity();	
				}
			else{
				$_dados['cpf'] = $_cpf;
				$_dados['pwd'] = strtoupper($crypt->Encrypt($f->filter(strtoupper(trim($_POST['senha'])))));
				$_dados['senha'] = trim($f->filter($_POST['senha']));				
				
				if(strlen($_dados['cpf']) < 11)throw new Exception("O campo 'CPF' é obrigatório!\n\n");		
				elseif(!$_dados['senha'])throw new Exception("O campo 'Senha' é obrigatório!\n\n");
				elseif($_dados['pwd']!=$_confirm)throw new Exception("O campo 'Senha' e 'Confirme' devem ser iguais!\n\n");	
				
				$_dados['dataReg'] = date('m.d.Y H:i:s');
				$_dados['validade'] = date('m.d.Y');
				$_dados['ultAcesso'] = date('m.d.Y H:i:s');
				$_dados['formaPgto'] = 2;
				$this->id = $cadastro->insert($_dados);
				$this->view->message = "Cadastro foi gravado com sucesso!\n\n";
				$datareg = date('d/m/Y H:i:s');
				$dataval = $datareg;
				$novo = true;
				// grava boleto no financeiro
				Zend_Loader::loadClass('Financeiro');
				$financeiro = new Financeiro();
				
				//$taxa = ($this->view->user->formacao == 'EST. GRADUAÇÃO: FARMÁCIA') ? $this->view->taxaAnuidade/2 : $this->view->taxaAnuidade;
				$offSet = 4;
				if ($this->view->user->uf=='BA') $_cCusto=2;
				elseif ($this->view->user->uf=='MG') $_cCusto=3;
				elseif ($this->view->user->uf=='SP') $_cCusto=4;
				elseif ($this->view->user->uf=='RJ') $_cCusto=5;
				elseif ($this->view->user->uf=='PR') $_cCusto=6;
				elseif ($this->view->user->uf=='RN') $_cCusto=7;
				else $_cCusto=1;				
				$_data = array(
					'idCadastro' => $this->id,
					'numDoc' =>  sprintf('%05d%d%d', $this->id, date('y'), date('y')+1),
					'tipoDoc' => 2,
					'conta' => 41101001,
					'historico' => 'TAXA DE ASSOCIADO - RENOVAÇÃO',
					'valor' => $_dados['valor'], 
					'vcto' => date("m.d.Y", time() + ($offSet * 86400)),
					'total' => $_dados['valor'],
					'formaPgto' => 2,
					'boleto' => sprintf('%05d%d%d', $this->id, date('y'), date('y')+1),
					'status' => 2,
					'idCentroCusto' => $_cCusto,
					);
				
				$_idBoleto = $financeiro->insert($_data);			
				$this->view->message .= "Boleto gerado com sucesso.\n\nClique no link abaixo para impressão.\n\n";
				$this->view->boleto = "<a href='http://www.sbrafh.org.br/boletos/boleto.php?id=$_idBoleto' target='_boleto'>
					http://www.sbrafh.org.br/boletos/boleto.php?id=$_idBoleto</a>";
				}

			//$this->fc->authentique($_POST['email'], $_POST['senha']);
			//$this->view->user = Zend_Auth::getInstance()->getIdentity();

			$ocupacao = null;			
			if($_POST['atuacao']):
				$query = $this->fc->dbReader($this->db, "SELECT *, '0' AS status FROM areaAtuacao", true);
				foreach($query as $rs):
					for($x=0; $x < count($_POST['atuacao']); $x++):
						if($rs->id == $_POST['atuacao'][$x])
							$ocupacao[] =  $rs->descricao;
					endfor;
				endforeach;
				$ocupacao = join(', ', $ocupacao);
			endif;
			
			
			if($_novo):
				$valor = ($_POST['formacao'] == 'EST. GRADUAÇÃO: FARMÁCIA') ? $this->view->taxaAnuidade / 2 : $this->view->taxaAnuidade;
				$valor = number_format($valor, 2, ',', '.');
				$vcto = ($datareg == $dataval) ? date("d/m/Y", time() + ($offSet * 86400)) : $dataval;				
				
				$_sexo = ((int)$_POST['sexo']==1) ? 'Masculino' : 'Feminino'; 
				$eBody = "
				<html>
					<head>
						<title>http://www.sbrafh.org.br/Associados</title>\r\n
						<style>
							.table{width:600px;border:0;font:9pt Arial, Helvetica, Sans-Serif}
							.table th{color:#fff;background:#f90;padding:2px 4px}
							.table td{border:solid 1px silver}
							.table .label{text-align:right;color:#808080;padding:0 4px}
							.table caption{background:#369;color:#fff;font-size:16px;padding:4px}
							div{width:600px;font:9pt Arial, Helvetica, Sans-Serif}
						</style>
					</head>
					<body>
						<img src='topoEmail.jpg' border='0'><br />
						<table class='table' border='0' cellpadding='1' cellspacing='0'>
							<caption>Inscrição de Associado</caption>
							<tr>
								<th colspan='2'>Dados Pessoais</th>
							</tr>
							<tr>
								<td class='label'>Data:</td>
								<td>". date('d/m/Y') ."</td>
							</tr>
						   <tr>
						   	<td class='label'>Inscrição No.:</td>
						   	<td>". sprintf('%06d', $this->id) ."</td>
						   </tr>
						   <tr>
						   	<td class='label'>Nome Completo:</td>
						   	<td>{$_POST['nome']}</td>
						   </tr>
						   <tr>
						   	<td class='label'>C P F:</td>
						   	<td>{$_POST['cpf']}</td>
						   </tr>
						   <tr>
						   	<td class='label'>R. G.:</td>
						   	<td>{$_POST['rg']}</td>
						   </tr>
							<tr>
								<td class='label'>Endereço:</td>
								<td>{$_POST['ender']}, {$_POST['num']} {$_POST['compl']}</td>
							</tr>
							<tr>
								<td class='label'>Bairro:</td>
								<td>{$_POST['bairro']}</td>
							</tr>
							<tr>
								<td class='label'>Cidade:</td>
								<td>{$_POST['cidade']}</td>
							</tr>
	
							<tr>
								<td class='label'>Estado:</td>					
								<td>{$_POST['uf']}</td>
							</tr>
							<tr>
								<td class='label'>CEP:</td>					
								<td>{$_POST['cep']}</td>
							</tr>
							<tr>
								<td class='label'>CEP:</td>					
								<td>{$_POST['pais']}</td>
							</tr>
							<tr>
								<td class='label'>E-mail:</td>
								<td>{$_POST['email']}</td>
							</tr>
							<tr>
								<td class='label' nowrap>DDD Telefone Fixo:</td>
								<td>{$_POST['fone']}</td>
							</tr>
							<tr>
								<td class='label' nowrap>DDD Telefone Celular:</td>
								<td>{$_POST['fone2']}</td>
							</tr>
							<tr>
								<td class='label'>Data de Nascimento:</td>
								<td>{$_POST['dataNcto']}</td>
							</tr>
							<tr>
								<td class='label'>Sexo:</td>
								<td>$_sexo</td>
							</tr>
							<tr>
								<td class='label' nowrap>Natural de:</td>
								<td>{$_POST['naturalidade']}</td>
							</tr>
	
							<tr>
								<th colspan='2'>Dados Profissionais</th>
							</tr>	
							<tr>
								<td class='label' nowrap>Formação Profissional:</td>
								<td>{$_POST['formacao']}</td>
							</tr>
							<tr>
								<td class='label' nowrap>Áreas de Atuação:</td>
								<td>$ocupacao</td>
							</tr>
	
							<tr>
								<th colspan='2'>Dados Comerciais</th>
							</tr>
							<tr>
								<td class='label'>Instituição:</td>
								<td>{$_POST['instituicao']}</td>
							</tr>
							<tr>
								<td class='label'>DDD/Telefone #1:</td>
								<td>{$hfone1}</td>
							</tr>
							<tr>
								<td class='label'>DDD/Telefone #2:</td>
								<td>{$hfone2}</td>
							</tr>
							<tr>
								<td class='label'>Fax:</td>
								<td>{$hfax}</td>
							</tr>
	
							<tr>
								<th colspan='2'>Dados SBRAFH</th>
							</tr>
							<tr>
								<td class='label'>Data de Cadastro</td>
								<td>{$datareg}</td>
							</tr>
							<tr>
								<td class='label'>Valor da Anuidade R$:</td>
								<td>{$valor}</td>
							</tr>
							<tr>
								<td class='label'>Vencimento:</td>
								<td>{$vcto}</td>
							</tr>
						</table>
						<div style='width:600px;text-align:center;padding-top:10px'>
							<a href='http://www.sbrafh.org.br'>http://www.sbrafh.org.br</a>
						</div>
					</body>
				</html>";
							
				//Zend_Loader::loadClass('htmlMimeMail');
				$mail = new htmlMimeMail();
				$mail->setReturnPath('Webmaster <webmaster@sbrafh.org.br>');
				$mail->setFrom("SBRAFH/CadastroOnline <webmaster@sbrafh.org.br>");
				//$mail->setReplayTo('Silvia <silvia.horta@emerson.com>');
				$mail->setSubject(sprintf('%s - Inscrição de Associado Num. %06d', $_dados['nome'], $this->id));
					
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores
				
				$mail->send(array(sprintf('%s <%s>', $_dados['nome'], $_dados['email'])));  // envia copia para o usuario
				//echo sprintf("<br /><br /><br /><br />email enviado com sucesso para %s %s", $_dados['nome'], $_dados['email']);
				
				$mail->send(array('Atendimento <atendimento@sbrafh.org.br>'));  // envia copia para o usuario
				//echo "<br />email enviado com sucesso Atendimento atendimento@sbrafh.org.br";
				
				$mail->send(array('Webmaster-SBRAFH <webmaster@sbrafh.org.br>'));  // envia copia para o usuario
	
				
				
				$mailto = "Atendimento <atendimento@sbrafh.org.br>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@sbrafh.org.br');
				$mail->setFrom("SBRAFH - Sociedade Brasileira de Farmácia Hospitalar <webmaster@sbrafh.org.br>");
				$mail->setReplayTo(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
				$mail->setSubject("Cadastro de Associado SBRAFH");
				$mail->setCc(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
				$mail->setBcc('webmaster <webmaster@sbrafh.org.br>');
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$result = $mail->send(array($mailto));  // envia copia para o usuario
			endif;
			
			try{
				$db2 = Zend_Registry::get('db2');
				$db2->query("INSERT INTO mailing SET 
					nome = '{$_dados['nome']}, 
					categ = 3,
					email = strtolower('{$_dados['email']}'),
					uf = '{$_dados['uf']}, 
					dataReg = now(),
					status = 0");
				}
			catch(Exception $ex){
				// ignora
				}		
			}
		catch(Exception $ex){
			throw new Exception($ex->getMessage());
			}	
		}







	function rnvAction(){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/associados/login/');
			}
		$this->view->where = "Renovar Inscricao";
		$this->view->page = "associados/renovacao.phtml";
		$this->render('index');	
		}








	// cadastro de novo associado
	function newAction(){
		if ($this->_request->isPost()):
			try{
				$this->saveCadastro();
				$this->view->areaAtuacao = $_POST['atuacao'];
				//$this->hstAction($this->view->message);
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
		endif;
		
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY sigla", true);
		$this->view->atuacao = $this->fc->dbReader($this->db, "SELECT * FROM areaAtuacao", true);
		$this->view->where = "Cadastro de Novo Associado";
		$this->view->page = "associados/novoassociado.phtml";
		$this->render('index');	
		}



	// atualização de cadastro de associado
	function updAction(){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/associados/login/');
			}
		$this->view->where = "Atualização de Cadastro de Associado";
		$this->view->page = "associados/edit.phtml";
		if (!$this->_request->isPost()){
			$query = $this->db->fetchAll(sprintf("SELECT *, CONVERT(CHAR(10), dataNcto, 103) AS dataNcto FROM cadastro WHERE idCadastro=%d", $this->view->user->idCadastro));			
			$_POST = get_object_vars($query[0]);
			$t = explode('|', $_POST['hfone1']);
			if(count($t) > 1):
				$_POST['hfone1'] = $t[0];
				$_POST['hramal1'] = $t[1];
			endif;
			$t = explode('|', $_POST['hfone2']);
			if(count($t) > 1):
				$_POST['hfone2'] = $t[0];
				$_POST['hramal2'] = $t[1];
			endif;
			$t = explode('|', $_POST['hfax']);
			if(count($t) > 1):
				$_POST['hfax'] = $t[0];
				$_POST['hramal3'] = $t[1];
			endif;
			$t = explode('|', $_POST['areaAtuacao']);
			$this->view->areaAtuacao = explode('|', $_POST['areaAtuacao']);
			$crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);		   
			$_POST['senha'] = $crypt->Decrypt($_POST['pwd']);
			$_POST['confirme'] = $_POST['senha'];

			$this->view->estados = $this->fc->dbReader($this->db, "SELECT sigla, upper(estado) AS estado FROM estados ORDER BY sigla", true);
			$this->view->atuacao = $this->fc->dbReader($this->db, "SELECT *, '0' AS status FROM areaAtuacao", true);
			$count = 0;
			foreach($this->view->atuacao as $rs):
				for($x=0; $x < count($this->view->areaAtuacao); $x++):
					if($rs->id == $this->view->areaAtuacao[$x])
						$this->view->atuacao[$count]->status = 1;
				endfor;
				$count++; 
			endforeach;
			$this->render('index');
			}
		else {
			try{
				$this->saveCadastro();
				$this->hstAction($this->view->message);
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();		
				$this->view->estados = $this->fc->dbReader($this->db, "SELECT sigla, upper(estado) AS estado FROM estados ORDER BY sigla", true);
				$this->view->atuacao = $this->fc->dbReader($this->db, "SELECT *, '0' AS status FROM areaAtuacao", true);
				
				if($_POST['atuacao']):
					$count = 0;
					foreach($this->view->atuacao as $rs):
						for($x=0; $x < count($this->view->areaAtuacao); $x++):
							if($rs->id == $this->view->areaAtuacao[$x])
								$this->view->atuacao[$count]->status = 1;
						endfor;
						$count++; 
					endforeach;
				endif;
				$this->render('index');
				}
			}
		}





	// atualização de cadastro de associado
	function pwdAction(){
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('/associados/login/');
			}

		if ($this->_request->isPost()){
			//Zend_Loader::loadClass('Zend_Filter_StripTags');
			$f = new Zend_Filter_StripTags();
			$crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);		   
			$_pwd = strtoupper($crypt->Encrypt($f->filter(strtoupper(trim($_POST['pwd'])))));		

			if(!$_pwd)throw new Exception("O campo 'Senha' é obrigatório!\n\n");
			elseif($_POST['pwd'] != $_POST['confirm'])throw new Exception("O campo 'Senha' e 'Confirme' devem ser iguais!\n\n");
			
			$this->db->query("UPDATE cadastro SET pwd='$_pwd' WHERE idCadastro={$this->view->user->idCadastro}");
			$this->view->message = "Senha alterada com sucesso!\n\n";
			}
		$this->view->where = "Alteração de Senha";
		$this->view->page = "associados/pwd.phtml";
		$this->render('index');
		}






	function recoverAction(){
		if ($this->_request->isPost()){
			try{
				$email = trim($this->_request->getPost('email'));
				$cpf = ereg_replace("([^0-9])",'', $this->_request->getPost('cpf'));
						
				if (empty($email))
					throw new Exception("Por favor, digite seu email\n\n");
	
				if (empty($cpf))
					$this->view->message = 'Por favor digite seu CPF\n\n';				

				$this->fc->recoverPwd($email, $cpf);
				$this->view->message = "Seus dados para acesso foram enviados\npara o email informado.\n\n";
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		$this->view->where = "Recuperar dados para acesso";
		$this->view->page = "auth/recover.phtml";
		$this->render('index');
		}







	function loginAction(){
		$this->view->logged = false;
		if ($this->_request->isPost()){
			try{
				$email = trim($this->_request->getPost('email'));
				$pwd = strtoupper($this->_request->getPost('password'));
						
				if (empty($email))
					throw new Exception("Por favor, digite seu email\n\n");
	
				if (empty($pwd))
					throw new Exception("Por favor digite sua senha de usuário\n\n");
				$logged = $this->fc->authentique($email, $pwd);
				if($logged == 'Ok'){
					$this->view->user = Zend_Auth::getInstance()->getIdentity();
					$this->view->message = "Olá {$this->view->user->nome}\n\nSeja bem vindo !!!\n\n";
					$this->view->logged = true;
					$this->db->query("UPDATE cadastro SET ultAcesso=getDate() WHERE idCadastro={$this->view->user->idCadastro}");
					$this->_redirect('associados/hst');
					}
				else
					$this->view->message = $logged;
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		//$this->fc->debug($this->view->user);
		$this->view->where = "Login de Usuário Registrado";
		$this->view->page = "auth/login.phtml";
		$this->render('index');
		}	







	function regAction(){
		$this->view->where = "Regulamento para Associado";
		$this->view->page = "regulamento.phtml";
		$this->render('index');
		}	


	function mdxAction()
		{
		$this->view->message = '';
		$auth = Zend_Auth::getInstance();
		
		if ($this->_request->isPost()){
			$email = trim($this->_request->getPost('email'));
			$password = strtoupper($this->_request->getPost('password'));
			if (empty($email)){
				$this->view->message = 'Por favor digite seu email !!!';
				}
			elseif (empty($password)){
				$this->view->message = 'Por favor digite sua senha de usuário !!!';
				}
			else {
				$log = $this->fc->authentique($email, $password);
				if($log=='Ok'){
					$this->view->user = Zend_Auth::getInstance()->getIdentity();
					$this->view->message = "Olá {$this->view->user->nome}\n\nSeja bem vindo !!!\n\n";
					}
				else
					$this->view->message = $log;
				}
			}

		if (!$auth->hasIdentity()) {
			$this->view->page = "auth/login.phtml";
			$this->render('index');
			}
		elseif($this->view->user->status > 0){
			$this->view->where = "Associados";
			$this->view->page = "medex.phtml";
			$this->render('index');
			}
		else{
			$this->view->where = "Associados";
			$this->view->page = "associados/medexoff.phtml";
			$this->render('index');
			}		

		}





	function indexAction()
		{
		$this->view->where = "Associados";
		$this->view->page = "associados/home.phtml";
		$this->render('index');
		}


	function whyAction()
		{
		$this->view->where = "Associados/Porque ser associado";
		$this->view->page = "associados/why.phtml";
		$this->render('index');
		}


	function rlsAction()
		{
		$this->view->where = "Associados/Porque ser associado";
		$this->view->page = "associados/regras.phtml";
		$this->render('index');
		}


	function recadAction()
		{
		$this->view->where = "Associados/Recadastramento";
		$this->view->page = "associados/recadastramento.phtml";
		$this->render('index');
		}



	function contatoAction(){
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {			
			}		
		
		$this->view->page = "contato.phtml";
		$this->view->where .= "Contato";
		$this->render('index');
		}


	function logoutAction()
		{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/associados/login');
		}
	}
?>
