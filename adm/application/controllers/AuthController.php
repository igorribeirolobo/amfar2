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
		}





	function loginAction(){
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
					if($data->dominio != 'admAmfar')
						throw new Exception("Acesso não permitido !!!\n\n");
					$auth->getStorage()->write($data);
					$this->db->query("UPDATE usuarios SET ultAcesso=getDate() WHERE id={$data->id}");					
					$this->view->user = Zend_Auth::getInstance()->getIdentity();
					$this->view->message = "Olá $data->nome, seja bem vindo(a).\n\nLogado(a) com sucesso!\n\n";
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
		$this->_redirect('auth/login');
		}
	}
?>
