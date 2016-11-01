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
	 * - Seleção de arquivos htm(l) ou image que será o corpo do email
	 * - Configuração de range de ID's para envio,
	 * - Seleção de uma categoria específica para envio ou para todos com exceção de bloqueados
	 * - Envio para somente um email para teste independente de estar na tabela ou não.
 */

if (!file_exists('config/config.php'))
	die('Arquivo de configuração não encontrado!');


session_start();
if (!$_SESSION['usrAdm']) {
	include 'index.php';
	die();
	}

include 'config/config.php';

$conn = mysql_connect($host,$userDB, $pwdDB);
mysql_select_db($dataBase);

// pesquisa o primeiro id valido
$query = mysql_query("select min(id) as minId from mailing where status >= 0", $conn);
$firstId = @mysql_result($query, 0, 'minId');

// pesquisa o ultimo id valido
$query = mysql_query("select max(id) as maxId from mailing where status >= 0", $conn);
$lastId = @mysql_result($query, 0, 'maxId');


// total de emails válidos
$query = mysql_query("select count(*) as total from mailing where status >= 0", $conn);
$total = @mysql_result($query, 0, 'total');

// pesquisa o ultimo id valido
$hoje=date('Y-m-d');
$sql="select MAX(id) as maxid from mailing where left(dataEnvio, 10) ='$hoje'";
$query = mysql_query($sql, $conn);
$maxid = @mysql_result($query, 0, 'maxid') + 1;
$nextId = ($maxid <= $lastId) ? $maxid : 1;

$toSend=false;


if ($_POST['sendThis']) {
	/*	
		*******************************************
		prepara o corpo do email
		busca no form os arquivos com check box='on'
		*******************************************
	*/
	$_SESSION['eBody']='';

	foreach ($_POST as $files => $value)	{
		$len=strlen($files);

		if (substr($files,0, 5)== 'body_')	{

			/*
				carrega o arquivo e monta o body do email
				arquivo para corpo do email so pode ser "*.html" ou "*.htm"
			*/

			$fname=substr($files,-(len-5));		

			if (stristr($fname,'html'))
				$fname[strlen($fname)-5]='.';

			elseif (stristr($fname,'htm'))
				$fname[strlen($fname)-4]='.';

			$fh=file("files/$fname");	
			foreach($fh as $line) $_SESSION['eBody'] .= $line;
			}	// if body

		elseif (substr($files,0, 5)== 'imag_')	{

			/*
				arquivo para anexo so pode ser
				"*.html","*.htm","*.jpg", "*.jpeg","*.gif" ou "*.png"
			*/

			$fname=substr($files,-(len-5));

			if (stristr($fname,'html'))	{
				$fname[strlen($fname)-5]='.';
				$type='text/html';	}

			elseif (stristr($fname,'jpeg'))	{
				$fname[strlen($fname)-5]='.';
				$type='image/jpg';	}

			elseif (stristr($fname, 'htm'))	{
				$fname[strlen($fname)-4]='.';
				$type='text/html';	}

			elseif (stristr($fname, 'gif'))	{
				$fname[strlen($fname)-4]='.';
				$type='image/gif';	}

			elseif (stristr($fname, 'jpg'))	{
				$fname[strlen($fname)-4]='.';
				$type='image/jpg';	}

			elseif (stristr($fname, 'png'))	{
				$fname[strlen($fname)-4]='.';
				$type='image/png';	}

			elseif (stristr($fname, 'doc'))	{
				$fname[strlen($fname)-4]='.';
				$type='text/doc';	}

			$_SESSION['eBody'] .="
				<tr>
					<td id='corpo'>
						<a href='$_coSite'>
							<img src='$_coSite/e-mailing/files/images/$fname' border='0'/></a>
					</td>
				</tr>";
			}	// if body

		} // end foreach
		
	$warning=str_replace('#_coName',$_coName, $_warning);
	$warning=str_replace("#_returnPath",$_returnPath, $warning);

	$_SESSION['eBody'] .="
			<tr>
				<td id='warning' bgcolor=#f0f0f0>{$warning}</td></tr>
			<tr>
				<td id=rodape><a href='$_coSite/e-mailing/mgrReturn.php?id='.>:: $_coName ::</a></i></td></tr>
			</table>
		</body>
	</html>\r\n";
	
	// $_SESSION['eBody'] contem o corpo do email

	
	// outros dados do form
	
	$_SESSION['eMail'] = isset($_POST['email']) ? $_POST['email'] : null;	// somente um para teste
	
	
	
	$_SESSION['enviado']=0;
	$_SESSION['nextID'] = $_POST['nextId'];	// iniciar a partir deste ID
	
	$_SESSION['lastID'] = $_POST['lastId'];	// ultimo ID válido
	
	if ($_SESSION['lastID']==0) {
		$query = mysql_query("select max(id) as maxId from mailing where status >= 0", $conn);
		$_SESSION['lastID'] = mysql_result($query, 0, 'maxId');
		}
		
	$_SESSION['limite']= isset($_POST['amount']) ? $_POST['amount'] : 10;
	
	$_SESSION['categ']= isset($_POST['cbCateg']) ? $_POST['cbCateg'] : 0;
	
	if ($_SESSION['categ'] > 0) {
		$query=mysql_query("select categ from mailing_categ where id={$_SESSION['categ']}");
		$_SESSION['categName']=mysql_result($query, 0, 'categ');
		}
	else $_SESSION['categ']='TODOS';
	
	$toSend=($_SESSION['nextID'] <= $_SESSION['lastID']) ? true : false;
	}	// fim do post
?>

<html>

	<head>
		<script language="javascript">			
			// Main editor scripts.
			var tStyle = /msie/.test(navigator.userAgent.toLowerCase()) ? 'style_IE.css' : 'style_FF.css';
			document.write('<link href="' + tStyle + '" type="text/css" rel="stylesheet" onerror="alert(\'Error loading \' + this.src);" />');
		</script>
		<script language="javascript" type="text/javascript" src="ajax.js"></script>
		<title>E-Mailing|Manager Send</title>
		<link rel="shortcut icon" type="image/ico" href="/favicon.ico" />
		

		<script language="javascript">
			
			function sender() {
				f=document.setIt;
				len=f.elements.length;
				url='sender.php';
			
				check=false;
				for (x=0; x < len-2; x++)	{					
					if (f[x].name.substr(0, 5) =='body_' && f[x].checked==true || f[x].name.substr(0, 5) =='imag_' && f[x].checked==true)
						check=true;
					}
				
				if (!check)	{
					alert("Selecione pelo menos um arquivo para envio!");
					return false;
					}
					
				nextId=parseInt(f.nextId.value);
				f.nextId.value=nextId;
					
				if (nextId==0 || isNaN(nextId)) {
					alert("Digite um ID válido para início");
					f.nextId.value='';
					f.nextId.focus();
					return false;
					}
					
				if (confirm("Antes de clicar em OK verifique:?\n\nOs ID's inicial e final estão corretos ?\nO(s) arquivo(s) selecionado(s) está(ão) correto(s) ?\nA categoria selecionada está correta?\n\nTecle [OK] para iniciar o envio."))
					return true;
				else
					return false;
				}
				
		</script>
	</head>

	<body>
		<div id="tudo">

		<?
			$page="Gerenciador de Envio";
			include 'includes/topo.php';
		?>
  
			<table border="0" cellpadding="2" cellspacing="2" width="100%">
				<tr>
					<td class="tfield">DATE:</td>
					<td class="tdata">Data de Envio: <?= date('d/m/Y') ?></td>      
					<td class="tfield">ID Inicial:</td>
					<td class="tdata"><b><?= substr("000000000$firstId",-79) ?></b></td>		
					<td class="tfield">ID Final:</td>
					<td class="tdata"><b><?= substr("000000000$lastId",-7) ?></b></td>
					<td class="tfield">Total:</td>
					<td class="tdata"><b><?= substr("000000000$total",-7) ?></b></td>
				</tr>
			</table>
		
			<form name="setIt" id="setIt" action="#" style="margin:2px 0" method="Post" onsubmit="return sender(this);">
				<input type="hidden" name="currId" value="0"/>
				<table border="0" cellpadding="1" cellspacing="1" width="100%">		
					<tr>
						<td bgcolor="#ffffff" valign="top" rowspan="4">
						<small>Este arquivo será o corpo do e-mail</small><br>
							<b>Selecione o arquivo HTML p/envio:</b><br>
						<?	$dh = dir ('files/');
							while ($entry = $dh->read()) {
								$fname = $entry;
								$pos = strpos($fname, '.');
								$ext = substr($fname, -(strlen($fname)-$pos-1));
								if ($ext == "html")
									echo "<input type=checkbox name=body_$fname>$fname</option><br>";
								}
							?>
						</td>
						<td bgcolor="#ffffff" valign="top" rowspan="4">
							<small>Este arquivo será adicionado no final do e-mail</small><br>	
							<b>Selecione Arquivo de Imagem p/envio:</b><br>
						<?	$dh = dir ('files/images/');
							while ($entry = $dh->read()) {
								$fname = $entry;
								$pos = strpos($fname, '.');
								$ext = substr($fname, -(strlen($fname)-$pos-1));				
								if ($entry != '.' && $entry != '..' && $entry != 'images')
									echo "<input type=checkbox name=imag_$fname>$fname</option><br>";
								}
							?>
						</td>
						<td bgcolor="#ffffff" valign="top" align=right>
							Inicio #: 
							<input type="text" id="nextId" name="nextId" style="text-align:center" size=8 value="<?= isset($_SESSION['nextID']) ? $_SESSION['nextID'] : $nextId ?>"/>					
							
							&nbsp;&nbsp;&nbsp;&nbsp;														
							Final #:
							<input type="text" id="lastId" name="lastId" style="text-align:center" size=10 value="<?= isset($_SESSION['lastID']) ? $_SESSION['lastID'] :  $lastId ?>"/>

							&nbsp;&nbsp;&nbsp;&nbsp;							
							Lote: <input type="text" id="amount" name="amount" value="10" disabled/>

					<tr>
						<td bgcolor="#ffffff" valign="top" align=right>
							E-mail-teste: 
								<input type="text" id="mailText" name="email">
					<tr>
						<td style="background:#eeeee4" valign="top" align=right>Categoria: 
							<select name="cbCateg" id="cbCateg" style="background:#fff; font-size:8pt">
								<option value="0">Todos</option>
							<?
							$query=mysql_query("select * from mailing_categ order by categ");
							while ($rsQuery=mysql_fetch_array($query)) {
								echo "<option value='{$rsQuery['id']}'/>{$rsQuery['categ']}</option>";
								}	?>
							</select>
							<input type="submit" class='but' name="sendThis" value="Enviar">
						</td>					
					</tr>
				</table>
			</form><?
			if (!$toSend) 
				include 'home.html';
			else
				echo "
					<div id='progresso' name='progresso'  style='width:1px;background-color:#990000'></div>
					<div id='porcentagem' style='font-family:verdana; font-size:12px; color:#FFFFFF'></div>
					<tr><td><div  id='nlinha' name='nlinha' style='font-family:verdana; font-size:12px; color:#990000'></div>
					<div  id='nlinhas' name='nlinhas' style='font-family:verdana; font-size:12px; color:#990000'></div>
					<iframe marginwidth='0' marginheight='0' name='working' 
						src='sender.php' frameborder='0' width='100%' scrolling='auto' height='382'>
					</iframe>";	?>
			
			<?	include 'includes/rodape.html' ?>
		</div>
	</body>
</html>