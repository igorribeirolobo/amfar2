<?

session_start();

if ($_POST['btLogar'])	{
	$sql="select uid, nome, status, nivel from usuarios where email='{$_POST['email']}' and senha='{$_POST['senha']}'";
	
	$query=mssql_query($sql);
	if (mssql_num_rows($query) > 0) {	
		$result=mssql_fetch_object($query);
		}

	if ($result->status > 0) {
		$_SESSION['admUid']=$result->uid;	// aponta para o cadastro de empresa
		$_SESSION['admUser']=$result->nome;
		$_SESSION['admNivel']=$result->nivel;
		}
	else
		$message="<p style='background:#c40000; text-align:center'>
		<b style='color:#fff'>Acesso não permitido!!!<br>
		Se estiver autorizado a esta seção,<br/>corrija e tente novamente!</b>";
	}
	
if (isset($_SESSION['admUser'])) {
	echo"<script>document.location.href='admin/';</script>";
	die();
	}

else {	?>


	<div id="mainTop" style="background:url(images/mainRestrito.png) no-repeat; height:24px; padding-left:200px">
		<p style="margin-top:4px"><b>Somente pessoas autorizadas da AMF</div>	

	<div align=center>	
		<form name="login" style="width:50%" id="login" action="" method="POST" onsubmit="return checkForm('login')">
			<input type="hidden" name="process" value="">	
			<table border="0" cellspacing="1" width="100%" align="center" style="margin:4px ; border: 4px dotted #c40000" cellpadding="3">
				<tr bgcolor="#000000">
					<td style='text-align:center' colspan="2">
						<b><font size="4">ÁREA RESTRITA</font></b></td>
				</tr>
				<tr bgcolor="#efefef" colspan="2">
					<td style='text-align:center' colspan="2" height=40><b style='color:#000'>
						Você está numa seção de acesso restrito.
		
					<tr bgcolor="#efefef" colspan="2">
						<td style='text-align:center' colspan="2" height=40>
						<a href="http://webmail.amfar.com.br/" target="_blank">
							<img src="images/email.gif" border="0" align="absMiddle"><br/>
							<b style='color:#000'>Clique aqui para acesso às contas de e-mail<br />da amfar.com.br</a>
	
				<tr>
					<td style='text-align:center' height=40 colspan="2">
						Para acesso administrativo, preencha os campos abaixo<br>
						e pressione o botão <b>Continuar</b>.</td>
				</tr>
				<tr>
					<td class="label" height=20>E-mail:</td>
					<td bgcolor="#efefef" style='border:1px solid #c0c0c0'><input type="text" name="email" id="email" size=40 value="" class="required">
	
				<tr>
					<td class="label" height=20>Senha:</td>
					<td bgcolor="#efefef" style='border:1px solid #c0c0c0'><input type="password" name="senha" id="senha" maxlength="10"" value="" class="required">
				<tr>							
					<td style='text-align:center' colspan="2"><?= $message ?>	
				<tr>
					<td style='text-align:center' height=40 colspan="2">
						<input type="submit" id="btSend" name="btLogar"  value="Continuar"></td>
				</tr>			
			</table>
		</form>
	</div>
<?	}	?>
