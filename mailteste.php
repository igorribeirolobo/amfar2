<?php
$headers = "MIME-Version: 1.1\r\n";
$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
$headers .= "From: webmaster@amfar.com.br\r\n"; // remetente
$headers .= "Return-Path: webmaster@amfar.com.br\r\n"; // return-path
$envio = mail("lab.design@globo.com", "Assunto-Teste", "Vamos ver", $headers);
 
if($envio)
 echo "Mensagem enviada com sucesso";
else
 echo "A mensagem não pode ser enviada";
?>
