<?
session_start();
include '../global.php';
include '../includes/funcoesUteis.php';
if (!$_SESSION['admUser'] || $_SESSION['admNivel'] < $admFin)
	die("Acesso não permitido");

//debug($_POST);
if ($_POST['btEnviar']) {
	if (!$_POST['dataInicial'])
		die("<b style='color:#c40000'>Data Inicial não fornecida!</b><br/>");
	elseif (!$_POST['dataFinal'])
		die("<b style='color:#c40000'>Data Final não fornecida!</b><br/>");
	else {
		$dataInicial = implode(".", array_reverse(explode("/", $dataInicial)));	// inverte a data para a pesquisa
		$dataFinal = implode(".", array_reverse(explode("/", $dataFinal)));	// inverte a data para a pesquisa
		$saldoAnterior=$_POST['saldoAnterior'];
		if ($dataInicial > $dataFinal) {
			$temp=$dataInicial;
			$dataInicial=$dataFinal;
			$dataFinal=$temp;
			}		
		}
	}

// limpa balancete anterior se necessario	
$query= mssql_query("select * from contas order by conta");
if (mssql_num_rows($query) > 0) {
	while ($rsConta=mssql_fetch_object($query)){
		mssql_query("update contas set mes=0 where conta=$rsConta->conta") or die ("Erro 177");
 		}
	}

// computa o campo mes
$sql="select *, convert(char(10), data, $dataFilter) as fData from caixa where (convert(char(10), data, 102) >='$dataInicial' and convert(char(10), data, 102) <='$dataFinal')";
//	echo"$sql";
$query = mssql_query($sql) or die("Erro:183");
if (mssql_num_rows($query)) {
	while ($rsConta=mssql_fetch_object($query)){
		mssql_query("update contas set mes=mes+$rsConta->entrada-$rsConta->saida where conta=$rsConta->conta") or die("Erro 187");
 		}
	}

$limite = "11999";	// intervalo para tipo de contas
$totalEntradas=$totalSaidas=0;
?>
<style>
	.tituloPagina {
		margin:0;			  
		font: bolder 16pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		letter-spacing:.1em;
		}


	#titulo {			  
		background:url(images/title_bg.jpg) repeat-x;
		font: bolder 10pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
		color:#fff;
		padding-left:4px;
		width:100%;
		height:24px;
		text-align:left;
		letter-spacing:.2em;
		padding-top:3px;
		padding-left:5px
		}

	#extrato td {
		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
	/*	padding:2px 4px;	*/
		}

	.center {text-align:center}

	.right {
		text-align:right;
		padding:1px 4px}
</style>
<body topmargin=0 leftmargin=0>
<p class="tituloPagina"><img src="images/logoEmpresa.jpg" align="absMiddle"> 
	<b>AMF - Associação Mineira de Farmacêuticos</p>

<div id="titulo">BALANCETE DO CAIXA - Período <?= $_POST['dataInicial'] ?> a <?= $_POST['dataFinal'] ?></div>

<table border="0" id="extrato" width="100%" celspacing="0" cellpadding="0">
	<tr bgcolor='#ffffff'>
		<td colspan=2 align=right>
			<b>Saldo Anterior =>&nbsp;&nbsp;
			<?=formatVal($saldoAnterior)?></b>
		</td></tr>

	<tr bgcolor='#808080'>
		<td class='center' width="50%"><b style='color:#fff'>E N T R A D A S</b></td>
		<td class='center' width="50%"><b style='color:#fff'>S A Í D A S</b></td>
	</tr>

	<tr bgcolor="#c0c0c0" align="center">
		<td vAlign="top">
			<table border="0" id="extrato" width="100%" celspacing="0" cellpadding="0">
		 		<tr bgcolor="#808080" align="center">	
					<td height=16><b style="color:#fff">Conta</td>
					<td><b style="color:#fff">Nome</td>
					<td><b style="color:#fff">Saldo R$</td>
				</tr><?php
				$counter=0;
				$sql = "select * from contas where conta <= $limite and mes <> 0 order by conta";
				$query= mssql_query($sql);
				if (mssql_num_rows($query)) {
					while($rsConta=mssql_fetch_object($query)) {
						$saldo = formatDec($rsConta->mes);				
						if ($counter % 2==0)
							echo "<tr bgcolor='#efefef'>";
						else
							echo "<tr bgcolor='#ffffff'>";
						
						echo "
							<td class='center'>$rsConta->conta</td>
							<td>&nbsp;$rsConta->nome</td>
							<td class='right'>$saldo</td></tr>";
							$totalEntradas += $rsConta->saldo;	// acumula entradas
						$counter++;
						}	// while
					}	?>
			
			</table>
		</td>
		<td vAlign="top">		
			<table border="0" id="extrato" width="100%" celspacing="0" cellpadding="0">
		 		<tr bgcolor="#808080" align="center">	
					<td height=16><b style="color:#fff">Conta</td>
					<td><b style="color:#fff">Nome</td>
					<td><b style="color:#fff">Saldo R$</td>
				</tr><?php
				$counter=0;
				$sql = "select * from contas where conta > $limite and mes <> 0 order by conta";
				$query= mssql_query($sql);
				if (mssql_num_rows($query)) {
					while($rsConta=mssql_fetch_object($query)) {
						$saldo = formatDec($rsConta->mes);				
						if ($counter % 2==0)
							echo "<tr bgcolor='#efefef'>";
						else
							echo "<tr>";
						
						echo "
							<td class='center'>$rsConta->conta</td>
							<td>&nbsp;$rsConta->nome</td>
							<td class='right'>$saldo</td></tr>";
							$totalSaidas += $rsConta->saldo;	// acumular saídas
						$counter++;
						}	// while
					}	?>
			
			</table>
		</td>
	</tr>

	<?php
		$fTotalEntradas=formatVal($totalEntradas);
		$fTotalSaidas=formatVal($totalSaidas);
		$fLiquido = formatVal($totalEntradas+$totalSaidas);
		$fSaldoFinal=formatVal($_POST['saldoAnterior']+$totalEntradas+$totalSaidas);
		?>

	<tr>
		<td align=right>
			 <span style="font-size:10pt">Total de Entradas: <b><?= $fTotalEntradas ?></b></span>
		</td>

		<td align=right>
			<span style="font-size:10pt">Total de Saídas: <b><?= $fTotalSaidas ?></b></span>
		</td></tr>

	<tr bgcolor='#c0c0c0'>
		<td align=right colspan=2>
		 	<span style="font-size:10pt">Total Líquido no período: <b><?=$fLiquido?></b></span>
	  	</td></tr>
	
	<tr bgcolor='#c0c0c0'>
		<td align=right colspan=2>
			<span style="font-size:10pt">Saldo Final (Saldo Anterior + Total Líquido) em <?=$_POST['dataFinal'] ?>: <b><?=$fSaldoFinal?></b></span>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td align=center colspan=2><a href="javascript:void(null)" onclick="print()"/>
			 <img src="../images/printer.png" align="absMiddle" border="0"> Imprimir Balancete</a></td>
	</tr>
</table>
