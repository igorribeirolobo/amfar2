<div id="mainTop" style="background:url(images/mainLinks.png) no-repeat; height:24px"></div><br />
<?
//if (!$_SESSION['userId'])	{
//	include 'usuarios/login2.php';
//	die();	}

// retorno do form

$id_cat=$_POST['cb_cat'];
$busca=$_POST['busca'];
	
if (!$id_cat)
	$sql="select l.*, c.categoria
		from links l, linkscat c
		where l.id_cat=c.id_cat and
		l.status=1 order by l.nome";

require 'includes/navbar.php';
$mult_pag =new Mult_Pag(16, 10);

$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);
//echo "sql=$sql $mult_pag->numreg";
?>

<table width="100%" border="0" cellspacing="2" cellpadding="2"><?
	if($res_pag==0)	{	
		/* resultado não retornou registros	*/	
		echo"<tr><td align=center height=200 colspan=2>
		<b style=\"font-size:12pt;color:red\">Nenhum registro localizado!</td></tr>";
		}
		
	else {
		echo"
		<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";				
		$todos_links = $mult_pag->Construir_Links("todos", "sim");				
		echo"</div></td></tr>";

		$counter=0;			
		while ($counter < $mult_pag->numreg) {
			$rsQuery=mssql_fetch_object($query);
		
		echo"
		<tr>
			<td height='0' class='Intertitulo2' style='border-bottom:1px solid #c0c0c0'>
			<b>{$rsQuery->nome}</b><br>{$rsQuery->categoria} 
			<font color='#999999'>{$rsQuery->comentario}</font>
			<a href='{$rsQuery->link}' target='_blank'/>
			<font color='#FF9900'>{$rsQuery->link}</font></a></td>";
			
			$counter++;
			}	// while

		echo"<tr bgcolor=#cccccc><td colspan=2 align=center><div id='bar'>";				
		$todos_links = $mult_pag->Construir_Links("todos", "sim");
		echo"</div></td></tr>";
		}	?>
</table>
