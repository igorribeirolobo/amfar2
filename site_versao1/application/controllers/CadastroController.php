<?php
class CadastroController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/cadastro", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->user = Zend_Auth::getInstance()->getIdentity();			

		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		$this->db2 = Zend_Registry::get('db2');
		
		$this->view->where .= ":: ";
		$this->view->tc = (int)$this->_request->getParam('tc',0);
		$this->view->idCurso = (int)$this->_request->getParam('curso',0);

		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->session = SessionWrapper::getInstance();
		//$this->session->setSessVar('logged', 'user');
		//$this->channel = $this->session->getSessVar('channel');
		//$this->view->channel = $this->channel;
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();
		$this->view->message = null;		
		}


	function preDispatch()
		{

		}




	function indexAction(){	
		$this->view->where = "Associe - Ficha de Inscrição";
		
		
		if ($this->_request->isPost()){
			if($this->_request->getPost('btContinue'))
				$this->_redirect('cursos/inscricao/id/' . $this->view->idCurso);
			try{
				//$this->fc->debug($_POST); die();
 
				$_dados = array(
					'tipo'		=> (int)$_POST['tipo'],
					'nome'		=> ($this->view->user->idCadastro) ?  $this->view->user->nome : trim($this->filter->filter(strtoupper($_POST['nome']))),
					'cpf'			=>	($this->view->user->idCadastro) ?  $this->view->user->cpf : ereg_replace("([^0-9])",'',$_POST['cpf']),
					'rg'			=> trim($this->filter->filter(strtoupper($_POST['rg']))),
					'ender'		=> trim($this->filter->filter(strtoupper($_POST['ender']))),
					'num'			=> trim($this->filter->filter(strtoupper($_POST['num']))),
					'compl'		=> trim($this->filter->filter(strtoupper($_POST['compl']))),
					'bairro'		=> trim($this->filter->filter(strtoupper($_POST['bairro']))),
					'cep'			=> ereg_replace("([^0-9])",'',$_POST['cep']),
					'cidade'		=> trim($this->filter->filter(strtoupper($_POST['cidade']))),
					'uf'			=> trim($this->filter->filter(strtoupper($_POST['uf']))),
					'email'		=> trim($this->filter->filter(strtolower($_POST['email']))),
					'fone'		=> $_POST['fone'],
					'fone2'		=> $_POST['fone2'],
					'profissao'	=> trim($this->filter->filter(strtoupper($_POST['profissao']))),
					'senha'		=> trim($this->filter->filter(strtolower($_POST['senha']))),
					);
				
				$labelCPF = ($_dados['tipo']==1) ? 'CNPJ' : 'CPF';
				$labelRG = ($_dados['tipo']==1) ? 'Inscr. Estadual' : 'RG';
				$labelFone2 = ($_dados['tipo']==1) ? 'Fone 2' : 'Celular';
				$labelProf = ($_dados['tipo']==1) ? 'Contato' : 'Profissão';
				
				
				if(!$_dados['nome'])throw new Exception("O campo 'Nome' é obrigatório !\n\n");
				elseif(!$_dados['cpf'])throw new Exception("O campo '$labelCPF' é obrigatório !\n\n");
				elseif(!$_dados['rg'])throw new Exception("O campo '$labelRG' é obrigatório !\n\n");
				elseif(!$_dados['ender'])throw new Exception("O campo 'Endereço' é obrigatório !\n\n");
				elseif(!$_dados['num'])throw new Exception("O campo 'Número' é obrigatório !\n\n");
				elseif(!$_dados['bairro'])throw new Exception("O campo 'Bairro' é obrigatório !\n\n");
				elseif(!$_dados['cidade'])throw new Exception("O campo 'Cidade' é obrigatório !\n\n");
				elseif(!$_dados['uf'])throw new Exception("O campo 'Estado' é obrigatório !\n\n");
				elseif(!$_dados['email'])throw new Exception("O campo 'E-mail' é obrigatório !\n\n");
				elseif(!$_dados['fone'])throw new Exception("O campo 'Fone' é obrigatório !\n\n");
				elseif(!$_dados['fone2'])throw new Exception("O campo '$labelFone2' é obrigatório !\n\n");
				elseif(!$_dados['profissao'])throw new Exception("O campo '$labelProf' é obrigatório !\n\n");
				elseif(!$_dados['senha'])throw new Exception("O campo 'Senha' é obrigatório !\n\n");
				elseif($_dados['senha'] != $_POST['confirme'])throw new Exception("O campo 'Senha {$_dados['senha']} e Confirme {$_POST['confirme']}' devem ser iguais !\n\n");
				
				$_userIP = $_SERVER["REMOTE_ADDR"];
				$fone = ereg_replace("([^0-9])",'',$_POST['fone']);
				$fone2 = ereg_replace("([^0-9])",'',$_POST['fone2']);
				$cep = ereg_replace("([^0-9])",'',$_POST['cep']);
				if(!$fone && !$fone2 && !$cep){
					$this->db->query("INSERT INTO blacklist(ip) VALUES('$_userIP')");
					die("Acesso indevido !\n\n");
					}
				
				// verifica a blacklist
				$blocked = $this->fc->dbReader($this->db, "SELECT ip FROM blacklist WHERE ip='$_userIP'")->ip;
				if($blocked){
					die("Acesso indevido !\n\n");
					}
				
				Zend_Loader::loadClass('Cadastro');
				$cadastro = new Cadastro();

				if($this->view->user->idCadastro){
					$cadastro->update($_dados, sprintf("idCadastro=%d", $this->view->user->idCadastro));
					$this->view->message = "Seu cadastro foi alterado com sucesso\n\n";
					}
				else {				
					$this->view->idCadastro = (int)$this->fc->dbReader($this->db, "SELECT idCadastro FROM cadastro WHERE cpf='{$_dados['cpf']}'")->idCadastro;
					if($this->view->idCadastro > 0){
						throw new Exception("Já existe um cadastro para este $labelCPF.\n\nDuplicidade de $labelCPF não é permitida.\n\nPor favor efetue o login e selecione Alteração Cadastral.\n\nObrigado.");
						}				
					$cadastro->insert($_dados);	
					$eBody="
					<style>
						#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
						#econtato caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
						#econtato td{padding:4px;border:solid 1px silver;color:#444}
						#econtato .label{text-align:right;color:#444;background:#D9E7F8}
						#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
						#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
						#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
					</style>
					<img src='topoEmail.jpg' border='0'/>	
					<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>
						<caption>Cadastro/Ficha de Inscrição</caption>
						<tr>
							<td class='label'>Nome:</td>
							<td><b>{$_dados['nome']}</b></td>
						</tr>
						<tr>
							<td class='label'>$labelCPF:</td>
							<td><b>" . $this->fc->formatCPf($_dados['cpf']) . "</b></td>
						</tr>
						<tr>
							<td class='label'>$labelRG:</td>
							<td><b>{$_dados['rg']}</b></td>
						</tr>
						<tr>
							<td class='label'>Endereço:</td>
							<td><b>{$_dados['ender']}, {$_dados['num']}&nbsp;&nbsp;{$_dados['compl']}</b></td>
						</tr>
						<tr>
							<td class='label'>Bairro:</td>
							<td><b>{$_dados['bairro']}</b></td>
						</tr>
						<tr>
							<td class='label'>Cidade:</td>
							<td><b>{$_dados['cidade']}</b></td>
						</tr>
						<tr>
							<td class='label'>Estado:</td>
							<td><b>{$_dados['uf']}</b></td>
						</tr>
						<tr>
							<td class='label'>CEP:</td>
							<td><b>" . $this->fc->formatCEP($_dados['cep']) . "</b></td>
						</tr>
						<tr>
							<td class='label'>DDD/Fone:</td>
							<td><b>{$_dados['fone']}</b></td>
						</tr>
						<tr>
							<td class='label'>DDD/$labelFone2:</td>
							<td><b>{$_dados['fone2']}</b></td>
						</tr>
						<tr>
							<td class='label'>$labelProf:</td>
							<td>{$_dados['profissao']}</td>
						</tr>
						<tr>
							<td class='tfooter' colspan='2'>
								<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
							</td>
						</tr>
					</table>";
			
			
					Zend_Loader::loadClass('htmlMimeMail');
	
					//echo "_imagePath=$_imagePath";				
					$mailto = "secretaria <secretaria@amfar.com.br>";
					$mail = new htmlMimeMail();
					
					$mail->setReturnPath('webmaster@amfar.com.br');
					$mail->setFrom("AMF/Agenciamento <webmaster@sbrafh.org.br>");
					$mail->setReplayTo(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
					$mail->setBcc('webmaster@amfar.com.br');
					$mail->setSubject("Cadastro/Ficha de Inscrição");		
					// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
					$text = strip_tags($eBody);
					$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
					
					$mail->send(array($mailto));  // envia copia para o usuario			
					$this->view->message .= "Sua ficha cadastral foi enviada com sucesso.\n\n";
					$_POST = null;
					}	
				
				Zend_Db_Table::setDefaultAdapter($this->db2);
				Zend_Loader::loadClass('Mailing');
				$mailing = new Mailing();
				$_mailing = array(
					'nome' => $_dados['nome'],
					'email' => $_dados['email'],
					'uf' => $_dados['uf'],
					'dataReg' => date('Y-m-d H:i:s'),
					);
				try{
					$mailing->insert($_mailing);
					}
				catch(Exception $ex){
				
					}
				if($this->channel){
					//die("this->channel = $this->channel");
					$this->_redirect($this->channel);
					}
									
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		if($this->view->user->idCadastro){
			$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro={$this->view->user->idCadastro}"));
			$_POST['confirme'] = $_POST['senha'];
			}
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY uf", true);
		}



	// deve ser chamado pelo cCurl
	function incompanyAction(){
		//$this->getHelper('viewRenderer')->setNoRender();	
		$_dados = array(
			'empresa'	=> trim($this->filter->filter(strtoupper($_POST['empresa']))),
			'ender'		=> trim($this->filter->filter(strtoupper($_POST['ender']))),
			'compl'		=> trim($this->filter->filter(strtoupper($_POST['compl']))),
			'bairro'		=> trim($this->filter->filter(strtoupper($_POST['bairro']))),
			'cidade'		=> trim($this->filter->filter(strtoupper($_POST['cidade']))),
			'uf'			=> trim($this->filter->filter(strtoupper($_POST['uf']))),
			'cep'			=> $_POST['cep'],
			'responsavel'=> trim($this->filter->filter(strtoupper($_POST['responsavel']))),
			'email'		=> trim($this->filter->filter(strtolower($_POST['email']))),
			'fone'		=> $_POST['fone'],
			'cel'			=> $_POST['cel'],
			'area'		=> trim($this->filter->filter(strtoupper($_POST['area']))),
			'descricao'	=> trim($this->filter->filter(strtolower($_POST['descricao']))),
			);
		if(!$_dados['empresa'])throw new Exception("O campo 'Empresa' é obrigatório !\n\n");
		elseif(!$_dados['ender'])throw new Exception("O campo 'Endereço' é obrigatório !\n\n");
		elseif(!$_dados['bairro'])throw new Exception("O campo 'Bairro' é obrigatório !\n\n");
		elseif(!$_dados['cidade'])throw new Exception("O campo 'Cidade' é obrigatório !\n\n");
		elseif(!$_dados['uf'])throw new Exception("O campo 'Estado' é obrigatório !\n\n");
		elseif(!$_dados['responsavel'])throw new Exception("O campo 'Responsável' é obrigatório !\n\n");
		elseif(!$_dados['email'])throw new Exception("O campo 'E-mail' é obrigatório !\n\n");
		elseif(!$_dados['fone'])throw new Exception("O campo 'DDD/Fone Comercial' é obrigatório !\n\n");
		elseif(!$_dados['cel'])throw new Exception("O campo 'DDD/Celular' é obrigatório !\n\n");
		elseif(!$_dados['area'])throw new Exception("O campo 'Área de Atuação' é obrigatório !\n\n");
		elseif(!$_dados['descricao'])throw new Exception("O campo 'Descrição da Solicitação' é obrigatório !\n\n");
				
		$fone = ereg_replace("([^0-9])",'',$_POST['fone']);
		$cep = ereg_replace("([^0-9])",'',$_POST['cep']);
		if(!$fone && !$cep)
			throw new Exception("Acesso indevido !\n\n");
		
		Zend_Loader::loadClass('InCompany');
		$incompany = new InCompany();
		$idCadastro = $incompany->insert($_dados);	

		Zend_Db_Table::setDefaultAdapter($this->db2);
		Zend_Loader::loadClass('Mailing');
		$mailing = new Mailing();
		$_mailing = array(
			'nome' => $_dados['nome'],
			'email' => $_dados['email'],
			'uf' => $_dados['uf'],
			'dataReg' => date('Y-m-d H:i:s'),
			);
		try{
			$mailing->insert($_mailing);
			}
		catch(Exception $ex){			
			}

		$eBody="
		<style>
			#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
			#econtato caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
			#econtato td{padding:4px;border:solid 1px silver;color:#444}
			#econtato .label{text-align:right;color:#444;background:#D9E7F8}
			#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
			#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
			#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
		</style>
		<img src='topoEmail.jpg' border='0'/>	
		<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>
			<caption>Contato In-Company</caption>
			<tr>
				<td align=right class=boxBorder>Empresa:</td>
				<td class=boxBorder><b>{$_dados['empresa']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Endereço:</td>
				<td class=boxBorder><b>{$_dados['ender']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Compl:</td>
				<td class=boxBorder><b>{$_dados['compl']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Bairro:</td>
				<td class=boxBorder><b>{$_dados['bairro']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Cidade:</td>
				<td class=boxBorder><b>{$_dados['cidade']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Estado:</td>
				<td class=boxBorder><b>{$_dados['uf']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>CEP:</td>
				<td class=boxBorder><b>{$_dados['cep']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Responsável:</td>
				<td class=boxBorder><b>{$_dados['responsavel']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>E-mail:</td>
				<td class=boxBorder><b>{$_dados['email']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>DDD/Telefone:</td>
				<td class=boxBorder><b>{$_dados['fone']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>DDD/Celular:</td>
				<td class=boxBorder><b>{$_dados['cel']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder nowrap>Área de Atuação:</td>
				<td class=boxBorder><b>{$_dados['area']}</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Descrição:</td>
				<td class=boxBorder>{$_dados['descricao']}</td>
			</tr>
		</table>";


		Zend_Loader::loadClass('htmlMimeMail');

		//echo "_imagePath=$_imagePath";				
		$mailto	= "Secretaria <secretaria@amfar.com.br>";
		$mailto2 = "Patricia <patricia@amfar.com.br>";
		$mailto3 = "Webmaster <webmaster@amfar.com.br>";
		$mail = new htmlMimeMail();
		
		$mail->setReturnPath('webmaster@amfar.com.br');
		$mail->setFrom("AMF/In-Company <webmaster@sbrafh.org.br>");
		$mail->setReplayTo(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
		$mail->setBcc('webmaster@amfar.com.br');
		$mail->setSubject("Cadastro/In-Company");		
		// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
		$text = strip_tags($eBody);
		$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
		
		$mail->send(array($mailto));  // envia copia para o usuario
		$mail->send(array($mailto2));  // envia copia para o usuario
		$mail->send(array($mailto3));  // envia copia para o usuario			
		echo "Ok";
		exit;
		}




	function listAction(){
		$this->view->where = "ADM/Lista de Alunos";
		if(empty($this->view->ord)) $this->view->ord = 'nome';
		$sql = sprintf("SELECT idCadastro, nome, datareg, validade, ultAcesso
			FROM cadastro WHERE (status=%d) ORDER BY %s", $this->view->st, $this->view->ord);

		if($this->view->st < 0)
			$this->view->where .= 'Bloqueados';
		elseif($this->view->st == 0)
			$this->view->where .= 'Inativos';
		elseif($this->view->st == 1)
			$this->view->where .= 'Ativos';
		elseif($this->view->st == 2)
			$this->view->where .= 'Isentos';
		elseif($this->view->st == 3)
			$this->view->where .= 'Diretoria';

		
		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view


		$this->view->title .= " - <small style='font-weight:normal'>". count($this->view->dbtable ) ." Registro(s) localizado(s)";
		$this->view->urlDel = $this->view->baseAct . sprintf("/list/st/%d/del", $this->view->st);
		$this->view->urlNav = $this->view->baseAct . sprintf("/list/st/%d/ord", $this->view->st);
		$this->view->urlHst = $this->view->baseAct . sprintf("/hst/st/%d/ord/%s", $this->view->st, $this->view->ord);
		$this->render();
		}




	}
