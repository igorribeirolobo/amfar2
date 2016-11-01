<style>
	#cursos td {border-bottom:1px solid #c0c0c0}
	#cursos p {
		background:url(images/h_arrow.gif) no-repeat 2px 2px;
		padding-left:14px;
		margin:4px 0;
		font:normal 9pt Tahoma, Verdana, Arial, Helvetica;
		color:#000066
		}
	.subTitulo {
		background:url(css/title_bg.jpg) repeat-x;
		text-align:left;
		font:bolder 10pt Tahoma, Verdana, Arial, Helvetica;
		color:#fff;
		padding:4px;
		text-transform:uppercase	
		}
</style>
<?
include 'global.php';
/*
  **Declarando Conexões com Banco de Dados***
  cria a conexao com a base de dados
  se houver falha na conexao ao servidor ou na seleção da database, encerra.
*/

// link veio da pagina
$idEvento=isset($_GET['idEvento']) ? $_GET['idEvento'] : $_POST['idEvento'];

if (isset($idEvento)) {
	require 'includes/funcoesUteis.php';
	$sql="select * from eventos where idEvento={$_GET['idEvento']}";
	$query=mssql_query($sql);
	$result=mssql_fetch_assoc($query);
//	debug($result);

	if (isset($_POST['btSend'])) {

		// formulario foi submetido entao prepara o e-mail para envio

	//	error_reporting(E_ALL);
		include("mail/htmlMimeMail.php");

		// prepara o corpo da mensagem e chama a funcao sendmail()

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
					leu o evento <b>$titulo</b> e acha que é de seu interesse.
					<br><br>
					Clique aqui para acessar o link indicado:
					<a href='$link'>$link</a><br><br>
			<tr>
				<td align=right class=boxBorder>Mensagem:</td>
				<td class=boxBorder colspan=3><b>$subject</td></tr>
		</table>";	

		$mailSend = sendMail($mailTo, $nameFrom, $mailFrom, 'AMFAR/Eventos', $eBody, 'Eventos');
		insertMailing($nameFrom, '',$mailFrom);
		insertMailing($nameTo, '',$mailTo);
	//	@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameFrom', '$mailFrom')");
	//	@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nameTo', '$mailTo')");
		}

	?>


	<html>
		<head>
			<title>AMFAR|Eventos</title>
			<script language="javascript" type="text/javascript" src="js/ajax.js"></script>		
			<link rel="stylesheet" href="js/formStyle.css" type="text/css">
			<link rel="stylesheet" href="css/style.css" type="text/css">
			<link rel="stylesheet" href="css/eventos.css" type="text/css">
		</head>
		<body onload="resizeTo(600,400)">
			<div id="header">
				<div id="titPage">:: AMF / Eventos da Área da Saúde</div>				
			</div>

			<div id="news">
				<div id="newsTitle" style="background:#efefef"><?=$result['titulo']?></div>
				<div id="corpo">Tipo de Evento:<?=$result['tipo']?></div>
				<div id="corpo">Descrição:<?=$result['descricao']?></div>
				<div id="corpo">Local: <?=$result['local']?></b></div>
				<div id="corpo">Realização:<b> <?=$result['realizacao']?></b></div>
				<div id="corpo">Informações:<b> <?=$result['informacoes']?></b></div>

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
						<input type='hidden' name='link' value='<?=$urlSite?>/lerEvento.php?idEvento=<?=$idEvento?>'/>
						<input type='hidden' name='titulo' value='<?=$result['evento'] ?>'/>
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
	</html><?
	die();
	}









// link veio do menu
$sql="select idEvento,titulo, convert(char(10), data, $dataFilter) as fData 
	from eventos where status={$_GET['st']} order by data desc";

require 'includes/navbar.php';
$mult_pag =new Mult_Pag(21,15);
$query=$mult_pag->executar($sql, $conn) or die("erro $sql" . mysql_error());
$res_pag=mssql_num_rows($query);
?>

<div id="mainTop" style="background:url(images/mainEventos<?=$_GET['st']?>.png) no-repeat; height:24px"></div>
<table border="0" cellpadding="2" cellspacing="2" width="100%" id="cursos">
	<tr bgcolor="000066">
		 <td align=center><b style='color:#fff'>Data</td>
		 <td align=center><b style='color:#fff'>Descrição do Evento</td></tr><?
	
	if($res_pag==0)	{	
		//* resultado não retornou registros	*/	
		echo"<tr><td align=center height=200 colspan=2>
			<b style=\"font-size:12pt;color:red\">Nenhum registro localizado!</td></tr>";
		}
	else {
		echo"<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";	
		$todos_links = $mult_pag->Construir_Links("todos", "sim");				
		echo"</div></td></tr>";

		$counter=0;			
		while ($counter <  $mult_pag->numreg) {
			$rsQuery=mssql_fetch_object($query);
			echo "<tr>
				<td width='15%' vAlign=top>
					<img src='images/copylist.gif' align='absmiddle' border='0' alt='Abrir Evento'>
					<a href='javascript:void(null)' onclick=\"abrir('eventos.php?idEvento={$rsQuery->idEvento}')\" title='Abrir Evento'/>
					<b><u>$rsQuery->fData</a></td>
				<td width='85%'>
					<a href='javascript:void(null)' onclick=\"abrir('eventos.php?idEvento={$rsQuery->idEvento}')\" title='Abrir Evento'/>
					<b><u>{$rsQuery->titulo}</u></b></a></td></tr>";
			$counter++;
			}
		
		echo"
			<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";			
				$todos_links = $mult_pag->Construir_Links("todos", "sim");
		echo"</div></td></tr>";
		}	// else
		
	echo"<tr><td class='subtitulo' colspan=2>Informa&ccedil;&otilde;es Gerais</td></tr><tr>";
	
	if ($_GET['st']==0) echo"
	<tr>
		<td colspan=2>
			<p>Para mais informações clique no evento.</td></tr>";
		
	else echo"
	<tr>
		<td colspan=2>
			<p>Os ventos divulgados acima são de responsabilidade dos anunciantes.</p>
			<p>A AMF não se responsabiliza pelas informações mostradas.</p>
			<p>Os interessados deverão contactar diretamente o responsável pelo evento 
			através de telefone, email ou site quando publicados.</p>
			<p>A AMF reserva-se o direito de cancelar qualquer divulgação de eventos que estejam fora da área da saúde 
			ou que possam de alguma forma denegrir sua imagem junto à seus alunos, parceiros ou patrocinadores.
		</td>
	</tr>";	?>
	
</table>
