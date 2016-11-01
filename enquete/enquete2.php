<?
 include '../global.php';

$conn = mysql_connect($host,$userDB,$pwdDB);
mysql_select_db($dataBase);
?>



<script language="javascript"><!--

	function res_enquete(op)	{
		document.location.href="resultado2.php"
		}
		
	function votar_enquete()	{
		voto=document.enquete.voto;
		perfil=document.enquete.perfil;
		if (voto[0].checked==false && voto[1].checked==false && voto[2].checked==false)	{
			alert("Selecione uma opção para voto");
			return false;
			}

		if (perfil[0].checked==false && perfil[1].checked==false && perfil[2].checked==false && perfil[3].checked==false)	{
			alert("Selecione uma opção para sua formação");
			return false;
			}

		document.enquete.submit();
		}		

	-->
</script>
<html>
	<head>
		<title>SBRAFH</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../style.css" rel="stylesheet" type="text/css">
	</head>
	
 <table border="0" cellspacing="1" cellpadding="1" width="100%" class="boxborder">
 	<tr>
 		<td align="center" colspan="2">
 			<img src="images/enquete.jpg"></td>
 	</tr>
	<tr bgcolor="#ff9900">
		<td align="center" colspan="2">
			<form name="enquete" method="post" action="resultado2.php?todo=votar" onsubmit="return votar()">
			<?
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_enquete =84",$conn);
				$result = mysql_fetch_array($query);	?>
			<font color="#ffffff"><b><?= $result["texto"]; ?></b></font></td>
			</tr>

		<?
		//	Seleciono todas as resposta por ordem crescente de entrada '
			$sql ="SELECT * FROM sbrafh_enquete WHERE id_parente=84 ORDER BY texto";
			$query = mysql_query($sql,$conn);
			$total = mysql_num_rows($query);
			
		// pega o total de perguntas	
			$counter = 0;
			while ($counter < $total) {
				$id_r = mysql_result($query, $counter, "id_enquete");
				$resposta = mysql_result($query, $counter, "texto");
				// Cria um conjunto de radio buttons com o valor do ID da resposta e o texto da resposta '
?>			<tr bgcolor="#efefef">
				<td align="center" width="10%">
					<input selected type="radio" name="voto" value="<?= $id_r ?>"></td>
				<td width="90%"><small><?= $resposta ?></small></td>
			</tr>
			<?
				$counter = $counter + 1;
				}	?>
				
			<tr bgcolor="#ff9900">
				<td colspan="2" align="center">


			<?
				$query = mysql_query("SELECT * FROM sbrafh_enquete WHERE id_enquete =88",$conn);
				$result = mysql_fetch_array($query);	?>
			<font color="#ffffff"><b><?= $result["texto"]; ?></b></font></td>
			</tr>

		<?
		//	Seleciono todas as resposta por ordem crescente de entrada '
			$sql ="SELECT * FROM sbrafh_enquete WHERE id_parente=88 ORDER BY texto";
			$query = mysql_query($sql,$conn);
			$total = mysql_num_rows($query);
			
		// pega o total de perguntas	
			$counter = 0;
			while ($counter < $total) {
				$id_r = mysql_result($query, $counter, "id_enquete");
				$resposta = mysql_result($query, $counter, "texto");
				// Cria um conjunto de radio buttons com o valor do ID da resposta e o texto da resposta '
?>			<tr bgcolor="#efefef">
				<td align="center" width="10%">
					<input selected type="radio" name="perfil" value="<?= $id_r ?>"></td>
				<td width="90%"><small><?= $resposta ?></small></td>
			</tr>
			<?
				$counter = $counter + 1;
				}	?>

			<tr>
				<td align="center" colspan="2" height="20">
					<input type="button" name="btVoto" value="Votar" onclick="javascript: votar_enquete()">
					&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript: res_enquete('')">
					<input name="btResult" type="button" value="Resultado" onclick="javascript: res_enquete()">
			</tr>
			</form>
		</table>
<?
mysql_free_result($query);
mysql_close($conn);
?>