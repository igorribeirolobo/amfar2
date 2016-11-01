<table width=100% border=0 cellPadding=0 cellSpacing=0>
	<tr>
		<td>
			<img src="images/guest_01.png" width=370 height=24 border="0"></td>
		<td>
			<img src="images/guest_03.png" width=98 height=24 border="0"></td>
		<td><a href="?lnk=61&act=ins">
			<img src="images/guest_02.png" width=98 height=24 border="0"></a></td>
	</tr>
</table>
<?
// debug($_GET);
//	debug($_POST);
// se veio o form grava a mensagem
if ($_POST['btGuest'])	{
	
	$nome=trim($_POST['nome']);
	$email=trim($_POST['email']);
	$message=trim(addslashes($_POST['mensagem']));
	
	if (stristr($message, 'href') && !stristr($email, 'amfar.com.br'))
		die("Mensagem inválida para este site!");
		
	$sql="insert into guestbook(nome, email, mensagem, data) values('$nome','$email','$message', getdate())";
	$query = mssql_query($sql) or die("Erro");
	//$query = mssql_query("insert into mailing(nome, email, dataReg) values('$nome','$email',getdate())") or die("Erro");
	insertMailing($nome, '',$email);

	include("mail/htmlMimeMail.php");
	$eBody = "
	<table border=0 cellspacing=2 cellpadding=2 width='100%'>
		<tr>
			<td valign=top align=center>
				<p style='font-size:8pt'>
				Mensagem enviada através do guestbook:<br /><br />{$message}
			</td>
		</tr>
	</table>";
		
	//	function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
	//	sendMail($mto, $mnf, $mmf, $ms, $mtb, $tit)		
	$mailsend = sendMail($eContato, $nome, $email, 'AMFAR/GuestBook', $eBody,'AMFAR|GuestBook');
	echo "<script>document.location.href='index.php?lnk=61';</script>";
	}

if ($_GET['act']=='ins') {
	// entrar mensagem	?>
	<div align=center>
		<form name="guestbook" id="guestbook" action="" method="post" onsubmit="return checkForm('guestbook')" />
			<input type="hidden" name="process" value="0" />
			<table cellSpacing="1" cellPadding="1" width="100%" border="0"/>
				<tr>
					<td align="center" colspan=2><b>Deixe sua mensagem</td></tr>
				
				<tr>
					<td class="label">Nome:</td>
					<td><input size="40" name="nome" class="required" onchange="toUpper(this)" /></td></tr>
				
				<tr>
					<td class="label">E-Mail:</td>
					<td><input class="required" size="40" id="email" name="email"/></td></tr>
				
				<tr>
					<td class="label">Mensagem:</td>
					<td><textarea class="required" name="mensagem" id="mensagem" rows="10" cols="40"></textarea></td></tr>
				
				<tr>
					<td colspan=2 align="center">			
						<input id="btSend" type="submit" value="Enviar Mensagem" name="btGuest"></td></tr>					
			</table>
		</form>
	</div>
	<table width=100% border=0 cellPadding=0 cellSpacing=0>
		<tr>
			<td>
				<img src="images/guest_01.png" width=370 height=24 border="0"></td>
				<td>
					<img src="images/guest_03.png" width=98 height=24 border="0"></td>
				<td><a href="?lnk=61&act=ins">
					<img src="images/guest2_02.png" width=98 height=24 border="0"></a></td>
		</tr>
	</table>
<?	
	}

else {

$sql = "select *, convert(char(10), data, $dataFilter) as fData from guestbook order by id desc";

require 'includes/navbar.php';
$mult_pag =new Mult_Pag(4, 10);
$query=$mult_pag->executar($sql, $conn) or die("erro $sql" . mssql_error());
$res_pag=mssql_num_rows($query);
?>

<table width="100%" cellspacing="4" cellpadding="4"/><?	
	if($res_pag==0) {	
		/* resultado não retornou registros	*/	
		echo"<tr><td align=center height=200 colspan=4>
			<b style=\"font-size:12pt;color:red\">Nenhuma mensagem até o momento!</td></tr>";
			}
	else {
		$counter = 0;
		while ($counter < $mult_pag->numreg) {
			$rsQuery=mssql_fetch_object($query);
			
			if ($counter%2) echo "<tr bgcolor=#efefef>";
			else echo "<tr bgcolor=#ffffff>";
			
			$strId=substr("000000{$rsQuery->id}",-6);
			
			$message=str_replace(chr(13).chr(10),'<br />', $rsQuery->mensagem);
			
			echo "
			<tr>
				<td style='text-align:center; padding:4px 0; border-bottom:1px solid #c0c0c0' vAlign=top>
					 Post # $strId<br /><br />{$rsQuery->fData}</b>
			 	</td>
				<td style='padding:4px 0; border-bottom:1px solid #c0c0c0'>
					 <p align=justify>$message<br />
					 	 <div align=right><small><b style='color:#336699'>:: {$rsQuery->nome}</div></p>
				</td>
			</tr>";
				
			$counter ++;
			}	// while
		}	// else
?>
	<tr>
</table>

<table width=100% border=0 cellPadding=0 cellSpacing=0>
	<tr>
		<td style='width:468px; background:url(images/mainTitulos.png); text-align:center'>
			<div id='bar'>
			<?$todos_links = $mult_pag->Construir_Links("todos", "sim"); ?>
		</td>
		<td><a href="?lnk=61&act=ins">
			<img src="images/guest_02.png" width=98 height=24 border="0"></a></td>
	</tr>
</table><?
	}
?>
