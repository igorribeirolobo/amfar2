<?php
class MailingController extends Zend_Controller_Action{
	function init(){
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->links = sprintf("%s/cursos", $this->view->baseUrl);
		$this->view->docs = sprintf("%s/public/docs", $this->view->baseUrl);
		$this->view->baseImg = sprintf("%s/public/images", $this->view->baseUrl);
			
		Zend_Loader::loadClass('Zend_Filter_StripTags');
		$this->filter = new Zend_Filter_StripTags();	
		$this->fc = new FuncoesUteis();		
		$this->view->fc = $this->fc;
		$this->db = Zend_Registry::get('db');
		$this->db2 = Zend_Registry::get('db2');
		Zend_Db_Table::setDefaultAdapter($this->db2);
		
	
		}


	function indexAction(){
		$this->view->message = '';
		$this->view->estados = $this->fc->dbReader($this->db, "SELECT * FROM estados ORDER BY uf", true);
		if ($this->_request->isPost()){
			//$this->fc->debug($_POST);
			try{
				$name = trim($this->filter->filter(strtoupper($_POST['name'])));
				$email =	trim($this->filter->filter(strtolower($_POST['email'])));
				$uf = trim($this->filter->filter(strtoupper($_POST['uf'])));
				$excluir = (int)($_POST['excluir']);
				
				if(!$name)throw new Exception("O campo 'Seu Nome' � obrigat�rio !\n\n");
				elseif(!$email)throw new Exception("O campo 'Seu E-mail' � obrigat�rio !\n\n");
				elseif(!$uf)throw new Exception("O campo 'Seu Estado' � obrigat�rio !\n\n");
				$eBody="
				<html>
					<head>
						<style>
							#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
							#econtato caption{font-size:16px;background:#D9E7F8;font-weight:bolder}
							#econtato td{padding:4px;border:solid 1px silver;color:#444}
							#econtato .label{text-align:right;color:#444;background:#D9E7F8}
							#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
							#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
							#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
						</style>
					</head>
					<body>
						<img src='topoEmail.jpg' border='0'/>
						<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>";
				
						if($excluir)
							$eBody .="					
							<caption>Mailing/Exclus�o da Lista</caption>
							<tr>
								<td>
									Ol�, <b>$name</b>, nesta data seu email <b>$email</b> foi exclu�do de nossa lista de mailing.<br />
									A partir de agora voc� n�o receber� mais nossos informativos.
								</td>
							</tr>";
						
						else
							$eBody .="	
							<tr>
								<td>
									Ol�, <b>$name</b>, nesta data seu email <b?$email</b> foi inclu�do em nossa lista de mailing.<br />
									A partir de agora voc� estar� recebendo nossos informativos.<br /><br />Obrigado.
								</td>
							</tr>";

						$eBody .="	
							<tr>
								<td class=tfooter>
									<a href='http://www.amfar.com.br'>:: AMF - Associa��o Mineira de Farmac�uticos ::</a></i>
								</td>
							</tr>
						</table>";
				
		
				//Zend_Loader::loadClass('htmlMimeMail');

				//echo "_imagePath=$_imagePath";				
				$mailto = "$name <$email>";
				$mail = new htmlMimeMail();
				
				$mail->setReturnPath('webmaster@amfar.com.br');
				$mail->setFrom("AMF/Mailing <webmaster@sbrafh.org.br>");
				$mail->setReplayTo(sprintf('%s <%s>', 'webmaster', 'webmaster@amfar.com.br'));
				$mail->setBcc('webmaster@amfar.com.br');
				$mail->setSubject(($excluir) ? "Exclus�o da lista de mailing" : 'Insclus�o na lista de Mailing');		

				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message = "Sua solicita��o foi enviada com sucesso.\n\nObrigado.\n\n";
				$_POST['nameto'] = null;
				$_POST['mailto'] = null;
				
				if(!$excluir){
					Zend_Loader::loadClass('Mailing');
					$mailing = new Mailing();					
					$_dados = array(
						'categ' => 0,
						'nome' => $name,
						'email' => $email,
						'uf' => $uf,
						'status' => 0,
						'dataReg' => date('Y-m-d H:i:s'),
						);
					try{
						//echo "dados 1";
						//$this->fc->debug($_dados);
						$mailing->insert($_dados);
						$this->view->message = "Seu email $email foi inclu�do com sucesso\nem nossa lista de mailing.\n\nObrigado\n\n";
						}
					catch(Exception $ex){
						$query = $this->fc->dbReader($this->db2, "SELECT DATE_FORMAT(dataReg, '%d/%m/%Y') AS data, status FROM mailing where email='$email'");
						if($query->status < 0){
							$this->db2->query("UPDATE mailing SET status = 1 WHERE email='$email'");
							$this->view->message = "Seu email $email retornou para nossa lista de mailing\n\nObrigado\n\n";
							}
						else
							$this->view->message = "Seu email $email j� faz parte de nossa lista\ndesde $query->data\n\n";
						}
					}
				else{
					$this->db2->query("UPDATE mailing SET status = -1 WHERE email='$email'");
					$this->view->message = "Seu email $email foi exclu�do de nossa lista\nA partir de agora voc� n�o receber� mais nossos informativos.\n\n";
					}
				}
			catch(Exception $ex){
				$this->view->message = $ex->getMessage();
				}
			}			

		$this->getHelper('layout')->disableLayout();		
		//$this->render('index');
		}





	function tccmailingAction(){
		$eBody="
		<html>
			<head>
				<style>
					#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
					#econtato caption{font-size:16px;font-weight:bolder;color:#14b7cd;text-align:center}
					#econtato td{padding:4px;border:solid 1px silver;color:#444}
					#econtato p{text-align:justify}
					#econtato .label{text-align:right;color:#444;background:#D9E7F8}
					#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
					#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
					#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
				</style>
			</head>
			<body>
				<img src='topoEmail.jpg' border='0'/>
				<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>				
					<caption>CURSO DE ESPECIALIZA��O <i>LATO SENSU</i><br>PEND�NCIA TRABALHO DE CONCLUS�O DE CURSO/TCC</caption>
					<tr>
						<td>
							<p>Ol�, <b>#name</b>,</p>
							<p>Os alunos reprovados ou que n�o apresentaram TCC, caso queiram, poder�o se rematricular na disciplina Elabora��o de TCC at� o dia 07/03/2014.</p>
							<p>Orienta��o TCC op��es:</p>
							<ol>
								<li>O aluno poder� indicar o orientador que dever� ser aprovado pela coordena��o do curso e unidade acad�mica certificadora.</li>
								<li>A AMF indicar� o orientador. Nesse, caso a linha de pesquisa ser� definida pelo orientador.</li>
							</ol>
							
							<p><b>Observa��o:</b> O aluno dever� escolher a sua op��o no ato da rematricula.<br />
								A confirma��o do nome / contato do orientador e regras gerais ser�o encaminhadas para o email do aluno at� o dia 17/03/2014.
							</p>
							
							<p>Data da apresenta��o do TCC: A apresenta��o definitiva ser� agendada para o per�odo de 28 de abril a 04 de maio de 2014. 
							Caso o aluno tenha condi��es a apresenta��o poder� ser antecipada.</p>
							
							<p>Valor da Disciplina Elabora��o de TCC: <b>R$ 490,00</b></p>
							<p>Forma de pagamento: Boleto banc�rio gerado no ato da rematricula no site www.amfar.com.br/site/. 
							Ap�s o pagamento do boleto, favor digitalizar e enviar para o email: <b>mariadasdores@amfar.com.br</b>.</p>
							<p><b>Nota importante:</b> a regulariza��o na disciplina de Elabora��o do TCC n�o inclui a resolu��o de pend�ncias 
							em outras disciplinas, frequ�ncia e/ou situa��o financeira.</p>
							
							
							<p>Acesse <b>http://www.amfar.com.br/site/</b>, clique no �cone Alunos/Login.<br />
							Caso n�o lembre a senha cadastrada, digite seu CPF e o email para o qual gostaria que fosse enviado os dados para login.</p>
							
							<p>Ap�s efetuar o login, clique no link <b>PEND�NCIA DE TCC</b> na se��o RESTRITO e siga as informa��es da tela.</p>
							
							<p><b>Maria das Dores Graciano Silva<br />
							Coordena��o de Curso</p>						
						</td>
					</tr>	
					<tr>
						<td class=tfooter>
							<a href='http://www.amfar.com.br'>:: AMF - Associa��o Mineira de Farmac�uticos ::</a></i>
						</td>
					</tr>
				</table>
			</body>
		</html>";

		$query = $this->fc->dbReader($this->db, "SELECT nome, email FROM pendenciaTCC_2014 ORDER BY nome", true);
		foreach($query as $rs):
			//echo "_imagePath=$_imagePath";				
			$mailto = "$rs->name <$rs->email>";
			//$mailto = "Webmaster <webmaster@amfar.com.br>";
			$html = str_replace('#name', $rs->nome, $eBody);
			
			$mail = new htmlMimeMail();			
			$mail->setReturnPath('webmaster@amfar.com.br');
			$mail->setFrom("AMF/Cursos de Especializa��o <webmaster@amfar.com.br>");
			$mail->setReplayTo(sprintf('%s <%s>', 'webmaster', 'webmaster@amfar.com.br'));
			$mail->setBcc('webmaster@amfar.com.br,');
			$mail->setSubject('PEND�NCIA TRABALHO DE CONCLUS�O DE CURSO/TCC');		
			$text = strip_tags($html);
			$mail->setHtml($html, $text, "./public/images/"); // envia html completo com todos os distribuidores			
			$mail->send(array($mailto));  // envia copia para o usuario			
			$message .= "<br />E-mail enviado para $rs->name ($rs->email)";
			//die("enviado");		
		endforeach;
		die("Enviado para $message");
		}



	function monitorAction(){
		$eBody="
		<html>
			<head>
				<style>
					#econtato{width:600px;font:12px Arial, Helvetica, Sans-Serif, Tahoma, Verdana;margin-top:10px}
					#econtato caption{font-size:16px;font-weight:bolder;color:#14b7cd;text-align:center}
					#econtato td{padding:4px;border:solid 1px silver;color:#444}
					#econtato p{text-align:justify}
					#econtato .label{text-align:right;color:#444;background:#D9E7F8}
					#econtato .tfooter{background:#D9E7F8; text-align:center;font-size:12px}\r\n
					#econtato .tfooter a{color:#444;text-align:center;text-decoration:none}\r\n
					#econtato .tfooter a:hover{color:#f00;text-decoration:underline}\r\n
				</style>
			</head>
			<body>
				<img src='topoEmail.jpg' border='0'/>
				<table id='econtato' cellSpacing='2' cellPadding='2' border='0'>				
					<caption>SELE��O DE MONITORES PARA O CURSO DE ATEN��O FARMAC�UTICA E FARM�CIA CL�NICA</caption>
					<tr>
						<td>
							<p>Ol�, <b>#name</b>,</p>
							<p><b>SELE��O DE MONITORES PARA O
								CURSO DE ATEN��O FARMAC�UTICA E FARM�CIA CL�NICA 2014 DA ASSOCIA��O MINEIRA DE FARMAC�UTICOS � AMF</b></p>
								
							<p><b>PER�ODO DE INSCRI��O:- </b>
								Durante o per�odo de 10 de mar�o a 20 de mar�o de 2014, estar�o abertas as inscri��es para Monitoria do Curso 
								de Aten��o Farmac�utica e Farm�cia  Cl�nica da AMF.</p>
							
							<p><b>OBJETIVO:- </b>Facilitar o acesso a qualifica��o do profissional, tendo como contrapartida a colabora��o 
								do monitor no suporte operacional necess�rio para manter e aprimorar a qualidade do curso oferecido.</p>
							
							<p><b>N�MERO DE VAGAS:-</b> Ser�o oferecidas 2  vagas . O curso  est� sujeito � confirma��o de sua data de in�cio, 
								podendo esta ser adiada, ou mesmo cancelada, em fun��o de n�o ser atingido o n�mero m�nimo de inscritos para sua viabiliza��o.</p>  
							
							<p><b>BENEF�CIOS PARA O MONITOR:- </b>
								 Bolsa de estudo, com desconto de 25 a 50 %  do valor da mensalidade.</p>
							
							<p><b>REQUISITOS PARA A PARTICIPA��O DO PROCESSO:- </b>
								Apenas poder�o participar os alunos que estiverem inscritos nesse curso, com a matr�cula paga e todos os documentos 
								enviados para AMF at�  17 de mar�o de 2014.</p>
								
							<p><b>INSCRI��O:- </b>
								Acesse http://www.amfar.com.br/site/, clique no �cone Alunos/Login. Caso n�o lembre a senha cadastrada, digite seu CPF e o email para o qual gostaria que fosse enviado os dados para login.
								Ap�s efetuar o login, clique no link SELE��O MONITOR na se��o RESTRITO e siga as informa��es da tela.</p>
								
								
							<p><b>SELE��O:- </b>
								 A sele��o do monitor ser� realizada por meio da an�lise de curriculum vitae  e entrevista presencial em data a ser divulgada.<br />
								Os candidatos selecionados para Monitoria da AMF ser�o informados por telefone / email  e assinar�o o Termo de Concess�o de Bolsa de Estudo parcial.</p>
							
							<p>O n�o comparecimento na entrevista implica, automaticamente, na desclassifica��o do candidato, que ser� substitu�do pelo candidato seguinte.</p>
							
							<p>O curriculum vitae  dever� ser enviado para amfar@amfar.com.br ap�s inscri��o no processo de sele��o para monitores.</p>
							
							<p><b>Maria das Dores Graciano Silva<br />
							Coordena��o de Curso</p>						
						</td>
					</tr>	
					<tr>
						<td class=tfooter>
							<a href='http://www.amfar.com.br'>:: AMF - Associa��o Mineira de Farmac�uticos ::</a></i>
						</td>
					</tr>
				</table>
			</body>
		</html>";

		$query = $this->fc->dbReader($this->db, "SELECT  cd.nome, cd.email
			FROM (cadastro AS cd INNER JOIN
				alunosCursos AS ac ON ac.idAluno = cd.idCadastro)
				WHERE (ac.idCurso = 157)
					ORDER BY cd.nome", true);
		foreach($query as $rs):
			//echo "_imagePath=$_imagePath";				
			$mailto = "$rs->name <$rs->email>";
			//$mailto = "Webmaster <webmaster@amfar.com.br>";
			$html = str_replace('#name', $rs->nome, $eBody);
			
			$mail = new htmlMimeMail();			
			$mail->setReturnPath('webmaster@amfar.com.br');
			$mail->setFrom("AMF/Cursos de Especializa��o <webmaster@amfar.com.br>");
			$mail->setReplayTo(sprintf('%s <%s>', 'webmaster', 'webmaster@amfar.com.br'));
			$mail->setBcc('webmaster@amfar.com.br,');
			$mail->setSubject('SELE��O DE MONITORES PARA O CURSO DE ATEN��O FARMAC�UTICA E FARM�CIA CL�NICA');		
			$text = strip_tags($html);
			$mail->setHtml($html, $text, "./public/images/"); // envia html completo com todos os distribuidores			
			$mail->send(array($mailto));  // envia copia para o usuario			
			$message .= "<br />E-mail enviado para $rs->name ($rs->email)";
			//die("enviado");		
		endforeach;
		die("Enviado para $message");
		}






	}
