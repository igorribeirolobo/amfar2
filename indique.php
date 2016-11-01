<div id="mainTop" style="background:url(images/mainIndique.png) no-repeat; height:24px; margin:4px 0"></div>
<div align="center"><?

$titulo="Indique este Site para seus amigos";


if ($_POST['btIndique']) {
	include("mail/htmlMimeMail.php");
	
//	debug($_POST);

	$nome = $_POST['nome'];
	$email= $_POST['email'];
	$uf 	= $_POST['uf'];

	$nameTo = $_POST['nameTo'];
	$mailTo = $_POST['mailTo'];
	$uf2	  = $_POST['ufTo'];
	
	insertMailing($nome, $est, $email);
	insertMailing($nameTo, $uf2, $mailTo);
	//@mssql_query("insert into mailing(nome, email, uf, dataReg) values ('$nome','$email', '$est', getdate())");
	//@mssql_query("insert into mailing(nome, email, uf, dataReg) values ('$nameTo','$mailTo', '$uf2', getdate())");

	$eBody="
	<table cellspacing=2 cellpadding=2 border=0 width=100% >
		<tr>
			<td class=boxborder bgcolor=#ffffff rowspan=5 align=center>
				<img border=0 src={$urlSite}/images/contato.jpg></td>

			<td align=right class=boxborder>De Nome:</td>
			<td class=boxborder><b>$nome</b></td></tr>
		<tr>
			<td align=right class=boxborder class=boxborder>Estado:</td>
			<td class=boxborder><b>$uf</td>
		</tr>
		<tr>
			<td align=right class=boxborder>E-mail:</td>
			<td class=boxborder><b>$email</td>
		</tr>
		<tr>
			<td align=right class=boxborder>Para Nome:</td>
			<td class=boxborder><b>$nameTo</b></td></tr>
		<tr>
			<td align=right class=boxborder class=boxborder>Estado:</td>
			<td class=boxborder><b>$uf2</td>
		</tr>
		<tr>
			<td align=right class=boxborder>E-mail:</td>
			<td class=boxborder><b>$mailTo</td>
		</tr>
	</table>";

	// function sendMail($mto, $mnf, $mmf, $ms, $mtb, $tit)
	$mailsend = sendMail($eContato, $nome, $email, 'AMF/Indicação', $eBody,'Indicação');

	$eBody="
	<table border=0 cellspacing=2 cellpadding=2 width=100% />
		<tr>
			<td class=boxborder align=center>
				<img border=0 src={$urlSite}/images/contato.jpg></td>
			<td class=boxborder style='padding:10px'>Olá <b>$nameTo,  </b><br><br>
				<b>$nome ($email [$email])</b> visitou o nosso Site e achou que você também gostaria de conhecer-nos !
				<p align=center>Aguardamos sua visita: <a href='$urlSite'>$urlSite</a></p>
			</td>
		</tr>
	</table>";	
	$mailsend = sendMail($mailTo, $nome, $email, 'AMF/Indicação', $eBody, 'Indicação');
	
	if ($mailsend =='E-mail enviado com sucesso') {
		$titulo="Caso queira indicar mais alguem esteja à vontade!
		<p style='text-align:center; margin-top:8px'>
			Caro(a) <b>$nome</b></font>, sua indicação foi enviada com sucesso para <b>$nameTo ($mailTo)</b>.</b><br>
			<b style='color:336699'>A AMFAR agradece o seu apoio. Obrigado !!!</b></p>";
		}
	}	?>


	<form action="#" style="width:360px" method="post" name="indique" id="indique" onsubmit="return checkForm(this.id)">
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td align=center colspan=2 class="tbCaption"><b style="color:#fff"><?= $titulo?></td></tr>
					
			<tr>
				<td class="label">Seu Nome:</td>
				<td><input type="text" size="50" id="nome" name="nome" value="<?= $nome ?>" maxlength="50" 
					onchange="toUpper(this)" class="required"/></td></tr>
					
			<tr>
				<td class="label">Seu E-mail:</td>
				<td><input type="text" size="50" name="email" id="email" value="<?= $email ?>" maxlength="50" 
					onchange="checkEmail(this)" class="required"></td></tr>
						
			<tr>
				<td class="label">Estado:</td>
				<td>
					<select name="uf" id="estado" size="1" value="" class="required">						
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
						<option value="MG" selected>Minas Gerais</option>
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
				</td></tr>
				
			<tr>
				<td align="center" colspan="2" nowrap>				
					<input type="checkbox" name="chbox" value="ON" checked>
						<b class="negrito">Autorizo o envio e E-mailing para o e-mail acima.</small>
				</td></tr>
				
			<tr><td colspan="2"><hr size=1"></td></tr>
				
			<tr>
				<td class="label">Amigo(a):</td>
				<td><input type="text" size="50" id="amigo(a)" maxlength="50" name="nameTo" value=""
					onchange="toUpper(this)" class="required"></td></tr>
					
			<tr>
				<td class="label">E-mail:</td>
				<td><input type="text" size="50" maxlength="50" id="email" name="mailTo" value=""
					onchange="checkEmail(this)" class="required"></td></tr>
			<tr>
				<td class="label">Estado:</td>
				<td>
					<select name="ufTo" id="estado" size="1" value="" class="required">						
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
						<option value="MG" selected>Minas Gerais</option>
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
				</td></tr>				

			<tr>
				<td align="center" colspan=2 height=30>
					<br><div id="dvBtSend">
					<input type="submit" id="btSend" name="btIndique" value="Enviar"/>
				</td>
			</tr>
		</table>
	</form>
</div>
