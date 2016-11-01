<?php
/*
 * e-Mailing - Sistema automatizado para criação e envio de emails por lote
 * Copyright (C) 2007 Lauro A. L. Brito
	 *
	 * == BEGIN LICENSE ==
	 *
	 * Licensed under the terms of any of the following licenses at your
	 * choice:
	 *
	 *  - GNU General Public License Version 2 or later (the "GPL")
	 *    http://www.gnu.org/licenses/gpl.html
	 *
	 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
	 *    http://www.gnu.org/licenses/lgpl.html
	 *
	 *  - Mozilla Public License Version 1.1 or later (the "MPL")
	 *    http://www.mozilla.org/MPL/MPL-1.1.html
	 *
	 * == END LICENSE ==
	 *
	 * Script para alterar, incluir ou excluir categorias de emails
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');
 
 
session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}

include_once "config/config.php";
include_once "includes/funcoesUteis.php";

// *** Declarando Conexões com Banco de Dados ***

// cria a conexao com a base de dados
$conn = mysql_connect($host,$userDB,$pwdDB) or die("Falha de Conexão - Por favor tente mais tarde !");
// se houver falha na conexao ao servidor de database, encerra.

mysql_select_db($dataBase) or die("Falha na Base de Dados - Por favor, informe ao <a href=mailto:$webmaster>webmaster</a> !");
// se houver falha na selecao da database, encerra.


if ($_POST['enviar'])	{
	foreach ($_POST as $campo => $valor)	{
	
		// pega os tres primeiros caracteres pra usar depois na atulizacao dos itens
		$pre=substr($campo, 0, 3);
		if ($pre=='ctg')	{
			
			$valor=ufWords($valor);
			$id = substr($campo, -(strlen($campo)-3));
			$sql = "update mailing_categ set categ='$valor' where id=$id";			
			$query=mysql_query("update mailing_categ set categ=upper('$valor') where id=$id");
			} // end foreach
		}
	$newCat	= isset($_POST['categ']) ? addslashes($_POST['categ']) : null;
	
	if ($newCat) mysql_query("insert into mailing_categ values(null, upper('$newCat'), 0)");
	}
	
if ($_GET['excluir'])
	$query=mysql_query("delete from mailing_categ where id=" . $_GET['excluir']);

$query=mysql_query("select * from mailing_categ order by categ");

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">		
		<title>e-Mailing</title>
			<script language="javascript">			
				// Main editor scripts.
				var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
				document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
	</script>
		<script language="JavaScript">
			function excluir(id)	{
				x=confirm("Tem certeza que deseja excluir esta Categoria?\nUma vez excluída não poderá ser recuperada e\nos emails relacionados à ela ficarão orfãos.");
				if (!x) return;
				document.location.href="?excluir="+id;
				}
		</script>
	</head>

	<body topmargin="0" leftmargin="0">
		<div id="tudo">
			<?
			$page="Gerenciador de Categorias";
			include 'includes/topo.php';
			?>
			<form name="categorias" action="#" method="POST">
				<table border="0" cellspacing="2" cellpadding="2" align=center>
					<tr>
						<td style="margin-top:2px; border:1px solid #60c659; background:#fff; padding:2px 10px; width:50%">
							<p align="justify" style="font-size:10pt">
								A categoria é utilizada para campanhas direcionadas a um tipo em especial 
								ou seja, você poderá criar um mailing específico para cada categoria e no 
								e-Sender, selecionar a categoria correspondente, e 
								enviar somente para os emails cujo campo categoria corresponda ao selecionado.<br>
								Caso o e-Mailing seja publicado num site contendo diversos segmentos tipo loja virtual, 
								poderia ser incluído uma opção no site para que o usuario marque os assuntos ou 
								produtos de interesse e esses dados seriam a categoria do email.

								<b>Exemplo de categorias:</b> Farmacêutico, Motociclistas, Informática, etc...
							</p>

						<td vAlign=top style="margin-top:2px; border:1px solid #60c659; background:#fff; padding:2px 10px; width:50%">

						<table border="0" cellspacing="1" cellpadding="1" align="center" bgcolor="eeeee4" width="100%">
							<tr>
								<td align=center><b>ID</td>				
								<td class="tmenu"><b>Categoria</td>
								<td align=center>X</td>
							</tr>					

							<?
							if (!mysql_numrows($query)) echo"
								<tr><td colspan=3 align=center style='background:#c40000; color:#fff'><b>Nenhuma categoria encontrada!";

							While ($res = mysql_fetch_array($query)) {
								echo"
								<tr>
									<td align='center'>{$res['id']}
									<td><input type=text id=categ name=ctg{$res['id']} value='{$res['categ']}'/></td>
									<td align='center'>
										<a href='javascript:excluir({$res['id']});' title='Excluir esta categoria'>
										<img src='images/excluir' border='0' alt='Excluir esta categoria'></a>";
								}	// while	?>
								<tr>
									<td colspan=3><hr size=1>
								<tr>
									<td align='center'>?
									<td><input type=text id=categ style='background:#e7f7e7' name=categ value=''/></td>
									<td align='center'>
								<tr>
									<td colspan=3 align='center'><input type="submit" class="but" name="enviar" value="Gravar">
						</table>
					</table>
				</form>
			</div>
		</div>
	</body>
</html>
<?
@mysql_free_result($query);
mysql_close($conn);
?>