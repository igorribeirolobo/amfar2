<?php
function createpdf($bg, $texto, $filename){
	//die("bg = $bg");
	ini_set("allow_url_fopen", 1);
	# Aqui inclu�mos a classe html2pdf.
	include('html2pdf/html2pdf.class.php');
	 
	/* Guardamos na vari�vel $html o html que queremos converter.
	 * Linha 13 - Inclu�mos o nosso arquivo css (exemploPdf.css)
	 * Linha 15 - Temos uma div de id = logo que formatamos a mesma 
	 *            com uma altura, largura, uma borda azul e uma imagem 
	 *            de background.
	 * Linha 16 - Temos agora um span de id = texto que formatamos 
	 *            usando a fonte arial em negrito. */
	 $left = (stristr($bg, '_PR')) ? '85px' : '65px';
	$html = '
	<style>
		#logo{
			/*border:     1px solid blue;*/
			width:      100%;
			height:     100%;
			background: url('. $bg .') no-repeat;
			}
		 
		
		#frame{
			margin-top:350px;
			margin-left:'. $left .';
			margin-right:200px;
			width:      780px;
			height:     250px;
			font-size:24px;
			}
		
		#texto{color:black;
			font-size:17pt;
			font-family: Arial;
			font-style: normal;
			text-align:center;
			line-height:150%;
			}
		
		#carga{color:yellow;
			font-size:18px;
			font-family: Arial;
			font-style: italic;
			text-align:left;
			}
	 </style>
	 
	<div id="logo">
		<div id="frame">
			<p id="texto">' . $texto . '</p>
		</div>
	</div>';
	 
	# Converte o html para pdf.
	try
	{
	    /* Aqui estamos instanciando um novo objeto que ir� criar o 
	     * pdf. Ent�o vamos aos parametros passados:
	     * 1� par�metro: Utilize �P� para exibir o documento no 
	     *               formato retrato e �L� para o formato 
	     *               paisagem. 
	     * 2� par�metro: Formato da folha A4, A5....... 
	     * 3� par�metro: Caso ocorra alguma exce��o durante a 
	     *               convers�o. Em qual idioma � para 
	     *               exibir o erro. No caso o idioma escolhido 
	     *               foi o portugu�s �pt�. 
	     * 4� par�metro: Informe TRUE caso o html de entrada esteja
	     *               no formato unicode e FALSE caso negativo. 
	     * 5� par�metro: Codifica��o a ser utilizada. ISO-8859-15, UTF8 ...... 
	     * 6� par�metro: Margem do documento. Voc� pode informa um 
	     *               �nico valor como no exemplo acima. 
	     *               Outra forma � informa um array setando as 
	     *               margens separadamente.: Exemplo: 
	     * $html2pdf = new HTML2PDF(
	     *   'P',
	     *   'A4',
	     *   'pt',
	     *   false,
	     *   'ISO-8859-15',
	     *   array(5,5,5,8));
	     * Sendo que a primeira posi��o do array representa a margem esquerda depois      
	     * topo, direita e rodap�. */
	    $html2pdf = new HTML2PDF('L','A4','pt', false, 'ISO-8859-15', 2);
	     
	    # Passamos o html que queremos converte.
	    $html2pdf->writeHTML($html); 
	     
	    /* Exibe o pdf:
	     * 1� par�metro: Nome do arquivo pdf. O nome que voc� quer dar ao pdf gerado. 
	     * 2� par�metro: Tipo de sa�da: 
	                     I: Abre o pdf gerado no navegador. 
	                     D: Abre a janela para voc� realizar o download do pdf. 
	                     F: Salva o pdf em alguma pasta do servidor. */
	    $html2pdf->Output($filename, 'I');
	   return 'Ok';
		}
		catch(HTML2PDF_exception $e) 
		{ 
		 return("Erro: $e"); 
		}
	}



try{
	include '../global.php';
	$query = mssql_fetch_object(mssql_query("SELECT id, tipo, nome, data, regional FROM inscricoesCursos WHERE id={$_GET['id']}"));
	$query2 = mssql_fetch_object(mssql_query("SELECT participante, palestrante, comissao FROM cursosTextosCertificados WHERE data='$query->data'"));
	if($query->tipo=='PARTICIPANTE')
		$texto = str_replace('#nome', $query->nome, $query2->participante);
	elseif($query->tipo=='PALESTRANTE')
		$texto = str_replace('#nome', $query->nome, $query2->palestrante);
	elseif($query->tipo=='COMISSAO')
		$texto = str_replace('#nome', $query->nome, $query2->comissao);

	$bg = sprintf('certificado%d_%s.jpg', ereg_replace("([^0-9])",'', $query->data), $query->regional);
	$filename = sprintf('%s_%d_%06d.pdf', $query->regional, $query->data, $query->id);
	createpdf($bg, $texto, $filename);
	}
catch(Exception $ex){
	echo $ex->getMessage;
	}
?>
