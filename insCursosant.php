<style>
	p, P {margin:4px 0}
	#cursos td {border-bottom:1px solid #c0c0c0}
	#cursos p {
		background:url(images/h_arrow.gif) no-repeat 2px 2px;
		padding-left:14px;
		margin:4px 0;
		font:normal 9pt Tahoma, Verdana, Arial, Helvetica;
		color:#000066
		}
	.subTitulo {
		background:url(css/title_bg.jpg) repeat-x;
		text-align:left;
		font:bolder 10pt Tahoma, Verdana, Arial, Helvetica;
		color:#fff;
		padding:4px;
		text-transform:uppercase	
		}
</style>
<?
include 'global.php';
include 'includes/funcoesUteis.php';

$idCurso=isset($_GET['idCurso']) ? $_GET['idCurso'] : $_POST['idCurso'];
if (!idCurso) die("ID do curso não passado");

$query=mssql_query("select *, 
	convert(char(10),inicio,103) as inicio,
	convert(char(10),final,103) as final 
	from cursos where idCurso=$idCurso");
$rsCurso=mssql_fetch_object($query);

$rsAluno['cpf']=ereg_replace("[' '-./ \t]",'',$_POST['cpf']);
$rsAluno['email']=addslashes(trim($_POST['email']));
$rsAluno['senha']=trim($_POST['senha']);

 	


if (isset($_POST['btSend'])) {

	// formulario foi submetido entao prepara o e-mail para envio

	//	error_reporting(E_ALL);
	include("mail/htmlMimeMail.php");

	// prepara o corpo da mensagem e chama a funcao sendmail()

	$mailFrom = trim($_POST['mailFrom']);
	$nameFrom = trim($_POST['nameFrom']);
	$mailTo = $_POST['mailTo'];
	$nameTo = trim($_POST['nameTo']);
	$subject = trim($_POST['message']);
	$titulo = trim($_POST['titulo']);
	$link = $_POST['link'];

	$body="	
		<table border=0 width=100% >
			<tr>
				<td class=boxBorder bgcolor=#ffffff align=center width=150>
					<img border=0 src='{$urlSite}/images/contato.jpg'></td>
				<td align=right class=boxBorder style='text-align:center'><br>
					Olá <b>$nameTo</b>, $nameFrom ($mailFrom) visitando nosso site 
					leu a matéria <b>$titulo</b> e acha que é de seu interesse.
					<br><br>
					Clique aqui para acessar a matéria indicada:
					<a href='$link'>$link</a><br><br>
			<tr>
				<td align=right class=boxBorder>Mensagem:</td>
				<td class=boxBorder colspan=3><b>$subject</td></tr>
		</table>";	

	$mailSend = sendMail($mailTo, $nameFrom, $mailFrom, 'AMFAR - Cursos', $body,'Indicação');
	@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameFrom', '$mailFrom')");
	@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameTo', '$mailTo')");
	}
/* fim do indique */











// veio do form login
if($_POST['btLogin']) {
	$sql="select * from cadastro where email='$email' and senha='$senha'";
//	echo "$sql";
	$query=mssql_query($sql);
	if (!mssql_num_rows($query)) {
		$msgErr="Desculpe mas seu cadastro não foi localizado!<br />
		Verifique se digitou os dados corretos e tente novamente ou<br />
		preencha o formulário abaixo se for novo aluno!";
		}
	
	else
		$rsAluno=mssql_fetch_assoc($query);
	}
// veio do form login	



















// veio form cadastro
if (isset($_POST['btNewCad'])) {
	//	debug($_POST);
	$rsAluno['idCadastro']=isset($_POST['idCadastro']) ? $_POST['idCadastro'] : 0;
	$rsAluno['nome']=addslashes(trim($_POST['nome']));
	$rsAluno['rg']=addslashes(trim($_POST['rg']));
	$rsAluno['ender']=addslashes(trim($_POST['ender']));
	$rsAluno['num']=addslashes(trim($_POST['num']));
	$rsAluno['compl']=addslashes(trim($_POST['compl']));
	$rsAluno['bairro']=addslashes(trim($_POST['bairro']));
	$rsAluno['cidade']=addslashes(trim($_POST['cidade']));
	$rsAluno['uf']=addslashes(trim($_POST['uf']));
	$rsAluno['cep']=addslashes(trim($_POST['cep']));
	$rsAluno['fone']=addslashes(trim($_POST['fone']));
	$rsAluno['fone2']=addslashes(trim($_POST['fone2']));
	$rsAluno['profissao']=addslashes(trim($_POST['profissao']));

	if ($rsAluno['idCadastro']==0) {
	 
		$query=mssql_query("select idCadastro from cadastro where cpf='{$rsAluno['cpf']}'");		
		if (mssql_num_rows($query) > 0)
			$newErr="Este CPF já está cadastrado em nossa base de dados";
		else {
			$sql="insert into cadastro(cpf, status, dataReg, ultAcesso) 
				values('$cpf', 0, getdate(), getdate())
				select @@Identity as LastID";
			$query=@mssql_query($sql);
			$rsAluno['idCadastro']=mssql_result($query, 0, 'LastID');
			@mssql_query("insert into mailing (dataReg, nome, email, uf) values (getdate(), '{$rsAluno['nome']}', '{$rsAluno['email']}', '{$rsAluno['uf']}')");
			}
		}


	
	// atualiza o cadastro do aluno
	if ($rsAluno['idCadastro'] > 0) {

		mssql_query("update cadastro set
			idGrupo=2,
			nome='{$rsAluno['nome']}',
			ender='{$rsAluno['ender']}',
			bairro='{$rsAluno['bairro']}',
			num='{$rsAluno['num']}',
			compl='{$rsAluno['compl']}',
			cep='{$rsAluno['cep']}',
			cidade='{$rsAluno['cidade']}',
			uf=upper('{$rsAluno['uf']}'),
			fone='{$rsAluno['fone']}',
			fone2='{$rsAluno['fone2']}',
			email='{$rsAluno['email']}',
			cpf='{$rsAluno['cpf']}',
			rg='{$rsAluno['rg']}',
			profissao='{$rsAluno['profissao']}',
			senha=lower('{$rsAluno['senha']}')
		where idCadastro={$rsAluno['idCadastro']}") or die("Erro 124");		
		}
	}
// if (isset($_POST['btNewCad']))














if ($_POST['btConfirma']) {

	//	$query=mssql_query("select * from cadastro where idCadastro={$_POST['idCadastro']}")
	$query=mssql_query("select * from cadastro where idCadastro={$_POST['idCadastro']}");
	$rsAluno=mssql_fetch_object($query);
	$sql="select idAlunoCursos, convert(char(10), data, $dataFilter) as data from alunosCursos where idAluno={$_POST['idCadastro']} and idCurso={$_POST['idCurso']}";	
	$query=mssql_query($sql);
	if (mssql_num_rows($query) > 0) {
		$data=mssql_result($query, 0, 'data');
		echo"
		<link rel='stylesheet' href='js/formStyle.css' type='text/css'/>
		<link rel='stylesheet' href='css/style.css' type='text/css' />
		<body onload='resizeTo(500,410)' style='floating:scroll-x:none' style='background:#ffffff'/>
			<img src='images/inscricaoOnLine.gif'>
			<table border=0 width=70% align=center>
				<tr>
					<td align=center>
						<p style='text-align:center'><br>
							Caro(a) <b>$rsAluno->nome</b></font>,<br><br>
							Sua matrícula para o curso<br><br>
							<b>$rsCurso->titulo</b><br><br>
							já foi efetuada no dia <b>$data</b>.<br>
						<p style='text-align:center'><br><a href='javascript:self.close()'>Fechar</a></p>
					</td>
				</tr>
			</table>";
		die();
		}
	
	$sql="insert into alunosCursos(idCurso, idAluno, data, status) values({$_POST['idCurso']}, {$_POST['idCadastro']}, getdate(), 1)";
	mssql_query($sql) or die("Erro $sql");

//	$contaCursos=41102001;
	$offSet=4;
	if ($rsCurso->status < 2) {
		for ($x=1; $x <= $rsCurso->parcelas; $x++) {
			$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("0$x", -2);
			$vcto = date("m.d.Y", time() + ($offSet * 86400));
			
			$sql="insert into financeiro(idCadastro, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, formaPgto, boleto, status, dataReg) values
				($rsAluno->idCadastro, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas,3, '$numdoc', 2, getdate())
				select @@Identity as LastID";		
			$query=mssql_query($sql) or die("Erro $sql");
			$offSet =$offSet+30;
			if ($x==1) $boleto_id=mssql_result($query, 0, 'LastID');
			}
		}

	include 'mail/htmlMimeMail.php';
	$strValor=formatVal($rsCurso->valor/$rsCurso->parcelas);

	$matricula=substr("0000000$idCadastro",-8) . substr("000$idCurso",-3);

	$eBody="
	<table cellSpacing=2 cellPadding=2 width=100% border=0>
		<tr>
			<td align=right class=boxBorder>Matrícula:</td>
			<td class=boxBorder><b>$matricula</b></td>
			<td align=right class=boxBorder>Data:</td>
			<td class=boxBorder><b>". date('d/m/Y') . "</b></td>
		</tr>
		<tr>
			<td align=right class=boxBorder>Aluno:</td>
			<td class=boxBorder><b>$rsAluno->nome</b></td>
			<td align=right class=boxBorder>CPF:</td>
			<td class=boxBorder><b>$rsAluno->cpf</b></td>
		</tr>
		<tr>
			<td align=right class=boxBorder rowspan=2>Endereço:</td>
			<td class=boxBorder rowspan=2>
				 <b>$rsAluno->ender<br />
				 $rsAluno->bairro<br />
				 $rsAluno->cep $rsAluno->cidade/$rsAluno->uf</b></td>
			<td align=right class=boxBorder>RG:</td>
			<td class=boxBorder><b>$rsAluno->rg</b></td>
		</tr>
		<tr>
			<td align=right class=boxBorder>Fone:<br/>Cel.:</td>
			<td class=boxBorder><b>$rsAluno->fone<br/>$rsAluno->fone2</b></td>
		</tr>

		<tr>
			<td align=right class=boxBorder>E-mail:</td>
			<td class=boxBorder colspan=3><b>$rsAluno->email</b></td>
		</tr>
		<tr>
			<td align=right class=boxBorder>Curso:</td>
			<td class=boxBorder><b>$rsCurso->titulo</b></td>
			<td align=right class=boxBorder>Valor:</td>
			<td class=boxBorder><b>$rsCurso->parcelas x $strValor</b></td>
		</tr>
	</table>";
	// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
	$mailsend = sendMail($rsAluno->email, $rsAluno->nome, $rsAluno->email, 'Inscrição on Line', $eBody,'Inscrição on Line');
//	echo "enviando e-mail para: $rsAluno->nome ($rsAluno->email)";
	$mailsend = sendMail($eMatricula, $rsAluno->nome, $rsAluno->email, 'Inscrição on Line', $eBody,'Inscrição on Line');
//	echo "enviando e-mail para: $rsAluno->nome ($rsAluno->email)";

	echo"
	<link rel='stylesheet' href='js/formStyle.css' type='text/css'/>
	<link rel='stylesheet' href='css/style.css' type='text/css' />
	<body onload='resizeTo(500,410)' style='floating:scroll-x:none' style='background:#ffffff'/>
		<img src='images/inscricaoOnLine.gif'>
		<table border=0 width=70% align=center>
			<tr>
				<td align=center nowrap>
					<p style='text-align:center'><br>
						Caro(a) <b>$rsAluno->nome</b></font>,<br>
						Sua inscrição foi enviada com sucesso.</b></font><br><br/>
						<a href='contrato.php?idCadastro=$rsAluno->idCadastro&idCurso=$idCurso' target='_blank'>
							<b>Clique Aqui</b></a> para visualizar e imprimir o contrato<br/><br/>";
				
				if ($rsCurso->status < 2 && $rsCurso->boleto==1) echo"
					<p style='text-align:center'><br><a href='javascript:mostraBoleto()'>
					<img src='images/boleto.gif' border=0/></a><br />Clique na imagem acima para imprimir o boleto correspondente à matrícula</p>";
				
				echo"	
					<p style='text-align:center'><br><a href='javascript:self.close()'>Fechar</a></p>
				</td>
			</tr>
		</table>

		<script language='Javascript'>
			<!--//
			function mostraBoleto(id) {
				winBol=window.open('boletos/boleto.php?id=$boleto_id','vpos','toolbar=no,menubar=no,resizable=yes,status=no,scrollbars=yes,width=720,height=485');
				winBol.focus();
				}
			//-->
		</script>
	</body>";
	die();
	}
// if ($_POST['btConfirma'])



 // debug($rsCurso)
  ?>


	<html>
		<head>
			<title>AMFAR - Informações de Cursos</title>
			<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
			<link rel="stylesheet" href="css/style.css" type="text/css">
			<link rel="stylesheet" href="js/formStyle.css" type="text/css">
			<link rel="stylesheet" href="css/cursos.css" type="text/css">
			<script language="javascript">
				var lastId=false;				  	  
				function changeDisplay(id) {
					var div = document.getElementById(id);
					var last = document.getElementById(lastId);

					if (div.className=='invisivel') {
						div.className='visivel';
						if (last) last.className=='invisivel';
						lastId=id;
						}
					else {
						div.className='invisivel';
						lastId=false;
						}
					}
			</script>			
		</head>
		
		<body onload="resizeTo(550,500)" style="background:#ffffff">
			<div id='mainTop' style='background:url(images/mainCursos<?=$rsCurso->status?>.png) no-repeat; height:24px'></div>
			<div id="news" style="padding:8px; text-align:left">
				<?= $rsCurso->descricao ?></div>


			<div id="newsOptions" style="background:#efefef; border:1px solid #c0c0c0; padding:4px">
				<img src="images/Novo.gif" border="0" align="absMiddle" onclick="self.print()">
					&nbsp;<a href="#inscricao">Inscrição on line</a>

				<img style="margin-left:10px" src="images/printer.png" border="0" align="absMiddle" onclick="self.print()">
					&nbsp;<a href="JavaScript:void(null)" onclick="self.print()">Imprimir</a>

				<img style="margin: 0 10px" src="images/email.gif" border="0" align="absMiddle">
					<a href="#indique" onclick="changeDisplay('envie')">Enviar&nbsp;para&nbsp;amigos</a>
			</div><?

			if ($mailSend =='E-mail enviado com sucesso') echo"
				<table border=0 align=center bgcolor=#efefef style='margin:10px; border:1px solid #336699'>
					<tr>
						<td align=center nowrap style='padding:6px 20px'>
								Prezado(a) <b>{$nameFrom}</b>, o link foi enviado com sucesso.</b></font><br><br>
								<b>Obrigado por utilizar nossos servi&ccedil;os!</b><br><br>
								Se ficou satisfeito, indique-nos para seus amigos!
						</td>
					</tr>
				</table>";	?>

				<div id='envie' class='invisivel'><a name='indique'>
					<form name='envieAmigo' id='envieAmigo' method='POST' action='#' onsubmit="return checkForm('envieAmigo')"/>
						<input type='hidden' name='link' value='<?=$urlSite?>/lerCurso.php?idCurso=<?=$idCurso?>'/>
						<input type='hidden' name='titulo' value='<?=$rsCurso->titulo ?>'/>
						<input type='hidden' name='process' value='0'/>
						<table cellpadding='1' cellspacing='1' width=100%>
							<tr>
								<td colspan=2 align=center>
									<div id="header"><div id="titPage">Envie este link a um(a) amigo(a)</div></div>
							<tr>
							<tr>
								<td class='label'>Seu nome:
								<td><input name='nameFrom' style='width:280px' onchange='toUpper(this)' value="<?=$nameFrom?>" class='required'/>
							<tr>
								<td class='label'>Seu E-mail:
								<td><input name='mailFrom' id='email' style='width:280px'  value="<?=$mailFrom?>"  class='required'/>
							<tr>
								<td class='label'>Nome amigo(a):
								<td><input name='nameTo' style='width:280px' class='required' onchange='toUpper(this)'/>
							<tr>
								<td class='label'>E-mail amigo(a):
								<td><input name='mailTo' id='email' style='width:280px' class='required'/>
							<tr>
								<td class='label'>Comentários:					
								<td>
									<textarea id='message' name='message' rows='5' style='width:280px' class='required'/><?=$subject?></textarea>
							</tr>
							<tr>
								<td colspan='2' align=center>			  
								<input type='submit' id='btSend' name='btSend' value='Enviar'/>
							</tr>
						</table>
					</form>
				</div><!-- align -->




			<?	if (!$rsAluno['idCadastro']) { ?>

				<div id="inscricaoOnLine"><a name='inscricao'><br />
					<p align=center><span style="color:#336699">.</span><br/>
						<b>.</b>
					<form name="login" id="login" style="margin-top:0" action="#cad" method="POST" onsubmit="return checkForm('login')"/>
						<input type="hidden" name="idCurso" value="<?=$idCurso?>"/>
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
					</form><?
					}	?>

					<!-- cadastro completo --><a name='cad'>
					<form name="cadastro" id="cadastro" style="margin-top:0" action="#cnf" method="POST" onsubmit="return checkForm('cadastro')"/>
						<input type="hidden" name="idCurso" value="<?=$idCurso?>"/>
						<input type="hidden" name="idCadastro" value="<?=$rsAluno['idCadastro']?>"/>
						<input type='hidden' name='process' value='0'/>

						<table cellSpacing="1" cellPadding="1" width="100%" border="0">
							<tr>
								<td align=center colspan=4 height="18" id="header">CADASTRO DE NOVO ALUNO</td>
							</tr>
							<tr>
								<td align=center colspan=4 bgcolor=#ffffff><?
									if ($rsAluno['idCadastro'] > 0) echo"
										<b>Confirme seus dados e altere se necessário!</b>";
									else echo"
										Preencha o cadastro abaixo para sua inscrição on line no curso selecionado";
									?>
								</td>
							</tr>
							<tr>
								<td class="label"><b class="blue">Nome:</td>
								<td colspan="3">
									<input maxlength="60" style="width:326px" name="nome" id="nome" value="<?=$rsAluno['nome']?>" onchange="toUpper(this)" class="required"></td>
							</tr>
							<tr>
								<td class="label">CPF:</td>
								<td><input maxlength="18" style="width:120px" id="cpf" name="cpf" value="<?=$rsAluno['cpf']?>" class="required"></td>
								<td class="label">RG:</td>
								<td><input maxlength="20" style="width:131px" id="rg" name="rg" value="<?=$rsAluno['rg']?>" class="required"></td>
							</tr>
							<tr>
								<td class="label">Endereço:</td>
								<td colspan="3"><input maxlength="100" style="width:258px" name="ender" onchange="toUpper(this)"
									value="<?=$rsAluno['ender']?>" class="required">
								&nbsp;<b style="color:#000066">Nº: <input maxlength="6" style="width:45px" name="num" id="Número"
									onchange="toUpper(this)" value="<?=$rsAluno['num']?>" class="required">
								</td>
							</tr>
							<tr>
								<td class="label">Compl.:</td>
								<td colspan=3>
									<input maxlength="30" style="width:75px"  name="compl" id="Complemento" onchange="toUpper(this)" value="<?=$rsAluno['compl']?>">
									&nbsp;<b style="color:#000066">Bairro: 
									<input maxlength="50" style="width:206px"  name="bairro" onchange="toUpper(this)" value="<?=$rsAluno['bairro']?>" class="required"></td>
							<tr>		
								<td class="label">CEP:</td>
								<td colspan=3><input maxlength="10" style="width:70px; text-align:center" id="cep" name="cep" onchange="checkCEP(this, 2)" value="<?=$rsAluno['cep']?>" class="required">
									&nbsp;<b style="color:#000066">Cidade: 
									<input maxlength="50" style="width:148px" name="cidade" onchange="toUpper(this)" value="<?=$rsAluno['cidade']?>" class="required"> / 
									UF:
									<input maxlength="2" id="uf" style="width:30px; text-align:center" name="uf" onchange="toUpper(this)" value="<?=$rsAluno['uf']?>" class="required">
							</tr>
							<tr>
								<td class="label">E-mail:</td>
								<td colspan="3"><input maxlength="100" style="width:326px" id="email" name="email" value="<?=$rsAluno['email']?>" onchange="checkEmail(this)" class="required"></td>
							</tr>
							<tr>
								<td class="label">Fone:</td>
								<td><input style="width:120px" id="fone" name="fone" maxlength="50" value="<?=$rsAluno['fone']?>" class="required"></td>
								<td class="label" nowrap>Celular:</td>
								<td><input name="fone2" id="celular" style="width:131px" maxlength="20" value="<?=$rsAluno['fone2']?>"></td>
							</tr>
							<tr>
								<td class="label">Profissão:</td>
								<td colspan=3><input style="width:326px" id="profissao" name="profissao" maxlength="100" value="<?=$rsAluno['profissao']?>" class="required"></td>
							</tr>
							<tr>
								<td style="text-align:center" colspan="4" bgColor="#ffffff">
									Crie uma senha para o seu próximo acesso! <b>Ex.: fulano0607</b>
							<tr>
								<td class="label">Senha:</td>
								<td><input type="password" maxlength="10" style="width:120px" name="senha" value="<?=$rsAluno['senha']?>" onchange="toLower(this)" class="required">
								<td class="label"><b class="blue">Confirme:</td>
								<td><input type="password" maxlength="10" id="confirme a senha" style="width:130px" name="senha2" value="<?=$rsAluno['senha']?>" onchange="toLower(this)" class="required">
							</tr>
							<?	if ($newErr) echo"<tr bgcolor=#c40000><td colspan=4 align=center><b style='color:#fff'>$newErr</b></td></tr>"; ?>
							<tr>
								<td colSpan="4" style="text-align:center">
									<input type="submit" id="btSend" value="Enviar" name="btNewCad"/></td>
							</tr>
						</table>
					</form>
				</div>					
				<script language="javascript">
					function checkButton() {
						document.confirma.btConfirma.disabled=true;
						if (document.confirma.concordo.checked)
							document.confirma.btConfirma.disabled=false;							
						}
				</script>

				<form name='confirma' style='width:100%; margin-top:0; border:0; background:#fff' action='' method='POST'  onsubmit="return checkForm('confirma')"/>
					<input type="hidden" name="idCurso" value="<?=$idCurso?>"/>
					<input type="hidden" name="idCadastro" value="<?=$rsAluno['idCadastro']?>"/>
					<input type='hidden' name='process' value='0'/>
					<style>
						#dvContrato {
							border:1px solid #336699;
						   float:left;
						   padding-top:4px;
						   margin-top:2px;
						   width:510px;
						   height:150px;
						   background:#33669;
						   overflow:scroll;
						   }
					</style>
					<center>

					<? if ($rsAluno['idCadastro'] > 0) {
							echo"<a name='inscricao'>
							<div id='dvContrato'><b style=color:red>LEIA COM ATENÇÃO!</b><br/><br/>";
							include 'divContrato.html';
							echo"<br/>
							</div>
							<input onclick='checkButton()' type='checkbox' id='Eu concordo' name='concordo' class='required'/> Eu li o contrato e estou de acordo!<br>
							<input type='submit' id='btSend' style='font-size:10pt; font-weight:bolder; color:#ff6600' value='Confirmar Inscrição' name='btConfirma' disabled />";
							} ?></center>
				</form>			
		</body>
	</html>
