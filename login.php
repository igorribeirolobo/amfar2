<div id="mainTop" style="background:url(images/mainLogin.png) no-repeat; height:24px"></div><br />
<div align=center>
<?
include ("global.php");

$tipo=isset($_GET['typeCad'])?$_GET['typeCad']:0;
//debug($_POST);
if ($_POST['btNewCad']) {
	$rsCadastro->idCadastro=$_POST['idCadastro'];	
	$rsCadastro->tipo=$_POST['tipo'];
	$rsCadastro->cpf=ereg_replace("[' '-./ \t]",'',$_POST['cpf']);
	$rsCadastro->email=trim($_POST['email']);
	$rsCadastro->senha=trim($_POST['senha']);	
	$rsCadastro->nome=str_replace("'",'"',trim($_POST['nome']));
	$rsCadastro->rg=str_replace("'",'"',trim($_POST['rg']));
	$rsCadastro->ender=str_replace("'",'"',trim($_POST['ender']));
	$rsCadastro->num=str_replace("'",'"',trim($_POST['num']));
	$rsCadastro->compl=str_replace("'",'"',trim($_POST['compl']));
	$rsCadastro->bairro=str_replace("'",'"',trim($_POST['bairro']));
	$rsCadastro->cidade=str_replace("'",'"',trim($_POST['cidade']));
	$rsCadastro->uf=str_replace("'",'"',trim($_POST['uf']));
	$rsCadastro->cep=str_replace("'",'"',trim($_POST['cep']));
	$rsCadastro->fone=str_replace("'",'"',trim($_POST['fone']));
	$rsCadastro->fone2=str_replace("'",'"',trim($_POST['fone2']));
	$rsCadastro->profissao=str_replace("'",'"',trim($_POST['profissao']));
	
	if (!$rsCadastro->idCadastro) {
		$query=mssql_query("select idCadastro from cadastro where cpf='$rsCadastro->cpf'");
		if (mssql_num_rows($query) > 0) {			
			$newErr="Este CPF/CNPJ já existe cadastrado em nossa base de dados<br>
				Caso tenha esquecido sua senha, use o link <b>Esqueci minha senha</b>";
			}
		else {
			$sql="insert into cadastro(cpf, status, dataReg, ultAcesso) 
				values('$cpf', 0, getdate(), getdate())
				select @@Identity as LastID";
			$query=mssql_query($sql);			
			$rsCadastro->idCadastro=mssql_result($query, 0, 'LastID');
			insertMailing($rsCadastro->nome, $rsCadastro->uf, $rsCadastro->email);
			//@mssql_query("insert into mailing(nome, email, dataReg, status) values('$rsCadastro->nome', '$rsCadastro->email', getdate(), 0)");			
			}
		}

	//echo "id=$rsCadastro->idCadastro";
	if($rsCadastro->idCadastro) {	// editando
		mssql_query("update cadastro set
			idGrupo=1,
			tipo=$rsCadastro->tipo,
			nome=upper('$rsCadastro->nome'),
			ender=upper('$rsCadastro->ender'),
			num=upper('$rsCadastro->num'),
			compl=upper('$rsCadastro->compl'),
			bairro=upper('$rsCadastro->bairro'),
			cep='$rsCadastro->cep',
			cidade=upper('$rsCadastro->cidade'),
			uf=upper('$rsCadastro->uf'),
			fone=upper('$rsCadastro->fone'),
			fone2=upper('$rsCadastro->fone2'),
			email=lower('$rsCadastro->email'),
			rg='$rsCadastro->rg',
			profissao=upper('$rsCadastro->profissao'),
			senha=lower('$rsCadastro->senha')
			where idCadastro=$rsCadastro->idCadastro");
			
		insertMailing($rsCadastro->nome, $rsCadastro->uf, $rsCadastro->email);
		//@mssql_query("insert into mailing(nome, email, dataReg, status) values('$rsCadastro->nome', '$rsCadastro->email', getdate(),0)");	
	
		/*
		$query=mssql_query("select * from associados where idCadastro=$rsCadastro->idCadastro");
		if (!mssql_num_rows($query)) {
			$validade = date("m.d.Y", time() + (365 * 86400));
			$sql="insert into associados(idCadastro, status, dataReg, validade) 
				values($rsCadastro->idCadastro, 0, getdate(), '$validade')";
			@mssql_query($sql);
		
//			$conta=11998;	// conta anuidade
			$offSet=4;
			$vcto = date("m.d.Y", time() + ($offSet * 86400));
			// numdoc=id do associado + ano da validade
			$numdoc=substr("000000$rsCadastro->idCadastro",-5) . substr($validade,-4);			
			$sql="insert into financeiro(idCadastro, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, formaPgto, boleto, status, dataReg) values
				($rsCadastro->idCadastro, getdate(), '$numdoc',2, $contaSocios, 'Taxa de Associado', $taxaAssociado, '$vcto',$taxaAssociado,3, '$numdoc', 2, getdate())
				select @@Identity as LastID";		
			$query=@mssql_query($sql);			
			$boleto_id=mssql_result($query, 0, 'LastID');
			
			if (!$boleto_id) $newErr="85 Erro de insert na tabela Financeiro/Associados!";			
			}
		*/	
		}	// editando
	}	//	$_POST['btNewCad']
	
elseif ($_POST['btLogin']) {
	
	$email= strtolower($_POST['email']);
	$senha = trim(strtolower($_POST['senha']));
	$query = mssql_query("select idCadastro from cadastro where email='$email' and senha='$senha'");
	$found=mssql_num_rows($query);
	$idCadastro = ($found > 0) ? mssql_result($query, 0, 'idCadastro') : false;	
		
	if (!$idCadastro )	
		$msgErr= "Login ou Senha Inválidos - Verifique e tente novamente!";
	else
		$query=mssql_query("update cadastro set ultAcesso=getdate() where idCadastro=$idCadastro");
	}	//************* if ($_POST['btLogin'])


elseif ($_POST['btForgot'])	{

//	if ($_POST['idForm'] != $_SESSION['idForm'])
//		die("Acesso não autorizado. Seu IP ". $_SERVER["REMOTE_ADDR"] . " foi capturado e será enviado aos orgãos competentes por tentativa de ataque a este site!");

	
	// veio pelo post	re-envio de senha
	$cpf = ereg_replace("[' '-./ \t]",'',$_POST['cpf']);
	$email = $_POST['email2'];
	$sql = "select email, nome, senha from cadastro where cpf='$cpf'";

	$query = mssql_query($sql);
	$rsCadastro = mssql_fetch_object($query);

	if ($rsCadastro)	{
		// usuario localizado cria o corpo da mensagem para envio
		
		include('mail/htmlMimeMail.php');
		$body = "
			<table border=0 cellspacing=2 cellpadding=2 width=100% />
				<tr>
					<td class=tright>De:</td><td class=tleft>AMFAR - Secretaria</td></tr>
				<tr>
					<td colspan=2 class=tleft>Olá <b>$rsCadastro->nome</b>, 
						você solicitou que fosse enviada os dados para acesso ao nosso site.<br><br>
						E-mail para acesso:&nbsp;&nbsp;<font color=006666><b>$rsCadastro->email</b></font>.<br><br>
						Senha para acesso é:&nbsp;&nbsp;<font color=006666><b>$rsCadastro->senha</b></font>.<br><br>
						Caso queira trocá-la, entre no link Login e após efetuar o login, selecione Alterar Cadastro.
						<br><br>Um abraço e seja muito bem vindo ao nosso Site!
					</td>
				</tr>
			</table>";

		// function sendMail(email_destino, remetente, email_remetente, assunto, corpo,titulo)
		$mailsend = sendMail($email,'AMF|Cadastro',$webmaster,'AMF|Envio de Senha', $body, "ENVIO DE SENHA");
		?>
			
		<table border="0" cellspacing="0" cellpadding="0" width="100%">

		<?	if ($mailsend == 'E-mail enviado com sucesso')	{	?>
			<tr>
				<td align="center" height="30">Caro <b><font size=2><?= $rsCadastro->nome ?></font></b>,</td></tr>
					<tr>
						<td align="center" height="30">					
							Sua senha foi enviada para o e-mail <b><?=$email?></b><br><br>
							Abra o seu programa de correio e veja a sua senha.</font></td>
					</tr>
					<tr>
						<td align="center" height="30">&nbsp;Atenciosamente,</td>
					</tr><?
				}
						
			else	{	?>
					<tr>
						<td align="center" height="30"><font color=red>Erro: E-mail não pode ser enviado!</font><br>
							Entre em contato com a secretaria do AMFAR: 11 3256-5972.</td>
					</tr><?
				}	?>
				
			<tr>
				<td align="center" height="30"><b class="Intertitulo1"><font size=2>:: AMF|Cadastro ::</font></b></td></tr>
			</tr>
		</table><?
		}
		
	else	{	
		$msgErr="Seu CPF não consta em nossa base de dados!";
		}	// CPF não localizado
		
	}	//	if ($_POST['btForgot'])


if (!$idCadastro && $_GET['flag']==1) {	?>

	<form name="login" id="login" method="post" action="#" "onload="document.login.email.focus();" onsubmit="return checkForm(this.id);">
		<input type="hidden" name="process" value="0"/>

		<table border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td align=center colspan=2 bgcolor="#efefef">
					<p align="center">
						<b>Alguns Links são exclusivos para Associados,<br />
						Alunos, Patrocinadores ou Parceiros.<br>
						Se você faz parte de um desses grupos,<br />
						digite abaixo seu E-mail e Senha para se logar!</p>
			<tr>
				<td class="label">E-mail:</td>
				<td><input type="text" name="email" id="email" style="width:250px" maxlength="100" value="<?= $email ?>" class="required"></td></tr>

			<tr>
				<td class="label">Senha:</td>
				<td><input type="password" name="senha" id="senha" style="width:120px"  maxlength="10" value="" class="required">

		<? if (!$found) echo "<tr><td colspan=2 class=msgErr>$msgErr</td></tr>";	?>

			<tr>
				<td colspan=2 align=center>				
					<input type="submit" name="btLogin" id="btSend" value="Acessar"></td></tr>
		</table>
	</form>




	<form name="forgot" id="forgot" method="post" action="#" onsubmit="return checkForm(this.id);">				
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellspacing="1" cellpadding="1">
			<caption><br /><b>Recuperar minha Senha</b></caption>
			<tr>
				<td align=center colspan=2 bgcolor="#efefef">
					<p align="center">
						<b>Caso tenha esquecido sua senha, use este formulário 
						para que a mesma seja enviada. Informe seu email atual para o caso do mesmo ser diferente do email constante 
						no seu cadastro.</p>
			</tr>
			<tr>
				<td class="label">Email:</td>
				<td><input type="text" name="email2" id="email2" style="width:250px" maxlength="80" value="" class="required">
			</tr>
			<tr>
				<td class="label" nowrap>CPF / CNPJ:</td>
				<td><input type="text" name="cpf" id="cpf" maxlength="20" value="" class="required">
			</tr>
			<tr>
				<td align=center colspan=2>
					<input type="submit" name="btForgot" id="btSend" value="Recuperar Senha"></td>
			</tr>
		</table>
	</form>
	<br /><?
	$style='display:none';
	}
	
//echo "uid=$uid rsCadastro->uid=$rsCadastro->idCadastro";


if ($rsCadastro->idCadastro > 0) $idCadastro=$rsCadastro->idCadastro;

if ($idCadastro) {	
	$query = mssql_query("select *, convert(char(10), dataReg, $dataFilter) as validade from cadastro where idCadastro=$idCadastro");
	$rsCadastro = mssql_fetch_object($query);
	
//	debug($rsCadastro);
	$tipo=$rsCadastro->tipo;
//	$status = $rsCadastro->status;
	$hoje=date('Y.m.d');
/*
	$query2=mssql_query("select idCadastro, numDoc, historico, valor, pgto, 
		CONVERT(char(10), vcto, 103) AS fVcto
		from financeiro
		where (idCadastro=$idCadastro)
		and (CONVERT(char(10), vcto, 102)<=getdate()+10)
		and (CONVERT(char(10), pgto, 103)='01/01/1900')");
		
	if ($status >=0) {
		session_start();	// inicializa as variaveis de sessoes			
		$_SESSION['userId'] = $idCadastro;
		}
			
		
	//	debug($_SESSION);
	echo"				
	<table border='0' cellpadding='0' width='100%'/>
		<tr>
			<td>
				<p align=center>Prezado(a) <b>$rsCadastro->nome</b></td>";
							

	if (mssql_num_rows($query2)) {
		$total=0;
		echo"<tr>
			<td align=center>
				<b style='color:#c40000'>ATENÇÃO: Caro usuário $rsCadastro->nome,<br /> 
				exite(m) pendência(s) de pagamento em seu nome:!<br />
				Clique no Nº Dcto para impressão do boleto<br /><br />
			<table cellpadding=1 cellspacing=1 border=1>
				<tr align=center>
					<td>Nº Docto<td>Histórico<td>Vcto.<td>Valor R$";
		while ($financeiro=mssql_fetch_object($query2)) {
			$total = $total + $financeiro->valor;
			echo "<tr>
				<td style='padding:0 8px'>
					<a href='boletos/boleto.php?id=$financeiro->id' target='_boleto'/><b>$financeiro->numDoc</b></a>
				<td style='padding:0 8px'>$financeiro->historico
				<td style='padding:0 8px'>$financeiro->fVcto
				<td style='padding:0 8px; text-align:right'>" . formatVal($financeiro->valor);				
			}
	
		echo"<tr><td colspan=3 align=right>Total<td align=right style='padding: 0 8px'><b>" . formatVal($total) . "</b>
			</table><br /><br />
				Entre em contato com nosso atendimento 31 3291-6242<br /> 
				caso haja alguma irregularidade nas informações acima.
			</td>
		</tr>
		</table>";
		}
/*
	if ($status ==-1) echo"
		<tr>
			<td align=center>
				<b style='color:#c40000'>ATENÇÃO: Caro usuário $rsCadastro->nome, 
				seu acesso encontra-se bloqueado!<br />
				Entre em contato com nosso atendimento 31 3291-6242 
				para regularizar sua situação cadastral.
			</td>
		</tr>
		</table>";

	elseif ($status==0) echo"
		<tr>
			<td align=center>
				<img src='css/h_arrow.gif' align='absMiddle'>&nbsp;
				<b style='color:#c40000'>Sua inscrição encontra-se inativa<br>
				por não acusarmos o pagamento da taxa de anuidade !<br><br>
				Para gerar novo boleto da Contribuição Anual $linkBoleto
			</td>
		</tr>
		</table>";

	elseif (date('Y-m-d') > $rsCadastro->validoAte) echo"
		<tr>
			<td align=center><br />
				<b>Sua inscrição finalizou em $rsCadastro->fDate !<br><br>
				Para gerar novo boleto da Contribuição Anual<br>
				<img src='css/h_arrow.gif' align='absMiddle'>&nbsp;$linkBoleto
			</td>
		</tr>
		</table>";
										
	elseif (date('Y-m-d') == $rsCadastro->validoAte) echo"
		<tr>
			<td align=center><br />
				<b>Sua inscrição finaliza hoje !<br><br>
				Para gerar novo boleto da Contribuição Anual<br>
				<img src='css/h_arrow.gif' align='absMiddle'>&nbsp;$linkBoleto
			</td>
		</tr>
		</table>";

	else echo"
		<tr>
			<td align=center><br />
				<b>Sua inscrição finaliza em $rsCadastro->fDate !<br><br>
				Caso necessite gerar novo boleto para sua inscrição $linkBoleto
			</td>
		</tr>
		</table>";
*/				
	echo"			
		<p align=center><br />
			<img src='css/h_arrow.gif' align='absMiddle'>&nbsp; 
			Altere seu cadastro se necessário!<br></p>";	
	}	// else do if (user)




/* 
	tipo: 0 cadastro pessoa física (alunos)
	tipo: 1 cadastro pessoa juríca (empresas)
	cadastro completo */
?>
				<div id="inscricaoOnLine"><a name='inscricao'><br />
					<p align=center><span style="color:#336699">.</span><br /><b style='color:#336699'><a href="javascript:void(null)" onclick="opener.location.href ='index.php?lnk=80&flag=2'">SEJA UM ASSOCIADO.</a></b>
					<form name="login" id="login" style="margin-top:0" action="#cad" method="POST" onsubmit="return checkForm('login')"/>
						<input type="hidden" name="id" value="<?=$id?>"/>
						<input type='hidden' name='process' value='0'/>

						<table cellSpacing="1" cellPadding="1" width="100%" border="0">
							<tr>
								<td align=center colspan=4 id='header'><b style="color:#fff">JÁ TENHO CADASTRO NA AMF</td>
							</tr>
							<tr>
								<td class="label" nowrap>E-mail:</td>
								<td colspan="3"><input maxlength="100" style="width:305px" id="email" name="email" onchange="checkEmail(this)" value="<?=$email?>" class="required"/></td>
							</tr>
							<tr>
								<td class="label">Senha:</td>
								<td><input type="password" maxlength="10" style="width:120px" id="senha" name="senha" onchange="toLower(this)" class="required"/>
							</tr>
							<?	if ($msgErr) echo"<tr bgcolor=#c40000><td colspan=4 align=center><b style='color:#fff'>$msgErr</b></td></tr>"; ?>

							<tr>
								<td colSpan="4" style="text-align:center">
									<input type="submit" id="btSend" value="Enviar" name="btLogin"/></td>
							</tr>
						</table>
					</form>

	<form name="cadastro" style="<?=$style?>" id="cadastro"  action="" method="POST" onsubmit="return checkForm('cadastro')"/>
		<input type="hidden" name="process" value="0"/>
		<input type="hidden" name="idCadastro" value="<?=isset($idCadastro) ? $idCadastro : $rsCadastro->idCadastro ?>"/>
		<input type="hidden" name="tipo" value="<?=$tipo?>"/>

		<table cellSpacing="1" cellPadding="1" width="100%" border="0">
			<tr>
				<td align=center colspan=4 height="18">
					 <input type="radio" name="tipo" value="0" <? if ($tipo==0) echo ' checked' ?>
					 	onclick="window.location.href='?lnk=80&typeCad=0'" /> <b>PESSOA FÍSICA
					 <input type="radio" name="tipo" value="1" <? if ($tipo==1) echo ' checked' ?>
					 	onclick="window.location.href='?lnk=80&typeCad=1'" /> <b>EMPRESA</b></td>
					 	
		<?					 	
			if ($tipo==0) echo"
			<tr>
				<td class='label'>Nome:</td>
				<td colspan='3'>
					<input maxlength='60' style='width:326px' name='nome' value='$rsCadastro->nome' onchange='toUpper(this)' class='required'></td>
			<tr>
				<td class='label'>CPF:</td>
				<td><input maxlength='18' style='width:120px' id='cpf' name='cpf' value='$rsCadastro->cpf' class='required'></td>
				<td class='label'>RG:</td>
				<td><input maxlength='20' style='width:132px' id='rg' name='rg' value='$rsCadastro->rg' class='required'></td>";

		else echo"
			<tr>
				<td class='label'>Empresa:</td>
				<td colspan='3'>
					<input maxlength='60' style='width:326px' name='nome' id='empresa' value='$rsCadastro->nome' onchange='toUpper(this)' class='required'></td>
			<tr>
				<td class='label'>CNPJ:</td>
				<td><input maxlength='18' style='width:120px' id='cnpj' name='cpf' value='$rsCadastro->cpf' class='required'></td>
				<td class='label'>Incr. Est.:</td>
				<td><input maxlength='20' style='width:132px' id='ie' name='rg' value='$rsCadastro->rg' class='required'></td>";
		?>

			<tr>
				<td class="label">Endereço:</td>
				<td colspan="3">
					 <input maxlength="100" style="width:266px" name="ender" id="endereço" onchange="toUpper(this)" value="<?=$rsCadastro->ender?>" class="required">
					 <b style="color:#000066">Nº: 
					 	 <input maxlength="6" style="width:40px; text-align:center" name="num" id="número" onchange="toUpper(this)" value="<?=$rsCadastro->num?>" class="required">
					 </td>

			<tr>
				<td class="label">Compl.:</td>
				<td colspan=3>
					<input maxlength="30" style="width:75px"  name="compl" id="Complemento" onchange="toUpper(this)" value="<?=$rsCadastro->compl?>">
					&nbsp;<b style="color:#000066">Bairro: 
					<input maxlength="50" style="width:206px"  name="bairro" id="bairro" onchange="toUpper(this)" value="<?=$rsCadastro->bairro?>" class="required"></td>
			<tr>		
				<td class="label">CEP:</td>
				<td colspan=3><input maxlength="10" style="width:70px; text-align:center" id="cep" name="cep" onchange="checkCEP(this, 2)" value="<?=$rsCadastro->cep?>" class="required">
					&nbsp;<b style="color:#000066">Cidade: 
					<input maxlength="50" style="width:148px" name="cidade" onchange="toUpper(this)" value="<?=$rsCadastro->cidade?>" class="required"> / 
					UF:
					<input maxlength="2" id="uf" style="width:30px; text-align:center" name="uf" onchange="toUpper(this)" value="<?=$rsCadastro->uf?>" class="required">
			</tr>

			<tr>
				<td class="label">E-mail:</td>
				<td colspan="3"><input maxlength="100" style="width:326px" id="email" name="email" value="<?=$rsCadastro->email?>" onchange="checkEmail(this)" class="required"></td>

			<tr>
				<td class="label">DDD/Fone:</td>
				<td><input style="width:120px" id="fone" name="fone" maxlength="50" value="<?=$rsCadastro->fone?>" class="required"></td>
				
		<?
			if ($tipo==0) echo"
				<td class='label' getdaterap>DDD/Cel.:</td>
				<td><input name='fone2' id='cel' style='width:132px' maxlength='20' value='$rsCadastro->fone2'>
			<tr>
				<td class='label' getdaterap>Profissão</td>
				<td colspan=3><input name='profissao' id='profissao' style='width:326px' maxlength='50' value='$rsCadastro->profissao' onchange='toUpper(this)'/>";
				
			else echo"
				<td class='label' getdaterap>DDD/Fax:</td>
				<td><input name='fone2' id='telefone #2' style='width:132px' maxlength='20' value='{$rsCadastro['fone2']}'>
			<tr>
				<td class='label' getdaterap>Contato</td>
				<td colspan=3><input name='profissao' id='contato' style='width:326px' maxlength='50' value='$rsCadastro->profissao' onchange='toUpper(this)'/>";
		?>
				
			<tr>
				<td style="text-align:center" colspan="4" style="background:#efefef">
					Crie uma senha para o seu próximo acesso! <b>Ex.: fulano0607</b>
			<tr>
				<td class="label">Senha:</td>
				<td><input type="password" maxlength="10" style="width:120px" name="senha" id="senha" value="<?=$rsCadastro->senha?>" onchange="toLower(this)" class="required">
				<td class="label"><b class="blue">Confirme:</td>
				<td><input type="password" maxlength="10" style="width:132px" name="senha2" name="confirma" value="<?=$rsCadastro->senha?>" onchange="toLower(this)" class="required">
			</tr>
			<?	if ($newErr) echo"<tr><td colspan=4 class='msgErr'>$newErr</td></tr>"; ?>
			<tr>
				<td colSpan="4" style="text-align:center">
					<input type="submit" id="btSend" value="Enviar" name="btNewCad"/></td>
			</tr>
		</table>
	</form>

<?
/*
	if ($boleto_id) {
		echo"
			<p style='text-align:center'><br><a href='javascript:mostraBoleto()'>
				<img src='images/boleto.gif' border=0/></a><br />Clique na imagem acima para imprimir o boleto correspondente à Taxa de Associado AMF.</p>
	
			<script language='Javascript'>
				<!--//
				function mostraBoleto() {
					winBol=window.open('boletos/boleto.php?id=$boleto_id','vpos','toolbar=no,menubar=no,resizable=yes,status=no,scrollbars=yes,width=720,height=485');
					winBol.focus();
					}
				//-->
			</script>";
			}
	*/
?>
</div>
</div>
