<?
$conn_enq = mysql_connect($host,$userDB,$pwdDB);
mysql_select_db($dataBase);

$todo = $_GET['todo'];

#********** remove uma pergunta e suas respostas ************
if ($todo=="del") {	
	if ($id!= "")
		$query = mysql_query("delete from sbrafh_enquete where id_enquete=$id", $conn_enq);
	elseif (isset($id_p)) {
		$query = mysql_query("delete from sbrafh_enquete where id_enquete=$id_p", $conn_enq);
		$query = mysql_query("delete from sbrafh_enquete where id_parente=$id_p", $conn_enq);	}
	}

#*********** adiciona uma nova pergunta ****************
if ($todo=="add") {
	if (isset($id)) {
		$query = mysql_query("select * from sbrafh_enquete where id_enquete = $id", $conn_enq);
		$pergunta = mysql_result($query, 0, "texto");
		$fdata = mysql_result($query, 0, "data");
		$fdata = substr($fdata, 0, strlen($fdata)-6);
		$ano = substr($fdata,0, 4);
		$mes = substr($fdata,4, 2);
		$dia = substr($fdata,6, 2);
		$fdata = "$dia/$mes/$ano";
		
		$sql = "select count(*) as total from sbrafh_enquete where id_parente=$id";
		$total = mysql_query($sql, $conn_enq);
		$t = mysql_result($total, 0, 'total') + 1;
		}
	else	{
		$da = getdate();
		$fdata = substr("0".$da[mday], -2) . "/" . substr("0".$da[mon],-2) . "/$da[year]";
		}
		?>
		<script language="javascript">
			function validar(form) {
				if (form.campo.value =='') {
					alert("Preencha o campo Pergunta e/ou Resposta!");
					form.campo.focus();
					return false;
				}
				return true;
			}
	</script>

		<table width="80%" align=center class=boxborder cellspacing="2" cellpadding="2">
		<form name="perguntas" action="index.php" method="post" onsubmit="return validar(this);">
			<input type="hidden" name="id" value="<?= $id ?>">
			<tr bgcolor="#808080">
				<td align="center" colspan=4><b>Inserindo Perguntas/Respostas!</b></td>
			</tr>
			<tr>
				<? if (!isset($id)) { ?>
						<td class=titulo>Data</td>
						<td class=titulo>Pergunta</td>
						<td class=titulo></td>
						<td class=titulo></td></tr>
						<tr>
							<td class=center><?= $fdata ?></td>
							<?	}
				else {	?>
					<td class=titulo>Data</td>
					<td class=titulo>Pergunta</td>
					<td class=titulo></td>
					<td class=titulo></td></tr>
				<tr>
					<td class=center><?= $fdata ?></td>
					<td class=left><b><?= strtoupper($pergunta) ?></b></td>
					<td class=titulo></td>
					<td class=titulo></td></tr>
				<tr>
					<td class=titulo>Resposta #<?= $t ?>:</td>
					<?	}	?>									
					<td><input type="text" name="campo" size="50"></td>
					<td class=titulo align=center>				
						<input type=submit name=insert value="Inserir"></td>
					<td class=boxborder align=center><a href="index.php">
						<img src="images/voltar.gif" border=0 alt="Volta para Index"></a></td>
				</tr></form>
		</table><?	}
	else	{
	}


#**************** insere nova pergunta no banco de dados ***************
if (isset($_POST['insert'])) {	
	# inserindo nova pergunta
	
	$sql="select max(id_enquete) as max_id from sbrafh_enquete";
	$query = mysql_query($sql, $conn_enq);
	$result = mysql_fetch_array($query);

	# Se o valor máximo de ID das respostas for null, ID da resposta passa a ter valor 1
	if (!isset($result["max_id"])) {
		$novo_id = 1; }
	else
		{ $novo_id = $result["max_id"] + 1; }
	
	if ($id!= '')
		$parente = $id;
	else
		$parente = 0;
	
	$campo = $_POST['campo'];
		
	$query = mysql_query("insert into sbrafh_enquete values ($novo_id,$parente,'$campo',0,now())", $conn_enq);
	}
	
elseif (isset($_POST['edit']) ? $_POST['edit'] : $_GET['edit'])	{
	# todo=editar
		$texto = $_GET['texto'];
		$query = mysql_query("update sbrafh_enquete set texto='$texto' where id_enquete=$id", $conn_enq);
	}	
	

@mysql_free_result($query);
mysql_close($conn_enq);
?>