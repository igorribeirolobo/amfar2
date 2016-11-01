try {
    xmlhttp = new XMLHttpRequest();
	} catch(ee) {
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch(e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(E) {
            xmlhttp = false;
        }
    }
}

var processing;

// ****** Funcao que extrai os codigos <script das paginas *****
function ExtraiScript(texto){
	var ini, pos_src, fim, codigo, texto_pesquisa;
	var objScript = null;
    
	texto_pesquisa = texto.toLowerCase()	//Joga na variavel de pesquisa o texto todo em minusculo para na hora da pesquisa nao ter problema com case-sensitive
	ini = texto_pesquisa.indexOf('<script', 0)	// Busca a primeira tag <script
    
	while (ini!=-1) {	// Executa o loop enquanto achar um <script
            
		var objScript = document.createElement("script");	//Inicia o objeto script
		pos_src = texto_pesquisa.indexOf(' src', ini)	// Busca se tem algum src a partir do inicio do script
		ini = texto_pesquisa.indexOf('>', ini) + 1;	 // Define o inicio para depois do fechamento dessa tag

		//Verifica se este e um bloco de script ou include para um arquivo de scripts
		//Se encontrou um "src" dentro da tag script, esta e um include de um arquivo script
        
		if (pos_src < ini && pos_src >=0) {
			ini = pos_src + 4;	 //Marca como sendo o inicio do nome do arquivo para depois do src
			fim = texto_pesquisa.indexOf('.', ini)+4;	//Procura pelo ponto do nome da extencao do arquivo e marca para depois dele
			codigo = texto.substring(ini,fim);	//Pega o nome do arquivo
			//Elimina do nome do arquivo os caracteres que possam ter sido pegos por engano
			codigo = codigo.replace("=","").replace(" ","").replace("\"","").replace("\"","").replace("\'","").replace("\'","").replace(">","");
			objScript.src = codigo;	// Adiciona o arquivo de script ao objeto que sera adicionado ao documento
			}
			
		else	{	//Se nao encontrou um "src" dentro da tag script, esta e um bloco de codigo script
            
			fim = texto_pesquisa.indexOf('</script>', ini);	// Procura o final do script
			codigo = texto.substring(ini,fim);	// Extrai apenas o script
			objScript.text = codigo;	// Adiciona o bloco de script ao objeto que sera adicionado ao documento
			}
      	
		document.body.appendChild(objScript);	//Adiciona o script ao documento
		ini = texto.indexOf('<script', fim);	// Procura a proxima tag de <script
		objScript = null;	//Limpa o objeto de script
		}
	}


var alerta="<br><br><br><br><br><br><center><img src='images/loading.gif'><br><b>Carregando.</b></center>";


// ****** função para ler todas as paginas e mostrar na div content *****
function ajaxLoader(url, id)	{
//	var alerta="<br><br><br><br><br><br><center><img src='images/loading.gif'><br><b>Carregando.</b></center>";
	var div=document.getElementById(id);	

	xmlhttp.open('GET', url, true);
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState < 4)
			div.innerHTML=alerta;	// aguarde
		
		if(xmlhttp.readyState == 4) {		
			if(xmlhttp.status == 200) {				
				conteudo=xmlhttp.responseText;
				ExtraiScript(conteudo);				
				div.innerHTML = conteudo;
				}
		else 
			div.innerHTML='Página não localizada';
			}
		}
	xmlhttp.send(null);
	}


function changeDisplay(id) {	

	var div = document.getElementById(id);
	var ic=document.getElementById('ic'+id);
	
	if (div.className=='invisivel') {
		div.className='visivel';
		if (ic)
			ic.setAttribute('src',"images/close.gif");
		}
	else {
		div.className='invisivel';
		if (ic)
			ic.setAttribute('src',"images/open.gif");
		}
	}
	

function replaceAll(str, from, to) {
	var idx = str.indexOf(from);
	while (idx > -1) {
		str = str.replace(from,to);
		idx = str.indexOf(from);
		}
	return str;
	}

function formFields(oForm)	{
	
	var aParams = new Array();
	for (var i=0 ; i < oForm.length; i++) {	
		var nome = oForm[i].name;
		var valor = replaceAll(oForm[i].value, '&','%26');
	

		if (oForm[i].type == 'checkbox' || oForm[i].type == 'radio') {
			if (oForm[i].checked) aParams.push(nome+'='+valor);
			}

		else if (oForm[i].type == 'button') {
			}
			
		else if(valor.length > 0)
			aParams.push(nome+'='+valor);
		}
	return aParams.join("&");	
	}
	



/*
	function responsavel para envio dos forms
	passar o div para mostrar o resultado
	caso o div não seja passado assumira o dvContent
*/

function checkError(message) {
	if (message.charAt(0)==' ')
		return true;
	else return false;
	}


function sendPost(url, param, id){
//	alert('url='+url+' param='+param);
	ajax = null; 
   // Procura por um objeto nativo (Mozilla/Safari)
   
	divID = document.getElementById(id);
   
   ajax = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");   
	ajax.onreadystatechange = function() {
			
		var msg = document.getElementById('msg');
		if(!msg)	{	
			msg = document.createElement('div');
			msg['id'] = 'msg';
			divID.appendChild(msg);
			}			

		if(ajax.readyState < 3)				
			msg.innerHTML = alerta +'<br><p>Aguarde...Conectando ao servidor...</p>';
			
		if(ajax.readyState == 4) {
			msg.innerHTML = alerta +'<br><p>Aguardando resposta do servidor...</p>';

			if(ajax.status == 200) {
				if (checkError(ajax.responseText))	{
					msg.innerHTML = ajax.responseText;
					return false;
					}	// if (erro =='*')
					
				else	{
				//	msg.innerHTML = 'Success !';
					conteudo=ajax.responseText;
					ExtraiScript(conteudo);
				//	alert(ajax.responseText);
					divID.innerHTML = ajax.responseText;
					}	// else
					
				}	// if(ajax.status == 200)
				
			else { 
				msg.innerHTML = 'AJAX Error Code: ' + ajax.status + ' - ' + ajax.statusText ;
				return false;
				}	// else

			}	//	onreadystatechange =4
			
		}	// ajax.onreadystatechange
		
	ajax.open("POST", url, true); 
	ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	ajax.send(param);
	}	// function
	

/*
	function para validar formulario
	todo campo deve ser identificado por id
	todo id do campo deve ser precedido por:
	nr_ para campos numéricos
	st_ para string
	dt_ para data
*/

function toUpper(objform) {
	objform.value=objform.value.toUpperCase()
	}

function toLower(objform) {
	objform.value=objform.value.toLowerCase()
	}

function checkForm(idForm) {
	var objForm=document.getElementById(idForm);
	var len=objForm.length;

	if (objForm.process.value==true) {
		alert('Aguarde, processo em andamento!');
		return false;
		}	
	
	
	for (x=0; x < len; x++) {		
		var required=(objForm[x].className=='required') ? true : false;	// pode ser required ou nulo
		var msg=objForm[x].title;		// mensagem de erro
		var name=objForm[x].name;		// nome do campo
		var id=objForm[x].id.toLowerCase();	// id do campo		
		var value=objForm[x].value		// quantidade máxima
				
		if (required) {
			if (value.length==0) {
				alert("Campo '"+ id.toUpperCase() +"' não pode ficar em branco !");
				objForm[x].focus();
				return false;
				}
				
		if ((id.indexOf('dat', 0)!=-1) && (!checkData(objForm[x].id))) return false;
			// tipo comuns
			if ((id=='email') && !checkEmail(objForm[x])) return false;
		//	if ((id=='fone' || id=='cel') && !checkFone(objForm[x])) return false;
		// 	if ((id=='cep') && !checkCEP(objForm[x])) return false;
			if (id=='cpf' || id=='cnpj') {
				objForm[x].value=soNumeros(value);
				if(!checkCNPJF(objForm[x].id)) return false;
				}
			}
		}

	objForm.process.value=true;
	return true;
	}


function checkCNPJF(idObj) {
	// primeiro deixa somente numeros
	
	objForm=document.getElementById(idObj);	

	cnpj = soNumeros(objForm.value);
	
	if (objForm.value.length == 0) {
		alert("Favor informar seu CNPJ ou CPF");
		objForm.focus();
		return false; }		
	
	if (!cnpjfValido(cnpj)) {
		alert("O CNPJ ou CPF é inválido !");
		objForm.focus();
		return false;				
		}
/*
	if (cnpj.length==11) {	// CPF
		p1 = cnpj.substr(0,3);
		p2 = cnpj.substr(3,3);
		p3 = cnpj.substr(6,3);
		p4 = cnpj.substr(9,2);
		objForm.value = p1+'.'+p2+'.'+p3+'-'+p4;
		}
	else {
		p1 = cnpj.substr(0,2);
		p2 = cnpj.substr(2,3);
		p3 = cnpj.substr(5,3);
		p4 = cnpj.substr(8,4);
		p5 = cnpj.substr(12,2);
		objForm.value = p1+'.'+p2+'.'+p3+'/'+p4+'-'+p5;
		}
	*/
			
	return true;
	}
	
/* ********  funcao para CNPJ e CPF ************************
	checa o cnpj e retorna no campo do form conforme o status
	status=0 não mexe,
	status=1 retorna campo sem formatação ou seja so numeros
	status=2 retorna campo com formatação
		CPF: 999.999.999-99 ou CNPJ: 99.999.999/0000-00
*/
function cnpjfValido(source) {
	var i, len;
	s = soNumeros(source);
	len=s.length-2;
	if ((len != 9) && (len != 12)) {		
		return false;
		}

	var c = s.substr(0,len);
	var dv = s.substr(len,2);
	var d1 = 0;
	for (i = 0; i < len; i++) {
		if (len==9) d1 += c.charAt(i) * (10 - i);
		else d1 += c.charAt(11 - i) * (2 + (i % 8));
		}

	if (d1 == 0) return false;
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(0) != d1) return false;

	d1 *= 2;
	for (i = 0; i < len; i++) {
		if (len < 12) { d1 += c.charAt(i) * (11-i); }
		else { d1 += c.charAt(11 - i) * (2 + ((i + 1) % 8));}
		}

	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(1) != d1) return false;	
	
	return true;
	}
	
	

function soNumeros(Str){
// Deixa so' os digitos no numero

	var Digitos = "0123456789";
	var temp = '';
	var digito = '';
	for (var i=0; i < Str.length; i++)	{
		digito = Str.charAt(i);
		if (Digitos.indexOf(digito) >= 0)	{
			temp += digito; }
		}
	return temp;
	}



/* ************************* checkCEP ************************* */
function checkCEP(objForm) {
	var invalid;
	invalid = /^\d{5}-\d{3}$/;

	if (objForm.value == '')	{
		alert('Campo '+objForm.name.toUpperCase()+' não pode ficar em branco!\nDigite no formato 9999-999');
		if (objForm.type!='hidden')
			objForm.focus();
		return false;
		}
		
	if (invalid.test(objForm.value) == false) {
		oldColor=objForm.style.color;
		objForm.style.color = "#c40000";
		alert("Favor informar corretamente o campo "+objForm.name.toUpperCase()+'\nDigite no formato 99999-999');
		objForm.focus();
		objForm.style.color=oldColor;
		return (false); }
	
	return true;
	}	//	end function
	



function checkEmail(objform) {
	var invalid;
	invalid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

	if (objform.value == '')	{
		alert('Campo email não pode ficar me branco!');
		objform.focus();
		return false;
		}
	if (invalid.test(objform.value) == false) {
		objform.style.color = "red";
		alert("Favor informar corretamente seu e-mail.");
		objform.focus();
		return (false); }

	return true;
	}
	

// função para formatar Data
function formataData(e, id)	{
	
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;

	var character = String.fromCharCode(code);

	if(code==8)
		{}
	else	{
		if(code > 47 && code < 58) {
			//-------------
			var conteudo;
			conteudo = document.getElementById(id).value;
			len=conteudo.length;
			if(len==2 || len==5) {
				conteudo = conteudo + "/";
				document.getElementById(id).value = conteudo;
				}
			}
		else {
			event.keyCode = 0;
			}
		}
	}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
////////                            Criado por : Flavio Theruo Kaminisse                           ////////
////////                                email: falecomjaps@gmail.com                               ////////
////////                              url: http://www.japs.etc.br                                  ////////
////////                                  Data Criao : 30/08/2005                                  ////////
////////                                                                                           ////////
////////                               - Compativel com MSIE e Firefox.                            ////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
//Funcao que valida a data
function checkData(idData) {
	var date = document.getElementById(idData).value;
	var array_data = new Array;
	var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	//vetor que contem o dia o mes e o ano
	array_data = date.split("/");
	erro = false;
	//Valido se a data esta no formato dd/mm/yyyy e se o dia tem 2 digitos e esta entre 01 e 31
	//se o mes tem d2 digitos e esta entre 01 e 12 e o ano se tem 4 digitos e esta entre 1000 e 2999
	if ( date.search(ExpReg) == -1 )
		erro = true;
	//Valido os meses que nao tem 31 dias com execao de fevereiro
	else if ( ( ( array_data[1] == 4 ) || ( array_data[1] == 6 ) || ( array_data[1] == 9 ) || ( array_data[1] == 11 ) ) && ( array_data[0] > 30 ) )
		erro = true;
	//Valido o mes de fevereiro
	else if ( array_data[1] == 2 ) {
		//Valido ano que nao e bissexto
		if ( ( array_data[0] > 28 ) && ( ( array_data[2] % 4 ) != 0 ) )
			erro = true;
		//Valido ano bissexto
		if ( ( array_data[0] > 29 ) && ( ( array_data[2] % 4 ) == 0 ) )
			erro = true;
	}
	if ( erro ) {
		alert("Data Invalida");
		document.getElementById(idData).focus();
		return false;
		}
	return true;
	}
	
/* ******************** checkFone ******************** */
function checkFone(objForm) {
	var invalid;
	invalid = /^\d{2} \d{4}-\d{4}$/;

	if (objForm.value == '')	{
		alert('Campo '+objForm.name.toUpperCase()+' não pode ficar em branco!\nDigite no formato 99 9999-9999');
		objForm.focus();
		return false;
		}
		
	if (invalid.test(objForm.value) == false) {
		oldColor=objForm.style.color;
		objForm.style.color = "#c40000";
		alert("Favor informar corretamente o campo "+objForm.name.toUpperCase()+'\nDigite no formato 99 9999-9999');
		oldColor=objForm.style.color=oldColor;
		objForm.focus();		
		return (false);
		}
	
	return true;
	}	// end function


function abreTela(popupfile,winheight,winwidth) {
	winPop=window.open(popupfile,"popWin","resizable=no,height=" + winheight + ",width=565,height=360, scrollbars=yes");
	winPop.focus();
	}
	
function ampliar(url) {
	winFoto=window.open(url,"fotoWin","resizable=no,width=440,height=296,scrollbars=no, toolbar=no, menubar=no, status=no");
	winFoto.focus();
	}
	
	
function sendContato() {
	var invalid;
	invalid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	obForm = document.formContato;
	
	if (obForm.name.value == "") {
		alert("Favor informar seu nome.");
		obForm.name.focus();
		return false; }

	if (!checkEmail(obForm.email)) return false;

	if (soNumeros(obForm.phone.value).length <  10) {
		alert("Favor informar seu nº de telefone no formato (xx) xxxx-xxxx.");
		obForm.phone.focus();
		return (false); }

	if (obForm.uf.value=='') {
		alert("Favor selecionar o seu estado.");
		obForm.uf.focus();
		return false; }


	if (obForm.subject.value == "") {
		alert("Favor informar o assunto.");
		obForm.subject.focus();
		return false; }

	if (obForm.obs.value == "") {
		alert("Favor preencher o campo mensagem.");
		obForm.obs.focus();
		return false; }

	if(!confirm("Seu e-mail está pronto para ser enviado.\nTecle [ok] para envio ou [cancelar].\nApós teclar OK aguarde a tela de confirmação!"))	
		return false;
		
	// cria variavel form e chama função para criar os parametros que serão enviados via post pelo ajax.sendPost
	sendPost('contato.php', formFields(obForm));
  }
  
 function abrir(url) {	
	var artWin= window.open(url,'winArt','left=0, top=0, width=780,height=400,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
	artWin.focus();
	}

function noticias(url) {	
	var notWin= window.open(url,'winNot','left=0, top=0, width=780,height=400,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
	notWin.focus();
	}
function eventos(url) {	
	var evtWin= window.open(url,'winEvt','left=0, top=0, width=560,height=540,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no');
	evtWin.focus();
	}

function sendCaixa() {
	if (!checkForm('lctos')) return false;
	myForm=document.lctos;
	param = formFields(myForm);
	sendPost('processCaixa.php', param, 'dvSave');
	
	for (x=0; x < myForm.elements.length; x++) {
		if (myForm[x].type=='text') myForm[x].value='';
		if (myForm[x].type=='select-one') myForm[x][0].selected=true
		};
	}

function sendConta() {
	if (!checkForm('novaconta')) return false;
	myForm=document.novaconta;
	param = formFields(myForm);
	sendPost('administrar.php', param, 'dvSave1');
	
	for (x=0; x < myForm.elements.length; x++) {
		if (myForm[x].type=='text') myForm[x].value='';
		if (myForm[x].type=='select-one') myForm[x][0].selected=true
		};
	}
