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

	}
