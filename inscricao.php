<?php
include 'global.php';
include 'includes/funcoesUteis.php';


$idCadastro = $_POST['idCadastro'];
$idCurso = $_POST['idCurso'];

//debug($_POST); die();
//	$query=mssql_query("select * from cadastro where idCadastro={$_POST['idCadastro']}")
$sql="SELECT cd.*, cr.titulo, cr.status FROM cadastro AS cd, cursos AS cr
	WHERE cd.idCadastro=$idCadastro AND cr.idCurso=$idCurso";
$query=mssql_query($sql); //echo $sql;
$rsAluno=mssql_fetch_object($query);
//debug($rsAluno); die();
$sql="SELECT idAlunoCursos, CONVERT(CHAR(10), data, $dataFilter) AS data FROM alunosCursos WHERE idAluno=$idCadastro AND idCurso=$idCurso";	
$query=mssql_query($sql);
//die('aqui');

if (mssql_num_rows($query) > 0) {
	
	$data=mssql_result($query, 0, 'data');
	//die('aqui 2');
	$sql = "SELECT idFinanceiro, pgto FROM financeiro WHERE
		(idCadastro = $idCadastro) AND (idCurso = $idCurso) AND (parcela = 1) ORDER BY vcto";
	//die('aqui 3');
	$query = mssql_query($sql);
	$rsBoleto= mssql_fetch_object($query); //echo $sql;
	
	//debug($rsBoleto); die();
	if($rsBoleto){
		$boleto_id = $rsBoleto->idFinanceiro;
		$pgto = ($rsBoleto->pgto!= '') ? $rsBoleto->pgto : '00/00/0000';
		}	
	?>

	<link rel='stylesheet' href='js/formStyle.css' type='text/css'/>
	<link rel='stylesheet' href='css/style.css' type='text/css' />
	<body onload='resizeTo(500,510)' style='floating:scroll-x:none' style='background:#ffffff'/>
		<img src='images/inscricaoOnLine.gif'>
		<table border=0 width=70% align=center>
			<tr>
				<td align=center>
					<p style='text-align:center'><br>
						Caro(a) <b><?=$rsAluno->nome?></b></font>,<br><br>
						Sua matrícula para o curso<br><br>
						<b><?=$rsAluno->titulo?></b><br><br>
						já foi efetuada no dia <b><?=$data?></b>.<br>
				<?
				if($rsAluno->status == 1){
					echo"<p style='text-align:left'><br>";
					echo sprintf("<a href='verContrato.php?uid=%d&cid=%d' target='_contrato' class='contrato'>
							<img src='images/ic_contrato.png' border='0' align='absBottom'>
							<span style='color:#f00;font-size:12px;text-decoration:underline'>
							Clique aqui para ler e imprimir o contrato</span></a></p>", 
								$idCadastro, $idCurso);

					if($boleto_id > 0 && $pgto != '00/00/0000') {	
						echo "<p style='text-align:left'>";
						echo "<a href='boletos/boleto.php?id=$boleto_id' target='_contrato'>
							<img src='mini.php?img=images/boleto.gif&wh=30' border='0' align='absBottom'>
							<span style='color:#f00;font-size:12px;text-decoration:underline'>
							Clique aqui para imprimir o boleto da primeira parcela</span></a></p>";
						}
					echo "<p style='text-align:left'>Enviar para AMF:<br />
							<b>Contrato assinado<br />
							Currículo Vitae mais histórico escolar da graduação<br />
							Uma foto 3 x 4<br />
							Cópia do Certificado de Graduação Autenticada<br />
							Cópia do CPF e Cópia do RG</b></p>";
					}
				elseif($boleto_id > 0 && $pgto != '00/00/0000') {	
						echo "<p style='text-align:left'>Caso precise re-imprimir o boleto, clique no link abaixo<br />";
						echo "<a href='boletos/boleto.php?id=$boleto_id' target='_contrato'>
							<img src='mini.php?img=images/boleto.gif&wh=30' border='0' align='absBottom'>
							<span style='color:#f00;font-size:12px;text-decoration:underline'>
							Clique aqui para imprimir o boleto.</span></a></p>";
						}	?>
					<p style='text-align:center'><br><a href='javascript:self.close()'>Fechar</a></p>
				</td>
			</tr>
		</table><?
	die();
	}

$query3=mssql_query("SELECT titulo, valor, parcelas, boleto, status, idCentroCusto FROM cursos WHERE idCurso=$idCurso");
$rsCurso=mssql_fetch_object($query3);

$sql="INSERT INTO alunosCursos(idCurso, idAluno, valorCurso, parcelas, valorParcelas, data, status)
	VALUES({$_POST['idCurso']}, {$_POST['idCadastro']}, $rsCurso->valor, $rsCurso->parcelas, $rsCurso->valor/$rsCurso->parcelas, getdate(), 1)";
@mssql_query($sql);



//	$contaCursos=41102001;
//$offSet=4;
if ($rsCurso->status < 2 && $idCurso==151) {
	for ($x=1; $x <= $rsCurso->parcelas; $x++) {
		$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("0$x", -2);
		if($x==1){
			//if($idCurso==149)
			$vcto = '04.12.2013';
			}
		if($x==2){
			//if($idCurso==149)
			$vcto = '05.15.2013';
			}
		else{
			$t=explode('.', $vcto);
			$time = strtotime("$t[0]/$t[1]/$t[2]") + (30 * 86400);
			$vcto = date("m.15.Y", $time);
			}
		
		$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
			($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
			SELECT @@IDENTITY AS LastID";		
		$query=mssql_query($sql) or die("Erro $sql");
		//$offSet =$offSet+30;
		if ($x==1) $boleto_id=mssql_result($query, 0, 'LastID');
		}
	}
elseif ($idCurso==152) {
	for ($x=1; $x <= $rsCurso->parcelas; $x++) {
		$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("0$x", -2);
		if($x==1){
			//if($idCurso==149)
			$vcto = '11.01.2013';
			}
		elseif($x==2){
			//if($idCurso==149)
			$vcto = '12.01.2013';
			}
		
		$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
			($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
			SELECT @@IDENTITY AS LastID";		
		$query=mssql_query($sql) or die("Erro $sql");
		//$offSet =$offSet+30;
		if ($x==1) $boleto_id=mssql_result($query, 0, 'LastID');
		}
	}
elseif ($idCurso==153) {
	for ($x=1; $x <= $rsCurso->parcelas; $x++) {
		$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("0$x", -2);
		if($x==1){
			//if($idCurso==149)
			$vcto = '03.10.2014';
			}
		elseif($x==2){
			//if($idCurso==149)
			$vcto = '03.25.2014';
			}
		
		$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
			($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
			SELECT @@IDENTITY AS LastID";		
		$query=mssql_query($sql) or die("Erro $sql");
		//$offSet =$offSet+30;
		if ($x==1) $boleto_id=mssql_result($query, 0, 'LastID');
		}
	}
elseif ($idCurso==154) {
	$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("01", -2);
	$vcto = '11.04.2013';
	$x=1;
	$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
		($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
		SELECT @@IDENTITY AS LastID";		
	$query=mssql_query($sql) or die("Erro $sql");
	$boleto_id=mssql_result($query, 0, 'LastID');
	}
elseif ($idCurso==155) {
	$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("01", -2);
	$vcto = '10.28.2013';
	$x=1;
	
	$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
		($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
		SELECT @@IDENTITY AS LastID";		
	$query=mssql_query($sql) or die("Erro $sql");
	$boleto_id=mssql_result($query, 0, 'LastID');
	}
elseif ($idCurso==156) {
	$numdoc=substr("000000$idCadastro",-5) . substr("0$idCurso",-2) . substr("01", -2);
	$vcto = '11.04.2013';
	$x=1;
	
	$sql="INSERT INTO financeiro(idCadastro, idCurso, dataLct, numDoc, tipoDoc, conta, historico, valor, vcto, total, parcela, formaPgto, boleto, status, dataReg) VALUES
		($rsAluno->idCadastro, {$_POST['idCurso']}, getdate(), '$numdoc',2, $contaCursos, 'Inscr. Curso: $rsCurso->titulo', $rsCurso->valor/$rsCurso->parcelas, '$vcto',$rsCurso->valor/$rsCurso->parcelas, $x, 3, '$numdoc', 2, getdate())
		SELECT @@IDENTITY AS LastID";		
	$query=mssql_query($sql) or die("Erro $sql");
	$boleto_id=mssql_result($query, 0, 'LastID');
	}


include 'mail/htmlMimeMail.php';
$strValor=formatVal($rsCurso->valor/$rsCurso->parcelas);

$matricula=substr("0000000$idCadastro",-8) . substr("000$idCurso",-3);

$eBody="
<table cellSpacing=2 cellPadding=2 width=100% border=0>
	<tr>
		<td align=right class=boxBorder>Matrícula:</td>
		<td class=boxBorder><b>$matricula</b></td>
		<td align=right class=boxBorder>Data:</td>
		<td class=boxBorder><b>". date('d/m/Y') . "</b></td>
	</tr>
	<tr>
		<td align=right class=boxBorder>Aluno:</td>
		<td class=boxBorder><b>$rsAluno->nome</b></td>
		<td align=right class=boxBorder>CPF:</td>
		<td class=boxBorder><b>$rsAluno->cpf</b></td>
	</tr>
	<tr>
		<td align=right class=boxBorder rowspan=2>Endereço:</td>
		<td class=boxBorder rowspan=2>
			 <b>$rsAluno->ender<br />
			 $rsAluno->bairro<br />
			 $rsAluno->cep $rsAluno->cidade/$rsAluno->uf</b></td>
		<td align=right class=boxBorder>RG:</td>
		<td class=boxBorder><b>$rsAluno->rg</b></td>
	</tr>
	<tr>
		<td align=right class=boxBorder>Fone:<br/>Cel.:</td>
		<td class=boxBorder><b>$rsAluno->fone<br/>$rsAluno->fone2</b></td>
	</tr>

	<tr>
		<td align=right class=boxBorder>E-mail:</td>
		<td class=boxBorder colspan=3><b>$rsAluno->email</b></td>
	</tr>
	<tr>
		<td align=right class=boxBorder>Curso:</td>
		<td class=boxBorder><b>$rsCurso->titulo</b></td>
		<td align=right class=boxBorder>Valor:</td>
		<td class=boxBorder><b>$rsCurso->parcelas x $strValor</b></td>
	</tr>
		<tr>
		<td align=center colspan='4'>Clique no(s) link(s) abaixo para impressão do(s) boleto(s):<p style='txt-align:left'>";
	
	$boletos = null;
	$sql="SELECT idFinanceiro FROM financeiro WHERE idCurso=$idCurso AND idCadastro=$idCadastro";
	$query=mssql_query($sql);
	while($financeiro = mssql_fetch_object($query)){
		$boletos .="	
		<a href='http://www.amfar.com.br/boletos/boleto.php?id=$financeiro->idFinanceiro' target='_boletos'>
			<b>http://www.amfar.com.br/boletos/boleto.php?id=$financeiro->idFinanceiro</b></a><br />";
		}

$eBody .="$boletos</p></tr>
</table>";

// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
$mailsend = sendMail($rsAluno->email, $rsAluno->nome, 'webmaster@amfar.com.br', 'Inscrição on Line', $eBody,'Inscrição on Line');
//	echo "enviando e-mail para: $rsAluno->nome ($rsAluno->email)";
$mailsend = sendMail($eMatricula, $rsAluno->nome, 'webmaster@amfar.com.br', 'Inscrição on Line', $eBody,'Inscrição on Line');
$mailsend = sendMail('amfar@amfar.com.br', $rsAluno->nome, 'webmaster@amfar.com.br', 'Inscrição on Line', $eBody,'Inscrição on Line');
//	echo "enviando e-mail para: $rsAluno->nome ($rsAluno->email)";
?>


<link rel='stylesheet' href='js/formStyle.css' type='text/css'/>
<link rel='stylesheet' href='css/style.css' type='text/css' />
<body onload='resizeTo(500,410)' style='floating:scroll-x:none' style='background:#ffffff'/>
	<img src='images/inscricaoOnLine.gif'>
	<table border=0 width=70% align=center>
		<tr>
			<td align=center nowrap>
				<p style='text-align:center'><br>
					Caro(a) <b><?=$rsAluno->nome?>,<br>
					Sua inscrição foi enviada com sucesso.</b></p><?

				if($idCurso== 151){
					echo"<p style='text-align:left'><br>";
					echo sprintf("<a href='verContrato.php?uid=%d&cid=%d' target='_contrato' class='contrato'>
							<img src='images/ic_contrato.png' border='0' align='absBottom'>
							<span style='color:#f00;font-size:12px;text-decoration:underline'>
							Clique aqui para ler e imprimir o contrato</span></a></p>", 
								$idCadastro, $idCurso);

					if($boleto_id > 0 && $pgto != '00/00/0000') {	
						echo "<p style='text-align:left'>";
						echo "<a href='boletos/boleto.php?id=$boleto_id' target='_contrato'>
							<img src='mini.php?img=images/boleto.gif&wh=30' border='0' align='absBottom'>
							<span style='color:#f00;font-size:12px;text-decoration:underline'>
							Clique aqui para imprimir o boleto da primeira parcela</span></a></p>";
						}
					echo "<b>Enviar para AMF:<br />
							Contrato assinado<br />
							Currículo Vitae mais histórico escolar da graduação<br />
							Uma foto 3 x 4<br />
							Cópia do Certificado de Graduação Autenticada<br />
							Cópia do CPF e Cópia do RG</b></p>";
					}
				elseif($boletos) {	
						echo "<p style='text-align:left'>";
						echo "Clique aqui para imprimir o boleto.</span><br />$boletos</p>";
						}	?>

				<p style='text-align:center'><br><a href='javascript:self.close()'>Fechar</a></p>
			</td>
		</tr>
	</table>
</body>
