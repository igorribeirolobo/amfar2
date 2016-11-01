<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';

//debug ($_POST);
if (!$_SESSION['admUser'] || $_SESSION['admNivel']< $admFin)
	die("Acesso não permitido");


$id=($_GET['id']) ? $_GET['id'] : $_POST['id'];
$status=($_GET['t']) ? $_GET['t'] : $_POST['status'];

if ($_POST['btSave']) {
//	debug($_POST);
	$message='';
	if (!$_POST['dataLct'])
		$message .="<b style='color:#ff0000'>Não informado data de lan&ccedil;amento<br/></b>";
	if (!$_POST['numDoc'])
		$message .="<b style='color:#ff0000'>Não informado o número de documento<br/></b>";
	elseif (!$_POST['valor'])
		$message .="<b style='color:#ff0000'>Não informado o valor de lan&ccedil;amento<br/></b>";
	elseif (!$_POST['vcto'])
		$message .="<b style='color:#ff0000'>Não informado a data do vencimento<br/></b>";

	else {
			
		
		$numDoc=trim($_POST['numDoc']);
		$tipoDoc=$_POST['tipoDoc'];
		$conta=$_POST['conta'];
		$historico	= str_replace("'",'"',trim($_POST['historico']));
		$valor = str_replace(',','.',trim($_POST['valor']));
		$valor=(double)$valor;
		
		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
			$dataLct = implode("-", array_reverse(explode("/", $_POST['dataLct'])));
		else {
			$dataLct = explode('/',$_POST['dataLct']);
			$dataLct="{$dataLct[1]}/{$dataLct[0]}/{$dataLct[2]}";			
			}
		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
			$vcto = implode("-", array_reverse(explode("/", $_POST['vcto'])));
		else {
			$vcto = explode('/',$_POST['vcto']);
			$vcto="{$vcto[1]}/{$vcto[0]}/{$vcto[2]}";
			}

		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
			$pgto = implode("-", array_reverse(explode("/", $_POST['pgto'])));
		else {
			$pgto = explode('/',$_POST['pgto']);
			$pgto="{$pgto[1]}/{$pgto[0]}/{$pgto[2]}";
			}
		if ($pgto=='//') $pgto='';

		if ($_SERVER["REMOTE_ADDR"]=='10.1.1.102')
			$dataCheque = implode("-", array_reverse(explode("/", $_POST['dataCheque'])));
		else {
			$dataCheque = explode('/',$_POST['dataCheque']);
			$dataCheque="{$dataCheque[1]}/{$dataCheque[0]}/{$dataCheque[2]}";
			}
		if ($dataCheque=='//') $dataCheque='';

	//	$dataLct=$_POST['dataLct'];
	//	$vcto=$_POST['vcto'];
	//	$pgto=$_POST['pgto'];
	//	$dataCheque=$_POST['dataCheque'];
			


		$acrescimo = str_replace(',','.',trim($_POST['acrescimo']));
		$acrescimo=(double)$acrescimo;		
		$formaPgto=$_POST['formaPgto'];
		$banco=isset($_POST['banco']) ? $_POST['banco'] : 0;
		$ctaBanco=trim($_POST['ctaBanco']);
		$numCheque=trim($_POST['numCheque']);
		$observacoes=trim($_POST['observacoes']);
			
		$total=$valor+$acrescimo;
		
		if (!$id) {
			$sql="insert into financeiro(dataReg) values(getDate()) 
				select @@Identity as LastID";
			$query=@mssql_query($sql);
			$id=mssql_result($query, 0, 'LastID');
			}	
		
		$sql="update financeiro set 
			dataLct='$dataLct',
			status=$status,
			numDoc='$numDoc',
			tipoDoc={$_POST['tipoDoc']},
			conta=$conta,
			historico='$historico',
			valor=$valor, 
			vcto='$vcto',
			pgto='$pgto',
			acrescimo=$acrescimo,
			total=$total,
			formaPgto=$formaPgto,
			banco=$banco,
			ctaBanco='$ctaBanco',
			numCheque='$numCheque',
			dataCheque='$dataCheque',
			observacoes='$observacoes'
			where id=$id";
	//	echo $sql;

		$query=mssql_query($sql);
		if (mssql_rows_affected($conn))
			$message .="<b style='color:green'>Registro gravado com sucesso!</b>";
		}
		$dataLct=$_POST['dataLct'];
		$vcto=$_POST['vcto'];
		$pgto=$_POST['pgto'];
		$dataCheque=$_POST['dataCheque'];
	}
?>

<script language="javascript" type="text/javascript" src="calendar/calendar.js"></script>
<div id="mainTop" style="background:url(images/admLancaFin<?=$_GET['t']?>.png) no-repeat; height:24px; margin-bottom:6px"></div>	

<form style='float:left; width:360px' name="lctos" id='lctos' action="" method="POST" onsubmit="return checkForm('lctos')">
	<input type="hidden" name="status" value="<?=$status?>"/>
	<input type="hidden" name="id" value="<?= $id ?>"/>
	<input type="hidden" name="process" value="0"/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%" style="border:2px dashed #004f6d">
		<tr>
			<td class="label">Data Lcto.:</td>
			<td>
				<input class='required' type=text id='data Lançamento' name=dataLct size=10 style='text-align:center' value='<?= isset($dataLct) ? $dataLct : date('d/m/Y')?>'
					onfocus="calendar.open('data Lançamento')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data Lançamento')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
			</td>
		
		<tr>			
			<td class="label" valign=top>Nº do Docto.:</td>
			<td>
				<input class='required' type="text" id='Número do Documento' name="numDoc" size="20" maxlength="20" onchange="toUpper(this)" value="<?=$numDoc?>">
			</td>

		<tr>		
			<td class="label">Tipo Docto.:</td>
			<td>
				<select class='required' id="tipo de documento" name="tipoDoc" style="font-size:8pt" size=1/>
					<option value=""/>Selecione</option><?
					$query=mssql_query("select * from tipoDoc");
					while ($result=mssql_fetch_object($query))	{
						if ($tipoDoc==$result->id)
							echo "<option value='$result->id' selected/>$result->tipo</option>\n";
						else
							echo "<option value='$result->id'/>$result->tipo</option>\n";
						}	?>
				</select>
			</td>

		<tr>		
			<td class="label">Conta:</td>
			<td>
				<select id="Seleção de Conta" name="conta" style="font-size:8pt" size=1/>
					<option value="0"/>Selecione</option><?
					$query=mssql_query("select conta, nome from contas order by nome");
					while ($result=mssql_fetch_object($query))	{
						if ($conta==$result->conta)
							echo "<option value='$result->conta' selected/>$result->nome</option>\n";
						else
							echo "<option value='$result->conta'/>$result->nome</option>\n";
						}	?>
				</select>
			</td>

		<tr>			
			<td class="label" valign=top>Histórico:</td>
			<td>
				<input class='required' type="text" id='historico do lançamento' name="historico" size="50" maxlength="100" onchange="toUpper(this)" value="<?=$historico?>">
			</td>

		<tr>		
			<td class="label" valign=top nowrap>Valor em R$</td>
			<td>
				<input class='required' type="text" name="valor" id='valor do lançamento' 
					style='text-align:right; padding:0 4px' size="12" maxlength="12" value="<?=$valor ?>"></td>
		</tr>

		<tr>
			<td class="label">Data Vcto.:</td>
			<td>
				<input class='required' type=text id='data de vencimento' name='vcto' size=10 style='text-align:center' value='<?=$vcto?>'
					onfocus="calendar.open('data de vencimento')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data de vencimento')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
			</td>

		<tr>
			<td class="label">Data Pgto.:</td>
			<td>
				<input type=text id='data de pagamento' name='pgto' size=10 style='text-align:center' value='<?=$pgto?>'
					onfocus="calendar.open('data de pagamento')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data de pagamento')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
			</td>

		<tr>		
			<td class="label" valign=top nowrap>Acréscimo R$</td>
			<td>
				<input type="text" name="acrescimo" id='valor do acréscimo' 
					style='text-align:right; padding:0 4px' size="12" maxlength="12" value='<?=$acrescimo?>'></td>
		</tr>

		<tr>		
			<td class="label" valign=top nowrap>Total em R$</td>
			<td>
				<input type="text" name="valor" id='total do lançamento' style='text-align:right; padding:0 4px' size="12" maxlength="12"
					 value='<?=$total?>' disabled></td>
		</tr>

		<tr>		
			<td class="label">Forma Pagto.:</td>
			<td>
				<select class='required' id="forma de pagamento" name="formaPgto" style="font-size:8pt" size=1/>
					<option value=""/>Selecione</option>
					<option value='1' <? if ($formaPgto==1) echo ' selected' ?> />DINHEIRO</option>
					<option value='2' <? if ($formaPgto==2) echo ' selected' ?> />CHEQUE</option>
					<option value='3' <? if ($formaPgto==3) echo ' selected' ?> />BOLETO</option>
					<option value='4' <? if ($formaPgto==4) echo ' selected' ?> />CARTÃO</option>
				</select>
			</td>

		<tr>		
			<td class="label">Banco:</td>
			<td>
				<select id="Banco" name="banco" style="font-size:8pt" size=1/>
					<option value="0"/>Selecione</option>
					<option value='237' <? if ($banco=='237') echo ' selected'?>/>BRADESCO</option>
					<option value='001' <? if ($banco=='001') echo ' selected'?>/>BRASIL</option>					
					<option value='104' <? if ($banco=='104') echo ' selected'?>/>C. E. F.</option>					
					<option value='141' <? if ($banco=='141') echo ' selected'?>/>ITAU</option>
					<option value='409' <? if ($banco=='409') echo ' selected'?>/>UNIBANCO</option>					
				</select>
			</td>

		<tr>		
			<td class="label" valign=top nowrap>Conta:</td>
			<td>
				<input type="text" name="ctaBanco" id='Conta no banco' style='text-align:center; padding:0 4px' size="12" maxlength="12"
					 value='<?=$ctaBanco?>'></td>

		<tr>		
			<td class="label" valign=top nowrap>Num. Cheque:</td>
			<td>
				<input type="text" name="numCheque" id='número do cheque' style='text-align:center; padding:0 4px' size="12" maxlength="12"
					 value='<?=$numCheque?>'></td>
		</tr>

		<tr>
			<td class="label">Cheque para:</td>
			<td>
				<input type=text id='data de pre-datado' name='dataCheque' size=10 style='text-align:center' value='<?=$dataCheque?>'
					onfocus="calendar.open('data de pre-datado')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data de pre-datado')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
			</td>

		<tr>		
			<td class="label" valign=top nowrap>Obs.:</td>
			<td>
				<input type="text" name="observacoes" id='observacoes' style='padding:0 4px' size="50" maxlength="100"
					 value='<?=$observacoes?>'></td>
		</tr>

		<tr>
			<td colspan=2 align=center><b>Verifique se os valores estão corretos antes de gravar, <br/>
				pois esta operação não poderá ser cancelada!</b>
		<tr><td colspan=2 align=center>&nbsp;<?=$message?>
		<tr>	
			<td style="padding:4px; text-align:center" colspan=2>				 
				<input type="submit" value="Lançar no Financeiro" style='cursor:pointer' name="btSave">
				<? if ($id) echo"
					<input style='margin-left:30px' type='button' value='Limpar' style='cursor:pointer' name='btClean' onclick=\"document.location.href='?act=lctFin&t=$status'\">";
				?>
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
	order by cxa.conta, cxa.data";
$query=mssql_query($sql);
if (mssql_num_rows($query)) {
	echo"
	<table border='1' style='float:left' width='100%' bgcolor=#ffffff cellpadding='1' cellspacing=1 style='border:1px dashed #004f6d'>
		<tr>
			 <td colspan=6 align=center><b>Lançamentos efetuados hoje no Livro Caixa</b>	 
		<tr bgcolor='#000066' align=center>			
			<td class='label'><b style='color:#fff'>Data</td>			
			<td class='label'><b style='color:#fff'>Conta</td>
			<td class='label'><b style='color:#fff'>Conta-Nome</td>
			<td class='label'><b style='color:#fff'>Histórico</td>
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
			<td align>&nbsp;$rsLacto->nome
			<td>&nbsp;$rsLacto->historico
			<td align=right>". number_format($rsLacto->entrada, 2,',', '.') . "&nbsp;&nbsp;
			<td align=right>". number_format($rsLacto->saida, 2,',', '.') . "&nbsp;&nbsp;
			<td align=center>$rsLacto->fDataReg";
		}
	}
	echo"</table>";
?>
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
