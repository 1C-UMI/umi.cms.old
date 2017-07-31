function symlinkInputCollection() {
	if(!window.symlinkInputCollectionInstance) {
		window.symlinkInputCollectionInstance = this;
	}
	return window.symlinkInputCollectionInstance;
};

symlinkInputCollection.prototype.inputs = new Array();

symlinkInputCollection.prototype.setObj = function (id, obj) {
	this.inputs[id] = obj;
};

symlinkInputCollection.prototype.getObj = function (id) {
	return this.inputs[id];
};

var nsc = new symlinkInputCollection();


function symlinkInput(id) {
	this.id = parseInt(id);
	window.symlinkInputCollectionInstance.setObj(this.id, this);

	this.placer = this.findPlacer();
};

symlinkInput.prototype.id = null;
symlinkInput.prototype.placer = null;
symlinkInput.prototype.values = new Array();
symlinkInput.prototype.out_inputs = new Array();

symlinkInput.prototype.init = function () {
	this.drawInputs();
	this.bindEvents();
	this.redrawRight();
	this.loadLevel(0);
};

symlinkInput.prototype.findPlacer = function () {
	var obj = document.getElementById('symlinkInputPlacer_' + this.id);
	return (obj) ? obj : false;
};

symlinkInput.prototype.drawInputs = function () {
	var tableObj = document.createElement("table");
	var tableBodyObj = document.createElement("tbody");
	var trObj = document.createElement("tr");
	var tdLeftObj = document.createElement("td");
	var tdMiddleObj = document.createElement("td");
	var tdRightObj = document.createElement("td");

	var fromInput = document.createElement("select");
	var toInput = document.createElement("select");

	var toLeft = document.createElement("input");
	var toRight = document.createElement("input");

	tableObj.className = "symlinkInputTable";
	tableBodyObj.className = "symlinkInput";
	trObj.className = "symlinkInputTr";
	tdLeftObj.className = "symlinkInputTdLeft";
	tdMiddleObj.className = "symlinkInputTdMiddle";
	tdRightObj.className = "symlinkInputTdRight";

	fromInput.className = "symlinkInputFromInput";
	toInput.className = "symlinkInputToInput";

	toRight.className = "symlinkInputToRight";
	toLeft.className = "symlinkInputToLeft";



	fromInput.multiple = true;
	toInput.multiple = true;

	toRight.type = "button";
	toLeft.type = "button";

	toRight.value = ">>>";
	toLeft.value = "<<<";

	


	tableObj.appendChild(tableBodyObj);
	tableBodyObj.appendChild(trObj);
	trObj.appendChild(tdLeftObj);
	trObj.appendChild(tdMiddleObj);
	trObj.appendChild(tdRightObj);

	tdLeftObj.appendChild(fromInput);
	tdMiddleObj.appendChild(toRight);
	tdMiddleObj.appendChild(toLeft);
	tdRightObj.appendChild(toInput);

	this.placer.appendChild(tableObj);

	this.toLeft = toLeft;
	this.toRight = toRight;

	this.fromInput = fromInput;
	this.toInput = toInput;

//	this.toInput.name = "data_values[" + this.id + "][]";
};


symlinkInput.prototype.loadLevel = function (parentId) {
	if(!parentId) parentId = 0;

	var src = "/admin/data/json_load_hierarchy_level/" + this.id + "/" + parentId + "/";

	var scriptObj = document.createElement("script");
	scriptObj.src = src;

	this.placer.appendChild(scriptObj);
};


symlinkInput.prototype.onLoad = function (res) {
	var sz = res.length, i;

	this.fromInput.innerHTML = "";

	for(i = 0; i < sz; i++) {
		var optionObj = document.createElement("option");

		var txtObj = document.createTextNode(res[i][1]);
		optionObj.appendChild(txtObj);
		optionObj.value = res[i][0];
		this.fromInput.appendChild(optionObj);
	}
};


symlinkInput.prototype.bindEvents = function () {
	var __self = this;

	this.toRight.onclick = function () {
		__self.moveRight();
	};

	this.toLeft.onclick = function () {
		__self.moveLeft();
	};

	this.fromInput.ondblclick = function () {
		__self.loadLevel(__self.fromInput.value);
	};
};

symlinkInput.prototype.redrawRight = function () {
	var res = this.values;
	var sz = res.length, i;

	this.toInput.innerHTML = "";

	for(i in this.values) {
		if(typeof res[i] == "undefined") continue;

		var optionObj = document.createElement("option");

		var txtObj = document.createTextNode(res[i][1]);
		optionObj.appendChild(txtObj);
		optionObj.id = optionObj.value = res[i][0];
		this.toInput.appendChild(optionObj);
	}
};


symlinkInput.prototype.addValues = function (vals, f) {
	var i;
	
	if(f) {
		this.values = vals;
	} else {
		f = false;
	}

	for(i in vals) {
		if(!f) {
			this.values[i] = vals[i];
		}

		var outInputObj = document.createElement("input");
		outInputObj.name = "data_values[" + this.id + "][]";
		outInputObj.type = "hidden";
		outInputObj.value = vals[i][0];
		this.placer.appendChild(outInputObj);
		this.out_inputs[vals[i][0]] = outInputObj;
	}

	this.redrawRight();
};


symlinkInput.prototype.delValues = function (values) {
	var i;
	for(i in values) {
		this.values[values[i][0]] = undefined;

		if(this.out_inputs[values[i][0]]) {
			var o = this.out_inputs[values[i][0]];
			o.parentNode.removeChild(o);
		}
	}

	this.redrawRight();
};


symlinkInput.prototype.moveRight = function () {
	var i;
	var res = new Array();
	for(i in this.fromInput.childNodes) {
		var cNode = this.fromInput.childNodes[i];

		if(!cNode) continue;

		if(cNode.tagName != "OPTION") continue;
		if(cNode.selected != true) continue;

		res[cNode.value] = new Array(cNode.value, cNode.innerHTML);
	}

	this.addValues(res);
};


symlinkInput.prototype.moveLeft = function () {
	var i;
	var res = new Array();

	for(i in this.toInput.childNodes) {
		var cNode = this.toInput.childNodes[i];
		if(!cNode) continue;

		if(cNode.tagName != "OPTION") continue;
		if(cNode.selected != true) continue;

		res[res.length] = new Array(cNode.value, cNode.innerHTML);
	}

	this.delValues(res);
};


















