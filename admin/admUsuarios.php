<?
session_start();
include '../global.php';
if ($_SESSION['admNivel']!=5) die("Acesso não permitido");
	
$_id = ($_POST['id'])?$_POST['id']:0;
$_msg = null;

if ($_POST['cmd']=='del') 
	mssql_query("DELETE FROM usuarios WHERE id=$_id");

elseif ($_POST['cmd']=='get') { 
	$rs = mssql_fetch_object(mssql_query("SELECT * FROM usuarios WHERE id=$_id"));
	$_senha = $rs->senha;
	if ($rs->password)
		$_pwd = convert_uudecode(base64_decode($rs->password));
	echo utf8_encode("$rs->nome:$rs->email:$rs->cargo:$_senha:$rs->nivel:$rs->status:$_pwd");
	exit;
	}	
	
elseif ($_POST['cmd']=='save') {
	$_POST = str_replace("'","´",$_POST);
	$_nome = $_POST['nome'];
	$_cargo = $_POST['cargo'];
	$_senha = base64_encode(convert_uuencode(trim($_POST['senha'])));
	$_nivel = $_POST['nivel'];
	$_email = $_POST['email'];
	$_status = $_POST['status'];

	if ($_id==0) {
		$sql = "INSERT INTO usuarios(nome, email, cargo, password, nivel, status, dataCad) VALUES(
			'$_nome', '$_email', '$_cargo', '$_senha', $_nivel, $_status, getDate())";
		@mssql_query($sql);
		if (mssql_rows_affected($conn) > 0)
			$_msg = "Novo registro inserido com sucesso!";
		else
			$_msg = "Não é permitido registro em duplicidade!";
		}
	else {
		$sql = "UPDATE usuarios SET nome='$_nome', email='$_email', cargo='$_cargo', nivel=$_nivel, password='$_senha', status=$_status, atualizado=getDate() WHERE id=$_id";
		mssql_query($sql);
		if (mssql_rows_affected($conn) > 0)
			$_msg = "Registro atualizado com sucesso!";
		else
			$_msg = "Erro na atualização do Registro\n\nNão é permitido registro em duplicidade!";
		}
//	echo $sql;
	}
?>
<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>
<script language="JavaScript">
	$(document).ready(function() {
		var originColor='#fff';
		var alertColor='#FADEC5'
		var $id = $('#id');
		var $cmd = $('#cmd');
		var $msg = $('#msg');
		var $myForm = $('#myForm');
		var $dvLayer = $('#dvLayer');
		var $nome = $('#nome');
		var $email = $('#email');
		var $cargo = $('#cargo');
		var $nivel = $('#nivel');
		var $senha = $('#senha');
		var $status = $('#status');
		var $btSend = $('#btSend');
		
		$dvLayer.css('left',(screen.width-750)/2+'px');
		
		$('.text').focus(function() {
			$('.text').each(function() {
				$(this).css('background',originColor);
				})
			$(this).css('background',alertColor);
			})

		$('.text').keyup(function() {
			if (this.id=='email')
				$(this).val($(this).val().toLowerCase());
			else
				$(this).val($(this).val().toUpperCase());
			})

		$('.edit').click(function() {
			var t = this.id.split(':');			
			$id.val(t[1]);
			$cmd.val('get');
			$.ajax({			
				type: 'POST',
				url: 'admUsuarios.php',
				data: $myForm.serialize(),
					success: function(msg){
						var ret = msg.split(':');
						$nome.val(ret[0].trim());
						$email.val(ret[1].trim());
						$cargo.val(ret[2].trim()); 
						$senha.val(ret[3].trim());
						$nivel.val(ret[4]);
						$status.val(ret[5]);
						$dvLayer.toggle('slow');
						}
				});
			})		

		$btSend.click(function() {
			if ($nome.val()=='') {
				alert('Digite o nome do usuário!');
				$nome.focus();
				return false;
				}
			else if ($email.val()=='') {
				alert('Digite o email do usuário!');
				$email.focus();
				return false;
				}
			else if (!$email.val().validEmail()) {
				alert('Digite corretamente o email do usuário!');
				$email.focus();
				return false;
				}
			else if ($cargo.val()=='') {
				alert('Digite o cargo do usuário!');
				$cargo.focus();
				return false;
				}
			else if ($senha.val()=='') {
				alert('Digite o senha do usuário!');
				$senha.focus();
				return false;
				}
			$cmd.val('save');
			$myForm.submit();
			})
		
		$('.newReg').click(function() {
			$id.val('0');
			$('.text').each(function() {
				$(this).val('');
				})
			$dvLayer.css('display','block');
			})
			
		$('#icClose').click(function() {
			$dvLayer.toggle('slow');
			})

		$('.del').click(function() {
			if(!confirm('Tem certeza que deseja excluir este registro?\n\nUma vez excluído não tem como recuperá-lo.'))
				return false;
			var t = this.id.split(':');
			$cmd.val('del');
			$id.val(t[1]);
			$myForm.submit();
			})

		$('.trLink').mouseover(function() {
			$('.trLink').each(function() {
				$(this).css('background','#fff');
				})
			$(this).css('background','#CBCFF6');	
			})
		
		if ($msg.val()!='') {
			alert($msg.val());
			}
		})
</script>

<style>
	*{font:normal 9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana}
	#tbUsuarios{width:100%;background:#fff}
	#tbUsuarios td{border:solid 1px silver;padding:2px 4px}
	tbUsuarios img{cursor:pointer}
	#tbUsuarios th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	#tbUsuarios .label{text-align:center;background:#369;color:#fff;font-size:9pt}
	#tbUsuarios .newReg{cursor:pointer}
	#tbUsuarios a{font-weight:bolder;color:#f00;text-decoration:none}
	#tbUsuarios a:hover{text-decoration:underline}
	#dvLayer {position:absolute;top:80px;left:100px;width:350px;height:200px;z-index:1000;background:#fff;display:none}

	#formUsuario th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	#formUsuario .label{text-align:right;background:#369;color:#fff;font-size:9pt}
	#formUsuario .text{background:#fff;border:solid 1px #369}
	#formUsuario #data{text-align:center;width:80px;border:solid 1px #369}
	#formUsuario #titulo{width:400px;border:solid 1px #369}
	#formUsuario #descricao{width:400px;height:60px;border:solid 1px #369}	
	#formUsuario #status{font-size:9pt;border:solid 1px #369}
	select option{padding-left:10px}
	#formUsuario #btSend, #btCancel{cursor:pointer}
	#formUsuario th p{text-align:center;font-weight:bolder}
	#formUsuario th img{text-align:right;}
</style>
	
<table id="tbUsuarios" border="0" cellpadding="1" cellpadding="1">
	<tr>
		<th colspan="6" align="center">
			Ordem decrescente por data. Use a barra de navegação abaixo para outras páginas ou clique no ID para Visualizar/Alterar a notícia.
		</th>
		<th><input type="button" class="newReg" value="Incluir"/></th>
	</tr>
	<tr>
		 <td class="label">ID</td>
		 <td class="label">Nome</td>
		 <td class="label">Email</td>
		 <td class="label">Cargo</td>
		 <td class="label">Nível</td>
		 <td class="label">Status</td>
		 <td class="label">Excluir</td>
 	</tr><?
	$sql="SELECT u.*, n.nivel FROM usuarios u, nivelSenha n WHERE u.nivel=n.id ORDER BY u.nome";	
	$query=mssql_query($sql);
	while ($rs=mssql_fetch_object($query)) {
		$status = ($rs->status==0)?'INATIVO':'ATIVO';	?>
		
		<tr class="trLink">
			<td align="center"><a href="#" class="edit" id="edit:<?=$rs->id?>"><?=sprintf('%04d',$rs->id)?></a></td>
			<td><?=$rs->nome?></td>
			<td><?=$rs->email?></td>
			<td><?=$rs->cargo?></td>
			<td align=center><?=$rs->nivel?></td>
			<td align=center><?=$status?></td>		
			<td align="center"><a href="#" class="del" id="del:<?=$rs->id?>">
				<img src="images/excluir.gif" border="0" alt="Excluir Usuário" alt="Excluir Usuário"/></a>
			</td>
		</tr><?
		}	?>
	<tr>
		<th colspan="6">Clique no botão ao lado para inserir novo registro.</th>
		<th align="center"><input type="button" class="newReg" value="Incluir"/></th>
	 </tr>
</table>	


<div id="dvLayer">
	<fieldset style="padding:8px">
		<form name="myForm" id="myForm" method="POST" action="">
			<input type="hidden" name="cmd" id="cmd" value=""/>	
			<input type="hidden" name="id" id="id" value=""/>
			<input type="hidden" id="msg" value="<?=$_msg?>"/>
	
			<table id="formUsuario" border="0" cellpadding="1" cellpadding="1">
				<tr>
					<th colspan="3">
						<p style="text-align:center">
							<img id="icClose" src="images/closeLayer.gif" border="0"
								style="cursor:pointer" alt="Fechar" title="Fechar" align="right"/>
							<b>Cadastro de Usuários</b>
						</p>
					</th>
				</tr>
				<tr>
					<td class="label">Nome:</td>
					<td colspan="2"><input class="text" type="text" id="nome" name="nome" size="50" maxlength="60" value=""/></td>						
				</tr>
				<tr>
					<td class="label">Email:</td>
					<td colspan="2"><input class="text" type="text" id="email" name="email" size="50" maxlength="100" value=""/>
				</tr>
				<tr>
					<td class="label">Cargo:</td>
					<td><input type="text" class="text" name="cargo" id="cargo" maxlength="50" value=""/></td>
					<td rowspan="4"><img src="images/login.gif">
				</tr>
				<tr>
					<td class="label" valign="top">Senha:</td>
					<td><input class="text" type="password" name="senha" id="senha" maxlength="20"value="">
				</tr>
				<tr>
					<td class="label" valign="top">nivel:</td>
					<td>
						<select class="combo" name="nivel" id="nivel" size="1"><?
						$query=mssql_query("SELECT * FROM nivelSenha ORDER BY id");
						while ($rs2=mssql_fetch_object($query)) {
							echo "<option value='$rs2->id'>$rs2->nivel</option>";
							}	?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label" valign="top">Status:</td>
					<td>
						<select class="combo" name="status" id="status" size="1">
							<option value="1">Ativo</option>
							<option value="0">Inativo</option>
						</select>
					</td>
				</tr>
				<tr height="30">
					<th colspan="3"><input type="button" name="btSend" id="btSend" value="Enviar"/></th>
				</tr>			
			</table>
		</form>
	</fieldset>
</div>
