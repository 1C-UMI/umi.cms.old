function umiPopup () {
	//TODO
	umiPopup.self = this;
}

umiPopup.prototype.containerDiv = null;
umiPopup.prototype.windowDiv = null;
umiPopup.prototype.iframe = null;
umiPopup.prototype.callbackFunction = null;
umiPopup.prototype.returnValue = null;
umiPopup.prototype.width = 400;
umiPopup.prototype.height = 200;
umiPopup.prototype.inputParam = null;
umiPopup.prototype.onCloseFunction = null;

umiPopup.getSelf = function () {
	if(!umiPopup.self) {
		umiPopup.self = new umiPopup();
	}
	return umiPopup.self;
};

umiPopup.prototype.open = function () {
	var containerDiv = this.containerDiv = document.createElement("div");
	containerDiv.className = "umiPopupContainerDiv";
	containerDiv.style.width = "100%";
	containerDiv.style.height = "100%";
	
	if(HTMLArea.is_ie) {
	    containerDiv.style.position = "absolute";
	} else {
	    containerDiv.style.position = "fixed";
	}
	containerDiv.style.left = "0px";
	containerDiv.style.top = "0px";
	document.body.appendChild(containerDiv);


	var windowDiv = this.windowDiv = document.createElement("div");
	windowDiv.className = "umiPopupWindowDiv";
	windowDiv.style.width = this.width + "px";
	windowDiv.style.height = this.height + "px";
	
	if(HTMLArea.is_ie) {
	    windowDiv.style.position = "absolute";
	} else {
	    windowDiv.style.position = "fixed";
	}
	windowDiv.style.left = "0px";
	windowDiv.style.top = "0px";


	document.body.appendChild(windowDiv);

	this.placeCentered();

	var __self = this;
	var handler_onResize = function () {
		__self.placeCentered();
	}

	window.onscroll = handler_onResize;
	window.onresize = handler_onResize;

};

umiPopup.prototype.close = function () {
	this.inputParam = null;

	window.onscroll = null;
	window.onresize = null;

	this.returnValue = null;
	if(typeof this.onCloseFunction == "function") {
		this.onCloseFunction();
	}


	if(this.iframe) {
		if(typeof this.iframe == "object") {
			this.iframe.parentNode.removeChild(this.iframe);
			this.iframe = null;
		} else {
			return false;
		}
	}

  
	if(this.containerDiv) {
		if(typeof this.containerDiv == "object") {
			this.containerDiv.parentNode.removeChild(this.containerDiv);
		} else {
			return false;
		}
	}


	if(this.windowDiv) {
		if(typeof this.windowDiv == "object") {
			this.windowDiv.parentNode.removeChild(this.windowDiv);
		} else {
			return false;
		}
	}

	return true;
};

umiPopup.prototype.setSize = function (width, height) {
	if(typeof width == "number" && typeof height == "number") {
		this.width = width;
		this.height = height;
	} else {
		this.error("umiPopup::setSize. Both arguments must be numbers.");
	}
};

umiPopup.prototype.setContent = function (DOMNode) {
	if(typeof DOMNode != "object") {
		this.error("umiPopup::setContent. First parameter must be an object.");
		return false;
	}

	if(this.windowDiv) {
		this.windowDiv.appendChild(DOMNode);
		return true;
	} else {
		return false;
	}
};

umiPopup.prototype.setUrl = function (url) {
	var iframe = this.iframe = document.createElement('iframe');
	iframe.className = "umiPopupIframe";
	iframe.style.width = this.width + "px";
	iframe.style.height = this.height + "px";
	iframe.frameBorder = "0";

	this.windowDiv.appendChild(iframe);
	iframe.focus();
	iframe.src = url;
};

umiPopup.prototype.setReturnValue = function (returnValue) {
	this.returnValue = returnValue;
};

umiPopup.prototype.getReturnValue = function () {
	return this.returnValue;
};

umiPopup.prototype.setCallbackFunction = function (callbackFunction) {
	if(typeof callbackFunction == "function") {
		this.callbackFunction = callbackFunction;
		return true;
	} else {
		return this.error("umiPopup::setCallbackFunction. First argument must be a function.");
	}
};

umiPopup.prototype.callCallbackFunction = function () {
	if(typeof this.callbackFunction == "function") {
		return this.callbackFunction(this.returnValue);
	} else {
		return false;
	}
};

umiPopup.prototype.error = function (err) {
	alert(err);
	return false;
}

umiPopup.prototype.placeCentered = function () {
	var obj = this.windowDiv;
	var leftLayer = (document.body.clientWidth - this.width)/2;
	var topLayer = (document.body.clientHeight - this.height)/2;
	obj.style.left = leftLayer + "px";
	obj.style.top = (topLayer + document.body.scrollTop) + "px";

	var obj = this.containerDiv;
	obj.style.top = (document.body.scrollTop) + "px";
};
