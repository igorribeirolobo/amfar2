<?php
$saved = false;
if ($_POST['btSend']) {

	include 'global.php';
	include 'mail/htmlMimeMail.php';
	try{
		$nome = trim($_POST['nome']);
		$email = trim($_POST['email']);
		$fone = trim($_POST['fone']);
		$cel = trim($_POST['cel']);
		$cursoSP = (int)($_POST['SP']);
		$cursoFH = (int)($_POST['FH']);
		$cursoFC = (int)($_POST['FC']);
		
		if(empty($nome)) throw new Exception("O campo 'Nome' é obrigatório!\n\n");
		elseif(empty($email)) throw new Exception("O campo 'E-mail' é obrigatório!\n\n");
		elseif(empty($fone)) throw new Exception("O campo 'Telefone Fixo' é obrigatório!\n\n");
		elseif(empty($cel)) throw new Exception("O campo 'Celular' é obrigatório!\n\n");
		elseif($cursoSP+$cursoFH+$cursoFC==0) throw new Exception("Selecione pelo menos um curso!\n\n");	
		
		mssql_query("INSERT INTO cursosReserva(nome, email, fone, cel, cursoSP, cursoFH, cursoFC)
			VALUES(upper('$nome'), lower('$email'), '$fone', '$cel', $cursoSP, $cursoFH, $cursoFC)");	
		
		
		$host='mysql01.amfar.com.br';	// host ou ip do mysql
		$userDB='amfar';	// login do mysql
		$pwdDB='amf10web20';	// senha do mysql
		$dataBase='amfar';	// base de dados
		$myconn =  mysql_connect($host, $userDB, $pwdDB);
		mysql_select_db($dataBase);
		@mysql_query("INSERT INTO mailing SET nome='$nome', email='$email', dataReg=NOW()");	
		mysql_close($myconn);
	
		
		$cursoSP = ($cursoSP==1) ? '<b>Saúde Pública</b>' : ''; 
		$cursoFH = ($cursoFH==1) ? '<b>Farmácia Hospitalar e Serviços de Saúde</b>' : '';
		$cursoFC = ($cursoFC==1) ? '<b>Farmacologia Clínica</b> (Previsão início Abril/2013)' : '';
		
		$eBody="		
			<table cellSpacing=2 cellPadding=2 width=100% border=0>
				<tr>
					<td align='right' class='boxBorder'>Nome:</td>
					<td class='boxBorder'><b>$nome</b></td>
				</tr>
				<tr>
					<td align='right' class='boxBorder'>E-mail:</td>
					<td class='boxBorder'><b>$email</b></td>
				</tr>
				<tr>
					<td align='right' class='boxBorder'>DDD/Fone:</td>
					<td class='boxBorder'><b>$fone</b></td>
				</tr>
				<tr>
					<td align='right' class='boxBorder'>DDD/Celular:</td>
					<td class='boxBorder'><b>$cel</b></td>
				</tr>
				<tr>
					<td class='boxBorder' colspan='2'>$cursoSP</td>
				</tr>
				<tr>
					<td class='boxBorder' colspan='2'>$cursoFH</td>
				</tr>
				<tr>
					<td class='boxBorder' colspan='2'>$cursoFC</td>
				</tr>
				<tr>
					<td align='center' class='boxBorder' colspan='2'>
						Aguarde nova informações sobre o(s) curso(s) selecionado(s)</td>
				</tr>
			</table>";
		// function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
		$mailsend = sendMail('secretaria@amfar.com.br', $nome, $email, 'AMF/Reserva de Cursos', $eBody, 'Reserva de Cursos');
		$mailsend = sendMail('webmaster@amfar.com.br', $nome, $email, 'AMF/Reserva de Cursos', $eBody, 'Reserva de Cursos');
		$mailsend = sendMail('webmaster@sbrafh.org.br', $nome, $email, 'AMF/Reserva de Cursos', $eBody, 'Reserva de Cursos');
		$message = "E-mail enviado com sucesso.";
		}
	catch(Exception $ex){
		$message = $ex->getMessage();
		}
	}
?>
<script type="text/javascript" src="http://www.amfar.com.br/js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="http://www.amfar.com.br/jsLibrary/jquery.maskedinput-1.2.2.min.js"></script>
<script type="text/javascript" src="http://www.amfar.com.br/jsLibrary/prototypes.js"></script>	
<link rel="stylesheet" href="js/formStyle.css" type="text/css"/>
<script>
	$(document).ready(function(){
		$('.fone').mask('(99) 9999-9999',{placeholder:'_'});
		$('#btSend').click(function(){
			if($('#nome').val().trim()==''){
				alert('Por favor, digite seu nome.\n\n');
				$('#nome').focus();
				return false;
				}
			if($('#email').val().trim()==''){
				alert('Por favor, digite seu E-mail.\n\n');
				$('#email').focus();
				return false;
				}
			if(!$('#email').val().validEmail()){
				alert('Por favor, digite corretamente seu E-mail.\n\n');
				$('#email').focus();
				return false;
				}
			if($('#fone').val().trim()==''){
				alert('Por favor, digite seu telefone fixo.\n\n');
				$('#fone').focus();
				return false;
				}
			if($('#cel').val().trim()==''){
				alert('Por favor, digite seu celular.\n\n');
				$('#cel').focus();
				return false;
				}
			if(!$('#SP').is(':checked') && !$('#FH').is(':checked') && !$('#FC').is(':checked')){
				alert('Por favor, selecione o curso desejado.\n\n');
				return false;
				}
			})
		if($('#msg').val() != undefined && $('#msg').val()!=''){
			alert($('#msg').val());
			if($('#msg').val()=='E-mail enviado com sucesso.')
				$('#close').click();
			}
		})
</script>
<style>
	#nome{width:300px}
	#email{width:300px}
	.fone{width:100px;text-align:center}
</style>

<p style="padding:0 4px;font-size:13px;border:solid 1px #ddd" align="center">
	<b class="blue" style="font-size:14px;text-decoration:underline">ATENÇÃO</b><br /><br />
	Estamos aceitando reservas para os cursos de:<br />
	<b>Saúde Pública</b>, <b>Farmácia Hospitalar e Serviços de Saúde</b> (Previsão início Abril/2013)<br />
	e <b>Farmacologia Clínica</b><br /><br />
	
	Preencha o formulário abaixo ou se preferir ligue <b>31 3291-6242</b><br />
	ou escreva para: <b>AMF - Associação Mineira de Farmacêuticos <br />
	Av. do Contorno, 9215 - Sl 502 - Ed. Humberto Martins Vieira<br />
	Prado - 30110-130 - Belo Horizonte/MG</p>
	
<form name="fcontato" id='fcontato' style="background:#fff;width:530px;border:1px solid #ddd;margin:0" action="" method="POST">
	<input type="hidden" id="msg" value="<?=$message?>" />
	<table cellSpacing="1" cellPadding="1" width="100%" border="0">
		<tr>
			<td class="label">Nome:</td>
			<td>
				<input type="text" id="nome" name="nome" maxLength="40" value="<?=$_POST['nome']?>" /></td>
		</tr>
		<tr>
			<td class="label">E-mail:</td>
			<td><input type="text" size="40" id="email" name="email" value="<?=$_POST['email']?>" /></td>
		</tr>
		<tr>
			<td class="label" nowrap>DDD/Fone Fixo:</td>
			<td><input type="text" id="fone" name="fone" size="40" class="fone" value="<?=$_POST['fone']?>" /></td>
		</tr>
		<tr>
			<td class="label">DDD/Celular:</td>
			<td><input type="text" id="cel" name="cel" size="40" class="fone" value="<?=$_POST['cel']?>" /></td>
		</tr>
		<tr>
			<td class="label" valign="top">Selecione:</td>
			<td style="font-size:13px">
				<input <?=($_POST['SP'])?'checked':''?> type="checkbox" name="SP" id="SP" class="cursos" value="1">Saúde Pública<br />
				<input <?=($_POST['FH'])?'checked':''?> type="checkbox" name="FH" id="FH" class="cursos" value="1">Farmácia Hospitalar e Serviços de Saúde<br />
			</td>
		</tr>
		<tr>
			<td style="text-align:center;background:#fff; color:#f00;font-weight:bolder" colspan="2">
				Após preencher e enviar os dados acima, você iré receber maiores informações sobre o(s) curso(s) selecionado(s) como 
				valores, condições de pagamento e previsão para início.</td>
		</tr>
		<tr>
			<td colSpan="2" style="text-align:center; background:#eeeee4">
				<input type="submit" id="btSend" class="tButton" value="Enviar" name="btSend" style="padding:4px 8px"></td>
		</tr>
	</table>
	<p style="text-align:center;font-size:14px;font-weight:bolder;color:darkgreen">
		O Curso FARMACOLOGIA CLÍNICA VIII TURMA já está aberto as inscrições. 
		<a href="javascript:abrir('insCursos.php?idCurso=149');">CLIQUE AQUI</a></p>
</form>
