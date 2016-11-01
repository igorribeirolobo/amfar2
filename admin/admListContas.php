<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';
if (!$_SESSION['admUser'] || $_SESSION['admNivel'] < $admFin)
	die("Acesso não permitido");

//debug ($_POST);
$id=isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
if ($_POST['btNovaConta']) {
	$message='';
	if(!$_POST['nome'])
		$message="<b style='color:#c40000'>Não informado Nome da nova Conta</b><br/>";
		
	elseif (!$_POST['conta'] && !$id)
		$message="<b style='color:#c40000'>Não informado Número da nova Conta</b><br/>";
	
	else {
		$nome  = str_replace("'",'"', trim($_POST['nome']));
		$conta = (int)$_POST['conta'];
		$saldo = str_replace('.','',$_POST['saldo']);
		$saldo = str_replace(',','.',$saldo);
		
		if ($id)
			mssql_query("update contas set conta=$conta, nome=upper('$nome'), saldo=$saldo where id=$id");
		
		else {	
			$sql="insert into contas(conta, nome, saldo) values($conta, upper('$nome'), $saldo)";
			@mssql_query($sql);
	
			if (mssql_rows_affected($conn)== 1) { 
				}
			else
				$message="<b style='color:#c40000'>&nbsp;Erro: Conta '$conta' já existe na base de dados!</b>";
			}
		}
	}
$id=$_GET['id'];
if ($id) {
	$query=mssql_query("select * from contas where id=$id order by conta");
	$rsConta=mssql_fetch_object($query);
	$nome=$rsConta->nome;
	$conta=$rsConta->conta;
	$saldo=$rsConta->saldo;
	}
?>
<style>
	#extrato td{
		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		padding:2px 4px;
		}
	.center {text-align:center}
	.right {text-align:right}
	#extrato a {
		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		text-decoration:underline
		}
	#extrato a:hover {
		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		color:#c40000;
		text-decoration:none
		}
/*	#extrato a:visited {
		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		color:#ff6600;
		text-decoration:none
		}
*/
</style>

<div id="mainTop" style="background:url(images/admPlanoContas.png) no-repeat; height:24px; padding:2px 0 0 280px; font-size:11pt; margin-bottom:6px">
	  <b style="color:#000066">Contas e Saldos até <?= date('d/m/Y H:i:s') ?></b>
</div>

<form name="novaconta" id="novaconta" style="width:100%; padding:0; margin:0" action="" method="POST" onsubmit="return checkForm(this.id)">
	<input type="hidden" name="id" value="<?=$id?>"/>
	<input type="hidden" name="process" value=""/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%">
		<tr>
			<td bgcolor="#000066" align="center"><b style="color:#fff">Nova Conta<br>(capacidade)</b> 

			<td style="padding-left:4px"><b>Nome:</b> (75)<br /> 
				<input class='required' type="text" name="nome" id="Nome da Conta"  value="<?=$nome?>" size="40" maxlength="75"></td> 
				
			<td style="padding-left:4px"><b>Número:</b> (10)<br/>
				<input class='required' type="text" name="conta" id="Número da Conta" 
						 style='text-align:center' value="<?=$conta?>" size="10" maxlength="10"/></td> 

			<td style="padding-left:4px"><b>Saldo R$: (12,2)</b><br />
				<input class='required' type="text" name="saldo" id="Saldo inicial" style='text-align:right' value="<?=formatDec($saldo)?>" size="12" maxlength="12"></td>

			<td style='text-align:center;padding:4px' vAlign=bottom>
				<input type="submit" style='cursor:pointer' value="Gravar" name="btNovaConta"/>				
				<input type="button" style='margin-left:20px; cursor:pointer' value="Limpar" onclick="document.location.href='?act=lstCta'"/>
			</td>  
		</tr>
		<? if ($message) echo "<tr><td colspan=5>$message</td></tr>"; ?>
	</table>
</form>


<table border="0" id="extrato" width="100%" celspacing="1" cellpadding="1">
	<tr bgcolor="#c0c0c0">
		<td class="center"><a href="?act=lstCta&ordem=conta"/><b>Conta</a></td>
		<td class="center"><a href="?act=lstCta&ordem=nome"/><b>Nome</a></td>
		<td class="center"><a href="?act=lstCta&ordem=saldo"/><b>Saldo R$</a></td>
		<td class="center"><a href="?act=lstCta&ordem=conta"/><b>Conta</a></td>
		<td class="center"><a href="?act=lstCta&ordem=nome"/><b>Nome</a></td>
		<td class="center"><a href="?act=lstCta&ordem=saldo"/><b>Saldo R$</a></td>
	</tr><?php
		$counter=0;			
		$coluna=1;
		$ordem= isset($_GET['ordem']) ? $_GET['ordem'] : 'conta'; 
		$query = mssql_query("select * from contas order by $ordem");					
		while ($rsConta=mssql_fetch_object($query)) {			
			if ($coluna==1) {
				if ($counter==0 || $counter % 2==0) 
					echo "<tr bgcolor=#efefef>";
				else
					echo "<tr bgcolor=#ffffff>";
				}
			else $coluna=0;

			$saldo = formatDec($rsConta->saldo);
			$saldo = ($rsConta->saldo>=0) ? "<b>$saldo" : "<b style='color:#c40000'>$saldo";
			echo "
				<td class='center'><a href='?act=lstCta&id=$rsConta->id'/><b>$rsConta->conta</a></td>
				<td>";
				if ($id==$rsConta->id)
					echo"<img src='images/h_arrow.gif'><b style='color:#ff6600'>";
					
			echo "$rsConta->nome</b></td>
				<td class='right'>$saldo</td>";
			$coluna++;
			$counter++;			
			}
		?>
</table>
