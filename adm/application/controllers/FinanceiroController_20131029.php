<?php
	
class FinanceiroController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseCss = $this->_request->getBaseUrl() ."/public/styles";
		$this->view->baseImg = $this->_request->getBaseUrl() ."/public/images";
		$this->view->baseAct = $this->_request->getBaseUrl() ."/cadastro";
		
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		
		$this->db = Zend_Registry::get('db');
		$this->db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		Zend_Loader::loadClass('FuncoesUteis');
		$this->fc = new FuncoesUteis();
		$this->view->fc = $this->fc;
		
		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->st = (int)$this->_request->getParam('st', 0);
		$this->view->id = (int)$this->_request->getParam('id', 0);
		}


	function preDispatch()
		{
		$auth = Zend_Auth::getInstance();
			if (!$auth->hasIdentity()) {
				$this->_redirect('auth/login');
				}
		}


	function listAction(){
		if($this->view->st==1)
			$this->view->where = "Financeiro - Contas a Pagar";
		elseif($this->view->st==2)
			$this->view->where = "Financeiro - Contas a Receber";
		elseif($this->view->st==3)
			$this->view->where = "Financeiro - Contas Pagas";
		elseif($this->view->st==4)
			$this->view->where = "Financeiro - Contas Recebidas";
		else
			$this->view->where = "Financeiro - Balanço Periódico";
		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			$inicio = $this->view->fc->dmY2msSql($this->_request->getPost('inicio'));
			$final = $this->view->fc->dmY2msSql($this->_request->getPost('final'));
			$_del = (int)$this->_request->getPost('del');
			
			if($_del){
				$this->db->query("DELETE FROM financeiro WHERE id=$_del");
				//echo "DELETE FROM financeiro WHERE id=$_del";
				$this->view->message = "Registro excluído com sucesso!";
				}
			}
		else{
			$_POST['inicio'] = date('01/m/Y');
			$_POST['final'] = date('d/m/Y');
			$inicio = date('m.01.Y');
			$final = date('m.d.Y');			
			}
		
		if($this->view->st < 3)
			$sql =  sprintf("SELECT *
				FROM financeiro WHERE (status=%d) AND (vcto BETWEEN '%s' AND '%s') ORDER BY vcto", 
					$this->view->st, $inicio, $final);

		elseif($this->view->st < 5)
			$sql =  sprintf("SELECT *
				FROM financeiro WHERE (status=%d) AND (pgto BETWEEN '%s' AND '%s') ORDER BY pgto", 
					$this->view->st, $inicio, $final);
		else
			$sql = sprintf("SELECT *
				FROM financeiro WHERE (status > 2) AND (pgto BETWEEN '%s' AND '%s') ORDER BY pgto", 
					$inicio, $final);
			
		$this->view->dbtable = $this->fc->dbReader($this->db, $sql, true); //echo $sql;
		}



	function printAction(){
		$this->getHelper('layout')->disableLayout();
		if($this->view->st==1)
			$this->view->caption = "AMF-Financeiro/Contas a Pagar";
		elseif($this->view->st==2)
			$this->view->caption = "AMF-Financeiro/Contas a Receber";
		elseif($this->view->st==3)
			$this->view->caption = "AMF-Financeiro/Contas Pagas";
		elseif($this->view->st==4)
			$this->view->caption = "AMF-Financeiro/Contas Recebidas";
		elseif($this->view->st==5)
			$this->view->caption = "AMF-Financeiro/Balanço Periódico";
		else
			$this->view->caption = "AMF-Financeiro/Despesas";
			
		$this->view->caption .= sprintf(" - Emitido em: %s", date('d/m/Y H:i:s'));
		
		//$this->fc->debug($_POST);
		$this->view->inicio = $this->view->fc->dmY2Ymd($this->_request->getPost('dataInicial'));
		$this->view->final = $this->view->fc->dmY2Ymd($this->_request->getPost('dataFinal'));		

		if($this->view->st < 5)
			$sql = sprintf("SELECT *
				FROM financeiro WHERE (status=%d) AND (vcto BETWEEN '%s' AND '%s') ORDER BY vcto DESC", 
					$this->view->st, $this->view->inicio, $this->view->final);
		elseif($this->view->st == 5)
			$sql = sprintf("SELECT *
				FROM financeiro WHERE (status > 2) AND (pgto BETWEEN '%s' AND '%s') ORDER BY pgto DESC", 
					$this->view->inicio, $this->view->final);	
		else
			$sql = sprintf("SELECT *
				FROM financeiro WHERE (status IN(1,3)) AND (vcto BETWEEN '%s' AND '%s') ORDER BY vcto DESC", 
					$this->view->inicio, $this->view->final);	
		//die($sql);
		$this->view->dbtable = $this->db->query($sql)->fetchAll();
		}





	function despesasAction(){
		$this->view->where = "Financeiro/Despesas - Alteração de  Lançamentos";
		if($this->view->id > 0){
			$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM financeiro WHERE idFinanceiro={$this->view->id}")); 
			$this->view->where = "Financeiro/Despesas - Alteração de  Lançamentos";
			}
		
		$this->view->tipoDoc = $this->fc->dbReader($this->db, "SELECT * FROM tipoDoc ORDER BY id", true);
		
		if ($this->_request->isPost()){
			try{
				//$this->fc->debug($_POST);
				$f = new Zend_Filter_StripTags();
				$_tipoDoc = (int)$this->_request->getPost('tipoDoc');
				if($_tipoDoc == 0){
					$_tipoDoc = (int)$this->fc->dbReader($this->db, sprintf("SELECT id FROM tipoDOC WHERE tipo='%s'", trim($f->filter($_POST['novoTipoDoc']))))->id;
					if($_tipoDoc == 0){
						Zend_Loader::loadClass('TipoDoc');
						$novoTipo = new TipoDoc();
						$_dados = array(
							'tipo' => trim($f->filter($this->_request->getPost('novoTipoDoc')))
							);
						$_tipoDoc = $novoTipo->insert($_dados);						
						}
					$_POST['tipoDoc'] = $_tipoDoc;
					$_POST['novoTipoDoc'] = null;
					}

				$_pgto = (!empty($_POST['pgto'])) ? $this->fc->dmY2Ymd($this->_request->getPost('pgto')) : null;
				$_emissao = (!empty($_POST['emissao'])) ? $this->fc->dmY2Ymd($this->_request->getPost('emissao')) : null;
				$_dados = array(
					'idForm' => $_POST['idForm'],
					'historico' => trim($f->filter($this->_request->getPost('historico'))),
					'empresa' => trim($f->filter($this->_request->getPost('empresa'))),
					'tipoDoc' => $_tipoDoc,
					'numDoc' => trim($f->filter($this->_request->getPost('numDoc'))),
					'vcto' => $this->fc->dmY2Ymd($this->_request->getPost('vcto')),
					'valor' => ereg_replace("([^0-9])",'',$_POST['valor'])/100,
					'desconto' 	=> ($_POST['desconto']	=='0,00') ? 0.0 : ereg_replace("([^0-9])",'',$_POST['desconto'])/100,
					'acrescimo' => ($_POST['acrescimo']	=='0,00') ? 0.0 : ereg_replace("([^0-9])",'',$_POST['acrescimo'])/100,
					'pgto' => $_pgto,
					'total' => ($_POST['total']=='0,00') ? 0.0 : ereg_replace("([^0-9])",'',$_POST['total'])/100,
					'modulo' => (int)$this->_request->getPost('modulo'),
					'curso' => trim($f->filter($this->_request->getPost('curso'))),
					'emissao' => $_emissao,
					'numCheque' => trim($f->filter($this->_request->getPost('numCheque'))),
					'valorCheque' => ($_POST['valorCheque']=='0,00') ? 0.0 : ereg_replace("([^0-9])",'',$_POST['valorCheque'])/100,
					'nominal' => trim($f->filter($this->_request->getPost('nominal'))),
					'status' => (empty($_dados['pgto'])) ? 1 : 3,
					);

				//$this->fc->debug($_dados);
				if (empty($_dados['historico']))
					throw new Exception("O campo 'Histórico' é obrigatório.\n\n");
				elseif(empty($_dados['empresa']))
					throw new Exception("O campo 'Empresa' é obrigatório.\n\n");
				elseif($_dados['tipoDoc'] == 0)
					throw new Exception("O campo 'TipoDoc' é obrigatório.\n\n");
				elseif(empty($_dados['numDoc']))
					throw new Exception("O campo 'Número do Documento' é obrigatório.\n\n");
				elseif(empty($_dados['vcto']))
					throw new Exception("O campo 'Vencimento' é obrigatório.\n\n");
				elseif($_dados['valor'] ==0)
					throw new Exception("O campo 'Valor' é obrigatório.\n\n");
				elseif($_dados['modulo'] > 0 && $_dados['curso'] == '')
					throw new Exception("Selecione de qual Curso é essa Despesa!\n\n");
				elseif($_dados['modulo'] == 0 && $_dados['curso'] != '')
					throw new Exception("Selecione o Módulo do Curso ou desmarque o Curso selecionado!\n\n");
				if($_dados['emissao'] != ''){
					if(empty($_dados['numCheque']))
						throw new Exception("O campo 'Número do Cheque' é obrigatório.\n\n");
					elseif($_dados['valorCheque'] == 0)
						throw new Exception("O campo 'Valor do Cheque' é obrigatório.\n\n");
					elseif(empty($_dados['nominal']))
						throw new Exception("O campo 'Nominal à' é obrigatório.\n\n");
					}

				$_dados['total'] = $_dados['valor'] + $_dados['acrescimo'] - $_dados['desconto'];
				
				// verificar se a pessoa não deu F5 e o sistema vai gravar em duplicidade no caso de novo registro
				if($this->view->id == 0)					
					$this->view->id = $this->fc->dbReader($this->db, "SELECT idFinanceiro FROM financeiro WHERE idForm='{$_dados['idForm']}'")->idFinanceiro;
					
				
				Zend_Loader::loadClass('Financeiro');
				$financeiro = new Financeiro();
				if($this->view->id > 0){
					$financeiro->update($_dados, "idFinanceiro={$this->view->id}");
					$this->view->message = "Registro alterado com sucesso!";
					}
				else{
					$this->view->id = $financeiro->insert($_dados);
					$this->view->message = "Novo Registro de Despesa inserido com sucesso!\n\n";
					}
				$_POST = null;
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}
		else{
			$this->view->idForm = uniqid();
			$_POST['dataReg'] = date('d/m/Y');
			}
		}


	function listdespAction(){
		$this->view->where = "Financeiro - Lista de Despesas";

		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			$inicio = $this->view->fc->dmY2msSql($this->_request->getPost('inicio'));
			$final = $this->view->fc->dmY2msSql($this->_request->getPost('final'));
			$_del = (int)$this->_request->getPost('del');
			
			if($_del){
				$this->db->query("DELETE FROM financeiro WHERE id=$_del");
				//echo "DELETE FROM financeiro WHERE id=$_del";
				$this->view->message = "Registro excluído com sucesso!";
				}
			}
		else{
			$_POST['inicio'] = date('01/m/Y');
			$_POST['final'] = date('d/m/Y');
			$inicio = date('m.01.Y');
			$final = date('m.d.Y');			
			}
		
		$sql = sprintf("SELECT *
			FROM financeiro WHERE (status IN(1,3)) AND (vcto BETWEEN '%s' AND '%s') ORDER BY vcto", 
				$inicio, $final);
			
		$this->view->dbtable = $this->fc->dbReader($this->db, $sql, true); //echo $sql;
		}



	function printdespAction(){
		$this->getHelper('layout')->disableLayout();
		$this->view->caption = "AMF-Financeiro/Despesas";
			
		$this->view->caption .= sprintf(" - Emitido em: %s", date('d/m/Y H:i:s'));
		
		//$this->fc->debug($_POST);
		$this->view->inicio = $this->view->fc->dmY2Ymd($this->_request->getPost('dataInicial'));
		$this->view->final = $this->view->fc->dmY2Ymd($this->_request->getPost('dataFinal'));		

		$sql = sprintf("SELECT *
			FROM financeiro WHERE (status IN(1,3)) AND (vcto BETWEEN '%s' AND '%s') ORDER BY vcto DESC", 
				$this->view->inicio, $this->view->final);	
		//die($sql);
		$this->view->dbtable = $this->db->query($sql)->fetchAll();
		if($this->_request->getPost('btExport')){
			$this->view->filename = sprintf("relatorio_despesas_%s_a_%s.xls", $this->view->inicio, $this->view->final);
			foreach($this->view->dbtable as $rs):
				unset($rs->idForm);
				unset($rs->idCadastro);
				unset($rs->idSacado);
				unset($rs->idCurso);
				unset($rs->conta);
				unset($rs->parcela);
				unset($rs->boleto);
				unset($rs->idContabil);
				unset($rs->idCentroCusto);
				unset($rs->status);
				unset($rs->banco);
				unset($rs->ctaBanco);
				unset($rs->idFinanceiro);
				unset($rs->dataReg);
				unset($rs->formaPgto);	
				$rs->dataLct = $this->fc->Ymd2dmY($rs->dataLct);
				$rs->vcto = $this->fc->Ymd2dmY($rs->vcto);
				$rs->pgto = $this->fc->Ymd2dmY($rs->pgto);
				$rs->emissao = $this->fc->Ymd2dmY($rs->emissao);
				$rs->valor = number_format($rs->valor, 2, ',','.');
				$rs->acrescimo = number_format($rs->acrescimo, 2, ',','.');
				$rs->desconto = number_format($rs->desconto, 2, ',','.');
				$rs->total = number_format($rs->total, 2, ',','.');
				$rs->valorCheque = number_format($rs->valorCheque, 2, ',','.');
				$rs->tipoDoc = ($rs->tipoDoc > 0) ? $this->fc->dbReader($this->db, "SELECT tipo FROM tipoDoc WHERE id=$rs->tipoDoc")->tipo : null;
				//$rs->formaPgto = ($rs->formaPgto > 0) ? $this->fc->dbReader($this->db, "SELECT tipo FROM formaPagto WHERE idFormaPgto=$rs->formaPgto")->formaPgto : null;
			endforeach; 
			$this->render('export2excel');
			}
		}


	}
