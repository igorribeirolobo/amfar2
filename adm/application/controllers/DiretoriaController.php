<?php
class DiretoriaController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseCss = $this->_request->getBaseUrl() ."/public/styles";
		$this->view->baseImg = $this->_request->getBaseUrl() ."/public/images";
		$this->view->baseEdit = $this->_request->getBaseUrl() ."/diretoria/edit/id";
		$this->view->baseSave = $this->_request->getBaseUrl() ."/diretoria/save/id";
		$this->view->baseDel = $this->_request->getBaseUrl() ."/diretoria/del/id";
		$this->view->user = Zend_Auth::getInstance()->getIdentity();	
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Loader::loadClass('FuncoesUteis');
		$this->view->fc = new FuncoesUteis();
		$this->fc = new FuncoesUteis();
		$this->view->fc = $this->fc;
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();
		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->id = (int)$this->_request->getParam('id',0);
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$this->_redirect('auth/login');
				}
		}
	

	function listAction(){
		$sql = "SELECT * FROM cadastro WHERE status=3 ORDER by nome";		
		$this->view->diretoria = $this->fc->dbReader($this->db, $sql, true);
		$this->view->where = "Diretoria/Membros";
		}


	function editAction(){
		$_POST  = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro={$this->view->id}"));
		$this->view->where = "Diretoria/Edição de Cadastro";			
		$this->render('form');
		}
		
	function addAction(){
		$this->view->agenda  = $this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE id IS NULL");			
		$this->view->where = "Diretoria / Novo Registro";
		$this->render('form');
		}



	function saveAction(){
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			try{			
				$_curriculo = trim(str_replace("'",'"', $this->_request->getPost('curriculo')));
				$dados = array(
					'atualizado' => date('m.d.Y H:i:s'),
					'curriculo' => $_curriculo
					);


				//$this->fc->debug($dados); 
				//$this->fc->debug($_FILES); //die();
				// redimensiona a foto se vier
				if ($_FILES['foto']['name']!=''){ 	
					$ext = explode('.',$_FILES['foto']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if(!stristr('jpg,jpeg', $ext))
						throw new Exception("O formato da foto deve ser em JPG.\n\n");
					
					$image = sprintf('%s.%s', uniqid(), $ext);
					$_target = sprintf("../site/public/diretoria/original_$image", $this->view->baseUrl);
					$_thumb= sprintf("../site/public/diretoria/$image", $this->view->baseUrl);
					
					if(!move_uploaded_file($_FILES['foto']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo para o servidor !\n\n");

					$_source = $_target;
						
					list($width_orig, $height_orig) = getimagesize($_source); // pega o tamanho original da imagem
					$fator_w = 120 / $width_orig;
					$fator_h = 132 / $height_orig;
					
					$dim = array(	
						'width' => 120,
						'height' => 132,
	               'orig_width' => $width_orig,
						'org_height' => $height_orig,
						'fator_w' => $fator_w,
						'fator_h' => $fator_h,
						'tmp_width' => round($width_orig * $fator_w),
						'tmp_height' => round($height_orig * $fator_w),
						);

					// Resample
					 // cria imagem no width x height desejado
					$image_t = imagecreatetruecolor($dim['width'], $dim['height']);					
					// captura a imagem original
					$_src = imagecreatefromjpeg($_target);
					
					// cria imagem no width desejado x altura proporcional
					$image_p = imagecreatetruecolor($dim['tmp_width'], $dim['tmp_height']);
					imagecopyresampled($image_p, $_src, 0, 0, 0, 0, $dim['tmp_width'], $dim['tmp_height'], $dim['orig_width'], $dim['org_height']);
					
					// faz novamente o rezise na largura e altura desejada - o excedente na altura é descartado
					imagecopyresampled($image_t, $image_p, 0, 0, 0, 0, $dim['width'], $dim['height'], $dim['width'], $dim['height']);					
					
					// grava a com 100% de qualidade
					imagejpeg($image_t, $_thumb, 100);
					@unlink($_target);					
					}
				$dados['foto'] = $image;
				//die();
				Zend_Loader::loadClass('Cadastro');
				$cadastro = new Cadastro();
				$cadastro->update($dados, "idCadastro={$this->view->id}");
				$this->view->message = "Registro alterado com sucesso!";
				}
			catch(Exception $ex){
				$this->view->message = sprintf("Err: %s", $ex->getMessage());
				}
			}
		$_POST  = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro={$this->view->id}"));
		$this->render('form');		
		}

	}
