<?
include("../global.php");
include("../includes/funcoesuteis.php");


$todo = $_GET['todo'];

$enq1 = "84";
$enq2 = "88";
$msg = "";

$voto=$_POST['voto'];
$perfil=$_POST['perfil'];


$conn = mysql_connect($host,$userDB,$pwdDB);
if (!$conn) {
	die ("Falha de Conexão!");	}
mysql_select_db($dataBase);


if ($todo=="votar") {
	// PHP adiciona um hit á resposta da enquete
	// recupera os dados dos campos hidden

	if (!$_COOKIE['enq84']) {
		$remotaddr = $_SERVER["REMOTE_ADDR"];
		$query=mysql_query("update sbrafh_enquete set hits= hits + 1 where id_enquete=$voto",$conn);
		$query=mysql_query("update sbrafh_enquete set hits= hits + 1 where id_enquete=$perfil",$conn);
		
		$x = savelog($conn);
		setcookie ("enq84", $remotaddr,time()+86400);
		$msg = "Seu voto foi computado com sucesso!";
		}
	else	{
		$msg="";
		}	
	}

?>
<html>
	<head>
		<TITLE>LAB-Enquete</TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../style.css" type="text/css">
	</head>
		<body bgcolor="#FFFFFF" topmargin=0 leftmargin=4>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" align=center>
				<tr>
					<td colspan="2" height="24" background="images/resultado.jpg">&nbsp;</td>
				</tr>
			<?
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_enquete=84", $conn);
				$result = mysql_fetch_array($query);
				$pergunta = $result["texto"];				
				$query = mysql_query("select sum(hits) as total from sbrafh_enquete where id_parente=84", $conn);
				$total = mysql_result($query, 0, "total");
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_parente=84 order by texto", $conn);					
				$totreg = mysql_num_rows($query);	?>				
				<tr>
					<td colspan="2" align="center">
						<b style="color:green"><?= $pergunta ?></b></td>
				</tr>

				<tr><? $counter = 0;

				while ($counter < $totreg)	{
					$resposta = mysql_result($query, $counter, "texto");
					$hits = mysql_result($query, $counter, "hits");
				?>

					<tr bgcolor="#eeeee4">
						<td colspan="2"><b><?= $resposta ?></b></td></tr>
					<tr>
					<? if ($total > 0) { ?>
						<td width="100" class="boxborder">
							<img src="images/percent.gif" border="0" height="11" width="<?= sprintf("%.2f",100/$total * $hits)?>"></td>
						<td align="right">
							<?= sprintf("%.0f",100/$total * $hits) ?>% (<?= $hits ?>)</td>
					<?} else {?>
						
						<td width="100" class="boxborder"><img src="images/percent.gif" border="0" height="11" width=""></td>
						<td width="150" align="right">0% (<?= $hits ?>)</td>
					<? } ?>
						
					</tr>
					<tr><?
					// Passo á resposta seguinte
					$counter=$counter + 1;
					}	?>


			<?
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_enquete=88", $conn);
				$result = mysql_fetch_array($query);
				$pergunta = $result["texto"];				
				$query = mysql_query("select sum(hits) as total from sbrafh_enquete where id_parente=88", $conn);
				$total = mysql_result($query, 0, "total");
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_parente=88 order by texto", $conn);					
				$totreg = mysql_num_rows($query);	?>				
				<tr>
					<td colspan="2" align="center">
						<b style="color:green"><?= $pergunta ?></b></td>
				</tr>

				<tr><? $counter = 0;

				while ($counter < $totreg)	{
					$resposta = mysql_result($query, $counter, "texto");
					$hits = mysql_result($query, $counter, "hits");
				?>

					<tr bgcolor="#eeeee4">
						<td colspan="2"><b><?= $resposta ?></b></td></tr>
					<tr>
					<? if ($total > 0) { ?>
						<td width="100" class="boxborder">
							<img src="images/percent.gif" border="0" height="11" width="<?= sprintf("%.2f",100/$total * $hits)?>"></td>
						<td width="150" align="right">
							<?= sprintf("%.0f",100/$total * $hits) ?>% (<?= $hits ?>)</td>
					<?} else {?>
						
						<td width="100" class="boxborder"><img src="images/percent.gif" border="0" height="11" width=""></td>
						<td align="right">0% (<?= $hits ?>)</td>
					<? } ?>
						
					</tr>
					<tr><?
					// Passo á resposta seguinte
					$counter=$counter + 1;
					}	?>


				<tr>
					<td align="center" colspan="2" class="total">Total de Votos: <b><?= $total ?></b></td>
				</tr>
			</table>
	</body>
</html>
<?
mysql_free_result($query);
mysql_close($conn);
?>