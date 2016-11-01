<?
session_start();
include '../global.php';
if (!$_SESSION['admUser']) {echo"<script>history.back();</script>";die();}


$_msg=null;	
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";
*/
if ($_POST['cmd']=='del') {
	$_idCadastro = $_POST['idCadastro'];
	$sql = "DELETE FROM cadastro WHERE idCadastro=$_idCadastro";// echo $sql;
	mssql_query($query);
	$_msg = "Registro excluído com sucesso!";
	}
elseif ($_POST['cmd']=='get') {
	$_id = $_POST['idCadastro'];
	$sql="SELECT
		idGrupo,
		idSubgrupo,
		nome,
		cpf,
		rg,
		ender,
		num,
		compl,
		cep,
		bairro,
		cidade,
		uf,
		fone,
		fone2,
		email,
		profissao,
		senha,
		status,  
		idAtividade,
		comentarios,
		nomefantasia,
		CONVERT(CHAR(10),ultAcesso,103) AS updated
		FROM cadastro WHERE (idCadastro = $_id)";
//	$sql = "	SELECT * FROM cadastro WHERE idCadastro=$_id";
	$rs = mssql_fetch_assoc(mssql_query($sql));
	$ret = utf8_encode(join('|',$rs));
	echo sprintf('%06d',$_id).'|'.$ret;
//	echo $ret;
	exit;
	}
elseif ($_POST['cmd']=='save') {
	$_POST = str_replace("'","´",$_POST);
	$_idCadastro = $_POST['idCadastro'];
	$_idGrupo = $_POST['idGrupo'];
	$_idSubGrupo = $_POST['idSubGrupo'];
	$_nome = trim($_POST['nome']);
	$_cpf =ereg_replace("[' '-./ \t]",'',$_POST['cpf']);
	$_rg = trim($_POST['rg']);
	$_ender = trim($_POST['ender']);
	$_num = trim($_POST['num']);
	$_compl = trim($_POST['compl']);
	$_bairro = trim($_POST['bairro']);
	$_cep = trim($_POST['cep']);
	$_cidade = trim($_POST['cidade']);
	$_uf = trim($_POST['uf']);
	$_email= trim($_POST['email']);
	$_profissao = trim($_POST['profissao']);
	$_fone = trim($_POST['fone']);
	$_fone2 = trim($_POST['fone2']);
	$_atividade= trim($_POST['idAtividade']);
	$_fantasia= trim($_POST['fantasia']);
	$_obs= trim($_POST['obs']);
	$_senha = trim($_POST['senha']);
	$_status= $_POST['status'];
	
	
	if (!$_idCadastro) {
		mssql_query("INSERT INTO cadastro(dataReg) values(getDate())
			SELECT @@IDENTITY AS LastID");
		$_idCadastro = mssql_result($query2,0,'LastID');
		if (!$_idCadastro||$_idCadastro==0)
			echo"Erro Gravando novo Registro";
			exit;
		}

	$sql="UPDATE cadastro SET
		idGrupo = $_idGrupo,
		idSubGrupo = $_idSubGrupo,
		idAtividade = $_atividade,
		status= $_status,
		nome='$_nome',
		cpf='$_cpf',
		rg='$_rg',
		ender='$_ender',
		num='$_num',
		compl='$_compl',
		bairro='$_bairro',
		cep='$_cep',
		cidade='$_cidade',
		uf=upper('$_uf'),
		email=lower('$_email'),
		profissao='$_profissao',
		fone='$_fone',
		fone2='$_fone2',		
		nomeFantasia = '$_fantasia',
		comentarios = '$_obs',
		senha = '$_senha',		
		ultAcesso=getdate()
			WHERE idCadastro = $_idCadastro"; //	echo "sql=$sql";	
	mssql_query($sql);
	if (mssql_rows_affected($conn) > 0)
		$_msg = "Registro atualizado com sucesso!";
	else {
		$_msg = "Erro atualizando registro!\n\n";
		exit;
		}
	}



$_cat = ($_POST['categoria'])?$_POST['categoria']:1;
$_key = (isset($_POST['key']))?$_POST['key']:null;
$sql="SELECT idCadastro, nome, cpf, status, CONVERT(CHAR(10), dataReg, 103) AS data FROM cadastro ";
if ($_key)	{
	$_cpf = ereg_replace("[' '-./ \t]",'',$_key);
	if ($_cpf)
		$sql .="WHERE nome LIKE '%$_key%' OR cpf='$_cpf'";
	}
else
	$sql .= "WHERE idGrupo=$_cat";	

$sql .= " ORDER BY nome";
?>
<style>
	*{font:normal 9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana}
	#tbList {width:100%;background:#fff}
	#tbList td, #busca td{padding:2px 4px;border-bottom:1px solid #eee;}
	#tbList th, #busca th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:20px}
	#tbList a{font-weight:bolder;color:#f00;text-decoration:none}
	#tbList .newReg{cursor:pointer}
	#tbList .label{text-align:center;background:#369;color:#fff;font-size:9pt}
	
	select option{padding-left:10px}
	
	#busca .label{text-align:right;background:#369;color:#fff;font-size:9pt;padding:0 4px}	
	#busca #btSearch{cursor:pointer}
	#busca th p{text-align:center;font-weight:bolder}
	#busca th img{text-align:right;}
	
	#dvLayer {position:absolute;top:35px;left:100px;width:470px;height:400px;z-index:1000;background:#fff;display:none}
	#tbForm td{background:#369}
	#tbForm th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:22px}
	#tbForm .label{text-align:right;background:#369;color:#fff;font-size:9pt;}
	#tbForm .text{background:#fff;border:solid 1px #369}
	#tbForm #btSend{cursor:pointer}
</style>

<script language="JavaScript" type="text/javascript" src="../js/jquery.maskedinput-1.2.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>

<script language="JavaScript">
	$(document).ready(function() {
		var originColor='#fff';
		var alertColor='#FADEC5'
		$('.trLink').mouseover(function() {
			$('.trLink').each(function() {
				$(this).css('background','#fff');
				})
			$(this).css('background','#CBCFF6');	
			})
		
		if ($('#msg').val()!='') {
			alert($('#msg').val());
			}
		
		var $myForm = $('#myForm');
		var $categoria = $('#categoria');
		var $key = $('#key');
		var $btSearch = $('#btSearch');	
		$categoria.change(function() {
			$loading.css('display','block');
			if ($categoria.val()!='') {
				$('#key').val('');				
				$myForm.submit();
				}
			})
		$btSearch.click(function() {
			if ($key.val()!='') $categoria.val('');
			$loading.css('display','block');
			$myForm.submit();
			})
				
		
		var $idCadstro = $('#idCadastro');
		var $cmd = $('#cmd');
		var $msg = $('#msg');		
		var $myForm2 = $('#myForm2');
		var $dvLayer = $('#dvLayer');
		$dvLayer.css('left',(screen.width-750)/2+'px');
		var $btSend = $('#btSend');

		
		$('.text').focus(function() {
			$('.text').each(function() {
				$(this).css('background',originColor);
				})
			$(this).css('background',alertColor);
			})

		$('.text').keyup(function() {
			if (this.id=='email')
				$(this).val($(this).val().toLowerCase());
			else if (this.id!='obs')
				$(this).val($(this).val().toUpperCase());
			})

		var $idCadastro = $('#idCadastro');
		var $idCad = $('#idCad');
		var $idGrupo = $('#idGrupo');
		var $idSubGrupo = $('#idSubGrupo');
		var $idAtividade = $('#idAtividade');
		var $nome = $('#nome');
		var $cpf = $('#cpf');
		var $rg = $('#rg');
		var $ender = $('#ender');
		var $num = $('#num');
		var $compl = $('#compl');
		var $cep = $('#cep');		
		var $bairro = $('#bairro');
		var $cidade = $('#cidade');
		var $uf = $('#uf');
		var $pais = $('#pais');
		var $fone1 = $('#fone');
		var $fone2 = $('#fone2');
		var $email = $('#email');
		var $profissao = $('#profissao');
		var $senha = $('#senha');
		var $status = $('#status');
		var $dataReg = $('#dataReg');
		var $updated = $('#updated');
		var $obs = $('#obs');
		var $fantasia = $('#fantasia');

		$('.edit').click(function() {
			var t = this.id.split(':');			
			$idCadastro.val(t[1]);
			$cmd.val('get');
			$.ajax({			
				type: 'POST',
				url: 'admListCadastros.php',
				data: $myForm2.serialize(),
					success: function(msg){
						var ret = msg.split('|');
						$idCad.val(ret[0]);
						$idGrupo.val(ret[1]);
						$idSubGrupo.val(ret[2]);
						$nome.val(ret[3]);
						$cpf.val(ret[4].formatCPF());
						$rg.val(ret[5]);
						$ender.val(ret[6]);
						$num.val(ret[7]);
						$compl.val(ret[8]);
						$cep.val(ret[9]);
						$bairro.val(ret[10]);
						$cidade.val(ret[11]);
						$uf.val(ret[12]);
						$fone1.val(ret[13]);
						$fone2.val(ret[14]);
						$email.val(ret[15]);
						$profissao.val(ret[16]);						
						$senha.val(ret[17]);
						$status.val(ret[18]);
						$updated.val(ret[22]);						
						$idAtividade.val(ret[19]);
						$obs.val(ret[20]);
						$fantasia.val(ret[21]);
						
						$dvLayer.toggle('slow');
						}
				});
			})		

		$('#icClose').click(function() {
			$dvLayer.toggle('slow');
			})

		$btSend.click(function() {
			if ($nome.val()=='') {
				alert('Preencha o campo \'Nome\'!');
				$nome.focus();
				return false;
				}
			else if ($cpf.val()=='') {
				alert('Preencha o campo \'CPF/CNPJ\'!');
				$cpf.focus();
				return false;
				}
			else if (!$cpf.val().validCPF()) {
				alert('Preencha corretamente o campo \'CPF/CNPJ\'!');
				$cpf.focus();
				return false;
				}
			else if ($rg.val()=='') {
				alert('Preencha o campo \'RG/IE\' !');
				$rg.focus();
				return false;
				}
			else if ($ender.val()=='') {
				alert('Preencha o campo \'Endereço\'!');
				$ender.focus();
				return false;
				}
			else if ($num.val()=='') {
				alert('Preencha o campo \'Número\'!');
				$num.focus();
				return false;
				}
			else if ($bairro.val()=='') {
				alert('Preencha o campo \'Bairro\'!');
				$bairro.focus();
				return false;
				}
			else if ($cep.val()=='') {
				alert('Preencha o campo \'CEP\'!');
				$cep.focus();
				return false;
				}
			else if ($cidade.val()=='') {
				alert('Preencha o campo \'Cidade\'!');
				$cidade.focus();
				return false;
				}
			else if ($uf.val()=='') {
				alert('Preencha o campo \'UF\'!');
				$uf.focus();
				return false;
				}
			else if ($email.val()=='') {
				alert('Preencha o email do usuário!');
				$email.focus();
				return false;
				}
			else if (!$email.val().validEmail()) {
				alert('Preencha corretamente o campo \'E-mail\'!');
				$email.focus();
				return false;
				}
			else if ($profissao.val()=='') {
				alert('Preencha corretamente o campo \'Profissão\'!');
				$profissao.focus();
				return false;
				}
			else if ($fone1.val()=='') {
				alert('Preencha corretamente o campo \'Fone #1\'!');
				$fone1.focus();
				return false;
				}
			else if ($fone2.val()=='') {
				alert('Preencha corretamente o campo \'Fone #2\'!');
				$fone2.focus();
				return false;
				}
			else if ($idGrupo.val()=='') {
				alert('Selecione o campo \'Grupo\'!');
				$idGrupo.focus();
				return false;
				}
			else if ($idSubGrupo.val()=='') {
				alert('Selecione o campo \'Sub-Grupo\'!');
				$idSubGrupo.focus();
				return false;
				}
			else if ($idAtividade.val()=='') {
				alert('Selecione o campo \'Atividade\'!');
				$idAtividade.focus();
				return false;
				}
			else if ($senha.val()=='') {
				alert('Digite o senha do usuário!');
				$senha.focus();
				return false;
				}
			$cmd.val('save');
			$myForm2.submit();
			})

		$('.del').click(function() {
			if(!confirm('Tem certeza que deseja excluir este registro?\n\nUma vez excluído não tem como recuperá-lo.'))
				return false;
			var t = this.id.split(':');
			$cmd.val('del');
			$idCadastro.val(t[1]);
			$myForm2.submit();
			})

		})
</script>

<form  name="myForm" id="myForm" method="POST" action="" style="width:100%; margin:0">
	<table id="busca" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td>Selecione o grupo: 
				<select name="categoria" id="categoria" size="1">
					<option value="">Selecione</option><?
					$qg=mssql_query("SELECT * FROM grupo ORDER BY nome");
					while ($rs=mssql_fetch_object($qg)) {
						$select = ($_POST['categoria']==$rs->idGrupo)?' selected':'';
						echo"<option $select value=$rs->idGrupo>$rs->nome</option>";
						}	?>
				</select>
			</td>
			<td class="label">ou digite Nome ou parte ou CPF</td>
			<td><input type="text" size="40" name="key" id="key" value="<?=$_POST['key']?>"></td>
			<td align="center"><input type="button" name="btSearch" id="btSearch" value=" OK "></td>
		</tr>
	</table>
</form>
<?	$query=mssql_query($sql);
	$rows = mssql_num_rows($query);	
?>

<table id="tbList" border="0" cellspacing="2" cellpadding="2">
	 <tr>
		  <th colspan="6">Os Cadastros são mostrados em ordem alfabética de nomes. Clique no ID para alterar</td>
	 </tr>
	 <tr>
		  <td class="label">UID</td>
		  <td class="label">Nome/Empresa</td>
		  <td class="label">CPF/CNPJ</td>
		  <td class="label">Status</td>
		  <td class="label">Criado em</td>
		  <td class="label">Excluir</td>
	 </tr><?
	if ($rows==0)	{	?> 
		<tr>
			<td colspan="5" align="center"><b style="color:#c40000">Nenhum registro localizado para <?=($_cpf)?$_cpf:$_procura?>!</b></td>
		</tr><?
		}
	else	{
	while ($rs=mssql_fetch_object($query)) {
		if ($rs->status == -1)
			$status="EXCLUÍDO";
		elseif (!$rs->status||$rs->status==0)
			$status="INATIVO";
		else
			$status="ATIVO";
		$_cpf = ereg_replace("[' '-./ \t]",'',$rs->cpf); 
		?>			
		<tr class="trLink" align="center">
			<td><a href="#" class="edit" id="edit:<?=$rs->idCadastro?>"><?=sprintf('%06d',$rs->idCadastro)?></a></td>
			<td align="left"><?=$rs->nome?></td>
			<td><?=formatCPF($_cpf)?></td>					
			<td><?=$status?></td>
			<td><?=$rs->data?></td>
			<td><a href="#" class="del" id="del:<?=$rs->idCadastro?>"><img src="images/excluir.gif" border="0" style="cursor:pointer"/></td>
		</tr><?
		}
	}	
?>
</table>

<div id="dvLayer">
	<fieldset>
		<form name="myForm2" id="myForm2" method="POST" action="" style="margin:0">
			<input type="hidden" name="cmd" id="cmd" value=""/>	
			<input type="hidden" name="idCadastro" id="idCadastro" value=""/>
			<input type="hidden" id="msg" value="<?=$_msg?>"/>
			<table id="tbForm" border="0" cellspacing="1" cellpadding="1" align="center"  width="100%">
				<tr>
					<th colspan="2">
						<p style="text-align:center">
							<img id="icClose" src="images/closeLayer.gif" border="0"
								style="cursor:pointer" alt="Fechar" title="Fechar" align="right"/>
							<b>Cadastro</b>
						</p>
					</th>
				</tr>
				<tr>
					<td class="label">ID:</td>
					<td>
						<div style="float:left">
							<input disabled id="idCad" style="width:60px" value=""/>
						</div>
						<div style="float:right">
							<span class="label">Grupo:</span>
							<select class="text" name="idGrupo" id="idGrupo" size="1" style="width:90px">
								<option value="0">Selecione</option><?
								$qg2=mssql_query("SELECT * FROM grupo ORDER BY nome");
								while ($rs=mssql_fetch_object($qg2)) {
									echo"<option value=$rs->idGrupo>$rs->nome</option>";
									}	?>
							</select>
							&nbsp;&nbsp;
							<span class="label">Sub-Grupo:</span>
							<select class="text" name="idSubGrupo" id="idSubGrupo" size="1" style="width:110px">
								<option value="0">Selecione</option><?
								$qg3=mssql_query("SELECT * FROM subGrupo ORDER BY nome");
								while ($rs=mssql_fetch_object($qg3)) {
									echo"<option value=$rs->IdSubGrupo>$rs->nome</option>";
									}	?>
							</select>
						</div>
						</div>
					</td>
				</tr>

				<tr>
					<td class="label">Nome:</td>
					<td><input class="text" name="nome" id="nome" style="width:395px" maxLength="60" value=""/></td>
				<tr>
					<td class="label">CPF/CNPJ:</td>
					<td>
						<div style="float:left">
							<input class="text" name="cpf" id="cpf" style="text-align:center"/>
						</div>
						<div style="float:right">
							<span class="label">RG/IE:</span>
							<input class="text" name="rg" id="rg" style="text-align:center;width:130px" value=""/>
						</div>
					</td>
				</tr>

				<tr>
					<td class="label">Endereço:</td>
					<td>
						<div style="float:left">
							<input class="text" name="ender" id="ender" style="width:328px" maxLength="100" value=""/>
						</div>
						<div style="float:right">
							<span class="label">&nbsp;Nº:</span>
							<input  class="text" name="num" id="num" size=2 style="text-align:center" value=""/>
						</div>
					</td>
				</tr>

				<tr>
					<td class="label">Compl.:</td>
					<td>
						<div style="float:left">
							 <input class="text" name="compl" id="compl" style="width:60px" maxLength="30" value=""/>
							</div>
						<div style="float:left;margin-left:10px">					 
						 <span class="label">&nbsp;Bairro:</span> 
						 	<input class="text" name="bairro" id="bairro" style="width:180px" maxLength="50" value=""/></span>
						</div>
						<div style="float:right">
					 		<span class="label">CEP:</span>
							<input class="text" name="cep" id="cep" style="width:66px;text-align:center;padding:0" maxLength="9" value=""/>
						</div>
					</td>
				</tr>

				<tr>
					<td class="label">Cidade:</td>
					<td>
						<div style="float:left">
							<input class="text" name="cidade" id="cidade" style="width:227px" maxLength="50" value=""/>
						</div>
						<div style="float:right">
							<span class="label">UF:</span> 
							<input class="text" name="uf" id="uf" maxLength="2" style="width:30px; text-align:center;padding:0" value=""/>
							<span class="label">País:</span>
							<input disabled name="país" id="país" style="width:66px" value="BRASIL"/>
						</div>
					</td>
				</tr>
	
				<tr>
					<td class="label">E-mail:</td>
					<td>
						<div style="float:left">
							<input class="text" name="email" id="email" style="width:227px" maxLength="60" value=""/>
						</div>
						<div style="float:right">
							<span class="label">Prof:</span>
							<input class="text" name="profissao" id="profissao" style="width:129px" maxLength="40" value=""/>
						</div>
					</td>
				<tr>	
					<td class="label">DDD/Tel 1:</td>
					<td>
						<div style="float:left">
							<input class="text" name="fone" id="fone" style="width:183px" maxLength="30" value=""/>
						</div>
						<div style="float:right">
							<span class="label" style="margin-left:11px">DDD/Tel 2:</span>
							<input class="text" name="fone2" id="fone2" style="width:130px" maxLength="30" value=""/>
						</div>
					</td>
				</tr>	
				<tr>
					<td class="label">Atividade:</td>
					<td>
						<select class="text" name="idAtividade" id="idAtividade" size="1">
							<option value="0">Selecione</option><?
							$qg4=mssql_query("SELECT * FROM atividade ORDER BY nome");
							while ($rs=mssql_fetch_object($qg4)) {
								echo"<option value='$rs->IdAtividade'>$rs->Nome</option>";
								}	?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="label">Fantasia:</td>
					<td><input type="text" class="text" name="fantasia" id="fantasia" style="width:395px" maxlenght="60" value=""/></td>
				</tr>
				<tr>
					<td class="label">Obs.:</td>
					<td><textarea class="text" name="obs" id="obs" style="width:395px;height:80px" maxlenght="200"></textarea></td>
				</tr>
				<tr>
					<td class="label">Senha:</td>
					<td>
						<div style="float:left">
							<input name="senha" id="senha" style="text-align:center; width:80px" value=""/>
						</div>
						<div style="float:right">
							<span class="label">Status:</span>
							<select class="text" name="status" id="status" size="1">
								<option value="-1">Cancelado</option>
								<option value="0">Inativo</option>
								<option value="1">Ativo</option>
							</select>
							&nbsp;&nbsp;
							<span class="label">Atualizado em:</span>
							<input disabled id="updated" style="text-align:center; width:80px" value=""/>
						</div>	
					</td>
				</tr>
				<tr>
					<th colspan=2 align=center><input type="button" name="btSend" id="btSend"" value="Enviar"/></th>
				</tr>
			</table>
		</form>
	</fieldset>
</div>			
