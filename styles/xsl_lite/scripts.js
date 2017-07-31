to_open = new Array();
//!!!all data must be in UTF-8


function v_onload(b_id) {

	if(b_id == "add_page_wysiwyg")
		return;

	disp = findUCookie(b_id + "_table", "setgroups");
//	t_obj = document.getElementById(b_id + "_table");

	if(disp == "none")
		v_switch(b_id);
/*

	t_obj.style.display = disp;


	if(disp == "none") {
		document.images[b_id + "_img"].src = "/images/cms/sg_arrow_up.gif";
	}
*/
}












//for cookie
function addCookie(szName,szValue,dtDaysExpires) 
{
//	return 1;
  szName = szName.replace(/\./g, "_");

   var dtExpires = new Date();
   var dtExpiryDate = "";

   dtExpires.setTime(dtExpires.getTime() + 
     dtDaysExpires * 60 * 60 * 1000);

   dtExpiryDate = dtExpires.toGMTString();

   document.cookie = 
    szName + "=" + szValue + "; path=/; expires=" + dtExpiryDate;
}

function findCookie(szName) 
{
  szName = szName.replace(/\./g, "_");


  var i = 0;
  var nStartPosition = 0;
  var nEndPosition = 0;  
  var szCookieString = document.cookie;  

  while(i <= szCookieString.length) 
  {
    nStartPosition = i;
    nEndPosition = nStartPosition + szName.length;

    if(szCookieString.substring( 
        nStartPosition,nEndPosition) == szName) 
    {
      nStartPosition = nEndPosition + 1;
      nEndPosition = 
        document.cookie.indexOf(";",nStartPosition);

      if(nEndPosition < nStartPosition)
        nEndPosition = document.cookie.length;

      return document.cookie.substring( 
          nStartPosition,nEndPosition);  
      break;    
    }
    i++;  
  }
  return "";
}

function addUCookie(vName, vVal, vExp, gName) {	//4093 bytes MAX!!!
	if(!gName)
		gName = "ucookie";

	if(!vExp)
		vExp = 3600*24*365;

	gVal = findCookie(gName);

	nVal = "";

	gArr = gVal.split("*");
	in_array = false;



	for(i = 0; i < gArr.length; i++) {
		tmp = gArr[i];
		cgArr = tmp.split("|");
		cVar = cgArr[0];
		cVal = cgArr[1];

		if(cVar == "")
			continue;

		if(cVar == vName) {
			in_array = true;
			nVal += vName + "|" + vVal + "*";
		} else
			nVal += tmp + "*";

	}

	if(!in_array)
		nVal += vName + "|" + vVal + "*";

	nVal = nVal.substr(0, nVal.length - 1);

	addCookie(gName, nVal, vExp);
}

function findUCookie(vName, gName) {
	if(!gName)
		gName = "ucookie";

	gVal = findCookie(gName);
	gArr = gVal.split("*");

	in_array = false;
	for(i = 0; i < gArr.length; i++) {
		tmp = gArr[i];
		cgArr = tmp.split("|");
		cVar = cgArr[0];
		cVal = cgArr[1];

		if(cVar == vName) {
//			alert(cVar + " = \"" + cVal + "\"");
			return cVal;
		}
	}
	return "";

}

function deleteUCookie(vName, vExp, gName) {
	if(!gName)
		gName = "ucookie";
	if(!vExp)
		vExp = 3600*24*365;

	gVal = findCookie(gName);
	gArr = gVal.split("*");

	nVal = "";


	for(i = 0; i < gArr.length; i++) {
		tmp = gArr[i];
		cgArr = tmp.split("|");
		cVar = cgArr[0];
		cVal = cgArr[1];

		//alert(cVar + " = " + cVal);
		if(cVar != vName)
				nVal += cVar + "|" + cVal + "*";
	}

	nVal = nVal.substr(0, nVal.length - 1);

	addCookie(gName, nVal, vExp);
}

function sitetree_switch(p_id, mode) {


 timg = document.getElementById('il' + p_id + 'li');
 objs = document.getElementById('s' + p_id + 's');
 obji = document.getElementById('i' + p_id + 'i');
 objpm = document.getElementById('p' + p_id + 'm');

if(!objs)
    return true;


 if(objs.style.display == 'none') {
  objpm.src = objpm.src.replace('plus.gif', '') + 'minus.gif';

  objs.style.display = '';
  if(obji)
   obji.style.display = '';

  if(timg)
   timg.style.display = '';

  if(mode != "ONLOAD")
	addUCookie(p_id + "_page", '1');
  else
	return true;

 } else {
  if(mode == "ONLOAD")
	return true;
//  addUCookie(p_id + "_page", '0' , 365);
  deleteUCookie(p_id + "_page", 365);


  objpm.src = objpm.src.replace('minus.gif', '') + 'plus.gif';
  objs.style.display = 'none';

  if(obji)
   obji.style.display = 'none';

  if(timg)
   timg.style.display = 'none';
 }

}

function hide_ugol(p_id) {
//	alert(p_id);
	timg = document.getElementById('il' + p_id + 'li');
	if(timg)
		timg.style.display = 'none';

}

function is_initEditor() {
	if(typeof initEditor == "function") {
		initEditor();
	}
}



/* AUTOFILL: TRANSLIT */
var rusBig = new Array( "Э", "Ч", "Ш", "Ё", "Ё", "Ж", "Ю", "Ю", "\Я", "\Я", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ц", "Щ", "Ъ", "Ы", "Ь");
var rusSmall = new Array("э", "ч", "ш", "ё", "ё","ж", "ю", "ю", "я", "я", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ц", "щ", "ъ", "ы", "ь" );
var engBig = new Array("E\'", "CH", "SH", "YO", "JO", "ZH", "YU", "JU", "YA", "JA", "A","B","V","G","D","E", "Z","I","J","K","L","M","N","O","P","R","S","T","U","F","H","C", "W","~","Y", "\'");
var engSmall = new Array("e\'", "ch", "sh", "yo", "jo", "zh", "yu", "ju", "ya", "ja", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s",  "t", "u", "f", "h", "c", "w", "~", "y", "\'");
var rusRegBig = new Array( /Э/g, /Ч/g, /Ш/g, /Ё/g, /Ё/g, /Ж/g, /Ю/g, /Ю/g, /Я/g, /Я/g, /А/g, /Б/g, /В/g, /Г/g, /Д/g, /Е/g, /З/g, /И/g, /Й/g, /К/g, /Л/g, /М/g, /Н/g, /О/g, /П/g, /Р/g, /С/g, /Т/g, /У/g, /Ф/g, /Х/g, /Ц/g, /Щ/g, /Ъ/g, /Ы/g, /Ь/g);
var rusRegSmall = new Array( /э/g, /ч/g, /ш/g, /ё/g, /ё/g, /ж/g, /ю/g, /ю/g, /я/g, /я/g, /а/g, /б/g, /в/g, /г/g, /д/g, /е/g, /з/g, /и/g, /й/g, /к/g, /л/g, /м/g, /н/g, /о/g, /п/g, /р/g, /с/g, /т/g, /у/g, /ф/g, /х/g, /ц/g, /щ/g, /ъ/g, /ы/g, /ь/g);
var engRegBig = new Array( /E'/g, /CH/g, /SH/g, /YO/g, /JO/g, /ZH/g, /YU/g, /JU/g, /YA/g, /JA/g, /A/g, /B/g, /V/g, /G/g, /D/g, /E/g, /Z/g, /I/g, /J/g, /K/g, /L/g, /M/g, /N/g, /O/g, /P/g, /R/g, /S/g, /T/g, /U/g, /F/g, /H/g, /C/g, /W/g, /~/g, /Y/g, /'/g);
var engRegSmall = new Array(/e'/g, /ch/g, /sh/g, /yo/g, /jo/g, /zh/g, /yu/g, /ju/g, /ya/g, /ja/g, /a/g, /b/g, /v/g, /g/g, /d/g, /e/g, /z/g, /i/g, /j/g, /k/g, /l/g, /m/g, /n/g, /o/g, /p/g, /r/g, /s/g, /t/g, /u/g, /f/g, /h/g, /c/g, /w/g, /~/g, /y/g, /'/g);


function translit(input, mode) {
	var textar = input;
	var res = "";

	if(mode == "E_TO_R") {
		if (textar) {
			for (i=0; i<engRegSmall.length; i++) {
				textar = textar.replace(engRegSmall[i], rusSmall[i])  
			}
			for (var i=0; i<engRegBig.length; i++) {
				textar = textar.replace(engRegBig[i], rusBig[i])  
				textar = textar.replace(engRegBig[i], rusBig[i])  
			} 
			res = textar;
		}
	}

	if(mode == "R_TO_E") {
		if (textar) {
			for (i=0; i<rusRegSmall.length; i++) {
				textar = textar.replace(rusRegSmall[i], engSmall[i])  
			}
			for (var i=0; i<rusRegBig.length; i++) {
//				textar = textar.replace(rusRegBig[i], engBig[i])  
				textar = textar.replace(rusRegBig[i], engSmall[i])  
			} 
			res = textar.toLowerCase();
		}
	}

	//
	res = res.replace(/[\/\\'\.,\t\|\+&\?%#@]*/g, "");
	res = res.replace(/[ ]+/g, "_");
	//

	//document.post.message.value = res;
	return res;
}

var g_obj;
var def_value = "";
var def_valueh1 = "";

function tt() {
	obj = g_obj;

	res = translit(obj.value, "R_TO_E");




	df = document.forms['adding_new_page'];
	if(df.alt_name)
		an = df.alt_name.value;
	else
		an = "";

	if(df.h1)
		h1 = df.h1.value;
	else
		h1 = obj.value;


	if(an == "" || translit(def_value, "R_TO_E") == an) {
		if(df.alt_name)
			df.alt_name.value = res;
	}

	if(h1 == "" || h1 == def_valueh1) {
		if(df.h1)
			df.h1.value = obj.value;
	}


	def_value = res;
	def_valueh1 = obj.value;
	def_alt = an;
}

function go_alt(obj) {
	g_obj = obj;
	setTimeout("tt()", 1);
}



function tt_f() {
	obj = g_obj;
	res = translit(obj.value, "R_TO_E");




	df = document.forms['adding_new_page'];
	if(df.name)
		an = df.name.value;
	else
		an = "";

	if(an == "" || translit(def_value, "R_TO_E") == an) {
		if(df.name)
			df.name.value = res;
	}

	def_value = res;
	def_alt = an;
}

function go_alt_a(obj) {
	g_obj = obj;
	setTimeout("tt_f()", 1);
}


/* AUTOCHECK FORMS */
var acf_inputs_test = new Array();
var acf_inputs_catch = new Array();
var is_focued_once = 0;


function acf_check(release_outside) {
	acf_name = 'test';

	rt = true;

	if(release_outside)
		is_focued_once = 0;
	if(is_focued_once == 1)
		is_focued_once = 0;
	for(i = 0; i < acf_inputs_test.length; i++) {
		r = filled = acf_field_check(acf_inputs_test[i]);

		if(!r)
			rt = false;
	}

	return rt;

}

function acf_field_check(field) {
	fid = field[0];
	wmsg = field[1];

	f_obj = document.getElementById(fid);
	if(!f_obj)
		return false;

	if(f_obj.value) {
		f_obj.style.border = "#C0C0C0 0.5pt solid";
		return true;
	} else {
		f_obj.style.border = "#A00 0.5pt solid";
		if(is_focued_once == 0) {
			f_obj.focus();
			is_focued_once = 1;
		}
		return false;
	}

}


function acf_catch_input(e) {

	is_ie	   = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));

	if(is_ie)
		c = event.keyCode;
	else
		c = e.keyCode;

	is_focued_once = 0;
	if(c != 13)
		is_focued_once = -1;
	res = acf_check(0)
	if(c == 13) {
		return res;
	}
	return true;

}

function acf_onload() {
	if(!acf_inputs_catch)
		return true;
	for(i = 0; i < acf_inputs_catch.length; i++) {
		field = acf_inputs_catch[i];

		f_obj = document.getElementById(field);
		if(f_obj) {
			f_obj.onkeypress = acf_catch_input;
		}
	}
	return true;
}

function if_sured(link, inp_text) {
	var href = link.href;

	var callback = function () {
		if(href) {
			window.location = href;
		}
	};

	var contDiv = document.createElement("div");
	var html = "<div style='margin: 15px;'><h1>Вы уверены, что хотите удалить эту страницу?</h1>";
	html += "<p>Вы собираетесь удалить страницу. Если вы уверены, нажмите 'Удалить'.<br />";
	html += "После удаления страница попадет в <a href='/admin/data/trash/'>корзину удаленных страниц</a>, откуда ее сможет восстановить администратор сайта.</p>";
	html += "<p align='right'><input type='button' value='Удалить' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmDeleteButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
	html += "</div>";

	contDiv.innerHTML = html;

	var up = new umiPopup();
		up.setSize(430, 130);
	up.setCallbackFunction(callback);
	up.open();
	up.setContent(contDiv);

	var delButton = document.getElementById('confirmDeleteButton');
	if(delButton) {
		delButton.focus();
	}


	return false;
}



function json_option_changed(iObj) {
	var elId = iObj.value;	
	var url = "/admin/catalog/get_subelements/" + elId + "/" + iObj.parentNode.id + "/";
	execInternalScript(url);
	iObj.parentNode.innerHTML = "<option>loading...</option>";
}

function lin_array(val, arr) {
	for(i = 0; i < arr.length; i++) {
		if(arr[i] == val) return true;
	}
	return false;
}

function jsonSelectCallback(elArr, parentNodeId, relId, val) {
	var iObj = document.getElementById(parentNodeId);
	var res = "";

	res += "<option></option>\r\n";

	var valArr = val.split("|");

	res += "<option value=\"" + relId + "\" onclick=\"javascript: json_option_changed(this);\">&lt;Go back&gt;</option>\r\n";
	for(var i = 0; i < elArr.length; i++) {
		if(elArr[i][2] == '0') {
			res += "<option value=\"" + elArr[i][0] + "\" onclick=\"javascript: json_option_changed(this);\">#" + elArr[i][1] + "</option>\r\n";
		} else {
//			if(val && val == elArr[i][0]) {
			if(val && lin_array(elArr[i][0], valArr)) {
				res += "<option value=\"" + elArr[i][0] + "\" selected=\"selected\">" + elArr[i][1] + "</option>\r\n";
			} else {
				res += "<option value=\"" + elArr[i][0] + "\">" + elArr[i][1] + "</option>\r\n";
			}
		}
	}
	iObj.innerHTML = res;
}

function json_select_load(objId, val) {
	var iObj = document.getElementById(objId);

	var url = "/admin/catalog/get_subelements_onload/" + objId + "/" + val + "/";
	execInternalScript(url);
	iObj.innerHTML = "<option>loading...</option>";
}

function execInternalScript(url) {
	var scriptObj = document.createElement("script");
	scriptObj.src = url;
	var placerObj = document.body.firstChild;

	do {
		if(placerObj.nodeType == 1) {
			placerObj.appendChild(scriptObj);
			break;
		}
	} while(placerObj = placerObj.nextSibling);
}





function json_cat_getter_load(objId, val, is_cat_only) {
	if(!is_cat_only) is_cat_only = 0;

	var iObj = document.getElementById(objId);
	var url = "/admin/catalog/get_subelements_onload_cat/" + objId + "/" + val + "/" + is_cat_only + "/";

//	execInternalScript(url);
//	iObj.innerHTML = "<option>loading...</option>";

	iObj.innerHTML = "";
	var o = document.createElement("option");
	o.innerHTML = "<option>loading...</option>";
	iObj.appendChild(o);

	execInternalScript(url);
}


function json_option_changed_cat(iObj, is_cat_only) {
	if(!is_cat_only) is_cat_only = 0;
	var is_ie	   = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));

	var elId = iObj.value;


	if(is_ie) {
		var url = "/admin/catalog/get_subelements/" + elId + "/" + iObj.id + "/1/" + is_cat_only + "/";
	} else {
		var url = "/admin/catalog/get_subelements/" + elId + "/" + iObj.parentNode.id + "/1/" + is_cat_only + "/";
	}

	execInternalScript(url);

	if(is_ie) {
		iObj.innerHTML = "";
		var o = document.createElement("option");
		o.innerHTML = "loading...";
		iObj.appendChild(o);
	} else {
		iObj.parentNode.innerHTML = "<option>loading...</option>";
	}
}


function jsonSelectCallback_cat(elArr, parentNodeId, relId, val, cats_only) {
	if(!cats_only) cats_only = 0;

	var is_ie	   = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));

if(is_ie) {

	var iObj = document.getElementById(parentNodeId);
	iObj.innerHTML = "";

	iObj.appendChild( document.createElement("option") );

	var valArr = val.split("|");

	var o = document.createElement("option");
	o.value = relId;
	var __cats_only = cats_only;
	o.innerHTML = "&lt;Go back&gt;";
	iObj.appendChild( o );

	for(var i = 0; i < elArr.length; i++) {
		var o = document.createElement("option");
		o.value = elArr[i][0];
		o.innerHTML = "[" + elArr[i][1] + "]";
		o.is_object = "no";

		if(elArr[i][2] == '0') {
			o.className = "jsonIcat";
		} else {
			if(val && lin_array(elArr[i][0], valArr)) {
				o.className = "jsonIobj";
				o.selected = true;
			} else {
				o.innerHTML = elArr[i][1];
				o.className = "jsonIobj";
				o.is_object = "yes";
			}
		}

		iObj.appendChild(o);
	}

	var __cats_only = cats_only;
	iObj.ondblclick = function () {
		json_option_changed_cat(this, __cats_only);
	};

} else {

	var iObj = document.getElementById(parentNodeId);
	var res = "";

	res += "<option></option>\r\n";

	var valArr = val.split("|");

	res += "<option value=\"" + relId + "\" ondblclick=\"javascript: json_option_changed_cat(this, '" + cats_only + "');\">&lt;Go back&gt;</option>\r\n";
	for(var i = 0; i < elArr.length; i++) {
		if(elArr[i][2] == '0') {
			res += "<option class=\"jsonIcat\" value=\"" + elArr[i][0] + "\" ondblclick=\"javascript: json_option_changed_cat(this, '" + cats_only + "');\">" + elArr[i][1] + "</option>\r\n";
		} else {
			if(val && lin_array(elArr[i][0], valArr)) {
				res += "<option class=\"jsonIobj\" value=\"" + elArr[i][0] + "\" selected=\"selected\">" + elArr[i][1] + "</option>\r\n";
			} else {
				res += "<option class=\"jsonIobj\" value=\"" + elArr[i][0] + "\">" + elArr[i][1] + "</option>\r\n";
			}
		}
	}

	iObj.innerHTML = res;
}

}

function cat_getter_del(iId) {
	var obj, obj2, nobj;

	if(obj = document.getElementById(iId)) {
		if(obj2 = document.getElementById('inp_' + iId)) {
			obj2.parentNode.removeChild(obj2);
		}

		nobj = document.createElement("input");
		nobj.type = "hidden";
		nobj.name = "cat_del[]";
		nobj.value = iId;

		obj.parentNode.appendChild(nobj);

		obj.parentNode.removeChild(obj);
	}


	return false;
}

function json_cat_addItems(iId) {
	var obj = document.getElementById(iId);
	if(!obj) return false;

	var l = obj.options.length;
	var i;
	for(i = 0; i < l; i++) {
		if(obj.options[i].selected) {
			var val = obj.options[i].value;
			var txt = obj.options[i].innerHTML;
			var is_cat = (obj.options[i].className == 'jsonIcat') ? true : false;

			json_cat_addItem(iId, val, txt, is_cat);
		}
	}
}

function json_cat_addItem(iId, val, txt, is_cat) {
	var obj = document.getElementById(iId + "_list");
	if(!obj) return false;

	var nO = document.createElement("div");
	nO.className = "cat_getter_i";
	nO.id = val;

	if(document.getElementById(val)) return false;

	var nh = "";

	var img_name = (is_cat) ? "ico_news_lent" : "ico_news_item";
	nh += "<div style=\"float: left;\"><img src=\"/images/cms/admin/full/" + img_name + ".gif\" />&nbsp;";
	nh += txt;
	nh += "</div><div style=\"float: right;\">";
	nh += "<a href=\"/\" onclick=\"javascript: return cat_getter_del('" + val + "');\">";
	nh += "<img src=\"/images/cms/admin/full/ico_del.gif\" border=\"0\" /></a></div>";
	nh += "<input type=\"hidden\" name=\"cat_getter_items[]\" value=\"" + val + "\" id=\"inp_" + val + "\" />";
	nh += "<div style=\"clear: both;\" /></div>";

	nO.innerHTML = nh;

	obj.appendChild(nO);
}



var onLoadFunctions__ = new Array();

function addOnLoadEvent(h) {
	if(typeof h != "function") {
		alert("addOnLoadEvent:: Handler must be a valid function");
		return false;
	}
	onLoadFunctions__[onLoadFunctions__.length] = h;
	return true;
}

function runOnLoadEvents() {
	var i;
	for(i = 0; i < onLoadFunctions__.length; i++) {
		var h = onLoadFunctions__[i];
		h();
	}
}




function get_tip(name, title, tip) {
	var callback = function () {
	};

	var contDiv = document.createElement("div");
	var html = "<div style='margin: 15px;'><h1>Подсказка для поля \"" + title + "\".</h1>";
	html += "<p>" + tip + "</p>";
	html += "<p align='right'><input type='button' value='Закрыть' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
	html += "</div>";

	contDiv.innerHTML = html;

	var up = new umiPopup();
	up.setSize(430, 115);
	up.setCallbackFunction(callback);
	up.open();
	up.setContent(contDiv);


	return false;
}


function if_sured_unrestorable(link, inp_text) {
	var href = link.href;

	var callback = function () {
		if(href) {
			window.location = href;
		}
	};

	var contDiv = document.createElement("div");
	var html = "<div style='margin: 15px;'><h1>Вы уверены, что хотите удалить?</h1>";
	html += "<p>Если вы уверены, нажмите 'Удалить'.<br />";
	html += "Этот элемент нельзя будет восстановить.</p>";
	html += "<p align='right'><input type='button' value='Удалить' style='width: 100px; font-weight: bold;' onclick='javascript: umiPopup.getSelf().close(); umiPopup.getSelf().callCallbackFunction();' id='confirmDeleteButton' /> <input type='button' value='Отменить' style='width: 100px;' onclick='javascript: umiPopup.getSelf().close();' /></p>";
	html += "</div>";

	contDiv.innerHTML = html;

	var up = new umiPopup();
		up.setSize(430, 110);
	up.setCallbackFunction(callback);
	up.open();
	up.setContent(contDiv);

	var delButton = document.getElementById('confirmDeleteButton');
	if(delButton) {
		delButton.focus();
	}


	return false;
}


function helpViewerState() {
	var infoBlock = document.getElementById('info-block');
	var infoBlockState = getCookie('info-block');
	var helpViewerStateLink = document.getElementById('helpViewerStateLink');
	
	if (infoBlockState == 'hide') {
		if(infoBlock) {
			infoBlock.style.display = 'none';

		}
		
		if(helpViewerStateLink) {
			helpViewerStateLink.className = "helpSwitcher helpSwitcherOff";
		}
	}
}

function helpViewerSwitch(_this) {
	var infoBlockState = getCookie('info-block');
	var infoBlock = document.getElementById('info-block');
	
	if (infoBlock.style.display == 'none') {
		infoBlock.style.display = 'block';
		setCookie('info-block', 'visible');
		
		_this.className = "helpSwitcher helpSwitcherOn";
	} else {
		infoBlock.style.display = 'none';
		setCookie('info-block', 'hide');
		
		_this.className = "helpSwitcher helpSwitcherOff";
	}
}


function checkVisible(id) {
	var blockId = id + '_table';
	var block = document.getElementById(blockId);
	if (block.style.display == 'none') {
		v_switch(id)
	}
	
}


function show_tip(caller, name, title, tip) {
	var is_ie = ((HTMLArea.agt.indexOf("msie") != -1) && (HTMLArea.agt.indexOf("opera") == -1));

	var oDiv = document.createElement("div");
	oDiv.style.width = '280px';
	oDiv.style.backgroundColor = '#FFF';
	oDiv.style.border = '#000 1px solid';
	oDiv.style.color = '#000';
	oDiv.style.padding = '5px';

	oDiv.style.position = "absolute";
	oDiv.style.zIndex = 300;

	var currWidth = document.body.clientWidth;
	if((document.lmouse.x + 280 + 30) > currWidth) {
		oDiv.style.left = (document.lmouse.x - 280 - 20) + 'px';
	} else {
		oDiv.style.left = (15 + document.lmouse.x) + 'px';
	}

	if(is_ie) {
		oDiv.style.top = (document.body.scrollTop + document.lmouse.y + 15) + 'px';
	} else {
		oDiv.style.top = (document.lmouse.y + 15) + 'px';
	}


	var r = "", i;
	var obj = screen;
	for(i in obj) {
		r += i + " = " + obj[i] + "\n";
	}

	title = title.replace(/</g, '&lt;').replace(/>/g, '&gt;');
	oDiv.innerHTML = "<b>" + title + "</b><br />" + tip;
	document.body.appendChild(oDiv);
	
	caller.onmouseout = function () {
		oDiv.parentNode.removeChild(oDiv);
		this.onmouseout = function () {};
	}
}
