<?
include 'includes/dbConnect.php';
include 'includes/funcoesUteis.php';

if ($_POST['btnLogon']) {		
	$user=$_POST['usuario'];
	$pwd=$_POST['senha'];
	checkPost(); // verifica sql injection
	
	$sql="SELECT id, fantasia, date_Format(ultimoAcesso, '%d/%m/%Y às %H:%i:%s') as lastAccess FROM clientes WHERE email='$user' AND senha='$pwd'";
//	echo $sql;
	$query=mysql_query($sql);
	if (@mysql_num_rows($query)==0) {
		$msg="Cliente não localizado !!!";
		}	
	else {
		$_SESSION['idCliente']=mysql_result($query,0,'id');
		$_SESSION['cliente']=mysql_result($query,0,'fantasia');
		$_SESSION['user']=$user;
		$lastAccess=mysql_result($query, 0, 'lastAccess');
		$sql="UPDATE clientes SET ultimoAcesso=now() WHERE id={$_SESSION['idCliente']}";
		mysql_query($sql) or die("Erro $sql");
		echo "<script>document.location.href='index.php?mn=4&pg=44';</script>";
		}
	}
	
if ($_SESSION['idCliente']) {
	$sql="SELECT ultimoAcesso FROM clientes WHERE id={$_SESSION['idCliente']}";
	$query=mysql_query($sql) or die("Erro $sql");
	$rsQuery = mysql_fetch_object($query);
	$data = explode('-',$rsQuery->ultimoAcesso);
	$hora = explode(' ',$data[2]);
//	debug($data);
//	debug($hora);
	?>
	<table cellSpacing="0" cellPadding="0" border="0" width="100%">
		<tr>
			<td valign="top" >
				<p style="text-align:center">Olá <b><?=$_SESSION['cliente']?></b>,<br/><br>
					Seja bem vindo!<br/><br>
					Seu último acesso foi<br/><br/><b><?= "{$hora[0]} {$data[1]} {$data[0]} {$hora[1]}" ?></b>.</p>
			</td>
		</tr>
	</table><?
	}

else {

?>
<table cellSpacing="0" cellPadding="0" border="0" width="100%">
	<tr>
		<td valign="top">
			<div id="upContainer">
				<p style="text-align:center">
					<img src="images/stop.png"/><br/><br/>
					<b style="font-size:12pt; color:#ff0000">Atenção:</b><br/>Você está numa área restrita a Clientes cadastrados !!!<br/><br/>
					Caso não tenha recebido sua Senha,<br/> 
					contacte o nosso Depto. Comercial e solicite.			
				</p>
				<br/>
				<form name="forgot" id="forgot" action="" method="POST" onsubmit="return checkForm('forgot')">
					<input type="hidden" name="process"/>
					<input type="hidden" name="url" id="logonADM"/>
					<center>
					<table border="0" cellpadding="2" cellspacing="2" class="uptable">
						<tr>
							<td class="label">E-mail:</td>
							<td>
								<input type="text" class="required" name="usuario" id="usuario" maxLength="100" style="width:250px"
									value="" /></td>
						</tr>
						<tr>
							<td class="label">CPF/CNPJ:</td>
							<td><input type="text" class="required" name="cnpj" id="cnpj" maxLength="10" style="width:100px" /></td>
						</tr>
						<tr bgcolor="#C7DBF0" align="center">
							<td class="inputcol" colspan=2>
								<input type="submit" name="btnForgot" value="Re-Enviar Senha" style="cursor:pointer"/>
							</td>
						</tr>
					</table>
					<p align="center" style="color:#ff0000; font-weight:bolder"><?= $msg ?></p>
					</center>
				</form>
				<br/><br/>
				<form name="login" id="login" action="" method="POST" onsubmit="return checkForm('login')">
					<input type="hidden" name="process"/>
					<input type="hidden" name="url" id="logonADM"/>
					<center>
					<table border="0" cellpadding="2" cellspacing="2" class="uptable">
						<tr>
							<td class="label">E-mail:</td>
							<td>
								<input type="text" class="required" name="usuario" id="usuario" maxLength="100" style="width:250px"
									value="<?=$_POST['usuario']?>" /></td>
						</tr>
						<tr>
							<td class="label">Senha:</td>
							<td><input type="password" class="required" name="senha" id="senha" maxLength="10" style="width:100px" /></td>
						</tr>
						<tr bgcolor="#C7DBF0" align="center">
							<td class="inputcol" colspan=2>
								<input type="submit" name="btnLogon" value="Logon" style="cursor:pointer"/>
							</td>
						</tr>
					</table>
					<p align="center" style="color:#ff0000; font-weight:bolder"><?= $msg ?></p>
					</center>
				</form>
			<div>
		</td>
	</tr>
</table>
<? } ?>
