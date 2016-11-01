<?php
/********************************************************
*
*  Crhstiano L. Secchi
*
*  $comprimento: quantidade de caracteres a ser impresso
*  $arquivo: caminho e nome do arquivo de imagem
/*******************************************************/
function antispam($comprimento, $fundo)	{

	header("Content-type: image/jpeg");
	
	$texto = null;
	$caracteres = "6A0B1C2D3E4F5G6H7I8J9K0L1M2N3O4P5Q6R7T8U9V1W2X3Y4Z5";

	$cor_texto_r = rand(0, 255);
	$cor_texto_g = rand(0, 128);
	$cor_texto_b = rand(0, 255);

	$imagem = imagecreate(($comprimento * 9) + 3, 21);
	$cor_fundo = imagecolorallocate($imagem, 204, 204, 204);
	$cor_texto = imagecolorallocate($imagem, $cor_texto_r, $cor_texto_g, $cor_texto_b);

	for ($i = 1; $i <= $comprimento; $i++) {
	$numero = rand(0, strlen($caracteres) - 1);
	$texto .= $caracteres[$numero];
	}

	imagestring($imagem, 5, 2, 3, $texto, $cor_texto);
	imagejpeg($imagem, $arquivo, 100);
	imagedestroy($imagem);

	return $texto;
	}

$temp=tempnam('','images/antispam.jpg');

echo "<img src=antispam(5,'images/antispam.jpg')>";
?>
