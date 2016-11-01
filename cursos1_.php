<style>
	p, P {margin:4px 0}
	#cursos td {border-bottom:1px solid #c0c0c0}
	#cursos p {
		background:url(images/h_arrow.gif) no-repeat 2px 2px;
		padding-left:14px;
		margin:4px 0;
		font:normal 9pt Tahoma, Verdana, Arial, Helvetica;
		color:#000066
		}
	.subTitulo {
		background:url(css/title_bg.jpg) repeat-x;
		text-align:left;
		font:bolder 10pt Tahoma, Verdana, Arial, Helvetica;
		color:#fff;
		padding:4px;
		text-transform:uppercase	
		}
</style>
<?

/* 
	********************************************************
	daqui pra baixo é mostrado no site principal link cursos
	********************************************************
*/
$sql="select idCurso, 
	convert(char(10), inicio, $dataFilter) as inicio, 
	titulo, status 
	from cursos 
	where (status={$_GET['st']} or status={$_GET['st']} + 2)
	AND ativo=1
	order by inicio, titulo";
require 'includes/navbar.php';
$mult_pag =new Mult_Pag(21,15);
$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);

if ($_GET['st']==0)
	$subTitulo=":: Cursos de Atualiza&ccedil;&atilde;o e Aperfei&ccedil;oamento 2&ordm; Semestre de 2008";
elseif ($_GET['st']==1)
	$subTitulo=":: Cursos de Especializa&ccedil;&atilde;o - 2&ordm; Semestre de 2008";

echo "
<div id='mainTop' style='background:url(images/mainCursos{$_GET['st']}.png) no-repeat; height:24px; padding-left:200px'></div>
<table width='100%' id='cursos' border='0' cellpadding='2' cellspacing='2'/>
	<tr>
		<td colspan='2' style='text-align:center'/>
			<b class='blue'>Da Teoria &agrave; Pr&aacute;tica, Esta &eacute; a Nossa Proposta</td>
	</tr>
	<tr>
		<td colspan='2' class='subTitulo'>$subTitulo</td>
	</tr>
	<tr bgcolor='000066'>
		<td align=center><b style='color:#fff'>In&iacute;cio</b></td>
		<td align=center><b style='color:#fff'>Curso</b></td>
	</tr>";

// cursos AMF
if ($_GET['st'] < 2) {		
	$counter=0;			
	while ($counter <  $mult_pag->numreg) {
	$rsCurso=mssql_fetch_object($query);
	if ($rsCurso->inicio != '') echo"
		<tr>
			<td style='padding:0 4px;text-align:center'>$rsCurso->inicio</td>
			<td style='padding:0 4px'><a style='background:none; padding:0' href='javascript:void(null)' 
				onclick=\"abrir('insCursos.php?idCurso=$rsCurso->idCurso')\"/>
				<span style='font-size:8pt'><b>$rsCurso->titulo</b><a/></td></tr>";

	else echo"
		<tr>
			<td style='text-align:center'>Breve</td>
			<td style='padding:0 4px'><span style='font-size:8pt'>$rsCurso->titulo</td></tr>";

	$counter++;
	}	// while

	echo"
	<tr>
		<td class='subtitulo' colspan=2>Informa&ccedil;&otilde;es Gerais</td></tr>
	<tr>
		<td colspan=2>
			<p>As inscri&ccedil;&otilde;es j&aacute; est&atilde;o abertas - <b>Vagas Limitadas</b></p>
			<p>Pagamentos em cheques, quando autorizados, ser&atilde;o depositados até 4 dias antes da data inicial do curso</p>
			<p>Os certificados s&oacute; ser&atilde;o entregues aos alunos caso os mesmos tenham 
				no m&iacute;nimo 75% de freq&uuml;&ecirc;ncia no curso.</p>
			<p>A AMF reserva-se o direito de cancelar os cursos caso não haja número mínimo de participantes.</p>
 <p> Baixe as seguintes apresenta&ccedil;&otilde;es.</p>
  <p> <a href='apresenta/EAS_2008.ppt'><b> Seguran&ccedil;a de pacientes - RENATA MAHFUZ DAUD GALLOTTI</b> </p>
 <p> <b> O estudo dos Erros Humanos - MARIO BORGES ROSA</b> </p>
 <p> <b> Eventos Adversos Relacionados a pratica medica - AJITH K. SANKARANKUTTY</b> </p>
	</td></tr>";
	}

else {
	if($res_pag==0) {	
		/* resultado não retornou registros	*/	
		echo"<tr><td align=center height=200 colspan=2>
			<b style=\"font-size:12pt;color:red\">Nenhum registro localizado!</td></tr>";
		}
	else {
		$counter=0;			
		while ($counter < $res_pag) {
			$rsQuery=mssql_fetch_object($query);					
			echo "<tr>
				<td width='15%' vAlign=top style='border-bottom:1px solid #ccc'>
					<img src='images/copylist.gif' align='absmiddle' border='0' alt='Abrir Evento'>
					<a href='javascript:void(null)' onclick=\"abrir('insCursos.php?idCurso={$rsQuery->idCurso}')\" title='Abrir Curso'/>
					<b><u>{$rsQuery->inicio}</a></td>
				<td width='85%' style='border-bottom:1px solid #ccc'><b>{$rsQuery->curso}</b></td></tr>";
			$counter++;
			}

		echo"
			<tr bgcolor=#cccccc>
				<td colspan=2 align=center><div id='bar'>";
				$todos_links = $mult_pag->Construir_Links("todos", "sim");				
				echo"</div></td>
			</tr>";
		}	// else
		echo"
			<tr>
				<td colspan=2>
					<p>Os cursos divulgados acima são de responsabilidade dos anunciantes.</p>
					<p>A AMF não se responsabiliza pelas informações mostradas.</p>
					<p>Os interessados deverão contactar diretamente o responsável pelo curso 
					através de telefone, email ou site quando publicados.</p>
					<p>A AMF reserva-se o direito de cancelar a divulgação de qualquer curso que esteja fora da área da saúde 
					ou que possa de alguma forma denegrir sua imagem junto à seus alunos, parceiros ou patrocinadores.		</td>
			</tr>";
	}
?>
</table>
