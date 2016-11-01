<?php
class RestritoController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/restrito", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->user = Zend_Auth::getInstance()->getIdentity();			

		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;

		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->tc = (int)$this->_request->getParam('tc',0);
		
		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->session = SessionWrapper::getInstance();
		$this->session->setSessVar('channel', 'user');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();
		$this->view->message = null;		
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
		//$this->fc->debug($auth); die();
		if (!$auth->hasIdentity() || (int)$this->view->user->idCadastro==0) {
			$this->_redirect('auth/login');
			}
		}




	function indexAction(){	
		$this->view->where = "Acesso Restrito";
		if ($this->_request->isPost()){
			try{
				//$this->fc->debug($_POST); die();
 
				$_dados = array(
					'tipo'		=> (int)$_POST['tipo'],
					'nome'		=> trim($this->filter->filter(strtoupper($_POST['nome']))),
					'cpf'			=>	ereg_replace("([^0-9])",'',$_POST['cpf']),
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
				
				$this->view->idCadastro = (int)$this->fc->dbReader($this->db, "SELECT idCadastro FROM cadastro WHERE cpf='{$_dados['cpf']}'")->idCadastro;
				if($this->view->idCadastro > 0){
					throw new Exception("Já existe um cadastro para este $labelCPF.\n\nDuplicidade de $labelCPF não é permitida.\n\nPor favor efetue o login e selecione Alteração Cadastral.\n\nObrigado.");
					}				
				
				Zend_Loader::loadClass('Cadastro');
				$cadastro = new Cadastro();
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
				$mail->setFrom("AMF/Agenciamento <webmaster@amfar.com.br>");
				$mail->setReplayTo(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
				$mail->setCc("{$_dados['nome']} <{$_dados['email']}>");
				$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject("Cadastro/Ficha de Inscrição");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message .= "Sua ficha cadastral foi enviada com sucesso.\n\n";
				$_POST = null;					
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY uf", true);
		}



	function mime_content_type ($f){
		return trim (exec('file -bi'. escapeshellarg($f)));
		}


	function downloadsAction(){	
		$this->view->where = "Restrito/Seção Downloads";
		$download = $this->_request->getParam('f',0);
		$this->view->folder = sprintf("..%s/public/downloads", $this->view->baseUrl);
		if($download){
			$this->getHelper('layout')->disableLayout();
			$this->view->fullpath = "{$this->view->folder}/$download";
			$this->view->mimetype = $this->mime_content_type($this->view->fullpath);					
			$this->render('reader');
			}
		}


	function boletosAction(){
		$hoje = date('Y.m.d');
		$sql="SELECT idFinanceiro, numdoc, historico, vcto, pgto, total FROM financeiro WHERE
			(idCadastro={$this->view->user->idCadastro}) AND (status=2) ORDER BY vcto";		
		//echo $sql;
		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(16);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Restrito/Histórico de Pagamentos";
		}

	function informativosAction(){	
		$this->view->where = "Restrito/Informativos";
		$this->view->folder = sprintf("..%s/public/informativos", $this->view->baseUrl);
		$info = $this->_request->getParam('f',0);
		if($info){
			$this->getHelper('layout')->disableLayout();
			$this->view->fullpath = "{$this->view->folder}/$info";
			$this->view->mimetype = $this->mime_content_type($this->view->fullpath);					
			$this->render('reader');
			}
		}
		
		
	function pendenciatccAction(){
		$this->view->id = (int)$this->fc->dbReader($this->db, "SELECT id FROM pendenciaTCC WHERE idCadastro={$this->view->user->idCadastro}")->id;
		if($this->view->id == 0)
			$this->view->message = "Desculpe mas seu cadastro não está relacionado na lista para rematrícula.";
		
		if ($this->_request->isPost()){
			try{
				//$this->fc->debug($_POST);
				$curso = $this->fc->dbReader($this->db, "SELECT * FROM pendenciaTCC WHERE id={$_POST['id']}");
				//$this->fc->debug($curso);
	
				$found = (int)$this->fc->dbReader($this->db, "SELECT idAlunoCursos FROM alunosCursos WHERE idCurso={$this->view->id}")->idAlunoCursos;
				//echo "found = $found";
				if($found > 0){
					throw new Exception("Já existe um registro de rematrícula em seu nome.\n\n");
					}
				
				Zend_Loader::loadClass('AlunosCursos');
				$inscricao = new AlunosCursos();
				$orientador = $_POST['orientador'];
				if(empty($orientador))
					throw new Exception("Por favor, selecione quem vai indicar o Orientador.\n\n");
				
				$_dados = array(
					'idCurso' => $this->view->id,
					'idAluno' => $this->view->user->idCadastro,
					'valorCurso' => 490,
					'parcelas' => 1,
					'valorParcelas' => 490,
					'obs' => 'TRABALHO DE CONCLUSÃO DE CURSO /TCC - Orientador: '.  $orientador,
					);
				$idInscricao = $inscricao->insert($_dados);
	
				Zend_Loader::loadClass('Financeiro');
				$financeiro = new Financeiro();
	
				$_boleto = array(
					'idCadastro' => $this->view->user->idCadastro,
					'idCurso' => $this->view->id,
					'dataLct' => date('m.d.Y'),
					'tipoDoc' => 2,
					'conta' => '41102001',
					'historico' => 'TRABALHO DE CONCLUSÃO DE CURSO /TCC',
					'valor' => 490,
					'total' => 490,
					'status' => 2,
					'idCentroCusto' => 1,
					'numDoc' => sprintf('%04d%03d01', $this->view->user->idCadastro, $this->view->id),
					'vcto' => '04.18.2013',
					'parcela' => 1,				
					);
				$this->view->boleto = $financeiro->insert($_boleto);

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
					<caption>AMF  TRABALHO DE CONCLUSÃO DE CURSO /TCC</caption>
					<tr>
						<td>
							Prezado(a) <b>$curso->nome</b>, nesta data recebemos o seu pedido de rematrícula para 
							o <b>TRABALHO DE CONCLUSÃO DE CURSO /TCC</b><br /><br />
							O Orientador será indicado pela(o): $orientador<br /><br />
							Link para impressão do boleto: <a href='http://www.amfar.com.br/boletos/boleto.php?id={$this->view->boleto}' target='_boleto'>http://www.amfar.com.br/boletos/boleto.php?id={$this->view->boleto}</a>
						</td>
					</tr>
					<tr>
						<td class='tfooter' colspan='2'>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
						</td>
					</tr>
				</table>";
		
		
				Zend_Loader::loadClass('htmlMimeMail');

				//echo "_imagePath=$_imagePath";				
				$mailto = "Maria das Dores <mariadasdores@amfar.com.br>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF/Cursos <webmaster@amfar.com.br>");
				$mail->setReplayTo(sprintf('%s <%s>', $_dados['nome'], $_dados['email']));
				$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject("Rematrícula");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				
				$mail->send(array("Secretaria <secretaria@amfar.com.br>"));  // envia copia para o usuario
				$mail->send(array("$query->nome <$query->email>"));  // envia copia para o usuario
				$this->view->message = "Sua rematrícula foi efetuada com sucesso";
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		}


		
	function sendmailAction(){
		$eBody="
		<style>
			#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
			#econtato caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
			#econtato td{padding:4px;border:solid 1px silver;color:#444}
			#econtato .label{text-align:right;color:#444;background:#D9E7F8}
			#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
			#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
			#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
			#econtato p{text-align:justify}
		</style>
		<img src='topoEmail.jpg' border='0'/>	
		<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>
			<caption>CURSO DE ESPECIALIZAÇÃO <i>LATO SENSU</i><br />							
					PENDÊNCIA TRABALHO DE CONCLUSÃO DE CURSO /TCC
				</caption>
			<tr>
				<td>							
					<p>Os alunos reprovados ou que não apresentaram TCC, caso queiram,poderão se rematricular na disciplina 
					Elaboração de TCC até o dia 18/04/2013.</p>
					
					<p><b>Orientação TCC opções:</b></p> 
						<ol>
							<li>O aluno poderá indicar o orientador que deverá ser aprovado pela coordenação do curso e unidade acadêmica certificadora.</li>
							<li>A AMF indicará o orientador. Nesse, caso a linha de pesquisa será definida pelo orientador</li>
						</ol>
					<p><b>Observação:</b> O aluno deverá escolher a sua opção no ato da rematricula.<br />
					A confirmação do nome / contato do orientador e regras gerais serão encaminhadas para o email do aluno até o dia 22/04/2013.</p>
					
					<p><b>Data da apresentação do TCC:</b> 							
					A apresentação definitiva será agendada para o período de 24 a 29 de junho de 2013. Caso o aluno tenha condições a apresentação poderá ser antecipada.</p>
					
					
					<p><b>Valor da Disciplina Elaboração de TCC:</b> R$ 490,00</p>
					
					<p><b>Forma de pagamento:</b> Boleto bancário gerado no ato da rematricula no site www.amfar.com.br/site/. 							
					Após o pagamento do boleto, favor digitalizar e enviar para o email: mariadasdores@amfar.com.br</p>
					
					<p><b>Informações:</b>  mariadasdores@amfar.com.br 9866-9504</p>
					
					<p><b> Nota importante:</b> a regularização na disciplina de Elaboração do TCC não inclui a resolução de 
					pendências em outras disciplinas, frequência e /ou situação financeira.</p>
					
					<p>Acesse <a href='http://www.amfar.com.br/site/'>http://www.amfar.com.br/site/</a>, clique no ícone Alunos/Login. 
					Caso não lembre a senha cadastrada, digite seu CPF e o email para o qual gostaria que fosse enviado os dados para login.<br />
					Após efetuar o login, clique no link <b>PENDÊNCIA DE TCC</b> na seção <b>RESTRITO</b> e siga as informações da tela.</p>						
												
					<p><b>Maria das Dores Graciano Silva<br />
					Coordenação de Curso</p>
				</td>
			</tr>
			<tr>
				<td class='tfooter'>
					<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
				</td>
			</tr>
		</table>";


		Zend_Loader::loadClass('htmlMimeMail');

		//echo "_imagePath=$_imagePath";	
		$query = $this->fc->dbReader($this->db, "SELECT nome, email FROM pendenciaTCC WHERE (email <> '') ORDER BY nome", true);
		//$this->fc->debug($query);
					
		//$mailto = "Maria das Dores <mariadasdores@amfar.com.br>";
		$mail = new htmlMimeMail();
		
		$mail->setReturnPath('webmaster@amfar.com.br');
		$mail->setFrom("AMF/Cursos <webmaster@amfar.com.br>");
		$mail->setReplayTo('Secretaria <secretaria@amfar.com.br>');
		$mail->setCc('secretaria@amfar.com.br');
		$mail->setBcc('webmaster@amfar.com.br');
		$mail->setSubject("PENDÊNCIA TRABALHO DE CONCLUSÃO DE CURSO /TCC");		
		// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
		$text = strip_tags($eBody);
		$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
		
		//$mail->send(array("Secretaria <secretaria@amfar.com.br>"));  // envia copia para o usuario
		foreach($query as $rs):
			$sendto = "$rs->nome <$rs->email>";
			echo "$sendto<br />";
			$mail->send(array($sendto));  // envia copia para o usuario
		endforeach;
		$this->view->message = "Sua rematrícula foi efetuada com sucesso";				
		die();
		}	
		
	}
