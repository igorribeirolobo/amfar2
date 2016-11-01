<?
include("../global.php");
include("../includes/funcoesuteis.php");


$id_r = $_GET['id_r'];
$id_p = $_GET['id_p'];
$todo = $_GET['todo'];

$enq = "enq$id_p";
$msg = "";
$conn_enq = mysql_connect($host,$userDB,$pwdDB);
if (!$conn_enq) {
	die ("Falha de Conexão!");	}
mysql_select_db($dataBase);


if ($todo=="votar") {
	// PHP adiciona um hit á resposta da enquete
	// recupera os dados dos campos hidden

	if (!$_COOKIE[$enq]) {
		$remotaddr = $_SERVER["REMOTE_ADDR"];
		$query=mysql_query("update sbrafh_enquete set hits= hits + 1 where id_enquete=$id_r",$conn_enq);
		
		$x = savelog($conn_enq);
		setcookie ("$enq", $remotaddr,time()+86400);
		$msg = "Seu voto foi computado com sucesso!";
			}
	else	{
		$msg="Você já votou nesta Enquete hoje !";
		}	
}

$sql="SELECT * FROM sbrafh_enquete WHERE id_enquete=$id_p";
$query = mysql_query($sql, $conn_enq);
$result = mysql_fetch_array($query);
$pergunta = $result["texto"];

mysql_free_result($query);
?>
<html>
	<head>
		<TITLE>LAB-Enquete</TITLE>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../style.css" type="text/css">
	</head>
		<body bgcolor="#FFFFFF" topmargin=0 leftmargin=0>
			<table width="100%" border="0" align=center bgcolor="#FFFFFF">
				<tr>
					<td colspan="3" width="100%">
						<img src="<?= $urlEnq ?>/images/resultado.jpg"></td>
				</tr>
			
			
				<?
					$sql="SELECT * FROM sbrafh_enquete WHERE id_parente=$id_p order by texto";
					$query = mysql_query($sql, $conn_enq);					
					$totreg = mysql_num_rows($query);
									
					$sql = "select sum(hits) as total from sbrafh_enquete where id_parente=$id_p";
					$query2 = mysql_query($sql, $conn_enq);
					$total = mysql_result($query2, 0, "total");
					mysql_free_result($query2);
					?>
				<tr>
					<td colspan="3" align="center" width="100%">
						<b><?= $pergunta ?></b></td>
				</tr>

				<tr>
					<td colspan="3" align="center"><font color=green><b>&nbsp;<? echo $msg ?></b></font></td>
				</tr>
				<tr><? $counter = 0;

				while ($counter < $totreg)	{
					$resposta = mysql_result($query, $counter, "texto");
					$hits = mysql_result($query, $counter, "hits");
				?>
					<tr>
						<td colspan="3" width="100%">
							<img src="images/percent.gif" border="0" height="1" width="100%"></td>
					</tr>
					<tr>
						<td width="150"><?= $resposta ?></td>
					<? if ($total > 0) { ?>
						<td width="80" class="boxborder"><img src="images/percent.gif" border="0" height="11" width="<?= sprintf("%.2f",100/$total * $hits)?>"></td>
						<td width="70" align="right"><?= sprintf("%.2f",100/$total * $hits) ?>% (<?= $hits ?>)</td>
					<?} else {?>
						
						<td width="80" class="boxborder"><img src="images/percent.gif" border="0" height="11" width=""></td>
						<td width="70" align="right">0% (<?= $hits ?>)</td>
					<? } ?>
						
					</tr>
					<tr><?
					// Passo á resposta seguinte
					$counter=$counter + 1;
					}	?>
				<tr>
					<td colspan="2" align="right" class="total" width="250">Total de Votos: </td>
					<td width="50" class="total"><b><?= $total ?></b></td>
				</tr>
				<tr>
					<td colspan="3" align="center" class="total"><a href="javascript: self.close();"><br>:: Fechar ::</a></td>
				</tr>					
			</table>
	</body>
</html>
<?
mysql_free_result($query);
mysql_close($conn_enq);
?>