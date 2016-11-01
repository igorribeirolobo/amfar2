<?php
class CadastrosController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/cadastros", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->user = Zend_Auth::getInstance()->getIdentity();			

		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		$this->view->where .= ":: ";
		$this->view->tc = (int)$this->_request->getParam('tc',0);

		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->user = Zend_Auth::getInstance()->getIdentity();

		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();
		$this->view->message = null;

		$this->crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);

		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		$this->view->ord = $this->_request->getParam('ord',0);
		$this->view->urlDel = $this->view->baseAct . "/list/st/".$this->view->st."/del";
		$this->view->urlNav = $this->view->baseAct . "/list/st/".$this->view->st."/ord";
		$this->view->urlHst = $this->view->baseAct . "/hst/st/".$this->view->st."/ord/".$this->view->ord;
		Zend_Loader::loadClass('Zend_Paginator');
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$this->_redirect('auth/login');
				}
		}


	function listAction(){
		$this->view->where = "ADM/Cadastro: ";
		if(!$this->view->st)
			$this->view->where .= 'GERAL';
		elseif($this->view->st < 0)
			$this->view->where .= 'BLOQUEADOS';
		elseif($this->view->st == 0)
			$this->view->where .= 'ALUNOS';
		elseif($this->view->st == 1)
			$this->view->where .= 'ASSOCIADOS';
		elseif($this->view->st == 2)
			$this->view->where .= 'ISENTOS';
		elseif($this->view->st == 3)
			$this->view->where .= 'DIRETORIA';
		elseif($this->view->st == 4)
			$this->view->where .= 'FORNECEDORES';

		$filter = new Zend_Filter_StripTags();
		$this->view->message = '';

		if(empty($this->view->ord)) $this->view->ord = 'nome';
		
		
		$sql = sprintf("SELECT idCadastro, nome,
			CONVERT(CHAR(10), datareg, 103) AS datareg, 
			CONVERT(CHAR(10), validade, 103) AS validade,
			CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso			
			FROM cadastro WHERE (status=%d) AND (NOT(nome) IS NULL)
				ORDER BY %s", $this->view->st, $this->view->ord);

		if ($this->_request->isPost()){
			$cpf = ereg_replace("[' '-./ \t]",'',$this->_request->getPost('key'));
			$key = trim($filter->filter($this->_request->getPost('key')));
			$sql = "SELECT idCadastro, nome,
				CONVERT(CHAR(10), datareg, 103) AS datareg, 
				CONVERT(CHAR(10), validade, 103) AS validade,
				CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso	
					FROM cadastro WHERE (cpf='$cpf') OR (nome like '%$key%') ORDER BY nome";		
			}

		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view

		$this->view->urlDel = sprintf("%s/list/st/%d/del", $this->view->links, $this->view->st);
		$this->view->urlNav = sprintf("%s/list/st/%d/ord", $this->view->links, $this->view->st);
		$this->view->urlHst = sprintf("%s/hst/st/%d/ord/%s", $this->view->links, $this->view->st, $this->view->ord);
		$this->render();
		}





	function openAction(){
		//$this->view->message = '';
		//$this->view->where = "ADM/Cadastro - Detalhes";
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY estado", true);
		$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT *,
			CONVERT(CHAR(10), datareg, 103) AS datareg, 
			CONVERT(CHAR(10), validade, 103) AS validade,
			CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso,
			CONVERT(CHAR(10), atualizado, 103) AS atualizado
				FROM cadastro WHERE idCadastro={$this->view->id}"));
		$_POST['password'] = $this->crypt->Decrypt(	$_POST['password']);
		$this->getHelper('layout')->disableLayout();
		$this->render('form');
		}




	function saveAction(){
		//$this->view->where = "ADM/Cadastro - Detalhes";
		Zend_Loader::loadClass('Cadastro');
		$cadastro = new Cadastro();
		$_id = $this->view->id;
		// gravando o cadastro
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){

			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$_validade = implode("-", array_reverse(explode("/", $this->_request->getPost('validade'))));
			
			$_data = array(
				'nome' => trim($filter->filter($this->_request->getPost('nome'))),
				'cpf' => ereg_replace("([^0-9])",'',$this->_request->getPost('cpf')),
				'ender' => trim($filter->filter($this->_request->getPost('endereco'))),
				'num' => (int)$this->_request->getPost('num'),
				'compl' => trim($filter->filter($this->_request->getPost('compl'))),
				'bairro' => trim($filter->filter($this->_request->getPost('bairro'))),
				'cidade' => trim($filter->filter($this->_request->getPost('cidade'))),
				'uf' => trim($filter->filter($this->_request->getPost('uf'))),
				'cep' => ereg_replace("([^0-9])",'',$this->_request->getPost('cep')),
				'fone' => trim($filter->filter($this->_request->getPost('fone'))),
				'fone2' => trim($filter->filter($this->_request->getPost('fone2'))),
				'email' => trim($filter->filter($this->_request->getPost('email'))),
				'profissao' => trim($filter->filter($this->_request->getPost('profissao'))),
				'senha' => trim($filter->filter($this->_request->getPost('senha'))),
				'comentarios' => trim($filter->filter($this->_request->getPost('comentarios'))),
				'validade' => $_validade,
				'status' => (int)$this->_request->getPost('status'),
				);
			//$this->view->fc->debug($_data); die("idCadastro = $_id");
			try{
				if($_id > 0) {
					$_data['atualizado'] = date('Y-m-d H:i:s');
					$cadastro->update($_data, "idCadastro=$_id");
					$this->view->message = "Cadastro atualizado com sucesso!";
					}
				else{
					$_id = $cadastro->insert($_data);
					$this->view->message = ($_id > 0) ? "Cadastro foi gravado com sucesso!" : "Erro gravando novo cadastro:";
					}
				//$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY estado", true);			
				//$this->view->cadastro = $this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro=$_id");	
				//$this->view->cadastro->password = $this->crypt->Decrypt($this->view->cadastro->password);				
				}
			catch (Exception $ex) {
				$this->view->message = $ex->getMessage();
				}
			} // post

		$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT *,
			CONVERT(CHAR(10), datareg, 103) AS datareg, 
			CONVERT(CHAR(10), validade, 103) AS validade,
			CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso,
			CONVERT(CHAR(10), atualizado, 103) AS atualizado
				FROM cadastro WHERE idCadastro=$_id"));
		$_POST['password'] = $this->crypt->Decrypt(	$_POST['password']);
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY estado", true);
		$this->getHelper('layout')->disableLayout();
		$this->render('form');
		}




	function sendAction(){
		$_id = (int)$this->_request->getParam('id');	
		$result = $this->db->query("SELECT * FROM cadastro WHERE id=$_id")->fetchAll();
		$cadastro = $result[0];
		$this->view->cadastro = $cadastro;		
		
		$_valor = 100.00;
		$this->view->strValor = number_format($_valor, 2, ',','.');
		$_validade = $cadastro->validade;
		$this->view->strVcto = $this->view->fc->Ymd2dmY($_validade);
		$_limite = date("Y-m-d", time() + (10 * 86400));
		$this->view->strLimite = date("d/m/Y", time() + (10 * 86400));
		$_ref = "Renovação de Anuidade de Associado";
	
		if($_validade < date('Y-m-d'))
			$_msg="<p><br />Prezado(a) <b>$cadastro->nome</b>, a sua anuidade venceu no dia <b>$this->view->strVcto</b>.</p>
			<p>Renove-a para não perder os benefícios concedidos somente aos associados em dia com a sociedade.</p>
			<p>A taxa para renovação é de <b>R$ $this->view->strValor</b></p>";
		elseif($_validade == date('Y-m-d'))
			$_msg="<p><br />Prezado(a) <b>$cadastro->nome</b>, a sua anuidade está vencendo hoje(<b>$this->view->strVcto</b>).</p>
			<p>Renove-a para não perder os benefícios concedidos somente aos associados em dia com a sociedade.</p>
			<p>A taxa para renovação é de <b>R$ $this->view->strValor</b></p>";
		elseif($_validade <= $_limite)
			$_msg="<p><br />Prezado(a) <b>$cadastro->nome</b>, a sua anuidade vai vencer em (<b>$this->view->strLimite</b>).</p>
			<p>Renove-a para não perder os benefícios concedidos somente aos associados em dia com a sociedade.</p>
			<p>A taxa para renovação é de <b>R$ $this->view->strValor</b></p>";	
	
		$eBody = "<html>
		<head>
			<title>http://www.amfar.com.br</title>
			<style type=text/css>
				table{font:normal 12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana}	
				td{text-align:left;color:#000;padding:0 8px}	
				.rodape {text-align:center;padding:4px 0}
			</style>						
		</head>
		<body topmargin=0 leftmargin=0>
			<img src='topoEmail.jpg' border='0'>
			<table cellpadding='2' cellspacing='2' width='600px' border='0'>
				<tr>
					<td>
						$_msg
						<p>Caso ja tenha pago a taxa, envie-nos o comprovante de pagamento para que possamos dar baixa no sistema 
						e dessa forma, regularizar sua situação.</p>
						<p>Caso ainda não tenha pago, clique no link abaixo para que o 
						sistema possa gerar nova data de vencimento e você possa pagar o boleto em qualquer estabelecimento 
						bancário ou através da internet usando o PTE (código acima da ficha de compensação). Ou ainda se preferir 
						acesse o site, entre na seção Associados e após o login clique no link Renovar minha Inscrição.</p>
						<p style='text-align:center;margin:20px 0'>
							<a href='http://www.amfar.com.br/boletos/geraBoleto.php?uid=$_id&iid=0' target='_boleto'>
							http://www.amfar.com.br/boletos/geraBoleto.php?uid=$_id&iid=0</a>
						</p>
						<p>Atenciosamente,</p>
						<p>AMF - Setor de Cadastro</p>
					</td>
				</tr>
				<tr>
					<td class='rodape'><a href='http://www.amfar.com.br/'>AMF - Associação Mineira de Farmacêuticos</a></td>
				</tr>
			</table>
		</body>
		</html>";
				
		Zend_Loader::loadClass('htmlMimeMail');
		try {
			//echo "_imagePath=$_imagePath";				
			$mailto = "$cadastro->nome <$cadastro->email>";
			$mail = new htmlMimeMail();
			$mail->setReturnPath('webmaster@amfar.com.br');
			$mail->setFrom("AMF/Cadastro <webmaster@amfar.com.br>");
			$mail->setReplayTo("amfar@amfar.org.br");
			$mail->setSubject("AMF/Cadastro - Renovação de Inscrição");		
			// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
			$text = strip_tags($eBody);
			$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
			// cópia para o administrativo
			$result = $mail->send(array($mailto));  // envia copia para o usuario			
   	   $this->view->message = 'Email enviado com sucesso!';
   	   $this->view->message = "Link enviado com sucesso para:\n\n$cadastro->nome\n$cadastro->email\n\n";	
			}
		catch (Exception $e){
			$this->view->message = $mail->errors;		
			}

		$this->view->estados = $this->db->query("SELECT * FROM estados ORDER BY estado")->fetchAll();

		// verifica se libera o link renovação para o usuário
		$limite = date("Y-m-d", time() + (10 * 86400));
		$this->view->sendLink = ($this->view->cadastro->validade <= $limite) ? true : false;
		$this->render('form');
		}



	function hstAction(){
		$this->view->where = "ADM/Histórico de Pagamentos/Eventos";
		$_id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		$this->view->ord = $this->_request->getParam('ord',0);
		if(empty($this->view->ord)) $this->view->ord = 'nome';
		$this->view->cadastro = $this->fc->dbReader($this->db, "SELECT idCadastro, nome, email, fone, fone2,
			CONVERT(CHAR(10), datareg, 103) AS datareg, 
			CONVERT(CHAR(10), validade, 103) AS validade,
			CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso,
			CONVERT(CHAR(10), atualizado, 103) AS atualizado,			
			status FROM cadastro WHERE (idCadastro=$_id)");

		$this->view->boletos = $this->fc->dbReader($this->db, "SELECT *,  CONVERT(CHAR(10), dataLct, 103) AS dataLct
			FROM financeiro WHERE (idCadastro=$_id) ORDER BY pgto DESC", true);
		
		$this->view->eventos = $this->fc->dbReader($this->db, "SELECT ac.data, cs.titulo
			FROM (alunoscursos AS ac INNER JOIN cursos AS cs ON ac.idCurso=cs.idCurso)
				WHERE (ac.idAluno=$_id) AND (ac.status=1) ORDER BY ac.data DESC", true);

		$this->view->urlLst = $this->view->urlList . sprintf("/list/st/%d/ord/%s", $this->view->st, $this->view->ord);
		$this->render();
		}

	}
