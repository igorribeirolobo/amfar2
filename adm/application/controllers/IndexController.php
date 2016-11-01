<?php
class IndexController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/index", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->db = Zend_Registry::get('db');
		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		$this->view->rg = $this->_request->getParam('rg',0);
		$this->view->dt = $this->_request->getParam('dt',0);
		$this->view->idBoleto = (int)$this->_request->getParam('boleto',0);
		$this->view->view = (int)$this->_request->getParam('view',0);
		Zend_Loader::loadClass('Zend_Paginator');
		}


	function preDispatch()
		{		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity() || $this->view->user->dominio != 'admAmfar') {
			$this->_redirect('auth/login');
			}
		}


	function indexAction()
		{

		}

	}
?>
