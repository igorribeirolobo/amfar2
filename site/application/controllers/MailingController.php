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
				
				if(!$name)throw new Exception("O campo 'Seu Nome' é obrigatório !\n\n");
				elseif(!$email)throw new Exception("O campo 'Seu E-mail' é obrigatório !\n\n");
				elseif(!$uf)throw new Exception("O campo 'Seu Estado' é obrigatório !\n\n");
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
							<caption>Mailing/Exclusão da Lista</caption>
							<tr>
								<td>
									Olá, <b>$name</b>, nesta data seu email <b>$email</b> foi excluído de nossa lista de mailing.<br />
									A partir de agora você não receberá mais nossos informativos.
								</td>
							</tr>";
						
						else
							$eBody .="	
							<tr>
								<td>
									Olá, <b>$name</b>, nesta data seu email <b?$email</b> foi incluído em nossa lista de mailing.<br />
									A partir de agora você estará recebendo nossos informativos.<br /><br />Obrigado.
								</td>
							</tr>";

						$eBody .="	
							<tr>
								<td class=tfooter>
									<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
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
				$mail->setSubject(($excluir) ? "Exclusão da lista de mailing" : 'Insclusão na lista de Mailing');		

				$text = strip_tags($eBody);
				$mail->setHtml($eBody, $text, "./public/images/"); // envia html completo com todos os distribuidores			
				$mail->send(array($mailto));  // envia copia para o usuario			
				$this->view->message = "Sua solicitação foi enviada com sucesso.\n\nObrigado.\n\n";
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
						$this->view->message = "Seu email $email foi incluído com sucesso\nem nossa lista de mailing.\n\nObrigado\n\n";
						}
					catch(Exception $ex){
						$query = $this->fc->dbReader($this->db2, "SELECT DATE_FORMAT(dataReg, '%d/%m/%Y') AS data, status FROM mailing where email='$email'");
						if($query->status < 0){
							$this->db2->query("UPDATE mailing SET status = 1 WHERE email='$email'");
							$this->view->message = "Seu email $email retornou para nossa lista de mailing\n\nObrigado\n\n";
							}
						else
							$this->view->message = "Seu email $email já faz parte de nossa lista\ndesde $query->data\n\n";
						}
					}
				else{
					$this->db2->query("UPDATE mailing SET status = -1 WHERE email='$email'");
					$this->view->message = "Seu email $email foi excluído de nossa lista\nA partir de agora você não receberá mais nossos informativos.\n\n";
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
					<caption>CURSO DE ESPECIALIZAÇÃO <i>LATO SENSU</i><br>PENDÊNCIA TRABALHO DE CONCLUSÃO DE CURSO/TCC</caption>
					<tr>
						<td>
							<p>Olá, <b>#name</b>,</p>
							<p>Os alunos reprovados ou que não apresentaram TCC, caso queiram, poderão se rematricular na disciplina Elaboração de TCC até o dia 07/03/2014.</p>
							<p>Orientação TCC opções:</p>
							<ol>
								<li>O aluno poderá indicar o orientador que deverá ser aprovado pela coordenação do curso e unidade acadêmica certificadora.</li>
								<li>A AMF indicará o orientador. Nesse, caso a linha de pesquisa será definida pelo orientador.</li>
							</ol>
							
							<p><b>Observação:</b> O aluno deverá escolher a sua opção no ato da rematricula.<br />
								A confirmação do nome / contato do orientador e regras gerais serão encaminhadas para o email do aluno até o dia 17/03/2014.
							</p>
							
							<p>Data da apresentação do TCC: A apresentação definitiva será agendada para o período de 28 de abril a 04 de maio de 2014. 
							Caso o aluno tenha condições a apresentação poderá ser antecipada.</p>
							
							<p>Valor da Disciplina Elaboração de TCC: <b>R$ 490,00</b></p>
							<p>Forma de pagamento: Boleto bancário gerado no ato da rematricula no site www.amfar.com.br/site/. 
							Após o pagamento do boleto, favor digitalizar e enviar para o email: <b>mariadasdores@amfar.com.br</b>.</p>
							<p><b>Nota importante:</b> a regularização na disciplina de Elaboração do TCC não inclui a resolução de pendências 
							em outras disciplinas, frequência e/ou situação financeira.</p>
							
							
							<p>Acesse <b>http://www.amfar.com.br/site/</b>, clique no ícone Alunos/Login.<br />
							Caso não lembre a senha cadastrada, digite seu CPF e o email para o qual gostaria que fosse enviado os dados para login.</p>
							
							<p>Após efetuar o login, clique no link <b>PENDÊNCIA DE TCC</b> na seção RESTRITO e siga as informações da tela.</p>
							
							<p><b>Maria das Dores Graciano Silva<br />
							Coordenação de Curso</p>						
						</td>
					</tr>	
					<tr>
						<td class=tfooter>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
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
			$mail->setFrom("AMF/Cursos de Especialização <webmaster@amfar.com.br>");
			$mail->setReplayTo(sprintf('%s <%s>', 'webmaster', 'webmaster@amfar.com.br'));
			$mail->setBcc('webmaster@amfar.com.br,');
			$mail->setSubject('PENDÊNCIA TRABALHO DE CONCLUSÃO DE CURSO/TCC');		
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
					<caption>SELEÇÃO DE MONITORES PARA O CURSO DE ATENÇÃO FARMACÊUTICA E FARMÁCIA CLÍNICA</caption>
					<tr>
						<td>
							<p>Olá, <b>#name</b>,</p>
							<p><b>SELEÇÃO DE MONITORES PARA O
								CURSO DE ATENÇÃO FARMACÊUTICA E FARMÁCIA CLÍNICA 2014 DA ASSOCIAÇÃO MINEIRA DE FARMACÊUTICOS – AMF</b></p>
								
							<p><b>PERÍODO DE INSCRIÇÃO:- </b>
								Durante o período de 10 de março a 20 de março de 2014, estarão abertas as inscrições para Monitoria do Curso 
								de Atenção Farmacêutica e Farmácia  Clínica da AMF.</p>
							
							<p><b>OBJETIVO:- </b>Facilitar o acesso a qualificação do profissional, tendo como contrapartida a colaboração 
								do monitor no suporte operacional necessário para manter e aprimorar a qualidade do curso oferecido.</p>
							
							<p><b>NÚMERO DE VAGAS:-</b> Serão oferecidas 2  vagas . O curso  está sujeito à confirmação de sua data de início, 
								podendo esta ser adiada, ou mesmo cancelada, em função de não ser atingido o número mínimo de inscritos para sua viabilização.</p>  
							
							<p><b>BENEFÍCIOS PARA O MONITOR:- </b>
								 Bolsa de estudo, com desconto de 25 a 50 %  do valor da mensalidade.</p>
							
							<p><b>REQUISITOS PARA A PARTICIPAÇÃO DO PROCESSO:- </b>
								Apenas poderão participar os alunos que estiverem inscritos nesse curso, com a matrícula paga e todos os documentos 
								enviados para AMF até  17 de março de 2014.</p>
								
							<p><b>INSCRIÇÃO:- </b>
								Acesse http://www.amfar.com.br/site/, clique no ícone Alunos/Login. Caso não lembre a senha cadastrada, digite seu CPF e o email para o qual gostaria que fosse enviado os dados para login.
								Após efetuar o login, clique no link SELEÇÃO MONITOR na seção RESTRITO e siga as informações da tela.</p>
								
								
							<p><b>SELEÇÃO:- </b>
								 A seleção do monitor será realizada por meio da análise de curriculum vitae  e entrevista presencial em data a ser divulgada.<br />
								Os candidatos selecionados para Monitoria da AMF serão informados por telefone / email  e assinarão o Termo de Concessão de Bolsa de Estudo parcial.</p>
							
							<p>O não comparecimento na entrevista implica, automaticamente, na desclassificação do candidato, que será substituído pelo candidato seguinte.</p>
							
							<p>O curriculum vitae  deverá ser enviado para amfar@amfar.com.br após inscrição no processo de seleção para monitores.</p>
							
							<p><b>Maria das Dores Graciano Silva<br />
							Coordenação de Curso</p>						
						</td>
					</tr>	
					<tr>
						<td class=tfooter>
							<a href='http://www.amfar.com.br'>:: AMF - Associação Mineira de Farmacêuticos ::</a></i>
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
			$mail->setFrom("AMF/Cursos de Especialização <webmaster@amfar.com.br>");
			$mail->setReplayTo(sprintf('%s <%s>', 'webmaster', 'webmaster@amfar.com.br'));
			$mail->setBcc('webmaster@amfar.com.br,');
			$mail->setSubject('SELEÇÃO DE MONITORES PARA O CURSO DE ATENÇÃO FARMACÊUTICA E FARMÁCIA CLÍNICA');		
			$text = strip_tags($html);
			$mail->setHtml($html, $text, "./public/images/"); // envia html completo com todos os distribuidores			
			$mail->send(array($mailto));  // envia copia para o usuario			
			$message .= "<br />E-mail enviado para $rs->name ($rs->email)";
			//die("enviado");		
		endforeach;
		die("Enviado para $message");
		}






	}
