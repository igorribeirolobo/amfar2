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
		

		$this->session = SessionWrapper::getInstance();
		Zend_Loader::loadClass('Zend_Paginator');
		}


	function preDispatch()
		{		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			$this->_redirect('auth/login');
			}
		}






	function indexAction(){
		$page = intval($this->_getParam('page', 1));
		$del = (int)$this->_request->getParam('del',0);
		if($del){
			$this->db->query("DELETE FROM noticias WHERE idNoticia=$del");
			//$this->view->message = "Registro excluído com sucesso !!!\n\n";
			$this->_redirect("noticias/index/page/$page");
			}
		
		
		$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
			FROM noticias
				ORDER BY data DESC";
		
		$dados = $this->fc->dbReader($this->db, $sql, true);			
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(16);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view			
		
		$this->view->where .= "Notícias do Setor";
		}



	function excluirAction(){
		$this->db->query("DELETE FROM noticias WHERE idNoticia={$this->view->id}");
		$this->view->message = "Registro excluído com sucesso!\n\n";
		$this->_redirect('noticias/index');
		}




	function openAction(){
		if ($_POST['btSend']) {
			//$this->fc->debug($_POST); 
			//$this->fc->debug($_FILES);
			try{
				$f = new Zend_Filter_StripTags();		
				if ($_FILES['userFile']['name']!=''){ 	
					$ext = explode('.',$_FILES['userFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if($ext != 'jpg')
						throw new Exception("O formato do arquivo de Imagem deve ser em JPG.\n\n");
					
					$image = sprintf('%s.%s', uniqid(), $ext);
					$thumb = sprintf('%s.%s', uniqid(), $ext);
					$_target = "../site/public/news/$image";
					$_thumb = "../site/public/news/thumbs/$thumb";
					if(!move_uploaded_file($_FILES['userFile']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo de Imagem!\n\n");

					// Resample
					 // cria imagem no width x height desejado
					list($width_orig, $height_orig) = getimagesize($_target); // pega o tamanho original da imagem
					$fator_w = 250 / $width_orig;
					$fator_h = 187 / $height_orig;
					
					$dim = array(	
						'width' => 250,
						'height' => 187,
                  'orig_width' => $width_orig,
						'org_height' => $height_orig,
						'fator_w' => $fator_w,
						'fator_h' => $fator_h,
						'tmp_width' => round($width_orig * $fator_w),
						'tmp_height' => round($height_orig * $fator_w),
						);  
										

					if($dim['tmp_width'] > $dim['width']){
						$dim['tmp_width'] = $dim['width'];
						$dim['tmp_height'] = round($height_orig * $fator_w);					
						}

					if($dim['tmp_height'] > $dim['height']){
						$dim['tmp_height'] = $dim['height'];
						$dim['tmp_width'] = round($width_orig * $fator_h);					
						}
										
					$image_t = imagecreatetruecolor($dim['width'], $dim['height']);
					$_src = imagecreatefromjpeg($_target);
					
					// cria imagem no width desejado x altura proporcional
					$image_p = imagecreatetruecolor($dim['tmp_width'], $dim['tmp_height']);
					imagecopyresampled($image_p, $_src, 0, 0, 0, 0, $dim['tmp_width'], $dim['tmp_height'], $dim['orig_width'], $dim['org_height']);
					
					// faz novamente o rezise na largura e altura desejada - o excedente na altura é descartado
					imagecopyresampled($image_t, $image_p, 0, 0, 0, 0, $dim['width'], $dim['height'], $dim['width'], $dim['height']);					
					
					// grava a com 100% de qualidade
					imagejpeg($image_t, $_target, 100);
					
					
					
					unset($dim);

					list($width_orig, $height_orig) = getimagesize($_target); // pega o tamanho original da imagem
					$fator_w = 125 / $width_orig;
					$fator_h = 94 / $height_orig;
					
					$dim = array(	
						'width' => 125,
						'height' => 94,
                  'orig_width' => $width_orig,
						'org_height' => $height_orig,
						'fator_w' => $fator_w,
						'fator_h' => $fator_h,
						'tmp_width' => round($width_orig * $fator_w),
						'tmp_height' => round($height_orig * $fator_w),
						);
						
					//$this->fc->debug($dim);   
										
					if($dim['tmp_height'] < $dim['height']){
						//echo "tmp_height < height";
						$dim['tmp_height'] = $dim['height'];
						$dim['tmp_width'] = round($width_orig * $fator_h);					
						}
					
					if($dim['tmp_width'] < $dim['width']){
						//echo "tmp_width < width";
						$dim['tmp_width'] = $dim['width'];
						$dim['tmp_height'] = round($height_orig * $fator_w);					
						}

					//$this->fc->debug($dim);
					//$ratio_orig = $width_orig/$height_orig; // ratio					


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
					}
				
				if ($_FILES['arquivo']['name']!=''){ 	
					$ext = explode('.',$_FILES['arquivo']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if(!stristr('jpg|gif|png|htm|html|pdf', $ext))
						throw new Exception("O formato do arquivo de Imagem deve ser em JPG, GIF, PNG, PDF, HTML ou PHTML.\n\n");
					
					$filename = basename($_FILES['arquivo']['tmp_name']) . '.' . $ext;

					$dir = getcwd();
					//$_target = sprintf("/public%s/application/views/scripts/news/$filename", $this->view->baseUrl);
					$_target = sprintf("../site/public/news/$filename", $this->view->baseUrl);
						
					if(!move_uploaded_file($_FILES['arquivo']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo $filename para $_target! - dir=[$dir]\n\n");
					$_POST['link'] = "http://www.amfar.com.br/site/public/news/$filename";		
					}

				Zend_Loader::loadClass('Noticias');
				$news = new Noticias();
				$_dados = array(
					'data' => $this->fc->dmY2msSql($_POST['data']),
					'titulo' => trim($f->filter($_POST['titulo'])),
					'subTitulo' => trim($f->filter($_POST['subTitulo'])),
					'fotografo' => trim($f->filter($_POST['fotografo'])),
					'materia' => trim($_POST['materia']),
					'fonte' => trim($f->filter($_POST['fonte'])),
					'legenda' => trim($f->filter($_POST['legenda'])),
					'creditos' => trim($f->filter($_POST['credito'])),
					'link' => $_POST['link'],
					);
					
				if($image):
					$_dados['foto'] = $image;
					$_dados['thumb'] = $thumb;
				endif;				

				if(!$_dados['titulo'])throw new Exception("O campo 'Título' é obrigatório!\n\n");
				elseif(!$_dados['subTitulo'])throw new Exception("O campo 'Sub-Titulo' é obrigatório!\n\n");
				elseif(!$_dados['materia'] && !$_dados['link'])throw new Exception("O campo 'Matéria' é obrigatório!\n\n");

				if($this->view->id > 0){
					$news->update($_dados, "idNoticia=". $this->view->id);
					$this->view->message = "Registro atualizado com sucesso!\n\n";
					}
				else{	
					$this->view->id = $news->insert($_dados);
					$this->view->message = "Novo Registro inserido com sucesso!\n\n";				
					}
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		if(!$this->view->id){
			$this->view->id=0;
			$_POST['pasta'] = 'news';
			$_POST['restrito'] = 1;
			$_POST['status'] = 1;			
			}
		else{
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM noticias WHERE idNoticia=" . $this->view->id;
			$_POST = get_object_vars($this->fc->dbReader($this->db, $sql));						
			}
		$this->view->action = sprintf('%s/open/id/%d', $this->view->links, $this->view->id);
		$this->view->uniqid = uniqid();
		$this->session->setSessVar('uniqid', $this->view->uniqid);
		$this->getHelper('layout')->disableLayout();
		}

	}
?>
