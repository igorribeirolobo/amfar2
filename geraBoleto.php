<!-- <div id="mainTop" style="background:url(images/mainBoletos.png) no-repeat; height:24px"></div><br /> -->
<?
include 'global.php';
include 'includes/funcoesUteis.php';

$id=$_GET['id'];	// aponta para o id do financeiro
if (!$id || $id==0) die("Não foi informado o ID do usuário!");
	
$query=mssql_query("select 
	c.uid, c.nome, c.cpf, c.ender, c.bairro, c.cep, c.cidade, c.uf, c.email,
	f.numDoc, f.historico, convert(char(10), f.vcto, 102) as vcto, convert(char(10), f.vcto, 103) as dataVcto, f.valor 
	from cadastro c, financeiro f
	where (f.id=$id and c.uid=f.uid)");

$user=mssql_fetch_object($query);
debug($user);

$emissao=date('Y.m.d');
$dataLimite = date("d/m/Y", time() + (30 * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
$dataLimite=explode('/', $dataLimite);
$dataLimite="{$dataLimite[2]}.{$dataLimite[1]}.{$dataLimite[0]}";

$query=mssql_query("select max(id) as maxid from boletos");
if (mssql_num_rows($query)) {
	$boleto_id=mssql_result($query, 0, 'maxid')+1;
	}
else
	 $boleto_id=1;

$query=mssql_query("select id from boletos where numDoc='$user->numDoc'");
//echo mssql_num_rows($query);
if (mssql_num_rows($query)==0) {
	$sql= "insert into boletos(id, uid, sacado, ender1, ender2, cpf, email, numdoc, valor, referente, emissao, vcto, dataLimite) values (
		$boleto_id, $user->uid, '$user->nome', '$user->ender - $user->bairro', '$user->cep - $user->cidade/$user->uf', '$user->cpf', '$user->email', '$user->numDoc', $user->valor, '$user->historico', '$emissao', '$user->vcto','$dataLimite')";
//	echo "$sql";
	$query=mssql_query($sql) or die("Erro: $sql");
	}
else {
	$boleto_id=mssql_result($query, 0, 'id');
	$sql= "update boletos set
		valor=$user->valor,
		referente='$user->historico',
		emissao='$emissao',
		vcto='$user->vcto',
		dataLimite='$dataLimite'
		where id=$boleto_id";
	$query=mssql_query($sql) or die("Erro: $sql");
	}

/* fim do gera boleto
	talvez seja necessario registrar o boleto numa tabela do financeiro
*/

include("mail/htmlMimeMail.php");
$eBody="
<table border=0 cellspacing=2 cellpadding=2 width=100%>		
	<tr>
		<td class=tright>Nome:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->nome</td>
		<td class=tright>CPF:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->cpf</b></td></tr>
		
	<tr>
		<td class=tright>Referente:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->historico</b></td>
		<td class=tright>Nº Documento:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->numdoc</b></td></tr>
		
	<tr>
		<td class=tright>Vencimento:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->dataVcto</b></td>
		<td class=tright>Valor R$:</td>
		<td bgcolor=#ffffff class=boxborder><b>$user->valor</b></td>
		
	<tr>
		<td valign=top colspan=4>
			Foi gerado um boleto com os dados acima</td></tr>
			
	<tr>
		<td colspan=4 class=tcenter>
			Caso não tenha impresso seu boleto, 
			<a href='$urlSite/boletos/boleto.php?id=$boleto_id'>CLIQUE AQUI</a><br>
			para imprimir e pagar em qualquer banco até o vencimento.</td>
	</tr>
</table>";


//	function sendMail($mto, $mnf, $mmf, $ms, $mtb,$tit)
//	copia para usuario
$mailsend = sendMail($emal, 'AMFAR|Boleto', $eBoletos, "AMFAR|Boleto Nº $numdoc", $eBody,'COBRANÇA');
$mailsend = sendMail($eBoletos, 'AMFAR|Boleto', $webmaster, "AMFAR|Boleto Nº $numdoc", $eBody,'COBRANÇA');
?>
		
<script LANGUAGE="Javascript">
	function boleto() {
		window.open('boletos/boleto.php?id=<?=$boleto_id?>','','width=700, height=500, menubar, scrollbars, resizable');
		}
</script>

<div id="mainTop" style="background:url(images/mainBoleto.png) no-repeat; height:24px"></div><br />
<table border="0" cellpadding="0" width="100%">			
	<tr>
		<td align="center">
			<b>Prezado(a) <b><?=$nome?></b><br/><br/> 
			<b>Boleto gerado com sucesso!</b></td></tr>
	<tr>
		<td align="center"><br/>Uma cópia foi enviada para o seu e-email: <?=$email?><br/></td></tr>
	<tr>
		<td align="center" height="30" >Atenciosamente,</td></tr>
	<tr>
		<td align="center">
			<p>Caso possua uma impressora a laser ou jato de tinta, 
			<a href="javascript:boleto(<?=$boleto_id?>)"><b>CLIQUE AQUI</b></a><br>
			para exibir e imprimir um boleto banc&aacute;rio
			que poder&aacute; ser pago em qualquer banco.</p></td></tr>				
	<tr>
		<td align="center" height="50" ><em><strong>AMFAR|Cobrança</em></td></tr>
</table>
