function validForm(formref, items){
	var formerror = false;
	for(i = 0; i < items.length; i++)
	{
		var single = true;
		var tipo = items[i][1];
		var label = items[i][2];
		var required = items[i][3];
		var minlen = 0;
		var maxlen = 0;

		if(items[i].length > 4){
			minlen = items[i][4];
		}
		if(items[i].length > 5){
			maxlen = items[i][5];
		}
		var obj = formref.elements[items[i][0]];
		//alert( items[i][0] + " " + obj.type);
		switch (obj.type){

			case "select-one":
				if (!obj.disabled){
					if(required){

						if((obj.selectedIndex == -1) || (obj.options[obj.selectedIndex].value == 0)){
							alert("Errore nel campo '" + label + "': selezionare un valore!");
							formerror = true;
						}
					}
				}
				break;
			case "textarea":
			case "text":
				obj.value = trim(obj.value);
				if (!obj.disabled){
					//alert(" obj = "+obj+" required = "+required)
					if(required)
					{
						if(obj.value.length == 0){
							alert("Errore nel campo '" + label + "': campo obbligatorio!");
							formerror = true;
						}
					}
					if((!formerror) && (obj.value.length > 0)){
						switch(tipo){
							case "number":
								if(!isNumeric(obj.value)){
								//if(!numeroValido(obj.value)){
									alert("Errore nel campo '" + label + "': inserire solo numeri!");
									formerror = true;
								}
								break;
							case "date":
								if (!isDate(obj.value)){
									alert("Errore nel campo '" + label + "': formato data errato gg/mm/aaaa!");
									formerror = true;
								}else{
									obj.value = cleanDate(obj.value);
								}
								break
							case "datetime":
								if (!isDateTime(obj.value)){
									alert("Errore nel campo '" + label + "': formato data errato gg/mm/aaaa hh:mm:ss!");
									formerror = true;
								}else{
									obj.value = cleanDateTime(obj.value);
								}
								break
							case "string":
								break;
							case "email":
								if (!isEmail(obj.value)){
									alert("Errore nel campo '" + label + "': indirizzo e-mail non valido!");
									formerror = true;
								}
								break;
							case "cf":
								obj.value = obj.value.toUpperCase();					
								if (!isCf(obj.value)){
									alert("Errore nel campo '" + label + "':formato errato!");
									formerror = true;
								}
								break;
						}
					}
					if(!formerror){
						if((minlen > 0) && (obj.value.length < minlen) && (obj.value.length > 0)){
							alert("Errore nel campo '" + label + "': testo troppo corto! minimo = " + minlen + " caratteri");
							formerror = true;
						}
						if((maxlen > 0) && (obj.value.length > maxlen) && (obj.value.length > 0)){
							alert("Errore nel campo '" + label + "': testo troppo lungo! massimo = " + maxlen + " caratteri");
							formerror = true;
						}
					}
				}
				break;
			case "password":
				if (!obj.disabled){
					if(required){
						if(obj.value.length == 0){
							alert("Errore nel campo '" + label + "': campo obbligatorio!");
							formerror = true;
						}
					}
					if((minlen > 0) && (obj.value.length < minlen) && (obj.value.length > 0)){
						alert("Errore nel campo '" + label + " testo troppo corto! minimo = " + minlen + " caratteri");
						formerror = true;
					}
					if((maxlen > 0) && (obj.value.length > maxlen) && (obj.value.length > 0)){
						alert("Errore nel campo '" + label + "': testo troppo lungo! massimo = " + maxlen + " caratteri");
						formerror = true;
					}
					if((tipo == "password") && (formerror == false))
					{
						if(obj.value !=  formref.elements[items[i][0] + "_confirm"].value){
							alert("Errore nel campo 'conferma " + label + "'");
							formerror = true;
						}
					}
				}
				break;
			case "checkbox":
				if (!obj.disabled){
					if(required){
						if (!obj.checked){
							alert("Errore nel campo '" + label + "': campo obbligatorio!");
							formerror = true;
						}
					}
				}
				break;
			default:
				if(required){
					radioOk = false;
					for(j = 0; j < obj.length; j++){
						if(obj[j].type == "radio"){
							single = false;
							if (!obj[j].disabled){
								if(obj[j].checked){
									radioOk = true;
									break;
								}
							}
						}
					}
					if(!radioOk){
						alert("Errore nel campo '" + label + "': selezionare un valore!");
						formerror = true;
					}
				}
		}
		if(formerror){
			if(single){
				obj.focus();
			//}else{
			//	obj[0].focus();
			}
			return false;
			break;
		}
	}
	return true;
}
function trim(stringa)
{
   var reTrim=/\s+$|^\s+/g;
   return stringa.replace(reTrim,"");
}
function isCf(cf)
{
   var re = /^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/;
   if(!re.test(cf)){
     return false;
   }
   return true;
}

function isEmail(indirizzo) {
  if (window.RegExp) {
    var nonvalido = "(@.*@)|(\\.\\.)|(@\\.)|(\\.@)|(^\\.)";
    var valido = "^.+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,4}|[0-9]{1,5})(\\]?)$";
    var regnv = new RegExp(nonvalido);
    var regv = new RegExp(valido);
    if (!regnv.test(indirizzo) && regv.test(indirizzo))
      return true;
    return false;
	}
  else {
    if(indirizzo.indexOf("@") >= 0)
      return true;
    return false;
  	}
}

function maxdays(month, year)
{
	var mtds = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if( year%4==0 )
	{
		if( ( year%100==0 ) && ( year%400!=0 ) )
		{
			mtds[1]=28;
		}
		else
		{
			mtds[1]=29;
		}
	}
	return mtds[month-1];
}

function isDateTime(data){
	var err = 1;
	if (data.length == 0){
		return "";
	}
	if (data.length > 2){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == " " ){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
	}
	if(tmp.length == 2)
	{
		return(isDate(tmp[0]) && isTime(tmp[1]));
	}
	else
	{
		return false;
	}
}


function isDate(data){
	var err = 1;
	if (data.length == 0){
		return true;
	}
	if (data.length > 7){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == "-" || c == "/" || c == "." || c == ","){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
		if(tmp.length == 3){
			err = 0;
			for(i = 0; i < tmp.length; i++){
				if(isNaN(tmp[i])){
					err = 1;
					break;
				}
			}
		}
		if(err == 0){
			if((tmp[1] < 1) || (tmp[1] > 12) || (tmp[0] < 1) || (tmp[0] > 31)){
				err = 1;
			}
		}
		if(err == 0){
			if (tmp[0] > maxdays(tmp[1], tmp[2])){
				err = 1;
			}
		}
		if(err == 0){
			if (tmp[2] < 1900){
				err = 1;
			}
		}
	}else{
		return false;
	}
	if(err){
		return false;
	}else{
		return true;
	}
}

function isTime(data){
	var err = 1;
	if (data.length == 0){
		return true;
	}
	if (data.length > 3){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == ":" || c == "-"){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
		if(tmp.length == 2){
			tmp.push("00");
		}
		if(tmp.length == 3){
			err = 0;
			for(i = 0; i < tmp.length; i++){
				if(isNaN(tmp[i])){
					err = 1;
					break;
				}
			}
		}
		if(err == 0){
			if((tmp[0] < 0) || (tmp[0] > 23) || (tmp[1] < 0) || (tmp[1] > 59)  || (tmp[2] < 0) || (tmp[2] > 59)){
				err = 1;
			}
		}
	}else{
		return false;
	}
	if(err){
		return false;
	}else{
		return true;
	}
}

function cleanDateTime(data){
	var err = 1;
	if (data.length == 0){
		return "";
	}
	if (data.length > 2){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == " " ){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
	}
	data = cleanDate(tmp[0]) + " " + cleanTime(tmp[1]);
	
	return data;
}

function cleanDate(data){
	var err = 1;
	if (data.length == 0){
		return "";
	}
	if (data.length > 7){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == "-" || c == "/" || c == "." || c == ","){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
		if(tmp.length == 3){
			err = 0;
			for(i = 0; i < tmp.length; i++){
				if(isNaN(tmp[i])){
					err = 1;
					break;
				}
			}
		}
		if(err == 0){
			if((tmp[2] > 2100) || (tmp[2] < 1900) || (tmp[1] < 1) || (tmp[1] > 12) || (tmp[0] < 1) || (tmp[0] > 31)){
				err = 1;
			}
		}
		if(err == 0){
			if (tmp[0] > maxdays(tmp[1], tmp[2])){
				err = 1;
			}
		}
	}
	if(err){
		return "";
	}else{
		if(tmp[0].length < 2 ){
			tmp[0] = "0" + tmp[0];
		}
		if(tmp[1].length < 2  ){
			tmp[1] = "0" + tmp[1];
		}
		data = tmp[0] + "/" + tmp[1] + "/" + tmp[2];
		return data;
	}
}

function cleanTime(data){
	var err = 1;
	if (data.length == 0){
		return "";
	}
	if (data.length > 3){
		var tmp = new Array();
		var p = 0;
		for(var i = 0; i < data.length; i++){
			var c = data.slice(i, i+1);
			if(c == ":" || c == "-"){
				tmp.push(data.slice(p, i));
				p = i + 1;
			}
		}
		tmp.push(data.slice(p));
		
		if(tmp.length == 2){
			tmp.push("00");
		}
		if(tmp.length == 3){
			err = 0;
			for(i = 0; i < tmp.length; i++){
				if(isNaN(tmp[i])){
					err = 1;
					break;
				}
			}
		}
		if(err == 0){
			if((tmp[0] < 0) || (tmp[0] > 23) || (tmp[1] < 0) || (tmp[1] > 59)  || (tmp[2] < 0) || (tmp[2] > 59)){
				err = 1;
			}
		}
	}
	if(err){
		return "";
	}else{
		if(tmp[0].length < 2 ){
			tmp[0] = "0" + tmp[0];
		}
		if(tmp[1].length < 2  ){
			tmp[1] = "0" + tmp[1];
		}
		if(tmp[2].length < 2 ){
			tmp[2] = "0" + tmp[2];
		}
		data = tmp[0] + ":" + tmp[1] + ":" + tmp[2];
		return data;
	}
}
function isAlfa(text){
	var c = "";
	text = text.toUpperCase();
	for(var i = 0; i < text.length; i++){
		c = text.charAt(i);
		if((c < "A") || (c > "Z")){
			return false;
		}
	}
	return true;
}
function isNumeric(text){
	var c = "";
	for(var i = 0; i < text.length; i++){
		c = text.charAt(i);
		if(( c < "0") || (c > "9")){
			return false;
		}
	}
	return true;
}
function isInteger(text){
	var c = "";
	for(var i = 0; i < text.length; i++){
		c = text.charAt(i);
		if((i == 0) && (c == "-")){
			continue;
		}
		if(( c < "0") || (c > "9")){
			return false;
		}
	}
	return true;
}
function isVocale(c){
	var vocale = false;	
	var cfVocali = ["A", "E", "I", "O", "U"];
	for(var j = 0; j < cfVocali.length; j++){
		if (c == cfVocali[j]){
			vocale = true;
			break;
		}
	}
	return vocale;
}
function getCfPartNome(text, tipo){
	var cfCons = new Array();
	var tmpCons = "";
	var tmpVoc = "";
	var outText = "";
	var i = 0;
	var c = "";
	switch (tipo.toLowerCase()){
		case "nome":
			cfCons = [1,3,4];
			break;
		case "cognome":
			cfCons = [1,2,3];
			break;
		default:
			return outText;
	}
	
	text = text.toUpperCase();
	for(i = 0; i < text.length; i++){
		c = text.substr(i,1);
		if (isAlfa(c)){
			if (!isVocale(c)){
				tmpCons += c;
			}else{
				tmpVoc += c;
			}
		}
	}
	if((tmpCons + tmpVoc).length > 0) {
		c = "";
		if(tmpCons.length <= 3){
			outText = tmpCons;
		}
		if(tmpCons.length > 3){
			for(i = 0; i < cfCons.length; i++){
				if(cfCons[i] <= tmpCons.length){
					outText += tmpCons.substr(cfCons[i] - 1, 1)
				}else{
					break;
				}
			}
		}
		outText += tmpVoc.substr(0, 3 - (outText.length))
		for(i=0; i < 3 - outText.length; i++){
			outText += "X";
		}
	}
	return outText;
}
function getCfPartData(data, sesso){
	var cfMesi = new Array();
	cfMesi["01"]= "A";
	cfMesi["02"]= "B";
	cfMesi["03"]= "C";
	cfMesi["04"]= "D";
	cfMesi["05"]= "E";
	cfMesi["06"]= "H";
	cfMesi["07"]= "L";
	cfMesi["08"]= "M";
	cfMesi["09"]= "P";
	cfMesi["10"]= "R";
	cfMesi["11"]= "S";
	cfMesi["12"]= "T";
	var outText = "";
	var day = "";
	var month = "";
	var year = "";
	sesso = trim(sesso);
	sesso = sesso.toUpperCase();
	data = cleanDate(data);
	if(((sesso == "M") || (sesso == "F")) && (data.length == 10)){
		day = data.substr(0,2);
		if(sesso == "F"){
			day = parseInt(day,10) + 40;
		}
		month = data.substr(3,2);
		year = data.substr(8);
		outText = year +  cfMesi[month] + day;
	}
	return outText;
}
function getCfPartCrc(cf){
	var i = 0;
	var code = 0;
	var c = "";
	cfCode = new Array();
	cfCode["A"] = 1;
	cfCode["B"] = 0;
	cfCode["C"] = 5;
	cfCode["D"] = 7;
	cfCode["E"] = 9;
	cfCode["F"] = 13;
	cfCode["G"] = 15;
	cfCode["H"] = 17;
	cfCode["I"] = 19;
	cfCode["J"] = 21;
	cfCode["K"] = 2;
	cfCode["L"] = 4;
	cfCode["M"] = 18;
	cfCode["N"] = 20;
	cfCode["O"] = 11;
	cfCode["P"] = 3;
	cfCode["Q"] = 6;
	cfCode["R"] = 8;
	cfCode["S"] = 12;
	cfCode["T"] = 14;
	cfCode["U"] = 16;
	cfCode["V"] = 10;
	cfCode["W"] = 22;
	cfCode["X"] = 25;
	cfCode["Y"] = 24;
	cfCode["Z"] = 23;
	cfCode["0"] = 1;
	cfCode["1"] = 0;
	cfCode["2"] = 5;
	cfCode["3"] = 7;
	cfCode["4"] = 9;
	cfCode["5"] = 13;
	cfCode["6"] = 15;
	cfCode["7"] = 17;
	cfCode["8"] = 19;
	cfCode["9"] = 21;
	
	cf = cf.toUpperCase();
	for(i = 0; i < 15; i++){
		c = cf.substr(i,1);
		if(isAlfa(c) || isNumeric(c)){
			if(i % 2 > 0){
				if (isAlfa(c)){
					code -= "A".charCodeAt();
				}else{
					code -= "0".charCodeAt();
				}
				code += c.charCodeAt();
			}else{
				code += cfCode[c];
			}
		}else{
			code = "";
			break;
		}
	}
	if (code != ""){
		code = String.fromCharCode((code % 26) + "A".charCodeAt());
	}
	return code;
}
function validaCF(cf, nome, cognome, sesso, dataNascita){
	var cfPartNome = "";	
	var cfPartCognome = "";	
	var cfPartData = "";	
	var cfPartCode = "";	

	cf = cf.toUpperCase();
	cf = trim(cf);
	if(cf.length != 16){
		return false;
	}
	if(!isNumeric(cf.substr(12,3))){
		return false;
	}
	if(!isAlfa(cf.substr(11,1))){
		return false;
	}
	cfPartCognome = getCfPartNome(cognome, "cognome");
	if(cfPartCognome.length == 0){
		return false;
	}
	if(cfPartCognome != cf.substr(0,3)){
		return false;
	}
	cfPartNome = getCfPartNome(nome, "nome");
	if(cfPartNome.length == 0){
		return false;
	}
	if(cfPartNome != cf.substr(3,3)){
		return false;
	}
	cfPartData = getCfPartData(dataNascita, sesso);
	if(cfPartData.length == 0){
		return false;
	}
	if(cfPartData != cf.substr(6,5)){
		return false;
	}
	cfPartCode = getCfPartCrc(cf);
	if(cfPartCode.length == 0){
		return false;
	}
	if(cfPartCode != cf.substr(15,1)){
		return false;
	}
	return true;
}

function trim(stringa)
{
   reTrim=/\s+$|^\s+/g;
   return stringa.replace(reTrim,"");
}