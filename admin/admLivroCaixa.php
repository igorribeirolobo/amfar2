<?php

session_start();
include '../global.php';
include '../includes/funcoesUteis.php';

//debug($_SESSION);

if (!$_SESSION['admUser'] || $_SESSION['admNivel'] < $admFin)
	die("Acesso não permitido");

if (!$_POST['btCaixa'])
	die("Acesso não autorizado!");
		
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$saldoAnterior  = trim($_POST['saldoAnterior']);
$conta = $_POST['cbContas'];

//debug($_POST);
?>
<html>
	<head>
	   <title>AMFAR - Caixa</title>
	   <link rel="stylesheet" href="css/style.css" type="text/css">
	   <style>
	   	h1, h2, h3 {
	   		margin:0;	   	
				font: bolder 18pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif
				}
	   	h2 {
				font: bolder 13pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif
				}
	   	h3 {
				font: bolder 11pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif
				}
	   	
			#extrato td{
	   		font:8pt Tahoma, Verdana, Arial, Helvetica, Sans-Serif;
	   		padding:2px 4px;
	   		border:1px solid #c0c0c0
	   		}
	   	.center {text-align:center}
	   	.right {text-align:right}

	   </style>
	   <script>
	   	function toUpper(s) {
	   		document.write(s.toUpperCase());
	   		}
	   </script>
	</head>
	<body style="background:#fff">
		<div id="tudo">
			<table id="topo" border="0" width="100%" celspacing="0" cellpadding="0">
				<tr>
					<td align="center" rowspan="2" width="95" style="background:#fff; border:2px outset #c0c0c0">
						<img src="images/logoEmpresa.jpg"></td>
						
					<td style="background:url(images/admMovContabil.png) no-repeat; height:24px">
				<tr bgcolor=#c0c0c0>
					 <td style="border:2px inset #c0c0c0; text-align:center; height:24px">						
						<h2>Movimento Contábil período de <?= $dataInicial ?> a <?= $dataFinal ?></h2>
						<h3><b>Emissão <?= date('d/m/Y') ?></h3>
					</td>
			</tr>
		</table>
		<table border="0" id="extrato" cellpadding="1" cellspacing="1" width="100%">
			<tr bgcolor="#c0c0c0">
				<td><b>Data</td>
				<td><b>Conta</td>
				<td><b>Nome</td>
				<td><b>Histórico</td>
				<td><b>Entrada R$</td>
				<td><b>Saída R$</td>
				<td><b>Saldo R$</td>
			</tr><?php 
			$dtInicial = implode(".", array_reverse(explode("/", $dataInicial)));	// inverte a data para a pesquisa
			$dtFinal = implode(".", array_reverse(explode("/", $dataFinal)));	// inverte a data para a pesquisa
			
			if ($conta==0)
				$sql="select cx.*, convert(char(10), cx.data, $dataFilter) as fData, ct.nome
					from caixa cx, contas ct where
					cx.conta=ct.conta and	 
					(convert(char(10), cx.data, 102) >='$dtInicial' and convert(char(10), cx.data, 102) <='$dtFinal') 
					order by data";
			else
				$sql="select cx.*, convert(char(10), cx.data, $dataFilter) as fData, ct.nome
					from caixa cx, contas ct where
					ct.conta=$conta and 
					cx.conta=ct.conta and
				 	(convert(char(10), cx.data, 102) >='$dtInicial' and convert(char(10), cx.data, 102) <='$dtFinal') 
					order by data";
					 
//			echo $sql;
				
			$query = mssql_query($sql) or die("Erro de query $sql");
			$rows=mssql_num_rows($query);			
			$counter=0;
			$saldoAtual = $saldoAnterior; 
			while ($rsConta=mssql_fetch_object($query)) {				
				if ($counter==0) {
	 				echo"
					<tr bgcolor='#eeeee4'>
						<td class='center'>$rsConta->fData
						<td colspan=5 align=right><b>Saldo Anterior</td>
						<td class='right'><b>" . formatVal($saldoAnterior);
					 }
			//	$saldoAnterior=formatDec($rsConta->saldoAnterior);
				$saldoAtual += $rsConta->entrada - $rsConta->saida;
				$entrada=($rsConta->entrada > 0.00) ? formatDec($rsConta->entrada) : '0,00';
				$saida=($rsConta->saida > 0.00) ? "-" . formatDec($rsConta->saida) : '0,00';								
				$data=$rsConta->fData;
				if ($counter % 2==0)
					echo "<tr bgcolor=#efefef>";
				else
					echo "<tr>";
				echo "
					<td class='center'>$rsConta->fData
					<td class='center'>$rsConta->conta</td>
					<td>$rsConta->nome</td>
					<td>$rsConta->historico</td>
					<td class='right'>$entrada</td>
					<td class='right'>$saida</td>
					<td class='right'>". formatVal($saldoAtual) . "</td></tr>";
					$counter++;				
				}	// while
				
			if ($counter > 0) echo" 				
				<tr bgcolor='#eeeee4'>
					<td class='center'>$dataFinal
					<td colspan=5 align=right><b>Saldo Atual</td>
					<td class='right'><b>" . formatVal($saldoAtual);
			else
				echo"<tr bgcolor='#eeeee4'><td align=center colspan=7>		
					<b>Nenhum lançamento encontrado!</td></tr>";
			?>			
		</table>
	</body>
</html>
