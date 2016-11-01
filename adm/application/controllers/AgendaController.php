<?php
class AgendaController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseCss = $this->_request->getBaseUrl() ."/public/styles";
		$this->view->baseImg = $this->_request->getBaseUrl() ."/public/images";
		$this->view->baseEdit = $this->_request->getBaseUrl() ."/agenda/edit/id";
		$this->view->baseSave = $this->_request->getBaseUrl() ."/agenda/save/id";
		$this->view->baseDel = $this->_request->getBaseUrl() ."/agenda/del/id";

		$this->view->user = Zend_Auth::getInstance()->getIdentity();	

		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);

		Zend_Loader::loadClass('FuncoesUteis');
		$this->view->fc = new FuncoesUteis();
		$this->fc = new FuncoesUteis();
		$this->view->fc = $this->fc;

		Zend_Loader::loadClass('Zend_Filter_StripTags');
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
		$sql = "SELECT * FROM agenda ORDER by data DESC";		
		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Agenda da Diretoria/Lista";		
		}


	function editAction(){
		$_id = (int)$this->_request->getParam('id',0);
		$this->view->agenda  = $this->fc->dbReader($this->db, "SELECT * FROM agenda WHERE id=$_id");
		$this->view->where = "Agenda da Diretoria/Alteração";			
		$this->render('form');
		}
		
	function addAction(){
		$this->view->agenda  = $this->fc->dbReader($this->db, "SELECT * FROM agenda WHERE id IS NULL");			
		$this->view->where = "ADM/Agenda da Diretoria / Novo Registro";
		$this->render('form');
		}

	function delAction(){
		$_id = (int)$this->_request->getParam('id',0);
		$this->db->query("DELETE FROM agenda WHERE id=$_id");
		$this->_redirect('agenda/list');
		}


	function saveAction(){
		$_id = (int)$this->_request->getParam('id',0);
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){			
			//$filter = new Zend_Filter_StripTags();
			$_userId = (int)$this->_request->getPost('userId');
			$_descricao = trim(str_replace("'","´",$this->_request->getPost('descricao')));
			$_data = $this->fc->dmY2msSql($this->_request->getPost('data'));
			$data = array(
				'data' => $_data,
				'userId' => $_userId,				
				'descricao' => $_descricao,
				);
			Zend_Loader::loadClass('Agenda');
			$agenda = new Agenda();
			try{
				if($_id){
					$agenda->update($data, "id=$_id");
					//$this->view->message = "Registro alterado com sucesso!";
					$this->_redirect('agenda/list');
					}
				else{
					$data['datareg'] = date('Y-m-d H:i:s');
					$_id = $agenda->insert($data);
					//$this->view->message = "Novo Registro incluído com sucesso!";
					$this->_redirect("agenda/list");
					}
				}
			catch(Exception $ex){
				$this->view->message = sprintf("Err: %s", $ex->getMessage());
				}
			}
		$this->view->agenda  = $this->fc->dbReader($this->db, "SELECT * FROM agenda WHERE id=$_id");
		$this->render('form');		
		}

	}
