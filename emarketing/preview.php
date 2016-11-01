<?php

session_start();
if (!$_SESSION['usrAdm']) {
	die();
	}

include 'config/config.php';

// parte text/html
$ds = date("w");
$mes = date("n");
$dia = substr(date("0d"), -2);
$ano = date("Y");

$semana = array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado');
$nomeMes = array('','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
$dataext =	"{$semana[$ds]}, $dia {$nomeMes[$mes]} $ano";

$dataEnvio = date('Y/m/d');

$image="$_bgTop";
$image_info = GetImageSize($image);
list($widthTop, $heightTop) = $image_info;

$image="$_bgBottom";
$image_info = GetImageSize($image);
list($widthBottom, $heightBottom) = $image_info;

/*	
	*******************************************
	prepara o corpo do email
	busca no form os arquivos com check box='on'
	*******************************************
*/
$body="";

foreach ($_POST as $files => $value)	{
	$len=strlen($files);

	if (substr($files,0, 5)== 'body_')	{

		/*
			carrega o arquivo e monta o body do email
			arquivo para corpo do email so pode ser "*.html" ou "*.htm"
		*/

		$fname=substr($files,-(len-5));		

		if (stristr($fname,'html'))
			$fname[strlen($fname)-5]='.';

		elseif (stristr($fname,'htm'))
			$fname[strlen($fname)-4]='.';

		$fh=file("files/$fname");	
		foreach($fh as $line) $body .= $line;
		}	// if body

	elseif (substr($files,0, 5)== 'imag_')	{

		/*
			arquivo para anexo so pode ser
			"*.html","*.htm","*.jpg", "*.jpeg","*.gif" ou "*.png"
		*/

		$fname=substr($files,-(len-5));

		if (stristr($fname,'html'))	{
			$fname[strlen($fname)-5]='.';
			$type='text/html';	}

		elseif (stristr($fname,'jpeg'))	{
			$fname[strlen($fname)-5]='.';
			$type='image/jpg';	}

		elseif (stristr($fname, 'htm'))	{
			$fname[strlen($fname)-4]='.';
			$type='text/html';	}

		elseif (stristr($fname, 'gif'))	{
			$fname[strlen($fname)-4]='.';
			$type='image/gif';	}

		elseif (stristr($fname, 'jpg'))	{
			$fname[strlen($fname)-4]='.';
			$type='image/jpg';	}

		elseif (stristr($fname, 'png'))	{
			$fname[strlen($fname)-4]='.';
			$type='image/png';	}

		elseif (stristr($fname, 'doc'))	{
			$fname[strlen($fname)-4]='.';
			$type='text/doc';	}

$body .="
<img border=0 src=\"$_coSite/e-mailing/files/images/$fname\">";
		}	// if body

	} // end foreach
	
$warning=str_replace('#_coName',$_coName,$_warning);
$warning=str_replace("#_returnPath",$_returnPath,$warning);

// parte text/html

$eMailing = "
<html>
   <head>
      <title>$_coName: Mailing</title>
      <style>
         #topo {
            background:#fff;
            height:{$heightTop}px;
            padding:44px 0 0 125px;
            font: bolder 18pt Arial, Helvetica, sans-serif, Verdana;
            color:#ffffff;
            text-align:center;
            }

         .titulo {
            padding:44px 0 0 125px;
            font: bolder 14pt Arial, Helvetica, sans-serif, Verdana;
            color:#ffffff;
            text-align:center;
            }

         .data {
            padding:0 4px;
            margin:0;
            font: bolder 9pt Arial, Helvetica, sans-serif, Verdana;
            text-align:right;
            background:#f0f0f0;
            }

         #warning {
            padding:0 4px;
            margin:0;
            font: normal 9pt Arial, Helvetica, sans-serif, Verdana;
            text-align:center;
            border-left:1px solid #808080;
            border-right:1px solid #808080;
            }

         #corpo {
            margin:0;
            border-left:1px solid #808080;
            border-right:1px solid #808080;
            text-align:center;
            background:#f0f0f0;
            }

         #rodape {
            background:#a0a0a0;
            text-align:center;
            font: bolder 10pt Arial, Helvetica, sans-serif, Verdana;
            color:#000;
            height:{$heightBottom}px;
            background:url('$_coSite/e-mailing/$_bgBottom') no-repeat;
            }

         #rodape a {
            font: bolder 9pt arial, verdana;
            color:#efefef; font-size:9pt;
            text-decoration:none
            }

         #rodape a:hover {
            color:#fff;
            text-decoration:none;
            border-bottom:1px solid #c40000
            }
      </style>
   </head>

   <body topmargin='4' leftmargin='4'/>
      <table border='0' cellpadding='0' cellspacing='0' width='565'/>
         <tr><td><img src=\"$_coSite/e-mailing/$_bgTop\"/></td></tr>
         <tr><td id=corpo><p class=data>:: Informativo - $dataext ::</p></td></tr>
         <tr><td id=corpo><img border=0 src=\"$_coSite/e-mailing/files/images/$fname\"></td></tr>
         <tr><td id=rodape><a href=\"$_coSite\">:: $_coName ::</a></td></tr>
      </table>
   </body>
</html>";

//if (file_exists("files/emailing.html"))
//	unlink("files/emailing.html");
	
$fn=@fopen("files/emailing.html", "w+");
fputs($fn, $eMailing);
@fclose($fn);

$temp='';
$fh=file("files/emailing.html");
	foreach($fh as $line) $temp .= $line;;
@fclose($fn);
?>
<script>
	document.location.href="files/emailing.html";
</script>
