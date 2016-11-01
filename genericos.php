<?
require 'includes/navbar.php';
$mult_pag =new Mult_Pag(20,15);
$sql="select * from genericos order by produto";
$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);
?>
<script language="javascript">
	var lastId;

	function showThis(id) {
		var div = document.getElementById(id);
		if (lastId) lastId.className='invisivel';
		
		div.className='visivel';
		lastId=div;
		}
</script>

<style>
	#ttTab {
		background:url(images/title_bg.jpg) repeat-x;
		font: bolder 8pt Arial, Helvetica, Sans-Serif, Tahoma, Verdana;
		text-align:center;
		color:#fff;
		}
		
	#prod {
		border-bottom:1px solid #808080;
		border-right:1px solid #808080;
		padding:1px 4px
		}
</style>
<div id="mainTop" style="background:url(images/mainGenericos.png) no-repeat; height:24px"></div>
	<table border="0" cellpadding="4" cellspacing="1" width="100%">
		<tr>
			<td id="ttTab">Produto</td>
			<td id="ttTab">Classe Terapêutica</b></td>
			<?	
			if ($res_pag==0)
				echo "<tr><td colspan=6>Nenhum evento cadastrado !!!</td>";
			else {
				$counter=0;
				While ($counter < $mult_pag->numreg) {
					$result = mssql_fetch_object($query);					
					echo"
					<tr>
						<td id='prod'><a style='text-decoration:none' href=\"javascript:void(null)\" onclick=\"showThis('$counter')\"><b class='blue'>{$result->produto}</a></td>
						<td id='prod'>{$result->classe}</td></tr>
					<tr>
						<td colspan=2 bgcolor=#eeeee4>
							<table id='$counter' class=invisivel width='100%'>
								<tr>
									<td id='prod'><small>Concentração:</small><br />
										<b>{$result->concentracao}</b></td>
									
									<td id='prod'><small>Laboratório:</small><br />
										<b>{$result->laboratorio}</b></td>
										
									<td id='prod'><small>Referência:</small><br />
										<b>{$result->referencia}</b><br/>
										
									<td id='prod'><small>Forma Farmacêutica:</small><br />									
										<b>{$result->forma}</b></td>
								</tr>
							</table></td></tr>";
					$counter++;
					}
				}	?>
			</td>
		<tr>
			<td colspan=6 style="text-align:center; background:#efefef">
				 <div id='bar'>
				 		<? $todos_links = $mult_pag->Construir_Links("todos", "sim");?>
				</div>
		</tr>
	</table>
