var Mozilla = (navigator.appName.indexOf("Netscape") != -1);

function quickEdit() {
	var th = (typeof window.onload == "function") ? window.onload : function () {};
	var __self = this;

	window.onload = function () {
		th();
		__self.init();
	};
};

quickEdit.instance = null;

quickEdit.getInstance = function () {
	if(! quickEdit.instance) {
		quickEdit.instance = new quickEdit();
	}
	return quickEdit.instance;
};


quickEdit.prototype.placer = null;
quickEdit.prototype.containerObj = null;
quickEdit.prototype.is_allowed = true;
quickEdit.prototype.is_loaded = false;


quickEdit.prototype.init = function () {
	this.placer = lLib.createVoidContainer();
	this.placer.style.zIndex = "10000000";
	this.draw();

	var __self = this;
	var handler = function (res) {
		__self.__onLoad(res);
	};
	lLib.getInstance().makeRequest("/content/json_get_editable_blocks/?", handler);
};

quickEdit.prototype.draw = function () {
	var placer = this.placer;
	var options = "";

	this.hide();
	
	placer.style.width = "510px";
	placer.style.backgroundColor = "#FFF";
	placer.style.padding = "1px";
	placer.style.position = "absolute";


	var div0 = document.createElement("div");
	div0.style.backgroundColor = "#FFF";
	div0.style.border = "#959A9F 1px solid";
	div0.style.padding = "1px";
	placer.appendChild(div0);

	var div1 = document.createElement("div");
	div1.style.height = "15px";
	div1.style.width = "100%";
	div1.style.backgroundColor = "#EEEFF0";
	div1.style.textAlign = "right";
	div0.appendChild(div1);

	var divClose = document.createElement("div");
	divClose.style.cursor = "pointer";
	divClose.style.fontFamily = "Tahoma";
	divClose.style.fontSize = "11px";
	divClose.style.float = "right";
	divClose.style.color = "#FFF";
	divClose.style.fontWeight = "bold";
	divClose.style.backgroundColor = "#959A9F";
	divClose.style.width = "15px";
	divClose.style.height = "15px";
	divClose.style.textAlign = "center";
	divClose.style.cssFloat = "right";
	divClose.style.styleFloat = "right";
	divClose.innerHTML = "X";

	var __self = this;
	divClose.onclick = function () {
		__self.hide();
	}
	div1.appendChild(divClose);


	var div2 = document.createElement("div");
	div2.style.marginLeft = "24px";
	div2.style.marginRight = "25px";
	div2.style.marginTop = "15px";
	div2.style.marginBottom = "15px";

	div0.appendChild(div2);

	var containerObj = document.createElement("div");
	containerObj.style.margin = "0px";
	containerObj.style.padding = "0px";
	containerObj.style.border = "0";
	containerObj.style.textAlign = "left";
	div2.appendChild(containerObj);

	this.containerObj = containerObj;

	var handler_onResize = function () {
		__self.placeCentered();
	}

	window.onscroll = handler_onResize;
	window.onresize = handler_onResize;

	this.placeCentered();
};

quickEdit.prototype.placeCentered = function () {
	var obj = this.placer;

	var arrPageSizes = getPageSizes();

	var leftLayer = (arrPageSizes[2] - obj.offsetWidth)/2;
	var topLayer = (arrPageSizes[3] - obj.offsetHeight)/2;

	obj.style.left = leftLayer + 'px';
	if (document.documentElement && document.documentElement.scrollTop) {
		// IE 6 Strict
		obj.style.top = topLayer + document.documentElement.scrollTop + 'px';
	} else {
		obj.style.top = topLayer + document.body.scrollTop + 'px';
	}
};



quickEdit.prototype.show = function () {
	if(!this.is_loaded) return false;

	if(this.is_allowed) {
		this.placer.style.display = '';
		this.placer.style.border = "red 0px solid";
		this.placeCentered();
		return true;
	} else {
		return false;
	}
};

quickEdit.prototype.hide = function () {
	if(!this.placer) {
		return false;	
	}
	
	this.placer.style.display = 'none';
	return true;
};

quickEdit.prototype.__onLoad = function (res) {
	var containerObj = this.containerObj;

	var html = "<span style=\"font-family: Tahoma; font-size: 11px; font-weight: bold; color: #000; text-align: left;\">Что Вы хотите отредактировать?</span><br /><br />\r\n";

	var i, sz = res.links.length;

	for(i = 0; i < sz; i++) {
		var name = res.links[i][0];
		var link = "/"+pre_lang+res.links[i][1];
		var type_name = res.links[i][2];
		var type_link = "/"+pre_lang+res.links[i][3];

		html += "<span style=\"font-family: Tahoma; font-size: 11px; font-weight: bold; color: #000;\">&#187; </span><a href=\"" + link + "\" style=\"font-family: Tahoma; font-size: 11px; color: #138ECC; font-weight: bold; text-decoration: underline;\">" + name + "</a> <a href=\"" + type_link + "\" style=\"font-family: Tahoma; font-size: 11px; color: #138ECC; text-decoration: underline;\">(" + type_name + ")</a></span><br />\r\n";
	}

		html += "<br /><span style=\"font-family: Tahoma; font-size: 11px; font-weight: bold; color: #000;\">&#187; </span><a href=\"/"+pre_lang+"/admin/\" style=\"font-family: Tahoma; font-size: 11px; color: #138ECC; font-weight: bold; text-decoration: underline;\">Перейти в администрирование</a> </span><br />\r\n";

	var inpDiv = document.createElement("div");
	inpDiv.innerHTML = html;

	containerObj.appendChild(inpDiv);

	this.is_loaded = true;
};


quickEdit.getInstance();