
<style type="text/css">
	#divNewsCont{position:absolute; width:140; height:85; overflow:hidden; top:425; left:10; clip:rect(0,140,85,0); visibility:hidden}
	#divNewsText{position:absolute; top:0; left:0}
</style>
<script type="text/javascript" language="JavaScript">
<!--
function checkBrowser(){
	this.ver=navigator.appVersion
	this.dom=document.getElementById?1:0
	this.ie5=(this.ver.indexOf("MSIE 5")>-1 && this.dom)?1:0;
	this.ie4=(document.all && !this.dom)?1:0;
	this.ns5=(this.dom && parseInt(this.ver) >= 5) ?1:0;
	this.ns4=(document.layers && !this.dom)?1:0;
	this.bw=(this.ie5 || this.ie4 || this.ns4 || this.ns5)
	return this
}
bw=new checkBrowser()

lstart=80
loop=true
// Velocidad
speed=60
pr_step=1

function makeObj(obj,nest){
    nest=(!nest) ? '':'document.'+nest+'.'
	this.el=bw.dom?document.getElementById(obj):bw.ie4?document.all[obj]:bw.ns4?eval(nest+'document.'+obj):0;
  	this.css=bw.dom?document.getElementById(obj).style:bw.ie4?document.all[obj].style:bw.ns4?eval(nest+'document.'+obj):0;
	this.scrollHeight=bw.ns4?this.css.document.height:this.el.offsetHeight
	this.newsScroll=newsScroll;
	this.moveIt=b_moveIt; this.x; this.y;
    this.obj = obj + "Object"
    eval(this.obj + "=this")
    return this
}
function b_moveIt(x,y){
	this.x=x;this.y=y
	this.css.left=this.x
	this.css.top=this.y
}
function newsScroll(speed){
	if(this.y>-this.scrollHeight){
		this.moveIt(0,this.y-pr_step)
		setTimeout(this.obj+".newsScroll("+speed+")",speed)
	}else if(loop) {
		this.moveIt(0,lstart)
		eval(this.obj+".newsScroll("+speed+")")
	  }
}
function newsScrollInit(){
	oNewsCont=new makeObj('divNewsCont')
	oNewsScroll=new makeObj('divNewsText','divNewsCont')
	oNewsScroll.moveIt(0,lstart)
	oNewsCont.css.visibility='visible'
	oNewsScroll.newsScroll(speed)
}
onload=newsScrollInit;

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<STYLE TYPE='text/css'>
	<!-- TextRollover-1 -->
	a:link { color:#003366; text-decoration:none}
	a:visited { color:#003366; text-decoration:none}
	a:hover { color:#990000; text-decoration:underline; cursor:hand}
	a:active { color:#003366; text-decoration:none}
</STYLE>
</head>
<body>
<div id="divNewsCont">
<div id="divNewsText">
<p>

<?
$texto='<p align=justify>';
$texto .='O nosso web Site foi reformulado e mudou de plataforma, ';
$texto .='migrando de Windows para Linux e isso demandou muitas mudanças ';
$texto .='drásticas em toda a estrutura do site.</p>';

$texto .='<p align=justify>';
$texto .='Estamos monitorando o seu funcionamento no entando, ';
$texto .='caso encontre algum problema durante a sua navegação, ';
$texto .='queira por favor reportar-nos (a página que gerou o erro ';
$texto .='inclusive a mensagem mostrada) para que possamos ';
$texto .='rapidamente efetuar as correções.</p>';

$texto .='<p align=justify>';
$texto .='Novos recursos foram implementados como Fórum de debates, ';
$texto .='Indique-nos, Livro de visitas, login de usuarios para ';
$texto .='acessos restritos, impressão de boleto on line. ';
$texto .='Trabalhamos muito para oferecer rapidez, dinamismo, ';
$texto .='objetividade e caso tenha alguma sugestão que venha a melhorá-lo, ';
$texto .='não hesite em comunicar-nos pois sendo a sua sugestão aproveitável, ';
$texto .='com certeza iremos implementá-la.</p>';

$texto2 ='<p align=justify>';
$texto2 .='Se você é um associado SBRAFH ativo ';
$texto2 .='ou seja, em dia com a sua anuidade, ';
$texto2 .='Clique no link <b>Login</b> e digite ';
$texto2 .='o seu <b>CPF</b> para receber em seu e-mail ';
$texto2 .='a senha criada automaticamente pelo site.<br';
$texto2 .='Ao receber a senha no seu e-mail, retorne ao ';
$texto2 .='site e clique em Login novamente para atualizar ';
$texto2 .='o seu cadastro, caso contrário não terá acesso ';
$texto2 .='à várias páginas e links restritos que farão ';
$texto2 .='parte desta nova versão do site.<br>';
$texto2 .='Após receber a sua senha, você pode entrar novamente ';
$texto2 .='no site clicando em Login e atualizar o seu cadastro ';
$texto2 .='e por favor, troque a sua senha por uma de sua preferência';
?>


<font class="Texto1">
<a href='contato.php' target='meio'><?= $texto ?></a>
<a href='usuarios/cadastro.php' target='meio'><?= $texto2 ?></a>
</div>
</div>
</body>
</html>