<?php
class cursosController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseCss = $this->_request->getBaseUrl() ."/public/styles";
		$this->view->baseImg = $this->_request->getBaseUrl() ."/public/images";
		$this->view->links = $this->_request->getBaseUrl() ."/cursos";
		$this->view->baseEdit = $this->_request->getBaseUrl() ."/cursos/edit/id";
		$this->view->baseSave = $this->_request->getBaseUrl() ."/cursos/save/id";
		$this->view->baseDel = $this->_request->getBaseUrl() ."/cursos/del/id";
		
		$this->view->user = Zend_Auth::getInstance()->getIdentity();	
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		Zend_Loader::loadClass('FuncoesUteis');
		$this->fc = new FuncoesUteis();
		$this->view->fc = $this->fc;
		
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		Zend_Loader::loadClass('Zend_Paginator');
		
		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->uid = (int)$this->_request->getParam('uid',0);
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$this->_redirect('auth/login');
				}
		}
	

	function listAction(){
		$_del = (int)$this->_request->getParam('del',0);
		if($_del > 0){
			try{
				$count = (int)$this->fc->dbReader($this->db, "SELECT count(*) AS total FROM alunosCursos WHERE idCurso=$_del")->total;
				if($count > 0)
					throw new Exception("Neste curso existem $count alunos inscritos.\n\nNão pode ser excluído.\n\n");
				$this->db->query("DELETE FROM cursos WHERE id=$_id");
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		$sql = "SELECT idCurso, inicio, titulo, valor, parcelas, status, ativo FROM cursos ORDER by idCurso DESC";		
		$dados = $this->fc->dbReader($this->db, $sql, true);
		$page = intval($this->_getParam('page', 1));
		$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
		$paginator->setItemCountPerPage(32);		
		$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
		$paginator->setCurrentPageNumber($page);	// Seta a página atual		
		$this->view->paginator = $paginator;	// Passa o paginator para a view
		$this->view->where = "Lista de Cursos";
		}




	function openAction(){
		$this->view->where = "ADM/Agenda de Cursos - Alteração";	
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			Zend_Loader::loadClass('Zend_Filter_StripTags');
			$filter = new Zend_Filter_StripTags();

			$_inicio = ($this->_request->getPost('inicio') != '00/00/0000') ? $this->view->fc->dmY2Ymd($this->_request->getPost('inicio')) : null;
			$_final = ($this->_request->getPost('final') != '00/00/0000') ? $this->view->fc->dmY2Ymd($this->_request->getPost('final')) : null;
			$_datamax = ($this->_request->getPost('dataMax') != '00/00/0000') ? $this->view->fc->dmY2Ymd($this->_request->getPost('dataMax')) : null;
			$_valor = str_replace('.','', $this->_request->getPost('valor'));
			$_valor = (double)str_replace(',','.', $_valor);
			$_cargaHoraria = (int)$this->_request->getPost('cargaHoraria');
			$_parcelas = (int)$this->_request->getPost('parcelas');
					
			$_titulo = trim($filter->filter($this->_request->getPost('titulo')));
			$_descricao = trim(str_replace("'",'"',$this->_request->getPost('descricao')));
			$_status = (int)$this->_request->getPost('status');
			$_ativo = (int)$this->_request->getPost('ativo');
			
			$_data = array(
				'inicio' => $_inicio,
				'final' => ($_final!='0000-00-00') ? $_final : null,
				'titulo' => $_titulo,				
				'descricao' => $_descricao,
				'cargaHoraria' => $_cargaHoraria,
				'valor' => $_valor,
				'parcelas' => $_parcelas,
				'dataMax' => ($_datamax!='0000-00-00') ? $_datamax : null,
				'status' => $_status,
				'ativo' => $_ativo,
				);
		//	$this->view->fc->debug($_POST);
		//	$this->view->fc->debug($_data);
		//	die("idCurso={$this->view->id}");
			
			Zend_Loader::loadClass('Cursos');
			$curso = new Cursos();
			try{
				if($this->view->id){
					$curso->update($_data, "idCurso={$this->view->id}");
					$this->_redirect('cursos/list');
					}
				else{
					$data['datareg'] = date('Y-m-d H:i:s');
					$this->view->id = $curso->insert($_data);
					$this->_redirect('cursos/list');
					}
				}
			catch(Exception $ex){
				$this->view->message = sprintf("Erro: %s", $ex->getMessage());
				}
			}
		$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM cursos WHERE idCurso={$this->view->id}"));		
		$this->render('form');		
		}




	function contratoAction(){
		//$sql = "SELECT titulo, inicio, final, valor, parcelas, cargaHoraria FROM cursos WHERE idCurso={$this->view->id}"; //die($sql);
		//$sql = "SELECT titulo, inicio, final, valor, parcelas, cargaHoraria FROM cursos WHERE idCurso={$this->view->id}"; //die($sql);
		$sql = "SELECT cs.titulo, cs.inicio, cs.final, ac.idAluno, ac.idCurso, ac.valorCurso AS valor, ac.parcelas, ac.valorParcelas, cs.cargaHoraria FROM
			(cursos AS cs INNER JOIN alunosCursos AS ac ON ac.idCurso=cs.idCurso)
				WHERE (ac.idAlunoCursos={$this->view->id})"; //die($sql);
		$this->view->curso = $this->fc->dbReader($this->db, $sql);
		
		$sql = "SELECT * FROM cadastro WHERE idCadastro={$this->view->uid}";
		$query = $this->fc->dbReader($this->db, $sql);
		
		$sql = "SELECT vcto FROM financeiro WHERE
			(idCadastro={$this->view->curso->idAluno}) AND (idCurso={$this->view->curso->idCurso}) ORDER BY vcto";
		//die("$sql");
		$this->view->inicio = $this->fc->Ymd2dmY($this->fc->dbReader($this->db, $sql)->vcto);
		//die("this->view->inicio = {$this->view->inicio}");

		
		$this->view->curso->idCadastro = $query->idCadastro;
		$this->view->curso->nome = $query->nome;
		$this->view->curso->ender = $query->ender;
		$this->view->curso->num =  $query->num;
		$this->view->curso->compl = $query->compl;
		$this->view->curso->bairro = $query->bairro;
		$this->view->curso->cidade = $query->cidade;
		$this->view->curso->uf = $query->uf;
		$this->view->curso->rg = $query->rg;
		$this->view->curso->cpf = $query->cpf;
		$this->getHelper('layout')->disableLayout();			
		}

/*
	function contratoAction(){
		$_id = (int)$this->_request->getParam('id',0);
		$_idCurso = (int)$this->_request->getParam('curso',0);
		$query = $this->db->query("SELECT cd.id AS idCadastro, '$_idCurso' AS idCurso, '$_id' AS idInscricao, 
			cd.nome, cd.ender, cd.num, cd.compl, cd.bairro, cd.cidade, cd.uf, cd.rg, cd.cpf,
			ca.valor AS total, ca.parcelas, ca.valorParcelas AS parcela
			FROM (cadastro AS cd INNER JOIN alunoscursos AS ca ON cd.id=ca.idCadastro)
				WHERE ca.id=$_id")->fetchAll();
		
		$this->view->contrato = $query[0];
		
		$query = $this->db->query("SELECT titulo, cargaHoraria, inicio, final
			FROM cursos WHERE id=$_idCurso")->fetchAll();
		
		$this->view->contrato->titulo = $query[0]->titulo;
		$this->view->contrato->cargaHoraria = $query[0]->cargaHoraria;
		$this->view->contrato->inicio = $query[0]->inicio;
		$this->view->contrato->final = $query[0]->final;
		
		$this->view->fc->debug($this->contrato);		
		}
*/




	function inscricoesAction(){
		$this->view->where = "Detalhes da Inscrição";
		$this->view->idCurso = ($this->_request->getPost('idCurso'))?(int)$this->_request->getPost('idCurso'):(int)$this->_request->getParam('curso',0);
		$this->view->id = (int)$this->_request->getParam('id', 0);
		
		//echo "this->view->id = {$this->view->id}";
		if($this->view->id){
			$_del = (int)$this->_request->getParam('del', 0);
			if($_del){
				$sql = "DELETE FROM financeiro WHERE idFinanceiro=$_del"; //echo $sql;
				$this->db->query($sql);
				}
			
			// selecio	nado um ID na lista
			$sql = sprintf("SELECT cd.idCadastro, cd.nome, cd.cpf, cd.rg, cd.ender, cd.num, cd.compl, cd.bairro, cd.cidade, cd.cep, cd.uf, 
				cd.fone, cd.fone2, cd.email, cd.profissao,
				ac.valorCurso, ac.parcelas, ac.valorParcelas, ac.data, ac.status AS cursoStatus,
				cs.titulo, cs.inicio, cs.cargaHoraria FROM(cadastro AS cd
					INNER JOIN alunosCursos AS ac ON ac.idAluno=cd.idCadastro
					INNER JOIN cursos AS cs ON cs.idCurso=ac.idCurso)
					WHERE (ac.idAlunoCursos=%d)", $this->view->id); //echo $sql;

			$this->view->dbtable = $this->fc->dbReader($this->db, $sql);
			if($this->view->dbtable->valorCurso==0){
				$query = $this->fc->dbReader($this->db, "SELECT valor, parcelas FROM cursos WHERE idCurso={$this->view->idCurso}");
				$this->view->dbtable->valorCurso = $query->valor;
				$this->view->dbtable->parcelas = $query->parcelas;
				$this->view->dbtable->valorParcelas = $query->valor / $query->parcelas;
				}
			
			$sql = sprintf("SELECT idFinanceiro, dataLct, numDoc, historico, valor, vcto, pgto, total
				FROM financeiro WHERE (idCurso=%d) AND (idCadastro=%d) ORDER BY vcto", $this->view->idCurso, $this->view->dbtable->idCadastro);
			$this->view->boletos = $this->fc->dbReader($this->db, $sql, true); //echo $sql;
			//$this->fc->debug($this->view->boletos);
			$this->render('inscricoesDetalhes');  			
			}
		else {			
			$sql = sprintf("SELECT cd.cpf, cd.nome, ac.idAlunoCursos, ac.data, ac.status
				FROM (cadastro AS cd INNER JOIN alunosCursos AS ac ON ac.idAluno=cd.idCadastro)
					WHERE (ac.idCurso=%d) ORDER BY cd.nome", $this->view->idCurso); //echo $sql; //die();
			$this->view->dbtable = $this->fc->dbReader($this->db, $sql, true); 
			$this->view->combo = $this->fc->dbReader($this->db, "SELECT idCurso, titulo, inicio FROM cursos ORDER by ativo DESC, inicio DESC", true);		
			$this->render('inscricoes');
			}
		}










	function reservasAction(){
		$this->view->cs = $this->_request->getParam('cs',0);
		if($this->view->cs=='SP'){
			$this->view->where = "ADM/Reserva de Cursos: SAÚDE PÚBLICA";
			$this->view->caption = "Reserva de Cursos: SAÚDE PÚBLICA";
			$sql = "SELECT id, nome, email, fone, cel, 'SAÚDE PÚBLICA' AS curso, CONVERT(CHAR(10), data, 103) AS data
				FROM cursosReserva WHERE (cursoSP=1)
					ORDER by nome, data";	
			}
		elseif($this->view->cs=='FH'){
			$this->view->where = "ADM/Reserva de Cursos: FARMÁCIA HOSPITALAR E SERVIÇOS DE SAÚDE";
			$this->view->caption = "Reserva de Cursos: FARMÁCIA HOSPITALAR E SERVIÇOS DE SAÚDE";
			$sql = "SELECT id, nome, email, fone, cel, 'FARMÁCIA HOSPITALAR' AS curso, CONVERT(CHAR(10), data, 103) AS data
				FROM cursosReserva WHERE (cursoFH=1)
					ORDER by nome, data";
			}
		else{
			$this->view->where = "ADM/Reserva de Cursos: FARMACOLOGIA CLÍNICA";
			$this->view->caption = "Reserva de Cursos: FARMACOLOGIA CLÍNICA";
			$sql = "SELECT id, nome, email, fone, cel, 'FARMACOLOGIA CLÍNICA' AS curso, CONVERT(CHAR(10), data, 103) AS data
				FROM cursosReserva WHERE (cursoFC=1)
					ORDER by nome, data";
			}
		
		$_del = (int)$this->_request->getParam('del',0);
		if($_del > 0){
			$this->db->query("DELETE FROM cursosReserva WHERE id=$_id");
			$this->view->message = "Registro Excluído com sucesso.\n\n";
			}

					//$this->fc->debug($this->view->dbtable); die();
		if($this->_request->getParam('export',0)){
			//$this->view->dbtable = $this->fc->dbReader($this->db, "SELECT *, 
			//	CONVERT(CHAR(10), data, 103) AS data
			//		FROM cursosReserva ORDER BY cursoSP, cursoFH, cursoFC, nome", true);
			
			$this->view->dbtable = $this->fc->dbReader($this->db, $sql, true);
			
			$this->getHelper('layout')->disableLayout();
			$this->render('export2excel');		
			}
		else{		
			//$sql = "SELECT * FROM cursosReserva ORDER by data DESC, nome";		
			
			
			$dados = $this->fc->dbReader($this->db, $sql, true);
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(16);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
			$this->view->totalSP = 	(int)$this->fc->dbReader($this->db, "SELECT COUNT(*) AS total FROM cursosReserva WHERE cursoSP='1'")->total;
			$this->view->totalFH = 	(int)$this->fc->dbReader($this->db, "SELECT COUNT(*) AS total FROM cursosReserva WHERE cursoFH='1'")->total;
			$this->view->totalFC = 	(int)$this->fc->dbReader($this->db, "SELECT COUNT(*) AS total FROM cursosReserva WHERE cursoFC='1'")->total;
			}	
		}


	}
