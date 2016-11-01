<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';

if (!$_SESSION['admUser']) {echo"<script>history.back();</script>";die();}
	

if ($_POST['cmd']=='del') {
	$_idAlunoCursos = $_POST['idAlunoCursos'];
	$sql = "DELETE FROM alunoCursos WHERE idAlunoCursos=$_idAlunoCursos"; echo $sql;
//	mssql_query($query); 
	$_msg = "Registro excluído com sucesso!";
	}


//debug($_POST);
$_idCurso=($_POST['idCurso'])?$_POST['idCurso']:0;
$_ordem = $_POST['ordem'];
$rows = 0;

if ($_idCurso > 0)
	$sql="SELECT c.idCadastro, c.nome, c.cpf, i.idCurso, CONVERT(CHAR(10), i.data, 103) AS data
		FROM alunosCursos AS i INNER JOIN 
			cadastro AS c ON i.idAluno = c.idCadastro
				WHERE (i.idCurso = $_idCurso)
					ORDER BY c.nome";
else
	$sql="";

if ($sql !='') {
	$query = mssql_query($sql);
	$rows=mssql_num_rows($query);
	}
//echo $sql;
?>
<style>
	#btSearch{cursor:pointer;padding:0}
	#myForm th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:22px;font-size:10pt}
	#tbForm td{background:#369}
	#tbForm th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:22px}
	#tbForm .label{text-align:right;background:#369;color:#fff;font-size:9pt;}
	#tbForm .text{background:#fff;border:solid 1px #369}
</style>

<script language="JavaScript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script language="JavaScript" type="text/javascript" src="lib/jquery.tablesorter.js"></script>

<script language="JavaScript">
	$(document).ready(function() {		
		var $dvLayer = $('#dvLayer');
		var $myForm2 = $('#myForm2');
		var $cmd = $('#cmd');
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
		
		$("#list").tableSorter({
			sortColumn: 'name',			// Integer or String of the name of the column to sort by.
			sortClassAsc: 'headerSortUp',		// class name for ascending sorting action to header
			sortClassDesc: 'headerSortDown',	// class name for descending sorting action to header
			headerClass: 'header'
			});

		$('.open').click(function() {
			var t = this.id.split(':');			
			$idCadastro.val(t[1]);
			$cmd.val('get');
			$.ajax({			
				type: 'POST',
				url: 'admCadastros.php',
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

		$('#idCurso').change(function() {$('#myForm').submit();})
		
		if ($.browser.msie)
			$dvLayer.css('left',(screen.width-470-220)/2+'px');
		else
			$dvLayer.css('left',(screen.width-470)/2+'px');

					
		$('#dvLayerClose').click(function() {
			$dvLayer.toggle('slow');
			})
		})
		
</script>
<div id="toPrint">
	<form name="myForm" id="myForm" style="margin:0" method="POST" action="">
		<table border="0" cellpadding="2" cellspacing="2" align="center" width="100%">
			<tr>
				<th>Cursos: 
					<select name="idCurso" id="idCurso" size="1">
						<option value="">Selecione</option><?
						$qc = mssql_query("SELECT idCurso, titulo FROM cursos WHERE ativo=1 ORDER BY titulo");
						while ($rs=mssql_fetch_object($qc)) {
							$selected = ($_idCurso==$rs->idCurso)?' selected':'';
							echo "<option $selected value='$rs->idCurso'>$rs->titulo</option>";
							}	?>
					</select>
					&nbsp;Clique no Nome para gerar o contrato
				</th>
			</tr>
		</table>
	</form>
	
	<table id="list" border="0" cellspacing="2" cellpadding="2">
		<thead>
			<tr>
				<th class="header">Início</th>
				<th class="header headerSortUp">Nome do Aluno</th>
				<th class="header">CPF do Aluno</th>
				<th class="header">Vcto</th>
				<th class="header">Pgto</th>
				<th class="header">Status</th>
			</tr>
		</thead>
		<tbody><?
		if ($sql=='')
			echo"
			<tr>
				<td colspan='7' align='center'>
					<b style='color:#c40000'><b>Selecione o curso na caixa de seleção acima!</b></td>
			</tr>";
		elseif ($rows==0)
			echo"
			<tr>
				<td colspan='7' align='center'>
					<b style='color:#c40000'><b>Nenhum registro localizado o curso selecionado!</b></td>
			</tr>";
		else
			while ($rs = mssql_fetch_object($query)) {
				$numdoc = sprintf('%05d',$rs->idCadastro);
				$numdoc .= sprintf('%02d',$rs->idCurso);
				
				$rs2->vcto=$rs2->pgto='00/00/0000';
				$sql="SELECT idFinanceiro,
					CONVERT(CHAR(10), vcto, 103) AS vcto,
					CONVERT(CHAR(10), pgto, 103) AS pgto
						FROM financeiro WHERE numDoc like '%$numdoc%'";
				$query2=mssql_query($sql); //echo $sql;
				
				if (mssql_num_rows($query2) > 0) {
					$rs2 = mssql_fetch_object($query2);		
					if($rs2->pgto=='01/01/1900') {
						$status = "<span style='color:red'>Pnd</span>";
						$rs2->pgto = "<span style='color:red'>00/00/0000</span>";
						}
					else
						$status = "<span style='color:darkgreen'>Ok</span>";
					}
				else {
						$status = "<span style='color:red'>???</span>";
						$rs2->pgto = "<span style='color:red'>00/00/0000</span>";		
					}
				?>
				<tr align="center" class="trLink" style="height:18px">
					<td><?=$rs->data?></td>
					<td align="left">
						<!-- <a href="#" onclick="eventos('cadastro.php?gt=<?=$rs->idCadastro?>')" title="open:<?=$rs->idCadastro?>"><?=$rs->nome?></a> -->
						<a href="../contrato.php?idCadastro=<?=$rs->idCadastro?>&idCurso=<?=$_idCurso?>" target="_blank"><?=$rs->nome?></a>
					</td>
					<td><?=formatCPF($rs->cpf)?></td>			
					<td><?=$rs2->vcto?></td>
					<td><?=$rs2->pgto?></td>
					<td><?=$status?></td>
				</tr><?
				}	?>
		</tbody>
	</table>
</div>


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
							<img id="dvLayerClose" src="images/closeLayer.gif" border="0"
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
			</table>
		</form>
	</fieldset>
</div>
