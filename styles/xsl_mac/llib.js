document.is_ie = !(navigator.appName.indexOf("Netscape") != -1);


var lmouse = function () {
	this.x = 0;
	this.y = 0;

	this.offsetX = 0;	//for dragging
	this.offsetY = 0;

	this.handler = this.defHandler;
	var listener = function(e)	{
						if(!e)
							e = null;
						document.lmouse.catchEvent(e);
					};
	document.onmousemove = listener;

	if(!document.is_ie) {
		document.addEventListener("mousemove", listener, true);
	}
}

lmouse.prototype.catchEvent = function(e) {
	if(document.is_ie) {
		this.x = event.x;
		this.y = event.y;
	} else {
		this.x = e.pageX;
		this.y = e.pageY;
	}

	lmouse = document.lmouse;
	if("function" == typeof this.handler) {
//		this.resetAllSelections();
		this.handler();
	}
}


lmouse.prototype.setEventHander = function () {
	if(!document.is_ie)
		document.captureEvents(Event.MOUSEMOVE);
	document.onmousemove = MMove;
}


lmouse.prototype.defHandler = function() {
	lmouse = document.lmouse;
//	window.status = "Default MouseHandler. Mouse X: " + lmouse.x + "; Mouse Y: " + lmouse.y;
}

lmouse.prototype.resetHandler = function() {
	this.handler = this.defHandler;
}

lmouse.prototype.setHandler = function(h) {
	if("function" == typeof h) {
	this.handler = h;
	} else
		return alert("lmouse.prototype.setHandler: handler is not a function");
}

lmouse.prototype.resetAllSelections = function() {
	if(document.is_ie)
		document.selection.empty();
	else
		window.getSelection().removeAllRanges();
}

document.lmouse = new lmouse();


function getAbsoluteLocation(obj) {
	var pX = 0;
	var pY = 0;
	var oParent = obj.offsetParent; 

	while (oParent) {
		pX += oParent.offsetLeft;
		pY += oParent.offsetTop;
		oParent = oParent.offsetParent; 
	}

	return { x: pX + obj.offsetLeft, y: pY + obj.offsetTop }
}

function execInternalScript(url) {
	url += "&rnd=" + Math.random();
	var scriptObj = document.createElement("script");
	scriptObj.src = url;

//	var placerObj = document.getElementById("scriptPlacer");
	var placerObj = document.body;
	placerObj.appendChild(scriptObj);

	return true;

	var placerObj = document.body.firstChild;

	do {
		if(placerObj.nodeType == 1) {
			placerObj.appendChild(scriptObj);
			break;
		}
	} while(placerObj = placerObj.nextSibling);
}
