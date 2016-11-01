<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';
if (!$_SESSION['admUser'] || $_SESSION['admNivel']< $admFin)
	die("Acesso não permitido");
	
$found=false;
if ($_POST['btEnviar']) {
	if (!$_POST['dataInicial'])
		$message="<b style='color:#c40000'>Data Inicial não fornecida!</b><br/>";
	elseif (!$_POST['dataFinal'])
		$message="<b style='color:#c40000'>Data Final não fornecida!</b><br/>";
	else {
		$dataInicial = implode(".", array_reverse(explode("/", $dataInicial)));	// inverte a data para a pesquisa
		$dataFinal = implode(".", array_reverse(explode("/", $dataFinal)));	// inverte a data para a pesquisa
		$saldoAnterior=$_POST['saldoAnterior'];
		if ($dataInicial > $dataFinal) {
			$temp=$dataInicial;
			$dataInicial=$dataFinal;
			$dataFinal=$temp;
			}
		$found=true;			
		}
	}
?>

<div id="mainTop" style="background:url(images/admBalancoCaixa.png) no-repeat; height:24px; margin-bottom:6px"></div>	
<script language="javascript" type="text/javascript" src="calendar/calendar.js"></script>
<form style="float:left; margin:0; width:460px" name='balancete' id='balancete' action="" method="POST" onsubmit="return checkForm('balancete')">
	<input type="hidden" name="process" value="0"/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%">
		<tr> 
			<td style="padding:0 2px">Data Inicial:<br>
				<input class='required' type=text id='data Inicial' name='dataInicial' size=10 style='text-align:center' value='<?=$_POST['dataInicial']?>'
					onfocus="calendar.open('data Inicial')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data Inicial')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a></td>
			</td>
			<td style="padding:4px">Data Final:<br/>
				<input class='required' type=text id='data final' name='dataFinal' size=10 style='text-align:center' value='<?=$_POST['dataFinal']?>'
					onfocus="calendar.open('data final')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data final')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a></td>
			</td>
			<td style="padding:4px">Saldo anterior:<br/>
				<input class='required' type="text" style='text-align:right; padding:0 4px' name="saldoAnterior" size="12" maxlength="12" value="<?=$_POST['saldoAnterior']?>"/></td> 
				
			<td style='text-align:center;padding:4px' vAlign=bottom>
				<input type="submit" style='cursor:pointer' value="Visualizar" name="btEnviar"></td>
		</tr>         
	</table>
</form><br/>


<style>
	.visivel {display:inline}	
	.invisivel {display:none}
	
	#dvCalendar {
		float:left;
		position:absolute;
		top:70px;
		left:600px;
		
		width:154px;
		_width:154px;
	
		height:164px;
		_height:auto;
		margin-top:2px;
		margin-bottom:2px;	
		border-style:outset;
		text-align:center;
		background:#eeeee4;
		z-index:100;
		}
	
	#dvCalendar .topCalendar {
		margin-top:2px;
		width:100%;
		height:100%;	
		text-align:center;
		background:#c0c0c0
		}
	
	#dvCalendar td {
		background:#e4e4e4;
		border:1px solid #c0c0c0
		}
	
	#dvCalendar .icones {
		width:15px;
		text-align:center;
		}
	
	#dvCalendar #mes {
		text-align:center;
		font: bolder 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;	
		background:#404040;
		color:#fff;
		}
	
	#dvCalendar #calendar {
		width:154px;
		_width:156px;
		height:auto;
		padding:1px;
		text-align:center
		}
	
	#dvCalendar .dias {
		float:left;
		width:auto;
		height:110px;
		text-align:center;
		font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
		}
	
	#dvCalendar .diasSemana {	
		border-top:1px solid #404040;
		border-right:1px solid #c0c0c0;
		border-left:1px solid #404040;
		border-bottom:1px solid #c0c0c0;
		text-align:center;
		font: bolder 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
		background: #404040;
		color:#ffffff;
		width:20px 
		}
	
	#dvCalendar .dia {
		height:20px;	
		text-align:center;	
		font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
		border-bottom:1px solid #000;
		border-right:1px solid #000; }
	
	#dvCalendar .dia a {
		font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
		text-decoration:none; }
	
	#dvCalendar .dia a:hover {
		font-weight:bolder;
		background:#c40000;
		color:#fff;
		}
</style>

<div id=dvCalendar class=invisivel>
	<div class=topCalendar>
		<table cellpadding='0' cellspacing='0' width='156'/>
			<tr align=center>
				<td class='icones'>
					<a href='javascript:void(null)' onclick='calendar.go(-1)'/>
					<img src='calendar/prevArrow.gif' width="5" height="10"  border=0/></a></td>
				<td id='mes'></td>
				<td class='icones'>
					<a href='javascript:void(null)' onclick='calendar.go(1)'/>
						<img src='calendar/nextArrow.gif' width="5" height="10" border=0/></a></td>						
				<td class='icones'>
					<a href='javascript:void(null)' onclick='calendar.close()'/>
						<img style='margin-top:2px' src='calendar/close.gif' border=0/></a></td>
		</table>
		<div id='calendar'></div>
	</div>
</div>




<?	// balancete
if ($found) {	
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
	
	<p style="margin-top:70px"></p>

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
	</table>
	
	<form name="imprimir" action="admPrintBalanco.php" method="post" target="_blank"/>
		<input type="hidden" name="dataInicial" value="<?=$_POST['dataInicial']?>"/>
		<input type="hidden" name="dataFinal" value="<?=$_POST['dataFinal']?>"/>		
		<input type="hidden" name="saldoAnterior" value="<?=$saldoAnterior?>"/>	
		<p align=center><input type="image" name="toPrint" src="../images/printer.png" value="Imprimir"/></p>
	</form>
<?	} ?>
