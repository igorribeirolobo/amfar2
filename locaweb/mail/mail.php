<?
//Pega os dados postados pelo formul�rio HTML e os coloca em variaveis
$nomeremetente   = $_POST['nomeremetente'];
$emailremetente   = $_POST['emailremetente'];

$emaildestinatario = $_POST['emaildestinatario'];
$comcopia           = $_POST['comcopia'];
$comcopiaoculta   = $_POST['comcopiaoculta'];
$assunto               = $_POST['assunto'];
$mensagem          = $_POST['mensagem'];
$servidor               = $_POST['servidor'];

//Monta a mensagem a ser enviada no corpo do e-mail
$mensagem = "<html><head><title>$assunto</title></head> <body>$mensagem</body> </html>";

//Especifica o destinatario da mensagem
$to = "$emaildestinatario"; 

//Monta o cabe�alho da mensagem
$headers = "MIME-Version: 1.0\n"; 
$headers .= "Content-type: text/html; charset=iso-8859-1\n"; 
$headers .= "From: $nomeremetente <$emailremetente>\n"; 
$headers .= "Cc: $comcopia \n";
$headers .= "Bcc: $comcopiaoculta \n";
$headers .= "Return-Path: <$emailremetente>\n"; 

/* Enviando a mensagem */ 
mail($to, $assunto, $mensagem, $headers);
print "Mensagem <b>$assunto</b> enviada com sucesso ! <br><br>
Para: $emaildestinatario<br> 
Com c�pia: $comcopia <br> 
Com c�pia Oculta: $comcopiaoculta <br>";
?>