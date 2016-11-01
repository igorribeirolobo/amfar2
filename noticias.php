<?
/*
  **Declarando Conexões com Banco de Dados***
  cria a conexao com a base de dados
  se houver falha na conexao ao servidor ou na seleção da database, encerra.
*/

include 'global.php';

$_idNoticia=isset($_GET['idNoticia']) ? $_GET['idNoticia'] : $_POST['idNoticia'];
// chamado pelo link da pagina
if (isset($_idNoticia)) {
	include 'includes/funcoesUteis.php';
	
	// le a noticia na database
	$sql="select * from noticias where idNoticia=$_idNoticia";
//	echo $sql;
	$query=mssql_query($sql);
	$result=mssql_fetch_assoc($query);
	echo'<pre>';
	print_r($resut);
	echo'</pre>';

	if (isset($_POST['btSend'])) {	
	// formulario foi submetido entao prepara o e-mail para envio
	
	//	debug($_POST);
	
		include("mail/htmlMimeMail.php");	// prepara o corpo da mensagem e chama a funcao sendmail()
	
		$mailFrom = trim($_POST['mailFrom']);
		$nameFrom = trim($_POST['nameFrom']);
		$mailTo = $_POST['mailTo'];
		$nameTo = trim($_POST['nameTo']);
		$subject = trim($_POST['message']);
		$titulo = trim($_POST['titulo']);
		$link = $_POST['link'];
	
	$eBody="
	<table border=0 width=100% >
		<tr>
			<td class=boxBorder bgcolor=#ffffff align=center width=150>
				<img border=0 src='{$urlSite}/images/contato.jpg'></td>
			<td align=right class=boxBorder style='text-align:center'><br>
				Olá <b>$nameTo</b>, $nameFrom ($mailFrom) visitando nosso site 
				leu a matéria <b>$titulo</b> e acha que é de seu interesse.
				<br><br>
				Clique aqui para acessar a matéria indicada:
				<a href='$link'>$link</a><br><br>
		<tr>
			<td align=right class=boxBorder>Mensagem:</td>
			<td class=boxBorder colspan=3><b>$subject</td></tr>
			</table>";

		// envia o link da noticia para o usuario selecionado
		$mailSend = sendMail($mailTo, $nameFrom, $mailFrom, 'AMFAR/Notícias', $eBody, 'Notícias');

		insertMailing($nameFrom, '', $mailFrom);
		insertMailing($nameTo, '', $mailTo);

		//@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameFrom', '$mailFrom')");
		//@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameTo', '$mailTo')");
		}	?>

	<html>
		<head>
			<title>AMFAR|Notícias</title>
			<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
			<link rel="stylesheet" href="js/formStyle.css" type="text/css">
			<link rel="stylesheet" href="css/style.css" type="text/css">
			<link rel="stylesheet" href="css/noticias.css" type="text/css">
		</head>
		<body>
			<div id="header"><div id="titPage">:: AMF / Notícias da Área da Saúde</div></div>

			<div id="news">
				<div id="newsTitle"><?=$result['titulo']?></div>
				<div id="newsEye"><?=$result['subTitulo']?></div>			

				<div class="creditos">
					<?=$result['creditos']?> - Fonte: <?=$result['fonte']?> - Publicada em <?=implode("/", array_reverse(explode("-", substr($result['data'], 0, 10))))?>
				</div>

		<?		if ($result['foto'])	echo"	
					<div id='newsExtras'>
						<span class='newsPhotographer'>{$result['fotografo']}</span>
						<img style='border:1px solid #808080' align=center src='{$result['foto']}' width=220 border=0>
						<span class='newsLegenda'>{$result['legenda']}</span>
					</div>";
				//	$materia=str_replace(chr(13).chr(10), '<br>', $result['materia']);
				$materia=str_replace(array('&lt;','&gt;'),array('<','>'), $result['materia']);	?>

				<p><?= "$materia" ?></p>
				<br><?
				if (trim($result['url'])!='') include $result['url'];
				
				if ($result['link']) echo"
					<p>Link: <a href='{$result['link']}' target='_blank'/>{$result['link']}</a></p>";	?>

				<div id="newsOptions">
					<img src="images/printer.png" border="0" align="absMiddle" onclick="self.print()">
						&nbsp;<a href="JavaScript:void(null)" onclick="self.print()">Imprimir</a>
					<img style="margin: 0 10px" src="images/email.gif" border="0" align="absMiddle">
						<a href="#formEnvie">Enviar&nbsp;para&nbsp;amigos</a>
				</div><?


				if ($mailSend =='E-mail enviado com sucesso') echo"
					<table border=0 align=center bgcolor=#efefef style='margin:10px; border:1px solid #336699'>
						<tr>
							<td align=center nowrap style='padding:6px 20px'>
									Prezado(a) <b>{$nameFrom}</b>, o link foi enviado com sucesso.</b></font><br><br>
									<b>Obrigado por utilizar nossos servi&ccedil;os!</b><br><br>
									Se ficou satisfeito, indique-nos para seus amigos!
							</td>
						</tr>
					</table>";	?>



				<a name='formEnvie'>
				<div align=center>					
					<form name='envieAmigo' id='envieAmigo' method='POST' action='#' onsubmit="return checkForm('envieAmigo')"/>
						<input type='hidden' name='link' value='<?=$urlSite?>/lerNoticia.php?idNoticia=<?=$result['idNoticia']?>'/>
						<input type='hidden' name='titulo' value='<?=$result['titulo'] ?>'/>
						<input type='hidden' name='process' value='0'/>
						<table cellpadding='1' cellspacing='1' width=100%>
							<tr>
								<td colspan=2 align=center>
									<div id="header"><div id="titPage">Envie este link a um(a) amigo(a)</div></div>
							<tr>
							<tr>
								<td class='label'>Seu nome:
								<td><input name='nameFrom' style='width:280px' onchange='toUpper(this)' value="<?=$nameFrom?>" class='required'/>
							<tr>
								<td class='label'>Seu E-mail:
								<td><input name='mailFrom' id='email' style='width:280px'  value="<?=$mailFrom?>"  class='required'/>
							<tr>
								<td class='label'>Nome amigo(a):
								<td><input name='nameTo' style='width:280px' class='required' onchange='toUpper(this)'/>
							<tr>
								<td class='label'>E-mail amigo(a):
								<td><input name='mailTo' id='email' style='width:280px' class='required'/>
							<tr>
								<td class='label'>Comentários:					
								<td>
									<textarea id='message' name='message' rows='5' style='width:280px' class='required'/><?=$subject?></textarea>
							</tr>
							<tr>
								<td colspan='2' align=center>			  
								<input type='submit' id='btSend' name='btSend' value='Enviar'/>
							</tr>
						</table>
					</form>
				</div><!-- align -->
			</div><!-- div news -->
		</body>
	</html>
<?	die();
	}






/* chamado pelo link do menu	*/

$sql="select idNoticia, titulo, link, convert(char(10),data, 103) as fdata from noticias where status=0 order by data desc";

require 'includes/navbar.php';
$mult_pag =new Mult_Pag(21,15);

$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);;
?>

<div id="mainTop" style="background:url(images/mainNoticias.png) no-repeat; height:24px; padding-left:200px">
	<p style="margin-top:4px"><b>Clique na data para abrir a notícia</div>	
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr bgcolor="000066">
		 <td align=center><b style='color:#fff'>Data</td>
		 <td align=center><b style='color:#fff'>Título</td>
	</tr><?
	if($res_pag==0)	{	
		/* resultado não retornou registros	*/	
		echo"<tr><td align=center height=200 colspan=2>
		<b style=\"font-size:12pt;color:red\">Nenhum registro localizado!</td></tr>";
		}
	else {
		echo"<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";			
				
		$todos_links = $mult_pag->Construir_Links("todos", "sim");
				
		echo"</div></td></tr>";

		$counter=0;			
		while ($counter < $mult_pag->numreg) {
			$rsQuery=mssql_fetch_object($query);
			echo "<tr>
				<td width='15%' vAlign=top style='border-bottom:1px solid #ccc'>					
					<b>$rsQuery->fdata</td>
				<td width='85%' style='border-bottom:1px solid #ccc'>
					<a href='javascript:void(null)' onclick=\"abrir('noticias.php?idNoticia={$rsQuery->idNoticia}')\" title='Abrir notícia completa'/>
						<img src='images/copylist.gif' align='absmiddle' border='0' alt='Abrir notícia completa'></a>
					<a href='javascript:void(null)' onclick=\"abrir('noticias.php?idNoticia={$rsQuery->idNoticia}')\" title='Abrir notícia completa'/>
					 <b><u>{$rsQuery->titulo}</u></b></a></td></tr>";
			$counter++;
			}	?>

		<tr bgcolor=#cccccc>
			<td colspan=2 align=center>
				<div id='bar'><?			
					$todos_links = $mult_pag->Construir_Links("todos", "sim");
					for ($n = 0; $n < count($todos_links); $n++) {
						echo "{$todos_links[$n]}&nbsp;&nbsp;";
						}	?>
				</div>
			</td>
		</tr><?
		}	?>
</table>
