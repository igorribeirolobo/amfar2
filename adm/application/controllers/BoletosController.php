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
		$filtro = ($_st==0) ? "(pgto='0000-00-00')" : "(pgto <> '0000-00-00')";
		
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



	function searchAction(){
		$this->view->where = "ADM/Financeiro - Baixar Boletos";
		if ($this->_request->isPost()){
			try{		
				$f = new Zend_Filter_StripTags();
				//$this->fc->debug($_POST); die();
				$numdoc = $f->filter(trim($this->_request->getPost('numdoc')));
				if(empty($numdoc))
					throw new Exception("Digite o número do documento para a busca.\n\n");
				
				$sql = "SELECT fi.*, cd.nome AS sacado FROM 
					(financeiro AS fi INNER JOIN cadastro AS cd ON fi.idCadastro=cd.idCadastro)
						WHERE (fi.numDoc like '$numdoc%') AND(fi.status IN(2,4))";
				//echo $sql;
				$query = $this->fc->dbReader($this->db, $sql, true);
				//$this->fc->debug($query);
				if(empty($query))
					throw new Exception("Nenhum resultado localizado para a busca '$numdoc'.\n\nCorrija e tente novamente.\n\n");
				
				$this->view->boletos = $query;
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();	
				}
			}
		}





	function openAction(){
		$this->view->where = "ADM/Boletos - Alterações/Baixa";
		$this->view->message = '';
		$_id = (int)$this->_request->getParam('id',0);
		$this->view->idCurso = (int)$this->_request->getParam('curso',0);
		$this->view->idInscricao = (int)$this->_request->getParam('inscricao',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		if($_id){
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			//$this->view->fc->debug($_POST); die();
			Zend_Loader::loadClass('Financeiro');
			$boleto = new Financeiro();			
						
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
			else{
				if($status==3) $status=1;
				elseif($status==4)$status=2;
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
					//$this->_redirect("boletos/open/curso/$_curso/inscricao/$_inscricao/id/$_id/");
					$this->view->message = "Boleto atualizado com sucesso!";
					}
				else{
					$_data['dataLct'] = date('m.d.Y');
					$_data['idCadastro'] = $_idCadastro;
					$_data['idCurso'] = $_curso;
					$_data['numDoc'] = $_numDoc;
					$_id = $boleto->insert($_data);
					//$this->_redirect("boletos/open/curso/$_curso/inscricao/$_inscricao/id/$_id/");
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
		//$this->view->message = $msg;
		$this->getHelper('layout')->disableLayout();
		$this->render('form');
		}


	function saveAction(){
		Zend_Loader::loadClass('Financeiro');
		$boleto = new Financeiro();
		$_curso = (int)$this->_request->getParam('curso', 0);
		$_inscricao = (int)$this->_request->getParam('inscricao', 0);
		$_id = (int)$this->_request->getParam('id',0);
		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
			Zend_Loader::loadClass('Financeiro');
			$boleto = new Financeiro();			
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



	function enviarboletoAction(){
		$_id = (int)$this->_request->getParam('id',0);
		$sql = "SELECT idCadastro, idSacado, numDoc, historico, total, vcto, parcela FROM financeiro WHERE idFinanceiro=$_id";
		$this->view->boleto = $this->fc->dbReader($this->db, $sql);

		if($this->view->boleto->idSacado > 0)
			$sql = "SELECT nome, cpf, ender, num, compl, bairro, cep, cidade, uf, email FROM cadastro WHERE idCadastro={$this->view->boleto->idSacado}";
		else
			$sql = "SELECT nome, cpf, ender, num, compl, bairro, cep, cidade, uf, email FROM cadastro WHERE idCadastro={$this->view->boleto->idCadastro}";
		
		$query = $this->fc->dbReader($this->db, $sql);
		$this->view->boleto->nome = $query->nome;
		$this->view->boleto->cpf = $this->fc->formatCPF($query->cpf);
		$this->view->boleto->ender = $query->ender . ', '. $query->num . '&nbsp;&nbsp;&nbsp;&nbsp;' . $query->compl . '<br />';
		$this->view->boleto->ender .= $query->bairro . '<br />';
		$this->view->boleto->ender .= $this->fc->formatCEP($query->cep) . '&nbsp;&nbsp;&nbsp;&nbsp;' . $query->cidade . ' / ' . $query->uf;
		$this->view->boleto->email = $query->email;

		
		if (strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
			try{
				$email = trim(strtolower($_POST['email']));
				if(stristr($email, ';')){
					$t = explode(';', $email);
					$email = trim($t[0]);
					$email2 = trim($t[0]);
					}
				
				Zend_Loader::loadClass('BoletoPDF');
				$pdf = new BoletoPDF('P','mm','A4');			
				$pdf->_dba = $this->db;
				$pdf->_idBoleto = $_id;
				$pdf->_images = $this->view->baseImg;
				$pdf->SetCreator('AMF Associação Mineira de Farmacêuticos');
				$pdf->SetAuthor('Webmaster');
				$pdf->SetTitle(sprintf('Boleto para pagamento de parcela %02d ref. %s', $this->view->boleto->parcela, $this->view->boleto->historico)); 		
				$pdf->SetSubject(sprintf('Boleto para pagamento de parcela %02d ref. %s', $this->view->boleto->parcela, $this->view->boleto->historico));
				$pdf->SetFont('Arial','',9);
				// nome do arquivo = idCadastro.idBoleto.vcto		
				$pdf->getSetting();
				$pdf->AddPage();
				$pdf->BodyTable();
				$pdf->Output($pdf->file, "F");
					
				$eBody = "
				<html>
					<head>
						<title>http://www.sbrafh.org.br/Associados</title>\r\n
						<style>
							.table{width:600px;border:0;font:9pt Arial, Helvetica, Sans-Serif}
							.table th{color:#fff;background:#f90;padding:2px 4px}
							.table td{border:solid 1px silver}
							.table .label{text-align:right;color:#808080;padding:0 4px}
							.table caption{background:#369;color:#fff;font-size:16px;padding:4px}
							div{width:600px;font:9pt Arial, Helvetica, Sans-Serif}
						</style>
					</head>
					<body>
						<img src='topoEmail.jpg' border='0'><br />
						<table class='table' border='0' cellpadding='1' cellspacing='0'>";
						
					$eBody .= sprintf('<caption>Boleto para pagamento de parcela %02d ref. %s</caption>', $this->view->boleto->parcela, $this->view->boleto->historico);
					
					$eBody .= "
						   <tr>
						   	<td style='padding:8px'>
						   		<p>Prezado(a) Aluno <b>{$this->view->boleto->nome}</b>, 
						   		segue em anexo o boleto em pdf para pagamento da sua parcela 
									referente a <b>{$this->view->boleto->historico}</b></p>
								</td>
						   </tr>
						   <tr>
						   	<td align='center'>
									Se preferir imprimir pela internet, acesse o endereço abaixo para imprimir o boleto:<br />
						   		<a href='http://www.amfar.com.br/boletos/boleto.php?id=$_id'>
						   			http://www.amfar.com.br/boletos/boleto.php?id=$_id</a></td>
						</table>
						<div style='width:600px;text-align:center;padding-top:10px'>
							<a href='http://www.amfar.com.br'>http://www.amfar.com.br</a>
						</div>
					</body>
				</html>";
			//	die();
			//	Zend_Loader::loadClass('htmlMimeMail');
		
				//echo "_imagePath=$_imagePath";				
				$mailto = $email;
				//$mailto = "Webmaster <webmaster@sbrafh.org.br>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF - Associação Mineira de Farmacêuticos <webmaster@amfar.com.br>");
				$mail->setReplayTo("secretaria <secretaria@amfar.com.br>");
				//$mail->setCc("secretaria <secretaria@amfar.com.br>");
				$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject(sprintf('parcela referente a %s', $this->view->boleto->historico));
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$attachment = $mail->getFile($pdf->file);
				$mail->addAttachment($attachment, basename($pdf->file), 'application/pdf');
				// cópia para o administrativo
				$mail->send(array($mailto));  // envia copia para o usuario	
				if($email2)
					$mail->send(array($email2));  // envia copia para o usuario
			   $this->view->message = "Boleto enviado com sucesso para\n{$_POST['email']}";
			   }
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
		   }
		else
			$_POST['email'] = $this->view->boleto->email;
		
		$this->getHelper('layout')->disableLayout();
		//$this->fc->debug($this->view->boleto);
		$this->render('enviarboleto');
		}
	}
