<?php
class AuthController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/auth", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->session = SessionWrapper::getInstance();
		$this->channel = $this->session->getSessVar('channel');
		$this->idCurso = (int)$this->_request->getParam('curso',0);
		}




	function loginAction(){
		if($this->view->user->idCadastro){
			if($this->channel)
				$this->_redirect('/'.$this->channel);
			else
				$this->_redirect('/restrito');
			}
			
		if ($this->_request->isPost()){
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$f = new Zend_Filter_StripTags();
			
			try{
		
				if($this->_request->getPost('btLogin')):
					$email = trim($f->filter($this->_request->getPost('email')));
					$pwd = trim($this->_request->getPost('password'));			
					
					if (empty($email))
						throw new Exception("Por favor digite seu email !!!/n/n");
					elseif(empty($pwd))
						throw new Exception("Por favor digite sua senha de usuário !!!\n\n");

					Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
					$db = Zend_Registry::get('db');
					$authAdapter = new Zend_Auth_Adapter_DbTable($db);	
					$authAdapter->setTableName('cadastro');					
					$authAdapter->setIdentityColumn('email');
					$authAdapter->setCredentialColumn('senha');
					// Seta as credenciais para a autenticação
					$authAdapter->setIdentity($email);
					$authAdapter->setCredential($pwd);
					// Faça a autenticação
					$auth = Zend_Auth::getInstance();	  
					$result = $auth->authenticate($authAdapter);									
					if ($result->isValid()) {
						// success: store database row to auth's storage
						// system. (Not the password though!)
						$data = $authAdapter->getResultRowObject(null,'senha');
						$auth->getStorage()->write($data);
						$db->query("UPDATE cadastro SET ultAcesso=getDate() WHERE idCadastro={$data->idCadastro}");
						if($this->idCurso > 0){
							//die("this->channel = $this->channel");
							//$this->_redirect($this->channel);
							$this->_redirect('/cadastro/index/curso/' . $this->idCurso);
							}
						else
							$this->_redirect('/restrito');
						}
					throw new Exception("Usuário não localizado !!!\n\n");
				endif;

				// se passou é recover password
				$cpf = ereg_replace("([^0-9])",'',$_POST['cpf']);
				$email = trim($f->filter($this->_request->getPost('email2')));
				$query = $this->fc->dbReader($this->db, "SELECT nome, email, senha FROM cadastro WHERE cpf='$cpf'");
				if(!$query->email)
					throw new Exception("Desculpe, seu CPF/CNPJ não foi localizado !!!\n\n");
					
				if(empty($query->senha)){
					// criar senha para o cadastro
					$query->senha = sprintf("%08x", time());
					$db->query("UPDATE cadastro SET senha='$query->senha' WHERE cpf='$cpf'");
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
					<caption>Cadastro/Dados para acesso ao site</caption>
					<tr>
						<td>Prezado(a) <b>$query->nome</b>, você solicitou que fosse enviado os dados para acesso restrito ao nosso site. 
							Seguem abaixo os dados.<br /><br />
							E-mail: <b>$query->email</b><br />
							Senha: <b>$query->senha</b>
						</td>
					</tr>
					<tr>
						<td><b>Caso não tenha feito essa solicitação, por favor, apague este e-amail.</td>
					</tr>
					<tr>
						<td class='tfooter'>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
						</td>
					</tr>
				</table>";
		
		
				Zend_Loader::loadClass('htmlMimeMail');

				//echo "_imagePath=$_imagePath";				
				$mailto = "$query->nome <$query->email>";
				$mail = new htmlMimeMail();				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF/Cadastro <webmaster@sbrafh.org.br>");
				$mail->setReplayTo('Webmaster <webmaster@amfar.com.br>');
				//$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject("Dados para acesso ao site");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message = "Os dados para acesso foram enviados com sucesso\npara $email.\n\n";
				$_POST = null;	
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			} // if ($this->_request->isPost())
		$this->view->where = "Login de Usuário Cadastrado";
		}	





	function admloginAction(){
		$crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);

		if ($this->_request->isPost()){
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$f = new Zend_Filter_StripTags();
			
			try{
				$login = trim(strtolower($this->_request->getPost('password')));			
				$pwd = $crypt->Encrypt($login);
				if(empty($pwd))
					throw new Exception("Por favor digite sua senha de usuário !!!\n\n");
			
				//die("pwd=4951415142414173<br />pwd=$pwd");
				
				Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
				//$db = Zend_Registry::get('db');
				$authAdapter = new Zend_Auth_Adapter_DbTable($this->db);	
				$authAdapter->setTableName('usuarios');					
				$authAdapter->setIdentityColumn('password');
				$authAdapter->setCredentialColumn('password');
				// Seta as credenciais para a autenticação
				$authAdapter->setIdentity($pwd);
				$authAdapter->setCredential($pwd);
				// Faça a autenticação
				$auth = Zend_Auth::getInstance();	  
				$result = $auth->authenticate($authAdapter);								
				if ($result->isValid()) {
					// success: store database row to auth's storage
					// system. (Not the password though!)
					$data = $authAdapter->getResultRowObject(null,'password');
					if($data->dominio != 'amfar')
						throw new Exception("Acesso não permitido !!!\n\n");
					$auth->getStorage()->write($data);
					$this->db->query("UPDATE usuarios SET ultAcesso=getDate() WHERE id={$data->id}");
					$this->_redirect('/adm');
					}
				else
					throw new Exception("Usuário não localizado !!!\n\n");			
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			} // if ($this->_request->isPost())
		$this->view->where = "Login de Usuário Administrativo";
		}	






	function indexAction()
		{
		$this->_redirect('/');
		}




	function logoutAction()
		{
		Zend_Auth::getInstance()->clearIdentity();
		$this->session->emptySess();
		$this->_redirect('auth/login');
		}
	}
?>
