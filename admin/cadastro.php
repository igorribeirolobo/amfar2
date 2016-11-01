<body bgcolor="#f0f0f0" onload="resizeTo(500,460);">
<style>
	*{font:normal 10pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana}
	table {width:100%}	
	td{background:#fff;padding:2px 4px;border:solid 1px silver}
	th{text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:22px}
	.label{text-align:right;background:#369;color:#fff;font-size:9pt;}
</style>	
<?php
session_start();
include '../global.php';


function debug($var) {
	echo "<pre>";
	print_r($var);
	echo "</pre>";
	}

function formatCPF($strNumber)	{
	$strNumber = ereg_replace("[' '-./ \t]",'',$strNumber);
	if (strlen($strNumber)==11)	{	// o numero e CPF
		$temp = substr($strNumber,0,3) . '.';
		$temp .= substr($strNumber,3,3) . '.';
		$temp .= substr($strNumber,6,3) . '-';
		$temp .= substr($strNumber,-2);
		}	
	else	{	
		$temp = substr($strNumber,0,2) . '.';
		$temp .= substr($strNumber,2,3) . '.';
		$temp .= substr($strNumber,5,3) . '/';
		$temp .= substr($strNumber,8,4) . '-';
		$temp .= substr($strNumber,-2);
		}		
	return($temp);
}	// end function


$_id = ($_GET['gt'])?$_GET['gt']:$_POST['id'];
if (!$_id) $_id = 0;

if ($_GET['gt']) {
	$sql="SELECT *,
		CONVERT(CHAR(10),dataReg,103) AS dataReg,
		CONVERT(CHAR(10),ultAcesso,103) AS updated
		FROM cadastro WHERE idCadastro=$_id";
	$qyGet = mssql_query($sql); //echo $sql;
	$rs = mssql_fetch_object($qyGet);
//	debug($rs);
	if ($rs->idAtividade > 0) {
		$sql = "SELECT nome FROM atividade WHERE idAtividade=$rs->idAtividade";
		//echo $sql;
		$rs->atividade = mssql_result(mssql_query($sql),0,0);
		}
	 ?>
	<fieldset>
		<legend>Dados do Cadastro</legend>			
		<table border="0" cellspacing="1" cellpadding="1">
			<tr>
				<td class="label">ID:</td>
				<td><?=sprintf('%06d',$rs->idCadastro)?></td>
				<td class="label">Criado em:</td>
				<td><?=$rs->dataReg?></td>
			</tr>	
			<tr>
				<td class="label">Nome:</td>
				<td colspan="3"><?=$rs->nome?></td>
			</tr>
			<tr>
				<td class="label">CPF/CNPJ:</td>
				<td><?=formatCPF($rs->cpf)?></td>				
				<td class="label">RG/IE:</td>
				<td><?=$rs->rg?></td>
			</tr>	
			<tr>
				<td class="label">Endereço:</td>
				<td colspan="3"><?="$rs->ender, $rs->num $rs->compl"?></td>
			</tr>
			<tr>
				<td class="label">Bairro:</td>
				<td><?=$rs->bairro?></td>
				<td class="label">CEP:</td>
				<td><?=$rs->cep?></td>
			</tr>
			<tr>
				<td class="label">Cidade:</td>
				<td><?=$rs->cidade?></td>
				<td class="label">UF:</td>
				<td><?=$rs->uf?></td>
			</tr>
			<tr>
				<td class="label">E-mail:</td>
				<td colspan="3"><?=$rs->email?></td>
			</tr>
			<tr>	
				<td class="label">DDD/Tel 1:</td>
				<td><?=$rs->fone?></td>
				<td class="label">DDD/Tel 2:</td>
				<td><?=$rs->fone2?></td>
			</tr>
			<tr>
				<td class="label">Profissão</td>
				<td colspan="3"><?=$rs->profissao?></td>
			</tr>
			<tr>
				<td class="label">Atividade:</td>
				<td colspan="3"><?=$rs->atividade?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label">Fantasia:</td>
				<td colspan="3"><?=$rs->nomeFantasia?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label">Obs.:</td>
				<td colspan="3"><?=$rs->comentarios?>&nbsp;</td>
			</tr>
			<tr>
				<td class="label">Senha:</td>
				<td><?=$rs->senha?></td>
				<td class="label">Status:</td>
				<td><?if ($rs->status < 0) echo 'Cancelado';
						elseif ($rs->status==0) echo 'Inativo';
						elseif ($rs->status==1) echo 'Ativo';	?>
				</td>
			</tr>
		</table>
	</fieldset>
<?	}


?>

