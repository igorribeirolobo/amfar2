<?
// include 'global.php';

$conn_enq = mysql_connect($host,$userDB,$pwdDB);
mysql_select_db($dataBase);


if ($id_enquete == "") {
	$sql="Select max(id_enquete) as id_max from sbrafh_enquete";
	$query = mysql_query($sql,$conn_enq);
	$result = mysql_fetch_array($query);
	$id_enquete = $result["id_max"];
	mysql_free_result($query);
}

$sql = "SELECT * FROM sbrafh_enquete WHERE id_enquete =$id_enquete";
$query = mysql_query($sql,$conn_enq);
$result = mysql_fetch_array($query);
?>



<script language="javascript">
<!--

	function res_enquete(op)	{
		id_p = document.enquete.id_p.value;
		id_r = document.enquete.id_r.value;

		url="enquete/result_enq.php?todo="+op+"&id_p="+id_p+"&id_r="+id_r;
		ResultWin = window.open(url,"winresult","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,left=0,top=0,width=320,height=250");
		ResultWin.focus();
		}


	function voto(id)	{
		document.enquete.id_r.value = id;
	//	res_enquete('votar');
		}
		
	function votar_enquete()	{
		if (document.enquete.id_r.value == "")
			alert("Por favor, selecione uma opção !");
		else	
			res_enquete('votar'); }		

-->
</script>
 <table border="0" cellspacing="1" cellpadding="1" width="160" class="boxborder">
 	<tr>
 		<td align="center" colspan="2">
 			<img src="<?= $urlEnq ?>/images/enquete.jpg"></td>
 	</tr>
	<tr bgcolor="#ff9900">
		<td align="center" colspan="2">
			<form name="enquete" method="post" onsubmit="return votar()">
			<input type="hidden" name="id_p" value="<?= $id_enquete ?>">
			<input type="hidden" name="id_r" value="">
			<font color="#ffffff"><b><?= $result["texto"]; ?></b></font></td>
			</tr>

		<?
		//	Seleciono todas as resposta por ordem crescente de entrada '
			$sql ="SELECT * FROM sbrafh_enquete WHERE id_parente=$id_enquete ORDER BY texto";
			$query = mysql_query($sql,$conn_enq);
			$total = mysql_num_rows($query);
			
		// pega o total de perguntas	
			$counter = 0;
			while ($counter < $total) {
				$id_r = mysql_result($query, $counter, "id_enquete");
				$resposta = mysql_result($query, $counter, "texto");
				// Cria um conjunto de radio buttons com o valor do ID da resposta e o texto da resposta '
?>			<tr bgcolor="#efefef">
				<td align="center" width="10%">
					<input selected type="radio" name="resposta" value="<?= $id_r ?>" onClick="voto(<?= $id_r ?>);"></td>
				<td width="90%"><small><?= $resposta ?></small></td>
			</tr>
			<?
				$counter = $counter + 1;
				}	?>
			<tr>
				<td align="center" colspan="2" height="20">
					<input class="botao_novo" type="button" value="Votar" onclick="javascript: votar_enquete()">
					&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript: res_enquete('')">
					<input class="botao_novo" type="button" value="Resultado" onclick="javascript: res_enquete()">
			</tr>
			</form>
		</table>
<?
mysql_free_result($query);
mysql_close($conn_enq);
?>