<?php
class CursosController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/cursos", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
		$this->view->user = Zend_Auth::getInstance()->getIdentity();			

		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->view->db = $this->db;
		Zend_Db_Table::setDefaultAdapter($this->db);
		$this->view->where .= ":: ";
		$this->view->id = (int)$this->_request->getParam('id',0);
		$this->view->st = (int)$this->_request->getParam('st',0);
		$this->view->uid = (int)$this->_request->getParam('uid',0);

		Zend_Loader::loadClass('Zend_Paginator');
		$this->view->user = Zend_Auth::getInstance()->getIdentity();
		$this->session = SessionWrapper::getInstance();
		
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();		
		}




	function indexAction(){	
		$this->view->where = "Página Inicial";
		}




	function sendlinkAction(){
		$this->view->message = '';
		$this->view->curso = $this->fc->dbReader($this->db, "SELECT titulo FROM cursos WHERE idCurso={$this->view->id}");
		if ($this->_request->isPost()){
			try{
				$namefrom = trim($this->filter->filter(strtoupper($_POST['namefrom'])));
				$mailfrom =	trim($this->filter->filter(strtolower($_POST['mailfrom'])));
				$uffrom = trim($this->filter->filter(strtoupper($_POST['uffrom'])));
				$nameto = trim($this->filter->filter(strtoupper($_POST['nameto'])));
				$mailto = trim($this->filter->filter(strtolower($_POST['mailto'])));
				$ufto	= trim($this->filter->filter(strtoupper($_POST['ufto'])));
				
				if(!$namefrom)throw new Exception("O campo 'Seu Nome' é obrigatório !\n\n");
				elseif(!$mailfrom)throw new Exception("O campo 'Seu E-mail' é obrigatório !\n\n");
				elseif(!$uffrom)throw new Exception("O campo 'Seu Estado' é obrigatório !\n\n");
				elseif(!$nameto)throw new Exception("O campo 'Nome do(a) Amigo(a)' é obrigatório !\n\n");
				elseif(!$mailto)throw new Exception("O campo 'E-mail do(a) Amigo(a)' é obrigatório !\n\n");
				elseif(!$ufto)throw new Exception("O campo 'Estado do(a) Amigo(a)' é obrigatório !\n\n");

				$eBody="
				<style>
					#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
					#econtato caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
					#econtato td{padding:4px;border:solid 1px silver;color:#444}
					#econtato .label{text-align:right;color:#444;background:#D9E7F8}
					#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
					#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
					#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
				</style>
				<img src='topoEmail.jpg' border='0'/>	
				<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>
					<caption>Curso: Indicação para amigos</caption>
					<tr>
						<td colspan='2'>
							Olá, <b>$nameto</b>, <b>$namefrom</b> visitou nosso site  
							e indicou o link do curso abaixo para você.<br /><br />
							Clique no link que é do seu interesse também.
					</tr>
					<tr>
						<td class='label'>Curso:</td>
						<td><b>{$this->view->curso->titulo}</b></td>
					</tr>
					<tr>
						<td class='label'>Link:</td>
						<td><a href='http://www.amfar.com.br/site/cursos/open/id/{$this->view->id}/'<b>http://www.amfar.com.br/site/curso/id/{$this->view->id}/</a></b></td>
					</tr>
					<tr>
						<td class=tfooter colspan=2>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
						</td>
					</tr>
				</table>";
		
		
				//Zend_Loader::loadClass('htmlMimeMail');

				//echo "_imagePath=$_imagePath";				
				$mailto = "$nameto <$mailto>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF/Cursos <webmaster@sbrafh.org.br>");
				$mail->setReplayTo(sprintf('%s <%s>', 'Secretaria', 'secretaria@amfar.com.br'));
				$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject("Indicação de Cursos");		
				// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message = sprintf("Sua indicação foi enviada com sucesso para\n%s (%s)\n\nObrigado.\n\n",
					$nameto, str_replace('<','[', str_replace('>',']', $mailto)));
				$_POST['nameto'] = null;
				$_POST['mailto'] = null;
				
				if($_POST['mailing']){
					$db2 = Zend_Registry::get('db2');
					Zend_Db_Table::setDefaultAdapter($db2);
					Zend_Loader::loadClass('Mailing');
					$mailing = new Mailing();
					
					$_dados = array(
						'categ' => 0,
						'nome' => $namefrom,
						'email' => $mailfrom,
						'uf' => $uffrom,
						'status' => 0,
						'dataReg' => date('Y-m-d H:i:s'),
						);

					try{
						//echo "dados 1";
						//$this->fc->debug($_dados);
						$mailing->insert($_dados);
						}
					catch(Exception $ex){
						//$this->view->message = "insert 1 " . $ex->getMessage();
						}
					$_dados['nome'] = $nameto;
					$_dados['email'] = $mailto;
					$_dados['uf'] = $ufto;						

					try{
						//echo "dados 2";
						//$this->fc->debug($_dados);
						$mailing->insert($_dados);
						}
					catch(Exception $ex){
						//$this->view->message = "insert 2 " . $ex->getMessage();
						}

					}
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}			


		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY uf", true);
		$this->getHelper('layout')->disableLayout();		
		$this->render('sendlink');
		}




	function openAction(){
		if($this->view->id){
			$sql = "SELECT * FROM cursos WHERE idCurso={$this->view->id}"; //die($sql);
			$this->view->curso = $this->fc->dbReader($this->db, $sql);
			$this->view->where = ($this->view->curso->status) ? "AMF - Cursos de Especialização / Detalhes" : "AMF - Cursos de Atualização / Detalhes";
			//$this->fc->debug($this->view->curso); die();
			//$this->getHelper('layout')->disableLayout();
			//$this->render('cursos/view');
			}
		}


	
	function listAction(){
		$sql="SELECT idCurso, inicio, titulo, url, status 
			FROM cursos WHERE (ativo=1) AND (status={$this->view->st})
				ORDER BY inicio DESC, titulo";
		//echo $sql;
		$this->view->cursos = $this->fc->dbReader($this->db, $sql, true);		
		$this->view->where = ($this->view->st) ? "AMF - Cursos de Especialização" : "AMF - Cursos de Atualização";
		}


	function inscricaoAction(){
		$this->view->where =  "Cursos/Inscrição online";
		if(!$this->view->user->idCadastro){
			$this->_redirect('auth/login/curso/' . $this->view->id);
			}
		else{
			$sql = "SELECT inicio, final, titulo, valor, parcelas FROM cursos WHERE idCurso={$this->view->id}"; //die($sql);
			$this->view->curso = $this->fc->dbReader($this->db, $sql);
			$this->view->curso->idCadastro = $this->view->user->idCadastro;
			$this->view->curso->nome = $this->view->user->nome;
			$this->view->curso->rg = $this->view->user->rg;
			$this->view->curso->cpf = $this->view->user->cpf;
			if ($this->_request->isPost() && $this->_request->getPost('btConfirm')){
				try{
					$sql="SELECT data FROM alunosCursos WHERE (idAluno={$this->view->user->idCadastro}) AND (idCurso={$this->view->id})";
					$found = $this->fc->dbReader($this->db, $sql)->data;
					if($found)
						throw new Exception(sprintf("Já existe uma inscrição efetuada no dia %s\npara este CPF e este curso!\n\n", $this->fc->Ymd2dmY($found)));

					//$this->fc->debug($this->view->curso);
					
					// procede com a inscrição
					Zend_Loader::loadClass('AlunosCursos');
					$inscricao = new AlunosCursos();
					$_dados = array(
						'idCurso' => $this->view->id,
						'idAluno' => $this->view->user->idCadastro,
						'valorCurso' => $this->view->curso->valor,
						'parcelas' => $this->view->curso->parcelas,
						'valorParcelas' => number_format($this->view->curso->valor/$this->view->curso->parcelas,2,'.',''),
						);
					
					//$this->fc->debug($_dados); die();
					$idInscricao = $inscricao->insert($_dados);
					
					//echo "idInscricao = $idInscricao";
					
					// lança no financeiro
					Zend_Loader::loadClass('Financeiro');
					$financeiro = new Financeiro();

					$_boleto = array(
						'idCadastro' => $this->view->user->idCadastro,
						'idCurso' => $this->view->id,
						'dataLct' => date('m.d.Y'),
						'tipoDoc' => 2,
						'conta' => '41102001',
						'historico' => sprintf('Inscr. Curso: %s', $this->view->curso->titulo),
						'valor' => $_dados['valorParcelas'],
						'total' => $_dados['valorParcelas'],
						'status' => 2,
						'idCentroCusto' => 1,
						);
					
					for ($x=1; $x <= $_dados['parcelas']; $x++):
						if($this->view->id==152){
							$vcto = ($x==1) ? '03.10.2014' : '03.25.2014';
							}
						else{
							if($x==1){
								$vcto = '04.15.' . date('Y');
								}
							else{
								$t=explode('.', $vcto);
								$time = strtotime("$t[0]/$t[1]/$t[2]") + (30 * 86400);
								$vcto = date("m.15.Y", $time);
								}
							}
						
						$_boleto['numDoc'] = sprintf('%04d%03d%02d', $_dados['idAluno'], $_dados['idCurso'], $x);
						$_boleto['vcto'] = $vcto;
						$_boleto['parcela'] = $x;
						$idFinanceiro = $financeiro->insert($_boleto);
						if ($x == 1)
							$this->view->idBoleto = $idFinanceiro;
					endfor;
					
					
					$matricula = sprintf('%04d%03d', $this->view->user->idCadastro, $this->view->id);
					
					$eBody="
					<html>
						<head>
							<title>AMF - Inscrição Online em Cursos</title>
							<style>
								#inscricao{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
								#inscricao caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
								#inscricao td{padding:4px;border:solid 1px silver;color:#444}
								#inscricao .label{text-align:right;color:#444;background:#D9E7F8}
								#inscricao .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
								#inscricao .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
								#inscricao .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
							</style>
						</head>
						<body>						
							<img src='topoEmail.jpg' border='0' />
							<table id='inscricao' cellspacing='2' cellpadding='2' width='600' border='0'>
								<caption>Cursos: Inscrição Online</caption>
								<tr>
									<td class='label'>Matrícula:</td>
									<td><b>$matricula</b></td>
									<td class='label'>Data:</td>
									<td><b>". date('d/m/Y') . "</b></td>
								</tr>
								<tr>
									<td class='label'>Aluno:</td>
									<td><b>{$this->view->user->nome}</b></td>
									<td class='label'>CPF:</td>
									<td><b>". $this->fc->formatCPF($this->view->user->cpf) . "</b></td>
								</tr>
								<tr>
									<td class='label' rowspan=2>Endereço:</td>
									<td rowspan=2>
										 <b>{$this->view->user->ender}, {$this->view->user->num}  {$this->view->user->compl}<br />
										 {$this->view->user->bairro}<br />
										 {$this->view->user->cidade} / {$this->view->user->uf}<br />
										 {$this->view->user->cep}</b>
									</td>
									<td class='label'>RG:</td>
									<td><b>{$this->view->curso->rg}</b></td>
								</tr>
								<tr>
									<td class='label'>Fone::</td>
									<td><b>{$this->view->user->fone}</b></td>
								</tr>
							
								<tr>
									<td class='label'>E-mail:</td>
									<td><b>{$this->view->user->email}</b></td>
									<td class='label'>Cel.:</td>
									<td><b>{$this->view->user->fone2}</b></td>
								</tr>
								<tr>
									<td class='label'>Curso:</td>
									<td colspan='3'><b>{$this->view->curso->titulo}</b></td>
								</tr>
								<tr>
									<td class='label'>Valor Total:</td>
									<td><b>R$ " . number_format($_dados['valorCurso'],2,',','.') . "</b></td>
									<td class='label'>Parcelas:</td>
									<td><b>{$this->view->curso->parcelas} X R$ " . number_format($_boleto['valor'],2,',','.') . "</b></td>
								</tr>
								<tr>
									<td colspan='4' style='text-align:center'>
										<b>Utilize o link abaixo para imprimir o boleto referente à primeira parcela.<br /><br />
										<a href='http://www.amfar.com.br/boletos/boleto.php?id={$this->view->idBoleto}'>
											http://www.amfar.com.br/boletos/boleto.php?id={$this->view->idBoleto}</a>
									</td>
								</tr>
							</table>
						</body>
					</html>";
					
					$mailto = sprintf('%s <%s>', $this->view->user->nome, $this->view->user->email);
					$mail = new htmlMimeMail();
					
					$mail->setReturnPath('webmaster@amfar.com.br');
					$mail->setFrom("AMF/Cursos <webmaster@sbrafh.org.br>");
					$mail->setReplayTo(sprintf('%s <%s>', 'Secretaria', 'secretaria@amfar.com.br'));
					$mail->setCc(sprintf('%s <%s>', 'Secretaria', 'secretaria@amfar.com.br'));
					$mail->setBcc('webmaster@amfar.com.br');
					$mail->setSubject("Inscrição Online");		
					// para o usuario envia tabela de distribuidores. esta mesma copia enviar para ridgid e para os supervisores
					$text = strip_tags($eBody);
					$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
					$mail->send(array($mailto));  // envia copia para o usuario			
					$this->view->message = "Sua inscriçao foi enviada com sucesso\nAguarde mais informações em seu email.\n\nObrigado.\n\n";
					}
				catch(Exception $ex){
					$this->view->message = $ex->getMessage();
					}	
				}	
			}
		}


	function contratoAction(){
		if(!$this->view->user->idCadastro)
			$this->_redirect('auth/login');
		else{
			//$sql = "SELECT titulo, inicio, final, valor, parcelas, cargaHoraria FROM cursos WHERE idCurso={$this->view->id}"; //die($sql);
			$sql = "SELECT cs.titulo, cs.inicio, cs.final, ac.idCurso, ac.idAluno, ac.valorCurso AS valor, ac.parcelas, ac.valorParcelas, cs.cargaHoraria FROM
				(cursos AS cs INNER JOIN alunosCursos AS ac ON ac.idCurso=cs.idCurso)
					WHERE (ac.idAlunoCursos={$this->view->id})"; //die($sql);
			$this->view->curso = $this->fc->dbReader($this->db, $sql);
			$sql = "SELECT vcto FROM financeiro WHERE
				(idCadastro={$this->view->curso->idAluno}) AND (idCurso={$this->view->curso->idCurso}) ORDER BY vcto";
			//die("$sql");
			$this->view->inicio = $this->fc->Ymd2dmY($this->fc->dbReader($this->db, $sql)->vcto);
			
			$sql = "SELECT * FROM cadastro WHERE idCadastro={$this->view->curso->idAluno}";
			$query = $this->fc->dbReader($this->db, $sql);
			
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
		}



	function contratosAction(){
		$sql="SELECT ac.idAlunoCursos, ac.idCurso, ac.data, cs.titulo, cs.inicio, ac.parcelas, ac.valorCurso AS valorTotal, ac.valorParcelas AS parcela FROM
			(alunosCursos AS ac INNER JOIN cursos AS cs ON ac.idCurso=cs.idCurso)
				WHERE (ac.idAluno = {$this->view->user->idCadastro}) AND(cs.parcelas > 2)
					ORDER BY ac.data DESC";
		$this->view->contratos = $this->fc->dbReader($this->db, $sql, true);	//echo $sql;	
		$this->view->where = "Cursos/Contratos de Prestação de Serviços";
		//$this->fc->debug($this->view->contratos);
		}

	}
