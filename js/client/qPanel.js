var Mozilla = (navigator.appName.indexOf("Netscape") != -1);

// Object qPanel ===============================================================
function qPanel() {
	var th = (typeof window.onload == "function") ? window.onload : function () {};
	var __self = this;
	window.onload = function () {
		th();
		__self.init();
	};
};

qPanel.instance = null;

qPanel.getInstance = function () {
	if(!qPanel.instance) {
		qPanel.instance = new qPanel();
	}
	return qPanel.instance;
};

qPanel.prototype.placer = null;
qPanel.prototype.qedit = null;
qPanel.prototype.user_info = null;
qPanel.prototype.qmodules = null;
qPanel.prototype.is_allowed = true;
qPanel.prototype.is_loaded = false;
qPanel.prototype.resize_img = null;
qPanel.prototype.resize_ref = null;
qPanel.prototype.controls = null;
qPanel.prototype.minimized = false;
qPanel.prototype.pre_lang = "";

qPanel.prototype.init = function () {
	this.placer = lLib.createVoidContainer();
	var __self = this;
	var handler = function (res) {
		__self.__onLoad(res);
	};
	lLib.getInstance().makeRequest("/content/json_get_editable_blocks/?", handler);
};

qPanel.prototype.__onLoad = function (res) {
	var oUserInfo = this.user_info;
	this.draw();
	//fill oUserInfo
	if (oUserInfo) {
		oUserName = document.createElement("a");
		oUserName.href = this.pre_lang + "/users/settings/";
		oUserName.title = res.user_groups;
		oUserName.style.fontWeight = "normal";
		oUserName.style.color = "#1A72BC";
		oUserName.style.fontFamily = "Arial";
		oUserName.style.fontSize = "11px";
		oUserName.style.textDecoration = "underline";

		oUserName.innerHTML += res.user_login;

		oUserInfo.appendChild(oUserName);

//		oUserLogin = document.createElement("span");
//		oUserLogin.style.marginLeft = "2px";
//		oUserLogin.style.fontWeight = "normal";
//		oUserLogin.innerHTML = ", " + res.user_groups + "";
//		oUserInfo.appendChild(oUserLogin);

		this.show();

	}
	var oQModules = this.qmodules;
	// fill oQModules
	if (oQModules) {
		var oGoModule = new Option("Перейти к модулю", "#");
		oQModules.options.add(oGoModule);
		var i, sz = res.modules.length;
		for(i = 0; i < sz; i++) {
			var sName = res.modules[i][0];
			var sLink = res.modules[i][1];
			var oNextOpt = new Option(sName, "/" + pre_lang + sLink);
			oQModules.options.add(oNextOpt);
		}
	}
	var oQEdit = this.qedit;
	//fill oQEdit options
	//optionName = new Option ([optionText, optionValue, defaultSelected, selected]);
	if (oQEdit) {
		// first option
		var oWhatEdit =new Option("Что отредактировать?", "#");
		oQEdit.options.add(oWhatEdit);
		var i, sz = res.links.length;
		for(i = 0; i < sz; i++) {
			var sName = res.links[i][0];
			var sLink = res.links[i][1];
			var oNextOpt =new Option(sName, "/"+pre_lang+sLink);
			oQEdit.options.add(oNextOpt);
		}
	}

	this.is_loaded = true;
	bMinimized = (getCookie("qpanel_minimized") == 1);
	this.show(bMinimized);
};

qPanel.prototype.draw = function () {
	this.hide();
	var placer = this.placer;
	placer.style['styleFloat'] = 'right';
	placer.style['cssFloat'] = 'right';
	placer.style.backgroundColor = "#F0F2F4";
	placer.style.borderBottom = "1px solid #ADADAD";
	placer.style.borderLeft = "5px solid #FE9807";
	placer.style.position = "absolute";
	placer.style.display = "none";
	placer.style.overflow = "visible";

	placer.style.zIndex = "200";
	placer.style.color = "#464646";
	placer.style.fontFamily = "Arial";
	placer.style.fontWeight = "normal";
	placer.style.fontSize = "12px";

	placer.style.textAlign = "left";

	var oLeftDiv = document.createElement("div");
	oLeftDiv.style.marginLeft = "7px";

	var oRightDiv = document.createElement("div");
	placer.appendChild(oRightDiv);

	var oUserImg = document.createElement("img");
	oUserImg.src = "/images/cms/qpanel_user.gif";
	oUserImg.style.border = "0px";
	oUserImg.style.marginTop = "2px";
	oUserImg.align = "absmiddle";
	var oImg = oLeftDiv.appendChild(oUserImg);

	var oUserInfo = document.createElement("span");
	oUserInfo.style.marginLeft = "10px";
	oUserInfo.style.whiteSpace = "nowrap";
	this.user_info = oUserInfo;
	oLeftDiv.appendChild(oUserInfo);

	var oOpenSpan = document.createElement("span");
	oOpenSpan.innerHTML="";
	oOpenSpan.style.marginLeft = "5px";
	oLeftDiv.appendChild(oOpenSpan);

	var oExitRef = document.createElement("a");
	oExitRef.href = "/"+pre_lang+"/users/logout/";
	oExitRef.style.color = "#1A72BC";
	oExitRef.style.fontFamily = "Arial";
	oExitRef.style.fontSize = "11px";
	oExitRef.style.fontWeight = "normal";
	oExitRef.style.textDecoration = "underline";
	oExitRef.style.marginLeft="4px";
	oExitRef.style.marginRight="4px";
	oExitRef.innerHTML = "Выход";
	oLeftDiv.appendChild(oExitRef);

	var oCloseSpan = document.createElement("span");
	oCloseSpan.innerHTML="";
	oLeftDiv.appendChild(oCloseSpan);

	var oSeparator = document.createElement("img");
	oSeparator.src = "/images/cms/qpanel_vline.gif";
	oSeparator.style.width = "1px";
	oSeparator.style.height = "14px";
	oSeparator.style.border = "0px";
	oSeparator.style.marginLeft = "10px";
	oSeparator.style.marginRight = "10px";
	oSeparator.align = "absmiddle";
	oLeftDiv.appendChild(oSeparator);

	var oQEdit = document.createElement("select");
	oQEdit.onchange = function () {
		window.location = this.options[this.selectedIndex].value; return true;
	}
	oQEdit.style.color = "#464646";
	oQEdit.style.fontFamily = "Arial";
	oQEdit.style.fontSize = "11px";
	oQEdit.style.fontWeight = "bold";
	oQEdit.style.border = "1px solid #7F9DB9 ";
	oQEdit.style.width = "160px";
	oLeftDiv.appendChild(oQEdit);
	this.qedit = oQEdit;

	var oSeparator2 =oSeparator.cloneNode(true);
	oLeftDiv.appendChild(oSeparator2);

	var oQModules = document.createElement("select");
	oQModules.onchange = function () {
		window.location = this.options[this.selectedIndex].value; return true;
	}
	oQModules.style.color = "#464646";
	oQModules.style.fontFamily = "Arial";
	oQModules.style.fontSize = "11px";
	oQModules.style.fontWeight = "bold";
	oQModules.style.border = "1px solid #7F9DB9 ";
	oQModules.style.width = "135px";
	oLeftDiv.appendChild(oQModules);
	this.qmodules = oQModules;

	var oSeparator3 =oSeparator.cloneNode(true);
	oLeftDiv.appendChild(oSeparator3);

	oAdminSpan = document.createElement("span");
	oAdminSpan.style.whiteSpace = "nowrap";
	var oAdminImg = oUserImg.cloneNode(true);
	oAdminImg.src = '/images/cms/qpanel_conf.gif';
	oAdminSpan.appendChild(oAdminImg);
	var oAdminRef = oExitRef.cloneNode(true);
	oAdminRef.innerHTML = "Администрирование";
	oAdminRef.href = "/"+pre_lang+"/admin/";
	oAdminSpan.appendChild(oAdminRef);
	oLeftDiv.appendChild(oAdminSpan);

	var oSeparator4 =oSeparator.cloneNode(true);
	oLeftDiv.appendChild(oSeparator4);

	var oReferersImg = document.createElement("img");
	oReferersImg.src = "/images/cms/qpanel_referers.gif";
	oReferersImg.style.border = "0px";
	oReferersImg.style.marginTop = "2px";
	oReferersImg.align = "absmiddle";
	var oImg = oLeftDiv.appendChild(oReferersImg);


	oAdminSpan = document.createElement("span");
	oAdminSpan.style.whiteSpace = "nowrap";
	var oAdminRef = oExitRef.cloneNode(true);
	oAdminRef.innerHTML = "Переходы";
	oAdminRef.href = "#";
	oAdminRef.onclick = function () {
		floatReferers.getInstance().loadLinks();
		return false;
	};
	oAdminSpan.appendChild(oAdminRef);
	oLeftDiv.appendChild(oAdminSpan);


	var oSeparator4 =oSeparator.cloneNode(true);
	oLeftDiv.appendChild(oSeparator4);

	var oTicketImg = document.createElement("img");
	oTicketImg.src = "/images/cms/qpanel_ticket.gif";
	oTicketImg.style.border = "0px";
	oTicketImg.style.marginTop = "2px";
	oTicketImg.align = "absmiddle";
	var oImg = oLeftDiv.appendChild(oTicketImg);


	oAdminSpan = document.createElement("span");
	oAdminSpan.style.whiteSpace = "nowrap";
	var oAdminRef = oExitRef.cloneNode(true);
	oAdminRef.innerHTML = "Создать заметку";
	oAdminRef.href = "#";
	oAdminRef.onclick = function () {
		alert("Теперь нужно выделить область страницы, к которой Вы собираетесь создать заметку.");
		umiTickets.getInstance().beginCreatingTicket();
		return false;
	};
	oAdminSpan.appendChild(oAdminRef);
	oLeftDiv.appendChild(oAdminSpan);


	
	var oResizeSpan = document.createElement("span");
	oResizeSpan.style.whiteSpace = "nowrap";

	var oResizeImg =  oUserImg.cloneNode(true);
	oResizeImg.src = "/images/cms/qpanel_uarrow.gif";
	this.resize_img = oResizeImg;
	oResizeSpan.appendChild(oResizeImg);

	var oResizeRef =  oExitRef.cloneNode(true);
	oResizeRef.innerHTML = "Скрыть";
	this.resize_ref = oResizeRef;
	oResizeRef.href = "#";
	var oObj = this;
	oResizeRef.onclick = function () {
		oObj.show(!oObj.minimized);
		return false;
	}
	oResizeSpan.appendChild(oResizeRef);


	oRightDiv.appendChild(oResizeSpan);
	oRightDiv.style['styleFloat'] = 'right';
	oRightDiv.style['cssFloat'] = 'right';

	this.controls = oLeftDiv;
	placer.appendChild(oLeftDiv);
}

qPanel.prototype.show = function (bMinimized) {
	if(!this.is_loaded) return false;
	this.hide();
	var bMinimized = (bMinimized? bMinimized: false);
	var placer = this.placer;
	if(this.is_allowed) {
		var oResizeImg = this.resize_img;
		var oResizeRef = this.resize_ref;
		var oControls = this.controls;
		placer.style.top = '0px';
		placer.style.right = '0px'
		if (oResizeImg && oResizeRef && oControls) {
			if (bMinimized) {
				placer.style.left = '';
				placer.style.width = '130px';
				placer.style.paddingTop = "0px";
				placer.style.paddingBottom = "0px";
				oResizeImg.src = '/images/cms/qpanel_darrow.gif';
				oResizeRef.innerHTML = 'Развернуть панель';
				oControls.style.display = 'none';
				setCookie("qpanel_minimized", 1);
				this.minimized = true;
			} else {
				placer.style.width = '';
				placer.style.left = '0px';
				placer.style.paddingTop = "7px";
				placer.style.paddingBottom = "7px";
				oResizeImg.src = '/images/cms/qpanel_uarrow.gif';
				oResizeRef.innerHTML = 'Свернуть';
				oControls.style.display = 'block';
				setCookie("qpanel_minimized", 0);
				this.minimized = false;
			}
		}
		this.placer.style.display = 'block';
		this.placer.style.overflow = 'visible';
		return true;
	} else {
		return false;
	}
};

qPanel.prototype.hide = function () {
	this.placer.style.display = 'none';
	this.placer.style.overflow = 'hidden';
	return true;
};


qPanel.getInstance();
