/* insere o style utilizado pelo calendario */

/*
	3-month calendar script- Ada Shimar (adashimar@chalktv.com)
	script featured on and available at:
	http://www.javascriptkit.com/
	Adaptado por Lauro A L Brito
*/



// toda a formatacao da tabela vir� do arquivo style.css
// fun��o que gera o calendario
var LastMonth=0;
var LastYear=0;
var dateRangeIn;
var dateRangeOut;
var dateFieldId;

var rightArrow='images/rightArrow.png';
var leftArrow='images/leftArrow.png';
var imgClose='images/closeLayer.gif';


sysMonth = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
Meses = new Array("Janeiro","Fevereiro","Mar�o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
Week = new Array('D','S','T','Q','Q','S','S');

// data atual
hoje = new Date();
mesAtual = hoje.getMonth();
anoAtual = hoje.getYear();
minimo	= mesAtual;
maximo	= mesAtual-1;


/*
	*********************************************************************
	limpa o m�s anterior na tabela de ocupa��o porque na passagem do m�s,
	o sistema vai considerar o m�s anterior como sendo do pr�ximo ano
	*********************************************************************
*/
mesAnterior = mesAtual - 1;
if (mesAnterior < 0)
	mesAnterior=11;

if (anoAtual < 2000)    // Y2K Fix, Isaac Powell
	anoAtual = anoAtual + 1900;
var yr = yr1 = anoAtual; // last month�s year
var mo = mesAtual

var dataInicial,  dataFinal;

var calendar= {
	
	setRange: function (dateIn, dateOut) {
		dateRangeIn = dateIn;
		dateRangeOut = dateOut;
		},
		
	go: function(offSet) {

		// voltar
		if (offSet == -1) {
			if (mo > 0) mo--;
			else {
				mo = 11;  // seta dezembro
				yr = yr1 = yr-1; // passa para o ano corrente
				}	// if (yr==anoAtual)

			}	//	if (offSet == 1)

		// avan�ar
		if (offSet == 1) {
			if (mo < 11) mo++;
			else {
				yr = yr1 = yr+1; // passa para o ano seguinte
				mo = 0;  // seta janeiro
				}	// if (yr==anoAtual)

			}	//	if (offSet == 1)
		calendar.show();
		},

	show: function () {
		var jHTML;
		novoMes = new Date(sysMonth[mo] + " 1," + yr1); // assign to date
		brancos = novoMes.getDay();	// dias inexistentes
		yr = eval(yr);

		maxDay = 31;
		if (mo ==1) {
			maxDay=28;
			if (yr % 4 == 0) maxDay=29;	}
		else
			if (mo==3 || mo==5 || mo==8 || mo==10) maxDay=30;

	//	alert('mo='+mo+' dias='+idxMes[mo]);

		document.getElementById('mes').innerHTML = Meses[mo] + " " + yr;

		jHTML='<table border="0" width="100%" align="center"/><tr>\r\n';
		// imprimi o nome dos dias da semana
		for (var i = 0;i < 7; i ++)
			jHTML += '<td id=diasSemana>'+ Week[i];

		// primeira semana
		jHTML += '<tr>\r\n';
		dia = 0;
		for (var i = 0; i < 7; i++) {
			if (i < brancos)
				jHTML +='<td id="dia">&nbsp;';
			else {
				dia++;
				if (dia < 10) 
					jHTML +='<td id="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">0'+ dia +'</a>';
				else
					jHTML +='<td id="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">'+ dia +'</a>';
				}
			}

		// demais semanas
		while (dia < maxDay) {
			jHTML += '<tr>\r\n';
			for (var i = 0; i < 7; i++) {
				dia++;
				if (dia > maxDay)
					jHTML += '<td id="dia">&nbsp;';
				else
					if (dia < 10)
						jHTML += '<td id="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">0' + dia +'</a>';
					else
						jHTML += '<td id="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">' + dia +'</a>';
				}
			}

		jHTML += '</table>';

	//	remove a div anterior com o mes
		var dvMes = document.getElementById('calendar');
		var dvDias = document.getElementById('dias');
		if (!dvDias) {		
			dvDias = document.createElement('DIV');
			dvDias.id='dias';
			dvMes.appendChild(dvDias);
			}
		dvDias.innerHTML = jHTML;
		},

	open: function(objData) {
		document.getElementById('cbContas').style.visibility='hidden';
		document.getElementById('cbContas2').style.visibility='hidden';
		div=document.getElementById('dvCalendar');
		dateField=document.getElementById(objData.id);
		div.className="visivel";
		calendar.show()
		},

	setField: function(d) {
		if (d < 10) d='0'+d;
		var mes=eval(mo+1);
		if (mes < 10) mes='0'+mes;
		dateField.value=d+'/'+mes+'/'+yr;
		calendar.close();
		},

	close: function() {
		div=document.getElementById('dvCalendar');
		div.className="invisivel";
		document.getElementById('cbContas').style.visibility='visible';
		document.getElementById('cbContas2').style.visibility='visible';
		}
	}
	
/* inclui na pagina */
document.write("\
<div id=dvCalendar class=invisivel>\
	<div class=topCalendar>\
		<div class='prev'>\
			<a href='javascript:void(null)' onclick='calendar.go(-1)'/>\
			<img class='nave' src='"+leftArrow+"' border=0/></a>\
		</div>\
		<div id='mes'/></div>\
		<div id='next'/>\
			<a href='javascript:void(null)' onclick='calendar.go(1)'/>\
				<img id='nave' src='"+rightArrow+"' border=0/></a>\
		</div>\
		<div id='icClose'/>\
			<a href='javascript:void(null)' onclick='calendar.close()'/>\
			<img style='margin-top:2px' src='"+imgClose+"' border=0/></a></div>\
		</div>\
		\
		<div id='calendar'></div>\
	</div>\
</div>");