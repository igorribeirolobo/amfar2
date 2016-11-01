
<?php 
if (isset($_POST['txtdest']))
{
	$destino = $_POST['txtdest'];
	$assunto = $_POST['txtass'];
	$mensagem = $_POST['txtmsg'];
	if (PATH_SEPARATOR ==":") {
		$quebra = "\r\n";
	} else {
		$quebra = "\n";
	}
	$headers = "MIME-Version: 1.1".$quebra;
	$headers .= "Content-type: text/plain; charset=iso-8859-1".$quebra;
	$headers .= "From: contato@isbrae.com.br".$quebra; //E-mail do remetente
	$headers .= "Return-Path: contato@isbrae.com.br".$quebra; //E-mail do remetente
	mail($destino, $assunto, $mensagem, $headers, "-r". "contato@isbrae.com.br");
	print "Mensagem enviada com sucesso!";
}else{ ?>
   <html>
   <head>
       <title>Mail - PHP</title>
       <style type="text/css">
           #Text1
           {
               width: 287px;
           }
           #Text2
           {
               width: 287px;
           }
           #Text3
           {
               width: 287px;
           }
           #Text4
           {
               width: 287px;
           }
           #btEnviar
           {
               width: 100px;
           }
           #btLimpar
           {
               width: 100px;
           }
           #TextArea1
           {
               width: 287px;
           }
       </style>
   </head>
   <body>
   <form id="form" name="form" method="POST" action="Mail.php">
       <h2 align="center" style="text-decoration: underline"> Formuláo de Contato (Mail - PHP)</h2>
       <table width="450px" align="center" border="1" cellpadding="5" cellspacing="5">
           <tr>
               <td align="right">
                   Email Destinatáo:</td>
               <td>
                   <input id="txtdest" name="txtdest" type="text" /></td>
           </tr>
           <tr>
               <td align="right">
                   Assunto:</td>
               <td>
                   <input id="txtass" name="txtass" type="text" /></td>
           </tr>
           <tr>
               <td align="right">
                   Mensagem:</td>
               <td>
                   <textarea id="txtmsg" name="txtmsg" rows="2"></textarea></td>
           </tr>
           <tr>
               <td align="center" colspan="2">
                   <table style="width:100%;" cellspacing="10">
                       <tr>
                           <td align="right">
                               <input id="btEnviar" type="submit" value="Enviar" /></td>
                           <td>
                               <input id="btLimpar" type="reset" value="Limpar" align="left" /></td>
                       </tr>
                   </table>
               </td>
           </tr>
       </table>
       </form>
   </body>
</html>
<?php } ?>
