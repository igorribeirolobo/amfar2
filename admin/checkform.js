	// cria array de meses para maximo dia

	function CriaArray (n) { this.length = n }

	maxDay = new CriaArray(13);
	maxDay[0] = 0
	maxDay[1] = 31
	maxDay[2] = 28
	maxDay[3] = 31
	maxDay[4] = 30
	maxDay[5] = 31
	maxDay[6] = 30
	maxDay[7] = 31
	maxDay[8] = 31
	maxDay[9] = 30
	maxDay[10] = 31
	maxDay[11] = 30
	maxDay[12] = 31

//************ converte toda entrada em maiúsculas

function toUpper(f) {	
	f.value=f.value.toUpperCase();
	}



//************ converte toda entrada em minúsculas

function toDown(f) {
	s=f.value.toLowerCase();
	return s;
	}


//************ função para validar data

function checkDate(f) {

	s=limpa_string(f.value);

	if (s.length < 8) {
		alert("Data inválida - Digite no formato DD/MM/AAAA !" + f.name);
		f.focus();
		return false;
		}

	dia=parseInt(s.substr(0,2));
	mes=parseInt(s.substr(2,2));
	ano=parseInt(s.substr(4,4));
	
	hoje = new Date();
	yearSys = hoje.getYear()-16;

	// verifica se ano é válido
	if (ano < 1900 || ano > (yearSys)) {
		alert("Ano  inválido - Digite o ano entre 1900 e " + yearSys + " !");
		return false;
		}
		
	if (ano % 4 == 0) maxday[2]=29;

	// verifica se mes esta entre 1..12
	if (mes < 1 || mes > 12) {
		alert("Mês inválido - Digite um mês entre 01 e 12 !");
		return false;
		}

	// verifica se dia é válido
	if (dia < 1 || dia > maxDay[mes]) {
		alert("Dia inválido - Digite o dia entre 01 e " + maxDay[mes] +"!");
		return false;
		}

	// chegou até aqui entao está tudo correto, é so formatar e retornar o resultado
	
	if (dia < 10) dia='0'+dia;

	if (mes < 10) mes='0'+mes;

	f.value=dia+'/'+mes+'/'+ano;
	}



//*********** função para validação de cep
function checkCep(f)	{
	s = limpa_string(f.value);
	
	if (s.length < 8) {
		alert("Seu 'CEP' ter ser no formato: 00000-000.");
		f.focus();
		return false;
		}
	f.value=s.substr(0,5) +'-'+s.substr(5,3)
	}



//*********** função para validação de e-mail
function checkMail(f)	{
	var invalid;
	invalid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;

	if (invalid.test(f.value) == false) {
		f.style.color = "red";
		alert("Endereço de E-mail inválido !");
		f.focus();
		return false;
		}
		
	f.value=f.value.toLowerCase();
	return true;
		
	}



//************ função para checar CPF/CNPJ
function checkCPF(f) {

	s = limpa_string(f.value);
	// checa se é cpf
	
	if (s.length == 11) {
	
		if (valida_CPF(f.value) == false) {
			alert("O CPF não é válido !");
			f.focus();
			return false;
			}

		cpf1=s.substr(0,3);
		cpf2=s.substr(3,3);
		cpf3=s.substr(6,3);
		ctrl=s.substr(9,2);
		f.value=cpf1 + '.' + cpf2 + '.' + cpf3 + '-' + ctrl;		
		}
	
	else if (s.length == 14) {
	// checa se é cnpj
	
		if (valida_cnpj(f.value) == false ) {
			alert("O CNPJ não é válido !");
			f.focus();
			return false;
			}
			
		cnpj1=s.substr(0,2);
		cnpj2=s.substr(2,3);
		cnpj3=s.substr(5,3);
		cnpj4=s.substr(8,4);
		ctrl=s.substr(12,2);
		
		f.value = cnpj1 + '.' + cnpj2 + '.' + cnpj3 + '/' + cnpj4 + '-' + ctrl;		
		}
	else	{
		alert("O CPF ou CNPJ não é válido !");
		f.focus();
		return false;
		}
	}


//************  Deixa so' os digitos no numero
function limpa_string(S)	{

	var Digitos = "0123456789";
	var temp = "";
	var digito = "";
	for (var i=0; i<S.length; i++)	{
		digito = S.charAt(i);
		if (Digitos.indexOf(digito)>=0)	{
			temp=temp+digito	}
			
		}
		
	return temp
	}



//************ Valida entrada do cpf
function valida_CPF(s)	{
	var i;
	s = limpa_string(s);
	var c = s.substr(0,9);
	var dv = s.substr(9,2);
	var d1 = 0;
	
	for (i = 0; i < 9; i++)	{
		d1 += c.charAt(i)*(10-i);
		}
	
	if (d1 == 0) return false;
	
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	
	if (dv.charAt(0) != d1)	{
		return false;
		}

	d1 *= 2;
	
	for (i = 0; i < 9; i++)	{
		d1 += c.charAt(i)*(11-i);
		}
		
	d1 = 11 - (d1 % 11);
	
	if (d1 > 9) d1 = 0;
	
	if (dv.charAt(1) != d1)	{
		return false;	}
		
	return true;
	}



//************ Valida entrada do cpf
function valida_cnpj(s)	{
	var i;
	s = limpa_string(s);
	var c = s.substr(0,12);
	var dv = s.substr(12,2);
	var d1 = 0;
	for (i = 0; i < 12; i++)	{
		d1 += c.charAt(11-i)*(2+(i % 8));
		}
	if (d1 == 0) return false;
	
	d1 = 11 - (d1 % 11);
	
	if (d1 > 9) d1 = 0;
	
	if (dv.charAt(0) != d1)	{
		return false;
		}

	d1 *= 2;
	for (i = 0; i < 12; i++)	{
		d1 += c.charAt(11-i)*(2+((i+1) % 8));
		}
		
	d1 = 11 - (d1 % 11);
	
	if (d1 > 9) d1 = 0;
	
	if (dv.charAt(1) != d1)	{
		return false;	}
	
	return true;
	}