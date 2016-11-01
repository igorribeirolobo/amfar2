<?
include("../global.php");

#------------------------------------------------------------------------------------------------
# Este php faz a administração de todas as enquetes, permitindo através dele adicionar perguntas,
# adicionar respostas a essas perguntas, alterar o texto e data de uma pergunta, alterar o texto
# de uma resposta, apagar uma pergunta e todas as suas respostas, apagar apenas uma resposta
#------------------------------------------------------------------------------------------------

$pagetogo = isset($_POST['pagetogo']) ? $_POST['pagetogo'] : $_GET['pagetogo'];
$todo = isset($_POST['todo']) ? $_POST['todo'] : $_GET['todo'];
$id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
$id_p = isset($_POST['id_p']) ? $_POST['id_p'] : $_GET['id_p'];
$id_r = isset($_POST['id_r']) ? $_POST['id_r'] : $_GET['id_r'];

include_once('manut.php');	
?>
<html>
	<head>
		<title>Administração da Enquete ver 1.0</title>
		<meta name="Author-PHP" content="Lauro Assis Lima de Brito">
		<meta name="Email" content="webmaster@labgraphic.com.br">
	
		<style>
			P { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 10pt; font-style: normal; font-weight: normal; color: #000000}
			a { font-family: arial, verdana; color:#000000; font-size:10pt; font-weight:normal; text-decoration:none; }
			a:hover { color:red; text-decoration:none; font-weight:bold; }
			a:visited {  font-family: arial, verdana; color:#000000; font-size:10pt; font-weight:normal; text-decoration:none; }
			

			td {padding-left:4px; padding-right:4px;font-family: Arial, Verdana, Helvetica, sans-serif; font-size: 8pt; font-style: normal; font-weight: normal; color: #000020}
			.titulo {text-align=center; background-color: #efefef; text-align: center; font-family: Arial, Helvetica, sans-serif, Verdana; font-size: 10pt; font-style: normal; font-weight:bold; color: #000020}
			.dir {text-align: right;}
			.center {background-color: #efefef; text-align: center;}
			.left {background-color: #efefef; text-align: left;}
			.titulo {background-color: #FF9900; text-align: center; font-weight:bold; color: #000020}			
			.boxborder { border-color:#000000; border-style:solid; border-width:1; }
			.but {background-color: #c0c0c0; font-family: Arial,Verdana; font-weight: bold; color: indigo; cursor: hand; font-size: 11px}
		</style>
	</head>
	
	<body topmargin="0"><?	
		include('home.php'); ?>
	</body>
</html>