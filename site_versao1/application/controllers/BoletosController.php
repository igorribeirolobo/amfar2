<?php
	
class BoletosController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseCss = $this->_request->getBaseUrl() ."/public/styles";
		$this->view->baseImg = $this->_request->getBaseUrl() ."/public/images";
		$this->view->ImgBoletos = $this->_request->getBaseUrl() ."/public/boletos";
		$this->view->baseAct = $this->_request->getBaseUrl() ."/boletos";
		
		
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		$this->view->db = $this->db ; 
		
		Zend_Loader::loadClass('FuncoesUteis');
		$this->fc = new FuncoesUteis();
		$this->view->fc = $this->fc;
		$this->view->message = '';
		$this->_helper->layout->setLayout("layout2");
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$this->_redirect('auth/login');
				}
		}


	function listAction(){		
		$_st = (int)$this->_request->getParam('st',0);
		$_ord = $this->_request->getParam('ord',0);
		if(empty($_ord)) $_ord = 'vcto DESC';
		$filtro = ($_st==0) ? "(CONVERT(CHAR(10), pgto, 102) ='1900.01.01')" : "(CONVERT(CHAR(10), pgto, 102)  <> '1900.01.01')";
		
		$this->view->dbtable = $this->db->query("SELECT * FROM boletos WHERE ($filtro) ORDER BY $_ord")->fetchAll();
		//$this->fc->debug($this->view->dbtable); die();
		$this->view->title = ":: Lista de Boletos ";
		$this->view->title .= ($_st==0) ? 'Pendentes' : 'Pagos';

		$this->view->title .= " - <small style='font-weight:normal'>". count($this->view->dbtable ) ." Registro(s) localizado(s)";
		$this->view->urlDel = $this->view->baseAct . "/del/st/$_st/ord/$_ord/id";
		$this->view->urlNav = $this->view->baseAct . "/list/st/$_st/ord";
		$this->view->baseEdit = $this->view->baseAct ."/open/st/$_st/ord/$_ord/id";
		$this->render();
		}





	function printAction(){		
		$_curso = (int)$this->_request->getParam('curso',0);
		$_inscricao = $this->_request->getParam('inscricao',0);		
		$this->view->boletos = $this->db->query("SELECT * FROM financeiro WHERE (idInscricao=$_inscricao) ORDER BY vcto")->fetchAll();
		$_cadastro = $this->view->boletos[0]->idCadastro; 
		$query = $this->db->query("SELECT nome, cpf, ender, num, compl, bairro, cep, cidade, uf FROM cadastro WHERE (id=$_cadastro)")->fetchAll();
		$this->view->cadastro = $query[0];
		$this->view->cadastro->cpf = $this->view->fc->formatCPF($this->view->cadastro->cpf);
		$this->view->cadastro->cep = $this->view->fc->formatCEP($this->view->cadastro->cep);
		#$this->view->fc->debug($this->view->boletos);
		#$this->view->fc->debug($this->view->cadastro); die();
		$this->render();
		}




	function addAction(){
		$this->view->message = '';
		$_id = (int)$this->_request->getParam('id',0);
		$sql = "SELECT nome, cpf, ender, num, compl, bairro, cep, cidade, uf 
			FROM cadastro WHERE id=$_id";
		$result = $this->db->query($sql)->fetchAll();
		$result = $result[0];
		$result->id = 0;
		$result->idCadastro = $_id;
		$result->idCurso = 0;		
		$result->dataLcto = date('Y-m-d');
		$result->pgto = '0000-00-00';			
		$result->numDoc = sprintf('%05d%s', $_id, date('ym'));
		$result->vcto = date('Y-m-d', time()+4*86400);
		$result->valor = 0;
		$result->acrescimo = $result->desconto = 0;
		$result->total = $result->valor;			
		$result->vcto = date("Y-m-d", time() + (4 * 86400));
		$this->view->boleto = $result;
		$this->render('form');
		}







	function openAction(){
		$this->view->where = "ADM/Boletos - Alterações/Baixa";
		$this->view->message = '';
		$_id = (int)$this->_request->getParam('id',0);
		$this->view->idInscricao = (int)$this->_request->getParam('inscricao',0);
		if($_id){
			$sql = "SELECT * FROM financeiro WHERE idFinanceiro=$_id";
			$result = $this->fc->dbReader($this->db, $sql);
			
			$result->aluno = $this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro=$result->idCadastro");
			$result->sacado = ($result->idSacado > 0) ? $this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro=$result->idSacado") : $result->aluno;	
			}
		else{
			$_curso = (int)$this->_request->getParam('curso', 0);
			$_inscricao = (int)$this->_request->getParam('inscricao', 0);
			
			$sql = "SELECT cd.id AS idCadastro, cd.nome, cd.cpf, cd.ender, cd.num, cd.compl, cd.bairro, cd.cep, cd.cidade, cd.uf,
				ac.idCurso, ac.parcelas, ac.valorParcelas AS valor, CONCAT('Inscr. Curso: ', cs.titulo) AS historico				
					FROM (alunoscursos AS ac INNER JOIN cadastro AS cd ON ac.idCadastro=cd.id
						INNER JOIN cursos AS cs ON ac.idCurso=cs.id)
							WHERE ac.id=$_inscricao";
			$result = $this->db->query($sql)->fetchAll();
			$result = $result[0];
			$result->id = 0;
			$result->dataLcto = date('Y-m-d');
			$result->pgto = '0000-00-00';
			
			$query = $this->db->query("SELECT MAX(numDoc) AS max FROM financeiro WHERE (idCurso=$_curso) AND (idCadastro=$result->idCadastro)")
				->fetchAll();
			if($query){
				$result->numDoc = sprintf('%09d',(int)$query[0]->max+1);
				}
			else{
				$result->numDoc = sprintf('%07d%2d', $result->idCadastro, 1);
				$result->vcto = date('Y-m-d');
				}
			$result->acrescimo = $result->desconto = 0;
			$result->total = $result->valor;			
			$result->vcto = date("Y-m-d", time() + (4 * 86400));
			}
		//echo $sql;//die();
		//$result = $this->db->query($sql)->fetchAll();
		//$this->view->fc->debug($result);die();
		$this->view->boleto = $result;
		$this->view->message = $msg;
		$this->render('form');
		}


	function saveAction(){
		Zend_Loader::loadClass('Financeiro');
		$boleto = new Financeiro();
		$_curso = (int)$this->_request->getParam('curso', 0);
		$_inscricao = (int)$this->_request->getParam('inscricao', 0);
		$_id = (int)$this->_request->getParam('id',0);
		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			
			//$this->view->fc->debug($_POST); die();			
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();
			$_idCadastro = (int)$this->_request->getPost('idCadastro');
			$_numDoc = $this->_request->getPost('numDoc');
			$vcto = $this->view->fc->dmY2Ymd($this->_request->getPost('vcto'));			
			$valor = str_replace('.','',$_POST['valor']);
			$valor = str_replace(',','.',$valor);
			$acrescimo = str_replace('.','',$_POST['acrescimo']);
			$acrescimo= str_replace(',','.',$acrescimo);
			$desconto = str_replace('.','',$_POST['desconto']);
			$desconto = str_replace(',','.',$desconto);
			$total  = $valor + $acrescimo - $desconto;
			$pgto = $this->view->fc->dmY2Ymd($this->_request->getPost('pgto'));

			$historico = trim($filter->filter($this->_request->getPost('historico')));
			$observacoes = trim($filter->filter($this->_request->getPost('observacoes')));
			$status = (int)$this->_request->getPost('status');
			if($pgto!='0000-00-00'){
				if($status==1) $status=3;
				elseif($status==2)$status=4;
				}
			$_data = array(
				'vcto' => $vcto,
				'valor' => $valor,
				'acrescimo' => $acrescimo,
				//'desconto' => $desconto,
				'total' => $total,
				'pgto' => ($pgto != '0000-00-00') ? $pgto : null,
				'historico' => $historico,
				'status' => $status,
				'observacoes' => $observacoes,
				);
			//$this->view->fc->debug($_data);die("id=$_id");
			try{
				if($_id){
					$boleto->update($_data, "idFinanceiro = $_id");
					$this->_redirect("boletos/open/curso/$_curso/inscricao/$_inscricao/id/$_id/");
					$this->view->message = "Boleto atualizado com sucesso!";
					}
				else{
					$_data['dataLct'] = date('m.d.Y');
					$_data['idCadastro'] = $_idCadastro;
					$_data['idCurso'] = $_curso;
					$_data['numDoc'] = $_numDoc;
					$_id = $boleto->insert($_data);
					$this->_redirect("boletos/open/curso/$_curso/inscricao/$_inscricao/id/$_id/");
					$this->view->message = "Novo registro incluído com sucesso!";
					}
				/*
				if($pgto !='0000-00-00'){					
					$this->db->query("UPDATE usuario SET status=1, dataPgto='$pgto', validade='$validade', matricula='$_matricula' WHERE id=$_usuarioId");
					if($_inscricaoId)
						$this->db->query("UPDATE inscricoes SET status=1, dataPgto='$pgto' WHERE id=$_inscricaoId");
					}
				*/				
				}
			catch (Exception $ex) {
				$this->view->message = $ex->getMessage();
				}
			} // post
		if($_id){
			$sql = "SELECT cd.nome, cd.cpf, cd.ender, cd.num, cd.compl, cd.bairro, cd.cep, cd.cidade, cd.uf, 
				fi.id, fi.dataLcto, fi.idCadastro, fi.idCurso, fi.numDoc, fi.historico, fi.valor, fi.vcto, fi.pgto, fi.acrescimo, 
				fi.desconto, fi.total, fi.status, fi.observacoes 
					FROM (financeiro AS fi INNER JOIN cadastro AS cd ON fi.idCadastro=cd.id)
					WHERE fi.id=$_id";
			//echo $sql;//die();
			$this->view->boleto = $this->fc->dbReader($this->db, $sql);
			$this->render('form');
			}
		}



	function delAction(){
		$_id = (int)$this->_request->getParam('id',0);
		$_curso = (int)$this->_request->getParam('curso',0);
		$_inscricao = (int)$this->_request->getParam('inscricao',0);
		//die("DELETE FROM boletos WHERE id=$_id");
		$this->db->query("DELETE FROM financeiro WHERE id=$_id");		
		if($_curso)
			$this->_redirect("cursos/inscricoes/curso/$_curso/id/$_inscricao");	
		}


	}
