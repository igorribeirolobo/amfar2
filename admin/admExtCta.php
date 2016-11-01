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
		$dataIni = implode(".", array_reverse(explode("/", $dataInicial)));	// inverte a data para a pesquisa
		$dataFin = implode(".", array_reverse(explode("/", $dataFinal)));	// inverte a data para a pesquisa

		if ($dataIni > $dataFin) {
			$temp=$dataIni;
			$dataIni=$dataFin;
			$dataFin=$temp;
			}
		$found=true;
		$sConta=$_POST['conta'];	
		}	
	}
?>

<div id="mainTop" style="background:url(images/admExtCta.png) no-repeat; height:24px; margin-bottom:6px"></div>	
<script language="javascript" type="text/javascript" src="calendar/calendar.js"></script>
<form style="float:left; margin:0"  name='balancete' id='balancete' action="" method="POST" onsubmit="return checkForm('balancete')">
	<input type="hidden" name="process" value="0"/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%">
		<tr> 
			<td style="padding:0 2px" nowrap>Data Inicial:<br>
				<input class='required' type=text id='data Inicial' name='dataInicial' size=10 style='text-align:center' value='<?=$dataInicial?>'
					onkeypress="formataData(this.event,'data Inicial')"/></td>
				</td>
			<td style="padding:4px" nowrap>Data Final:<br/>
				<input class='required' type=text id='data final' name='dataFinal' size=10 style='text-align:center' value='<?=$dataFinal?>'
					onkeypress="formataData(this.event,'data final')"></td>
				</td>
			<td style="padding:4px" nowrap>Conta:<br/>
				<select class='required' id="Conta" name="conta" style="font-size:8pt" size=1/>
					<option value="0"/>Todas</option><?
					$query=mssql_query("select conta, nome from contas order by nome");
					while ($resultr=mssql_fetch_object($query))	{
						echo "<option value='$resultr->conta'/>$resultr->nome ($resultr->conta)</option>\n";
						}	?>
				</select>
				</td> 
				
			<td style='text-align:center;padding:4px' vAlign=bottom>
				<input type="submit" style='cursor:pointer' value="Visualizar" name="btEnviar"></td>
		</tr>
		<tr>
			<td colspan=4>         






<?	// balancete
if ($found) {	
	// limpa balancete anterior se necessario	
		$sql="execute listaBalancete 1,$sConta";
		$query=mssql_query($sql);
		//echo $sql;
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
			border:1px solid #000
		/*	padding:2px 4px;	*/
			}
	
		.center {text-align:center}
	
		.right {
			text-align:right;
			padding:1px 4px}
	</style>
	

	<table border="0" id="extrato" width="100%" celspacing="4" cellpadding="4">
		<tr>
			<td colspan=3 id="titulo">
				EXTRATO DE CONTAS - Emissão: <?=date('d/m/Y')?> - Período: <?=$_POST['dataInicial']?> - <?=$_POST['dataFinal']?></td>
		<tr>
			<td><b>Conta Nº
			<td><b>Conta Nome
			<td align=center><b>Tipo
<?
		$sbc="";
		$total=0;
//		echo mssql_num_rows($query);
		while ($res=mssql_fetch_object($query)) {
		  
			if ($sbc != substr($res->conta, 0, 5)) {
				$sbc=substr($res->conta, 0, 5);
				echo"<tr><td>$sbc<td colspan=2><b>$res->descricao<td>";				
				}
				
			if ($res->tipoconta==1) $tipo="D";
			else $tipo="C";
			
			echo "
				<tr>
					<td>$res->conta</td>
					<td>&nbsp;&nbsp;$res->nome<td align=center>$tipo";
					$sql="SELECT *,
							CONVERT(CHAR(10), datamov, 103) AS fDataMov, historico 
					 	FROM (lancamento_contabil INNER JOIN movimento_contabil ON lancamento_contabil.nummov=movimento_contabil.id) 
						WHERE (CONVERT(CHAR(10), datamov, 102) >= '$dataIni')
							AND (CONVERT(CHAR(10), datamov, 102) <= '$dataFin')
							AND (conta=$res->conta)
					ORDER BY lancamento_contabil.id";
					$query2=mssql_query($sql);
					if (mssql_num_rows($query2)) {
						echo "<tr><td colspan=3>
							<table cellpadding=1 cellspacing=1 border=0 width=100% style='background:#ffffff'>
								<tr>
									<td>Data<td>NumDoc</td><td>Histórico<td>Saldo Ant.<td>Valor<td>Saldo Atual";
						while ($res2=mssql_fetch_object($query2)) {
							$sdAn=formatDec($res2->saldoAnt);
							$vlr=formatDec($res2->valor);
							$sdAt=formatDec($res2->saldoAtu);
							echo "
								<tr align=center>
									<td style='border:0;background:#c0c0c0; text-align:center'>$res2->fDataMov</td>
									<td style='border:0;background:#c0c0c0; text-align:center'>$res2->nummov</td>
									<td style='border:0;background:#c0c0c0; text-align:left'>$res2->historico</td>
									<td style='border:0;background:#c0c0c0; text-align:right'>$sdAn</td>
									<td style='border:0;background:#c0c0c0; text-align:right'><b>$vlr</b>";
							if ($res2->tipolct==1) echo " D";
							else echo " C";
							
							echo"<td style='border:0;background:#c0c0c0; text-align:right'>$sdAt</td>";									
							}
						echo"</table><br/>";
						}
			}
 ?>
		</tr>
	</table><?
	}
	?>
			</td>
		</tr>
	</table>
</form><br/>
