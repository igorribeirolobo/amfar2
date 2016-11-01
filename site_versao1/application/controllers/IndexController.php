<?php
class IndexController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/index", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->user = Zend_Auth::getInstance()->getIdentity();			

		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		$this->db2 = Zend_Registry::get('db2');

		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);

		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->session = SessionWrapper::getInstance();
		$this->session->setSessVar('channel', 'user');
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();		
		}


	function preDispatch()
		{
//		$auth = Zend_Auth::getInstance();
//			if (!$auth->hasIdentity()) {
//				$this->_redirect('auth/login');
//				}
		}




	function indexAction(){	
		$this->view->where = "Página Inicial";
		$this->view->noticias = $this->fc->dbReader($this->db, "SELECT TOP 5 idNoticia, CONVERT(CHAR(10),data,103) AS strdata, titulo, subTitulo, thumb, link, url FROM noticias ORDER BY data DESC", true);
		$this->view->eventos = $this->fc->dbReader($this->db, "SELECT TOP 5 id, CONVERT(CHAR(10),data,103) AS strdata, titulo FROM eventos ORDER BY data DESC", true);
		}



	function noticiasAction(){
		if($this->view->id){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM noticias WHERE idNoticia=" . $this->view->id;
			$this->view->news = $this->fc->dbReader($this->db, $sql);				
			$this->view->where = "/AMF Preview-News";
			$this->getHelper('layout')->disableLayout();
			$this->render('viewnews');		
			}
		else{		
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
				FROM noticias
					ORDER BY data DESC";
			
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(6);		
			$paginator->setPageRange(7);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view					
			$this->view->where .= "/AMF Notícias";
			}
		}



	function galeriaAction(){	
		$this->view->where = "Galeria de Fotos";
		}


	function mime_content_type ($f){
		return trim (exec('file -bi'. escapeshellarg($f)));
		}



	function agenciamentoAction(){	
		$this->view->where = "Agenciamento - Estreitando Relações";
		if ($this->_request->isPost()){
			try{
				//$this->fc->debug($_POST);
				//$this->fc->debug($_FILES);
				$_dados = array(
					'nome'		=> trim($this->filter->filter(strtoupper($_POST['nome']))),
					'cpf'			=>	ereg_replace("([^0-9])",'',$_POST['cpf']),
					'email'		=> trim($this->filter->filter(strtolower($_POST['email']))),
					'fone'		=> ereg_replace("([^0-9])",'',$_POST['fone']),
					'celular'			=> ereg_replace("([^0-9])",'',$_POST['celular']),
					'perfil'		=> trim($this->filter->filter(strtoupper($_POST['perfil']))),
					'obs'			=> trim($this->filter->filter(strtoupper($_POST['obs']))),
					);
				
				if(!$_dados['nome'])throw new Exception("O campo 'Nome Completo' é obrigatório !\n\n");
				elseif(!$_dados['cpf'])throw new Exception("O campo 'CPF' é obrigatório !\n\n");
				elseif(!$_dados['email'])throw new Exception("O campo 'E-mail' é obrigatório !\n\n");
				elseif(!$_dados['fone'])throw new Exception("O campo 'Fone' é obrigatório !\n\n");
				elseif(!$_dados['celular'])throw new Exception("O campo 'Celular' é obrigatório !\n\n");
				elseif(!$_dados['perfil'])throw new Exception("O campo 'Área de Atuação' é obrigatório !\n\n");
				elseif(!$_FILES['userfile']['name'])throw new Exception("O Currículo em DOC ou PDF é obrigatório !\n\n");
			
				$temp = explode('.', $_FILES['userfile']['name']);
				$ext = strtolower($temp[count($temp)-1]);
				if(!stristr('pdf,doc,docx,odt', $ext))
					throw new Exception("Erro: O arquivo enviado é inválido.\nEnvie somente no formato *.doc(x) ou *.pdf !\n\n");
				
				$arquivo = sprintf('%s.%s', date('YmdHis'), $ext);				
				
				if(!move_uploaded_file($_FILES['userfile']['tmp_name'], "../../curriculos/$arquivo"))
					throw new Exception("Erro transferindo arquivo para o servidor !\n\n");
				$_dados['curriculo'] = $arquivo;
				Zend_Loader::loadClass('Curriculos');
				$curriculo = new Curriculos();
				$curriculo->insert($_dados);

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
					<caption>Agenciamento/Envio de Currículo</caption>
					<tr>
						<td class='label'>Nome:</td>
						<td><b>{$_dados['nome']}</b></td>
					</tr>
					<tr>
						<td class='label'>CPF:</td>
						<td><b>" . $this->fc->formatCPf($_dados['cpf']) . "</b></td>
					</tr>
					<tr>
						<td class='label'>E-mail:</td>
						<td><b>{$_dados['email']}</b></td>
					</tr>
					<tr>
						<td class='label'>DDD/Fone:</td>
						<td><b>{$_dados['fone']}</b></td>
					</tr>
					<tr>
						<td class='label'>DDD/Celular:</td>
						<td><b>{$_dados['celular']}</b></td>
					</tr>
					<tr>
						<td class='label'>Área de Atuação:</td>
						<td>{$_dados['perfil']}</td>
					</tr>
					<tr>
						<td class='label'>Observações:</td>
						<td>{$_dados['obs']}</td>
					</tr>
					<tr>
						<td class='label'>Anexo:</td>
						<td>{$_dados['curriculo']}</td>
					</tr>
					<tr>
						<td class=tfooter colspan=2>
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
				$mail->setSubject("Agenciamento/Envio de Currículo");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				
				$anexo = "../../curriculos/$arquivo";							
				$type = $this->mime_content_type($anexo);
				$attachment = $mail->getFile($anexo);
				$mail->addAttachment($attachment, $arquivo, $type);
		
				$result = $mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message .= "Seus dados e seu curriculo foram\nenviados com sucesso para o servidor.\n\n";
				$_POST = null;					

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
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		}





	function eventosAction(){
		$sql="SELECT *, CONVERT(CHAR(10), data, 103) AS fData 
			FROM eventos WHERE(status=1) ORDER BY data DESC";		
		
		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Agenda Anual de Eventos";
		}



	function vagasAction(){
		$this->view->page = "index/vagas.phtml";
		$this->view->where .= "Oportunidades de Empregos";
		$this->render('index');
		}

	
	
	
	function cursosAction(){
		$sql="SELECT idCurso, CONVERT(CHAR(10), inicio, 103) AS fInicio, titulo, url, status 
			FROM cursos WHERE (ativo=1) AND (status={$this->view->st})
				ORDER BY inicio DESC, titulo";
		$this->view->cursos = $this->fc->dbReader($this->db, $sql, true);		
		$this->view->where = ($this->view->st) ? "AMF - Curss de Especialização" : "AMF - Cursos de Atualização";
		}



	function linksuteisAction(){
		$sql="SELECT * FROM links ORDER BY nome";
		$dados = $this->fc->dbReader($this->db, $sql, true);
		
		$page = intval($this->_getParam('page', 1));		
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Links Úteis da Área de Saúde";
		//$this->fc->debug($this->view->paginator); die();
		}



	function legislacaoAction(){
		$sql="SELECT * FROM legislacao ORDER BY datapub DESC";
		$dados = $this->fc->dbReader($this->db, $sql, true);
		
		$page = intval($this->_getParam('page', 1));		
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Links Úteis da Área de Saúde";
		//$this->fc->debug($this->view->paginator); die();
		}



	function newsAction(){
		if($this->view->id){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM noticias WHERE id=" . $this->view->id;
			$this->view->news = $this->fc->dbReader($this->db, $sql);				
			$this->view->where .= "/SBRAFH Preview-News";
			if(stristr($this->view->news->link,'.phtml'))
				$this->view->page = $this->view->news->link;
			elseif(stristr($this->view->news->link,'.pdf')){
				$this->view->page = "index/news.phtml";
				$this->view->arquivo = sprintf('%s/public/%s', $this->view->baseUrl, $this->view->news->link);
				$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
					FROM noticias WHERE (CONVERT(CHAR(10), data, 102) >= '2012.04.01')
						ORDER BY data DESC";
				
				$dados = $this->fc->dbReader($this->db, $sql, true);			
				$page = intval($this->_getParam('page', 1));
				$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
				$paginator->setItemCountPerPage(16);		
				$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
				$paginator->setCurrentPageNumber($page);	// Seta a página atual		
				$this->view->paginator = $paginator;	// Passa o paginator para a view			
				
				$this->view->where .= "/SBRAFH News";	
				}
			else
				$this->view->page = "preview-news.phtml";
			$this->render('index');
			}
		else{
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
				FROM noticias WHERE (CONVERT(CHAR(10), data, 102) >= '2012.04.01')
					ORDER BY data DESC";
			
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(16);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view			
			
			$this->view->where .= "/SBRAFH News";
			$this->view->page = "index/news.phtml";
			$this->render('index');
			}
		}




	function contatoAction(){
		if ($this->_request->isPost()){
			try{
				$_dados = array(
					'nome'		=> trim($this->filter->filter(strtoupper($_POST['nome']))),
					'fone'		=>	trim($this->filter->filter($_POST['fone'])),
					'email'		=> trim($this->filter->filter(strtolower($_POST['email']))),
					'assunto'	=> trim($this->filter->filter(strtoupper($_POST['assunto']))),
					'mensagem'	=> trim($this->filter->filter($_POST['mensagem'])),
					);
				
				if(!$_dados['nome'])throw new Exception("O campo 'Nome' é obrigatório !\n\n");
				elseif(!$_dados['fone'])throw new Exception("O campo 'Fone' é obrigatório !\n\n");
				elseif(!$_dados['email'])throw new Exception("O campo 'E-mail' é obrigatório !\n\n");
				elseif(!$_dados['assunto'])throw new Exception("O campo 'Assunto' é obrigatório !\n\n");
				elseif(!$_dados['mensagem'])throw new Exception("O campo 'Mensagem' é obrigatório !\n\n");
				
				Zend_Loader::loadClass('Contato');
				$contato = new Contato();
				$contato->insert($_dados);

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
				<img src='topoEmail.jpg' border='0' />	
				<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>
					<caption>Contato</caption>
					<tr>
						<td class='label'>Nome:</td>
						<td><b>{$_dados['nome']}</b></td>
					</tr>
					<tr>
						<td class='label'>DDD/Fone:</td>
						<td><b>{$_dados['fone']}</b></td>
					</tr
					<tr>
						<td class='label'>E-mail:</td>
						<td><b>{$_dados['email']}</b></td>
					</tr>
					<tr>
						<td class='label'>Assunto:</td>
						<td>{$_dados['assunto']}</td>
					</tr>
					<tr>
						<td class='label'>Mensagem:</td>
						<td>{$_dados['mensagem']}</td>
					</tr>
					<tr>
						<td class=tfooter colspan=2>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
						</td>
					</tr>
				</table>";
		
		
				Zend_Loader::loadClass('htmlMimeMail');

				//echo "_imagePath=$_imagePath";				
				$mailto = "Secretaria <secretaria@amfar.com.br>";
				//$mailto = "Webmaster <webmaster@amfar.com.br>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF/Contato <webmaster@sbrafh.org.br>");
				$mail->setReplayTo('Secretaria <secretaria@amfar.com.br>');
				//$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject("Contato");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message = "Seu email foi eenviados com sucesso.\n\nCaso o mesmo necessite de resposta,\nresponderemos assim que possível.\n\nObrigado\n\n";
				$_POST = null;	

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

				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}					
			}		
		
		$this->view->where = "Contato";
		}


	}
