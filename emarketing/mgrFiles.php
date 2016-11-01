<?php
/*
 * e-Mailing - Sistema automatizado para cria��o e envio de emails por lote
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
	 * Script respons�vel por upload de arquivos htm, html e imagens para o servidor
	 * incluindo a imagem para o topo e rodap� do e-mail.
	 * O script tamb�m permite a exclus�o de arquivos desnecess�rios.
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configura��o n�o encontrado!');
	

session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}
	
include 'config/config.php';

$conn = mysql_connect($host,$userDB, $pwdDB);
mysql_select_db($dataBase);

if ($_GET['excluir']) {
	unlink($_GET['excluir']);
	// excluir e faz reload para eliminar o GET caso o usuario pressione F5
	echo"<script>document.location.href='mgrFiles.php'</script>";
	}


$msg1='';
$msg2='';
if ($_POST['upload'])	{
		
	if ($_FILES['imgTop']['name']) {
		$folder		= $_FILES['imgTop'];	// Obt�m dados do upload
		$fName = $folder['name'];
		$tipo=explode('.', $fName);
		$tmp	= $folder['tmp_name'];
		if(move_uploaded_file($tmp, "images/topoEmail.$tipo[1]")) {
			$_bgTop="images/topoEmail.$tipo[1]";
			$msg0 = "Arquivo enviado com sucesso para pasta: [$_bgTop]";
			mysql_query("update mailing_setup set bgTop='$_bgTop'") or die("Erro " . mysql_error());
			}
		else
			$msg0 = "Arquivo $fName n�o p�de ser enviado!";
		}

	if ($_FILES['imgBottom']['name']) {
		$folder		= $_FILES['imgBottom'];	// Obt�m dados do upload
		$fName = $folder['name'];
		$tipo=explode('.', $fName);
		$tmp	= $folder['tmp_name'];
		if(move_uploaded_file($tmp, "images/rodapeEmail.$tipo[1]")) {
			$_bgBottom="images/rodapeEmail.$tipo[1]";
			$msg1 = "Arquivo enviado com sucesso para pasta: [$_bgBottom]";
			mysql_query("update mailing_setup set bgBottom='$_bgBottom'") or die("Erro " . mysql_error());			
			}
		else
			$msg1 = "Arquivo $fName n�o p�de ser enviado!";
		}

	if ($_FILES['files']['name']) {
		$folder		= $_FILES['files'];	// Obt�m dados do upload
		$fName = $folder['name'];
		$tmp	= $folder['tmp_name'];
		if(move_uploaded_file($tmp, "files/$fName"))	{					
			$msg2 = "Arquivo enviado com sucesso para pasta: [files/$fName]";
			// arquivo enviado deve ser feito um parse para corrigir os src das imagens
			$newPath="$_coSite/e-mailing/files/images/";
			
			$fn=@fopen("files/$fName", "r");
			$html = fread($fn, 4096);
			@fclose($fn);
			
			$html=str_replace('  ', ' ', $html);
			$html=str_replace('= ', '=', $html);
			$html=str_replace(' =', '=', $html);
			$counter=0;
			
			$pos1=strpos($html, 'src');
			while ($pos1 > 0) {
				$pos2=strpos($html, '.gif', $pos1);
				if (!$pos2)
					$pos2=strpos($html, '.GIF', $pos1);
					
				if (!$pos2)
					$pos2=strpos($html, '.png', $pos1);
				if (!$pos2)
					$pos2=strpos($html, '.PNG', $pos1);

				if (!$pos2)
					$pos2=strpos($html, '.jpg', $pos1);
				if (!$pos2)
					$pos2=strpos($html, '.JPG', $pos1);
					
				$offset=4;
					
				if (!$pos2) {
					$pos2=strpos($html, '.jpeg', $pos1);
					if (!$pos2)
						$pos2=strpos($html, '.JPEG', $pos1);
					$offset=5;
					}						

				// $pos2 indica a posi��o inicial da extens�o do arquivo
					
				$_src=substr($html, $pos1+4, $pos2-$pos1);
				$_image= basename($_src);
				
			//	echo('$_src  = ' . $_src . '<br>');
			//	echo('$_image  = ' . $_image . '<br>');

				if (substr($_src, 0, 1)=='"' || substr($_src, 0, 1)=="'")
					$_src=substr($_src, 1, strlen($_src)-1);
				
				$html=str_replace($_src, $newPath . $_image, $html);
				$pos1=strpos($html, 'src',$pos2);
				$counter++;
				}
				
			if ($counter > 0) {
				$fn=@fopen("files/$fName", "w");
				fputs($fn, $html);
				@fclose($fn);
				}
			}
		else
			$msg2 = "Arquivo $fName n�o p�de ser enviado!";
		}

	if ($_FILES['filesImgs']['name']) {		
		$images	= $_FILES['filesImgs'];	// Obt�m dados do upload
		$fName	= $images['name'];
		$tmp		= $images['tmp_name'];
		if(move_uploaded_file($tmp, "files/images/$fName"))
			$msg3 = "Arquivo $fName enviado com sucesso para pasta: [files/images/$fName]";
		else
			$msg3 = "Arquivo $fName n�o p�de ser enviado!";
		}
	}
?>

<html>

	<head>
		<title>E-Mailing|Manager Files</title>	
		<meta http-equiv='Content-Type' content='text/html; charset=windows-1252'>
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
		
		<script language="javascript">			
			function check(f)	{
				body	= f.files.value.toLowerCase();
				iBody	= f.filesImgs.value.toLowerCase();
				if (body!='') {					
					ext = body.indexOf('htm', 0)
					if (ext == -1) {						
						alert("O arquivo selecionado � inv�lido");						
						document.upload.files.value='';
						f.files.focus();
						return false;
						}
					}

				if (iBody!='') {					
					ext = iBody.indexOf('jpg', 0);					
					if (ext == -1)
						ext = iBody.indexOf('gif', 0);						
					if (ext == -1)
						ext = iBody.indexOf('png', 0);
					if (ext==-1) {
						alert("O arquivo selecionado � inv�lido");
						f.foldersImgs.value='';
						f.foldersImgs.focus();
						return false;
						}
					}

				if (count != 0)
					return true;
				alert('Nenhum arquivo selecionado!');
				return false;					
				}
		</script>
	</head>

	<body>
	<div id="tudo">
		<?
			$page="Gerenciador de Arquivos";
			include 'includes/topo.php';
		?>


			<table border="0" cellpadding="2" cellspacing="2" width="100%">
				<form method="POST" name="upload"  ENCTYPE="multipart/form-data" action="#" onsubmit="return check(this)">
				<input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
				<tr>
					<td rowspan="3" width="400">

						<table border="0" cellpadding="2" cellspacing="2" width="400" bgcolor="#ffffff">						
							<tr>
								<td align="right" style="border:1px solid #60c659; padding:4px">
									<p style="margin:8px; color:#808080; background:#efefef">
									<b>Selecione os arquivos para corpo do email (e-body)</b></p>
									HTML Files: htm/html gravado diretamente na pasta <b>files</b><br>
									IMAGES Files: gif/jpg/png gravado diretamente na pasta <b>files/images
									<b class=azul>
										HTML Files: <input type="file" class="tFile" name="files" /><br>
										IMAGES Files: <input type="file" class="tFile" name="filesImgs" /><br>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type="submit" class="but" name="upload" value="Enviar Arquivos"></td>
							</tr>
						</table>
					</td>
					
					<td bgcolor="#ffffff" valign="top" style="padding:4px">
						<p class="folders">files <b>*.html</b> (body) - <b>Clique no arq. p/excluir</b></p>
						<div id="folderFiles">
						<?
							$dh = dir ('files/');
							while ($entry = $dh->read()) {								
								$fname = $entry;															
								if(stristr(strtolower($entry),".htm"))
									echo"<p class='delFiles'><a href='?excluir=files/$fname'>$fname</p>";
								}
							?>
						</div>
					</td>
				<tr>
					<td bgcolor="#ffffff" valign="top" style="padding:4px">
						<p class="folders">files/images  <b>jpg gif png</b> (body) - <b>Clique no arq. p/excluir</b></p>
						<div id="folderFilesImages">
						<?
							$dh = dir ('files/images/');
							while ($entry = $dh->read()) {
								$fname = $entry;
								$preName= explode(".", $fname);									
								
								if($preName[1] == "jpg" || $preName[1] == "gif" || $preName[1] == "png")
									echo"<p class='delFiles'><a href='?excluir=files/images/$fname'>files/images/$fname</p>";
								}
							?>							
						</div>
					</td>

					<tr>
						<td align="center" colspan="2">&nbsp;<?= "$msg0<br>$msg1<br>$msg2<br>$msg3"; ?></td>
					</tr>
			  </table>
			</form>
			
			<? include'includes/rodape.html' ?>
			<div id="preview"></div>	
		</div>
	</body>
</html>
