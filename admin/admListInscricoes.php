<?
if (!$_SESSION['admUser'])	{	?>
	<script>
		history.back();
	</script><?
	}
	
/*
	***Declarando Conexões com Banco de Dados***
	cria a conexao com a base de dados
	se houver falha na conexao ao servidor de database, encerra.
	se houver falha na selecao da database, encerra.
*/
$idCurso=isset($_POST['cbCursos']) ? $_POST['cbCursos'] : 0;

require '../includes/navbar.php';
$mult_pag =new Mult_Pag(25,10);
// 1º verifica se o campo e cpf

if ($idCurso > 0)
	$sql="SELECT
		c.uid,
		c.nome, 
		c.cpf, 
		i.id,		
		CONVERT(char(10), i.data, 103) AS data
		FROM alunosCursos i, cadastro c
		WHERE i.idCurso=$idCurso AND i.idAluno=c.uid";

else
	$sql="";

if ($sql !='') {
	$resultado=$mult_pag->executar($sql, $conn);
	$res_pag=mssql_num_rows($resultado);
	}
else
	$res_pag=0;
echo $sql;
?>
<table border="0" cellpadding="0" cellspacing="1" width="100%">						
	<tr>
		<td style="background:url(images/admListInscricoes.png) no-repeat; height:24px"></td>
</table>
<form action="index.php?act=lstIns" style="width:100%; margin:0" method="POST" name="comboBusca"/>
	<input type="hidden" name="cpf" value="">
	<table border="0" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td class="tright" align="right">Cursos</td>
			<td class="tleft">
				<select name="cbCursos" size="1">
					<option value="">Selecione</option><?
						$query=mssql_query("select *, convert(char(10), inicio, $dataFilter) as inicio from cursos where ativo=1 order by titulo");
						while ($res=mssql_fetch_object($query)) {
							if ($idCurso==$res->id)
								echo "<option value='$res->id' selected>$res->titulo</option>";
							else
								echo "<option value='$res->id'>$res->titulo</option>";
							}	?>
						</select>
					</td>
			<td class="tcenter"><input type="submit" name="search" value="OK "></td>
		</tr>
	</table>
</form>

<table border="0" cellspacing="2" cellpadding="2" width="100%">
	 <tr>
		  <td colspan="7" class="tcenter">
		  	Os Registros são mostrados em alfabética de nomes. 
		  	Use o navebar abaixo para avançar/retroceder páginas <b style="color:#c40000">Nome</b> para abrir o cadastro</td>
	 </tr>
	 <tr >
		  <td class="tTable">ID</td>
		  <td class="tTable">Nome do Aluno</td>
		  <td class="tTable">CPF do Aluno</td>		  
		  <td class="tTable" align="center">Data</td>
		  <td class="tTable" align="center">Pgto</td>
		  <td class="tTable" align="center">Status</td>
	 </tr>
<?
if ($res_pag==0) {
	if ($sql !='')
		echo"<tr>
			<td colspan='5' align='center'>
				<b style='color:#c40000'><b>Nenhum registro localizado para $procura!</b></td>
			</tr>
		</table>";
else
	echo"<tr>
		<td colspan='5' align='center'>
			<b style='color:#c40000'><b>Selecione o curso na caixa de seleção acima!</b></td>
		</tr>
	</table>";
	}

else	{		
	$counter = 0;
	while ($counter < $mult_pag->numreg)	{						
		$result=mssql_fetch_object($resultado);
		$numDoc=substr("00000$result->uid",-5) . substr("0$idCurso",-2) . '01';
		$sql="select id,
			convert(char(10), pgto, 103) as pgto
			from financeiro where numDoc='$numDoc'";
		$query2=mssql_query($sql);
		$resFinan=mssql_fetch_object($query2);	
	//	echo $sql;
		$status=($resFinan->pgto=='01/01/1900') ? '' : ' EFETIVADA';
		if ($resFinan->pgto=='01/01/1900') $resFinan->pgto=''; 			
		$strUid=substr("00000000$result->id", -5);
//		debug($result);		
		echo"
		<tr>
			<td class=tcenter>$strUid</td>
			<td class='tleft'><a href=\"javascript:abrir('cadastro.php?uid=$result->uid')\">$result->nome</a></td>
			<td class='tright' nowrap>" . formatCPF($result->cpf) . "</td>
			<td class='tcenter'>$result->data</td>
			<td class='tcenter'>$resFinan->pgto</td>
			<td class='tcenter'>$status</td>
		</tr>";
		$counter ++;
		}	// while

echo"<tr><td colspan=5 align=center><div id='bar'>";					
	$todos_links = $mult_pag->Construir_Links("todos", "sim");

echo"</div></tr></table>";
}	// else	
?>
