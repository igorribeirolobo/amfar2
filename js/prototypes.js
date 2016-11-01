// JavaScript Document
String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };
String.prototype.onlyNumbers =  function() {
	var Digitos = "0123456789";var temp = "";var digito = "";
	for (var i=0; i<this.length; i++){digito = this.charAt(i);if (Digitos.indexOf(digito)>=0){temp=temp+digito}}return temp}	// end function
String.prototype.validCPF =  function() {
	var i, len, s;s = this.onlyNumbers();len=s.length-2;
	if ((len != 9 && len != 12)) return false;
	var c = s.substr(0,len);var dv = s.substr(len,2);var d1 = 0;
	for (i = 0; i < len; i++) {if (len==9) d1 += c.charAt(i) * (10 - i);else d1 += c.charAt(11 - i) * (2 + (i % 8));}
	if (d1 == 0) return false;d1 = 11 - (d1 % 11);if (d1 > 9) d1 = 0;
	if (dv.charAt(0) != d1) return false;
	d1 *= 2;for (i = 0; i < len; i++) {if (len < 12) { d1 += c.charAt(i) * (11-i); }else { d1 += c.charAt(11 - i) * (2 + ((i + 1) % 8));}}
	d1 = 11 - (d1 % 11);if (d1 > 9) d1 = 0;if (dv.charAt(1) != d1) return false;return true;
	}

String.prototype.formatCPF = function() {var s = this.replace(/[' '-./ \t]/,'');if(s.length==11){return s.substr(0,3)+'.'+s.substr(3,3)+'.'+s.substr(6,3)+'-'+s.substr(9,2);}else if(s.length==14)return s.substr(0,2)+'.'+s.substr(2,3)+'.'+s.substr(5,3)+'/'+s.substr(8,4)+'-'+s.substr(12,2);else return s;}

String.prototype.validCEP =  function() {
	var len,s;s = this.onlyNumbers();len=s.length;
	if (len < 8) return false;
	var p = parseInt(s.substr(0,5));
	if (p<1000) return false;
	return true;
	}
String.prototype.validEmail =  function() {
	var valid= /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/;
	if (this.length == 0) return false;
	if (!valid.test(this)) return (false);
	return true;
	}
String.prototype.validDate = function () {
	var temp = this.split('/');
	var dia=parseInt(temp[0]);
	var mes=parseInt(temp[1]);
	var ano=parseInt(temp[2]);
	if((mes==4||mes==6||mes==9||mes==11)&&dia > 30)
		return false;
	else if((ano%4)!=0&&mes==2&&dia>28)
		return false;
	else if((ano%4)==0&&mes==2&&dia>29)
		return false;
	return true;}

String.prototype.validPhone =  function(ddd) {var len;s = this.onlyNumbers();len=s.length;if (len < 7) return false;if (ddd&&len < 9) return false;return true;}
Number.prototype.toDecimal = function() {
	var c=2;
	d=',';
	t='.';
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "").split('.');
	}
function toFloat(value) {
	var temp = value.replace('.','');
	var temp = temp.replace(',','.');
	return parseFloat(temp);
	}
