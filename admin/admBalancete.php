<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';
if (!$_SESSION['admUser'] || $_SESSION['admNivel']< $admFin)
	die("Acesso não permitido");
	
$query=mssql_query("execute listaBalancete 0,0");

?>

<div id="mainTop" style="background:url(images/admBalancoCaixa.png) no-repeat; height:24px; margin-bottom:6px"></div>	
	<style>
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
			border:1px solid #000
		/*	padding:2px 4px;	*/
			}
	
		.center {text-align:center}
	
		.right {
			text-align:right;
			padding:1px 4px}
	</style>
	

	<div id="titulo">BALANCETE DE CONTAS - Emissão: <?=date('d/m/Y')?></div>

	<table border="0" id="extrato" width="600" celspacing="4" cellpadding="4">
		<tr>
			<td><b>Conta Nº
			<td><b>Conta Nome
			<td align=right><b>Saldo
	<? $sbc="";
		$total=0;
		while ($res=mssql_fetch_object($query)) {			
			if ($sbc != substr($res->conta, 0, 5)) {
				if ($sbc!= '') {			
					echo"<tr bgcolor=#c0c0c0><td colspan=2 align=right>Total<td align=right><b>&nbsp;" . formatDec($total);
					$total=0;
					}
				$sbc=substr($res->conta, 0, 5);
				echo"<tr><td>$sbc<td colspan=2><b>$res->descricao<td>";				
				}
			
		echo "
			<tr>
				<td>$res->conta</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;$res->nome
				<td align=right>&nbsp;" . formatDec($res->saldo);
			
			if ($res->tipoconta==1)
				echo " D";
			else
				echo " C";
			$total += $res->saldo;

			}
		echo"<tr bgcolor=#c0c0c0><td colspan=2 align=right>Total<td align=right><b>&nbsp;" . formatDec($total); ?>
		</tr>
	</table>

