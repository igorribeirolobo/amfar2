<?php
class AdmController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/adm", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->db = $this->view->db = Zend_Registry::get('db');
		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		$this->view->rg = $this->_request->getParam('rg',0);
		$this->view->dt = $this->_request->getParam('dt',0);
		$this->view->idBoleto = (int)$this->_request->getParam('boleto',0);
		$this->view->view = (int)$this->_request->getParam('view',0);
		Zend_Loader::loadClass('Zend_Paginator');
		$this->_helper->layout->setLayout("layout2");
		}


	function preDispatch()
		{		
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity() || $this->view->user->dominio != 'amfar') {
			$this->_redirect('auth/admlogin');
			}
		}






	function indexAction()
		{

		}




	function enviarboleto(){
		//$financeiro = $this->fc->dbReader($this->db, "SELECT * FROM financeiro WHERE idFinanceiro={$this->view->idBoleto}");	
		$cadastro = $this->fc->dbReader($this->db, "SELECT nome, email, validade FROM cadastro WHERE idCadastro={$this->view->id}");

		Zend_Loader::loadClass('BoletoPDF');
		$pdf = new BoletoPDF('P','mm','A4');
		
		
		$pdf->_dba = $this->db;
		$pdf->_idBoleto = $this->view->idBoleto;
		$pdf->_images = $this->view->baseImg;
		$pdf->SetCreator('SBRAFH Sociedade Brasileira de Farmácia Hospitalar');
		$pdf->SetAuthor('Webmaster');
		$pdf->SetTitle('Boleto Renovação de Anuidade de Associado'); 		
		$pdf->SetSubject('Boleto Renovação de Anuidade de Associado');
		$pdf->SetFont('Arial','',9);
		// nome do arquivo = idCadastro.idBoleto.vcto		
		$pdf->getSetting();
		$pdf->AddPage();
		$pdf->BodyTable();
		$pdf->Output($pdf->file, "F");

		if($cadstro->validade < date('Y-m-d'))
			$msg = sprintf('venceu em %s', $this->fc->Ymd2dmY($cadastro->validade));
		elseif($cadstro->validade = date('Y-m-d'))
			$msg = 'está vencendo hoje';
		else
			$msg = sprintf('vai vencer em %s', $this->fc->Ymd2dmY($cadastro->validade));			
		
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
				<table class='table' border='0' cellpadding='1' cellspacing='0'>
					<caption>Renovação de Inscrição de Associado</caption>
					<tr>
						<td align='center'>São Paulo, ". date('d/m/Y') ."</td>
					</tr>
				   <tr>
				   	<td style='padding:8px'>Inscrição Num.: <b>". sprintf('%06d', $this->view->id) ."</td>
				   </tr>
				   <tr>
				   	<td style='padding:8px'>
				   		<p>Prezado(a) Associado <b>$cadastro->nome</b>, 
				   		verificamos em nossos registros que a validade da sua anuidade de Associado SBRAFH 
							$msg. Para sua comodidade, anexamos o boleto para possa fazer sua renovação e manter 
							os benefícios concedidos somente aos associados ativos ou seja, em dia com a SBRAFH.</p>
							<p>Imprima o boleto em anexo e pague até a data de vencimento em qualquer banco de sua 
							preferência ou pela internet usando a linha digitável impressa acima da ficha de compensação.</p>
							<p>Agradecemos a sua atenção e esperamos contar com você em mais um ano, lembrando que neste ano 
							estaremos sorteando diversos brindes para os associados que fizeram sua renovação, portanto não 
							deixe de pagar a sua anuidade para participar do sorteio.</p>
							<p>Atenciosamente,<br /><br />
							Diretoria SBRAFH<br />
							Gestão 2012-2013.</p> 
						</td>
				   </tr>
				   <tr>
				   	<td align='center'>
							Se preferir imprimir pela internet, acesse o endereço abaixo para imprimir o boleto:<br />
				   		<a href='http://www.sbrafh.org.br/boletos/boleto.php?id={$this->view->idBoleto}'>
				   			http://www.sbrafh.org.br/boletos/boleto.php?id={$this->view->idBoleto}</a></td>
				</table>
				<div style='width:600px;text-align:center;padding-top:10px'>
					<a href='http://www.sbrafh.org.br'>http://www.sbrafh.org.br</a>
				</div>
			</body>
		</html>";
		
		Zend_Loader::loadClass('htmlMimeMail');

		//echo "_imagePath=$_imagePath";				
		$mailto = "$cadastro->nome <$cadastro->email>";
		//$mailto = "Webmaster <webmaster@sbrafh.org.br>";
		$mail = new htmlMimeMail();
		
		$mail->setReturnPath('webmaster@sbrafh.org.br');
		$mail->setFrom("SBRAFH/Cadastro <webmaster@sbrafh.org.br>");
		$mail->setReplayTo("Atendimento <atendimento@sbrafh.org.br>");
		$mail->setCc("Atendimento <atendimento@sbrafh.org.br>");
		$mail->setBcc('webmaster@sbrafh.org.br');
		$mail->setSubject(sprintf('Renovação de Inscrição de Associado Num. %06d', $this->view->id));
		// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
		$text = strip_tags($eBody);
		$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
		$attachment = $mail->getFile($pdf->file);
		$mail->addAttachment($attachment, basename($pdf->file), 'application/pdf');
		// cópia para o administrativo
		$result = $mail->send(array($mailto));  // envia copia para o usuario
		$this->db->query(sprintf("UPDATE cadastro SET enviado='%s' WHERE idCadastro=%d", date('m.d.Y'), $this->view->id)); 			
	   return("Email enviado com sucesso para\n$cadastro->nome\n$cadastro->email");
		}





	function cadastroAction(){
	
		$this->view->where .= sprintf("/Cadastro: %s", ($this->view->st==0) ? 'SÓCIOS INATIVOS' : 'SÓCIOS ATIVOS');
		
		if($this->_request->getParam('boleto',0)){
			$this->view->message = $this->enviarboleto();
			}
		
		if($this->_request->getParam('gerarboleto',0)){
			//die('voltei');
			$this->view->id = (int)$this->_request->getParam('gerarboleto',0);
			$cadastro = $this->fc->dbReader($this->db, "SELECT * FROM cadastro WHERE idCadastro={$this->view->id}");
			
			try{
				Zend_Loader::loadClass('Financeiro');
				$financeiro = new Financeiro();				
				$offSet = 7;
				if ($cadastro->uf=='BA') $_cCusto=2;
				elseif ($cadastro->uf=='MG') $_cCusto=3;
				elseif ($cadastro->uf=='SP') $_cCusto=4;
				elseif ($cadastro->uf=='RJ') $_cCusto=5;
				elseif ($cadastro->uf=='PR') $_cCusto=6;
				elseif ($cadastro->uf=='RN') $_cCusto=7;
				else $_cCusto=1;
				$valor = ($cadastro->formacao == 'EST. GRADUAÇÃO: FARMÁCIA') ? $this->view->taxaAnuidade/2 : $this->view->taxaAnuidade;
				
				if($cadastro->validade < date("Y-m-d", time() + ($offSet * 86400))){
					// se a validade estiver expirando, faz como vencimento data do sistema + 4 dias
					$vcto = date("m.d.Y", time() + ($offSet * 86400));
					}
				else {
					// mantem a data de vencimento, a data de validade					
					$vcto = $this->fc->dmy2msSql($this->fc->Ymd2dmY($cadastro->validade));					
					} 
					
				//$this->fc->debug($cadastro);
				$_data = array(
					'idCadastro' => $this->view->id,
					'numDoc' =>  sprintf('%05d%d%d', $this->view->id, date('y'), date('y')+1),
					'tipoDoc' => 2,
					'conta' => 41101001,
					'historico' => 'TAXA DE ASSOCIADO - RENOVAÇÃO',
					'valor' => $valor,
					'vcto' => $vcto,
					'total' => $valor,
					'formaPgto' => 2,
					'boleto' => sprintf('%05d%d%d', $this->view->id, date('y'), date('y')+1),
					'status' => 2,
					'observacoes' => 'GERADO PELO ATENDIMENTO',
					'idCentroCusto' => $_cCusto,
					);
				
				//$this->fc->debug($_data); die();
				
				$this->view->idBoleto = (int)$this->fc->dbReader($this->db, sprintf("SELECT idFinanceiro FROM financeiro WHERE 
					(numDoc='%s') AND (idCadastro=%d) AND (valor=%s)", $_data['numDoc'], $this->view->id, $valor))->idFinanceiro;
				if($this->view->idBoleto==0)
					$this->view->idBoleto = $financeiro->insert($_data);
				else{
					$this->db->query("UPDATE financeiro SET vcto='$vcto' WHERE idFinanceiro={$this->view->idBoleto}");
					}
				$this->view->message = $this->enviarboleto();							
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}		
			}
		
		if($this->view->st==99){
			if($this->_request->getPost('btRange')){
				$_inicio = $this->fc->dmY2msSql($_POST['inicio']);
				$_final = $this->fc->dmY2msSql($_POST['final']);
				}
			else{
				// pega a data do sistema - 15 dias
				$_hoje = $this->fc->dmY2time(date('d/m/Y'));
				$_inicio = date('m.d.Y', $_hoje + (86400 * 15));
				$_final = date('m.d.Y', $_hoje + (86400 * 18));
				$_POST['inicio'] = date('d/m/Y', $_hoje + (86400 * 15));
				$_POST['final'] = date('d/m/Y', $_hoje + (86400 * 18));
				}
			
			$sql = "SELECT idCadastro, nome, cidade, uf, validade, enviado
				FROM cadastro WHERE (validade BETWEEN '$_inicio' AND '$_final') ORDER BY validade, nome";
			$this->view->where .= sprintf(' - A vencer: %s-%s', date('d/m', $_hoje + (86400 * 15)), date('d/m/Y', $_hoje + (86400 * 18)));	
			}
		else
			$sql = "SELECT idCadastro, nome, cidade, uf, validade, enviado
				FROM cadastro WHERE status={$this->view->st} ORDER BY nome";
			
		//echo "<br /><br /><br />$sql";
		
		
		if ($this->_request->isPost()){
			if($this->_request->getPost('btSearch')){
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$f = new Zend_Filter_StripTags();					
				$_idcadastro = (int)ereg_replace("([^0-9])",'', $this->_request->getPost('busca'));
				$_cpf = ereg_replace("([^0-9])",'', $this->_request->getPost('busca'));
				$_nome = trim($f->filter($this->_request->getPost('busca')));
				if($_idcadastro > 0)
					$sql = "SELECT idCadastro, nome, cidade, uf, 
						CONVERT(CHAR(10), dataReg, 103) AS datareg, CONVERT(CHAR(10), validade, 103) AS validade
						FROM cadastro WHERE idCadastro='$_idcadastro'";
				elseif(strlen($_cpf)==11)
					$sql = "SELECT idCadastro, nome, cidade, uf, 
						CONVERT(CHAR(10), dataReg, 103) AS datareg, CONVERT(CHAR(10), validade, 103) AS validade
						FROM cadastro WHERE cpf='$_cpf'";
				else
					$sql = "SELECT idCadastro, nome, cidade, uf, 
						CONVERT(CHAR(10), dataReg, 103) AS datareg, CONVERT(CHAR(10), validade, 103) AS validade
						FROM cadastro WHERE nome like '%$_nome%' ORDER BY nome";
				}
			elseif($this->_request->getPost('btBaixa')){
				//$this->fc->debug($_POST); die();
				$_dataPgto = $this->fc->dmY2mssql($_POST['dataPgto']);
				$_idFinanceiro = (int)$_POST['idFinanceiro'];
				$_validade = date("m.d.Y", $this->view->fc->dmy2time($this->_request->getPost('dataPgto')) + (365 * 86400));
				$sql = "UPDATE cadastro SET validade='$_validade', status = 1 WHERE idCadastro={$this->view->id}";
				//echo "$sql <br />";
				$this->db->query($sql);
				$sql = "UPDATE financeiro SET pgto='$_dataPgto', status = 4 WHERE idFinanceiro=$_idFinanceiro";
				//echo "$sql <br />";
				$this->db->query($sql);
				$this->view->message = "Anuidade baixada com sucesso.\n\n";
				}
			//echo "sql=$sql";								
			}

		if($this->view->id){
			$sql = "SELECT *, 
				CONVERT(CHAR(10), dataReg, 103) AS datareg, 
				CONVERT(CHAR(10), validade, 103) AS validade,
				CONVERT(CHAR(10), ultAcesso, 103) AS ultAcesso,
				CONVERT(CHAR(10), dataNcto, 103) AS dataNcto
				FROM cadastro WHERE idCadastro = {$this->view->id}";
			//echo "sql=$sql";
			//$query = $this->fc->dbReader($this->db, $sql);
			$this->view->cadastro = $this->fc->dbReader($this->db, $sql);
			$crypt = new Crypt(CRYPT_MODE_HEXADECIMAL);
			if($this->view->cadastro->pwd)
				$this->view->cadastro->senha = $crypt->Decrypt($this->view->cadastro->pwd);				
			$sql = "SELECT idFinanceiro, numDoc, dataLct, vcto, pgto, valor, historico
					FROM financeiro WHERE (idCadastro = {$this->view->id})
						ORDER BY idFinanceiro DESC";
			$this->view->financeiro = $this->fc->dbReader($this->db, $sql, true);
			try{
				$yvalidate = substr($this->view->cadastro->validade, 6, 4);
				$this->view->gerar = ($yvalidate <= date('Y')) ? true : false;
				}
			catch(Exception $ex){
				$this->view->gerar = true;
				}			
			$this->view->page = "adm/cadastro.phtml";
			$this->render('index');		
			}
		else {
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(64);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view			
			
			$this->view->page = "adm/cadastros.phtml";
			$this->render('index');
			}
		}




	function agendaAction(){
		$del = (int)$this->_request->getParam('del',0);
		
		if($this->_request->getParam('list',0)){
			if($del) {
				$this->db->query("DELETE FROM agenda WHERE idAgenda=$del");
				$this->view->message = "Registro excluído com sucesso!\n\n";
				}		
			
			$sql = "SELECT idAgenda, CONVERT(CHAR(10), data, 103) AS fdata, descricao, diretor, status FROM agenda
				WHERE (CONVERT(CHAR(10), data, 102) >= '2012.04.01') ORDER BY data desc";

			//$sql = "SELECT idAgenda, CONVERT(CHAR(10), data, 103) AS fdata, descricao, diretor, status FROM agenda
			//	WHERE CONVERT(CHAR(10), data, 102) ORDER BY data desc";

			$dados = $this->fc->dbReader($this->db, $sql, true);
			//$this->fc->debug($this->view->dbtable);
			
			$count=0;
			foreach($dados as $rs):
				if($rs->diretor == 0) $dados[$count]->diretor = null;
				else{
					$temp = explode (' ', $this->fc->dbReader($this->db, "SELECT nome FROM cadastro WHERE idCadastro=$rs->diretor")->nome);
					$dados[$count]->nome = $temp[0];
					}				
				$count++;
			endforeach;
			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(32);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
			
			
			$this->view->where .= "/Agenda da Diretoria";
			$this->view->page = "adm/agenda.phtml";
			$this->render('index');
			}
		else{
			if ($this->_request->isPost()){
				try{
					Zend_Loader::loadClass('Zend_Filter_StripTags');
					$f = new Zend_Filter_StripTags();					
					Zend_Loader::loadClass('Agenda');
					$agenda = new Agenda();
					$_dados = array(
						'data' => $this->fc->dmY2msSql($f->filter($_POST['data'])),
						'descricao' => trim($f->filter($_POST['descricao'])),
						'diretor' => (int)$_POST['diretor'],					
						);
					if(!$_dados['data'])throw new Exception("O campo 'Data' é obrigatório!\n\n");
					elseif(!$_dados['descricao']) throw new Exception("O campo 'Descrição' é obrigatório!\n\n");
					elseif($_dados['diretor'] == 0) throw new Exception("O campo 'Diretor' é obrigatório!\n\n");
					if($this->view->id){
						$agenda->update($_dados, "idAgenda={$this->view->id}");
						$this->view->message = "Registro atualizado com sucesso!!!\n\n";
						}
					else{
						$this->view->id = $agenda->insert($_dados);						
						$this->view->message = "Novo Registro inserido com sucesso!!!\n\n";
						}
						
					
					if((int)$_POST['twitter']==1):
						//$_message = $_dados['descricao'];
						$remotelink = sprintf(" Acesse: http://www.sbrafh.org.br%s/index/agenda/", $this->view->baseUrl);
						$len = strlen($remotelink)+1;
						$max = 140 - $len;
						$_message = $this->fc->str_reduce($_dados['descricao'], $max);
						//if(strlen($_message . $remotelink) > 140) $_message = substr($_message, 0, 140-$len);
						$_message .= $remotelink;

						Zend_Loader::loadClass('TwitterOAuth');					
						//die("aqui");
						$consumer_key = 'g0gDKhzlEi96MbDjQSzvA';
						$consumer_secret = 'p4AnZ7Ab3RPWHxt04h5qrpvegqhbfmAEFaISJtS5W4';  
						$oauth_token = '534822271-ukfhOGPmZdIt84nBspH6z7FtvuJSAN2FpouslUZa';  
						$oauth_token_secret = 'PmJumITJ0HCf05P3ifzOA6mumXZ1Xr4jUDMrID2HbM';  
											 
						$connection = new TwitterOAuth(  
						    $consumer_key,  
						    $consumer_secret,  
						    $oauth_token,  
						    $oauth_token_secret  
							);					
						
						$result = $connection->get('account/verify_credentials',array());					  
						if(property_exists($result,'error')){  
							throw new Exception("Não foi possível conectar com o twitter.");
							}
						// envia um post
						
						$result = $connection->post(  
							'statuses/update', array('status' => utf8_encode($_message))  
							);
					
						$this->view->message .= "\n\nMensagem publicada no mural do Twitter\n\n";					
					endif;
					}
				catch(Exception $ex){
					$this->view->message = $ex->getMessage();
					}
				}			
			
			elseif($this->view->id > 0){
				$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT CONVERT(CHAR(10), data, 103) AS data, descricao, diretor, status FROM agenda
					WHERE idAgenda = {$this->view->id}"));
				}
			else{
				$_POST['data'] = date('d/m/Y');
				$_POST['descricao'] = null;
				$_POST['diretor'] = $this->view->user->idCadastro;
				$_POST['status'] = 1;					
				}
			$this->view->diretores = $this->fc->dbReader($this->db, "SELECT idCadastro, nome FROM cadastro WHERE (diretoria=1) ORDER BY nome", true);
			$this->render('formAgenda');	
			}
		}




	function jornalAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del) {
			$this->db->query("DELETE FROM downloads WHERE id=$del");
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		elseif ($_POST['btSend']) {
			try{
				$f = new Zend_Filter_StripTags();
				
				
				if ($_FILES['pdfFile']['name']!=''){ 
					$ext = explode('.',$_FILES['pdfFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if($ext != 'pdf')throw new Exception("O formato do arquivo deve ser em PDF.\n\n");
					$file = sprintf('%08x', time());
					
					$pdffile = "$file.$ext";
					
					if(!move_uploaded_file($_FILES['pdfFile']['tmp_name'], "../../jornais/$pdffile"))
						throw new Exception("Erro transferindo arquivo PDF !\n\n");
	
					$ext = explode('.',$_FILES['icoFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if($ext != 'jpg|gif|png')
						throw new Exception("O formato do arquivo de Imagem deve ser em JPG, GIF ou PNG.\n\n");
					
					$icofile = "$file.$ext";
					$target = sprintf("..%s/public/jornais/$icofile", $this->view->baseUrl);
					if(!move_uploaded_file($_FILES['icoFile']['tmp_name'], $target))
						throw new Exception("Erro transferindo arquivo de Imagem!\n\n");
					
					//public function createthumb($name, $new_w, $new_h, $path)
					$this->fc->createthumb($target, 80, 107, $target);
					}
					
				
				Zend_Loader::loadClass('Downloads');
				$download = new Downloads();
				$_dados = array(
					'titulo' => trim($f->filter($_POST['titulo'])),
					'pasta' => trim($f->filter($_POST['folder'])),
					'restrito' => (int)$_POST['restrito'],
					'status' => (int)$_POST['status'],			
					);				

				if(!$_dados['titulo'])throw new Exception("O campo 'Título' é obrigatório!\n\n");
				elseif(!$_dados['pasta'])throw new Exception("O campo 'Pasta destino' é obrigatório!\n\n");
				//elseif(!$_dados['restrito'])throw new Exception("O campo 'Restrito' é obrigatório!\n\n");
				//elseif(!$_dados['status'])throw new Exception("O campo 'Status' é obrigatório!\n\n");
			
				if($this->view->id > 0){
					if($pdffile){
						$_dados['link'] = $pdffile;
						$_dados['icone'] = $icofile;						
						}
					
					$download->update($_dados, "id=". $this->view->id);
					$this->view->message = "Registro atualizado com sucesso!\n\n";
					}
				else{
					$_dados['link'] = $pdffile;
					$_dados['icone'] = $icofile;	
					$this->view->id = $download->insert($_dados);
					$this->view->message = "Novo Registro inserido com sucesso!\n\n";				
					}				
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		if($this->_request->getParam('add',0)){
			$this->view->id=0;
			$_POST['pasta'] = 'jornais';
			$_POST['restrito'] = 1;
			$_POST['status'] = 1;
			$this->view->action = sprintf('%s/jornal/id/0', $this->view->links);					
			$this->render('form-download');			
			}
		elseif($this->view->id){
			$sql = "SELECT id, CONVERT(CHAR(10), datareg, 103) AS data, titulo, pasta, link, icone, restrito, status
				FROM downloads WHERE id=" . $this->view->id;
			$_POST = get_object_vars($this->fc->dbReader($this->db, $sql));
			$this->view->action = sprintf('%s/jornal/id/%d', $this->view->links, $this->view->id);						
			$this->render('form-download');
			}
		else{
			$sql = "SELECT id, CONVERT(CHAR(10), datareg, 103) AS data, titulo, pasta, link, icone, restrito, status
				FROM downloads WHERE pasta='jornais' ORDER BY datareg DESC";
			$this->view->dbtable = $this->fc->dbReader($this->db, $sql, true);			
			
			$this->view->where .= "/Jornal SBRAFH";
			$this->view->page = "adm/jornal.phtml";
			$this->render('index');
			}
		}





	function libAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del) {
			$this->db->query("DELETE FROM biblioteca WHERE id=$del");
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		elseif ($_POST['btSend']) {
			try{
				$f = new Zend_Filter_StripTags();
				
				
				if ($_FILES['pdfFile']['name']!=''){ 
					$ext = explode('.',$_FILES['pdfFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					//if($ext != 'pdf')throw new Exception("O formato do arquivo deve ser em PDF.\n\n");
					$file = uniqid() . '.' . $ext;
					
					if(!move_uploaded_file($_FILES['pdfFile']['tmp_name'], "../../biblioteca/$file"))
						throw new Exception("Erro transferindo arquivo !\n\n");
					}
					
				
				Zend_Loader::loadClass('Biblioteca');
				$biblioteca = new Biblioteca();
				$_dados = array(
					'titulo' => trim($f->filter($_POST['titulo'])),
					'descricao' => $_POST['descricao'],
					'keywords' => trim($f->filter($_POST['keywords'])),
					'secao' => trim($f->filter($_POST['secao'])),
					'link' => trim($f->filter($_POST['link'])),
					'restrito' => (int)$_POST['restrito'],
					'status' => (int)$_POST['status'],			
					);
					
				if($file)
					$_dados['arquivo'] = $file;

				if(!$_dados['titulo'])throw new Exception("O campo 'Título' é obrigatório!\n\n");
				elseif(!$_dados['descricao'])throw new Exception("O campo 'Descricao' é obrigatório!\n\n");
				elseif(!$_dados['secao'])throw new Exception("O campo 'Seção' é obrigatório!\n\n");
				//elseif(!$_dados['restrito'])throw new Exception("O campo 'Restrito' é obrigatório!\n\n");
				//elseif(!$_dados['status'])throw new Exception("O campo 'Status' é obrigatório!\n\n");
			
				if($this->view->id > 0){					
					$biblioteca->update($_dados, "id=". $this->view->id);
					$this->view->message = "Registro atualizado com sucesso!\n\n";
					}
				else{
					//$this->fc->debug($_dados)	
					$this->view->id = $biblioteca->insert($_dados);
					$this->view->message = "Novo Registro inserido com sucesso!\n\n";				
					}				
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		if($this->_request->getParam('add',0)){
			$this->view->id=0;
			$_POST['secao'] = '';
			$_POST['restrito'] = 0;
			$_POST['status'] = 1;
			$this->view->action = sprintf('%s/lib/id/0', $this->view->links);					
			$this->render('form-lib');			
			}
		elseif($this->view->id){
			$sql = "SELECT id, CONVERT(CHAR(10), datareg, 103) AS data, titulo, descricao, keywords, secao, arquivo, link, restrito, status
				FROM biblioteca WHERE id=" . $this->view->id;
			$_POST = get_object_vars($this->fc->dbReader($this->db, $sql));
			$this->view->action = sprintf('%s/lib/id/%d', $this->view->links, $this->view->id);						
			$this->render('form-lib');
			}
		elseif($this->view->view){
			$arquivo = $this->fc->dbReader($this->db, "SELECT arquivo FROM biblioteca WHERE id={$this->view->view}")->arquivo;
			$this->view->fullpath = "../../biblioteca/$arquivo";
			$this->render('reader');
			}
		else{
			$sql = "SELECT id, CONVERT(CHAR(10), datareg, 103) AS data, titulo, descricao, keywords, secao, arquivo, link, restrito, status
				FROM biblioteca ORDER BY secao, titulo";
			
			
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(64);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
			
			
			$this->view->where .= "/Biblioteca Virtual";
			$this->view->page = "adm/lib.phtml";
			$this->render('index');
			}
		}






	function memoriasAction(){
		$del = (int)$this->_request->getParam('del',0);	
		$lib = (int)$this->_request->getParam('lib',0);		
		if($del){
			// remove a foto da pasta
			$urlfoto = $this->fc->dbReader($this->db, "SELECT urlfoto FROM memorias WHERE id=$del")->urlfoto;
			@unlink(sprintf('%s/public/memorias/%s', $this->view->baseUrl, $urlfoto));
			// apaga o registro da tabela
			$sql = "DELETE FROM memorias WHERE id=$del"; //echo $sql;
			$this->db->query($sql);
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		if($lib){
			$sql = "UPDATE memorias SET status=1 WHERE id=$lib"; //echo $sql;
			$this->db->query($sql);
			$this->view->message = "Registro alterado com sucesso!\n\n";
			}
		elseif ($_POST['btSendMemories']) {
			try{
				$f = new Zend_Filter_StripTags();			

				$_dados = array(
					'nome' => trim($f->filter($this->_request->getPost('nome'))),
					'email' => trim($f->filter($this->_request->getPost('email'))),
					'titulo' => trim($f->filter($this->_request->getPost('titulo'))),
					'legenda' => trim($f->filter($this->_request->getPost('legenda'))),
					'texto' => trim($f->filter($this->_request->getPost('texto'))),
					'status' => (int)$this->_request->getPost('status'),			
					);
					
				if (empty($_dados['nome']))
					throw new Exception("O campo nome é obrigatório.\n\n");
				elseif (empty($_dados['email']))
					throw new Exception("O campo email é obrigatório.\n\n");
				elseif (empty($_dados['titulo']))
					throw new Exception("O campo título é obrigatório.\n\n");	
				elseif (empty($_dados['texto']))
					throw new Exception("O campo texto é obrigatório.\n\n");
							
				Zend_Loader::loadClass('Memorias');
				$memoria = new Memorias();				
				$memoria->update($_dados, "id=". $this->view->id);
				$this->view->message = "Registro atualizado com sucesso!\n\n";		
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		if($this->view->id){
			$sql = "SELECT * FROM memorias WHERE id={$this->view->id}";
			$this->view->memoria = $this->fc->dbReader($this->db, $sql);
			if($this->_request->getParam('view',0)){
				$this->view->coments = $this->fc->dbReader($this->db, "SELECT * FROM memorias WHERE (sub={$this->view->id})", true);
				//$this->render('view-memorias');
				$this->view->where .= "/Minhas Memórias";
				$this->view->page = "adm/view-memorias.phtml";
				$this->render('index');
				}
			else{
				$_POST = get_object_vars($this->view->memoria);
				$this->render('form-memorias');				
				}
			}
		else{
			$sql = "SELECT id, data, titulo, nome, status FROM memorias WHERE (sub=0) ORDER BY data DESC";
			$dados = $this->fc->dbReader($this->db, $sql, true);
			$count=0;
			foreach($dados as $rs):
				$dados[$count]->coments = $this->fc->dbReader($this->db, "SELECT COUNT(*) AS total FROM memorias WHERE (sub=$rs->id)")->total;
				$count++;
			endforeach;
			
			//$this->fc->debug($dados);
						
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(64);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
						
			$this->view->where .= "/Minhas Memórias";
			$this->view->page = "adm/list-memorias.phtml";
			$this->render('index');
			}
		}







	function newsAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del) {
			$this->db->query("DELETE FROM noticias WHERE id=$del");
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		elseif ($_POST['btSend']) {
			//$this->fc->debug($_POST); 
			//$this->fc->debug($_FILES);
			try{
				$f = new Zend_Filter_StripTags();		
				if ($_FILES['userFile']['name']!=''){ 	
					$ext = explode('.',$_FILES['userFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if($ext != 'jpg')
						throw new Exception("aQUI O formato do arquivo de Imagem deve ser em JPG.\n\n");
					
					$image = sprintf('%s.%s', uniqid(), $ext);
					$thumb = sprintf('%s.%s', uniqid(), $ext);
					$_target = sprintf("..%s/public/news/$image", $this->view->baseUrl);
					$_thumb = sprintf("..%s/public/news/thumbs/$thumb", $this->view->baseUrl);
					if(!move_uploaded_file($_FILES['userFile']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo de Imagem!\n\n");
						
					list($width_orig, $height_orig) = getimagesize($_target); // pega o tamanho original da imagem
					$fator_w = 145 / $width_orig;
					$fator_h = 109 / $height_orig;
					
					$dim = array(	
						'width' => 145,
						'height' => 109,
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
					elseif($dim['tmp_width'] < $dim['width']){
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
					if($ext=='phtml')
						$_target = "/home/storage/1/1c/1c/sbrafh/public_html/novosite/application/views/scripts/news/$filename";
					else
						$_target = sprintf("..%s/public/news/$filename", $this->view->baseUrl);
						
					if(!move_uploaded_file($_FILES['arquivo']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo $filename para $_target! - dir=[$dir]\n\n");
					$_POST['link'] = "news/$filename";		
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
					$news->update($_dados, "id=". $this->view->id);
					$this->view->message = "Registro atualizado com sucesso!\n\n";
					}
				else{	
					$this->view->id = $news->insert($_dados);
					$this->view->message = "Novo Registro inserido com sucesso!\n\n";				
					}				

				if(!empty($_POST['twitter'])):
					$_message = trim($_POST['twitter']);
					$remotelink = sprintf(" Acesse: http://www.sbrafh.org.br%s/index/news/id/%d", $this->view->baseUrl, $this->view->id);
					$len = strlen($remotelink) + 5;
					if(strlen($_message . $remotelink) > 140) $_message = substr($_message, 0, 140-$len);
					$_message .= $remotelink;
					
					
					Zend_Loader::loadClass('TwitterOAuth');					
					//die("aqui");
					$consumer_key = 'g0gDKhzlEi96MbDjQSzvA';
					$consumer_secret = 'p4AnZ7Ab3RPWHxt04h5qrpvegqhbfmAEFaISJtS5W4';  
					$oauth_token = '534822271-ukfhOGPmZdIt84nBspH6z7FtvuJSAN2FpouslUZa';  
					$oauth_token_secret = 'PmJumITJ0HCf05P3ifzOA6mumXZ1Xr4jUDMrID2HbM';  
										 
					$connection = new TwitterOAuth(  
					    $consumer_key,  
					    $consumer_secret,  
					    $oauth_token,  
					    $oauth_token_secret  
						);					
					
					$result = $connection->get('account/verify_credentials',array());					  
					if(property_exists($result,'error')){  
						throw new Exception("Não foi possível conectar com o twitter.");
						}
					// envia um post
					
					$result = $connection->post(  
						'statuses/update', array('status' => utf8_encode($_message))  
						);
					
					//$this->fc->debug($result);
					
					
					$this->view->message .= "\n\nMensagem publicada no mural do Twitter\n\n";
				endif; // twitter
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}

		if($this->_request->getParam('add',0)){
			$this->view->id=0;
			$_POST['pasta'] = 'news';
			$_POST['restrito'] = 1;
			$_POST['status'] = 1;
			$this->view->action = sprintf('%s/news/id/0', $this->view->links);					
			$this->render('form-news');			
			}
		elseif($this->view->id){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM noticias WHERE id=" . $this->view->id;
			$_POST = get_object_vars($this->fc->dbReader($this->db, $sql));
			$this->view->action = sprintf('%s/news/id/%d', $this->view->links, $this->view->id);
			$this->view->uniqid = uniqid();
			$this->session->setSessVar('uniqid', $this->view->uniqid);						
			$this->render('form-news');
			}
		elseif($this->view->view){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM noticias WHERE id=" . $this->view->view;
			$this->view->news = $this->fc->dbReader($this->db, $sql);				
			$this->view->where .= "/SBRAFH Preview-News";
			if(stristr($this->view->news->link,'.phtml'))
				$this->view->page = $this->view->news->link;
			else
				$this->view->page = "preview-news.phtml";
			$this->render('index');
			}
		else{
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
				FROM noticias
					WHERE (CONVERT(CHAR(10), data, 102) >= '2012.04.01') 
						ORDER BY data DESC";
			
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(32);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view			
			
			$this->view->where .= "/SBRAFH News";
			$this->view->page = "adm/news.phtml";
			$this->render('index');
			}
		}









	function consultaAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del) {
			$this->db->query("DELETE FROM consultaPublica WHERE id=$del");
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		elseif ($_POST['btSend']) {

			try{
				$f = new Zend_Filter_StripTags();		
				Zend_Loader::loadClass('Consulta');
				$consulta = new Consulta();
				$_dados = array(
					'data' => $this->fc->dmY2msSql($_POST['data']),
					'titulo' => trim($f->filter($_POST['titulo'])),
					'resumo' => trim($f->filter($_POST['resumo'])),
					);
									
				//$this->fc->debug($_POST); 
				//$this->fc->debug($_FILES);
				if(!$_dados['titulo'])throw new Exception("O campo 'Título da Consulta Pública' é obrigatório!\n\n");
				elseif(!$_dados['resumo'] )throw new Exception("O campo 'Resumo da Consulta Pública' é obrigatório!\n\n");

				// move o arquivo enviado da pasta tmp para a pasta public/consultas
				if ($_FILES['userFile']['name']!=''){
					$t = explode('.', $_FILES['userFile']['name']);
					$ext = $t[count($t)-1];					
					$filename = sprintf('%s.%s', basename($_FILES['userFile']['tmp_name']), $ext);
					$_target = sprintf("..%s/public/consultas/$filename", $this->view->baseUrl);

					if(!move_uploaded_file($_FILES['userFile']['tmp_name'], $_target))
						throw new Exception("Erro transferindo arquivo para o servidor!\n\n");
					$_dados['arquivo'] = "consultas/$filename";		
					}


				//$this->fc->debug($_dados);
				//die();
				// grava o registro
				if($this->view->id > 0){
					$consulta->update($_dados, "id=". $this->view->id);
					$this->view->message = "Registro atualizado com sucesso!\n\n";
					}
				else{	
					$this->view->id = $consulta->insert($_dados);
					$this->view->message = "Novo Registro inserido com sucesso!\n\n";				
					}				
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			} // final do post
			
			
			

		if($this->_request->getParam('add',0)){
			$this->view->id=0;
			$_POST['status'] = 1;
			$this->view->action = sprintf('%s/consulta/id/0', $this->view->links);					
			$this->render('form-consulta');			
			}
		elseif($this->view->id){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM consultaPublica WHERE id={$this->view->id}";
			$_POST = get_object_vars($this->fc->dbReader($this->db, $sql));
			$this->view->action = sprintf('%s/consulta/id/%d', $this->view->links, $this->view->id);
			$this->view->uniqid = uniqid();
			$this->session->setSessVar('uniqid', $this->view->uniqid);						
			$this->render('form-consulta');
			}
		elseif($this->view->view){
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS data
				FROM consultaPublica WHERE id={$this->view->view}";
			$this->view->news = $this->fc->dbReader($this->db, $sql);				
			$this->view->where .= "/SBRAFH Consultas Públicas";
			if(stristr($this->view->news->link,'.phtml'))
				$this->view->page = $this->view->news->link;
			else
				$this->view->page = "preview-news.phtml";
			$this->render('index');
			}
		else{
			$sql = "SELECT *, CONVERT(CHAR(10), data, 103) AS datareg
				FROM consultaPublica
					ORDER BY data DESC";
			
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(32);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view			
			
			$this->view->where .= "/SBRAFH Consultas Públicas";
			$this->view->page = "adm/consulta.phtml";
			$this->render('index');
			}
		}
















	function galleryAction(){
		$this->fc->debug($_POST);
		$this->fc->debug($_FILES);
		$this->view->idGallery = (int)$_POST['idGallery'];
		if ($this->_request->isPost()){
			try{
				$f = new Zend_Filter_StripTags();
				Zend_Loader::loadClass('Gallery');
				$gallery = new Gallery();
				$_dados = array();				
				foreach($_POST as $field => $val):
					$t= explode('_', $field);
					$_id = (int)$t[1];
					if($t[0]=='data') $_dados['data'] = $this->fc->dmY2msSql($val);
					elseif($t[0]=='evento') $_dados['evento'] = trim($f->filter($val));
					elseif($t[0]=='btUpdate') $gallery->update($_dados, "idGallery=$_id");
					elseif($t[0]=='btSave') $gallery->insert($_dados);
					elseif($t[0]=='btDel') $gallery->delete("idGallery=$_id");
					elseif($t[0]=='btOpen') {
						$this->view->idGallery = $_id;
						}
				endforeach;

				if($_POST['btUpload']){
					$ext = explode('.',$_FILES['userFile']['name']);
					$ext = strtolower($ext[count($ext)-1]);
					if($ext != 'jpg|jpeg')
						throw new Exception("O formato do arquivo de Imagem deve ser em JPG.\n\n");
					
					$image = sprintf('%s.%s', basename($_FILES['userFile']['tmp_name']), $ext);
					$_target = sprintf("..%s/public/gallery/$image", $this->view->baseUrl);
					$_thumb = sprintf("..%s/public/gallery/thumbs/$image", $this->view->baseUrl);
					if(!move_uploaded_file($_FILES['userFile']['tmp_name'], $_target))
						throw new Exception(sprintf("Erro transferindo arquivo de Imagem!\n\nOrigem: %s\n\nDestino: %s\n\n",
							$_FILES['userFile']['tmp_name'], $_target));
						
					list($width_orig, $height_orig) = getimagesize($_target); // pega o tamanho original da imagem
					$fator_w = 75 / $width_orig;
					$fator_h = 56 / $height_orig;
					
					$dim = array(	
						'width' => 75,
						'height' => 56,
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
					elseif($dim['tmp_width'] < $dim['width']){
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
					$_dados['data'] = date('m.d.Y');
					$_dados['urlFoto'] = "../Gallery/$image";
					$_dados['thumbs'] = "../Gallery/Thumbs/$image";
					$_dados['idEvento'] = $this->view->idGallery;
					$gallery->insert($_dados);					
					}
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}		
			}
		if($this->view->idGallery){
			$sql = sprintf("SELECT * FROM Gallery WHERE (idGallery=%d) OR (idEvento=%d) ORDER BY idGallery", 
				$this->view->idGallery, $this->view->idGallery);		
			$this->view->gallery = $this->fc->dbReader($this->db, $sql, true);			
			$this->view->where .= "/SBRAFH Gallery";
			$this->view->page = "adm/gallery.phtml";
			$this->render('index');
			}
		else{
			$sql = "SELECT * FROM Gallery WHERE idEvento=0 ORDER BY data DESC";		
			$dados = $this->fc->dbReader($this->db, $sql, true);			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(8);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view			
			
			$this->view->where .= "/SBRAFH Gallery";
			$this->view->page = "adm/galleries.phtml";
			$this->render('index');
			}
		}



		function inscricaoAction(){
			try{
				$_id = (int)$this->view->id;
				if($_id==0)
					throw new Exception("Erro: O ID da inscrição não foi passado!\n\n");
				$this->view->inscricao = $this->fc->dbReader($this->db, "SELECT * FROM inscricoesCursos WHERE id=$_id");
				if ($this->_request->isPost()){
					//$this->fc->debug($_POST);
					$pgto = $this->fc->dmY2Ymd($this->_request->getPost('pgto'));
					$check = (int)$this->_request->getPost('compareceu');
					if(empty($pgto)){
						$this->db->query("UPDATE inscricoesCursos SET compareceu=$check WHERE id=$_id");
						}
					else{
						$this->db->query("UPDATE inscricoesCursos SET pgto='$pgto', compareceu=$check WHERE id=$_id");
						$this->db->query("UPDATE financeiro SET pgto='$pgto', status=4 WHERE idCurso=$_id");
						}
					}
				else{
					$_POST['pgto'] = ($this->view->inscricao->pgto) ? $this->fc->Ymd2dmY($this->view->inscricao->pgto) : null;
					$_POST['compareceu'] = $this->view->inscricao->compareceu;
					}				
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			$this->view->back = sprintf('%s/inscricoes/rg/%s/dt/%s', $this->view->links, $this->view->rg, $this->view->dt);
			$this->render('inscricaoCursos');
			}




	// seminario SENAC8
	/*
	function seminariosenacAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del){
			$sql = "DELETE FROM inscricoesCursos WHERE id=$del"; //echo $sql;
			$this->db->query($sql);
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		if($this->_request->getParam('export',0)){
			$this->view->dbtable = $this->fc->dbReader($this->db, "SELECT *, 
				CONVERT(CHAR(10), data, 103) AS data, 
				CONVERT(CHAR(10), vcto, 103) AS vcto, 
				CONVERT(CHAR(10), pgto, 103) AS pgto, 
				CONVERT(CHAR(10), datareg, 103) AS datareg
					FROM inscricoesCursos WHERE regional='{$this->view->rg}'ORDER BY nome", true);
			$count=0;
			foreach($this->view->dbtable as $rs):
				if($rs->status < 0)
					$this->view->dbtable[$count]->status = "NÃO ASSOCIADO";
				elseif($rs->status == 0)
					$this->view->dbtable[$count]->status = "ASSOCIADO INATIVO";
				else
					$this->view->dbtable[$count]->status = "ASSOCIADO ATIVO";
				$this->view->dbtable[$count]->valor = number_format($rs->valor, 2, ',','.');
				$count++;			
			endforeach;		
					
			//$this->fc->debug($this->view->dbtable); die();
			$this->render('export2excel');
			}
		else{
			$sql = "SELECT id, data, nome, formacao, valor, pgto, compareceu, status FROM inscricoesCursos WHERE regional='SP' ORDER BY nome";
			$dados = $this->fc->dbReader($this->db, $sql, true);					
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(64);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
						
			$this->view->where .= "/Seminário SENAC (20/10/2012) - Lista de Inscritos";
			$this->view->caption = "Seminário SENAC (20/10/2012) - Lista de Inscritos";
			$this->view->page = "adm/list-cursos.phtml";
			$this->view->rg = 'SP';
			$this->render('index');
			}
		}
	*/



	function inscricoesAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del){
			$sql = "DELETE FROM inscricoesCursos WHERE id=$del"; //echo $sql;
			$this->db->query($sql);
			$this->view->message = "Registro excluído com sucesso!\n\n";
			}
		if($this->_request->getParam('export',0) || $this->_request->getParam('presenca',0)){
			$this->view->dbtable = $this->fc->dbReader($this->db, "SELECT *, 
				CONVERT(CHAR(10), data, 103) AS data, 
				CONVERT(CHAR(10), vcto, 103) AS vcto, 
				CONVERT(CHAR(10), pgto, 103) AS pgto, 
				CONVERT(CHAR(10), datareg, 103) AS datareg
					FROM inscricoesCursos WHERE (regional='{$this->view->rg}') AND (data='{$this->view->dt}') ORDER BY nome", true);
			$count=0;
			foreach($this->view->dbtable as $rs):
				if($rs->status < 0)
					$this->view->dbtable[$count]->status = "NÃO ASSOCIADO";
				elseif($rs->status == 0)
					$this->view->dbtable[$count]->status = "ASSOCIADO INATIVO";
				else
					$this->view->dbtable[$count]->status = "ASSOCIADO ATIVO";
				$this->view->dbtable[$count]->valor = number_format($rs->valor, 2, ',','.');
				$count++;			
			endforeach;		
					
			//$this->fc->debug($this->view->dbtable); die();
			if($this->_request->getParam('export',0))
				$this->render('export2excel');
			else
				$this->render('presenca');
			}
		else{
			$sql = "SELECT id, data, nome, formacao, valor, pgto, compareceu, status, obs, CONVERT(CHAR(10), datareg, 103) AS datainsc
				FROM inscricoesCursos WHERE (regional='{$this->view->rg}' AND data='{$this->view->dt}')
					ORDER BY id DESC";
			
			$dados = $this->fc->dbReader($this->db, $sql, true);
			//$this->fc->debug($dados);
			if($dados){
				$query = $this->fc->dbReader($this->db, "SELECT curso, regional FROM inscricoesCursos WHERE id={$dados[0]->id}");
				$this->view->caption = "$query->curso - Regional $query->regional";
				}					
			
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(100);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
						
			$this->view->where .= "Lista de Inscritos - " . $this->view->caption;
			//$this->view->caption = "Seminário SENAC (20/10/2012) - Lista de Inscritos";
			$this->view->page = "adm/list-cursos.phtml";
			$this->render('index');
			}
		}

	


	function eventosAction(){
		$del = (int)$this->_request->getParam('del',0);		
		if($del){
			$sql = "DELETE FROM eventos WHERE idEvento=$del"; //echo $sql;
			$this->db->query($sql);
			//echo $sql;
			$this->view->message = "Registro $del excluído com sucesso!\n\n";
			}


		$this->view->id = $this->_request->getParam('id',0);
	
		if($this->_request->getParam('id',0)){
					
			if (!$this->_request->isPost()){
				if((int)$this->_request->getParam('id',0)==0){
					$_POST['status'] = 1;
					}
				else{
					$_POST = get_object_vars($this->fc->dbReader($this->db, "SELECT * FROM eventos WHERE idEvento=". $this->view->id));
					}
				}			
			else {
				Zend_Loader::loadClass('Zend_Filter_StripTags');
				$f = new Zend_Filter_StripTags();
				
				try{
					$arquivo = basename($_FILES['arquivo']['tmp_name']);
					$formatos = "pdf|jpg|gif|png|doc|docx|xls|xlsx";
					if ($_FILES['arquivo']['name']!=''): 	
						$ext = explode('.',$_FILES['arquivo']['name']);
						$ext = strtolower($ext[count($ext)-1]);
						if(!stristr($formatos, $ext))
							throw new Exception(sprintf("O formato do arquivo é inválido.\n\nFormatos válidos: *.%s", str_replace('|',';*.', $formatos)));
						
						if(!stristr($arquivo, '.')) $arquivo .= ".$ext";
						
						$_target = sprintf("..%s/public/eventos/$arquivo", $this->view->baseUrl);
						if(!move_uploaded_file($_FILES['arquivo']['tmp_name'], $_target))
							throw new Exception(sprintf("Erro transferindo arquivo para o servidor!\n\nArquivo: %s\n\nDestino: %s", $_FILES['arquivo']['tmp_name'], $_target));
						$this->view->message = sprintf("Arquivo %s transferido com sucesso para o servidor\n\n", $_FILES['arquivo']['name']);
					endif;
					
					$_dados = array(
						'data' => $this->fc->dmY2Ymd($this->_request->getPost('data')),
						'titulo' => $f->filter(trim($this->_request->getPost('titulo'))),
						'resumo' => $f->filter(trim($this->_request->getPost('resumo'))),
						'url' => strtolower($f->filter(trim($this->_request->getPost('url')))),
						'arquivo' => $arquivo,
						'status' => (int)$this->_request->getPost('status'),
						);
					if(!$_dados['data'])throw new Exception("O campo 'Data' é obrigatório!\n\n");
					elseif(!$_dados['titulo'])throw new Exception("O campo 'Título' é obrigatório!\n\n");
					elseif(!$_dados['resumo'] && !$_dados['url'] && !$_dados['arquivo'])
						throw new Exception("Preencha o campo 'Resumo' ou\no campo 'Link Externo' ou selecione um Arquivo!\n\n");	

					//$this->fc->debug($_POST);
					//$this->fc->debug($_FILES);
					//$this->fc->debug($_dados);
					
					Zend_Loader::loadClass('Eventos');
					$registro = new Eventos();
					
					if((int)$this->_request->getParam('id',0)==0){
						$this->view->id = $registro->insert($_dados);
						$this->view->message .= "Novo Evento incluído com sucesso!\n\n";
						}
					else{
						$registro->update($_dados, 'idEvento='.$this->view->id);
						$this->view->message .= "Evento atualizado com sucesso!\n\n";
						}
					// grava os dados na tabela
					}
				catch(Exception $ex){
					$this->view->message = $ex->getMessage();
					}
				}
				
			$this->view->action = sprintf("%s/eventos/id/%s", $this->view->links, $this->view->id);
			$this->render('form-eventos');			
			}
		else {
			$dados = $this->fc->dbReader($this->db, "SELECT idEvento, data, titulo, url, arquivo, status FROM eventos WHERE (data > '2011-12-31') ORDER BY data DESC", true);
			$page = intval($this->_getParam('page', 1));
			$paginator = Zend_Paginator::factory($dados);	// Seta a quantidade de registros por página
			$paginator->setItemCountPerPage(64);		
			$paginator->setPageRange(10);	// numero de paginas que serão exibidas		
			$paginator->setCurrentPageNumber($page);	// Seta a página atual		
			$this->view->paginator = $paginator;	// Passa o paginator para a view
			$this->view->where .= " / Agenda de Eventos Anual";
			$this->view->page = "adm/list-eventos.phtml";
			$this->render('index');
			}			
		}


	function monitoresAction(){
		$this->view->curso = $this->fc->dbReader($this->db, "SELECT titulo FROM cursos WHERE id={$this->view->id}")->titulo;
		}



	function logoutAction()
		{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('adm/login');
		}
	}
?>
