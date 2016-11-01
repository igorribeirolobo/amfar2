<?php
if ($_POST['btLogin']) {	
	$email= strtolower($_POST['email']);
	$senha = trim(strtolower($_POST['senha']));
	$query = mssql_query("select uid from cadastro where email='$email' and senha='$senha'");
	$found=mssql_num_rows($query);
	$uid = ($found > 0) ? mssql_result($query, 0, 'uid') : false;	
		
	if (!$uid )	
		$msgErr= "Login ou Senha Inválidos - Verifique e tente novamente!";
	else {
		session_start();	// inicializa as variaveis de sessoes			
		$_SESSION['userId'] = $uid;
		$query=mssql_query("update cadastro set ultAcesso=getdate() where uid=$uid");
		}
	}	//************* if ($_POST['btLogin'])
?>
<script language="JavaScript">
	<!--
		function alerta()	{
			if (event.button==1)	{
				alert("ATENÇÃO:\nO link refere-se a foto em alta resolução,\ndisponível somente para download."+'\n\n'+
			 "Use o botão direito do mouse\ne selecione a opção 'Salvar destino como...' !");
			 return (false);
				}
			}
		// -->
	</script>

<div id="mainTop" style="background:url(images/mainDownloads.png) no-repeat; height:24px"></div>
<?
if (!$_SESSION['userId']) {
	echo "<div align=center>
		<p style='margin:0; padding:4px; background:#c40000'>
		<b style='color:#fff'>ACESSO RESTRITO!!! Somente Usuários Cadastrados podem acessar estes arquivos!<p></div>";
	?>
	<div align=center>
		<form name="login" id="login" method="post" action="#" "onload="document.login.email.focus();" onsubmit="return checkForm(this.id);">
			<input type="hidden" name="process" value="0"/>
	
			<table border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td align=center colspan=2 bgcolor="#efefef">
						<p align="center">
							<b>Alguns Links são exclusivos para Associados,<br />
							Alunos, Patrocinadores ou Parceiros.<br>
							Se você faz parte de um desses grupos,<br/>
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
						<input type="submit" name="btLogin" id="btSend" value="Log In"></td></tr>
			</table>
		</form>
	</div><?
	}
else {	?>

	<table border="0" cellpadding="4" cellspacing="4" width="100%">
		<tr>
			<td valign="top">
				<img src="images/seta.jpg" align="absmiddle" border="0">
			</td>
			<td valign="top">
				<b><font color="#008080"><span style="text-transform: uppercase">
					ARQUIVOS PARA DOWNLOADS</span></font></b>
			</td>
		</tr>
		<?				
			$extValida=array('zip','rar','jpg','jpeg','gif','png','xls','doc','pdf','txt','htm','html');
	
			$dh = dir('downloads/');
			$counter=0;
			while ($entry = $dh->read()) {					
				$fname = $entry;
				if (strlen($fname) > 4) {
					$tipo = explode('.', $fname);
					$ext=strtolower($tipo[1]);
					$found=false;
					for($x=0; $x < count($extValida); $x++) {
						if ($ext==$extValida[$x]) $found=true;
						} 
					if ($found) {
						$counter++;
						echo "<tr><td align=center bgcolor=#eeeee4><b>$counter</b>
						</td><td bgcolor=#eeeee4><a href=\"downloads/$fname\" target=_blanck />
						<b><u>$fname</u></b></a></td></tr>";
						}
					}
			}	?>
			</td>
		</tr>
	</table><?
	}
?>
