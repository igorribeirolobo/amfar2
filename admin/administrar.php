<html>
<?
include '../global.php';
if ($_POST['newAccount']) {
	if(!$_POST['nome']) {
		echo "* Não informado data de lançamento";
		exit;
		}

	$nome  = utf8_decode(trim($_POST['nome']));
	$conta = utf8_decode($_POST['conta']);
	$valor = utf8_decode(str_replace(',','.',$_POST['valor']));
	
	$sql="insert into contas(conta, nome, saldo) values($conta, upper('$nome'),$valor)";
	mssql_query($sql);

	if (mssql_errno() == 0) 
		$msg="<b style='color:#006666'>Nova conta $conta: $nome ($valor) criada com sucesso!</b>";
	else
		$msg="<b style='color:#c40000'>Erro na cria&ccedil;&atilde;o da conta!" . mssql_error() . "</b>";
	
	echo $msg;
	exit;
	}


$query = "select * from caixa order by data";
$result= mssql_query($query);
$num_results = mssql_num_rows($result);
for ($i=0; $i <$num_results; $i++) {
	$paciente=mssql_fetch_object($result);
	$oldcodigo	= $paciente->DATA;
	$conta		= $paciente->CONTA; 
	$nome			= $paciente->NOME;
	$historico	= $paciente->HISTORICO;
	$entrada		= $paciente->ENTRADA;
	$saida		= $paciente->SAIDA;
	$saldo		= $paciente->SALDO;
                }
// inicializa saldo do caixa com o ultimo lançamento
$_SESSION['saldo_caixa']=$paciente->SALDO;
$query=mssql_query("select * from contas");
$result=mssql_fetch_object($query);
?>

<script language="javascript">
	function abrirContas() {
		window.open('contas.php');
		}
</script>

<div id="mainTop" style="background:url(images/mainAdministracao.png) no-repeat; height:24px; margin-bottom:6px"></div>	

<div id="administracao" style="width:560px; text-align:center">
	<form style='width:560px' action="caixa.php" id='caixa' name="caixa" method="POST" target='caixa' onsubmit="return checkForm('caixa')">
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellpadding="1" cellspacing=1 width="100%" style="border:2px dashed #004f6d">
			<tr>
				<td colspan=4 style="background:url(images/title_bg.jpg) repeat-x; height:20px; padding:0 2px">
					<b style='color:#fff'>:: CAIXA</td>
			<tr>
				<td style="padding:4px; border-bottom:1px solid #c0c0c0">
					<small>Data Inicial:</small><br />
					<input class='required' title='É necessário uma data inicial!' id=dataInicial name=dataInicial size=10 style='text-align:center' value='<?=$dataInicio?>'/>
						<a href="javascript:void(null)"/></td>
						
				<td style="padding:4px; border-bottom:1px solid #c0c0c0">
					<small>Data Final:</small><br />
					<input class='required' title='É necessário uma data final!' id=dataFinal name=dataFinal size=10 style='text-align:center' value='<?=$dataFinal?>'/>
						<a href="javascript:void(null)"/></td>

				<td style="padding:4px; border-bottom:1px solid #c0c0c0"><small>Conta</small><br />
					<select class='required' name="cbContas" id="cbContas" title='Selecione um conta para gerar o extrato!' style='font-color:8pt'>
						<option value="">Selecione</Option><?

						$query=mssql_query("select conta, nome from contas order by nome");
						while ($resultr=mssql_fetch_object($query))	{
							if ($conta == $resultr->conta)
								echo "<option selected value='$resultr->conta'/>$resultr->nome</option>\n";
							else
								echo "<option value='$resultr->conta'/>$resultr->nome</option>\n";
							}	?>
					</select>
				</td>
			</tr>
				<td style="padding:4px; text-align:center" colspan=3>
					<input type="submit" id='btSend' value="Lista Caixa" name="btCaixa"/></td>
			</tr>         
		</table>
	</form>

	<form style='width:560px' name="lctos" id='lctos' action="" method="POST">
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellpadding="1" cellspacing=1 width="100%" style="border:2px dashed #004f6d">
			<tr>
				<td colspan=4 style="background:url(images/title_bg.jpg) repeat-x; height:20px; padding:0 2px">
					<b style='color:#fff'>:: LANÇAMENTOS</td>
			<tr>
				<td style="padding:4px; border-bottom:1px solid #c0c0c0">
					<small>data</small><br />
					<input class='required' title='É necessário uma data de lançamento!' type=text id=dataLct name=dataLct size=10 style='text-align:center' value='<?=$dataLct?>'/>
						<a href="javascript:void(null)"/></td>
						
				<td style="padding:4px; border-bottom:1px solid #c0c0c0" valign=top>
					<small>histórico</small><br />
					<input class='required' title='É necessário um histórico de lançamento!' type="text" name="historico" size="38" maxlength="40" onchange="toUpper(this)"></td>					

						
				<td style="padding:4px; border-bottom:1px solid #c0c0c0" valign=top>
					<small>Valor</small><br />
					<input class='required' title='É necessário um valor de lançamento!' type="text" name="valor" style='text-align:right; padding:0 4px' size="12" maxlength="12"></td>
			</tr>
			<tr>
				<td style="padding:4px; text-align:center">
					<small>entrada/saida</small><br />
					<select class='required' title='Selecione o tipo de lançamento. Se débito ou crédito!!' name="tipLct" style="font-size:8pt" size='1'/>
						<option value=""/>Selecione</option>
						<option value='1'/>Crédito</option>
						<option value='2'/>Débito</option>
					</select>
					
				<td style="padding:4px">
					<small>Selecione a Conta</small><br />
					<select class='required' title='Selecione uma conta para este lançamento!' id="cbContas2" name="cbContas" style="font-size:8pt" size=1/>
						<option value=""/>Selecione</option><?
						$query=mssql_query("select conta, nome from contas order by nome");
						while ($resultr=mssql_fetch_object($query))	{
							echo "<option value='$resultr->conta'/>$resultr->nome</option>\n";
							}	?>
					</select>
				</td>

				<td style="padding:4px; text-align:center" vAlign=bottom>
					<input type="button" value="Incluir" id='btSend' name="save" onclick="sendCaixa();">					
				</td>
			</tr>
		</table>
		<div id='dvSave' style='text-align:center'></div>
	</form>

	<form style="width:560px" name='balancete' id='balancete' action="balanco1.php" method="POST" target="caixa"  onsubmit="return checkForm('balancete')">
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellpadding="1" cellspacing=1 width="100%" align=center style="border:2px dashed #004f6d">
			<tr>
				<td colspan=4 style="background:url(images/title_bg.jpg) repeat-x; height:20px; padding:0 2px">
					<b style='color:#fff'>:: BALANCETE</td>
			<tr> 
				<td style="padding:0 2px">
					<small>data inicial</small><br>
					<input class='required' title='É necessário uma data inicial!!' type=text id=blDataIni name=blDataIni size=10 style='text-align:center' value='<?=$blDataIni?>' />
						<a href="javascript:void(null)"/></td>
				</td>
				<td style="padding:4px">
					<small>data final</small><br/>
					<input class='required' title='É necessário uma data final!!' type=text id=blDataFim name=blDataFim size=10 style='text-align:center' value='<?=$blDataFim?>'/>
						<a href="javascript:void(null)"/></td>
				</td>
				<td style="padding:4px">
					<small>Saldo anterior</small><br/>
					<input type="text" style='text-align:right; padding:0 4px' name="saldoant" size="12" maxlength="12"></td> 
					
				<td style='text-align:center;padding:4px' vAlign=bottom>
					<input type="submit" id='btSend' value="Visualizar" name="btBalanco"></td>
			</tr>         
		</table>
	</form>


	<form name="novaconta" id="novaconta" style="width:560px" action="" method="POST" onsubmit="return checkForm(this)">
		<input type="hidden" name="newAccount" value="1"/>
		<input type="hidden" name="process" value="0"/>
		<table border="0" cellpadding="1" cellspacing=1 width="100%" align=center style="border:2px dashed #004f6d">
			<tr>
				<td colspan=4 style="background:url(images/title_bg.jpg) repeat-x; height:20px; padding:0 2px">
					<b style='color:#fff'>:: PLANO DE CONTAS</td>
			<tr>
				<td style='text-align:center;padding:4px; border-bottom:1px solid #c0c0c0' vAlign=bottom>
					<input type="button" value="Abrir Plano de Contas" id='btSend' name="B3" onclick="abrirContas()">
					
				<td style="padding:4px; border-bottom:1px solid #c0c0c0">
					<small>Conta</small><br />
					<input class='required' title='É necessário um Número de Conta!' type="text" name="conta" style='text-align:center' size="10" maxlength="40"></td> 
					
				<td style="padding:4px; border-bottom:1px solid #c0c0c0">
					<small>Valor</small><br />
					<input class='required' title='É necessário um Valor Inicial!' type="text" name="valor" style='text-align:right; padding:0 4px' size="15" maxlength="15"></td> 
			</tr>
			<tr>
				<td style="padding:4px" colspan=2>
					<small>Nome</small><br />
					<input class='required' title='É necessário um nome para a Conta!' type="text" name="nome" size="40" maxlength="40"></td>
					
				<td style='text-align:center;padding:4px' vAlign=bottom>
					<input type="button" id='btSend' value="Incluir" name="btSave" onclick="sendConta();">
				</td>  
			</tr>
		</table>
	</form>
	<div id='dvSave1' style='text-align:center; font:bolder 9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana'></div>
<!--	
		<link rel="stylesheet" href="css/calendar.css" type="text/css"/>
		<script language="javascript" type="text/javascript" src="js/calendar.js"></script>	-->
</div>
