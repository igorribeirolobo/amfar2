/* insere o style utilizado pelo calendario */

/*
	3-month calendar script- Ada Shimar (adashimar@chalktv.com)
	script featured on and available at:
	http://www.javascriptkit.com/
	Adaptado por Lauro A L Brito
*/



// toda a formatacao da tabela virá do arquivo style.css
// função que gera o calendario
var LastMonth=0;
var LastYear=0;
var dateRangeIn;
var dateRangeOut;
var dateFieldId;

var imgFolder='calendar';

sysMonth = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
Meses = new Array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
Week = new Array('D','S','T','Q','Q','S','S');

// data atual
hoje = new Date();
mesAtual = hoje.getMonth();
anoAtual = hoje.getYear();
minimo	= mesAtual;
maximo	= mesAtual-1;


/*
	*********************************************************************
	limpa o mês anterior na tabela de ocupação porque na passagem do mês,
	o sistema vai considerar o mês anterior como sendo do próximo ano
	*********************************************************************
*/
mesAnterior = mesAtual - 1;
if (mesAnterior < 0)
	mesAnterior=11;

if (anoAtual < 2000)    // Y2K Fix, Isaac Powell
	anoAtual = anoAtual + 1900;
var yr = yr1 = anoAtual; // last month´s year
var mo = mesAtual

var cFirstDate,  cLastDate;

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

		// avançar
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

		jHTML='<table border="0" width="100%" cellpadding=0 cellspacing=0 align="center"/><tr align=center>\r\n';
		// imprimi o nome dos dias da semana
		for (var i = 0;i < 7; i ++)
			jHTML += '<td class=diasSemana>'+ Week[i];

		// primeira semana
		jHTML += '<tr align="center">\r\n';
		dia = 0;
		for (var i = 0; i < 7; i++) {
			if (i < brancos)
				jHTML +='<td class="dia">&nbsp;';
			else {
				dia++;
				if (dia < 10) 
					jHTML +='<td class="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">0'+ dia +'</a>';
				else
					jHTML +='<td class="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">'+ dia +'</a>';
				}
			}

		// demais semanas
		while (dia < maxDay) {
			jHTML += '<tr align="center">\r\n';
			for (var i = 0; i < 7; i++) {
				dia++;
				if (dia > maxDay)
					jHTML += '<td id="dia">&nbsp;';
				else
					if (dia < 10)
						jHTML += '<td class="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">0' + dia +'</a>';
					else
						jHTML += '<td class="dia"><a href="javascript:void(null)" onclick="calendar.setField('+dia+')">' + dia +'</a>';
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
		div=document.getElementById('dvCalendar');
		dateField=document.getElementById(objData);
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
		}
	}
	
/**************************************************************************
incluir na pagina o bloco abaixo e ajustar o top e left conforme necessario
como utilizar:

<input type=text id=data name=data value="" onfocus="calendar.open('data')"/>
	<a href="javascript:void(null)" onclick="calendar.open('data')">
	<img src=images/calend.gif align=absMiddle border=0 alt="Abrir Calendário"></a>
*/

/*
<style>
.visivel {display:inline}	
.invisivel {display:none}

#dvCalendar {
	float:left;
	position:absolute;
	top:280px;
	left:500px;
	
	width:154px;
	_width:154px;

	height:164px;
	_height:auto;
	margin-top:2px;
	margin-bottom:2px;	
	border-style:outset;
	text-align:center;
	background:#eeeee4;
	z-index:100;
	}

#dvCalendar .topCalendar {
	margin-top:2px;
	width:100%;
	height:100%;	
	text-align:center;
	background:#c0c0c0
	}

#dvCalendar td {
	background:#e4e4e4;
	border:1px solid #c0c0c0
	}

#dvCalendar .icones {
	width:15px;
	text-align:center;
	}

#dvCalendar #mes {
	text-align:center;
	font: bolder 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;	
	background:#404040;
	color:#fff;
	}

#dvCalendar #calendar {
	width:154px;
	_width:156px;
	height:auto;
	padding:1px;
	text-align:center
	}

#dvCalendar .dias {
	float:left;
	width:auto;
	height:110px;
	text-align:center;
	font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
	}

#dvCalendar .diasSemana {	
	border-top:1px solid #404040;
	border-right:1px solid #c0c0c0;
	border-left:1px solid #404040;
	border-bottom:1px solid #c0c0c0;
	text-align:center;
	font: bolder 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
	background: #404040;
	color:#ffffff;
	width:20px 
	}

#dvCalendar .dia {
	height:20px;	
	text-align:center;	
	font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
	border-bottom:1px solid #000;
	border-right:1px solid #000; }

#dvCalendar .dia a {
	font: 8pt Arial, Helvetica, Verdana, Tahoma, sans-serif;
	text-decoration:none; }

#dvCalendar .dia a:hover {
	font-weight:bolder;
	background:#c40000;
	color:#fff;
	}
</style>
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
</div>
******************************************************************************/
