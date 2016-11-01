<style>
	#forum td {border-bottom:1px solid #ccc}
	#forum .post{
		text-align:center;
		background:#efefef;
		vAlign:top;
		}
</style>


<table border=0 cellPadding=0 cellSpacing=0>
	<tr>
		<td>
			<img src="forum/images/forum_01.png" width=465 height=24 border="0"></td>
		<td><?if ($_SESSION['userId']) echo "<a href='?lnk=60&act=ins&id=0'>"?>
			<img src="forum/images/forum_02.png" width=101 height=24 border="0"></a></td>
	</tr>
</table>

<?php
if (!$_SESSION['userId']) {
	echo "<div align=center>
		<p style='margin:0; padding:4px; background:#c40000'>
			<b style='color:#fff'>ACESSO RESTRITO!!! Somente Usuários Cadastrados podem postar neste fórum!<p></div>";
	}
//debug($_POST);

if ($_POST['novo_topico']) {
	$id=isset($_POST['id']) ? $_POST['id'] : 0;
	
	$nome	= trim($_POST['nome']);
	$titulo	= trim($_POST['titulo']);
	$email	= trim($_POST['email']);
	$comentario	= trim(addslashes($_POST['comentario']));	
	
	if (stristr($comentario, 'href') && !stristr($email, 'amfar.com.br'))
		die("Mensagem inválida para este fórum!");
		
	$comentario=str_replace(chr(13).chr(10),'<br />',$comentario);
	
	mssql_query("insert into forum(id_resposta, titulo, comentario, data, nome, email, hits) values($id, '$titulo', '$comentario', getdate(), '$nome', '$email', 0)") or die("Erro");
	
	if ($id > 0)
		mssql_query("update forum set hits=hits+1 where id=$id");
		
	//@mssql_query("insert into mailing (dataReg, nome, email) values (getdate(), '$nome', '$email')");
	insertMailing($nome, '',$email);

	if ($id > 0) {
		// respondendo
		$query=mssql_query("select nome, email  from forum where id=$id");
		$rsQuery=mssql_fetch_assoc($query);
	

		include("mail/htmlMimeMail.php");
		$eBody = "
		<table border=0 cellspacing=2 cellpadding=2 width='100%'>
			<tr>
				<td valign=top align=center>
					<p style='font-size:8pt'>Olá <b>{$rsQuery['nome']}</b>,<br />
					{$nome} enviou-lhe uma mensagem<br />
					através do nosso Fórum!<br /><br />						
					<a href='$urlSite/?lnk=60&id=$id'/>
						Clique Aqui</a> para ler a mensagem.</p><br />
				</td>
			</tr>
		</table>";
		
		//	function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
		//	sendMail($mto, $mnf, $mmf, $ms, $mtb, $tit)		
		$mailsend = sendMail($rsQuery['email'], $nome, $email, 'AMFAR/Fórum', $eBody,'AMFAR|FÓRUM');
		}
	$urlBack="index.php?lnk=60&id=$id";
	echo "<script>document.location.href='$urlBack';</script>";
	die();
	}	// final do insert apos o submit

require 'includes/navbar.php';
$mult_pag =new Mult_Pag(20,10);

if($_GET['act']=='ins')	{	?>

	<form action="" style="margin:0; width:566px" method="POST" name="forum" id="forum" onsubmit="return checkForm('forum')">	
		<input type="hidden" name="process" value="0"/>
		<input type="hidden" name="id" value="<?=$_GET['id']?>"/>
		<table border="0" id="forum" cellpadding="2" cellspacing="2" width="100%">
			<tr>		
				<td align="center" colspan="2">
					<b class="Intertitulo1"><big>ATENÇÃO:</big></b><br>
					As mensagens postadas neste Fórum são de responsabilidade do próprios usuários.<br />
					Isentamo-nos de quaisquer responsabilidades sobre as mensagens aqui postadas.</td></tr>

			<tr>
				<td class="label">Nome:</td>
				<td><input type="text" size=50 name="nome" value="<?= $nome ?>" class="required" onchange="toUpper(this);"></td></tr>

			<tr>
				<td class="label">E-Mail:</td>
				<td><input type="text" size=50 name="email" id="email" value="<?= $email ?>" class="required"></td></tr><?

				if ($id==0)	{ echo"<tr>
					<td class='label'>Título:</td>
					<td><input type='text' size=50 name='titulo' id='titulo' value='' class='required' onchange='toUpper(this);'></td></tr>";
					}	?>

			<tr>
				<td valign="top" class="label">Comentário:</td>
				<td valign="top">
					<textarea name="comentario" id="comentario" cols="56" rows="5" value="" class="required"></textarea></td></tr>
			<tr>
				<td></td>
				<td align="center">
					<input type="submit" value="Enviar Comentário" id="btSend" name="novo_topico"></td>
			</tr>
		</table>
	</form>
	<table width=99% border=0 cellPadding=0 cellSpacing=0>
		<tr>
			<td>
				<img src="forum/images/forum3_01.png" width=468 height=24 border="0"></td>
			<td><a href="?lnk=60">
				<img src="forum/images/forum3_02.png" width=98 height=24 border="0"></a></td>
		</tr>
	</table>
	<?
	}

if($_GET['id'] > 0) {
	$sql = "select *, convert(char(10), data, $dataFilter) as fData from forum where id={$_GET['id']}";
	$query = mssql_query($sql, $conn);
	$topico = mssql_fetch_assoc($query);
	
	$sql = "select *, convert(char(10), data, $dataFilter) as fData from forum where id_resposta={$_GET['id']} order by data desc";
	$query=$mult_pag->executar($sql, $conn) or die("erro $sql" . mssql_error());
	$res_pag=mssql_num_rows($query);	?>
	


	<table border="0" id="forum" width="100%" cellspacing="2" cellpadding="2">
		<tr>
			<td valign="top" class=post><b><?= $topico['nome'] ?></b><br/>
				<small><?= $topico['fData'] ?></small></td>
				
			<td><img src="css/seta.jpg" align="absMiddle" border="0" width="13" height="11">
				<b class="intertitulo1"><?= $topico["titulo"] ?></b><br />
				<?= $topico['comentario'] ?></td>
				
			<td class="post" valign="top">
				<small>Post #<br /><?= substr("000000{$topico['id']}", -6) ?></small></td>
		</tr><?
		
		if ($res_pag > 0) {
			$counter = 0;
			while ($counter < $res_pag) {
				$rsQuery=mssql_fetch_object($query);
				$strId=substr("000000{$rsQuery->id}", -6);
				
				if ($counter%2) echo "<tr bgcolor=#efefef>";
				else echo "<tr bgcolor=#ffffff>";

				echo "<td><small>{$rsQuery->nome}</small><br />
				{$rsQuery->fData}</td>
				<td>{$rsQuery->comentario}</td>
				<td align='center' valign='top'>
					<small>Post #$strId</small></td></tr>";

				$counter ++;
				}	// while
				
			}
			
	echo"</table>
	<table border=0 width=100% cellspacing=0 cellpadding=0>
		<td>
			<td colspan=2 style='background:url(images/mainTitulos.png); text-align:center'>
				<div id='bar' style='float:left; height:24px'>";
	
		if ($res_pag > 0) {
			$todos_links = $mult_pag->Construir_Links("todos", "sim");
			for ($n = 0; $n < count($todos_links); $n++) {
				echo "{$todos_links[$n]}&nbsp;&nbsp;";
				}
			}

		echo"
		<td width='98' style='background:url(images/mainTitulos.png); text-align:center'>
			<a href='javascript:history.back()'><img src='forum/images/forum2_02.png' border='0' height='24'></a></td>
		<td width='98' style='background:url(images/mainTitulos.png); text-align:center'>";
		if ($_SESSION['userId']) echo "<a href='?lnk=60&act=ins&id={$_GET['id']}'>";
		
		echo"<img src='forum/images/forum2_03.png' border='0' height='24'></a></td>
	</table>";
	}

if (!$_GET['id']) {
	$sql="select *, convert(char(10), data, $dataFilter) as fData from forum where id_resposta=0 order by data desc";

	$query=$mult_pag->executar($sql, $conn) or die("erro $sql" . mssql_error());
	$res_pag=mssql_num_rows($query);		?>

	<table border="0" id="forum" cellspacing="2" cellpadding="2" width="100%">
		<tr bgcolor="#808080">
			<td align="center"><b style="color:#fff">Data</b></td>
			<td align="center"><b style="color:#fff">Tópico/Autor</b</td>
			<td align="center"><b style="color:#fff">Resp.</b></td>			
		<?

		if($res_pag==0)	{	
			/* resultado não retornou registros	*/	
			echo"<tr><td align=center height=200 colspan=4>
			<b style=\"font-size:12pt;color:red\">Nenhum tópico até o momento!</td></tr>";
			}

	else	{
		echo"<div id='bar' height:24px' style='background:#c0c0c0; text-align:center'>";
			$todos_links = $mult_pag->Construir_Links("todos", "sim");
		echo"</div>";
		
		$counter = 0;
		while ($counter < $mult_pag->numreg) {
			$rsQuery=mssql_fetch_object($query);
			if ($counter%2) echo "<tr bgcolor=#efefef>";
			else echo "<tr bgcolor=#ffffff>";

			echo "<td class=post>{$rsQuery->fData}</td>
			<td><a href='?lnk=60&id={$rsQuery->id}'><b>{$rsQuery->titulo}</a><br />			
			<small>Autor: {$rsQuery->nome}</small></td>
			<td align=center>{$rsQuery->hits}</td>			
			</tr>";        
			$counter ++;
			}	// while
		}
		
	echo"
	</table>
	<table border=0 width=100% cellspacing=0 cellpadding=0>
		<td>
			<td colspan=2 style='background:url(images/mainTitulos.png); text-align:center'>
				<div id='bar' height:24px'>";
				$todos_links = $mult_pag->Construir_Links("todos", "sim");

		echo"</div>
		<td width='101' style='background:url(images/mainTitulos.png); text-align:center'>";
		
		if ($_SESSION['userId']) echo "<a href='?lnk=60&act=ins&id=0'>";
		
		echo"
			<img src='forum/images/forum_02.png' border='0' height='24'></a></td>
	</table>";

	}
?>
