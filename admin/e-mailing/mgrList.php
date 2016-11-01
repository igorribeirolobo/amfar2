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
	 * Script para listar todos os emails da tabela mailing com sistema de navegação
	 * possui links nas principais colunas permitindo alternar entre ordenação ASC e DESC
	 * Possui link para o script mailForm.php quer permite buscar, alterar, inserir ou bloquear um e-mail.	 
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');

session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}

include "config/config.php";
include "includes/funcoesUteis.php";
// Checa numero da pagina

require 'includes/navbar.php';
$max_links=10;
$max_res=22;
$mult_pag =new Mult_Pag();
$mult_pag->num_pesq_pag=$max_res;
$mult_pag->max_links=$max_links;

	
// Conexao com o banco de dados através da API mysql.php
$conn = mysql_connect($host,$userDB, $pwdDB);
mysql_select_db($dataBase);

mysql_query("update mailing set email=lower(email)", $conn);

$ordem=isset($_GET['ordem']) ? $_GET['ordem'] : 'id';
$dir=isset($_GET['dir']) ? $_GET['dir'] : 'ASC';

$_SESSION['dir']  =$dir;
$_SESSION['ordem']=$ordem;

$sql = "select mailing.*, date_format(dataReg, '%d/%m/%Y %H:%m:%s') as criado,  date_format(dataEnvio, '%d/%m/%Y %H:%m:%s') as enviado from mailing order by $ordem $dir";
		
$resultado=$mult_pag->executar($sql, $conn,"otimizada","mysql") or die("erro $sql" . mysql_error());
$res_pag=mysql_numrows($resultado);

$dirIcon="<a href=\"javascript:goto()\"><img id='dir' src='images/toDown.gif' border='0' onmouseover='swap();' onmouseout='restore();'></a>";
// echo "$sql _SESSION['dir']={$_SESSION['dir']}";
?>
		
<html>

	<head>
		<meta http-equiv="Content-Language" content="pt-br">
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
		
		<title>E-Mailing|Manager List</title>
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
	
	
		<script language="javascript">
		
			function busca(id) {
				insWin = window.open('mailform.php?id='+id,"winIns","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,left=220,top=170,width=350,height=150");
				insWin.focus();
		  		}
		  		
		  	function swap() {
		  		document.getElementById('dir').src='images/toUp.gif';
		  		}
		  		
		  	function restore() {
		  		document.getElementById('dir').src='images/toDown.gif';
		  		}

		  	function goto() {
		  		var dir='<?= $dir ?>';
		  		if (dir=='ASC') dir='DESC';
		  		else dir='ASC';
		  		url='mgrList.php?ordem=<?=$ordem?>&dir='+dir;		  		
		  		document.location.href=url;
		  		}

		</script>

	</head>

<body>
	<div id="tudo">

		<?
			$page="Gerenciador de E-mails";
			include 'includes/topo.php';
		?>

		<form name="nav" style="margin:0" action="#" method="GET">
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			
			<tr>
				<td class="tfield">DATE:</td>
				<td class="tdata" nowrap><?= date('d/m/Y H:m:s') ?></td>			
				<td class="tdata" width=100%>Clique no ID do usuário para alterações</td>

				<td class="tdata">
					<input type="button" class="but" value="BUSCAR ou INSERIR" onClick="javascript:busca();"></td>
			</tr>
		</table>
		</form>

		<table border="0" id="mailList" width="100%" cellspacing="1" cellpadding="1">
			<tr>
				<td class=tfield><a href=?ordem=id>ID</a>  <?if ($ordem=='id') echo $dirIcon ?></td>
				<td class=tfield><a href=?ordem=nome>Nome</a> <?if ($ordem=='nome') echo $dirIcon ?></td>
				<td class=tfield><a href=?ordem=uf>UF</a>  <?if ($ordem=='uf') echo $dirIcon ?></td>
				<td class=tfield><a href=?ordem=email>E-mail</a>  <?if ($ordem=='email') echo $dirIcon ?></td>
				<td class=tfield><a href=?ordem=categ>Categoria</a> <?if ($ordem=='categ') echo $dirIcon ?></td>
				<td class=tfield style="color:#fff">Ultimo Envio</td>
				<td class=tfield><a href=?ordem=status>status</a>  <?if ($ordem=='status') echo $dirIcon ?></td>

			</tr>
			<?
		// Checa se existem registro no banco de dados

		if ($res_pag==0) {
			echo "<tr><td colspan='3' align='center'>";
			include 'home.html';

			echo"</td></tr></table>";
			exit;
			}


		$counter = 0;
		While ($counter < $res_pag)  {
			$result=mysql_fetch_object($resultado);
			$color="#efefef";
			
			if ($result->status < 0) $status="<img src='images/ledRed.gif'>";
			elseif ($result->status == 0) $status="<img src='images/ledYellow.gif'>";
			else $status="<img src='images/ledGreen.gif'>";

			if ($counter==0 || $counter % 2 ==0)
				echo "<tr bgcolor='#ffffff'>";
			else
				echo "<tr>";


			$id=$result->id;
			$strId=substr("000000$id",-6);
			$nome=ufWords($result->nome);
			
			if ($result->categ==0) $categoria="INDEFINIDA";
			else
				$categoria=mysql_result(mysql_query("select categ from mailing_categ where id={$result->categ}"), 0, 'categ');				
			

			echo"		
				<td id='linkId' style='width:35px'><a href='javascript:busca($id)'>$strId</a>				
				<td style='text-align:left'>&nbsp;$nome
				<td align=center>$result->uf
				<td style='text-align:left'>&nbsp;$result->email
				<td align=center>$categoria
				<td align=center>$result->enviado
				<td style='background:#ccc' align=center>$status";

			$counter ++;
			$coll ++;
			}	// while

		echo "
			</table><p align=center style='margin-top:0; background:#efefef'>";

		// pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano
			$todos_links = $mult_pag->Construir_Links("todos", "sim");
			for ($n = 0; $n < count($todos_links); $n++) {
				echo $todos_links[$n] . "&nbsp;&nbsp;";
				}
			echo "&nbsp;&nbsp;Total: <b>$mult_pag->total_reg Registros"; ?>
		<? include 'includes/rodape.html' ?>
	</div>
	<div style="display:none"><img src=images/toUp.gif border=0></div>
	<?

@mysql_free_result($query);
mysql_close($conn);
?>