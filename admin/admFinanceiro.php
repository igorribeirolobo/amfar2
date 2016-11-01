<?php

session_start();
include '../global.php';
include '../includes/funcoesUteis.php';
//include '../includes/funcoesUteis.php';
if (!$_SESSION['admUser'] || $_SESSION['admNivel'] < $admFin)
	die("Acesso não permitido");

if (!$_POST['btCaixa'])
	die("Acesso não autorizado!");
		
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$centroCusto =$_POST['centroCusto'];
$parceiro =$_POST['parceiro'];

if ($centroCusto) {
	$query=mssql_query("SELECT nome FROM centroCusto WHERE id=$centroCusto");
	$nome=mssql_result($query, 0, 0);
	}

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
						
					<td style="background:url(images/admListFin<?=$_POST['t']?>.png) no-repeat; height:24px">
				<tr bgcolor=#c0c0c0>
					 <td style="border:2px inset #c0c0c0; text-align:center; height:24px">						
						<h2><?
							if ($_POST['t']=='1') echo "Relatório de Contas a Pagar - Período: $dataInicial a $dataFinal";
							elseif ($_POST['t']=='2') echo "Relatório de Contas a Receber - Período: $dataInicial a $dataFinal";
							elseif ($_POST['t']=='3') echo "Relatório de Contas Pagas - Período: $dataInicial a $dataFinal";
							else echo "Relatório de Contas Recebidas - Período: $dataInicial a $dataFinal";
							// if ($_POST['t']=='1') echo "Relatório de Contas a Pagar - Período: $dataInicial a $dataFinal" : "Relatório de Contas a Receber - Período: $dataInicial a $dataFinal ";
							if ($centroCusto) echo " - Centro de Custo: $nome";
							elseif ($parceiro) echo " Parceiro";	?>
						</h2>
						<h3><b>Emissão <?= date('d/m/Y') ?></h3>
					</td>
			</tr>
		</table>
		<table border="0" id="extrato" cellpadding="1" cellspacing="1" width="100%">
			<tr bgcolor="#c0c0c0" align=center>
				<td><b>Data Vcto</td>
				<td><b>Num Dcto.</td>
				<td><b>Tipo</td>
				<td><b>Conta</td>				
				<td><b>Histórico</td>
				<td><b>Valor R$</td>
				<td><b>Acrésc.</td>
				<td><b>Total</td>
				<td><b>Pgto</td>
				<td><b>Forma</td>				
				<td><b>Banco</td>
				<td><b>Cta. Banco</td>
				<td><b>Num. Ch</td>
				<td><b>Dt. Cheque</td>
				<td><b>Obs</td>
			</tr><?php 
			$dtInicial = implode(".", array_reverse(explode("/", $dataInicial)));	// inverte a data para a pesquisa
			$dtFinal = implode(".", array_reverse(explode("/", $dataFinal)));	// inverte a data para a pesquisa
//			$query=mssql_query("select * from financeiro");
//			$rsQuery=mssql_fetch_object($query);
//			debug($rsQuery);
			
			if ($_POST['t'] < 3) {
				$sql="select f.*, 
					convert(char(10), f.vcto, $dataFilter) as fData, 
					convert(char(10), f.pgto, $dataFilter) as fPgto,
					convert(char(10), f.dataCheque, $dataFilter) as fDataCheque
					from financeiro f where 
					(convert(char(10), f.vcto, 102) >='$dtInicial' and 
					convert(char(10), f.vcto,102) <='$dtFinal' and
					convert(char(10), f.pgto,102) ='1900.01.01') and
					f.status={$_POST['t']} ";
					}
			else {
				$sql="select f.*, 
					convert(char(10), f.vcto, $dataFilter) as fData, 
					convert(char(10), f.pgto, $dataFilter) as fPgto,
					convert(char(10), f.dataCheque, $dataFilter) as fDataCheque
					from financeiro f where 
					(convert(char(10), f.vcto, 102) >='$dtInicial' and 
					convert(char(10), f.vcto,102) <='$dtFinal' and
					convert(char(10), f.pgto,102) > '1900.01.01') and
					f.status={$_POST['t']}-2 ";
			 }
				
			if ($centroCusto) $sql .=" AND centroCusto=$centroCusto";
			elseif ($parceiro) $sql .= " AND uid=$parceiro";
				
			$sql .="	order by f.vcto";

			

	//		echo "sql=$sql";
			$query = mssql_query($sql) or die("Erro de query");
			$rows=mssql_num_rows($query);			
			$counter=$totalGeral=0;
			while ($rsConta=mssql_fetch_object($query)) {
				$valor=formatVal($rsConta->valor);
				$forma=$arrayFormaPgto[$rsConta->formaPgto];
				$banco=$arrayBancos[$rsConta->banco];
				if ($rsConta->fPgto=='01/01/1900') $rsConta->fPgto='00/00/0000';
				if ($rsConta->fDataCheque=='01/01/1900') $rsConta->fDataCheque='00/00/0000';

				$totalGeral +=$rsConta->total;
				$data=$rsConta->fData;				
				if ($counter % 2==0)
					echo "<tr bgcolor=#efefef>";
				else
					echo "<tr>";
				echo "
					<td class='center'>$rsConta->fData
					<td class='center'>$rsConta->numDoc</td>
					<td class='center'>$rsConta->tDoc</td>
					<td class='center'>$rsConta->conta</td>
					<td>$rsConta->historico</td>
					<td class='right'>$valor</td>
					<td class='right'>" . formatVal($rsConta->acrescimo) . "</td>
					<td class='right'><b>" . formatVal($rsConta->total) . "</td>
					<td class='right'>$rsConta->fPgto</td>
					<td class='right'>$forma</td>					
					<td class='center'>$banco</td>
					<td class='center'>$rsConta->ctaBanco</td>
					<td class='center'>$rsConta->numCheque</td>
					<td class='center'>$rsConta->fDataCheque</td>
					<td>$rsConta->observacoes</td>
					</tr>";

					$counter++;				
				}	// while
				
			if ($counter > 0) echo" 				
				<tr bgcolor='#eeeee4'>
					<td class='center'>&nbsp;
					<td colspan=6 align=right><b>Total do Período:</td>
					<td class='right'><b>" . formatVal($totalGeral);
			else
				echo"<tr bgcolor='#eeeee4'><td align=center colspan=7>		
					<b>Nenhum lançamento encontrado!</td></tr>";
			?>			
		</table>
	</body>
</html>
