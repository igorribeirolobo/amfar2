<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';

//debug ($_POST);
if (!$_SESSION['admUser'] || $_SESSION['admNivel']< $admFin)
	die("Acesso não permitido");


if ($_POST['btSave']) {
	$message='';

	if ($_POST['contaDebito'] == $_POST['contaCredito'])
		$message .="Conta para Débito não pode ser a mesma para Crédito<br/>";

	if (!$_POST['data'])
		$message .="Não informado data de lan&ccedil;amento<br/>";
	elseif (!$_POST['historico'])
		$message .="Não informado o Histórico de lan&ccedil;amento<br/>";
	elseif (!$_POST['contaDebito'])
		$message .="Não informado a conta de lan&ccedil;amento a Débito<br/>";	
	elseif (!$_POST['contaDebito'])
		$message .="Não informado a conta de lan&ccedil;amento a Crédito<br/>";
	elseif (!$_POST['valor'])
		$message .="Não informado o valor de lan&ccedil;amento<br/>";
	else {	
		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
			$data = implode("-", array_reverse(explode("/", $_POST['data'])));
		else {
			$data = explode('/',$_POST['data']);
			$data="{$data[1]}/{$data[0]}/{$data[2]}";
			}
		$contaDebito = $_POST['contaDebito'];
		$contaCredito = $_POST['contaCredito'];
		$valor = str_replace(',','.',trim($_POST['valor']));
		$historico	= str_replace("'",'"',trim($_POST['historico']));
		$numDoc	= str_replace("'",'"',trim($_POST['numDoc']));
	
		$query=mssql_query("select saldo from contas where conta=$contaDebito");	
		$saldoAnterior=mssql_result($query, 0, 'saldo');
		$saldoAtual=$saldoAnterior-$valor;			
		$sql="insert into caixa(data, conta, ccusto, historico, numDoc, saldoAnterior, entrada, saida, saldoAtual, dataReg) 
			values('$data', $contaDebito, {$_POST['cCusto']}, '$historico', '$numDoc', $saldoAnterior, 0, $valor, $saldoAtual, getDate())";
		$query=mssql_query($sql) or die ("Erro 44 $sql");
		mssql_query("update contas set saldo=$saldoAtual where conta=$contaDebito") or die("Erro atualizando Contas-Lcto Débito");

		$query=mssql_query("select saldo from contas where conta=$contaCredito");	
		$saldoAnterior=mssql_result($query, 0, 'saldo');
		$saldoAtual=$saldoAnterior+$valor;			
		$sql="insert into caixa(data, conta, ccusto, historico, numDoc, saldoAnterior, entrada, saida, saldoAtual, dataReg) 
			values('$data', $contaCredito, {$_POST['cCusto']}, '$historico', '$numDoc', $saldoAnterior, $valor, 0, $saldoAtual, getDate())";
		$query=mssql_query($sql) or die ("Erro 44 $sql");
		mssql_query("update contas set saldo=$saldoAtual where conta=$contaCredito") or die("Erro atualizando Contas-Lacto Crédito");


		echo"<script>alert('Lançamento efetuado com sucesso!');document.location.href='index.php?act=lctCxa';</script>";
		}
	}
?>

<script language="javascript" type="text/javascript" src="calendar/calendar.js"></script>
<div id="mainTop" style="background:url(images/admLancaCaixa.png) no-repeat; height:24px; margin-bottom:6px"></div>	

<form style='float:left; width:360px' name="lctos" id='lctos' action="" method="POST" onsubmit="return checkForm('lctos')">
	<input type="hidden" name="process" value="0"/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%" style="border:2px dashed #004f6d">
		<tr>			
			<td class="label" valign=top>Histórico:</td>
			<td>
				<input class='required' type="text" id='historico do lançamento' name="historico" size="80" maxlength="100" onchange="toUpper(this)">
			</td>		

		<tr>		
			<td class="label" valign=top nowrap>Valor em R$</td>
			<td>
				<input class='required' type="text" name="valor" id='valor do lançamento' style='text-align:right; padding:0 4px' size="12" maxlength="12">
				<span class="label">Num. Docto: 
					<input class='required' type="text" name="numDoc" id='Número de Documento' style='text-align:center; padding:0 4px' size="12" maxlength="12">
			</td>
		</tr>
		<tr>		
			<td class="label">Conta Débito:</td>
			<td>
				<select class='required' id="Seleção de Conta a Débito" name="contaDebito" style="font-size:8pt" size=1/>
					<option value=""/>Selecione</option><?
					$query=mssql_query("select conta, nome from contas order by nome");
					while ($resultr=mssql_fetch_object($query))	{
						echo "<option value='$resultr->conta'/>$resultr->nome ($resultr->conta)</option>\n";
						}	?>
				</select>
			</td>
		<tr>		
			<td class="label" nowrap>Conta Crédito:</td>
			<td>
				<select class='required' id="Seleção de Conta a Crédito" name="contaCredito" style="font-size:8pt" size=1/>
					<option value=""/>Selecione</option><?
					$query=mssql_query("select conta, nome from contas order by nome");
					while ($resultr=mssql_fetch_object($query))	{
						echo "<option value='$resultr->conta'/>$resultr->nome ($resultr->conta)</option>\n";
						}	?>
				</select>
			</td>

		<tr>		
			<td class="label">Cursos:</td>
			<td>
				<select name="cCusto" style="font-size:8pt" size=1/>
					<option value="0"/>Selecione</option><?
					$query=mssql_query("select id, titulo from cursos order by titulo");
					while ($resultr=mssql_fetch_object($query))	{
						echo "<option value='$resultr->id'/>$resultr->titulo</option>\n";
						}	?>
				</select>
			</td>
		<tr>
			<td class="label">Data:</td>
			<td>
				<input class='required' type=text id='data Lançamento' name=data size=10 style='text-align:center' value='<?=date('d/m/Y')?>'
					onfocus="calendar.open('data Lançamento')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data Lançamento')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
			</td>
		<tr>
			<td colspan=2 align=center><b>Verifique se os valores estão corretos antes de gravar, <br/>
				pois esta operação não poderá ser cancelada!</b>
		<tr><td colspan=2 align=center>&nbsp;<b style="color:red"><?=$message?></b>
		<tr>	
			<td style="padding:4px; text-align:center" colspan=2>				 
				<input type="submit" value="Contabilizar" style='cursor:pointer' name="btSave">					
			</td>
		</tr>
	</table>
</form>
<?
$hoje=date('Y.m.d');
$sql="select cxa.*,
	convert(char(10), cxa.data, $dataFilter) as fData, 
	convert(char(10), cxa.dataReg, $dataFilter) as fDataReg from
	caixa cxa, contas cta where 
	cxa.conta=cta.conta and
	convert(char(10), cxa.dataReg, 102)='$hoje' 
	order by id desc";
$query=mssql_query($sql);
if (mssql_num_rows($query)) {
	echo"
	<table border='1' style='float:left' width='100%' bgcolor=#ffffff cellpadding='1' cellspacing=1 style='border:1px dashed #004f6d'>
		<tr>
			 <td colspan=6 align=center><b>Lançamentos Contábeis efetuados hoje</b>	 
		<tr bgcolor='#000066' align=center>			
			<td class='label'><b style='color:#fff'>Data</td>			
			<td class='label'><b style='color:#fff'>Conta</td>
			<td class='label'><b style='color:#fff'>Histórico</td>
			<td class='label'><b style='color:#fff'>Num. Doc.</td>
			<td class='label'><b style='color:#fff'>Entrada R$</td>
			<td class='label'><b style='color:#fff'>Saída R$</td>
			<td class='label'><b style='color:#fff'>DataReg</td>";
		
	while($rsLacto=mssql_fetch_object($query)) {
		$entrada=($rsLcto->entrada > 0) ? formatDec($rsLcto->entrada) : '0.00';
		$saida=($rsLcto->saida > 0) ? formatDec($rsLcto->saida) : '0.00';
		echo "
		<tr>			
			<td align=center>$rsLacto->fData			
			<td align=center>$rsLacto->conta
			<td>&nbsp;$rsLacto->historico
			<td align=center>&nbsp;$rsLacto->numDoc
			<td align=right>". number_format($rsLacto->entrada, 2,',', '.') . "&nbsp;&nbsp;
			<td align=right>". number_format($rsLacto->saida, 2,',', '.') . "&nbsp;&nbsp;
			<td align=center>$rsLacto->fDataReg";
		}
	echo"</table>";
	}
?>
<style>
	.visivel {display:inline}	
	.invisivel {display:none}
	
	#dvCalendar {
		float:left;
		position:absolute;
		top:200px;
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

