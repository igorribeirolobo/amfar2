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
		$idCC=$_POST['idCC'];	

		$query=mssql_query("SELECT nome FROM centrocusto WHERE id=$idCC");
		$rsQuery=mssql_fetch_object($query);
		$strCC=$rsQuery->nome;
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
			<td style="padding:4px" nowrap>Centro de Custo:<br/>
				<select class='required' id="idCC" name="idCC" style="font-size:8pt" size=1/>
					<?
					$query=mssql_query("select id, nome from centrocusto order by nome");
					while ($resultr=mssql_fetch_object($query))	{
						echo "<option value='$resultr->id'/>$resultr->nome</option>\n";
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
	//	$sql="execute listaBalancete 1,0";
	//	$query=mssql_query($sql);
	//	echo $sql;
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
	

	<table border="0" id="extrato" width="630" celspacing="4" cellpadding="4">
		<tr>
			<td colspan=3 id="titulo">
				POSIÇÃO POR C. DE CUSTO: <?=$strCC?>  - Período: <?=$_POST['dataInicial']?> - <?=$_POST['dataFinal']?> - Emissão: <?=date('d/m/Y')?></td>
		<tr>
			<td><b>Conta Nº
			<td><b>Conta Nome
			<td align=center><b>Tipo
<?

		$sql="SELECT     lc.id, lc.nummov, lc.datamov, lc.conta, lc.tipolct, lc.valor, CONVERT(CHAR(10), lc.datamov, 103) AS fDataMov, mc.historico, mc.centrocusto AS cc, 
			ct.nome AS ctaNome, ct.tipoconta, pc.descricao AS subContaNome
			FROM lancamento_contabil AS lc INNER JOIN
				movimento_contabil AS mc ON lc.nummov = mc.id INNER JOIN
				contas AS ct ON lc.conta = ct.conta INNER JOIN
				planodecontas_subconta AS pc ON ct.idSubconta = pc.id
			WHERE (CONVERT(CHAR(10), lc.datamov, 102) >= '$dataIni')
				AND (CONVERT(CHAR(10), lc.datamov, 102) <= '$dataFin')
				AND (mc.centrocusto = $idCC) AND (pc.idEstrutura=3 OR pc.idEstrutura=4)
			ORDER BY lc.conta";
		$query=mssql_query($sql);
		$sbc="";
		$total=0;
		$totalgeral=0;
		$currCta=0;
		$grupo='';
		$print=false;
		$totalDsp=0;
		
		while ($res=mssql_fetch_object($query)) {		  
			if ($sbc != substr($res->conta, 0, 5)) {
				$sbc=substr($res->conta, 0, 5);
				// fechar a tabela de lancamentos
				if ($print) {
					echo "<tr style='background:#ffffff'><td align=right colspan=3><b>TOTAL&nbsp;</b></td><td align=right><b>" . formatDec($total) . "</b></td></tr></table>";
					$total=0;
					$print=false;
					}
			
			if ($grupo != 	substr($res->conta, 0, 1)) {
			 	if ($totalgeral > 0) {
					echo "<tr style='background:#ff0000'><td align=right colspan=2><b style='color:#ffffff'>TOTAL GERAL DE DESPESAS&nbsp;</b></td><td align=right><b style='color:#ffffff'>" . formatDec($totalgeral) . "</b></td></tr><tr><td style='border;0'><br/></tr>";
					$totalDsp=$totalgeral;
					$totalgeral=0;
					}
				$grupo=substr($res->conta, 0, 1);
				}
				
				echo"<tr><td>$sbc<td colspan=2><b>$res->subContaNome<td>";				
				}
				
		
			if ($currCta != $res->conta) {				
				if ($print) {
					echo "<tr style='background:#ffffff'><td align=right colspan=3><b>TOTAL&nbsp;</b></td><td align=right><b>" . formatDec($total) . "</b></td></tr></table><tr><td style='border:0'>";
					$total=0;					
					$print=false;				
					}
				
				$currCta=$res->conta;

				if ($res->tipoconta==1) $tipo="D";
				else $tipo="C";
				
				echo"	
					<tr><td>$res->conta</td>
						<td>&nbsp;&nbsp;$res->ctaNome<td align=center>$tipo
					<tr><td colspan=3>
						<table cellpadding=1 cellspacing=1 border=0 width=100% style='background:#ffffff'>
							<tr><td align=center>Data<td align=center>NumDoc</td><td>Histórico<td align=right>Valor";

				}
							
			$vlr=formatDec($res->valor);
			if ($res->tipoconta==$res->tipolct)
				$total=$total+$res->valor;
			else
				$total=$total - $res->valor;
				

			if ($res->tipoconta==$res->tipolct)
				$totalgeral=$totalgeral+$res->valor;
			else
				$totalgeral=$totalgeral - $res->valor;
		
			echo "
				<tr align=center>
					<td style='border:0;background:#c0c0c0; text-align:center'>$res->fDataMov</td>
					<td style='border:0;background:#c0c0c0; text-align:center'>$res->nummov</td>
					<td style='border:0;background:#c0c0c0; text-align:left'>$res->historico</td>									
					<td style='border:0;background:#c0c0c0; text-align:right' nowrap><b>$vlr</b>";
			
			if ($res->tipoconta==$res->tipolct) echo " +";
				else echo " -";
			$print=true;						
			}
			if ($print)
				echo "<tr style='background:#ffffff'><td align=right colspan=3><b>TOTAL&nbsp;</b></td><td align=right><b>" . formatDec($total) . "</b></td></tr></table>
					<tr style='background:#006666'><td align=right colspan=2><b style='color:#ffffff'>TOTAL GERAL DE RECEITAS&nbsp;</b></td><td align=right><b style='color:#ffffff'>" . formatDec($totalgeral) . "</b></td></tr>
					<tr style='background:#FF0000'><td align=right colspan=2><b style='color:#ffffff'>- TOTAL GERAL DE DESPESAS&nbsp;</b></td><td align=right><b style='color:#ffffff'>" . formatDec($totalDsp) . "</b></td></tr>
					<tr style='background:#FFFFFF'><td align=right colspan=2><b>SALDO DO PERÍODO&nbsp;</b></td><td align=right><b>" . formatDec($totalgeral-$totalDsp) . "</b></td></tr>";
 ?>
		</tr>
	</table><?
	}
	?>
			</td>
		</tr>
	</table>
</form><br/>
