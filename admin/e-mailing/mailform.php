<?php
/*
 * e-Mailing - Sistema automatizado para criação e envio de emails por lote
 * Copyright (C) 2007 Lauro A. L. Brito
	 *
	 * == BEGIN LICENSE ==
	 *
	 * Licensed under the terms of any of the following licenses at your
	 * choice:
	 *
	 *  - GNU General Public License Version 2 or later (the "GPL")
	 *    http://www.gnu.org/licenses/gpl.html
	 *
	 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
	 *    http://www.gnu.org/licenses/lgpl.html
	 *
	 *  - Mozilla Public License Version 1.1 or later (the "MPL")
	 *    http://www.mozilla.org/MPL/MPL-1.1.html
	 *
	 * == END LICENSE ==
	 *
	 * Popup para busca, edição e inclusão de emails na tabela mailing
	 * permite o bloqueio/desbloqueio de emails utilizado pelo responsável no retorno de
	 * e-mails inexistentes ou para remoção.
	 * O bloqueio se faz por meio do campo status onde:
	 * -1 - descartado na query de busca e portanto não é enviado, porém permance na base
	 *		  de dados para que não seja incluso numa segunda vez
	 * 0 - e-mail novo nunca foi enviado
	 * 1 - primeiro envio
	 * n - n envios
 */
 
 
if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');

include 'config/config.php';
session_start();

$conn = mysql_connect($host, $userDB, $pwdDB);
mysql_select_db($dataBase);
	
$id=isset($_GET['id']) ? $_GET['id'] : $id=0;

if ($_GET['search']) {
	$key = trim($_GET['search']);
	$query=mysql_query("select id from mailing where email='$key' or nome like '%$key%'");
	
	if (mysql_numrows($query) > 0)
		$id= mysql_result($query, 0, 'id');
	else
		$message = "<p class='erro'>E-mail não localizado !</p>";
	}

if ($_POST['submit']) {
	// formulario foi submetido entao prepara o e-mail para envio
	$id = isset($_POST['id']) ? $_POST['id'] : 0;
	$email = trim(strtolower($_POST['email']));
	$nome = $_POST['nome'];
	$status = $_POST['status'];
	$categ=$_POST['cbCateg'];
		
	if ($id==0) {
		$query = mysql_query("select max(id) as max_id from mailing");
		if ($query) {		
			$result = mysql_fetch_array($query);	
			$id = $result["max_id"] + 1;
			}
		
		$sql="insert into mailing(id, nome, email, categ, dataReg) VALUES ($id,'$nome', '$email', $categ, now())";
		$msgOk="<p class='ok'>Registro gravado com sucesso</p>";
		$msgErr="<p class='erro'>E-mail ja cadastrado anteriormente !</p>";
		}

	else	{
		$sql="update mailing set email='$email', nome='$nome', status=$status, categ=$categ where id=$id";
		$msgOk="<p class='ok'>Registro alterado com sucesso</p>";
		$msgErr="<p class='erro'>Registro não pôde ser alterado - Duplicidade de e-mail!</p>";
		}
		
		
	$query=mysql_query($sql, $conn);
	if ($query) {
		echo"<script>opener.location.href='mgrList.php?ordem={$_SESSION['ordem']}&dir={$_SESSION['dir']}'</script>";
		$message = $msgOk;			
		}
	else
		$message = $msgErr;
	}
// echo "sql=$sql";	

if ($id > 0) {
// foi passado um id entao entra em edição
	$sql = "select mailing.*, date_format(dataReg, '%d/%m/%Y %H:%i:%s') as criado from mailing where id=$id";
	$query=mysql_query($sql);
	$result = mysql_fetch_array($query);
	
	$email=$result['email'];
	$nome=$result['nome'];
	$arquivo=$result['arquivo'];
	$dataReg=$result['criado'];
	$dataEnvio=$result['dataEnvio'];
	$categ=$result['categ'];
	$status=$result['status'];
	
	}

?>
<html>
	<head>
		<title>E-mailing | Search/Insert/Edit</title>
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
	</head>

	<body style="background:#cccccc" onload="resizeTo(450,340); document.mailing.nome.focus();">

	<!-- entrada do formulario -->
	<script language="JavaScript">

	<!--//

		function Validator(theForm) {

			var invalid;
			invalid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

			if (invalid.test(document.mailing.email.value) == false) {
				document.mailing.email.style.color = "red";
				alert("Endereço de E-mail inválido !");
				theForm.email.focus();
				return (false); }

		/*
			if (theForm.nome.value == "") {
				alert("Informe o nome do usuário !");
				theForm.nome.focus();
				return (false); }	
				
			theForm.nome.value=theForm.nome.value.toUpperCase();
		*/

		  return true;
		  }
		  
		 function search() {
		 	var valid;
			valid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
			
		 	email=document.mailing.email;
		 	nome=document.mailing.nome;
		 	if (nome.value =='' && email.value=='') {
		 		alert("Digite o nome ou email para busca!");
		 		nome.focus();
		 		return false;
		 		}
		 		
		 	if (nome.value != '') key=nome.value;
		 		
		 	else {
				if (!valid.test(email.value)) {
					email.style.color = "red";
					alert("Endereço de E-mail inválido !");
					email.focus();
					return (false);
					}
				key=email.value;
				}
				
			document.location.href='mailform.php?search='+key;
			}

	// -->
	</script>		
	
	<table border="0" cellpadding="1" cellpadding="1" width="100%" align="center">
		<tr>
			<td bgcolor="#000000" width="60" border="0" width="60" rowspan="2">
				<img src="images/logoTopo.gif" width=60></td>
			<td class="page" style="border:4px groove #efefef">Cadastro no Mailing</td>
		</tr>
		<tr>
			<td align"center" align="center">
				Para busca digite o nome ou parte ou o email completo!</td>
		</tr>
		<tr>
			<td colspan="4" align="center"><?= $message ?></td>
		</tr>
	</table>
	
	<table border="0" id="mailForm" bgcolor="#ffffff" cellSpacing="2" cellpadding="2">			
		<form action="mailform.php" method="POST" name="mailing" onsubmit="return Validator(this)">
			<input type="hidden" name="id" value="<?= $id ?>">
			
			<tr>
				<td class=label>Nome:</td>
				<td colspan="3">
					<input type="text" style="width:350px" maxlength="50" name="nome" value="<?= $nome ?>" class="tInput">
				</td>
			</tr>

			<tr>
				<td class=label>E-Mail:</td>
				<td colspan="3">
					<input type="text" style="width:350px" maxlength="100" name="email" value="<?= $email ?>" class="tInput">
				</td>
			</tr>
			
				
			<tr>
				<td class=label>Criado:</td>
				<td>
					<input style="text-align:center" value="<?= $dataReg ?>" disabled class="data">
				</td>
				<td class=label nowrap>Ultimo Envio:</td>
				<td>
					<input style="text-align:center" value="<?= $dataEnvio ?>" disabled class="data">
				</td>
			</tr>

			<tr>
				<td class=label>Arquivo:</td>
				<td colspan="3"><b>&nbsp;<?= $arquivo ?></b></td></td>
			</tr>
				
			<tr>
				<td class=label>Enviado:</td>
				<td colspan="3">
				<?
					if ($status==-1) echo "$status: Bloqueado  <span style='color:red; font-weight:bolder'>(Para os próximos envios)</span>";
					elseif ($status==0) echo "$status: Novo <span style='color:orange; font-weight:bolder'>Nenhum Envio</b></font>";
					else echo "<span style='color:green; font-weight:bolder'>Enviado(s)</span> $status X";	?>
				</td>
			</tr>

			<tr>
				<td class=label>Categoria:</td>
				<td><?
					$query=mysql_query("select * from mailing_categ order by categ");						
					echo"
						<select name='cbCateg' style='font-size:8pt; background:#fff'>
						<option value='0'>TODAS</option>";
						
						while ($rsQuery=mysql_fetch_array($query)) {
							if ($categ==$rsQuery['id'])
								echo "<option value={$rsQuery['id']} selected />{$rsQuery['categ']}";
							else
								echo "<option value={$rsQuery['id']} />{$rsQuery['categ']}";
							}	?>
					</select>
				<td class=label>Status:
				<td>
					<select name='status' style='font-size:8pt; background:#fff'>
						<option value=-1 <? if ($status==-1) echo " selected" ?> />Bloqueado</option>
						<option value=<? if ($status < 0) echo "0"; else echo $status ?><? if ($status >= 0) echo " selected" ?> />Ativado</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input class="but" type="submit" name="submit" value="Gravar">&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="but" type="button" name="buscar" value="Buscar" onclick="search()">
					<input class="but" type="button" name="novo" value="Limpar" onclick="document.location.href='mailform.php'">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
<?
	mysql_close($conn);
	@mysql_free_result($query);
?>