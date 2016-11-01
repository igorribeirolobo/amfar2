<div id="mainTop" style="background:url(images/mainContato.png) no-repeat; height:24px"></div>	
<div align=center>
<?
if ($_POST['btContato']) {

	include 'global.php';
	include 'mail/htmlMimeMail.php';

	$nome=trim($_POST['nome']);
	$email=trim($_POST['email']);
	$fone=trim($_POST['fone']);
	$assunto=trim($_POST['assunto']);
	$mensagem=trim($_POST['mensagem']);
	
	mssql_query("insert into contato(nome, fone, email, assunto, mensagem, dataReg) values('$nome', '$fone', '$email', '$assunto', '$mensagem', getdate())");
	
	insertMailing($nome, '',$email);
	
	//@mssql_query("INSERT INTO mailing(nome, email, dataReg, status) values('$nome', '$email', getdate(),0)");

	$eBody="		
		<table cellSpacing=2 cellPadding=2 width=100% border=0>
			<tr>
				<td align=right class=boxBorder>Nome:</td>
				<td class=boxBorder><b>$nome</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>E-mail:</td>
				<td class=boxBorder><b>$email</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>DDD/Fone:</td>
				<td class=boxBorder><b>$fone</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Assunto:</td>
				<td class=boxBorder><b>$assunto</b></td>
			</tr>
			<tr>
				<td align=right class=boxBorder>Mensagem:</td>
				<td class=boxBorder>$mensagem</td>
			</tr>
		</table>";
	// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
	$mailsend = sendMail($eContato, $nome, $email, 'AMF/Contato', $eBody,'Contato');
	
	echo"<br/>
		<table border=0 width=70% align=center style='margin: 20px 0; border:1px solid #c0c0c0'>
			<tr>
				<td align=center>
					<img border=0 src=$urlSite/images/contato.jpg>
					<p style='text-align:center'>
						Caro(a) <b>$nome</b></font>,<br><br>
						Seu E-mail foi enviado com sucesso.</b><br>

						<b>Obrigado por utilizar nossos servi&ccedil;os!</b><br><br>
						Se ficou satisfeito, indique-nos para seus amigos.
					</p>
				</td>
			</tr>
		</table>";
	}
	
else {
?>
	<link rel="stylesheet" href="js/formStyle.css" type="text/css"/>
	
	<p style="padding:0 4px" align="center">
		A <b class="blue">AMFAR</b> está a sua disposição para o esclarecimento de dúvidas sobre a instituição,<br /> 
		nossos serviços, cursos, congressos, convênios e outros.<br/><br/>
		Preencha o formulário abaixo ou se preferir ligue <b>31 3291-6242</b> ou escreva para:<br />
		<b>AMF - Associação Mineira de Farmacêuticos<br />
		Av. do Contorno, 9215 - Sl 502 - Ed. Humberto Martins Vieira<br/>
		Prado - 30110-130 - Belo Horizonte/MG</p>
		
	<form name="contato" id='contato' style="margin-top:0" action="#" method="POST" onsubmit="return checkForm(this.id)">
		<input type="hidden" name="process" value="0"/>
		<table cellSpacing="1" cellPadding="1" width="70%" border="0" align="center">
			<tr>
				<td class="label">Nome:</td>
				<td>
					<input type="text" size="40" id="nome" name="nome" maxLength="40" 
					class="required" onchange="toUpper(this)"></td>
			</tr>
			<tr>
				<td class="label">E-mail:</td>
				<td><input type="text" size="40" id="email" name="email" class="required" onchange="checkEmail(this)"></td>
			</tr>
			<tr>
				<td class="label">DDD/Fone:</td>
				<td><input type="text" id="fone" name="fone" size="40" class="required"></td>
			</tr>
			<tr>
				<td class="label">Assunto:</td>
				<td><input type="text" name="assunto" id="assunto" size="40" class="required"></td>
			</tr>
			<tr>
				<td class="label">Mensagem:</td>
				<td><textarea name="mensagem" id="mensagem" rows="5" cols="40" class="required"></textarea></td>
			</tr>
			<tr>
				<td colSpan="2" style="text-align:center; background:#eeeee4">
					<input type="submit" id="btSend" class="tButton" value="Enviar" name="btContato"></td>
			</tr>
		</table>
	</form>
<?	}	?>
</div>
