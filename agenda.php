<?
include 'global.php';
/*
  **Declarando Conexões com Banco de Dados***
  cria a conexao com a base de dados
  se houver falha na conexao ao servidor ou na seleção da database, encerra.
*/
/*	ja criado no global.php
	$conn = mysql_connect($host,$userDB,$pwdDB) or die("Falha de Conexão - Por favor tente mais tarde !");
	mysql_select_db($dataBase) or die("Falha na Base de Dados - Por favor, informe ao <a href=mailto:$webmaster>webmaster</a> !");
*/
require 'includes/navbar.php';

//function Mult_Pag ($maxRows, $maxLinks)
$mult_pag =new Mult_Pag(20, 5);
$query=$mult_pag->executar("select *, convert(char(10),data,103) as fdata from agenda order by data desc", $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);
//	debug($mult_pag);
?>

<div id="mainTop" style="background:url(images/mainAgenda.png) no-repeat; height:24px; margin:4px 0"></div>	
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr bgcolor="000066">
		 <td align=center><b style='color:#fff'>Data</td>
		 <td align=center><b style='color:#fff'>Assunto</td>
	</tr><?
		if (!$res_pag)	{	
			/* resultado não retornou registros	*/	
			echo"<tr><td align=center height=200 colspan=2>
			<b style=\"font-size:12pt;color:red\">Nenhum registro localizado!</td></tr>";
			}
		else {
			echo"<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";
			$todos_links = $mult_pag->Construir_Links("todos", "sim");
			echo"</div></td></tr>";
			
			$counter = 0;
			While ($counter < $mult_pag->numreg) {
				$rsQuery=mssql_fetch_object($query);
				$agenda="$rsQuery->descricao";
				echo "<tr>
					<td width='10%' vAlign=top style='border-bottom:1px solid #ccc'>$rsQuery->fdata</td>
					<td width='90%' style='border-bottom:1px solid #ccc'>$agenda</td></tr>";
				$counter ++;
				}	?>

			<tr bgcolor=#cccccc>
				<td colspan=2 align=center>
					<div id='bar'><? $todos_links = $mult_pag->Construir_Links("todos", "sim");?></div>
				</td>
			</tr><?
			}	?>
</table>
