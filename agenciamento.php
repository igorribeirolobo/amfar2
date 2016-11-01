<link rel="stylesheet" href="js/formStyle.css" type="text/css"/>
<div id="mainTop" style="background:url(images/mainAgenciamento.png) no-repeat; height:24px"></div>

	<p style="background:#eeeee4; padding:4px"><img src="images/h_arrow.gif"><b>Estreitando Rela��es</b></p>

	<p style="padding:4px">
		Pensando no futuro e atenta �s constantes transforma��es do mercado,
		a <b class="blue">Associa��o Mineira de Farmac�uticos</b> concentra seus esfor�os nas pessoas
		que far�o a Empresa alcan�ar suas metas. � um trabalho que n�o se faz em um dia.
		Requer grandes investimentos, principalmente dos farmac�uticos associados,
		para captar, formar, manter e reconhecer o trabalho de sua Equipe.</p>

	<p style="padding:4px">
		<b class="blue">Modernidade, agilidade, comprometimento e seriedade</b> s�o palavras-chave
		dentro da <b class="blue">AMFAR</b>, uma Empresa em constante evolu��o, que aposta no
		sucesso e oferece o melhor atendimento aos seus Clientes.</p>
	<p style="padding:4px">
		Nossos recursos s�o as pessoas nas quais acreditamos e investimos: <b class="blue">VOC�!</b></p>

	<p style="text-align:center; padding:4px">
		<b class="blue">Portanto, precisamos nos unir.<br />
		Venha fazer parte dessa Equipe de vencedores!</b></p>

	<p style="background:#eeeee4; padding:4px"><img src="images/h_arrow.gif"><b>Envie seu Curr�culo</b></p>

	<p style="padding:4px">
		� Coloque no e-Mail a �rea de atua��o de seu interesse.<br /><br />
		� Enviar seu curr�culo em anexo para <b class="blue">curriculos@amfar.com.br</b>
			e indique se voc� � ou n�o s�cio da AMFAR, pois s�cios ter�o prioridade no processo seletivo.</p>

	<p style="padding:4px">
		<a href="#form" onClick="changeDisplay('Div1')" style="text-decoration:none">
			Clique aqui para abrir/fechar o formul�rio de solicita��o</a></p>

	<div id="Div1" class="invisivel"><a name="form"></a>
		<form name="curriculo" id="curriculo" style="margin-top:0" action="#" method="POST"  enctype="application/x-www-form-urlencoded">
			<table cellSpacing="2" cellPadding="2" width="70%" border="0" align="center">
				<tr>
					<td class="tLabel">Empresa:</td>
					<td colspan=3>
						<input size="40" style="width:296px" name="empresa" class="required" onchange="toUpper(this)"></td>
				</tr>
				<tr>
					<td class="tLabel">E-mail:</td>
					<td colspan=3><input size="40" style="width:296px" id='email' name="email"  class="required" onchange="checkEmail(this)"></td>
				</tr>
				<tr>
					<td class="tLabel">DDD/Fone:</td>
					<td><input style="width:120px" id='fone' name="fone" size="40" class="required"></td>
					<td class="tLabel">Contato:</td>
					<td><input style="width:120px" name="contato" size="40" class="required" onchange="toUpper(this)"></td>
				</tr>
				<tr>
					<td class="tLabel">Assunto:</td>
					<td colspan=3><input name="assunto" style="width:296px" size="40" value="SOLICITA��O DE CURRICULUM"></td>
				</tr>
				<tr>
					<td class="tLabel">Perfil:</td>
					<td colspan=3><textarea name="perfil" style="width:296px; height:100px" class="required" ></textarea></td>
				</tr>
				<tr>
					<td class="tLabel">Observa��es:</td>
					<td colspan=3><textarea name="obs" style="width:296px; height:100px" class="required" ></textarea></td>
				</tr>
				<tr>
					<td colSpan="4" style="text-align:center; background:#eeeee4">
						<input type="button" id="btSend" value="Enviar" name="btContato" onClick="buscaCurriculo('curriculo')"></td>
				</tr>
			</table>
		</form>
	</div>
