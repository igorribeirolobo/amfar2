<?
include "../global.php";	
session_start();

$act=$_GET['act'];

if (!$_SESSION['admUser']) $act="log";

if ($act=="off") {
	session_destroy();
	$act=null;
	}
require '../includes/funcoesUteis.php';
//debug($_SESSION);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php header("Content-Type: text/html; charset=ISO-8859-1",true) ?>
		<title>AMFAR - Administração</title>
		<link rel="stylesheet" href="admin.css" type="text/css">
		<script type="text/javascript" src="../js/ajax.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	
	<body topmargin=0 leftmargin="0">
		<div align=center>
			<table cellpadding=0 cellspacing=0 width="99%">
				<tr>
					<td colspan=3 id="dvTopo">AMFAR - ADMINISTRAÇÃO DO SITE</td>
				<tr>
				<tr>				
					<td id="dvMenu" valign="top" width=164>					
						<img border="0" src="images/logoMail.gif" width="100%">
						<table border="0" cellspacing="1" width="164" class="fcolor">
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
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel'] > 1)	echo "<a href=index.php?act=lstCad>";	?>
											Lista de Cadastros</a></td>
							</tr>
							<tr>
								<td class="menu">

											Lista Inscrições</a></td>
							</tr>
							<tr>
								<td class="menu">

											Cadastros Pendentes</a></td>
							</tr>
<!--							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel'] > 1)	echo "<a href=?act=lstBol>";	?>
										Baixa de Boletos</a></td>
							</tr>	-->
							<tr>
								<td class="tmenu">T A B E L A S</td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && ($_SESSION['admNivel']==1 || $_SESSION['admNivel']==5))	echo "<a href=?act=lstAgd>";	?>
										Link Agenda</a></td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && ($_SESSION['admNivel']==1 || $_SESSION['admNivel']==5))	echo "<a href=?act=lstNot>";	?>
										Link Notícias</a></td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel'] > 1)	echo "<a href=?act=lstEvt>";	?>
										Link Eventos</a></td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel'] > 1)	echo "<a href=?act=lstCrs>";	?>
										Link Cursos</a></td>
							</tr>
							<tr>
								<td class="tmenu">FINANCEIRO</td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstCta>";	?>
										Plano de Contas</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstCxa>";	?>
										Livro Caixa</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lctCxa>";	?>
										Lançar no Livro Caixa</a></td></tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lctFin&t=2>";	?>
										Lançar Contas a Receber</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lctFin&t=1>";	?>
										Lançar Contas a Pagar</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=2>";	?>
										Listar Contas a Receber</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstFin&t=1>";	?>
										Listar Contas a Pagar</a></td></tr>

							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=blcCxa>";	?>
										Balancete do Caixa</a></td></tr>
							<tr>
								<td class="tmenu">E N Q U E T E</td>
							</tr>
							<tr>
								<td class="nomenu">
									<?	//if ($_SESSION['admUser'])	echo "<a href=?act=meq>";	?>
											Alterar Enquete</a></td>
							</tr>
							<tr>
								<td class="nomenu">
									<?	//if ($_SESSION['admUser'])	echo "<a href=?act=enq>";	?>
											Resultado Enquete</a></td>
							</tr>
							<tr>
								<td class="tmenu">E-MAILING</td>
							</tr>
							<tr>
								<td class="menu">
									<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href='../e-mailing' target='_emailing'>"   ?>
											Envio de Mala-Direta</a></td>
							</tr>


							<tr>
								<td class="tmenu">U S U Á R I O S</td>
							</tr>
							<tr>
								<td class="menu">
									<?	if ($_SESSION['admUser'] && $_SESSION['admNivel']==5) echo "<a href=?act=lstUsr>";	?>
											Usuários do sistema</a></td>
							</tr>
							<tr>
								<td class="menu">
								<?	if ($_SESSION['admUser'])	echo "<a href=?act=off>";	?>
											<font color=red><b>Desconectar</b></font></a></td>
							</tr>
						</table>
					<td width=4>&nbsp;
					<td id="dvMain" valign="top" width="100%"><?
						if ($act=='log')
							include 'amfLogin.php';
	
						elseif ($act=='newCur')
							include 'adm_cursos.php';
	
						elseif ($act=='lisCur')
							include 'lista_cursos.php';
	
						elseif ($act=='nwr')
							include 'adm_cadastro.html';
	
						elseif ($act=='lstCad')
							include 'admListCadastros.php';
	
						elseif ($act=='lstPnd')
							include 'pendentes.php';
	
						elseif ($act=='lstIns')
							include 'lista_inscricoes.php';
	
						elseif ($act=='lstBol')
							include 'lista_boletos.php';
	
						elseif ($act=='lstAgd')
							include 'admListAgenda.php';

						elseif ($act=='lstNot')
							include 'admListNoticias.php';

						elseif ($act=='lstEvt')
							include 'admListEventos.php';
								
						elseif ($act=='lstCrs')
							include 'admListCursos.php';

						elseif ($act=='lstCta')
							include 'admListContas.php';

						elseif ($act=='lstCxa')
							include 'admListCaixa.php';

						elseif ($act=='lctFin')
							include 'admLancaFinan.php';

						elseif ($act=='lstFin')
							include 'admListFinan.php';

						elseif ($act=='lctCxa')
							include 'admLancaCaixa.php';

						elseif ($act=='blcCxa')
							include 'admBalanceteCaixa.php';

						elseif ($act=='lstUsr')
							include 'admUsuarios.php';

						elseif ($act=='email')
							include '../e-mailing/';

						elseif ($act=='meq')
							include '../enquete/index.php';
	
						elseif ($act=='enq')
							include '../enquete/resultados.php';
	
						else
							include 'home.html';	?>
					</td>
				</tr>							
			</table>
		</div>		
	</body>
</html>
