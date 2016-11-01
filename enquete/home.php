<?
$conn = mysql_connect($host,$userDB,$pwdDB);
mysql_select_db($dataBase) or die("Database não localizada!!");

$pagesize = 3;

if ($pagetogo == "") {
	$pagetogo = 0; }
	
# Seleciono do banco de dados todas as perguntas ordenadas por ordem decrescente de data

$sql="select * from $tablename where id_parente=0 order by data desc";
$query = mysql_query($sql, $conn);
if (!$query) {	?>
	<script>
		document.location.href="install.php";
	</script><? }
	
$maxRec = mysql_num_rows($query);
$maxPage = $maxRec / $pagesize;
$maxPage = (int)$maxPage;
if ($maxRec == $maxPage * $pagesize)
	$maxPage = $maxPage - 1;
?>


<table width="80%" bgcolor="#336699" align=center class=boxborder cellspacing="2" cellpadding="2">
	<tr>
		<td colspan=5 align=center  bgcolor="#FF9900"><font size="3"><b>Administração de Enquetes</b></font></td>
		<td align="center"><a href="javascript: history.back();"><img src="images/voltar.gif" border=0 alt="Voltar"></a><!-- linha 29 - fechar tag <a> --></td>
	</tr>
	<tr bgcolor="#000050"><font color="#ffffff">
		<td class=titulo>ID</td>
		<td class=titulo>Data</td>
		<td class=titulo>Pergunta</td>
		<td class=titulo>Resp</td>
		<td class=titulo>Editar</td>
		<td class=titulo>Apagar</td></font><!-- fechar tag <font> da linha 31-->
	</tr><?

	if (!$maxRec) {	?>
		<tr class=grey2>
			<td colspan=6 align=center bgcolor="#FF9900">Banco de dados vazio</td>
		</tr>
		<tr>
			<td class=center colspan=6>
				<a href="index.php?todo=add">Adicionar Enquete</a></td>
		</tr>
		<?
		
		# Se tiver perguntas no banco de dados, exibe as perguntas e suas respostas
			}
	else	{
		$counter = 0;
		$offset = ($pagetogo) * $pagesize;					
		mysql_data_seek($query, $offset);
								
		While ($counter < $pagesize && $counter+$offset < $maxRec)  {
			$pergunta = mysql_fetch_array($query);
			$data = substr($pergunta['data'], 0, strlen($pergunta['data'])-6);
			$ano = substr($data,0, 4);
			$mes = substr($data,4, 2);
			$dia = substr($data,6, 2);
			$fdata = "$dia/$mes/$ano";
			
			$sql = "select count(*) as total from $tablename where id_parente=" . $pergunta['id_enquete'];
			$total = mysql_query($sql, $conn);
			$t = mysql_result($total, 0, 'total');	
			?>	
				<tr bgcolor=#efefef>
				<td class=dir><?= $pergunta['id_enquete'] ?></td>
				<td class=dir><?= $fdata ?></td>
				<?	
					if ($todo=="edt" && $id_p==$pergunta['id_enquete']) {	?>
						<form name="edit" action="#" onsubmit="return validate(this);">
							<input type="hidden" name="id" value="<?= $id_p ?>">
							<input type="hidden" name="pagetogo" value="<?= $pagetogo ?>">
							<td colspan="2"><input type=text name=texto size=50 value="<?= $pergunta['texto'] ?>"></td>
							<td class=center>
								<input type=submit value="Alterar" name="edit" class="but"></td></form><?	}
					else	{	?>
						<td><b><?= $pergunta['texto'] ?></b></td>					
						<td>
							<a href="index.php?todo=add&id=<?= $pergunta['id_enquete'] ?>">
							<img src="images/botao_adicionar.gif" border=0 alt="Adicionar Resposta"> (<?= $t ?>)</a></td>
						<td class=center>
							<a href="index.php?todo=edt&id_p=<?= $pergunta['id_enquete'] ?>&pagetogo=<?= $pagetogo ?>">
							<img src="images/botao_editar.gif" border=0></a></td>
						<?	}	?>

				<td class=center>
					<a href="index.php?todo=del&id_p=<?= $pergunta['id_enquete'] ?>" OnClick="return confirm('Deseja excluir?')">
						<img src="images/botao_apagar.gif" border=0></a></td>
			</tr><?
			$counter ++;

			$query2 = mysql_query("select * from $tablename where id_parente=" .  $pergunta['id_enquete'], $conn);
			$x = 0;
			while ($result = mysql_fetch_array($query2)) {
				$x=$x+1;	?>
				<tr bgcolor=#ffffff>
					<td colspan="2" class=dir>Resposta # <?= $x ?></td>
					<? if ($todo!="edt" || $id!=$result['id_enquete']) {	?>
							<td colspan="2"><b><?= $result['texto'] ?></b></td>						
							<td class=center>
								<a href="index.php?todo=edt&id=<?= $result['id_enquete'] ?>&pagetogo=<?= $pagetogo ?>">
								<img src="images/botao_editar.gif" border=0></a></td><?	}
						else	{	?>
							<form name="edit" action="#" onsubmit="return validate(this);">
								<input type="hidden" name="id" value="<?= $id ?>">
								<input type="hidden" name="pagetogo" value="<?= $pagetogo ?>">
								<td colspan="2"><input type=text name=texto size=50 value="<?= $result['texto'] ?>"></td>
								<td class=center>
									<input type=submit value="Alterar" name="edit" class="but"></td>
							</form><?
							}	?>
						<td class=center>
							<a href="index.php?todo=del&id=<?= $result['id_enquete'] ?>" OnClick="return confirm('Deseja excluir?')">
					<img src="images/botao_apagar.gif" border=0></a></td>
				</tr>
			<?
				} #while 2
				
//				echo "<tr><td colspan=6><hr size=1></td></tr>";
//				die();	
		} #while 1	?><tr>
			<td colspan="6">
			<table width="100%" bgcolor="#ffffff" align="center">
				<tr>						
					<td class=center>
					<a href="index.php?todo=add">Adicionar Enquete</a></td>
			
	<?		
					echo "<form action=\"index.php\" method=\"POST\">\n";			
					echo "<td valign=middle width=\"25%\">";
					echo "<select name=pagetogo size=1 class=combo>";
					for ($x=0; $x<$maxPage+1; $x++) {
						$page=$x+1;
						echo "<option value=$x";
						if ($pagetogo==$x)
							echo " selected ";
						echo ">Página $page</option>"; }
					echo"</select> ";
					echo "<input type=submit name=submit value=\" ir \" class=but></td>";
					echo "</form>"; 
	?></table><!-- fechar tag <table> da linha 128 -->
				</td>
 			</tr>
 		</table></td>
 	</tr>	<?
	}  # else
	mysql_free_result($query);
	mysql_close($conn); ?>		
</table>

<script language="javascript">
	function validate(form) {
		if (form.texto.value =='') {
			alert("Preencha o campo!");
			form.texto.focus();
			return false;
		}
		return true;
	}
</script>