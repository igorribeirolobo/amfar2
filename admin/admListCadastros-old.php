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

require '../includes/navbar.php';
$mult_pag =new Mult_Pag(25,10);
// 1º verifica se o campo e cpf
$sql="select uid, nome, cpf, status, convert(char(10), dataReg, $dataFilter) as fData from cadastro ";

if ($_POST['search'])	{
	$cpf = ereg_replace("[' '-./ \t]",'',$_POST['procura']);
	$procura = strtoupper($_POST['procura']);

	if ($procura != "TODOS")
		$sql .="where nome like '%$procura%' or cpf='$cpf'";
	}
	
$sql .= " order by nome";
//echo "$sql";

$resultado=$mult_pag->executar($sql, $conn);
$res_pag=mssql_num_rows($resultado);

?>
<table border="0" cellpadding="0" cellspacing="1" width="100%">						
	<tr>
		<td style="background:url(images/admListCadastros.png) no-repeat; height:24px"></td>
</table>
<form action="index.php?act=lstCad" style="width:100%; margin:0" method="POST" name="encontra"/>
	<input type="hidden" name="cpf" value="">
	<table border="0" cellpadding="2" cellspacing="2" align="center">
		<tr>
			<td class="tright" align="right">Busca pelo Nome ou CPF</td>
			<td class="tleft"><input type="text" size="40" name="procura" value="TODOS"></td>
			<td class="tcenter"><input type="submit" name="search" value="OK "></td>
		</tr>
	</table>
</form>

<table border="0" cellspacing="2" cellpadding="2" width="100%">
	 <tr>
		  <td colspan="7" class="tcenter">
		  	Os Cadastros são mostrados em alfabética de nomes. 
		  	Use o navebar abaixo para avançar/retroceder páginas <b style="color:#c40000">Nome</b> para abrir o cadastro</td>
	 </tr>
	 <tr >
		  <td class="tTable">UID</td>
		  <td class="tTable">Nome/Empresa</td>
		  <td class="tTable">CPF/CNPJ</td>
		  <td class="tTable" align="center">Status</td>
		  <td class="tTable" align="center">Data</td>
	 </tr>
<?
if ($res_pag==0)	{	?> 
	<tr>
		<td colspan="5" align="center"><b style="color:#c40000">Nenhum registro localizado para <?= $procura?>!</b></td>
	</tr>
</table><?	}			

else	{		
	$counter = 0;
	while ($counter < $mult_pag->numreg)	{						
		$result=mssql_fetch_object($resultado);
		if ($result->status == -1)
			$status="EXCLUÍDO";
		elseif ($result->status == 0)
			$status="INATIVO";
		else
			$status="ATIVO";
			
		$strUid=substr("00000000$result->uid", -8);
			
		echo"
		<tr>
			<td class=tcenter>$strUid</td>
			<td class='tleft'>
					<a href=\"javascript:abrir('cadastro.php?uid=$result->uid')\"><b>$result->nome</b></a></td>
			<td class='tright' nowrap>" . formatCPF($result->cpf) . "</td>					
			<td class='tcenter'>$status</td>
			<td class='tcenter'>$result->fData</td>
		</tr>";
		$counter ++;
		}	// while

echo"<tr><td colspan=5 align=center><div id='bar'>";					
	$todos_links = $mult_pag->Construir_Links("todos", "sim");

echo"</div></tr></table>";
}	// else	
?>
