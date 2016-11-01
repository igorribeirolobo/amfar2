<?
include "../global.php";	
session_start();

$page[0]['icone']='admHome.png';
$page[0]['url']='home.html';
$page[1]['icone']='admCadastro.png';
$page[1]['url']='admCadastros.php';
$page[2]['icone']='admInscricoes.png';
$page[2]['url']='admInscricoes.php';
$page[5]['icone']='admAgenda.png';
$page[5]['url']='admAgenda.php';
$page[6]['icone']='admNoticias.png';
$page[6]['url']='admListNoticias.php';
$page[7]['icone']='admEventos.png';
$page[7]['url']='admListEventos.php';
$page[8]['icone']='admCursos.png';
$page[8]['url']='admListCursos.php';
$page[11]['icone']='admCursos.png';
$page[11]['url']='admBalancete.php';
$page[12]['icone']='admCursos.png';
$page[12]['url']='admBalanceteCaixa.php';
$page[20]['icone']='admUsuarios.png';
$page[20]['url']='admUsuarios.php';

$_p = ($_GET['p'])?$_GET['p']:0;
//$_p = ($_SESSION['admUser'])?$_GET['p']:0;

if ($_p < 0) {
	session_destroy();
	echo"<script>document.location.href='index.php';</script>";
	}
require '../includes/funcoesUteis.php';
?>

<html>
	<head>
		<title>AMF - Administração</title>
		<link rel="stylesheet" href="admin.css" type="text/css">
		<script language="JavaScript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>
		<script language="JavaScript" type="text/javascript" src="../js/prototypes.js"></script>
		<script language="JavaScript" type="text/javascript" src="lib/jquery.tablesorter.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<style>
			*{font:normal 9pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana}
			#icone {float:left;height:24px;width:100%;background:url(images/<?=$page[$_p]['icone']?>) no-repeat;padding:0}
			#navigator{float:left;width:100%;height:500px;_height:480px;overflow-y:hidden;_overflow-y:auto;margin:0;background:#fff url(images/home.jpg) no-repeat center center}
			#loading{display:none;float:left;width:16px;height:16px;margin:4px 0 0 12px}
			#printer{display:block;float:right;width:16px;height:16px;margin:4px 8px}
			#printer img{padding:2px; border:solid 1px silver}
			#printer img:hover{cursor:pointer;border:solid 1px #fff}
			
			#list {width:100%;background:#fff}
			#list th{text-align:left;padding-left:30px;height:20px;font-size:9pt;color:#444;border-bottom:solid 2px #444;cursor:default}
			#list td{border:solid 1px silver}	
			#list a{text-decoration:none;font-weight:bolder}
			#list a:hover{background:#00f;color:#fff}
			.even {background-color: #3D3D3D;}
			.odd {background-color: #6E6E6E;}
			.highlight {background-color: #3D3D3D;font-weight: bold;}
			.header {background:url(lib/header-bg.png) no-repeat}
			.headerSortUp {background:url(lib/header-asc.png) no-repeat}
			.headerSortDown {background:url(lib/header-desc.png) no-repeat}
			.scroll {height:440px;overflow:auto;overflow-x:hidden}
			#dvLayer {position:absolute;top:35px;left:320px;width:470px;height:400px;z-index:1000;background:#fff;display:none}
			#navigator{height:470px;overflow:scroll;overflow-x:hidden;background:#fff}
			.htable {text-align:center;background:url(images/title_bg.jpg);color:#fff;font-weight:bolder;height:24px;margin:0}
			.htable .newReg{margin-left:30px;cursor:pointer}
		</style>
		<script>			
			$(document).ready(function() {				
				var originColor='#fff';
				var alertColor='#FADEC5'
				$('.trLink').mouseover(function() {
					$('.trLink').each(function() {
						$(this).css('background','#fff');
						})
					$(this).css('background','#CBCFF6');	
					})

				
				$loading = $('#loading');
				$('#print').click(function() {
					var prtContent = $('#navigator').html();
					prtContent = prtContent.replace('class="scroll"','');
					var WinPrint = window.open('','','letf=0,top=0,width=1,height=1,toolbar=0,scrollbars=0,status=0');
					WinPrint.document.write('<style>');
					WinPrint.document.write('*{font:10pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;color:#000}');
					WinPrint.document.write('</style>');
					WinPrint.document.write(prtContent);
					WinPrint.document.close();
					WinPrint.focus();
					WinPrint.print();
					WinPrint.close();
					})
				})
			function eventos(url) {	
				var evtWin= window.open(url,'winEvt','left=0, top=0, width=560,height=540,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
				evtWin.focus();
				}
		</script>
	</head>
	
	<body topmargin="0" leftmargin="0" style="overflow-y:hidden">
		<div align=center>
			<table cellpadding=0 cellspacing=0 width="100%">
				<tr>				
					<td id="dvMenu" valign="top" style="width:220px">					
						<img border="0" src="images/logoAdm.png"/>
						<table border="0" cellspacing="1" width="100%" class="fcolor">
							<tr>
								<td class=lcolor align="center">
									<b>Administração do Site</b></font></td>
							</tr>
							<tr>
								<td class=menu><a href=../index.php>Voltar para o Site</a></td>
							</tr>
							<tr>
								<td class="tmenu">C A D A S T R O S</td>
							</tr>
							<tr>
								<td class="menu"><?=($_SESSION['admNivel']>1)?"<a href='index.php?p=1'>Lista de Cadastros</a>":'Lista de Cadastros'?></td>
							</tr>
							<tr>
								<td class="menu"><?=($_SESSION['admNivel']>1)?"<a href='index.php?p=2'>Lista Inscrições</a>":'Lista Inscrições'?></td>
							</tr>
							<tr>
								<td class="menu"><?=($_SESSION['admNivel']>1)?"<a href='index.php?p=3'>Baixa de Boletos</a>":'Baixa de Boletos'?></td>
							</tr>
							<tr>
								<td class="tmenu">T A B E L A S</td>
							</tr>
							<tr>
								<td class="menu">
									<?=($_SESSION['admNivel']==1||$_SESSION['admNivel']==5)?"<a href='?p=5'>Agenda Diretoria</a>":' Agenda Diretoria'?>
								</td>
							</tr>
							<tr>
								<td class="menu">
									<?=($_SESSION['admNivel']==1||$_SESSION['admNivel']==5)?"<a href='?p=6'>Link Notícias</a>":'Link Notícias'?>
								</td>
							</tr>
							<tr>
								<td class="menu">
									<?=($_SESSION['admNivel']>1)?"<a href='?p=7'>Link Eventos</a>":'Link Eventos'?>
								</td>
							</tr>
							<tr>
								<td class="menu">
									<?=($_SESSION['admNivel']>1)?"<a href='?p=8'>Link Cursos</a>":'Link Cursos'?>
								</td>
							</tr>
							<tr>
								<td class="tmenu">FINANCEIRO</td>
							</tr><!--
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstCta>";	?>
										Plano de Contas</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstCxa>";	?>
										Listar Movimento Contábil</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) //echo "<a href=?act=lctCxa>";	?>
										Lançamento Contábil</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) //echo "<a href=?act=lctFin&t=2>";	?>
										Lançar Contas a Receber</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5)//echo "<a href=?act=lctFin&t=1>";	?>
										Lançar Contas a Pagar</a></td></tr>
								-->
								
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=2>";	?>
										Listar Contas a Receber</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=4>";	?>
										Listar Contas a Recebidas</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=1>";	?>
										Listar Contas a Pagar</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=3>";	?>
										Listar Contas Pagas</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=extCta>";	?>
										Extrato de Conta</a></td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?p=11>";	?>
										Balancete-Caixa</a></td>
							</tr
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?p=12>";	?>
										Balancete Contas</a></td>
							</tr
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=posCta>";	?>
										Posição por C. de Custo</a></td>
							</tr>

							<tr>
								<td class="tmenu">U S U Á R I O S</td>
							</tr>
							<tr>
								<td class="menu"><?=($_SESSION['admNivel']==5)?"<a href=?p=20>Usuários do Sistema</a>":'Usuários do Sistema'?></td>
							</tr>
							<tr>
								<td class="menu"><?=($_SESSION['admUser'])?"<a href=?p=-1><b style='color:red;font-weight:bolder'>Desconectar</b></a>":'Desconectar'?></td>
							</tr>
						</table>
					</td>
					<td width=4>&nbsp;</td>
					<td valign="top">
						<div id="icone">
							<div id="loading"><img src="images/loading.gif" border="0"/></div>
							<div id="printer"><img id="print" src="images/printer.png" style="cursor:pointer" border="0" alt="Imprimir" title="Imprimir"/></div>
						</div>
						<div id="navigator"><?
							if ($_SESSION['admUser'])
								include $page[$_p]['url'];
							else
								include 'amfLogin.php';	?>
						</div>
					</td>
				</tr>							
			</table>
		</div>		
	</body>
</html>
