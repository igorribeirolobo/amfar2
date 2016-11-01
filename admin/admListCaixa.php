<?
session_start();
include '../global.php';
//include '../includes/funcoesUteis.php';

//debug ($_POST);
if (!$_SESSION['admUser'] || $_SESSION['admNivel'] < $admFin)
	die("Acesso não permitido");
?>
<script language="javascript" type="text/javascript" src="calendar/calendar.js"></script>
<link rel="stylesheet" href="admin.css" type="text/css">
<div style="background:url(images/admLivroCaixa.png) no-repeat; height:24px; margin-bottom:6px"></div>	

<form style='float:left; width:360px' action="admLivroCaixa.php" id='caixa' name="caixa" method="POST" target='caixa' onsubmit="return checkForm('caixa')">
	<input type="hidden" name="process" value="0"/>
	<table border="0" cellpadding="1" cellspacing=1 width="100%" style="border:2px dashed #004f6d">
		<tr>
			<td class="label">Data Inicial:</td>
			<td>
				<input class='required' id='data Inicial' name='dataInicial' size=10 style='text-align:center' value='<?=$dataInicio?>'
					onfocus="calendar.open('data Inicial')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data Inicial')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a></td>
		<tr>			
			<td class="label">Data Final:</td>
			<td>
				<input class='required' id='data Final' name=dataFinal size=10 style='text-align:center' value='<?=$dataFinal?>'
				onfocus="calendar.open('data Final')"/>
					<a href="javascript:void(null)" onclick="calendar.open('data Final')">
						<img src=calendar/calendar.gif align=absMiddle border=0 alt="Abrir Calendário"></a></td>

		<tr>
			<td class="label">Selecione a Conta:</td>
			<td>
				<select class='required' name="cbContas" id="contas" title='Selecione um conta para gerar o extrato!' style='font-color:8pt'>
					<option value="0">TODAS</Option><?

					$query=mssql_query("select conta, nome from contas order by nome");
					while ($resultr=mssql_fetch_object($query))	{
						if ($conta == $resultr->conta)
							echo "<option selected value='$resultr->conta'/>$resultr->nome</option>\n";
						else
							echo "<option value='$resultr->conta'/>$resultr->nome</option>\n";
						}	?>
				</select>
			</td>
		<tr>
			<td class="label">Saldo Anterior:</td>
			<td><input class='required' id='saldo anterior' name='saldoAnterior' size=10 style='text-align:center' value='<?=$saldoAnterior?>'
			</td>
		<tr>
			<td style="padding:4px; text-align:center" colspan=3>
				<input type="submit" style='cursor:pointer' value="Listar Movimento Contábil" name="btCaixa"/></td>
		</tr>         
	</table>
</form>

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
