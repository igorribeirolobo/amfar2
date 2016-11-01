<?php
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
$html = '
<style>
	#logo{
		/*border:     1px solid blue;*/
		width:      100%;
		height:     100%;
		background: url(certificado.jpg) no-repeat;
		}
	 
	
	#frame{background:red;
		margin-top:320px;
		margin-left:30px;
		margin-right:60px;
		width:      940px;
		height:     310px;
		font-size:24px;
		}
	
	#texto{color:yellow;
		font-size:26px;
		font-family: Arial;
		/*font-style: italic;*/
		text-align:center;
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
		<p id="texto">
			Certificamos que Fulano da Silva dos Santos participou como CONGRESSISTA do VIII Congresso Brasileiro de Farm�cia Hospitalar, 
			promovido pela Sociedade Brasileira de Farm�cia Hospitalar e Servi�os de Sa�de - SBRAFH, realizado no per�odo de 24 a 26 de 
			novembro de 2011, em Salvador � BA.
		</p>
		<p id="carga">
			Carga Hor�ria: 24hs
		</p>
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
    $html2pdf->Output('exemploPdf.pdf', 'I');
}
catch(HTML2PDF_exception $e) 
{ 
 echo $e; 
}
?>
