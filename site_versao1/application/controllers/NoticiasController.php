<?php
class NoticiasController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/noticias", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->db = Zend_Registry::get('db');

		$this->view->id = (int)$this->_request->getParam('id',0);
		Zend_Loader::loadClass('Zend_Paginator');
		}




	function indexAction(){	
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




	function previewAction(){
		$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
			FROM noticias WHERE idNoticia=" . $this->view->id;
		$this->view->news = $this->fc->dbReader($this->db, $sql);				
		$this->view->where = "/AMF Preview-News";
		$this->getHelper('layout')->disableLayout();
		//$this->render('preview');
		}



	}
?>
