<?php
class AboutController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/about", $this->view->baseUrl);			
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		Zend_Loader::loadClass('Zend_Paginator');
		}


	function preDispatch()
		{
//		$auth = Zend_Auth::getInstance();
//			if (!$auth->hasIdentity()) {
//				$this->_redirect('auth/login');
//				}
		}




	function indexAction(){	
		$this->view->where = "Sobre nós";
		}



	function historicoAction(){	
		$this->view->where .= ":: Sobre a SBRAFH/Histórico";
		$this->view->page = "about/historico.phtml";
		$this->render('index');
		}


	function diretoriaAction(){
		$this->view->where = "Diretoria Atual - Gestão 2013-2014";
		}


	function agendaAction(){
		$this->view->where = "Agenda da Diretoria";
		$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS fdata FROM agenda ORDER BY data DESC";
		
		$dados = $this->fc->dbReader($this->db, $sql, true);			
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		}


	function exdirAction(){	
		$this->view->where .= ":: Sobre a SBRAFH/Galeria de Diretores";
		$this->view->page = "about/exdiretoria.phtml";
		$this->render('index');
		}


	function localAction(){	
		$this->view->where = "Nossa Localização";
		}


	function missaoAction(){	
		$this->view->where .= ":: Sobre a SBRAFH/Nossao Visão/Visão";
		$this->view->page = "about/missao.phtml";
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
		$this->_redirect('/');
		}

	}
