<div id="mainTop" style="background:url(images/mainMailing.png) no-repeat; height:24px; margin:4px 0"></div>
<div align="center"><?

if ($_GET['email']) {
	$query = mysql_query("select email, nome from mailing where email='{$_GET['email']}'");
	$found = mssql_num_rows($query);
	
	if ($found) {
		mssql_query("update mailing set status=1 where email='{$_GET['email']}'");
		$rsQuery=mssql_fetch_object($query);
		
		echo"
		<table border='0' style='margin-top:20px' cellpadding='0' width='100%'/>
			<tr>
				<td align='center'>
					<img src='images/mailing.png'><br/>

					<p>Prezado(a) <b>$rsQuery->nome</b></p> 
					<p>Obrigado por confirmar o seu cadastro.</p> 
					<p>Você será informado sobre novos cursos, palestras,<br/>					
					e outros eventos promovidos pela AMFAR.<br/><br/>
					AMF/Cadastros</p>
				</td>
			</tr>
		</table>";		
		}
	
	else
		$msgErr="<p><strong>Email não cadastrado!</strong></p>";
	}		

if (isset($_POST['smtMailing']))	{	

	$nome = trim(strtoupper($_POST['nome']));
	$email= trim(strtolower($_POST['email']));	
	$est= $_POST['est'];
	
	insertMailing($nome, $est, $email);
//	@mssql_query("insert into mailing (nome, email, uf, dataReg, status) values('$nome','$email', '$est', getdate(),0)");
	$remetente = "AMF - Associação Mineira de Farmacêuticos"; 
	$remetente_email = "$eContato"; 
	$data = date("Y-m-d H:i:s"); 
	$ip = $_SERVER['REMOTE_ADDR']; 

	$mensagem = "
		<html>
			<head>
				<title>Confirma&ccedil;&atilde;o Ativa de Cadastro</title>
				<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
			</head>
			<body>
				<p>Você solicitou o cadastro em nosso mailing através do IP {$ip} em {$data}</p>				
				Para confirmar o seu cadastro<br>
				<a href='$urlSite/index.php?lnk=86&email=$email'>Clique Aqui</a></p>
			</body>
		</html>"; 
	
	$headers = "MIME-Version: 1.0\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
	$headers .= "From: {$remetente} <{$remetente_email}>\n";
	$headers .= "Return-Path: <$remetente_email>\n"; 

	$para 	= ($_SERVER["REMOTE_ADDR"]=!'10.1.1.102') ? "{$_POST['nome']}<{$_POST['email']}>" : "{$_POST['nome']}<lauro@localhost>"; 
	$assunto = "Confirmação de Cadastro";

	/* Enviando a mensagem */ 
	if (!mail($para, $assunto, $mensagem, $headers)) 
	print 'Falha no envio da mensagem!'; 

	echo"
	<table border='0' style='margin-top:20px' cellpadding='0' width='100%'/>
		<tr>
			<td align='center'>
				<img src='images/mailing.png'><br/>
				
				<p>Prezado(a) <b>$nome</b></p> 
				<p>Seu cadastro em nosso mailing ainda n&atilde;o foi efetivado.<br> 
				Para confirm&aacute;-lo, acesse a sua caixa postal <b>$email</b>,<br> 
				e clique sobre o link para confirmar este cadastro.</p> 
				<p>Esta pol&iacute;tica &eacute; feita, para que ningu&eacute;m utilize o 
				seu email indevidamente.</p> 
				<p><b>:: AMF - Política Anti-Spam ::</p>
			</td>
		</tr>
	</table>";

	// envio de e-mail
	include('mail/htmlMimeMail.php');

	// formata o e-mail para ser enviado ao usuario como confirmacao
	$eBody="
	<table border=0 width='100%'/>
		<tr>
			<td rowspan=3 class=boxborder width=165><img src='$urlSite/images/mailing.png'>
			<td align=right class=boxborder>Nome:</td>
			<td colspan=3 class=boxborder><b>$nome</b></td></tr>
		<tr>
			<td align=right class=boxborder class=boxborder>UF:</td>
			<td colspan=3 class=boxborder><b>$est</td></tr>
		<tr>
			<td align=right class=boxborder>E-mail:</td>
			<td colspan=3 class=boxborder><b>$email</td>
		</tr>
	</table>";

// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
	$mailsend = sendMail($eContato, $nome, $email, 'Mailing', $eBody,'Mailing');
	}

elseif (!$found)	{
?>
	<body bgcolor="#FFFFFF" onload="document.mailing.nome.focus()"/>
	<br />
	<? if ($msgErr) echo"<p align=center colspan=2>$msgErr</p>"	?>
	
	<form name="mailing" id="mailing" method="post" action="" onsubmit="return checkForm(this.id)"/>
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellpadding="0" width="100%">
			<tr>
				<td align="center">
					<img src="images/mailing.png">
				<td align="center" bgcolor="#efefef">
					Preencha os dados abaixo para fazer parte do nosso mailing. 
					Você será informado sobre novos cursos, palestras, 
					e outros eventos promovidos pela AMFAR.<br/><br/><b>Obrigado!</b>
				</td>
			</tr>			
			<tr>
				<td align="right">Nome:&nbsp;</td>
				<td>
					<input type="text" name="nome" id="nome" size="48" maxlength="50" value="<?= $nome ?>"
						onchange="toUpper(this)" class="required"/>
				</td>
			</tr>
			<tr>
				<td align="right">E-Mail:&nbsp;</td>
				<td>
					<input type="text" name="email" id="email" size="48" maxlength="50" value="<?= $email ?>" class="required"/>
				</td>
			</tr>
			<tr>
				<td align="right">UF:</td>
				<td>
					<select name="est" id="uf" size="1" value="" class="required">
						<option value="">Selecione</option>
						<option value="AC">Acre</option>
						<option value="AL">Alagoas</option>
						<option value="AP">Amapá</option>
						<option value="AM">Amazonas</option>
						<option value="BA">Bahia</option>
						<option value="CE">Ceará</option>
						<option value="DF">Distrito Federal</option>
						<option value="ES">Espírito Santo</option>
						<option value="GO">Goiás</option>
						<option value="MA">Maranhão</option>
						<option value="MT">Mato Grosso</option>
						<option value="MS">Mato Grosso Sul</option>
						<option value="MG">Minas Gerais</option>
						<option value="PA">Pará</option>
						<option value="PB">Paraíba</option>
						<option value="PR">Paraná</option>
						<option value="PE">Pernambuco</option>
						<option value="PI">Piauí</option>
						<option value="RJ">Rio de Janeiro</option>
						<option value="RN">R. G. do Norte</option>
						<option value="RS">R. G. do Sul</option>
						<option value="RO">Rondonia</option>
						<option value="RR">Roraima</option>
						<option value="SC">S. Catarina</option>
						<option value="SP">São Paulo</option>
						<option value="SE">Sergipe</option>
						<option value="TO">Tocantins</option>
					</select>
				</td>
			</tr>
			<tr bgcolor="#eeeee4">
				<td align="center" colspan="2">
					<input type="submit" id="btSend" name="smtMailing" value="Enviar"/>
				</td>
			</tr>
		</table>
	</form><br/><?
	}	?>
</div>
