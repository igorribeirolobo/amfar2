<?
session_start();
$_msg=null;
if ($_POST['email']&&$_POST['senha'])	{
	// $_senha = base64_encode(convert_uuencode(trim($_POST['senha'])));
	$_senha = $_POST['senha'];
	$_email = trim($_POST['email']);
	if (!ereg("^([0-9,a-z,A-Z]+)([.,_]([0-9,a-z,A-Z]+))*[@]([0-9,a-z,A-Z]+)([.,_,-]([0-9,a-z,A-Z]+))*[.]([0-9,a-z,A-Z]){2}([0-9,a-z,A-Z])?$", $_email)||empty($_email))
		$_msg = "Dados Inválidos";
	else {
		$sql="SELECT uid, nome, status, nivel FROM usuarios WHERE email='$_email' AND senha='$_senha'";
		//echo "sql=$sql";	
		$query=mssql_query($sql);	
		if (mssql_num_rows($query) > 0) {	
			$rs=mssql_fetch_object($query);		
			if ($rs->status > 0) {
				$_SESSION['admUid']=$rs->uid;	// aponta para o cadastro
				$_SESSION['admUser']=$rs->nome;
				$_SESSION['admNivel']=$rs->nivel;
				echo"<script>document.location.href='index.php'</script>";
				}
			else
				$_msg="Acesso não permitido!!!\n\n\n\nContacte o administrador!</b>";
			}
		else
			$_msg="Usuário não localizado!";
		}
	}
?>
<style>
	#dvLogin {width:300px;background:#fff;margin:0 auto;margin-top:120px}
	#myForm th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	#myForm a{color:#006;text-decoration:none}
	#myForm a:hover{color:#00f;border-bottom:solid 2px #00f}
	#myForm #email{width:230px;background:#fff;border:solid 1px #369}
	#myForm #senha{width:100px;text-align:center;background:#fff;border:solid 1px #369}
	#myForm #btSend{cursor:pointer;padding:0}
</style>

<script>
	$(document).ready(function() {
		var originColor='#fff';
		var alertColor='#FADEC5';

		$('.text').focus(function() {
			$('.text').each(function() {
				$(this).css('background',originColor);
				})
			$(this).css('background',alertColor);
			})
			
		$('#btSend').click(function() {
			if ($('#email').val()=='') {
				alert("Digite seu e-mail!");
				$('#email').focus();
				return false;
				}
//			else if (!$('#email').val().validEmail()) {
//				alert("Digite corretamente seu e-mail!");
//				$('#email').focus();
//				return false;
//				}
			else if ($('#senha').val()=='') {
				alert("Digite sua senha!");
				$('#senha').focus();
				return false;
				}
			$('#myForm').submit();
			})
				
		if ($('#msg').val()!='')
			alert($('#msg').val());
			
		$('#email').focus();
		})
</script>
<center>
<div id="dvLogin">
	<fieldset>
		<legend>Login de Usuário</legend>
		<form name="myForm"  id="myForm" style="margin:0" action="" method="POST">
			<input type="hidden" name="msg" id="msg" value="<?=$_msg?>"/>
			<table border="0" cellspacing="1" cellpadding="2">
				<tr>
					<th colspan="3">ÁREA RESTRITA</th>
				</tr>
				<tr>
					<td align="center" colspan="3">
						<img src="images/bloqueado.png" border="0"/>
					</td>
				</tr>		
				<tr>
					<td colspan="3" bgcolor="#B5C6D6">
						<p align="center">
							<a href="http://webmail.amfar.com.br/" target="_blank">
							<img src="images/email.gif" border="0" align="left">							
								Clique aqui para acesso às contas de e-mail</a>
						</p>
					</td>
				</tr>	
				<tr>
					<td align="center" colspan="3">
						Para acesso administrativo, preencha os campos abaixo e pressione o botão <b>Continuar</b>.
					</td>
				</tr>
				<tr>
					<td class="label">E-mail:</td>
					<td colspan="2"><input type="text" class="text" name="email" id="email" value=""/></td>
				</tr>	
				<tr>
					<td class="label">Senha:</td>
					<td><input type="password" class="text" name="senha" id="senha" maxlength="10"" value=""/></td>
					<td align="center"><input type="button" id="btSend" name="btSend" value="Continuar"/></td>
				</tr>			
			</table>
		</form>
	</fieldset>
</div>
<center>
