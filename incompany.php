<?
define('jsLib', 'http://www.amfar.com.br/jsLibrary');

if ($_POST['btContato']) {
//	include 'global.php';
//	include 'mail/htmlMimeMail.php';
	try{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://www.amfar.com.br/site/cadastro/incompany/");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		// Iremos usar o método POST
		curl_setopt($curl, CURLOPT_POST, true);
		// Definimos quais informações serão enviadas pelo POST (array)
		curl_setopt($curl, CURLOPT_POSTFIELDS, $_POST);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = trim(curl_exec($curl));
		curl_close($curl);
		if($result == 'Ok'){
			$message = "Cadastro gravado com sucesso\n\nE-mail enviado com sucesso.\n\nObrigado pelo seu contato.\n\n";
			$_POST = null;
			}
		else	
			$message = $result;
		}
	catch(Exception $ex){
		$message = $ex->getMessage();
		}
	}
?>


<script type="text/javascript" type="text/javascript" src="<?=jsLib?>/jquery-1.3.2.js"></script>
<script type="text/javascript" type="text/javascript" src="<?=jsLib?>/prototypes.js"></script>
<script type="text/javascript" type="text/javascript" src="<?=jsLib?>/jquery.maskedinput-1.2.2.min.js"></script>

<link rel="stylesheet" href="js/formStyle.css" type="text/css"/>
<style>
	.text{width:300px;font-size:12px;font-weight:normal}
	#cep{width:100px;text-align:center}
	.fone{width:100px;text-align:center}
	.buttons{cursor:pointer;padding:2px 8px}
	.buttons:hover{color:#f90}
</style>
<script>
	$(document).ready(function(){
		$('.fone').mask('(99) 9999-9999',{placeholder:'_'});
		$('#cep').mask('99999-999',{placeholder:'_'});
		
		$('#btSend').click(function(){
			if($('#cep').val().trim()==''){
				alert("Por favor, preencha o campo 'CEP'\n\n");
				$('#cep').focus();
				return false;
				}
			if($('#empresa').val().trim()==''){
				alert("Por favor, preencha o campo 'Empresa'\n\n");
				$('#empresa').focus();
				return false;
				}
			if($('#ender').val().trim()==''){
				alert("Por favor, preencha o campo 'Endereço'\n\n");
				$('#ender').focus();
				return false;
				}
			if($('#bairro').val().trim()==''){
				alert("Por favor, preencha o campo 'Bairro'\n\n");
				$('#bairro').focus();
				return false;
				}
			if($('#cidade').val().trim()==''){
				alert("Por favor, preencha o campo 'Cidade'\n\n");
				$('#cidade').focus();
				return false;
				}
			if($('#uf').val().trim()==''){
				alert("Por favor, selecione o campo 'Estado'\n\n");
				$('#uf').focus();
				return false;
				}

			if($('#responsavel').val().trim()==''){
				alert("Por favor, preencha o campo 'Responsavel'\n\n");
				$('#responsavel').focus();
				return false;
				}
			if($('#email').val().trim()==''){
				alert("Por favor, preencha o campo 'E-mail'\n\n");
				$('#email').focus();
				return false;
				}
			if(!$('#email').val().validEmail()){
				alert("Por favor, preencha o campo 'E-mail'\n\n");
				$('#email').focus();
				return false;
				}
			if($('#fone').val().trim()==''){
				alert("Por favor, preencha o campo 'Telefone'\n\n");
				$('#fone').focus();
				return false;
				}
			if($('#area').val().trim()==''){
				alert("Por favor, preencha o campo 'Área de Atuação'\n\n");
				$('#area').focus();
				return false;
				}
			if($('#descricao').val().trim()==''){
				alert("Por favor, preencha o campo 'Descrição da Solicitação'\n\n");
				$('#descricao').focus();
				return false;
				}
			if(!confirm("Os dados estão prontos para serem enviados.\n\nConfira se estão todos corretos e pressione botão Ok\n\n"))
				return false;
			})
		
			$('.text').focus(function(){$(this).css('background','#D2FABA')}).blur(function(){$(this).css('background','#fff')})
			$('.upper').blur(function(){$(this).val($(this).val().upper())})
			$('.lower').blur(function(){$(this).val($(this).val().lower())})

		$('#cep').change(function(event){					
			event.preventDefault();
			t = $('#cep').val().split('-');
			url = "http://cep.republicavirtual.com.br/web_cep.php?cep="+t[0]+t[1]+"&formato=javascript"	
			//$('.getender').each(function(){$(this).css('display','none');})			
			//$('.loading').each(function(){$(this).css('display','inline');})
			/* 
				Para conectar no serviço e executar o json, precisamos usar a função
				getScript do jQuery, o getScript e o dataType:"jsonp" conseguem fazer o cross-domain, os outros
				dataTypes não possibilitam esta interação entre domínios diferentes
				Estou chamando a url do serviço passando o parâmetro "formato=javascript" e o CEP digitado no formulário
				http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+$("#cep").val()
			*/
			
			$.getScript(url, function(){
				// o getScript dá um eval no script, então é só ler!
				// Se o resultado for igual a 1
		  		if(resultadoCEP["resultado"]){
					$("#ender").val(unescape(resultadoCEP["tipo_logradouro"]).upper()+" "+unescape(resultadoCEP["logradouro"]).upper()+', ');
					$("#bairro").val(unescape(resultadoCEP["bairro"])
						.replace('Conjunto','Conj.')
						.replace('Presidente','Pres.')
						.replace('Habitacional','Hab.')
						.replace('Castelo','Cast.')
						.upper());
					$("#cidade").val(unescape(resultadoCEP["cidade"]).upper());
					$("#uf").val(unescape(resultadoCEP["uf"]).upper());
					}
				else{
					alert("Endereço não encontrado");
					}
				});
			//$('.loading').each(function(){$(this).css('display','none');}) 
			//$('.getender').each(function(){$(this).css('display','inline');})						
			return false;
			})
		$('#cep').focus();
		if($('#msg').val() != '' && $('#msg').val() != undefined)
			alert($('#msg').val());
		})
</script>

<div id="mainTop" style="background:url(images/mainInCompany.png) no-repeat; height:24px"></div>	
<center>	
	<p style="padding:0 4px;font-size:14px" align="center"><b>Novidade! ATENDIMENTO CORPORATIVO</b></p>
	<p style="padding:0 50px;font-size:14px">Cursos em formato in company, desenvolvidos especialmente para atender às demandas de empresas da iniciativa privada, 
		de instituições governamentais e de órgãos do terceiro setor. Os cursos in company da AMF possuem customização de programas 
		e grade de horário flexível, focados nas necessidades e rotinas de cada empresa.</p>
	<p style="padding:0 50px;font-size:14px">Encaminhe sua demanda que entraremos em contato:</p>
		
	<form name="contato" id='contato' style="margin-top:0;width:95%;background:transparent;border:0" action="" method="POST">
		<input type="hidden" id="msg" value="<?=$message?>" />
		<table cellSpacing="1" cellPadding="1" border="0" align="center">
			<tr>
				<td style="text-align:center" colspan="2">
					<b>Após digitar o CEP, o sistema buscará automaticamente seu endereço<br />assim que posicionar o cursor sobre qualquer outro campo</b>
				</td>
			<tr>
				<td class="label">CEP:</td>
				<td>
					<input type="text" class="text" id="cep" name="cep" value="<?=$_POST['cep']?>" />
				</td>
			</tr>
			<tr>
				<td class="label">Empresa:</td>
				<td>
					<input type="text" class="text upper" id="empresa" name="empresa" maxLength="60" value="<?=$_POST['empresa']?>" /></td>
			</tr>
			<tr>
				<td class="label">Ender. (Rua, nº):</td>
				<td><input type="text" class="text upper" id="ender" name="ender" maxLength="60" value="<?=$_POST['ender']?>" /></td>
			</tr>
			<tr>
				<td class="label">Compl. (Sala, Andar, Conj.):</td>
				<td><input type="text" class="text upper" id="compl" name="compl" maxLength="30" value="<?=$_POST['compl']?>" /></td>
			</tr>
			<tr>
				<td class="label">Bairro:</td>
				<td><input type="text" class="text upper" id="bairro" name="bairro" maxLength="50" value="<?=$_POST['bairro']?>" /></td>
			</tr>
			<tr>
				<td class="label">Cidade:</td>
				<td><input type="text" class="text upper" id="cidade" name="cidade" maxLength="50" value="<?=$_POST['cidade']?>" /></td>
			</tr>
			<tr>
				<td class="label">Estado:</td>
				<td>
					<select name="uf" id="uf" size="1" class="text">
						<option selected value="">Selecione</option><?
						$query = mssql_query("SELECT * FROM estados ORDER BY estado");
						while($rs = mssql_fetch_object($query)):
							$selected=($_POST['uf'] == $rs->uf)?" selected":'';
							echo"<option $selected value='$rs->uf'>$rs->uf: $rs->estado</option>";
						endwhile;	?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="label">Responsável:</td>
				<td>
					<input type="text" class="text upper" id="responsavel" name="responsavel" maxLength="60" value="<?=$_POST['responsavel']?>" /></td>
			</tr>
			<tr>
				<td class="label">E-mail:</td>
				<td><input type="text" class="text lower" id="email" name="email" maxlength="80" value="<?=$_POST['email']?>" /></td>
			</tr>
			<tr>
				<td class="label" nowrap>DDD/Fone Comercial:</td>
				<td><input type="text" class="fone" id="fone" name="fone" value="<?=$_POST['fone']?>" /></td>
			</tr>
			<tr>
				<td class="label">DDD/Celular:</td>
				<td><input type="text"  class="fone" id="cel" name="cel" value="<?=$_POST['cel']?>" /></td>
			</tr>
			<tr>
				<td class="label">Área de Atuação:</td>
				<td><input type="text" class="text upper" name="area" id="area" maxlength="100" value="<?=$_POST['area']?>" /></td>
			</tr>
			<tr>
				<td colspan="2" class="label" style="text-align:center">
					Descrição da Solicitação:<br />
					<textarea class="text" name="descricao" id="descricao" rows="4" style="width:100%"><?=$_POST['descricao']?></textarea></td>
			</tr>
			<tr>
				<td colSpan="2" style="text-align:center; background:#eeeee4">
					<input type="submit" id="btSend" class="buttons" value="Enviar" name="btContato"></td>
			</tr>
		</table>
	</form>
</center>
