/*
 * e-Mailing - Sistema automatizado para criação e envio de emails por lote
 * Copyright (C) 2007 Lauro A. L. Brito
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Este é o arquivo de configuração da aplicação
 */

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

var running=false;
var runnID=null;
var loop;
var nAction;
var nIdForm;

var alerta="<br><br><br><center><img src='images/loaded.gif'><br><b>Carregando.</b></center>";


// function responsavel para envio dos forms
function ajaxLoader(url, target)	{
	
	req= (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	if (!req) {
		alert("Objeto AJAX não pôde ser criado");
		return false;
		}

	var tDiv = document.getElementById(target);
	var divMsg = document.getElementById('msgErr');
	
	if(!divMsg)	{	
		divMsg = document.createElement('div');
		divMsg['id'] = 'msgErr';
		tDiv.appendChild(divMsg);
		}

	req.onreadystatechange = function() {
	
		if(req.readyState < 4)				
			tDiv.innerHTML = alerta;

		if(req.readyState == 4) {

			if(req.status == 200) {
				erro=req.responseText.substr(0,1);

				if (erro =='*')	{					
					divMsg.innerHTML = req.responseText;
					return false;
					}	// if (erro =='*')

				else	{
					conteudo=req.responseText;
					ExtraiScript(conteudo);
					tDiv.innerHTML = conteudo;						
					}	// else

				}	// if(req.status == 200)

			else { 
				divMsg.innerHTML = "File:"+ url +" AJAX Error Code: " + req.status + ' - ' + req.statusText ;
				return false;
				}	// else

			}	// if(req.readyState == 4)
		}	// end function			
	
	req.open("GET", url, true);
	req.send(null);
	}



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


function formFields(idForm) {
	var elementosFormulario = document.getElementById(idForm).elements;
	var qtdElementos = elementosFormulario.length;
	var queryString = "";
	var elemento;

	//Cria uma funcao interna para concatenar os elementos do form
	this.ConcatenaElemento = function(nome,valor) { 
		if (queryString.length > 0) { 
			queryString += "&";
			}
		queryString += encodeURIComponent(nome) + "=" + encodeURIComponent(valor);
		};


	//Loop para percorrer todos os elementos
	for (var i=0; i<qtdElementos; i++) {
		// Pega o elemento
		elemento = elementosFormulario[i];
		if (!elemento.disabled) {
			//Trabalha com o elemento caso ele nao esteja desabilitado
			switch(elemento.type) {
				//Realiza a acao dependendo do tipo de elemento
				case 'text': case 'password': case 'hidden': case 'textarea': 
				this.ConcatenaElemento(elemento.name,elemento.value);
				break;
				case 'select-one':
					if (elemento.selectedIndex>=0) {
						this.ConcatenaElemento(elemento.name,elemento.options[elemento.selectedIndex].value);
						}
					break;
					
				case 'select-multiple':
					for (var j=0; j<elemento.options.length; j++) {
						if (elemento.options[j].selected) {
							this.ConcatenaElemento(elemento.name,elemento.options[j].value);
							}
						}
				break;
				
				case 'checkbox': case 'radio':
					if (elemento.checked) {
						this.ConcatenaElemento(elemento.name,elemento.value);
						}
				break;
				
				}	// switch
				
			}	// if (!elemento.disabled)
			
		}	//	for (var i=0; i<qtdElementos; i++)
		
	return queryString;
	}



// function responsavel para envio dos forms
function sendPost (url, idForm)	{

   // Procura por um objeto nativo (Mozilla/Safari)	
	req= (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	if (!req) {
		alert("Objeto AJAX não pôde ser criado");
		return false;
		}
		
	var param = formFields(idForm);

//	alert('param='+param);
	var tDiv = document.getElementById('dvSender');
	var divMsg = document.getElementById('msgErr');
	
	nAction=url;
	nIdForm=idForm;
	
	
	if(!divMsg)	{	
		var divMsg = document.createElement('div');
		divMsg['id'] = 'msgErr';
		tDiv.appendChild(divMsg);
		}

	req.onreadystatechange = function() {
	//	alert(req.readyState + ' - ' + req.status + ' -  url='+ url);
	
		if(req.readyState < 4)				
			divMsg.innerHTML = alerta;

		if(req.readyState == 4) {

			if(req.status == 200) {
				erro=req.responseText.substr(0,1);

				if (erro ==' ')	{
					divMsg.innerHTML = req.responseText;
					return false;
					}	// if (erro =='*')

				else	{
					conteudo=req.responseText;
					ExtraiScript(conteudo);
					tDiv.innerHTML = conteudo;						
					}	// else

				}	// if(req.status == 200)

			else { 
				divMsg.innerHTML = "File:"+ url +" AJAX Error Code: " + req.status + ' - ' + req.statusText ;
				return false;
				}	// else

			}	// if(req.readyState == 4)
		}	// end function			
	
	req.open("POST", url, true);
	req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	req.send(param); 
	}


// ****** função para ler todas as paginas e mostrar na div content *****
function openThisFile(theFile)	{
	
	document.location.href='newsletter.php?fname='+theFile;
	
	/*
	var div=document.getElementById("content");

	xmlhttp.open('GET', url, true);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==0 || xmlhttp.readyState < 3)
			div.innerHTML=alerta;	// aguarde
			
		if(xmlhttp.readyState == 4) {
		
			if(xmlhttp.status == 200) {
				conteudo=xmlhttp.responseText;
				ExtraiScript(conteudo);				
				div.innerHTML = conteudo;
	//			alert(div.innerHTML = conteudo);
				}
			}
		}
	xmlhttp.send(null);
	*/
	}




function newsSave() {
	myForm=document.newsletter;

	if (myForm.namefile.value=='') {
		alert("Digite o nome do arquivo !");
		myForm.namefile.focus();
		return false;
		}
		
//	param = formFields(myForm);
//	alert('param='+param+' fck='+myForm.fckeditor1.value);
//	sendPost('newsletterSave.php', param);
	return true;
	}
	
function sendLogin() {

	if (document.usrLog.senha.value =='') {
		alert('Por favor, digite sua senha!');
		document.usrLog.senha.focus();
		return false;
		}

	sendPost('userLogin.php','usrLog');
	}