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
	li{font-size:12px}
</style>
<?

/* 
	********************************************************
	daqui pra baixo é mostrado no site principal link cursos
	********************************************************
*/
$sql="select idCurso, 
	convert(char(10), inicio, $dataFilter) as finicio, 
	titulo, status 
	from cursos 
	where (status={$_GET['st']} or status={$_GET['st']} + 2)
	AND ativo=1
	order by inicio";
//echo "$sql";
require 'includes/navbar.php';
$mult_pag =new Mult_Pag(21,15);
$query=$mult_pag->executar($sql, $conn) or die("erro $sql");
$res_pag=mssql_num_rows($query);

if ($_GET['st']==0 || $_GET['st']==2)
	$subTitulo=":: Cursos de Atualiza&ccedil;&atilde;o e Aperfei&ccedil;oamento - 2013";
elseif ($_GET['st']==1 || $_GET['st']==3)
	$subTitulo=":: Cursos de Especializa&ccedil;&atilde;o - 2013";

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
		<td align=center><b style='color:#fff'>Início</b></td>
		<td align=center><b style='color:#fff'>Curso</b></td>
	</tr>";

// cursos AMF
if ($_GET['st'] < 2) {		
	$counter=0;			
	while ($counter <  $mult_pag->numreg) {
	$rsCurso=mssql_fetch_object($query);
	if ($rsCurso->finicio != '') echo"
		<tr>
			<td style='padding:0 4px;text-align:center'>$rsCurso->finicio</td>
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
			
			<p style='background:none'>Prezado Aluno,</p>
			
			<p style='background:none;text-align:justify'>Aproveitamos para reafirmar o nosso compromisso e empenho em manter a qualidade do serviço 
				oferecido pela Associação Mineira de Farmacêuticos em parceria com o Centro Universitário Newton Paiva, que tem o 
				reconhecimento e respeito da comunidade acadêmica a nível nacional.</p>
			
			
			<p><b style='color:red'>Para assegurar a sua vaga junto a AMF, você deverá:</b>
				<ol style='margin-top:0'>
					<li>Preencher a ficha de inscrição;</li>
					<li><b>Imprimir e pagar o boleto</b> referente à primeira parcela (nos casos de cursos com pagamento parcelado);</li>
					<li><b>Imprimir e assinar o contrato de prestação de serviços</b> (para cursos de especializações);</li>
					<li><b>Enviar para a AMF</b> os documentos necessários para efetivar a matrícula:<br />
						<b>Contrato assinado<br />
						Currículo Vitae mais histórico escolar da graduação<br />
						Uma foto 3 x 4<br />
						Cópia do Certificado de Graduação Autenticada<br />
						Cópia do CPF e Cópia do RG</b>					
					</li>
					<li><b style='color:red'>Caso o pagamento da primeira parcela para cursos de especialização 
						não seja pago dentro do prazo estipulado, a inscrição será cancelada sem qualquer aviso; 
						exceto nos casos de negociação prévia do aluno com a AMF antes do prazo estipulado.</b>;
					</li>
				</ol>
			</p>
			<p><b style='color:red'>As vagas serão reservadas somente para inscrições efetivadas até 15 dias antes 
				do início do curso. Após este prazo não aceitaremos qualquer reclamação ou solicitação de 
				prorrogação para pagamento.</b></p> 
			<p>Os certificados s&oacute; ser&atilde;o entregues aos alunos caso os mesmos tenham 
				no m&iacute;nimo 75% de freq&uuml;&ecirc;ncia no curso.</p>
			<p>A AMF reserva-se o direito de cancelar os cursos caso não haja número mínimo de participantes.</p>
			<p style='background:none;font-weight:bolder;font-size:13px'>Estamos esperando por você!</p>
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
					ou que possa de alguma forma denegrir sua imagem junto à seus alunos, parceiros ou patrocinadores.
				</td>
			</tr>";
	}
	echo"
	<tr>
		<td class='subtitulo' colspan=2 style='display:none'>Clique nos links abaixo para baixar os PDF's das apresentações</td></tr>
	<tr>
		<td colspan=2 style='display:none'>
			<p><a href='apresenta/AcreditacaoHospitalarDrRenatoAbril2009.pdf' target='_blank'>Acreditação Hospitalar Dr. Renato Abril 2009</a></b></p>
			<p><a href='apresenta/EventosadeincidentesDraRenataIIForumAbril2009.pdf' target='_blank'>Eventos ad e incidentes Dra Renata II Forum Abril 2009</a></b></p>
			<p><a href='apresenta/Futuro_da_prevencao_Katia_II_Forum_abril_2009.pdf' target='_blank'>Futuro da prevencao Katia II Forum abril 2009</a></b></p>
			<p><a href='apresenta/Erros_de_medicacao_John_II_Forum_Abril_2009.pdf' target='_blank'>Erros de medicacao John II Forum Abril 2009</a></b></p>
			<p><a href='apresenta/Administracao_de_medicamentos_PREVENCAO_Katia_II_Forum_2009.pdf' target='_blank'>Administracao de medicamentos PREVENCAO Katia II Forum 2009</a></b></p>
			<p><a href='apresenta/EAS_2008.pdf' target='_blank'>Eventos Adversos e Incidentes (Dra Renata Gallotti)</a></b></p>
			<p><a href='apresenta/ERROS_HUMANOS.pdf' target='_blank'>Erros Humanos (Farm. Mário Borges Rosa)</a></b></p>
			<p><a href='apresenta/SEG_PACs.pdf' target='_blank'>Eventos Adversos Relacionados a Prática Médica (Prof Dr Ajith K Sankarankutty)</a></b></p>
			<p><a href='apresenta/1_Erros_Humanos_Simposio_CRFMG_2008.pdf' target='_blank'>Erros Humanos Simposio CRFMG_2008 (Farm. Mário Borges Rosa)</a></b></p>
			<p><a href='apresenta/Definicoes_Exercicios_Simposio_CRFMG_2008.pdf' target='_blank'>Definicoes Exercicios Simposio CRFMG 2008 (Farm. Mário Borges Rosa)</a></b></p>
			<p><a href='apresenta/Definicoes_Simposio_CRFMG_2008.pdf' target='_blank'>Definicoes Simposio CRFMG 2008 (Farm. Mário Borges Rosa)</a></b></p>
			<p><a href='apresenta/AulaConciliacaoModif_301108.pdf' target='_blank'>Conciliacao de Medicamentos (Farm. Hessem Miranda Neiva)</a></b></p>
			<p><a href='apresenta/erros_dispensacao_adminstracao_Simposio_CRFMG_2008.pdf' target='_blank'>Erros de Dispensação (Farm. Tânia Azevedo Anacleto)</a></b></p>
			<p><a href='apresenta/ISMP_Brasil.pdf' target='_blank'>Instituto para Práticas Seguras no Uso de Medicamentos ISMP Brasil(Equipe ISMP-Brasil)</a></b></p>
			<p><a href='apresenta/sitesdereferencia.pdf' target='_blank'>Sites de Referencia</a></b></p>
		</td></tr>";	
	
?>
</table>
