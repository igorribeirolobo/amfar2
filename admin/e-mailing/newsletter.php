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
	 * - Script responsável pelo envio em lote.
	 * - Caso receba o campo e-mail, envia somente um para o email informado
	 	  neste caso os ID's são desconsiderados
	 * - Campo email vazio, será considerado o ID inicial e o ID final,
	 	  ambos colocados automaticamente pelo sistema mas podem ser alterados.
	 *	- O script envia o primeiro lote de no máximo 20 emails e aguarda 8 segs
	 	  para enviar o próximo lote e assim sucessivamente até chegar no último ID.
 */
 
 
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
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
 * Sample page.
 */
 
if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');


session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}


include 'config/config.php';
include 'includes/funcoesUteis.php';


if ($_GET['openFile']) {
	$myFile='';
	$fh=file("files/{$_GET['openFile']}");
		foreach($fh as $line) $myFile .= $line;
	$filename=$_GET['openFile'];
	}
	

if (isset($_POST))
   $postArray = &$_POST ;			// 4.1.0 or later, use $_POST
else
   $postArray = &$HTTP_POST_VARS ;	// prior to 4.1.0, use HTTP_POST_VARS
   
if ($postArray) {
	foreach ($postArray as $sForm => $value) {
		if (get_magic_quotes_gpc())
			$newsletter = stripslashes($value);
		else
			$newsletter = $value;

		$namefile = isset($_POST['namefile']) ? trim(checkFileName($_POST['namefile'])) : 'untitled.html';
		if (!strpos($namefile, '.html') && !strpos($namefile, '.HTML'))
			$namefile .= '.html';
		
		}

	if (!$newsletter)
		$myFile="Voce n&atilde;o criou nada para ser gravado!";
	
	else { 
	$myFile="
		<html>
			<head>
				<meta http-equiv='Content-Type' content='text/html; charset=windows-1252'>
				<title>$_coName: Mailing</title>
				<style>
					body {
						background-color: #ffffff;
						padding: 5px 5px 5px 5px;
						margin: 0px; }

					body, td {
						font-family: Arial, Verdana, Sans-Serif;
						font-size: 12px; }


					a[href] {
						color: #0000FF !important;	/* For Firefox... mark as important, otherwise it becomes black */ }

					p, ul, li {margin: 4px 0 }


					.Bold {font-weight: bold }

					.Title {
						font-weight: bold;
						font-size: 18px;
						color: #cc3300; }

					.Code {				
						border: #8b4513 1px solid;
						padding-right: 5px;
						padding-left: 5px;
						color: #000066;
						font-family: 'Courier New' , Monospace;
						background-color: #ff9933; }

				</style>

			</head>				
			<body>
				$newsletter
			</body>
		</html>";
		if (file_exists("files/$namefile"))
			unlink("files/$namefile");
			
		$fn=@fopen("files/$namefile", "w+");
		fputs($fn, $myFile);
		@fclose($fn);
		}
	}



include 'fckeditor/fckeditor.php';

?>
<html>
	<head>
		<title>E-Mailing|FCKeditor</title>		
		<meta http-equiv='Content-Type' content='text/html; charset=windows-1252'>
		<meta name="robots" content="noindex, nofollow">		
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
		<script language="javascript" type="text/javascript" src="ajax.js"></script>
		
		<script type="text/javascript">

			function FCKeditor_OnComplete(editorInstance) {				
				var oCombo = document.getElementById('cmbToolbars');
				oCombo.value = editorInstance.ToolbarSet.Name ;
				oCombo.style.visibility = '' ;
				}

			function ChangeToolbar(toolbarName) {
				window.location.href = window.location.pathname + "?Toolbar=" + toolbarName ;
				}

		</script>
		
	</head>
	
	<body>
		<div id="tudo" style="height:auto">
			<?	$page="Composer";
				include 'includes/topo.php'; ?>
			<form name="newsletter" action="#" method="POST">
				<div id="toolBar"style="width:780px" >
					Ferramentas: 
					<select id="cmbToolbars" onchange="ChangeToolbar(this.value);"/>
						<option value="Default" selected>Default</option>
						<option value="Basic">B&aacute;sica</option>
						<option value="Advanced">Avan&ccedil;ada</option>
					</select>
					
					Editar arquivo (html):
					<select name="openFile" id="cmbOpenFile" onchange="document.location.href='newsletter.php?openFile='+this.value"/>
						<option value="">Selecione o Arquivo</option>
					<?	$dh = dir ('files/');
						while ($entry = $dh->read()) {
							$fname = strtolower($entry);
							if (stristr($fname, 'htm')) {
								if ($_GET['openFile']==$fname)
									echo "<option value='$fname' selected>$fname</option><br>";
								else
									echo "<option value='$fname'>$fname</option><br>";
								}
							}	?>
					</select>					
					Nome do Arquivo: 
						<input class="tInput" style="width:200px; border:1px solid #808080" name="namefile" value="<?= $filename?>"></span>
				</div>
				<div style="width:780px">
				<?php
					// Automatically calculates the editor base path based on the _samples directory.
					// This is usefull only for these samples. A real application should use something like this:
					// $oFCKeditor->BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
					$sBasePath = $_SERVER['PHP_SELF'] ;
					$sBasePath = "$_coSite/e-mailing/fckeditor/";
					$oFCKeditor = new FCKeditor('FCKeditor1') ;
					$oFCKeditor->BasePath = $sBasePath ;
					$oFCKeditor->Height = '400' ;

					if (isset($_GET['Toolbar']))
						$oFCKeditor->ToolbarSet = htmlspecialchars($_GET['Toolbar']);
						$oFCKeditor->Value = "$myFile";
						$oFCKeditor->Create();	?>

					<p style="margin:0px; text-align:center">
						<input type="submit" class="but" value="Gravar" onclick="return newsSave()"></p>				
				</div>
			</form>
			<? include 'includes/rodape.html' ?>
		</div>
		
	</body>
</html>